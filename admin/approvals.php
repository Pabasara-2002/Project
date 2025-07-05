<?php
session_start();
include('../db.php');

// Check if user is admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Approve or Reject handling
if (isset($_GET['action'], $_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("SELECT * FROM pending_stock_items WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $insert = $conn->prepare("INSERT INTO stock_items (item_name, quantity, unit, reorder_level, added_by) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([$item['item_name'], $item['quantity'], $item['unit'], $item['reorder_level'], $item['added_by']]);

            $delete = $conn->prepare("DELETE FROM pending_stock_items WHERE id = ?");
            $delete->execute([$id]);
        }
    } elseif ($action === 'reject') {
        $delete = $conn->prepare("DELETE FROM pending_stock_items WHERE id = ?");
        $delete->execute([$id]);
    }

    header("Location: approvals.php");
    exit();
}

// Fetch pending stock items
$pendingItems = $conn->query("SELECT * FROM pending_stock_items ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approvals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial; background: #f9f9f9; margin: 0; padding: 0; }

        .sidebar {
            width: 260px;
            background: linear-gradient(to bottom, #1e3a8a, #2563eb);
            color: white;
            height: 100vh;
            position: fixed;
            padding: 30px 20px;
            box-shadow: 3px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 24px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 18px;
            color: #e2e8f0;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 8px;
            transition: background 0.3s ease;
        }

        .sidebar a i {
            margin-right: 12px;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: rgba(255,255,255,0.2);
        }

        .sidebar a.logout {
            background: #dc2626;
            color: #fff;
            margin-top: 40px;
            text-align: center;
        }

        .container { margin-left: 260px; padding: 30px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        th, td { border: 1px solid #dee2e6; padding: 12px; text-align: center; }
        th { background-color: #1e40af; color: white; }
        .btn { padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; }
        .approve { background-color: #38a169; color: white; }
        .reject { background-color: #e53e3e; color: white; }
        .badge { font-size: 0.9rem; }
    </style>
</head>
<body>

<!-- Sidebar Start -->
<nav class="col-md-3 col-lg-2 d-md-block sidebar">
    <h4 class="text-center text-white fw-bold mb-4"><i class="fas fa-utensils"></i> Admin Panel</h4>
    <a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php"><i class="fas fa-users-cog"></i> Manage Users</a>
    <a href="manage_stock.php"><i class="fas fa-boxes"></i> Manage Stock</a>
    <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
    <a href="approvals.php" class="active"><i class="fas fa-check-circle"></i> Approvals</a>
    <a href="add_menu_item.php"><i class="fas fa-plus-circle"></i> Add Menu Item</a>
  <a href="view_menu_items.php"><i class="fas fa-eye"></i> View Menu Items</a>
  
    <a href="request_reorder.php"><i class="fas fa-undo-alt"></i> Request Reorder</a>
    <a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</nav>
<!-- Sidebar End -->

<div class="container">
    <h2>📝 Pending Stock Approvals</h2>

    <a href="admin_dashboard.php" style="
        position: fixed;
        bottom: 70px;
        right: 30px;
        padding: 10px 18px;
        background-color: #3b82f6;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    ">🔙 Back to Dashboard</a>

    <?php if (count($pendingItems) > 0): ?>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Reorder Level</th>
                    <th>Added By</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= $item['unit'] ?></td>
                        <td><?= $item['reorder_level'] ?></td>
                        <td><?= htmlspecialchars($item['added_by']) ?></td>
                        <td><span class="badge bg-warning text-dark px-3 py-2">Pending</span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending items for approval.</p>
    <?php endif; ?>
</div>

</body>
</html>
