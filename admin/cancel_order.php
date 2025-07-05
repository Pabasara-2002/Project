<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['customer_id'])) {
    die("Access denied.");
}

$customerId = $_SESSION['customer_id'];
$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($orderId > 0) {
    $stmt = $pdo->prepare("UPDATE orders SET order_status = 'cancelled' WHERE id = ? AND customer_id = ? AND order_status = 'pending'");
    $stmt->execute([$orderId, $customerId]);

    header("Location: customer_orders.php?msg=Order cancelled.");
    exit;
} else {
    echo "Invalid order ID.";
}
