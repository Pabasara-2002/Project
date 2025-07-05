<?php
// delete_order.php
require '../db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $orderId = $_GET['id'];

    // First delete from order_items table
    $conn->begin_transaction();

    try {
        $stmt1 = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt1->bind_param("i", $orderId);
        $stmt1->execute();

        $stmt2 = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt2->bind_param("i", $orderId);
        $stmt2->execute();

        $conn->commit();
        header("Location: manager_view_orders.php?msg=Order+deleted+successfully");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error deleting order: " . $e->getMessage();
    }
} else {
    echo "Invalid order ID.";
}
?>
