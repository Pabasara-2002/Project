<?php
$conn = new mysqli("localhost", "root", "", "restaurant");
$result = $conn->query("SELECT * FROM stock_log ORDER BY created_at DESC");
?>

<h2>Stock Usage Log</h2>
<table class="table table-striped">
    <thead><tr><th>Ingredient</th><th>Qty Deducted</th><th>Reason</th><th>Order ID</th><th>Date</th></tr></thead>
    <tbody>
        <?php while ($log = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($log['ingredient_name']) ?></td>
            <td><?= $log['quantity_deducted'] ?></td>
            <td><?= htmlspecialchars($log['reason']) ?></td>
            <td><?= $log['order_id'] ?></td>
            <td><?= $log['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
