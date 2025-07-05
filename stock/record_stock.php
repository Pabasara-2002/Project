<?php
session_start();
include('../db.php');

// Check if user is logged in and is a stock keeper
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'stock') {
    header("Location: ../login.php");
    exit();
}

// Fetch stock item names from database
try {
    $stmt = $conn->query("SELECT DISTINCT item_name FROM stock_items ORDER BY item_name ASC");
    $itemNames = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $itemNames = [];
    $_SESSION['error'] = "Failed to fetch stock items: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Record Stock Wastage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Poppins', sans-serif;
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
        .content {
            margin-left: 260px;
            padding: 40px;
        }
        .form-card {
            background-color: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            border-left: 6px solid rgb(43, 87, 207);
        }
        .form-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #1e293b;
        }
        .btn-primary {
            background-color: rgb(43, 87, 207);
            border: none;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        .hidden {
            display: none;
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

<!-- Content -->
<div class="content">
    <div class="form-card">
        <div class="form-title"><i class="fas fa-trash-alt me-2"></i>Record Stock Wastage</div>

        <?php
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="record_wastage_action.php" method="POST">
            <div class="mb-3">
                <label for="item_name" class="form-label">Item Name</label>
                <select id="item_name" name="item_name" required class="form-select">
                    <option value="" disabled selected>Select an item</option>
                    <?php foreach ($itemNames as $name): ?>
                        <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="quantity" class="form-label">Wasted Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
            </div>

            <div class="mb-3">
                <label for="reason" class="form-label">Reason</label>
                <select class="form-select" id="reason" name="reason" onchange="checkOtherReason()" required>
                    <option value="">-- Select Reason --</option>
                    <option value="Expired">Expired</option>
                    <option value="Damaged in Storage">Damaged in Storage</option>
                    <option value="Spoiled">Spoiled</option>
                    <option value="Returned by Customer">Returned by Customer</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="mb-3 hidden" id="otherReasonBox">
                <label for="other_reason" class="form-label">Please specify</label>
                <input type="text" class="form-control" id="other_reason" name="other_reason">
            </div>

            <button type="submit" class="btn btn-primary px-4">Submit</button>
        </form>
    </div>
</div>

<script>
    function checkOtherReason() {
        const reason = document.getElementById("reason").value;
        const otherBox = document.getElementById("otherReasonBox");
        const otherInput = document.getElementById("other_reason");

        if (reason === "Other") {
            otherBox.classList.remove("hidden");
            otherInput.required = true;
        } else {
            otherBox.classList.add("hidden");
            otherInput.required = false;
            otherInput.value = "";
        }
    }
</script>

</body>
</html>
