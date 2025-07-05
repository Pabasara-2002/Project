<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'cashier') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION['current_order']) || empty($_SESSION['current_order']) || !isset($_SESSION['table'])) {
    echo "<script>alert('No items to submit.'); window.location.href='place_order.php';</script>";
    exit();
}

$table_number = $_SESSION['table'];
$order_items = $_SESSION['current_order'];
$cashier_name = $_SESSION['username'];
$order_time = date("Y-m-d H:i:s");

try {
    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (table_number, order_time) VALUES (?, ?)");
    $stmt->execute([$table_number, $order_time]);
    $order_id = $conn->lastInsertId();

    // Insert each item
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, item_name, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($order_items as $item) {
        $stmt_item->execute([$order_id, $item['item_name'], $item['quantity'], $item['price']]);
    }

    // Clear session
    unset($_SESSION['current_order']);
    unset($_SESSION['table']);

    echo "<script>alert('Order submitted successfully!'); window.location.href='place_order.php';</script>";

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
?>
