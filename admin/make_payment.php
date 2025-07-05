<?php
if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=restaurant", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // First check if payment already exists
        $check = $pdo->prepare("SELECT * FROM payments WHERE order_id = ?");
        $check->execute([$orderId]);

        if ($check->rowCount() > 0) {
            // Update payment status to success
            $update = $pdo->prepare("UPDATE payments SET payment_status = 'success', paid_at = NOW() WHERE order_id = ?");
            $update->execute([$orderId]);
        } else {
            // Insert new payment record
            $insert = $pdo->prepare("INSERT INTO payments (order_id, payment_status, paid_at) VALUES (?, 'success', NOW())");
            $insert->execute([$orderId]);
        }

        // Redirect back to order view
        header("Location: cashier_view_orders.php");
        exit();

    } catch (PDOException $e) {
        die("Payment Error: " . $e->getMessage());
    }
} else {
    echo "Invalid order ID.";
}
?>
