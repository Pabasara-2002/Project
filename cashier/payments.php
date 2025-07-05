<?php
session_start();
include('../db.php');

if (!isset($_GET['order_id'])) {
    die("Order ID is required.");
}

$order_id = intval($_GET['order_id']);

// Fetch order total
$sql = "SELECT o.id, o.table_number, o.order_time, o.status,
        SUM(oi.quantity * oi.price) AS total_amount
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        WHERE o.id = ?
        GROUP BY o.id";

$stmt = $conn->prepare($sql);
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

// Handle payment submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_type = $_POST['payment_type'] ?? '';
    $amount = $order['total_amount'];

    if (!in_array($payment_type, ['Cash', 'Card'])) {
        $errors[] = "Invalid payment type selected.";
    }

    if (empty($errors)) {
        // Insert payment record
        $insert = $conn->prepare("INSERT INTO payments (order_id, payment_type, amount) VALUES (?, ?, ?)");
        $insert->execute([$order_id, $payment_type, $amount]);

        // Update order status to paid
        $update = $conn->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
        $update->execute([$order_id]);

        header("Location: payments.php?success=1");
        exit;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Payments - Order #<?= $order_id ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Payment for Order #<?= $order_id ?></h2>

    <div class="card p-4 mb-4">
        <p><strong>Table Number:</strong> <?= htmlspecialchars($order['table_number']) ?></p>
        <p><strong>Order Time:</strong> <?= $order['order_time'] ?></p>
        <p><strong>Total Amount:</strong> Rs. <?= number_format($order['total_amount'], 2) ?></p>
        <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
    </div>

    <?php if ($order['status'] == 'paid'): ?>
        <div class="alert alert-success">This order has already been paid.</div>
    <?php else: ?>
        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="payment_type">Select Payment Type:</label>
                <select name="payment_type" id="payment_type" class="form-control" required>
                    <option value="">-- Select --</option>
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-credit-card"></i> Submit Payment</button>
            <a href="view_bills.php" class="btn btn-secondary">Back to Bills</a>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
