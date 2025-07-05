<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'manager') {
    header("Location: ../login.php");
    exit();
}

include('../db.php');  // Ensure you have a proper DB connection

// Fetch pending stock items
$query = "SELECT * FROM pending_stock_items WHERE status = 'pending'";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Stock Items</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Add your custom styles */
    </style>
</head>
<body>

<!-- Sidebar -->
<!-- (Same sidebar code as before) -->

<!-- Main Content -->
<div class="main-content">
    <h1>Pending Stock Items</h1>
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Supplier</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['item_name']) ?></td>
                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                    <td><?= htmlspecialchars($row['supplier']) ?></td>
                    <td>
                        <a href="approve_item.php?id=<?= $row['id'] ?>">Approve</a> | 
                        <a href="reject_item.php?id=<?= $row['id'] ?>">Reject</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
