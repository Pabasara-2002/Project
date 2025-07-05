<?php
session_start();
include('../db.php');

// Check if manager is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../login.php");
    exit();
}

// Approve or Reject action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        // Move from pending to main stock_items
        $stmt = $conn->prepare("SELECT * FROM pending_stock_items WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $insert = $conn->prepare("INSERT INTO stock_items (item_name, quantity, unit, reorder_level, added_by) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([
                $item['item_name'],
                $item['quantity'],
                $item['unit'],
                $item['reorder_level'],
                $item['added_by']
            ]);

            // Delete from pending
            $conn->prepare("DELETE FROM pending_stock_items WHERE id = ?")->execute([$id]);
        }
    } elseif ($action === 'reject') {
        // Just delete from pending
        $conn->prepare("DELETE FROM pending_stock_items WHERE id = ?")->execute([$id]);
    }

    header("Location: approve_pending_stock.php?status=1");
    exit();
}

// Fetch pending stock items
$pendingItems = $conn->query("SELECT * FROM pending_stock_items ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Stock Approvals</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding: 30px;
            background: #f9fafb;
        }
        h2 {
            color: #1a202c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #edf2f7;
        }
        form {
            display: inline;
        }
        button {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .approve-btn {
            background-color: #48bb78;
            color: white;
        }
        .reject-btn {
            background-color: #f56565;
            color: white;
        }
        .message {
            background-color: #d1fae5;
            color: #065f46;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2>📝 Pending Stock Approvals</h2>

    <?php if (isset($_GET['status']) && $_GET['status'] == 1): ?>
        <div class="message">✔️ Action completed successfully.</div>
    <?php endif; ?>

    <?php if (count($pendingItems) === 0): ?>
        <p>No pending items for approval.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Reorder Level</th>
                    <th>Requested By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td><?= htmlspecialchars($item['unit']) ?></td>
                        <td><?= htmlspecialchars($item['reorder_level']) ?></td>
                        <td><?= htmlspecialchars($item['added_by']) ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button type="submit" name="action" value="approve" class="approve-btn">Approve</button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button type="submit" name="action" value="reject" class="reject-btn">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
