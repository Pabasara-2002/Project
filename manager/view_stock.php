<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'manager') {
    header("Location: ../login.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM stock_items ORDER BY updated_at DESC");
$stmt->execute();
$stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager - Current Stock</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: #1e3a8a;
            color: white;
            position: fixed;
            padding-top: 30px;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
        }
        .sidebar a {
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            display: block;
            font-size: 16px;
        }
        .sidebar a:hover {
            background-color: #2563eb;
        }
        .logout-btn-sidebar {
            margin-top: auto;
            margin-left: 20px;
            margin-right: 20px;
            margin-top: 30px;
            background-color: #ef4444;
            padding: 10px;
            border-radius: 6px;
            display: block;
            text-align: center;
        }
        .logout-btn-sidebar:hover {
            background-color: #dc2626;
        }
        .main-content {
            margin-left: 260px;
            padding: 40px;
        }
        .card {
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        table th {
            background-color: #1e40af;
            color: white;
        }
        .back-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<!-- Sidebar -->
<div class="sidebar">
    <h2>Manager</h2>
    <a href="view_stock.php"><i class="fas fa-box"></i> View Stock</a>
    
   
    <a href="approve_stock_items.php"><i class="fas fa-file-signature"></i> Pending Items</a>
    <a href="view_reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
   
    <a href="approve_reorder_requests.php"><i class="fas fa-sync-alt"></i> Approve Reorder Requests</a>
    <a href="low_stock_alerts.php"><i class="fas fa-bell"></i> Low Stock Alerts</a>
    <a href="manager_view_orders.php"><i class="fas fa-receipt"></i> View Orders</a>
    <a href="../logout.php" class="logout-btn-sidebar">Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Back Button -->
   <!-- Back Button -->
<div class="text-end mb-3">
    <a href="dashboard.php" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
    </a>
</div>


    <h2 class="mb-4"><i class="fas fa-boxes me-2"></i>Current Stock Items</h2>

    <div class="card p-3">
        <?php if (!empty($stocks)): ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Reorder Level</th>
                        <th>Unit</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stocks as $item): ?>
                    <tr>
                        <td><?= $item['id'] ?></td>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= $item['reorder_level'] ?></td>
                        <td><?= $item['unit'] ?></td>
                        <td><?= date('Y-m-d H:i', strtotime($item['updated_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="alert alert-info">No stock items found.</div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
