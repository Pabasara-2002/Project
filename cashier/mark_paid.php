<?php
require '../db.php';

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Check if order is approved first
    $check = $conn->prepare("SELECT order_status FROM orders WHERE id = ?");
    $check->execute([$order_id]);
    $order = $check->fetch();

    if ($order && strtolower(trim($order['order_status'])) === 'approved')
 {
        $stmt = $conn->prepare("UPDATE orders SET order_status = 'paid' WHERE id = ?");
        $stmt->execute([$order_id]);

        header("Location: cashier_view_orders.php?msg=Order marked as paid");
        exit;
    } else {
        header("Location: cashier_view_orders.php?msg=Only approved orders can be marked as paid");
        exit;
    }
} else {
    header("Location: cashier_view_orders.php?msg=Invalid request");
    exit;
}
?>
