<?php
session_start();
require_once '../db.php';

// Access control: only manager allowed
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../login.php");
    exit();
}

// Fetch ingredient data
$stmt = $conn->prepare("SELECT id, ingredient_name, unit, quantity_in_stock, threshold FROM ingredients ORDER BY ingredient_name ASC");
$stmt->execute();
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Low Stock Ingredients - Manager Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f1f5f9;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      margin-top: 60px;
    }
    .low-stock {
      background-color: #fff3cd !important;
    }
    .badge-status {
      font-size: 0.85rem;
      padding: 5px 10px;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="text-primary">📦 Low Stock Ingredients</h2>
    <a href="manager_view_ingredients.php" class="btn btn-sm btn-secondary">🔁 Refresh</a>
  </div>

  <table class="table table-bordered table-hover align-middle">
    <thead class="table-dark text-center">
      <tr>
        <th>#</th>
        <th>Ingredient Name</th>
        <th>Unit</th>
        <th>Stock Available</th>
        <th>Threshold</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $lowFound = false;
      foreach ($ingredients as $index => $row):
        $stock = floatval($row['quantity_in_stock']);
        $threshold = floatval($row['threshold']);
        $isLow = $stock <= $threshold;
        if (!$isLow) continue; // Show only low stock items
        $lowFound = true;
      ?>
      <tr class="low-stock">
        <td class="text-center"><?= $index + 1 ?></td>
        <td><?= htmlspecialchars($row['ingredient_name']) ?></td>
        <td class="text-center"><?= htmlspecialchars($row['unit']) ?></td>
        <td class="text-center"><?= number_format($stock, 2) ?></td>
        <td class="text-center"><?= number_format($threshold, 2) ?></td>
        <td class="text-center">
          <span class="badge bg-warning text-dark badge-status">Low Stock</span>
        </td>
      </tr>
      <?php endforeach; ?>

      <?php if (!$lowFound): ?>
        <tr>
          <td colspan="6" class="text-center text-success fw-bold py-4">✅ All ingredients are above threshold.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>
