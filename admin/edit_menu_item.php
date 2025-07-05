<?php
require_once '../db.php';

if (!isset($_GET['id'])) {
    header("Location: view_menu_items.php");
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    echo "Menu item not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];

    // Handle file upload
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
        $imageName = basename($_FILES['item_image']['name']);
        $targetPath = "../uploads/" . $imageName;
        move_uploaded_file($_FILES['item_image']['tmp_name'], $targetPath);
        $update = $conn->prepare("UPDATE menu_items SET name = ?, price = ?, image = ? WHERE id = ?");
        $update->execute([$name, $price, $imageName, $id]);
    } else {
        $update = $conn->prepare("UPDATE menu_items SET name = ?, price = ? WHERE id = ?");
        $update->execute([$name, $price, $id]);
    }

    // Optional: Save ingredients (you can truncate previous and insert new)
   if (isset($_POST['ingredients']) && isset($_POST['quantities'])) {
    $ingredients = $_POST['ingredients'];
    $quantities = $_POST['quantities'];

    $insertIngredient = $conn->prepare("INSERT INTO menu_ingredients (menu_item_id, ingredient_name, quantity) VALUES (?, ?, ?)");

    foreach ($ingredients as $index => $ingredientname) {
        $quantity = $quantities[$index];
        $insertIngredient->execute([$id, $ingredientname, $quantity]);
    }
}


    header("Location: view_menu_items.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Menu Item</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .ingredient-group {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }
    .remove-btn {
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        font-weight: bold;
        cursor: pointer;
    }
  </style>
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2 class="mb-4">Edit Menu Item</h2>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Item Name</label>
      <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($item['name']) ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Price (Rs)</label>
      <input type="number" class="form-control" name="price" step="0.01" value="<?= htmlspecialchars($item['price']) ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Current Image</label><br>
      <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" alt="Menu Image" width="150">
    </div>

    <div class="mb-3">
      <label class="form-label">Change Image (optional)</label>
      <input type="file" class="form-control" name="item_image" accept="image/*">
    </div>

    <div id="ingredients-section">
      <label class="form-label">Ingredients</label>
      <div class="ingredient-group">
        <select name="ingredients[]" class="form-select" required>
          <option value="" disabled selected>Select Ingredient</option>
          <?php
          $ingredientsList = [
            "Chicken", "Basmati Rice", "Onion", "Yogurt", "Biryani Masala", "Godamba Roti", "Cheese", "Egg",
            "Rice", "Soy Sauce", "Bell Pepper", "Chili Sauce", "Carrot", "Cabbage", "Dough", "Fish Curry Mix",
            "Potato", "Fish", "Pancake Dough", "Sausage", "Chicken/Fish Mix", "Bread Slices", "Lettuce", "Bun",
            "Chicken Patty", "Tea Brew", "Sugar", "Ice Cubes", "Coffee Powder", "Milk", "Orange Juice",
            "Coconut Milk", "Jaggery", "Flour", "Cocoa Powder", "Butter", "Mixed Fruits", "Sugar Syrup"
          ];
          foreach ($ingredientsList as $ing) {
              echo "<option value=\"$ing\">$ing</option>";
          }
          ?>
        </select>
        <input type="text" name="quantities[]" class="form-control" placeholder="Quantity (e.g. 100g, 2pcs)" required />
        <button type="button" class="remove-btn" onclick="removeIngredient(this)">×</button>
      </div>
    </div>

    <button type="button" class="btn btn-secondary mb-3" onclick="addIngredientField()">+ Add More Ingredients</button>

    <div class="d-grid">
      <button type="submit" class="btn btn-success">Update Menu Item</button>
    </div>
  </form>
</div>

<script>
  function addIngredientField() {
    const section = document.getElementById('ingredients-section');
    const group = document.createElement('div');
    group.className = 'ingredient-group';
    group.innerHTML = `
      <select name="ingredients[]" class="form-select" required>
        <option value="" disabled selected>Select Ingredient</option>
        <?php foreach ($ingredientsList as $ing): ?>
          <option value="<?= $ing ?>"><?= $ing ?></option>
        <?php endforeach; ?>
      </select>
      <input type="text" name="quantities[]" class="form-control" placeholder="Quantity (e.g. 100g, 2pcs)" required />
      <button type="button" class="remove-btn" onclick="removeIngredient(this)">×</button>
    `;
    section.appendChild(group);
  }

  function removeIngredient(button) {
    const group = button.parentNode;
    group.parentNode.removeChild(group);
  }
</script>
</body>
</html>
