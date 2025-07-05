<?php
session_start();
include('../db.php');

// Check if admin is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch item names for dropdown
$message = "";

// ✅ Get ingredient names instead of stock_item_names
$stmt = $conn->query("SELECT ingredient_name FROM ingredients ORDER BY ingredient_name ASC");
$itemNames = $stmt->fetchAll(PDO::FETCH_COLUMN); // We’ll use this for dropdown

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $reorder_level = $_POST['reorder_level'];
    $added_by = $_SESSION['username'];

    $stmt = $conn->prepare("INSERT INTO pending_stock_items (item_name, quantity, unit, reorder_level, added_by, role) VALUES (?, ?, ?, ?, ?, 'stock')");
    $stmt->execute([$item_name, $quantity, $unit, $reorder_level, $added_by]);

    header("Location: manage_stock.php?pending=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Stock Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7fafc;
            margin: 0;
            padding-left: 270px;
            padding-top: 50px;
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

        .form-container {
            max-width: 600px;
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
            font-weight: 500;
            margin-top: 15px;
        }

        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
        }

        .btn {
            background-color: #3182ce;
            color: #fff;
            padding: 10px 20px;
            margin-top: 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #2b6cb0;
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

    <!-- Sidebar Start -->
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
    <!-- Sidebar End -->

    <div class="form-container">
        <h2>➕ Add Stock Item</h2>
        <form method="post">
            <label for="item_name">Item Name (from Ingredients)</label>
<select id="item_name" name="item_name" required class="form-select">
    <option value="" disabled selected>Select an item</option>
    <?php foreach ($itemNames as $name): ?>
        <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
    <?php endforeach; ?>
</select>

            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" required min="1" step="any">

            <label for="unit">Unit</label>
            <select id="unit" name="unit" required>
                <option value="" disabled selected>Select unit</option>
                <option value="kg">Kilograms (kg)</option>
                <option value="g">Grams (g)</option>
                <option value="L">Liters (L)</option>
                <option value="ml">Milliliters (ml)</option>
                <option value="pcs">Pieces (pcs)</option>
            </select>

           

            <button type="submit" class="btn">💾 Submit for Approval</button>
        </form>
        <a class="back-link" href="manage_stock.php">⬅️ Back to Manage Stock</a>
    </div>

</body>
</html>
