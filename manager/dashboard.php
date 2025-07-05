<?php
session_start();
include('../db.php');

// Allow access to manager only
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../login.php");
    exit();
}

// Get unread low stock alerts count
// Get unread low stock alerts count
$alert_sql = "SELECT COUNT(*) AS count FROM low_stock_alerts WHERE is_read = 0";
$alert_result = $conn->query($alert_sql);
$alert_count = $alert_result->fetch(PDO::FETCH_ASSOC)['count'];


// Pending stock items count
$pendingTotal = $conn->query("SELECT COUNT(*) FROM pending_stock_items")->fetchColumn();

$approval_message = '';
if (isset($_GET['approval_status'])) {
    if ($_GET['approval_status'] == 'success') {
        $approval_message = "Approval was successful!";
    } elseif ($_GET['approval_status'] == 'failure') {
        $approval_message = "Approval failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manager Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
            display: flex;
        }
        .sidebar {
            width: 240px;
            height: 100vh;
            background-color: #1e3a8a;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 30px 0;
            position: fixed;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 22px;
        }
        .sidebar a {
            padding: 14px 20px;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: background 0.3s ease;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .sidebar a:hover {
            background-color: #2563eb;
        }
        .logout-btn-sidebar {
            margin-top: auto;
            margin: 20px;
            background-color: #ef4444;
            text-align: center;
            border-radius: 6px;
            padding: 12px 0;
        }
        .logout-btn-sidebar:hover {
            background-color: #dc2626;
        }

        .main-content {
            margin-left: 240px;
            padding: 30px;
            width: calc(100% - 240px);
            position: relative;
        }

        .top-right-alert {
            position: absolute;
            top: 30px;
            right: 30px;
        }

        h1 {
            font-size: 28px;
            color: #1f2937;
        }
        .welcome {
            color: #4b5563;
            margin-bottom: 20px;
        }
        .approval-message {
            background-color: #d1fae5;
            color: #065f46;
            border-left: 6px solid #10b981;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .pending-box {
            max-width: 600px;
            margin: 0 auto 30px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .pending-box h3 {
            color: #1e40af;
            margin-bottom: 15px;
        }
        .pending-count {
            font-size: 24px;
            font-weight: bold;
            color: #ef4444;
        }
        .pending-box a {
            margin-top: 15px;
            background-color: #2563eb;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
        }
        .pending-box a:hover {
            background-color: #1d4ed8;
        }

        .card-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .card {
            flex: 1 1 calc(33.33% - 20px);
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: all 0.2s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }
        .card i {
            font-size: 26px;
            color: #3b82f6;
            margin-bottom: 10px;
        }
        .card p {
            font-size: 15px;
            font-weight: 600;
            margin: 10px 0;
            color: #1f2937;
        }
        .card a {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 14px;
            border-radius: 6px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            font-size: 14px;
        }
        .card a:hover {
            background-color: #2563eb;
        }

        .btn-outline-warning {
            border: 2px solid #f59e0b;
            color: #f59e0b;
            background-color: white;
            border-radius: 50%;
            padding: 10px 12px;
            font-size: 18px;
        }

        .btn-outline-warning:hover {
            background-color: #fde68a;
        }

        .badge {
            font-size: 12px;
        }

        @media (max-width: 992px) {
            .card {
                flex: 1 1 calc(50% - 20px);
            }
        }
        @media (max-width: 600px) {
            .card {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>

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
    <!-- Bell icon notification -->
    <div class="top-right-alert">
        <a href="low_stock_alerts.php" class="btn btn-outline-warning position-relative">
            <i class="fas fa-bell"></i>
            <?php if ($alert_count > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?= $alert_count ?>
                </span>
            <?php endif; ?>
        </a>
    </div>

    <h1>👨‍💼 Manager Dashboard</h1>
    <p class="welcome">Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>

    <?php if ($approval_message): ?>
        <div class="approval-message"><?= htmlspecialchars($approval_message) ?></div>
    <?php endif; ?>

    <div class="pending-box">
        <h3>🔔 Pending Approvals</h3>
        <p>You have <span class="pending-count"><?= $pendingTotal ?></span> stock item(s) pending approval.</p>
        <?php if ($pendingTotal > 0): ?>
            <a href="approve_stock_items.php">Review Now</a>
        <?php endif; ?>
    </div>

    <!-- Cards -->
    <div class="card-grid">
        <div class="card">
            <i class="fas fa-box"></i>
            <p>View Stock Levels</p>
            <a href="view_stock.php">Go</a>
        </div>
      
        
        <div class="card">
            <i class="fas fa-sync-alt"></i>
            <p>Approve Reorder Requests</p>
            <a href="manager_reorder_requests.php">Go</a>
        </div>
        
        <div class="card">
            <i class="fas fa-chart-bar"></i>
            <p>View Reports</p>
            <a href="view_reports.php">Go</a>
        </div>
        <div class="card">
            <i class="fas fa-bell"></i>
            <p>Low Stock Alerts</p>
            <a href="low_stock_alerts.php">Go</a>
        </div>
        <div class="card">
            <i class="fas fa-check-circle"></i>
            <p>view_Orders</p>
            <a href="manager_view_orders.php">Go</a>
        </div>
    </div>
</div>

</body>
</html>
