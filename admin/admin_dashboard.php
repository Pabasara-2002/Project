<?php 
session_start();
try {
    $conn = new PDO("mysql:host=localhost;dbname=restaurant", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// 🔔 Low Stock Alert Count
$alert_count = 0;
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM ingredients WHERE quantity < reorder_level");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $alert_count = $result ? (int)$result['count'] : 0;
} catch (PDOException $e) {
    $alert_count = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8fafc;
    }
    .sidebar {
      min-height: 100vh;
      background: linear-gradient(180deg, #1e3a8a, #2563eb);
      color: white;
      padding-top: 30px;
    }
    .sidebar a {
      color: #cbd5e1;
      padding: 15px 25px;
      display: block;
      text-decoration: none;
      transition: all 0.3s;
      font-size: 16px;
    }
    .sidebar a:hover, .sidebar a.active {
      background: rgba(255, 255, 255, 0.15);
      padding-left: 35px;
      color: #fff;
    }
    .notification-bell {
      position: fixed;
      top: 20px;
      right: 30px;
      font-size: 26px;
      color: #2563eb;
      z-index: 999;
    }
    .notification-bell .badge {
      position: absolute;
      top: -8px;
      right: -10px;
      background-color: #dc2626;
      font-size: 12px;
      padding: 4px 7px;
      border-radius: 50%;
      color: white;
    }
    .card-box {
      border-radius: 18px;
      padding: 25px;
      background: #ffffff;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
      transition: all 0.3s;
      height: 100%;
    }
    .card-box:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
    }
    .icon-circle {
      background-color: #3b82f6;
      color: white;
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      margin-bottom: 14px;
    }
    .btn-gradient {
      background: linear-gradient(to right, #1e3a8a, #2563eb);
      color: white;
      padding: 6px 20px;
      font-size: 14px;
      border: none;
      border-radius: 20px;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    .btn-gradient:hover {
      background: linear-gradient(to right, #2e49aa, #3785f3);
      transform: scale(1.05);
      box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
    }
    .logout {
      background-color: #dc2626;
      color: white;
      font-weight: bold;
    }
    .logout:hover {
      background-color: #b91c1c;
    }
  </style>
</head>
<body>

<!-- 🔔 Notification Bell -->
<div class="notification-bell" title="Low Stock Alerts">
  <a href="low_stock_alerts.php">
    <i class="fas fa-bell"></i>
    <?php if ($alert_count > 0): ?>
      <span class="badge"><?= $alert_count ?></span>
    <?php endif; ?>
  </a>
</div>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 sidebar">
      <h4 class="text-center fw-bold text-white mb-4"><i class="fas fa-utensils"></i> Admin Panel</h4>
      <?php
      $pages = [
        'dashboard.php' => ['Dashboard', 'fa-chart-line'],
        'manage_users.php' => ['Manage Users', 'fa-users-cog'],
        'manage_stock.php' => ['Manage Stock', 'fa-boxes'],
        'approvals.php' => ['Approvals', 'fa-check-circle'],
        
        'add_menu_item.php' => ['Add Menu Item', 'fa-plus-circle'],
        'view_menu_items.php' => ['View Menu Items', 'fa-eye'],
        'view_bills.php' => ['View Bills', 'fa-file-invoice-dollar'],
        'daily_summery.php' => ['Sales Report', 'fa-calendar-day'],
        'view_orders.php' => ['View Orders', 'fa-list-alt'],
      ];
      foreach ($pages as $file => [$label, $icon]) {
        $active = basename($_SERVER['PHP_SELF']) === $file ? 'active' : '';
        echo "<a href=\"$file\" class=\"$active\"><i class=\"fas $icon me-2\"></i> $label</a>";
      }
      ?>
      <a href="../logout.php" class="logout text-center d-block p-2 mt-4"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <main class="col-md-9 col-lg-10 px-4 py-4">
      <h2 class="fw-semibold mb-4">📋 Admin Dashboard</h2>

      <?php if (isset($_GET['message'])): ?>
        <div class="alert <?= $_GET['message'] === 'success' ? 'alert-success' : 'alert-danger' ?>">
          <?= $_GET['message'] === 'success' ? '🟢 Reorder request sent successfully!' : '❌ Failed to send reorder request!' ?>
        </div>
      <?php endif; ?>

      <div class="row g-4">
        <?php
        $cards = [
          ['Manage Users', 'Create, edit or delete users.', 'manage_users.php', 'fa-users-cog'],
          ['Manage Stock', 'Add, update, delete stock items.', 'manage_stock.php', 'fa-boxes'],
          ['Approvals', 'Approve or reject changes.', 'approvals.php', 'fa-check-circle'],
         
          ['Add Menu Item', 'Add new dishes to the menu.', 'add_menu_item.php', 'fa-plus-circle'],
          ['View Menu Items', 'Browse existing dishes.', 'view_menu_items.php', 'fa-eye'],
          ['View Bills', 'Check all billing records.', 'view_bills.php', 'fa-file-invoice-dollar'],
          ['Sales Report', 'View daily/monthly reports.', 'daily_summery.php', 'fa-calendar-day'],
          ['View Orders', 'Track and manage orders.', 'view_orders.php', 'fa-list-alt'],
        ];
        foreach ($cards as [$title, $desc, $link, $icon]) {
          echo "
          <div class='col-md-6 col-lg-4'>
            <div class='card card-box'>
              <div class='icon-circle'><i class='fas $icon'></i></div>
              <h5>$title</h5>
              <p class='text-muted'>$desc</p>
              <a href='$link' class='btn btn-gradient mt-2'>Go</a>
            </div>
          </div>";
        }
        ?>
      </div>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
