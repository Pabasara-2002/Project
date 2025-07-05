<?php
session_start();
require_once '../db.php'; // Ensure this returns a $conn (PDO)
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Menu Items</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f1f5f9;
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
    .card {
      border-radius: 15px;
    }
    img.menu-img {
      width: 100px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
    }
    main {
      margin-left: 250px;
      padding: 30px;
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="sidebar">
      <h2><i class="fas fa-utensils"></i> Admin Panel</h2>
      <a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
      <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
      <a href="manage_stock.php"><i class="fas fa-boxes"></i> Manage Stock</a>
      <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
      <a href="approvals.php"><i class="fas fa-check-circle"></i> Approvals</a>
      <a href="add_menu_item.php"><i class="fas fa-plus-circle"></i> Add Menu Item</a>
      <a href="view_menu_items.php" class="active"><i class="fas fa-eye"></i> View Menu Items</a>
      <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
      <h2 class="fw-semibold mb-4 text-dark">🍽️ Menu Items & Ingredients</h2>

      <div class="card p-4 shadow-sm">
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary">
              <tr>
                <th>#</th>
                <th>Menu Item</th>
                <th>Price</th>
                <th>Image</th>
                <th>Ingredients</th>
                <th>Actions</th>
              </tr>
            </thead>

            <tbody>
              <?php
              $stmt = $conn->prepare("SELECT * FROM menu_items");
              $stmt->execute();
              $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

              foreach ($menuItems as $index => $item) {
                  echo "<tr>";
                  echo "<td>" . ($index + 1) . "</td>";
                  echo "<td>" . htmlspecialchars($item['item_name']) . "</td>";
                  echo "<td>Rs. " . htmlspecialchars($item['price']) . "</td>";

                  // Image display
                  $imagePath = '../uploads/' . htmlspecialchars($item['image']);
                  echo "<td><img src='$imagePath' alt='Menu Image' class='menu-img'></td>";

                  // ✅ Fetch ingredients using corrected JOIN
                  $ingredientStmt = $conn->prepare("
                      SELECT i.ingredient_name, mi.quantity
                      FROM menu_ingredients mi
                      JOIN ingredients i ON mi.ingredient_id = i.ingredient_id
                      WHERE mi.menu_item_id = ?
                  ");
                  $ingredientStmt->execute([$item['id']]);
                  $ingredients = $ingredientStmt->fetchAll(PDO::FETCH_ASSOC);

                  echo "<td>";
                  if ($ingredients) {
                      echo "<ul>";
                      foreach ($ingredients as $ingredient) {
                          $quantity = htmlspecialchars($ingredient['quantity']);
                          if ($quantity == 0 || $quantity === '' || $quantity === null) {
                              echo "<li>" . htmlspecialchars($ingredient['ingredient_name']) . "</li>";
                          } else {
                              echo "<li>" . htmlspecialchars($ingredient['ingredient_name']) . " - " . $quantity . "</li>";
                          }
                      }
                      echo "</ul>";
                  } else {
                      echo "No ingredients listed.";
                  }
                  echo "</td>";

                  // Actions
                  echo "<td>
                          <a href='edit_menu_item.php?id={$item['id']}' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i> Edit</a>
                          <a href='delete_menu_item.php?id={$item['id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure you want to delete this item?');\"><i class='fas fa-trash'></i> Delete</a>
                        </td>";

                  echo "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</div>

</body>
</html>
