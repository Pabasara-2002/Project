<?php
require_once '../db.php';

if (isset($_GET['id'])) {
    $menu_item_id = $_GET['id'];

    // Delete from order_items first
    $conn->prepare("DELETE FROM order_items WHERE item_id = ?")->execute([$menu_item_id]);

    // Then delete from menu_ingredients
    $conn->prepare("DELETE FROM menu_ingredients WHERE menu_item_id = ?")->execute([$menu_item_id]);

    // Finally delete from menu_items
    $conn->prepare("DELETE FROM menu_items WHERE id = ?")->execute([$menu_item_id]);

    header("Location: view_menu_items.php?deleted=1");
    exit();
} else {
    echo "Invalid menu item ID.";
}
?>
