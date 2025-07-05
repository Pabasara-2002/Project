<?php
session_start();
require_once 'db.php';

if (!isset($_GET['order_id'])) {
    die("Invalid request.");
}

$orderId = intval($_GET['order_id']);

// Optional: Validate order ownership if customer login exists

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mock payment logic
    $stmt = $conn->prepare("UPDATE orders SET payment_status = 'paid' WHERE id = ?");
    $stmt->execute([$orderId]);

    header("Location: customer_orders.php?paid=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pay Now</title>
</head>
<body>
    <h2>💳 Confirm Your Payment</h2>
    <form method="post">
        <p>Click below to simulate payment.</p>
        <button type="submit">✅ Confirm Payment</button>
    </form>
</body>
</html>
