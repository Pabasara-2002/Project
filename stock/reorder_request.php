<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'stock') {
    header("Location: ../login.php");
    exit();
}

$message = "";

// Get stock items that are at or below reorder level
$items = $conn->prepare("SELECT * FROM stock_items WHERE quantity <= reorder_level ORDER BY item_name ASC");
$items->execute();
$low_stock_items = $items->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stock_item_id = $_POST['stock_item_id'];
    $requested_quantity = $_POST['requested_quantity'];
    $requested_by = $_SESSION['username'];

    // First get the item_name from stock_items table using stock_item_id
    $stmt_item = $conn->prepare("SELECT item_name FROM stock_items WHERE id = ?");
    $stmt_item->execute([$stock_item_id]);
    $item = $stmt_item->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $item_name = $item['item_name'];

        // Insert reorder request with required fields
        $stmt = $conn->prepare("INSERT INTO reorder_request (item_name, quantity, requested_by, request_date, status) VALUES (?, ?, ?, NOW(), 'Pending')");
        $stmt->execute([$stock_item_id, $requested_quantity, $requested_by]);

        $message = "✅ Reorder request sent successfully for approval.";
    } else {
        $message = "❌ Invalid stock item selected.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Reorder Stock Request</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
body {
    background-color: #f1f5f9;
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
    margin-left: 270px;
    padding: 40px;
}
.form-container {
    max-width: 600px;
    margin: auto;
    background-color: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
}
h2 {
    text-align: center;
    color: #1e3a8a;
    margin-bottom: 25px;
    font-size: 28px;
}
label {
    font-weight: 600;
    margin-top: 15px;
}
select, input[type=number] {
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
.alert {
    max-width: 600px;
    margin: 20px auto;
    font-weight: 500;
}
</style>
</head>
<body>
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
    <h2><i class="fas fa-retweet me-2 text-primary"></i>Reorder Stock Request</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <?php if (count($low_stock_items) === 0): ?>
        <p>No items need reorder at this time.</p>
    <?php else: ?>
        <div class="form-container">
            <form method="post">
                <label for="stock_item_id">Select Item (Low Stock)</label>
                <select id="stock_item_id" name="stock_item_id" required>
                    <option value="" disabled selected>Select item</option>
                    <?php foreach ($low_stock_items as $item): ?>
                        <option value="<?= $item['id'] ?>">
                            <?= htmlspecialchars($item['item_name']) ?> (Current: <?= $item['quantity'] ?> <?= htmlspecialchars($item['unit']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="requested_quantity">Requested Quantity</label>
                <input type="number" id="requested_quantity" name="requested_quantity" min="1" required>

                <button type="submit" class="btn">Submit Reorder Request</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
