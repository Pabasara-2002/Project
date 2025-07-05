<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'stock') {
    header("Location: ../login.php");
    exit();
}

// ✅ Fetch unread low stock alert count
$alertQuery = $conn->query("SELECT COUNT(*) AS alert_count FROM low_stock_alerts WHERE is_read = 0");
$alertData = $alertQuery->fetch(PDO::FETCH_ASSOC);
$alertCount = $alertData['alert_count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Keeper Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Poppins', sans-serif;
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

        .content {
            margin-left: 260px;
            padding: 40px;
        }

        .dashboard-card {
            background-color: white;
            border-radius: 16px;
            padding: 30px 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-align: center;
            border-bottom: 4px solid #2b57cf;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.12);
        }

        .card-icon {
            font-size: 38px;
            color: #2b57cf;
        }

        .card-title {
            margin-top: 15px;
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }

        .go-btn {
            margin-top: 12px;
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .go-btn:hover {
            background-color: #1e40af;
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 20px;
            }

            .sidebar {
                display: none;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4><i class="fas fa-user-cog me-2"></i>Stock Keeper</h4>
    <a href="view_stock.php"><i class="fas fa-boxes me-2"></i> View Stock Levels</a>
    <a href="low_stock.php">
        <i class="fas fa-exclamation-triangle me-2"></i> Low Stock Alerts
        <?php if ($alertCount > 0): ?>
            <span class="badge"><?= $alertCount ?></span>
        <?php endif; ?>
    </a>
    <a href="add_stock.php"><i class="fas fa-plus-circle me-2"></i> Add New Stock</a>
    <a href="stock_adjustment.php"><i class="fas fa-sliders-h me-2"></i> Stock Adjustment</a>
    <a href="reorder_request.php"><i class="fas fa-paper-plane me-2"></i> Reorder Requests</a>
    <a href="record_stock.php"><i class="fas fa-trash-alt me-2"></i> Record Stock Wastage</a>
    <a href="view_wastage.php"><i class="fas fa-eye me-2"></i> View Stock Wastage</a>
    <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h2 class="mb-4">📦 Stock Keeper Dashboard</h2>

    <?php if ($alertCount > 0): ?>
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <div>
                ⚠️ You have <?= $alertCount ?> low stock item<?= $alertCount > 1 ? 's' : '' ?>.
                <a href="low_stock.php" class="alert-link">View alerts</a>
            </div>
        </div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php
        $cards = [
            ["icon" => "fa-boxes", "title" => "View Stock Levels", "link" => "view_stock.php"],
            ["icon" => "fa-exclamation-triangle", "title" => "Low Stock Alerts", "link" => "low_stock.php"],
            ["icon" => "fa-plus-circle", "title" => "Add New Stock", "link" => "add_stock.php"],
            ["icon" => "fa-sliders-h", "title" => "Stock Adjustment", "link" => "stock_adjustment.php"],
            ["icon" => "fa-paper-plane", "title" => "Reorder Requests", "link" => "reorder_request.php"],
            ["icon" => "fa-trash-alt", "title" => "Record Stock Wastage", "link" => "record_stock.php"],
            ["icon" => "fa-eye", "title" => "View Stock Wastage", "link" => "view_wastage.php"],
        ];

        foreach ($cards as $card) {
            echo '
            <div class="col">
                <div class="dashboard-card">
                    <i class="fas ' . $card["icon"] . ' card-icon"></i>
                    <div class="card-title">' . $card["title"] . '</div>
                    <a href="' . $card["link"] . '"><button class="go-btn">Go</button></a>
                </div>
            </div>';
        }
        ?>
    </div>
</div>

</body>
</html>
