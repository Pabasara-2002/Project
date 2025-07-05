<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$stmt = $conn->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Manage Users</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0; padding: 0; box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      display: flex;
      background-color: #f0f4f8;
      color: #1a202c;
    }

    /* Sidebar */
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

    /* Main Content */
    .main {
      margin-left: 260px;
      width: calc(100% - 260px);
      padding: 40px;
    }

    .card {
      background: white;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .card-header h1 {
      font-size: 22px;
      color: #1e3a8a;
    }

    .btn {
      text-decoration: none;
      padding: 10px 18px;
      border-radius: 5px;
      font-size: 14px;
      font-weight: 500;
      display: inline-block;
      transition: background 0.2s;
    }

    .btn-back {
      background-color: #4b5563;
      color: white;
    }

    .btn-back:hover {
      background-color: #374151;
    }

    .btn-add {
      background-color: #10b981;
      color: white;
      margin-left: 10px;
    }

    .btn-add:hover {
      background-color: #059669;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 14px;
      text-align: left;
      border-bottom: 1px solid #e2e8f0;
    }

    th {
      background-color: #f9fafb;
      color: #4a5568;
    }

    tr:hover {
      background-color: #f1f5f9;
    }

    .action-link {
      color: #2563eb;
      margin-right: 12px;
      font-weight: 500;
    }

    .action-link:hover {
      text-decoration: underline;
    }

    .delete-link {
      color: #dc2626;
    }

    @media (max-width: 768px) {
      .sidebar {
        display: none;
      }
      .main {
        margin: 0;
        width: 100%;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2><i class="fas fa-user-shield"></i> Admin Panel</h2>
  <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
  <a href="manage_users.php" class="active"><i class="fas fa-users-cog"></i> Manage Users</a>
  <a href="manage_stock.php"><i class="fas fa-boxes"></i> Manage Stock</a>
  <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
  <a href="approvals.php"><i class="fas fa-clipboard-check"></i> Approvals</a>
  <a href="enable_disable_users.php"><i class="fas fa-user-lock"></i> Enable/Disable</a>
  <a href="request_reorder.php"><i class="fas fa-undo-alt"></i> Reorder Requests</a>
  <a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
  <div class="card">
    <div class="card-header">
      <h1>👥 Manage Users</h1>
      <div>
        <a href="admin_dashboard.php" class="btn btn-back">⬅ Back</a>
        <a href="add_user.php" class="btn btn-add">➕ Add User</a>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Username</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $index => $user): ?>
        <tr>
          <td><?= $index + 1 ?></td>
          <td><?= htmlspecialchars($user['username']) ?></td>
          <td><?= htmlspecialchars($user['role']) ?></td>
          <td>
            <a href="edit_user.php?id=<?= $user['id'] ?>" class="action-link">Edit</a>
            <a href="delete_user.php?id=<?= $user['id'] ?>" class="action-link delete-link" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
