<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch reorder requests with item details
$sql = "SELECT r.id, s.item_name, r.quantity, r.requested_by, r.request_date, r.status
        FROM reorder_request r
        JOIN stock_items s ON r.item_name = s.id
        ORDER BY r.request_date DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Reorder Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background-color: #1d4ed8;
            color: white;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
        .status-approved {
            color: green;
            font-weight: bold;
        }
        .status-rejected {
            color: red;
            font-weight: bold;
        }
        .sidebar {
            height: 100vh;
            background-color: #1e40af;
            color: white;
            width: 240px;
            position: fixed;
            padding-top: 30px;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 12px 20px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #2563eb;
        }
        h2 {
            color: #1e3a8a;
        }
    </style>
</head>
<body>
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


<div class="container">
    <h2><i class="fas fa-retweet me-2"></i>Reorder Requests</h2>

    <?php if (count($requests) === 0): ?>
        <p>No reorder requests found.</p>
    <?php else: ?>
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Requested By</th>
                        <th>Request Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                        <tr>
                            <td><?= htmlspecialchars($req['item_name']) ?></td>
                            <td><?= htmlspecialchars($req['quantity']) ?></td>
                            <td><?= htmlspecialchars($req['requested_by']) ?></td>
                            <td><?= htmlspecialchars($req['request_date']) ?></td>
                            <td class="<?php 
                                if ($req['status'] === 'Pending') echo 'status-pending';
                                elseif ($req['status'] === 'Approved') echo 'status-approved';
                                else echo 'status-rejected';
                            ?>">
                                <?= htmlspecialchars($req['status']) ?>
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
