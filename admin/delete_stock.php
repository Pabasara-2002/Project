<?php
require '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // First delete related alerts
    $stmt1 = $conn->prepare("DELETE FROM low_stock_alerts WHERE item_id = ?");
    $stmt1->execute([$id]);

    // Then delete the item
    $stmt2 = $conn->prepare("DELETE FROM stock_items WHERE id = ?");
    $stmt2->execute([$id]);

}
// Redirect the user to the 'manage_stock.php' page after successful deletion
header("Location: manage_stock.php");
exit();
?>
