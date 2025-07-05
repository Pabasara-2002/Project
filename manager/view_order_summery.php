<?php
session_start();
include('../db.php');

// Optional: check if manager is logged in

$stmt = $conn->query("SELECT * FROM order_summary ORDER BY ordered_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5 bg-light">
    <div class="container bg-white p-4 rounded shadow">
        <h3 class="mb-4">📋 Order Summary</h3>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Placed By</th>
                    <th>Order Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?= $row['order_id'] ?></td>
                        <td><?= $row['item_name'] ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td><?= $row['placed_by'] ?></td>
                        <td><?= $row['ordered_at'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
