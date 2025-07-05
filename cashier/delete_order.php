<?php
require '../db.php';

if (!isset($_GET['id'])) {
    die("Order ID not provided.");
}

$order_id = $_GET['id'];

try {
    // Begin transaction
    $conn->beginTransaction();

    // Step 1: Delete from order_items
    $stmtItems = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmtItems->execute([$order_id]);

    // Step 2: Delete from orders
    $stmtOrder = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmtOrder->execute([$order_id]);

    $conn->commit();

    header("Location: cashier_view_orders.php");
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    echo "Error deleting order: " . $e->getMessage();
}
?>
