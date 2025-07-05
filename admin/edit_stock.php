<?php
session_start();
include('../db.php');

// Check if admin is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage_stock.php");
    exit();
}

// Fetch stock item
$stmt = $conn->prepare("SELECT * FROM stock_items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "Stock item not found.";
    exit();
}

// Item name options (can also be fetched from database if dynamic)
$item_names = [
    'Rice', 'Pasta', 'Noodles', 'Bread', 'Flour', 'Cornmeal',
    'Salt', 'Pepper', 'Chili Powder', 'Curry Powder', 'Soy Sauce', 'Vinegar', 'Tomato Ketchup', 'Mustard',
    'Lentils', 'Chickpeas', 'Red Beans', 'Green Peas', 'Coconut Milk', 'Canned Tomatoes',
    'Vegetable Oil', 'Olive Oil', 'Butter', 'Margarine', 'Ghee',
    'Chicken Breast', 'Chicken Thigh', 'Beef Mince', 'Fish Fillet', 'Eggs', 'Paneer',
    'Potatoes', 'Onions', 'Carrots', 'Tomatoes', 'Bell Peppers', 'Cabbage', 'Spinach',
    'Bananas', 'Apples', 'Lemons', 'Oranges', 'Pineapple',
    'Milk', 'Cheese', 'Cream', 'Yogurt',
    'Bottled Water', 'Soft Drinks', 'Juice Packs', 'Tea Leaves', 'Coffee Powder'
];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $reorder_level = $_POST['reorder_level'];

    $stmt = $conn->prepare("UPDATE stock_items SET item_name = ?, quantity = ?, unit = ?, reorder_level = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$item_name, $quantity, $unit, $reorder_level, $id]);

    header("Location: manage_stock.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Stock Item</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7fafc;
            margin: 0;
            padding: 50px;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100%;
            background-color: rgb(19, 64, 160);
            padding-top: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 15px 20px;
            text-decoration: none;
            font-size: 16px;
        }

        .sidebar a:hover {
            background-color: #2d3748;
        }

        .sidebar a.logout {
            background: #ef4444;
        }

        .form-container {
            max-width: 500px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2d3748;
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="number"], input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .btn {
            display: inline-block;
            background-color: #38a169;
            color: #fff;
            padding: 10px 20px;
            margin-top: 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #2f855a;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #3182ce;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h2 style="color:white; text-align:center;">Admin Panel</h2>
    <a href="manage_users.php">👤 Manage Users</a>
    <a href="manage_stock.php">📦 Manage Stock</a>
    <a href="reports_dashboard.php">📊 Reports</a>
    <a href="system_settings.php">⚙️ System Settings</a>
    <a href="approvals.php">✅ Approvals</a>
    <a href="enable_disable_users.php">🔍 Enable/Disable Users</a>
    <a href="integrations.php">📋 Integrations</a>
    <a href="logout.php" class="logout">🚪 Logout</a>
</div>

<div class="form-container">
    <h2>✏️ Edit Stock Item</h2>
    <form method="post">
        <label for="item_name">Item Name</label>
        <select name="item_name" id="item_name" required>
            <?php foreach ($item_names as $name): ?>
                <option value="<?= htmlspecialchars($name) ?>" <?= ($name === $item['item_name']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($name) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="quantity">Quantity</label>
        <input type="number" id="quantity" name="quantity" required value="<?= $item['quantity'] ?>">

        <label for="unit">Unit</label>
        <input type="text" id="unit" name="unit" required value="<?= htmlspecialchars($item['unit']) ?>">

        <label for="reorder_level">Reorder Level</label>
        <input type="number" id="reorder_level" name="reorder_level" required value="<?= $item['reorder_level'] ?>">

        <button type="submit" class="btn">💾 Update Stock</button>
    </form>
    <a class="back-link" href="manage_stock.php">⬅️ Back to Manage Stock</a>
</div>

</body>
</html>
