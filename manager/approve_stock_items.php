<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("SELECT * FROM pending_stock_items WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $insert = $conn->prepare("INSERT INTO stock_items (item_name, quantity, unit, reorder_level) VALUES (?, ?, ?, ?)");
            $insert->execute([$item['item_name'], $item['quantity'], $item['unit'], $item['reorder_level']]);
            $delete = $conn->prepare("DELETE FROM pending_stock_items WHERE id = ?");
            $delete->execute([$id]);
        }
    } elseif ($action === 'reject') {
        $delete = $conn->prepare("DELETE FROM pending_stock_items WHERE id = ?");
        $delete->execute([$id]);
    }

    header("Location: approve_stock_items.php");
    exit();
}

$stmt = $conn->query("SELECT * FROM pending_stock_items");
$pendingItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Stock Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: #1e40af;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
        }

        .sidebar h2 {
            font-size: 22px;
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            color: white;
            padding: 12px 15px;
            display: block;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .sidebar a:hover {
            background-color: #2563eb;
        }

        .sidebar .logout-btn-sidebar {
            margin-top: 30px;
            background-color: #ef4444;
            text-align: center;
        }

        .sidebar .logout-btn-sidebar:hover {
            background-color: #dc2626;
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        h2.title {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            color: #1e293b;
        }

        table {
            width: 100%;
            margin-top: 20px;
        }

        table thead {
            background-color: #1e3a8a;
            color: white;
        }

        table td, table th {
            text-align: center;
            vertical-align: middle;
        }

        .btn-approve {
            background-color: #16a34a;
            color: white;
        }

        .btn-reject {
            background-color: #dc2626;
            color: white;
        }

        .btn-approve:hover {
            background-color: #15803d;
        }

        .btn-reject:hover {
            background-color: #b91c1c;
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            text-decoration: none;
            color: #2563eb;
            font-weight: 500;
        }

        @media screen and (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }
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
        <div class="container card p-4">
            <h2 class="title">✅ Pending Stock Items for Approval</h2>

            <?php if (count($pendingItems) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Reorder Level</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingItems as $item): ?>
                                <tr>
                                    <td><?= $item['id'] ?></td>
                                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= $item['unit'] ?></td>
                                    <td><?= $item['reorder_level'] ?></td>
                                    <td>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-sm btn-approve">Approve</button>
                                        </form>
                                        <form method="post" class="d-inline ms-2">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-sm btn-reject">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">No pending stock items.</p>
            <?php endif; ?>

            <div class="back-link">
                <a href="dashboard.php">⬅️ Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
