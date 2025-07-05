<?php
session_start();
include('../db.php');

// Fetch all orders with order items
$sql = "SELECT o.id AS order_id, o.table_number, o.order_time, oi.item_name, oi.quantity, oi.price 
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        ORDER BY o.order_time DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group orders by order_id
$grouped_orders = [];
foreach ($orders as $row) {
    $grouped_orders[$row['order_id']]['table_number'] = $row['table_number'];
    $grouped_orders[$row['order_id']]['order_time'] = $row['order_time'];
    $grouped_orders[$row['order_id']]['items'][] = [
        'item_name' => $row['item_name'],
        'quantity' => $row['quantity'],
        'price' => $row['price']
    ];
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Bills</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <body class="bg-light">
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .sidebar {
        height: 100vh;
        background-color: #1e3a8a;
        color: white;
        position: fixed;
        width: 240px;
        padding-top: 30px;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }

    .sidebar h4 {
        text-align: center;
        font-weight: bold;
        margin-bottom: 30px;
        color: #facc15;
    }

    .sidebar a {
        color: white;
        display: block;
        padding: 14px 20px;
        text-decoration: none;
        font-size: 15px;
        transition: 0.3s;
    }

    .sidebar a i {
        margin-right: 10px;
    }

    .sidebar a:hover {
        background-color: #2563eb;
        color: #facc15;
    }

    .logout-btn {
        display: block;
        margin: 30px auto;
        background-color: #ef4444;
        padding: 10px 24px;
        border-radius: 30px;
        text-align: center;
        text-decoration: none;
        font-size: 15px;
        font-weight: 500;
        color: white;
        transition: 0.3s;
        width: 80%;
    }

    .logout-btn:hover {
        background-color: #dc2626;
        transform: translateY(-2px);
    }

    .main-content {
        margin-left: 260px;
        padding: 30px;
    }

    .card-header {
        font-size: 16px;
    }

    .table th, .table td {
        font-size: 14px;
    }
</style>

    <div class="sidebar">
  <h2><i class="fas fa-utensils"></i> Admin Panel</h2>
  <a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
  <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
  <a href="manage_stock.php"><i class="fas fa-boxes"></i> Manage Stock</a>
  <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
  <a href="approvals.php"><i class="fas fa-check-circle"></i> Approvals</a>
  <a href="add_menu_item.php"><i class="fas fa-plus-circle"></i> Add Menu Item</a>
      <a href="view_menu_items.php" class="active"><i class="fas fa-eye"></i> View Menu Items</a>
  
  <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>


<div class="main-content">
    <h2 class="mb-4 text-center">View Bills</h2>

    <?php foreach ($grouped_orders as $order_id => $order): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <strong>Order ID:</strong> <?= $order_id ?> |
                <strong>Table:</strong> <?= htmlspecialchars($order['table_number']) ?> |
                <strong>Time:</strong> <?= $order['order_time'] ?>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price (Rs)</th>
                            <th>Total (Rs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($order['items'] as $item):
                            $line_total = $item['quantity'] * $item['price'];
                            $total += $line_total;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= number_format($item['price'], 2) ?></td>
                            <td><?= number_format($line_total, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="font-weight-bold">
                            <td colspan="3" class="text-right">Total</td>
                            <td><?= number_format($total, 2) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>

