<?php
session_start();

// Correct path to the database connection file
include('../db.php');

// Only allow managers
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'manager') {
    header("Location: ../login.php");
    exit();
}


// Fetch pending orders
$query = "SELECT * FROM purchase_orders WHERE status = 'pending'";
$stmt = $conn->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Approve an order
if (isset($_POST['approve'])) {
    $order_id = $_POST['order_id'];
    $query = "UPDATE purchase_orders SET status = 'approved' WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $order_id);
    $stmt->execute();
    header("Location: approve_po.php"); // Refresh the page
    exit();
}

// Reject an order
if (isset($_POST['reject'])) {
    $order_id = $_POST['order_id'];
    $query = "UPDATE purchase_orders SET status = 'rejected' WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $order_id);
    $stmt->execute();
    header("Location: approve_po.php"); // Refresh the page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Purchase Orders</title>
    <style>
        body {
            background-color: #f7f7f7;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #1e3a8a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
        }
        .action-buttons {
            display: flex;
            justify-content: space-around;
        }
        .approve-btn, .reject-btn {
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .approve-btn {
            background-color: #4caf50;
            color: white;
            border: none;
        }
        .approve-btn:hover {
            background-color: #45a049;
        }
        .reject-btn {
            background-color: #f44336;
            color: white;
            border: none;
        }
        .reject-btn:hover {
            background-color: #e53935;
        }
        .logout-btn {
            background-color: #e74c3c;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 12px 25px;
            margin-top: 20px;
            border-radius: 8px;
            text-decoration: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>


<div class="container">
<a href="dashboard.php" style="
    display: inline-block;
    margin: 20px;
    padding: 10px 20px;
    background-color: #3b82f6;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background-color 0.3s ease;
    float:right
">⬅️ Back to Dashboard</a>

    <h1>Approve Purchase Orders</h1>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order) { ?>
                <tr>
                    <td><?= htmlspecialchars($order['id']) ?></td>
                    <td><?= htmlspecialchars($order['item_name']) ?></td>
                    <td><?= htmlspecialchars($order['quantity']) ?></td>
                    <td><?= htmlspecialchars($order['order_date']) ?></td>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                    <td class="action-buttons">
                        <form action="approve_po.php" method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <button type="submit" name="approve" class="approve-btn">Approve</button>
                        </form>
                        <form action="approve_po.php" method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <button type="submit" name="reject" class="reject-btn">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="../logout.php" class="logout-btn">🚪 Logout</a>
</div>

</body>
</html>
