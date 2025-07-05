<?php
include '../db.php';
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("UPDATE orders SET status='completed' WHERE id=$id");
}
header("Location: view_orders.php");
exit;
