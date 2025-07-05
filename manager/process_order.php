<?php
$conn = new mysqli("localhost", "root", "", "restaurant");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id > 0) {
    $conn->begin_transaction();

    try {
        // ✅ 1. Mark order as approved
        $stmt = $conn->prepare("UPDATE orders SET order_status = 'approved', payment_status = 'unpaid' WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        // ✅ 2. Get ordered menu items
        $orderItemsStmt = $conn->prepare("SELECT item_id, quantity FROM order_items WHERE order_id = ?");
        $orderItemsStmt->bind_param("i", $order_id);
        $orderItemsStmt->execute();
        $orderItems = $orderItemsStmt->get_result();

        while ($orderItem = $orderItems->fetch_assoc()) {
            $menu_item_id = $orderItem['item_id'];
            $ordered_qty = intval($orderItem['quantity']);

            // ✅ 3. Get ingredients for the menu item
            $ingredientsStmt = $conn->prepare("
                SELECT si.id AS stock_id, si.item_name, si.quantity AS stock_qty, si.reorder_level,
                       mi.quantity_required
                FROM menu_ingredients mi
                JOIN stock_items si ON mi.ingredient_id = si.id
                WHERE mi.menu_item_id = ?
            ");
            $ingredientsStmt->bind_param("i", $menu_item_id);
            $ingredientsStmt->execute();
            $ingredients = $ingredientsStmt->get_result();

            if ($ingredients->num_rows === 0) {
                throw new Exception("No ingredients mapped for menu item ID: $menu_item_id");
            }

            while ($ingredient = $ingredients->fetch_assoc()) {
                $stock_id = $ingredient['stock_id'];
                $item_name = $ingredient['item_name'];
                $available_qty = floatval($ingredient['stock_qty']);
                $reorder_level = floatval($ingredient['reorder_level']);
                $required_qty = floatval($ingredient['quantity_required']) * $ordered_qty;

                // ✅ 4. Check availability
                if ($available_qty < $required_qty) {
                    throw new Exception("Not enough stock for ingredient: $item_name");
                }

                // ✅ 5. Deduct stock
                $deductStmt = $conn->prepare("UPDATE stock_items SET quantity = quantity - ? WHERE id = ?");
                $deductStmt->bind_param("di", $required_qty, $stock_id);
                $deductStmt->execute();

                // ✅ 6. Insert low stock alert if needed
                $new_qty = $available_qty - $required_qty;
                if ($new_qty <= $reorder_level) {
                    $checkAlert = $conn->prepare("SELECT id FROM low_stock_alerts WHERE item_name = ? AND is_read = 0");
                    $checkAlert->bind_param("s", $item_name);
                    $checkAlert->execute();
                    $existing = $checkAlert->get_result();

                    if ($existing->num_rows === 0) {
                        $message = "$item_name is low (" . number_format($new_qty, 2) . " units left)";
                        $insertAlert = $conn->prepare("INSERT INTO low_stock_alerts (item_name, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
                        $insertAlert->bind_param("ss", $item_name, $message);
                        $insertAlert->execute();
                    }
                }
            }
        }

        // ✅ 7. Commit transaction
        $conn->commit();
        header("Location: manager_view_orders.php?msg=Order ID $order_id processed & stock updated.");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "Failed to process order: " . $e->getMessage();
    }
} else {
    echo "Invalid order ID.";
}

$conn->close();
?>
