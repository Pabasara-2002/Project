<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($action === 'approve') {
    $stmt = $conn->prepare("UPDATE reorder_request SET status = 'Approved' WHERE id = ?");
    $stmt->execute([$id]);
} elseif ($action === 'reject') {
    $stmt = $conn->prepare("UPDATE reorder_request SET status = 'Rejected' WHERE id = ?");
    $stmt->execute([$id]);
}
if ($request) {
            $stmt2 = $conn->prepare("UPDATE stock SET quantity = quantity + ? WHERE item_name = ?");
            $stmt2->execute([$request['quantity'], $request['item_name']]);
}
    header("Location: approve_reorder_requests.php");
    exit();
}

$sql = "SELECT id, item_name, quantity, requested_by, request_date 
        FROM reorder_request 
        WHERE status = 'Pending' 
        ORDER BY request_date DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Reorder Requests</title>
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-left: 260px;
            padding: 40px;
        }
        .table th {
            background-color: #1e40af;
            color: white;
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
       
        h2 {
            color:2rgb(1, 8, 22);
        }
        .btn-approve {
            background-color: #16a34a;
            color: white;
        }
        .btn-reject {
            background-color: #dc2626;
            color: white;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Manager</h2>
    <a href="view_stock.php"><i class="fas fa-box"></i> View Stock</a>
    
    <a href="approve_adjustments.php"><i class="fas fa-cogs"></i> Stock Adjustments</a>
    <a href="approve_stock_items.php"><i class="fas fa-file-signature"></i> Pending Items</a>
    <a href="view_reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
   
    <a href="approve_reorder_requests.php"><i class="fas fa-sync-alt"></i> Approve Reorder Requests</a>
    <a href="low_stock_alerts.php"><i class="fas fa-bell"></i> Low Stock Alerts</a>
    <a href="manager_view_orders.php"><i class="fas fa-receipt"></i> View Orders</a>
    <a href="../logout.php" class="logout-btn-sidebar">Logout</a>
</div>

<div class="container">
    <h2><i class="fas fa-retweet me-2"></i>Pending Reorder Requests</h2>

    <?php if (count($requests) === 0): ?>
        <p>No pending reorder requests.</p>
    <?php else: ?>
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Item Id</th>
                        <th>Quantity</th>
                        <th>Requested By</th>
                        <th>Request Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                        <tr>
                            <td><?= htmlspecialchars($req['item_name']) ?></td>
                            <td><?= htmlspecialchars($req['quantity']) ?></td>
                            <td><?= htmlspecialchars($req['requested_by']) ?></td>
                            <td><?= htmlspecialchars($req['request_date']) ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $req['id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-sm btn-approve me-1">
                                        <i class="fas fa-check-circle me-1"></i>Approve
                                    </button>
                                </form>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $req['id'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-sm btn-reject">
                                        <i class="fas fa-times-circle me-1"></i>Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
