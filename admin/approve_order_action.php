<?php
require '../db.php';

if (isset($_GET['id'], $_GET['action'])) {
    $order_id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'approve') {
        $status = 'approved';
    } elseif ($action == 'reject') {
        $status = 'rejected';
    } else {
        header("Location: cashier_view_orders.php?msg=Invalid action");
        exit;
    }

    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);

    header("Location: cashier_view_orders.php?msg=Order has been $status.");
    exit;
}
?>
