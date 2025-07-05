<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$stmt = $conn->query("SELECT * FROM stock_items");
$stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Stock</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f8fafc;
    }

    .sidebar {
      width: 240px;
      background: linear-gradient(180deg, #1e3a8a, #2563eb);
      color: white;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      padding-top: 30px;
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 22px;
      font-weight: 600;
    }

    .sidebar a {
      display: block;
      padding: 15px 25px;
      color: #cbd5e1;
      text-decoration: none;
      font-size: 15px;
      transition: all 0.3s ease;
    }

    .sidebar a:hover {
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
      padding-left: 35px;
    }

    .sidebar a.logout {
      background-color: #dc2626;
      color: white !important;
      font-weight: bold;
    }

    .sidebar a.logout:hover {
      background-color: #b91c1c;
    }

    .main {
      margin-left: 240px;
      padding: 30px;
    }

    .topbar {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      gap: 15px;
      margin-bottom: 20px;
    }

    .topbar h1 {
      font-size: 24px;
      color: #1f2937;
      font-weight: 600;
    }

    .search-bar {
      position: relative;
      width: 300px;
    }

    .search-bar input {
      padding: 10px 14px 10px 38px;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      font-size: 14px;
      width: 100%;
      transition: 0.3s ease;
    }

    .search-bar input:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
    }

    .search-bar i {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      color: #94a3b8;
    }

    .btn {
      padding: 10px 18px;
      font-size: 14px;
      border: none;
      border-radius: 8px;
      color: white;
      cursor: pointer;
      transition: background 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }

    .btn-green {
      background: linear-gradient(to right, #16a34a, #22c55e);
    }

    .btn-green:hover {
      background: #15803d;
    }

    .btn-gray {
      background: #4b5563;
    }

    .btn-gray:hover {
      background: #374151;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 16px rgba(0,0,0,0.05);
    }

    th, td {
      padding: 16px;
      text-align: left;
      border-bottom: 1px solid #f1f5f9;
    }

    th {
      background-color: #f3f4f6;
      font-weight: 600;
      color: #374151;
    }

    td {
      color: #475569;
    }

    tr:hover {
      background-color: #f9fafb;
    }

    .link {
      color: #2563eb;
      text-decoration: none;
      font-weight: 500;
    }

    .link:hover {
      text-decoration: underline;
    }

    .delete-link {
      color: #ef4444;
    }

    .delete-link:hover {
      text-decoration: underline;
    }

    .actions {
      display: flex;
      gap: 10px;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2><i class="fas fa-utensils"></i> Admin Panel</h2>
  <a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
  <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
  <a href="manage_stock.php" class="active"><i class="fas fa-boxes"></i> Manage Stock</a>
  <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
  <a href="approvals.php"><i class="fas fa-check-circle"></i> Approvals</a>
  <a href="add_menu_item.php"><i class="fas fa-plus-circle"></i> Add Menu Item</a>
  <a href="view_menu_items.php"><i class="fas fa-eye"></i> View Menu Items</a>
  <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
  <div class="topbar">
    <h1>📦 Manage Stock</h1>
    <div class="search-bar">
      <i class="fas fa-search"></i>
      <input type="text" id="searchInput" placeholder="Search stock item...">
    </div>
    <div>
      <a href="admin_dashboard.php" class="btn btn-gray">⬅️ Back</a>
      <a href="add_stock.php" class="btn btn-green">➕ Add Stock Item</a>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Item</th>
        <th>Quantity</th>
        <th>Reorder Level</th>
        <th>Unit</th>
        <th>Updated</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($stocks as $index => $item): ?>
      <tr>
        <td><?= $index + 1 ?></td>
        <td class="stock-name"><?= htmlspecialchars($item['item_name']) ?></td>
        <td><?= $item['quantity'] ?></td>
        <td><?= $item['reorder_level'] ?></td>
        <td><?= $item['unit'] ?></td>
        <td><?= $item['updated_at'] ?></td>
        <td class="actions">
          <a href="edit_stock.php?id=<?= $item['id'] ?>" class="link">Edit</a>
          <a href="delete_stock.php?id=<?= $item['id'] ?>" class="delete-link" onclick="return confirm('Delete this item?')">Delete</a>
        </td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>

<script>
  // Live search filter
  document.getElementById("searchInput").addEventListener("keyup", function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("table tbody tr");

    rows.forEach(row => {
      const itemName = row.querySelector(".stock-name").textContent.toLowerCase();
      row.style.display = itemName.includes(filter) ? "" : "none";
    });
  });
</script>

</body>
</html>
