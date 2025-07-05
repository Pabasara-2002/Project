<?php
session_start();
require_once 'db.php'; // This should create $pdo PDO connection

if (isset($_GET['msg']) && $_GET['msg'] === 'success') {
    echo '<div style="padding:10px; background:#d4edda; color:#155724; margin-bottom:15px; border-radius:5px;">';
    echo '✅ Payment Successful! Your order has been placed.';
    echo '</div>';
}

if (!isset($_SESSION['customer_id'])) {
    die("Please log in to view your orders.");
}

$customerId = $_SESSION['customer_id'];
$todayOnly = isset($_GET['today']) && $_GET['today'] === '1';

if ($todayOnly) {
    $stmtOrders = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? AND DATE(order_date) = CURDATE() ORDER BY order_date DESC");
} else {
    $stmtOrders = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
}

$stmtOrders->execute([$customerId]);
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Your existing HTML/CSS unchanged -->


<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #eef2f5;
            margin: 0;
            padding: 30px;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        .order {
            background: white;
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order h3 {
            margin: 0;
            color: #007bff;
        }
        .order small {
            color: #888;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background: #f0f0f0;
        }
        .order-total {
            text-align: right;
            margin-top: 10px;
            font-weight: bold;
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 7px 15px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }
        .btn:hover {
            background-color: #c82333;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .filter-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            padding: 8px 12px;
            background-color: #17a2b8;
            color: white;
            border-radius: 5px;
        }
        .filter-link:hover {
            background-color: #138496;
        }

        /* ✅ Status badges */
        .badge {
            padding: 5px 10px;
            border-radius: 12px;
            color: white;
            font-size: 13px;
            font-weight: bold;
        }
        .badge-pending {
            background-color: #ffc107; /* yellow */
        }
        .badge-paid {
            background-color: #28a745; /* green */
        }
        .badge-cancelled {
            background-color: #dc3545; /* red */
        }
        .text-success {
            color: #28a745;
            font-weight: bold;
            margin-left: 10px;
        }
        .text-warning {
            color: #ffc107;
            font-weight: bold;
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="container">

    <h2>🧾 My Orders</h2>

    <a href="?today=1" class="filter-link">📅 View Today's Orders</a>
    <a href="customer_orders.php" class="filter-link" style="background-color: #6c757d;">🔁 View All</a>

    <?php if (count($orders) === 0): ?>
        <p>You have no orders<?= $todayOnly ? " today" : "" ?>.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order">
                <div class="order-header">
                    <h3>Order #<?= htmlspecialchars($order['id']) ?></h3>
                    <?php
                        $status = strtolower(trim($order['order_status']));
                        if ($status === 'paid') {
                            echo '<span class="badge badge-paid">Paid</span>';
                        } elseif ($status === 'cancelled') {
                            echo '<span class="badge badge-cancelled">Cancelled</span>';
                        } elseif ($status === 'approved') {
                            echo '<span class="badge badge-pending">Approved</span>';
                        } else {
                            echo '<span class="badge badge-pending">Pending</span>';
                        }
                    ?>
                </div>
                <small>Placed on: <?= htmlspecialchars($order['order_date']) ?></small>

                <?php
                $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
                $stmtItems->execute([$order['id']]);
                $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['item_name']) ?></td>
                                <td><?= (int)$item['quantity'] ?></td>
                                <td>Rs. <?= number_format($item['price'], 2) ?></td>
                                <td>Rs. <?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p class="order-total">Total: Rs. <?= number_format($order['total_price'], 2) ?></p>

                <!-- Cancel button for pending -->
                <?php if ($status === 'pending'): ?>
                    <a href="cancel_order.php?id=<?= $order['id'] ?>" class="btn" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel Order</a>
                <?php endif; ?>

                <!-- Your full payment UI block -->
                <?php if ($order['order_status'] === 'approved' && $order['payment_status'] === 'unpaid'): ?>
                    <form method="POST" action="payhere_payment.php" style="margin-top:10px;">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" class="btn btn-success">💳 Pay Now</button>
                    </form>
                <?php elseif ($order['payment_status'] === 'paid'): ?>
                    <span class="text-success">✅ Paid</span>
                <?php else: ?>
                    <span class="text-warning">⏳ Awaiting Approval</span>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
