<?php
$conn = new mysqli("localhost", "root", "", "restaurant");

// Get the ingredient with the lowest quantity
$sql = "SELECT l.*, i.ingredient_name, i.quantity
        FROM low_stock_alerts l 
        JOIN ingredients i ON l.ingredient_id = i.ingredient_id 
        ORDER BY i.quantity ASC, l.created_at DESC 
        LIMIT 1";  // Only the lowest one
$result = $conn->query($sql);

// Fetch one row
$alert = $result->fetch_assoc();

// Mark that alert as read (if available)
if ($alert) {
    $alert_id = $alert['id'];
    $conn->query("UPDATE low_stock_alerts SET is_read = 1 WHERE id = $alert_id");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Low Stock Alerts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .sidebar {
            width: 260px;
            background: linear-gradient(to bottom, #1e3a8a, #2563eb);
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 30px 20px;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar h4 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 22px;
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

        .sidebar a:hover,
        .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar a.logout {
            background: #dc2626;
            color: #fff;
            margin-top: 40px;
            justify-content: center;
        }

        .main-content {
            margin-left: 260px;
            padding: 40px;
        }

        .alert-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 30px;
        }

        h2 {
            font-weight: bold;
            margin-bottom: 25px;
            color: rgb(11, 11, 12);
        }

        .table th {
            background-color: rgb(36, 73, 177);
            color: white;
            font-weight: 500;
            text-align: center;
        }

        .table td {
            vertical-align: middle;
            text-align: center;
        }

        @media (max-width: 768px) {
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
<nav class="sidebar">
    <h4><i class="fas fa-utensils me-2"></i>Admin Panel</h4>
    <a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php"><i class="fas fa-users-cog"></i> Manage Users</a>
    <a href="manage_stock.php"><i class="fas fa-boxes"></i> Manage Stock</a>
    <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
    <a href="approvals.php"><i class="fas fa-check-circle"></i> Approvals</a>
    <a href="request_reorder.php"><i class="fas fa-undo-alt"></i> Request Reorder</a>
     <a href="add_menu_item.php"><i class="fas fa-plus-circle"></i> Add Menu Item</a>
  <a href="view_menu_items.php"><i class="fas fa-eye"></i> View Menu Items</a>
    <a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</nav>

<!-- Main Content -->
<div class="main-content">
    <div class="alert-card">
        <h2><i class="bi bi-bell-fill text-warning me-2"></i>Low Stock Alert (Most Critical)</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="text-center">
                    <tr>
                        <th>Ingredient Name</th>
                        <th>Message</th>
                        <th>Current Quantity</th>
                        <th>Alert Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($alert): ?>
                        <tr>
                            <td><?= htmlspecialchars($alert['ingredient_name']) ?></td>
                          <td>
    <?= !empty($alert['message']) 
        ? htmlspecialchars($alert['message']) 
        : 'Low stock: ' . htmlspecialchars($alert['ingredient_name']) . ' is running low' ?>
</td>

                            <td><?= htmlspecialchars($alert['quantity']) ?> units</td>
                            <td><?= date("Y-m-d h:i A", strtotime($alert['created_at'])) ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No low stock alerts found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
