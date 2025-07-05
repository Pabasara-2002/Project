<?php
session_start();
require_once 'db.php'; // Your PDO connection file

if (!isset($_SESSION['customer_id'])) {
    die("Please login first.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['order_id'])) {
    die("Invalid request.");
}

$customer_id = $_SESSION['customer_id'];
$order_id = intval($_POST['order_id']);

// Verify the order belongs to logged in customer and is unpaid
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND customer_id = ?");
$stmt->execute([$order_id, $customer_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found or you don't have permission.");
}

if ($order['payment_status'] === 'paid') {
    die("Order is already paid.");
}

// Simulate payment success (in real use, integrate with PayHere SDK or API here)
// Update order payment_status to 'paid' and order_status to 'paid'
$update = $pdo->prepare("UPDATE orders SET payment_status = 'paid', order_status = 'paid' WHERE id = ?");
$update->execute([$order_id]);

// Redirect back to orders page with success message
header("Location: customer_orders.php?msg=success");
exit;
?>
