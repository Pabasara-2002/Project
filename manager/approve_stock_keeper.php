<?php
session_start();
include('../db.php');

// Only allow manager
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'manager') {
    header("Location: ../login.php");
    exit();
}

// Handle approval
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'approve') {
        // Move to main stock table
        $stmt = $conn->prepare("INSERT INTO stock_items (item_name, quantity, unit, added_by)
            SELECT item_name, quantity, unit, added_by_role FROM pending_stock WHERE id = :id");
        $stmt->execute(['id' => $id]);

        // Remove from pending
        $conn->prepare("DELETE FROM pending_stock WHERE id = :id")->execute(['id' => $id]);

        header("Location: approve_stock_keeper.php?approval_status=success");
        exit();
    } elseif ($action == 'reject') {
        // Just delete from pending
        $conn->prepare("DELETE FROM pending_stock WHERE id = :id")->execute(['id' => $id]);
        header("Location: approve_stock_keeper.php?approval_status=rejected");
        exit();
    }
}

// Fetch pending stock added by stock keepers
$stmt = $conn->prepare("SELECT * FROM pending_stock WHERE added_by_role = 'stock'");
$stmt->execute();
$pendingItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Stock Keeper Items</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f1f1f1;
        }
        h2 {
            color: #2d3b53;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
        a.approve {
            background-color: #4CAF50;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 5px;
        }
        a.reject {
            background-color: #e74c3c;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<h2>🧾 Approve Stock Keeper Items</h2>

<?php if (isset($_GET['approval_status'])): ?>
    <p style="color: green;">
        <?= $_GET['approval_status'] == 'success' ? 'Item approved!' : 'Item rejected or failed.' ?>
    </p>
<?php endif; ?>

<?php if (count($pendingItems) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Added By Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pendingItems as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['id']) ?></td>
                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                    <td><?= htmlspecialchars($item['unit']) ?></td>
                    <td><?= htmlspecialchars($item['added_by_role']) ?></td>
                    <td>
                        <a class="approve" href="?action=approve&id=<?= $item['id'] ?>">Approve</a>
                        <a class="reject" href="?action=reject&id=<?= $item['id'] ?>">Reject</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No pending items from Stock Keeper.</p>
<?php endif; ?>

<a class="back-btn" href="dashboard.php">⬅ Back to Dashboard</a>

</body>
</html>
