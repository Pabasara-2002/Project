<?php
session_start();

// Allow only managers
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'manager') {
    header("Location: ../login.php");
    exit();
}

include('../db.php');

// Forecast average usage over last 30 days
$query = "
    SELECT item_name, 
           ROUND(SUM(quantity_used) / COUNT(DISTINCT used_date), 2) AS avg_daily_usage 
    FROM stock_usage 
    WHERE used_date >= CURDATE() - INTERVAL 30 DAY
    GROUP BY item_name
";

$stmt = $conn->query($query);
$forecast = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forecast Stock Requirements</title>
    <style>
        body {
            background: #f3f4f6;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            border: 2px solid #10b981;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        h2 {
            color: #065f46;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background: #10b981;
            color: white;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 16px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-btn:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📅 Forecast Stock Requirements (Next 7 Days)</h2>

    <table>
        <tr>
            <th>Item Name</th>
            <th>Avg. Daily Usage</th>
            <th>Forecast (7 Days)</th>
        </tr>
        <?php if ($forecast): ?>
            <?php foreach ($forecast as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['item_name']) ?></td>
                    <td><?= $row['avg_daily_usage'] ?></td>
                    <td><?= $row['avg_daily_usage'] * 7 ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">No usage data available.</td></tr>
        <?php endif; ?>
    </table>

    <a href="dashboard.php" class="back-btn">🔙 Back to Dashboard</a>
</div>

</body>
</html>
