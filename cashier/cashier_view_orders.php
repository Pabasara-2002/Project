<?php
require '../db.php';

// Fetch orders (includes order_status)
$sql = "SELECT o.id, o.order_time, o.total_price, o.order_status, o.payment_status, c.name AS customer_name
        FROM orders o
        LEFT JOIN customers c ON o.customer_id = c.id
        ORDER BY o.order_time DESC";


$stmt = $conn->query($sql);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cashier - View Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">

    <!-- Success message after actions -->
    <?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_GET['msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <h2>All Orders</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total Price</th>
                <th>Order Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <?php
            $order_id = $order['id'];
            $customer_name = $order['customer_name'] ?? 'N/A';
            $order_time = $order['order_time'] ?? 'N/A';
            $order_status = $order['order_status'] ?? 'pending';

            // Fetch order items for display
            $item_stmt = $conn->prepare("SELECT item_name, price, quantity FROM order_items WHERE order_id = ?");
            $item_stmt->execute([$order_id]);
            $items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);

            $total_price = 0;
            ?>
            <tr>
                <td><?= htmlspecialchars($order_id) ?></td>
                <td><?= htmlspecialchars($customer_name) ?></td>
                <td>
                    <?php foreach ($items as $item):
                        $total_price += $item['price'] * $item['quantity'];
                    ?>
                        <?= htmlspecialchars($item['item_name']) ?> (Qty: <?= intval($item['quantity']) ?>)<br>
                    <?php endforeach; ?>
                </td>
                <td>Rs. <?= number_format($total_price, 2) ?></td>
                <td><?= htmlspecialchars($order_time) ?></td>
                <td>
                    <!-- Display buttons/badges based on order_status -->
                    <?php if (strtolower(trim($order_status)) === 'approved'): ?>

                        <a href="mark_paid.php?id=<?= $order_id ?>" class="btn btn-success btn-sm">Mark as Paid</a>
                    <?php elseif (strtolower(trim($order_status)) === 'paid'): ?>

                        <span class="badge bg-success">Paid</span>
                    <?php else: ?>
                        <span class="badge bg-warning">Pending Approval</span>
                    <?php endif; ?>

                    <a href="delete_order.php?id=<?= $order_id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<a href="dashboard.php" class="btn btn-outline-primary btn-sm mt-2">
    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
</a>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
