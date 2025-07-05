<?php
$orderId = $_GET['order_id'];

require_once 'db.php';
$stmt = $conn->prepare("UPDATE orders SET payment_status = 'paid' WHERE id = ?");
$stmt->execute([$orderId]);
?>

<h2>✅ Payment Successful</h2>
<p>Your order #<?= htmlspecialchars($orderId) ?> is now marked as paid.</p>
<a href="customer_orders.php">Back to Orders</a>
