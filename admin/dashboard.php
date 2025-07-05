<?php
session_start();
include('../db.php');

// Only allow logged-in admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Low stock
$lowStockThreshold = 10;
$lowStockStmt = $conn->prepare("SELECT * FROM stock_items WHERE quantity < ?");
$lowStockStmt->execute([$lowStockThreshold]);
$lowStockItems = $lowStockStmt->fetchAll(PDO::FETCH_ASSOC);

// Totals
$totalStockItems = $conn->query("SELECT COUNT(*) FROM stock_items")->fetchColumn();
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Recent updates for chart (latest 7)
$recentUpdatesStmt = $conn->query("SELECT item_name, quantity FROM stock_items ORDER BY updated_at DESC LIMIT 7");
$recentStockUpdates = $recentUpdatesStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f6fa;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 240px;
      height: 100vh;
      background: #1e40af;
      color: white;
      padding: 30px 20px;
      box-sizing: border-box;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar h2 {
      font-size: 22px;
      margin-bottom: 30px;
      color: #fff;
    }

    .sidebar a {
      display: block;
      color: white;
      text-decoration: none;
      margin-bottom: 15px;
      padding: 12px;
      border-radius: 8px;
      transition: background 0.2s ease;
    }

    .sidebar a i {
      margin-right: 10px;
    }

    .sidebar a:hover, .sidebar a.active {
      background: rgb(123, 168, 241);
    }

    .sidebar a.logout {
      background: #ef4444;
    }

    .main {
      margin-left: 240px;
      padding: 40px;
    }

    .main h1 {
      font-size: 28px;
      margin-bottom: 25px;
      color: #1e293b;
    }

    .cards {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      margin-bottom: 30px;
    }

    .card {
      flex: 1;
      background: white;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
      display: flex;
      align-items: center;
      gap: 15px;
      transition: transform 0.2s ease;
    }

    .card:hover {
      transform: translateY(-4px);
    }

    .card i {
      font-size: 32px;
      padding: 20px;
      border-radius: 50%;
      background: #e0f2fe;
      color: #0284c7;
    }

    .card.green i { background: #dcfce7; color: #16a34a; }
    .card.yellow i { background: #fef9c3; color: #ca8a04; }

    .card-content h2 {
      font-size: 15px;
      color: #6b7280;
      margin: 0;
    }

    .card-content p {
      font-size: 26px;
      margin: 5px 0 0;
      font-weight: bold;
      color: #111827;
    }

    .low-stock-alert {
      background: #fff3cd;
      color: #856404;
      padding: 18px;
      border-left: 6px solid #ffc107;
      border-radius: 8px;
      margin-bottom: 25px;
    }

    .table-container {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      margin-top: 30px;
    }

    @media (max-width: 768px) {
      .main { margin-left: 0; padding: 20px; }
      .sidebar {
        position: static;
        width: 100%;
        height: auto;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
      }
      .cards { flex-direction: column; }
      .card { width: 100%; }
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2><i class="fas fa-utensils"></i> Admin Panel</h2>
  <a href="admin_dashboard.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
  <hr style="margin: 20px 0; border-color: #444;">
  <a href="../manager/dashboard.php"><i class="fas fa-user-tie"></i> Manager</a>
  <a href="../stock/dashboard.php"><i class="fas fa-boxes"></i> Stock Keeper</a>
  <a href="../cashier/dashboard.php"><i class="fas fa-cash-register"></i> Cashier</a>
  <a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
  <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h1>

  <?php if (!empty($lowStockItems)): ?>
    <div class="low-stock-alert">
      <strong><i class="fas fa-exclamation-triangle"></i> Low Stock Alert:</strong><br>
      <?php foreach ($lowStockItems as $item): ?>
        <?= htmlspecialchars($item['item_name']) ?> - <?= $item['quantity'] ?> <?= $item['unit'] ?><br>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="cards">
    <div class="card">
      <i class="fas fa-users"></i>
      <div class="card-content">
        <h2>Total Users</h2>
        <p><?= $totalUsers ?></p>
      </div>
    </div>
    <div class="card green">
      <i class="fas fa-box"></i>
      <div class="card-content">
        <h2>Stock Items</h2>
        <p><?= $totalStockItems ?></p>
      </div>
    </div>
    <div class="card yellow">
      <i class="fas fa-bell"></i>
      <div class="card-content">
        <h2>Low Stock Alerts</h2>
        <p><?= count($lowStockItems) ?></p>
      </div>
    </div>
  </div>

  <!-- Stock Update Chart Section -->
  <!-- Pie Chart Section -->



</body>
</html>
