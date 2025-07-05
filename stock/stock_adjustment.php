<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'stock') {
    header("Location: ../login.php");
    exit();
}

$message = "";

// Handle form submission only after confirmation (JS will trigger form submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    $item_name = $_POST['item_name'];
    $adjustment_type = $_POST['adjustment_type'];
    $adjust_quantity = (int) $_POST['adjust_quantity'];

    // Get current stock quantity
    $stmt = $conn->prepare("SELECT quantity FROM stock_items WHERE item_name = ?");
    $stmt->execute([$item_name]);
    $currentStock = $stmt->fetchColumn();

    if ($currentStock !== false) {
        if ($adjustment_type === 'add') {
            $newQuantity = $currentStock + $adjust_quantity;
        } else {
            $newQuantity = max(0, $currentStock - $adjust_quantity);
        }

        // Update stock quantity
        $update = $conn->prepare("UPDATE stock_items SET quantity = ? WHERE item_name = ?");
        $update->execute([$newQuantity, $item_name]);

        // Insert adjustment record for history
        $insert = $conn->prepare("INSERT INTO stock_adjustment_history (item_name, adjustment_type, quantity, adjusted_by, adjusted_at) VALUES (?, ?, ?, ?, NOW())");
        $insert->execute([$item_name, $adjustment_type, $adjust_quantity, $_SESSION['username']]);

        $message = "✅ Stock successfully " . ($adjustment_type === 'add' ? "increased" : "decreased") . ".";
    } else {
        $message = "❌ Item not found!";
    }
}

// Get items for dropdown
$items = $conn->query("SELECT item_name FROM stock_items ORDER BY item_name ASC")->fetchAll(PDO::FETCH_COLUMN);

// Get last 10 adjustment records
$history = $conn->query("SELECT * FROM stock_adjustment_history ORDER BY adjusted_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <meta charset="UTF-8" />
    <title>Stock Adjustment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
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


        .main {
            margin-left: 260px;
            padding: 40px;
        }

        .card {
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .form-select,
        .form-control {
            border-radius: 10px;
        }

        .btn-primary {
            background-color: #2563eb;
            border: none;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .alert {
            max-width: 600px;
            margin: 20px auto;
        }

        /* History Table Styling */
        .history-table {
            max-width: 900px;
            margin: 40px auto 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
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


<div class="main">
    <h2 class="mb-4 text-primary">⚖️ Stock Adjustment</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <div class="card">
        <form id="adjustmentForm" method="POST" onsubmit="return showConfirmModal(event)">
            <div class="mb-3">
                <label for="item_name" class="form-label">Item Name</label>
                <select class="form-select" name="item_name" id="item_name" required>
                    <option value="" disabled selected>Select Item</option>
                    <?php foreach ($items as $item): ?>
                        <option value="<?= htmlspecialchars($item) ?>"><?= htmlspecialchars($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="adjustment_type" class="form-label">Adjustment Type</label>
                <select class="form-select" name="adjustment_type" id="adjustment_type" required>
                    <option value="add">Add Stock</option>
                    <option value="remove">Remove Stock</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="adjust_quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" name="adjust_quantity" id="adjust_quantity" required min="1" />
            </div>

            <input type="hidden" name="confirm" id="confirm" value="no" />

            <button type="submit" class="btn btn-primary w-100">💾 Submit Adjustment</button>
        </form>
    </div>

    <!-- Adjustment History Table -->
    <div class="history-table mt-5 p-4">
        <h4>📜 Recent Adjustment History</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Adjustment Type</th>
                    <th>Quantity</th>
                    <th>Adjusted By</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($history) === 0): ?>
                    <tr><td colspan="5" class="text-center">No adjustment history found.</td></tr>
                <?php else: ?>
                    <?php foreach ($history as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['item_name']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($row['adjustment_type'])) ?></td>
                            <td><?= (int)$row['quantity'] ?></td>
                            <td><?= htmlspecialchars($row['adjusted_by']) ?></td>
                            <td><?= htmlspecialchars($row['adjusted_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-primary" id="confirmModalLabel">Confirm Stock Adjustment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to <span id="confirmAction"></span> <b><span id="confirmQty"></span></b> units of <b><span id="confirmItem"></span></b>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmYesBtn">Yes, Confirm</button>
      </div>
    </div>
  </div>
</div>

<script>
function showConfirmModal(event) {
    event.preventDefault();

    const item = document.getElementById('item_name').value;
    const qty = document.getElementById('adjust_quantity').value;
    const type = document.getElementById('adjustment_type').value;

    if (!item || !qty || qty <= 0) {
        alert('Please fill all fields correctly.');
        return false;
    }

    // Set modal text
    document.getElementById('confirmAction').textContent = type === 'add' ? 'add' : 'remove';
    document.getElementById('confirmQty').textContent = qty;
    document.getElementById('confirmItem').textContent = item;

    // Show modal
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    confirmModal.show();

    // Confirm button handler
    document.getElementById('confirmYesBtn').onclick = function () {
        document.getElementById('confirm').value = 'yes';
        confirmModal.hide();
        document.getElementById('adjustmentForm').submit();
    };

    return false;
}
</script>

</body>
</html>
