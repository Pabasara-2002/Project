<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'manager') {
    header("Location: ../login.php");
    exit();
}

include('../db.php');

// Approve or Reject form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['action'])) {
    $id = $_POST['id'];
    $action = $_POST['action'];
    
    if (in_array($action, ['approved', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE stock_adjustments SET status = :action WHERE id = :id");
        $stmt->execute([':action' => $action, ':id' => $id]);
    }
}

// Fetch pending adjustments
$stmt = $conn->query("SELECT * FROM stock_adjustments WHERE status = 'pending'");
$adjustments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Stock Adjustments</title>
    <style>
        body { font-family: Arial; background: #f3f4f6; padding: 20px; }
        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            border: 2px solid #8b5cf6;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        h2 { color: #6d28d9; margin-bottom: 20px; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border: 1px solid #ccc; text-align: center; }
        th { background: #8b5cf6; color: white; }

        form { display: inline; }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }

        .approve { background-color: #10b981; }
        .reject { background-color: #ef4444; }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 16px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-btn:hover { background: #2563eb; }
    </style>
</head>
<body>

<div class="container">
    <h2>⚙️ Approve Stock Adjustments</h2>

    <?php if ($adjustments): ?>
        <table>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Reason</th>
                <th>Requested By</th>
                <th>Requested At</th>
                <th>Action</th>
            </tr>
            <?php foreach ($adjustments as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['item_name']) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= htmlspecialchars($row['reason']) ?></td>
                    <td><?= $row['requested_by'] ?></td>
                    <td><?= $row['requested_at'] ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button name="action" value="approved" class="btn approve">Approve</button>
                            <button name="action" value="rejected" class="btn reject">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No pending adjustments.</p>
    <?php endif; ?>

    <a href="dashboard.php" class="back-btn">🔙 Back to Dashboard</a>
</div>

</body>
</html>
