<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "No stock item selected.";
    exit();
}

$stock_id = $_GET['id'];

// Get item name
$stmt = $conn->prepare("SELECT item_name FROM stock_items WHERE id = ?");
$stmt->execute([$stock_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
$item_name = $item ? $item['item_name'] : 'Unknown Item';

// Get usage history
$usage = $conn->prepare("SELECT * FROM stock_usage WHERE stock_item_id = ? ORDER BY used_at DESC");
$usage->execute([$stock_id]);
$records = $usage->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Stock Usage - <?= htmlspecialchars($item_name) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8fafc;
      margin: 0;
      padding: 20px;
    }

    .container {
      max-width: 900px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    h2 {
      margin-bottom: 20px;
      font-weight: 600;
      color: #1e3a8a;
    }

    a.btn {
      display: inline-block;
      padding: 10px 16px;
      background-color: #2563eb;
      color: white;
      border-radius: 8px;
      text-decoration: none;
      margin-bottom: 20px;
    }

    a.btn:hover {
      background-color: #1d4ed8;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 14px;
      text-align: left;
      border-bottom: 1px solid #e5e7eb;
    }

    th {
      background-color: #f1f5f9;
      color: #374151;
    }

    td {
      color: #475569;
    }

    tr:hover {
      background-color: #f9fafb;
    }

    .empty {
      text-align: center;
      color: #9ca3af;
      padding: 30px;
    }
  </style>
</head>
<body>

<div class="container">
  <a href="manage_stock.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Stock</a>
  <h2>📊 Stock Usage for <em><?= htmlspecialchars($item_name) ?></em></h2>

  <?php if (count($records) === 0): ?>
    <div class="empty">No usage records found for this item.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Used Quantity</th>
          <th>Reason</th>
          <th>Date & Time</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($records as $i => $row): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= $row['used_quantity'] ?></td>
          <td><?= htmlspecialchars($row['reason']) ?></td>
          <td><?= $row['used_at'] ?></td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  <?php endif ?>
</div>

</body>
</html>
