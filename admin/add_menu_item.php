<?php
session_start();
require_once '../db.php'; // PDO instance $conn

// Fetch ingredient list
$stmt = $conn->query("SELECT ingredient_name FROM ingredients ORDER BY ingredient_name ASC");
$existingIngredients = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Menu Item</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f1f5f9;
      font-family: 'Poppins', sans-serif;
    }
    .form-container {
      max-width: 700px;
      background: white;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      padding: 30px;
      margin: 50px auto;
    }
    .ingredient-group {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 10px;
      align-items: center;
    }
    .ingredient-group select,
    .ingredient-group input {
      flex: 1 1 30%;
      min-width: 150px;
    }
    .remove-btn {
      background: #dc3545;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      flex: 0 0 auto;
    }
    .remove-btn:hover {
      background: #bb2d3b;
    }
      .sidebar {
            width: 260px;
            background: linear-gradient(to bottom, #1e3a8a, #2563eb);
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 30px 20px;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar h4 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 22px;
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

        .sidebar a:hover,
        .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar a.logout {
            background: #dc2626;
            color: #fff;
            margin-top: 40px;
            justify-content: center;
        }

  </style>
</head>
<body>
   <nav class="sidebar">
        <h4><i class="fas fa-utensils me-2"></i>Admin Panel</h4>
        <a href="admin_dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="manage_users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : '' ?>"><i class="fas fa-users-cog"></i> Manage Users</a>
        <a href="manage_stock.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_stock.php' ? 'active' : '' ?>"><i class="fas fa-boxes"></i> Manage Stock</a>
        <a href="reports.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>"><i class="fas fa-chart-bar"></i> Reports</a>
        <a href="approvals.php" class="<?= basename($_SERVER['PHP_SELF']) == 'approvals.php' ? 'active' : '' ?>"><i class="fas fa-check-circle"></i> Approvals</a>
        
        <a href="request_reorder.php" class="<?= basename($_SERVER['PHP_SELF']) == 'request_reorder.php' ? 'active' : '' ?>"><i class="fas fa-undo-alt"></i> Request Reorder</a>
        <a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
<div class="form-container">
  <h3 class="mb-4">➕ Add New Menu Item</h3>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Menu item added successfully!</div>
  <?php endif; ?>

  <form action="process_add_menu_item.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Menu Item Name</label>
      <input type="text" class="form-control" name="item_name" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Price (Rs)</label>
      <input type="number" class="form-control" name="item_price" step="0.01" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Item Image</label>
      <input type="file" class="form-control" name="item_image" accept="image/*" required>
    </div>

    <label class="form-label">Ingredients</label>
    <div id="ingredients-section">
      <div class="ingredient-group">
        <select name="ingredients[]" class="form-control" required>
          <option value="">Select Ingredient</option>
          <?php foreach ($existingIngredients as $ingredient): ?>
            <option value="<?= htmlspecialchars($ingredient) ?>"><?= htmlspecialchars($ingredient) ?></option>
          <?php endforeach; ?>
        </select>
       <input type="text" name="quantities[]" class="form-control" placeholder="Quantity (e.g. 100g, 2pcs)" required />
        <button type="button" class="remove-btn" onclick="removeIngredient(this)">×</button>
      </div>
        
    </div>

    <button type="button" class="btn btn-secondary mb-3" onclick="addIngredientField()">+ Add More Ingredients</button>
    <button type="submit" class="btn btn-primary w-100">Add Menu Item</button>
  </form>
</div>

<script>
function addIngredientField() {
  const section = document.getElementById('ingredients-section');
  const group = document.createElement('div');
  group.className = 'ingredient-group';
  group.innerHTML = `
    <select name="ingredients[]" class="form-control" required>
      <option value="">Select Ingredient</option>
      <?php foreach ($existingIngredients as $ingredient): ?>
        <option value="<?= htmlspecialchars($ingredient) ?>"><?= htmlspecialchars($ingredient) ?></option>
      <?php endforeach; ?>
    </select>
    <input type="number" name="quantities[]" class="form-control" placeholder="Quantity" step="0.01" required />
    <select name="units[]" class="form-control" required>
      <option value="">Unit</option>
      <option value="g">g</option>
      <option value="kg">kg</option>
      <option value="ml">ml</option>
      <option value="L">L</option>
      <option value="pcs">pcs</option>
    </select>
    <button type="button" class="remove-btn" onclick="removeIngredient(this)">×</button>
  `;
  section.appendChild(group);
}

function removeIngredient(button) {
  button.parentNode.remove();
}
</script>
</body>
</html>
