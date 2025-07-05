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
            height: 100vh;
            background-color: #2b57cf;
            color: white;
            position: fixed;
            width: 240px;
            padding-top: 30px;
        }

        .sidebar h4 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .sidebar a {
            color: white;
            display: block;
            padding: 14px 20px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #1d4ed8;
        }

        .sidebar a .badge {
            background-color: #ef4444;
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 12px;
            margin-left: 10px;
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
            transition: background-color 0.3s ease, transform 0.2s ease;
            width: 80%;
        }

        .logout-btn:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
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
    <h4><i class="fas fa-user-cog me-2"></i>Stock Keeper</h4>
    <a href="view_stock.php"><i class="fas fa-boxes me-2"></i> View Stock Levels</a>
    <a href="low_stock.php"><i class="fas fa-exclamation-triangle me-2"></i> Low Stock Alerts</a>
    <a href="add_stock.php"><i class="fas fa-plus-circle me-2"></i> Add New Stock</a>
    <a href="stock_adjustment.php"><i class="fas fa-sliders-h me-2"></i> Stock Adjustment</a>
    <a href="reorder_request.php"><i class="fas fa-paper-plane me-2"></i> Reorder Requests</a>
    <a href="record_stock.php"><i class="fas fa-trash-alt me-2"></i> Record Stock Wastage</a>
    <a href="view_wastage.php"><i class="fas fa-eye me-2"></i> View Stock Wastage</a>
    <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
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
