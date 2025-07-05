<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'stock') {
    header("Location: ../login.php");
    exit();
}

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

    $message = "✅ Stock item added successfully. Pending manager approval.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: #1e3a8a;
            padding: 30px 20px;
            color: #fff;
        }

        .sidebar h4 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .sidebar a {
            display: block;
            color: #e0e7ff;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }

        .sidebar a:hover {
            background-color: #3b82f6;
            color: white;
        }

        .logout-btn {
            background-color: #dc2626;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
            border-radius: 30px;
            display: block;
            text-decoration: none;
            font-weight: 500;
        }

       .main-content {
    margin-left: 250px;
    padding: 40px 30px;
}

.form-container {
    background-color: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
}


        h2 {
            text-align: left;
            color: #1e3a8a;
            margin-bottom: 25px;
            font-size: 28px;
        }

        label {
            font-weight: 600;
            margin-top: 15px;
        }

        input[type="number"], select {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
        }

        .btn {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            font-weight: bold;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #1d4ed8;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #2563eb;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .alert {
            max-width: 600px;
            margin: 20px auto;
            font-weight: 500;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center fw-bold"><i class="fas fa-user-cog me-2"></i>Stock Keeper</h4>
    <a href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
    <a href="view_stock.php"><i class="fas fa-boxes me-2"></i> View Stock Levels</a>
    <a href="low_stock.php"><i class="fas fa-exclamation-triangle me-2"></i> Low Stock Alerts</a>
    <a href="add_stock.php"><i class="fas fa-plus-circle me-2"></i> Add New Stock</a>
    <a href="stock_adjustment.php"><i class="fas fa-sliders-h me-2"></i> Stock Adjustment</a>
   
    <a href="reorder_request.php"><i class="fas fa-paper-plane me-2"></i> Reorder Requests</a>
    <a href="record_stock.php"><i class="fas fa-trash-alt me-2"></i> Record Stock Wastage</a>
     <a href="view_wastage.php"><i class="fas fa-trash-alt me-2"></i> View Stock Wastage</a>
    <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<div class="main-content">
    <h2><i class="fas fa-plus-circle me-2 text-primary"></i>Add New Stock</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?= $message ?></div>
    <?php endif; ?>

    <div class="container">
        <div class="row justify-content-start"> <!-- Aligned left -->
            <div class="col-md-8 offset-md-1"> <!-- More toward sidebar -->
                <div class="form-container">
                    <form method="post">
                       <label for="item_name">Item Name (from Ingredients)</label>
<select id="item_name" name="item_name" required class="form-select">
    <option value="" disabled selected>Select an item</option>
    <?php foreach ($itemNames as $name): ?>
        <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
    <?php endforeach; ?>
</select>


                        <label for="quantity">Quantity</label>
                        <input type="number" id="quantity" name="quantity" required class="form-control">

                        <label for="unit">Unit</label>
                        <select id="unit" name="unit" required class="form-select">
                            <option value="" disabled selected>Select unit</option>
                            <option value="kg">Kilograms (kg)</option>
                            <option value="g">Grams (g)</option>
                            <option value="L">Liters (L)</option>
                            <option value="ml">Milliliters (ml)</option>
                            <option value="pcs">Pieces (pcs)</option>
                        </select>

                        

                        <button type="submit" class="btn btn-primary mt-4 w-100">
                            💾 Submit for Approval
                        </button>
                    </form>

                    <a class="back-link" href="dashboard.php">⬅️ Back to dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>
