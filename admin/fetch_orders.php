<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=restaurant", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("
        SELECT orders.id, customers.name AS customer_name, orders.created_at,
               COALESCE(payments.payment_status, 'pending') AS payment_status
        FROM orders
        JOIN customers ON orders.customer_id = customers.id
        LEFT JOIN payments ON payments.order_id = orders.id
        ORDER BY orders.created_at DESC
    ");
} catch (PDOException $e) {
    echo "<p class='text-danger'>Database Error: " . $e->getMessage() . "</p>";
    exit;
}
?>

<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Items Ordered</th>
            <th>Time</th>
            <th>Payment</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td>
                    <ul class="mb-0">
                        <?php
                        $orderId = $row['id'];
                        $itemStmt = $pdo->prepare("SELECT item_name, price FROM order_details WHERE order_id = ?");
                        $itemStmt->execute([$orderId]);
                        while ($item = $itemStmt->fetch(PDO::FETCH_ASSOC)):
                        ?>
                            <li><?= htmlspecialchars($item['item_name']) ?> - Rs. <?= number_format($item['price'], 2) ?></li>
                        <?php endwhile; ?>
                    </ul>
                </td>
                <td><?= date('Y-m-d h:i A', strtotime($row['created_at'])) ?></td>
                <td>
                    <?php if ($row['payment_status'] === 'success'): ?>
                        <span class="badge bg-success">Paid</span>
                    <?php else: ?>
                        <a href="make_payment.php?order_id=<?= $row['id'] ?>" class="btn btn-sm btn-success">
                            <i class="fas fa-credit-card"></i> Pay
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
