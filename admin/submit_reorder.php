<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stock_id = $_POST['stock_id'];
    $quantity = $_POST['quantity'];
    $requested_by = "admin"; // Use session later if needed

    $stmt = $conn->prepare("INSERT INTO reorder_requests (stock_item_id, quantity, requested_by) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $stock_id, $quantity, $requested_by);

    if ($stmt->execute()) {
        header("Location: request_reorder.php?message=success");
        exit();
    } else {
        header("Location: request_reorder.php?message=error");
        exit();
    }
}
?>
