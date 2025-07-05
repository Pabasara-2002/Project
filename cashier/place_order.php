<?php
session_start();
include('../db.php');

// Check login and role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'cashier') {
    header("Location: ../login.php");
    exit();
}

// Initialize order session
if (!isset($_SESSION['current_order'])) {
    $_SESSION['current_order'] = [];
}

// Menu price list
$priceList = [
    'Fried Rice (Chicken)' => 400,
    'Fried Rice (Egg)' => 350,
    // Add all your items...
];

// Handle add item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu_item'], $_POST['quantity'], $_POST['table'], $_POST['add_item'])) {
    $table = $_POST['table'];
    $menu_item = $_POST['menu_item'];
    $quantity = max(1, intval($_POST['quantity'])); // prevent 0

    if (!array_key_exists($menu_item, $priceList)) {
        echo "<script>alert('Invalid item selected'); location.href='place_order.php';</script>";
        exit();
    }

    $unit_price = $priceList[$menu_item];
    $_SESSION['table'] = $table;

    $_SESSION['current_order'][] = [
        'item_name' => $menu_item,
        'quantity' => $quantity,
        'price' => $unit_price
    ];

    header("Location: place_order.php");
    exit();
}

// Handle submit order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    if (empty($_SESSION['current_order'])) {
        echo "<script>alert('No items to submit.'); location.href='place_order.php';</script>";
        exit();
    }

    $customer_id = $_SESSION['user_id'] ?? null;
    if (!$customer_id) {
        echo "<script>alert('Customer ID missing.'); location.href='../login.php';</script>";
        exit();
    }

    $table_number = $_SESSION['table'] ?? '';

    $conn->begin_transaction();

    try {
        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (customer_id, table_number, order_date) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $customer_id, $table_number);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        foreach ($_SESSION['current_order'] as $item) {
            $item_name = $item['item_name'];
            $qty = $item['quantity'];

            // Get menu item id
            $stmt = $conn->prepare("SELECT id FROM menu_items WHERE name = ?");
            $stmt->bind_param("s", $item_name);
            $stmt->execute();
            $menu_result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$menu_result) continue;

            $menu_id = $menu_result['id'];

            // Insert order item
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $order_id, $menu_id, $qty);
            $stmt->execute();
            $stmt->close();

            // Get ingredients for this item
            $stmt = $conn->prepare("SELECT ingredient_id, quantity_required FROM menu_ingredients WHERE menu_item_id = ?");
            $stmt->bind_param("i", $menu_id);
            $stmt->execute();
            $ingredients = $stmt->get_result();
            $stmt->close();

            while ($ing = $ingredients->fetch_assoc()) {
                $ingredient_id = $ing['ingredient_id'];
                $needed_qty = $ing['quantity_required'] * $qty;

                // Deduct stock
                $stmt = $conn->prepare("UPDATE ingredients SET quantity_in_stock = quantity_in_stock - ? WHERE id = ?");
                $stmt->bind_param("di", $needed_qty, $ingredient_id);
                $stmt->execute();
                $stmt->close();

                // Low stock check
                $stmt = $conn->prepare("SELECT ingredient_name, quantity_in_stock, threshold FROM ingredients WHERE id = ?");
                $stmt->bind_param("i", $ingredient_id);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($result['quantity_in_stock'] <= $result['threshold']) {
                    $message = "Ingredient '{$result['ingredient_name']}' is low on stock!";
                    $stmt = $conn->prepare("SELECT id FROM low_stock_alerts WHERE ingredient_id = ? AND is_read = 0");
                    $stmt->bind_param("i", $ingredient_id);
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows === 0) {
                        $stmt->close();
                        $stmt = $conn->prepare("INSERT INTO low_stock_alerts (ingredient_id, message, created_at) VALUES (?, ?, NOW())");
                        $stmt->bind_param("is", $ingredient_id, $message);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        $stmt->close();
                    }
                }
            }
        }

        $conn->commit();

        unset($_SESSION['current_order']);
        unset($_SESSION['table']);

        echo "<script>alert('✅ Order placed successfully!'); location.href='place_order.php';</script>";
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('❌ Order failed: " . $e->getMessage() . "'); location.href='place_order.php';</script>";
        exit();
    }
}
?>
