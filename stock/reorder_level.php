<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'stock') {
    header("Location: ../login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'];
    $reorder_level = $_POST['reorder_level'];

    // Update reorder level in database - assume table: stock_item_names
    $stmt = $conn->prepare("UPDATE stock_item_names SET reorder_level = ? WHERE name = ?");
    $stmt->execute([$reorder_level, $item_name]);

    $message = "✅ Reorder level updated successfully for '{$item_name}'.";
}

$itemNames = $conn->query("SELECT name, reorder_level FROM stock_item_names ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Reorder Level - Stock Keeper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            height: 100vh;
            background-color: rgb(43, 87, 207);
            color: white;
            position: fixed;
            width: 240px;
            padding-top: 30px;
        }

        .sidebar a {
            color: white;
            display: block;
            padding: 14px 20px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #2563eb;
        }

        .logout-btn {
            margin-top: 30px;
            display: block;
            background-color: #ef4444;
            padding: 10px 24px;
            border-radius: 50px;
            text-align: center;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .logout-btn:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
        }

        .logout-btn i {
            font-size: 16px;
        }

        .main-content {
            margin-left: 280px;
            padding: 40px 50px;
            max-width: 700px;
        }
        h2 {
            color: #1e3a8a;
            margin-bottom: 30px;
            font-weight: 700;
            font-size: 2rem;
        }
        label {
            font-weight: 600;
            margin-top: 20px;
        }
        select, input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 1.5px solid #cbd5e0;
            border-radius: 8px;
            font-size: 1rem;
        }
        .btn-submit {
            margin-top: 30px;
            width: 100%;
            background-color: #2563eb;
            color: white;
            padding: 14px;
            font-weight: 700;
            font-size: 1.1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-submit:hover {
            background-color: #1e40af;
        }
        .alert {
            margin-top: 25px;
            padding: 15px;
            border-radius: 8px;
            font-weight: 600;
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
    <a href="reorder_level.php"><i class="fas fa-retweet me-2"></i> Reorder Level</a>
    <a href="reorder_request.php"><i class="fas fa-paper-plane me-2"></i> Reorder Requests</a>
    <a href="record_stock.php"><i class="fas fa-trash-alt me-2"></i> Record Stock Wastage</a>
     <a href="view_wastage.php"><i class="fas fa-trash-alt me-2"></i> View Stock Wastage</a>
    <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>
<div class="main-content">
    <h2><i class="fas fa-retweet me-2 text-primary"></i>Update Reorder Level</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="item_name">Select Item</label>
        <select name="item_name" id="item_name" required>
            <option value="" disabled selected>-- Select Stock Item --</option>
            <?php foreach ($itemNames as $item): ?>
                <option value="<?= htmlspecialchars($item['name']) ?>">
                    <?= htmlspecialchars($item['name']) ?> (Current: <?= htmlspecialchars($item['reorder_level']) ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="reorder_level">New Reorder Level</label>
        <input type="number" id="reorder_level" name="reorder_level" min="1" required placeholder="Enter new reorder level" />

        <button type="submit" class="btn-submit">Save Reorder Level</button>
    </form>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
