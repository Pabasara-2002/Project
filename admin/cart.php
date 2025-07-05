<?php
session_start();

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['index']) && isset($_POST['action'])) {
    $index = (int)$_POST['index'];
    if (isset($_SESSION['cart'][$index])) {
        if ($_POST['action'] === 'increase') {
            $_SESSION['cart'][$index]['quantity'] += 1;
        } elseif ($_POST['action'] === 'decrease' && $_SESSION['cart'][$index]['quantity'] > 1) {
            $_SESSION['cart'][$index]['quantity'] -= 1;
        }
    }
    header("Location: cart.php");
    exit();
}

$cart = $_SESSION['cart'];
$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
        }

        h2 {
            text-align: center;
            margin-top: 30px;
        }

        table {
            border-collapse: collapse;
            width: 90%;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px 14px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .quantity-control {
            display: flex;
            justify-content: center;
            gap: 5px;
            align-items: center;
        }

        .quantity-control button {
            padding: 4px 10px;
            font-size: 16px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .quantity-control input {
            width: 40px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 4px;
        }

        .total-row {
            font-weight: bold;
            background-color: #fafafa;
        }

        .order-button, .back-button {
            display: inline-block;
            margin: 15px 10px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            text-align: center;
        }

        .order-button:hover {
            background-color: #218838;
        }

        .back-button {
            background-color: #6c757d;
        }

        .back-button:hover {
            background-color: #5a6268;
        }

        .button-group {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<h2>Your Shopping Cart</h2>

<?php if (count($cart) === 0): ?>
    <p style="text-align:center;">Your cart is empty.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>Item Name</th>
            <th>Price (Rs.)</th>
            <th>Quantity</th>
            <th>Subtotal (Rs.)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cart as $index => $item): 
            $item_name = isset($item['item_name']) ? $item['item_name'] : 'Unknown';
            $price = isset($item['price']) ? (float)$item['price'] : 0;
            $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;
            $subtotal = $price * $quantity;
            $total += $subtotal;
        ?>
        <tr>
            <td><?= htmlspecialchars($item_name) ?></td>
            <td><?= number_format($price, 2) ?></td>
            <td>
                <form method="post" class="quantity-control">
                    <input type="hidden" name="index" value="<?= $index ?>">
                    <button type="submit" name="action" value="decrease">−</button>
                    <input type="text" name="quantity" value="<?= $quantity ?>" readonly>
                    <button type="submit" name="action" value="increase">+</button>
                </form>
            </td>
            <td><?= number_format($subtotal, 2) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="3">Total:</td>
            <td><?= number_format($total, 2) ?></td>
        </tr>
    </tbody>
</table>

<div class="button-group">
    <form method="post" action="place_order1.php" style="display:inline;">
        <input type="submit" name="order_now" value="Order Now" class="order-button" />
    </form>
    <a href="view_menu.php" class="back-button">← Back to Menu</a>
</div>

<?php endif; ?>

</body>
</html>
