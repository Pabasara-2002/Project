<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'stock') {
    header("Location: ../login.php");
    exit();
}

// Fetch stock items
$stmt = $conn->query("SELECT item_name, quantity, unit, reorder_level, updated_at FROM stock_items ORDER BY item_name");
$stock_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Levels - Stock Keeper</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f1f5f9;
            margin: 0;
        }

        .sidebar {
            height: 100vh;
            background-color: rgb(43, 87, 207);
            color: white;
            position: fixed;
            width: 240px;
            padding-top: 30px;
        }

        .sidebar a {
            color: white;
            display: block;
            padding: 14px 20px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #2563eb;
        }

        .logout-btn {
            margin-top: 30px;
            display: block;
            background-color: #ef4444;
            padding: 10px 24px;
            border-radius: 50px;
            text-align: center;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .logout-btn:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
        }

        h2 {
            margin-bottom: 30px;
            color: #1f2937;
        }

        .card {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: none;
        }

        table th {
            background-color: #1e40af;
            color: white;
        }

        .low-stock {
            background-color: #fff3cd !important;
            color: #856404;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            background: #6b7280;
            padding: 10px 18px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back:hover {
            background-color: #374151;
        }

        .search-box {
            margin-bottom: 20px;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37,99,235,0.25);
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center fw-bold"><i class="fas fa-user-cog me-2"></i>Stock Keeper</h4>
    <a href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
    <a href="view_stock.php"><i class="fas fa-boxes me-2"></i> View Stock Levels</a>
    <a href="low_stock.php"><i class="fas fa-exclamation-triangle me-2"></i> Low Stock Alerts</a>
    <a href="add_stock.php"><i class="fas fa-plus-circle me-2"></i> Add New Stock</a>
    <a href="stock_adjustment.php"><i class="fas fa-sliders-h me-2"></i> Stock Adjustment</a>
   
    <a href="reorder_request.php"><i class="fas fa-paper-plane me-2"></i> Reorder Requests</a>
    <a href="record_stock.php"><i class="fas fa-trash-alt me-2"></i> Record Stock Wastage</a>
    <a href="view_wastage.php"><i class="fas fa-trash-alt me-2"></i> View Stock Wastage</a>
    <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2><i class="fas fa-warehouse me-2"></i>Stock Levels Overview</h2>

    <div class="card p-4">
        <div class="search-box">
            <input type="text" class="form-control" id="stockSearch" placeholder="Search by item name...">
        </div>

        <?php if (!empty($stock_items)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="stockTable">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Reorder Level</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stock_items as $item): 
                        $isLowStock = $item['quantity'] < $item['reorder_level'];
                    ?>
                    <tr class="<?= $isLowStock ? 'table-warning' : '' ?>">
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= htmlspecialchars($item['unit']) ?></td>
                        <td><?= $item['reorder_level'] ?></td>
                        <td><?= date('Y-m-d H:i', strtotime($item['updated_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="alert alert-info">No stock items found in the system.</div>
        <?php endif; ?>
    </div>

    <a href="dashboard.php" class="back"><i class="fas fa-arrow-left me-2"></i>Back to Dashboard</a>
</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Real-time search filter
    document.getElementById("stockSearch").addEventListener("keyup", function () {
        var value = this.value.toLowerCase();
        var rows = document.querySelectorAll("#stockTable tbody tr");

        rows.forEach(function (row) {
            var itemName = row.cells[0].textContent.toLowerCase();
            row.style.display = itemName.includes(value) ? "" : "none";
        });
    });
</script>
</body>
</html>
