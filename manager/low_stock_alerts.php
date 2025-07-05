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
