<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

// Get item info from query string
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$item_name = isset($_GET['item']) ? $_GET['item'] : '';
$price = isset($_GET['price']) ? floatval($_GET['price']) : 0.0;

if ($item_id <= 0 || $item_name == '' || $price <= 0) {
    die("Invalid item.");
}

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if item already in cart
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['item_id'] === $item_id) {
        $item['quantity']++;
        $found = true;
        break;
    }
}
if (!$found) {
    $_SESSION['cart'][] = [
        'item_id' => $item_id,
        'item_name' => $item_name,
        'price' => $price,
        'quantity' => 1
    ];
}

header("Location: cart.php");
exit;
