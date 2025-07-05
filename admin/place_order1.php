<?php
session_start();

// DB connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=restaurant", "root", ""); // Adjust credentials
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check user login
if (!isset($_SESSION['customer_id'])) {
    die("Please log in to place an order.");
}

// Check cart
if (empty($_SESSION['cart'])) {
    die("Your cart is empty.");
}

try {
    $pdo->beginTransaction();

    // Calculate total price
    $totalPrice = 0;
    foreach ($_SESSION['cart'] as $item) {
        $price = isset($item['price']) ? floatval($item['price']) : 0;
        $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;
        $totalPrice += $price * $quantity;
    }

    // Insert order
    $stmtOrder = $pdo->prepare("INSERT INTO orders (customer_id, order_date, total_price) VALUES (?, NOW(), ?)");
    $stmtOrder->execute([$_SESSION['customer_id'], $totalPrice]);
    $orderId = $pdo->lastInsertId();

    // Prepare other statements
    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, item_id, item_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
    $stmtCheck = $pdo->prepare("SELECT id FROM menu_items WHERE id = ?");
    $stmtIngredients = $pdo->prepare("SELECT ingredient_id, quantity_required FROM menu_ingredients WHERE menu_item_id = ?");
    $stmtReduce = $pdo->prepare("UPDATE ingredients SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");

    // Process each cart item
    foreach ($_SESSION['cart'] as $item) {
        $itemId = isset($item['item_id']) ? intval($item['item_id']) : 0;
        $itemName = isset($item['item_name']) ? $item['item_name'] : '';
        $price = isset($item['price']) ? floatval($item['price']) : 0;
        $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;

        if ($itemId > 0 && $itemName !== '' && $price > 0 && $quantity > 0) {
            // Verify menu item exists
            $stmtCheck->execute([$itemId]);
            if ($stmtCheck->rowCount() > 0) {
                // Insert order item
                $stmtItem->execute([$orderId, $itemId, $itemName, $price, $quantity]);

                // Reduce ingredient quantities
                $stmtIngredients->execute([$itemId]);
                while ($ingredient = $stmtIngredients->fetch(PDO::FETCH_ASSOC)) {
                    $ingredientId = $ingredient['ingredient_id'];
                    $qtyPerItem = $ingredient['quantity_required'];
                    $totalToReduce = $qtyPerItem * $quantity;

                    if ($totalToReduce > 0) {
                        // Only reduce if enough quantity exists to prevent negative stock
                        $stmtReduce->execute([$totalToReduce, $ingredientId, $totalToReduce]);
                        if ($stmtReduce->rowCount() === 0) {
                            throw new Exception("Not enough stock for ingredient ID: $ingredientId");
                        }
                    }
                }
            }
        }
    }

    $pdo->commit();
    unset($_SESSION['cart']);
} catch (Exception $e) {
    $pdo->rollBack();
    die("<p style='color:red; font-weight:bold;'>Order failed: " . htmlspecialchars($e->getMessage()) . "</p>");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Order Placed</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .card {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card h2 {
            color: #28a745;
            margin-bottom: 15px;
        }
        .card p {
            font-size: 18px;
            margin-bottom: 25px;
        }
        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        .buttons a {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }
        .buttons a:hover {
            background-color: #0056b3;
        }
        .buttons a.back {
            background-color: #6c757d;
        }
        .buttons a.back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>✅ Order Placed Successfully!</h2>
    <p>Your order ID is <strong>#<?= htmlspecialchars($orderId) ?></strong>.</p>
    <div class="buttons">
        <a href="view_menu.php" class="back">← Back to Menu</a>
        <a href="customer_orders.php">View Orders</a>
    </div>
</div>

</body>
</html>
