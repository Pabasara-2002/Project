<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'manager') {
    header("Location: ../login.php");
    exit();
}

include_once 'db_connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the rejected item from pending_stock_items table
    $query = "DELETE FROM pending_stock_items WHERE id = '$id' AND status = 'pending'";
    if (mysqli_query($conn, $query)) {
        header("Location: pending_stock_items.php");  // Redirect to the pending items page
        exit();
    }
}
?>
