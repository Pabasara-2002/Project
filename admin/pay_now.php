<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['customer_id'])) {
    die("Please log in to continue.");
}

$customerId = $_SESSION['customer_id'];
$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($orderId > 0) {
    // Fetch the order to verify ownership and status
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND customer_id = ?");
    $stmt->execute([$orderId, $customerId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        if ($order['order_status'] === 'approved' && $order['payment_status'] === 'unpaid') {
            // Simulate payment
            $update = $pdo->prepare("UPDATE orders SET payment_status = 'paid', order_status = 'paid' WHERE id = ?");
            $update->execute([$orderId]);

            header("Location: customer_orders.php?msg=Payment successful for Order #$orderId.");
            exit;
        } elseif ($order['payment_status'] === 'paid') {
            echo "🟢 Order is already paid.";
        } elseif ($order['order_status'] === 'pending') {
            echo "⚠️ This order hasn't been processed by the manager yet.";
        } else {
            echo "⚠️ Cannot proceed with payment.";
        }
    } else {
        echo "❌ Order not found or access denied.";
    }
} else {
    echo "❌ Invalid request.";
}
