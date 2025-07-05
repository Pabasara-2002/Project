<?php
session_start();
include('../db.php');

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
    $stmt->execute([$username, $role, $id]);

    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit User</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f3f4f6;
      display: flex;
    }

    .sidebar {
      width: 250px;
      height: 100vh;
      background: linear-gradient(180deg, #1e3a8a, #2563eb);
      padding-top: 30px;
      color: white;
      position: fixed;
      box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 24px;
      font-weight: bold;
    }

    .sidebar a {
      display: block;
      padding: 14px 30px;
      color: #e2e8f0;
      text-decoration: none;
      font-size: 15px;
      transition: background 0.3s, padding-left 0.3s;
    }

    .sidebar a:hover, .sidebar a.active {
      background-color: rgba(255, 255, 255, 0.15);
      padding-left: 40px;
    }

    .sidebar a.logout {
      background-color: #dc2626;
      color: white;
      margin-top: 30px;
      font-weight: bold;
    }

    .main-content {
      margin-left: 250px;
      padding: 50px;
      width: calc(100% - 250px);
    }

    .form-card {
      max-width: 600px;
      margin: auto;
      background-color: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
    }

    .form-card h2 {
      font-size: 24px;
      margin-bottom: 20px;
      color: #1f2937;
      text-align: center;
    }

    input, select, button {
      width: 100%;
      padding: 12px;
      margin-bottom: 16px;
      font-size: 16px;
      border: 1px solid #d1d5db;
      border-radius: 6px;
    }

    input:focus, select:focus {
      border-color: #3b82f6;
      outline: none;
    }

    button {
      background-color: #2563eb;
      color: white;
      border: none;
      cursor: pointer;
    }

    button:hover {
      background-color: #1d4ed8;
    }

    .cancel-btn {
      background-color: #6b7280;
      margin-top: -10px;
    }

    .cancel-btn:hover {
      background-color: #4b5563;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2><i class="fas fa-user-cog"></i> Admin Panel</h2>
    <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php"><i class="fas fa-users-cog"></i> Manage Users</a>
    <a href="manage_stock.php"><i class="fas fa-boxes"></i> Manage Stock</a>
    <a href="reports_dashboard.php"><i class="fas fa-chart-bar"></i> Reports</a>
    
    <a href="approvals.php"><i class="fas fa-check-circle"></i> Approvals</a>
    <a href="enable_disable_users.php"><i class="fas fa-user-lock"></i> Enable/Disable Users</a>
    
    <a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="form-card">
      <h2>📝 Edit User</h2>
      <form method="POST">
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required placeholder="Username">
        
        <select name="role" required>
          <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
          <option value="manager" <?= $user['role'] == 'manager' ? 'selected' : '' ?>>Manager</option>
          <option value="stock_keeper" <?= $user['role'] == 'stock_keeper' ? 'selected' : '' ?>>Stock Keeper</option>
          <option value="supplier" <?= $user['role'] == 'supplier' ? 'selected' : '' ?>>Supplier</option>
          <option value="cashier" <?= $user['role'] == 'cashier' ? 'selected' : '' ?>>Cashier</option>
        </select>

        <button type="submit">💾 Update</button>
        <a href="manage_users.php"><button type="button" class="cancel-btn">↩️ Cancel</button></a>
      </form>
    </div>
  </div>

</body>
</html>
