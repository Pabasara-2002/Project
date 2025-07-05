<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'stock') {
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT item_name, quantity, reason, recorded_by, recorded_at FROM stock_wastage ORDER BY recorded_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$wastage_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Stock Wastage Records</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #1e3a8a;
            color: #fff;
            padding: 20px 15px;
            flex-shrink: 0;
        }

        .sidebar h4 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .sidebar a {
            display: block;
            color: #e0e7ff;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: 0.3s;
            font-size: 15px;
        }

        .sidebar a:hover {
            background-color: #3b82f6;
            color: white;
        }

        .logout-btn {
            background-color: #dc2626;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
            border-radius: 30px;
            display: block;
            text-decoration: none;
            font-weight: 500;
        }

        .main-content {
            flex: 1;
            padding: 30px;
        }

        .card {
            border-radius: 12px;
        }

        .card-body {
            padding: 25px;
        }

        .table thead th {
            background-color: #343a40;
            color: #fff;
            text-align: center;
        }

        .table td {
            vertical-align: middle;
            text-align: center;
        }

        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
        <h4><i class="fas fa-user-cog me-2"></i>Stock Keeper</h4>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
        <a href="view_stock.php"><i class="fas fa-boxes me-2"></i> View Stock Levels</a>
        <a href="low_stock.php"><i class="fas fa-exclamation-triangle me-2"></i> Low Stock Alerts</a>
        <a href="add_stock.php"><i class="fas fa-plus-circle me-2"></i> Add New Stock</a>
        <a href="stock_adjustment.php"><i class="fas fa-sliders-h me-2"></i> Stock Adjustment</a>
        <a href="reorder_level.php"><i class="fas fa-retweet me-2"></i> Reorder Level</a>
        <a href="reorder_request.php"><i class="fas fa-paper-plane me-2"></i> Reorder Requests</a>
        <a href="record_stock.php"><i class="fas fa-trash-alt me-2"></i> Record Stock Wastage</a>
        <a href="view_wastage.php"><i class="fas fa-trash-restore me-2"></i> View Stock Wastage</a>
        <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="mb-4"><i class="fas fa-trash-alt"></i> Stock Wastage Records</h2>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Reason</th>
                                <th>Recorded By</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($wastage_records) > 0): ?>
                                <?php foreach ($wastage_records as $record): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($record['item_name']) ?></td>
                                        <td><?= htmlspecialchars($record['quantity']) ?></td>
                                        <td><?= htmlspecialchars($record['reason']) ?></td>
                                        <td><?= htmlspecialchars($record['recorded_by']) ?></td>
                                        <td><?= htmlspecialchars($record['recorded_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center">No wastage records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
