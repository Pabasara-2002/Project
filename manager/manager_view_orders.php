<?php
$conn = new mysqli("localhost", "root", "", "restaurant");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all orders
$sql = "SELECT o.id, o.order_time, o.order_status, o.payment_status, c.name AS customer_name
        FROM orders o
        LEFT JOIN customers c ON o.customer_id = c.id
        ORDER BY o.order_time DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager - View Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <h2>Manager - View Orders</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Order Time</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php
            $order_id = $row['id'];
            $customer = $row['customer_name'] ?? 'N/A';
            $order_time = $row['order_time'];
            $order_status = strtolower(trim($row['order_status']));
            $payment_status = strtolower(trim($row['payment_status']));
            ?>
            <tr>
                <td><?= $order_id ?></td>
                <td><?= htmlspecialchars($customer) ?></td>
                <td><?= $order_time ?></td>
                <td><?= ucfirst($order_status) ?></td>
                <td><?= ucfirst($payment_status) ?></td>
                <td>
                    <?php if ($order_status === 'pending'): ?>
                        <!-- ✅ Process Button -->
                        <a href="process_order.php?id=<?= $order_id ?>" class="btn btn-primary btn-sm">Process</a>
                    <?php else: ?>
                        <span class="badge bg-success">Processed</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<a href="dashboard.php" class="btn btn-outline-primary btn-sm mt-2">
    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
</a>

</body>
</html>
