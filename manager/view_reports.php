<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../login.php");
    exit();
}

include('../db.php');

$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$reportType = $_GET['report_type'] ?? 'stock';

$params = [];
$data = [];
$dateField = '';

switch ($reportType) {
    case 'stock':
        $dateField = 'date';
        $sql = "SELECT item_name, movement_type, quantity, date FROM stock_movement";
        break;

    case 'wastage':
        $dateField = 'date';
        $sql = "SELECT item_name, quantity, reason, date FROM stock_wastage";
        break;

    case 'low_stock':
        $sql = "SELECT i.ingredient_name AS ingredient_name, l.created_at AS date 
                FROM low_stock_alerts l 
                JOIN ingredients i ON l.ingredient_id = i.ingredient_id 
                ORDER BY l.created_at DESC";
        break;

    case 'ingredient_usage':
        $dateField = 'o.created_at';
        $sql = "SELECT 
                    i.ingredient_name AS ingredient_name, 
                    DATE(o.created_at) AS usage_date, 
                    SUM(oi.quantity * mii.quantity_required) AS total_used
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN menu_ingredients mii ON oi.item_id = mii.menu_item_id
                JOIN ingredients i ON mii.ingredient_id = i.ingredient_id";
        break;

    default:
        $sql = "";
}

// Apply filters
if (!in_array($reportType, ['low_stock', 'ingredient_usage']) && $startDate && $endDate && $dateField) {
    $sql .= " WHERE $dateField BETWEEN :start AND :end";
    $params[':start'] = $startDate;
    $params[':end'] = $endDate;
}

if ($reportType === 'ingredient_usage') {
    if ($startDate && $endDate) {
        $sql .= " WHERE DATE($dateField) BETWEEN :start AND :end";
        $params[':start'] = $startDate;
        $params[':end'] = $endDate;
    }
    $sql .= " GROUP BY i.ingredient_name, usage_date ORDER BY usage_date DESC";
}

// Execute query
if (!empty($sql)) {
    try {
        $stmt = $conn->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>📊 Manager Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
            font-family: sans-serif;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            max-width: 1100px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }
        h2 {
            color: #1e3a8a;
        }
        form {
            margin-bottom: 20px;
        }
        select, input[type="date"], button {
            margin-right: 10px;
        }
        button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 7px 14px;
            border-radius: 5px;
        }
        button:hover {
            background: #1d4ed8;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #e0e7ff;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            background: #4f46e5;
            padding: 8px 14px;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-btn:hover {
            background: #4338ca;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>📈 Manager Reports</h2>
    <form method="GET">
        <label>Report Type:</label>
        <select name="report_type">
            <option value="stock" <?= $reportType === 'stock' ? 'selected' : '' ?>>Stock Movement</option>
            <option value="wastage" <?= $reportType === 'wastage' ? 'selected' : '' ?>>Wastage</option>
            <option value="low_stock" <?= $reportType === 'low_stock' ? 'selected' : '' ?>>Low Stock Ingredients</option>
            <option value="ingredient_usage" <?= $reportType === 'ingredient_usage' ? 'selected' : '' ?>>Daily Ingredient Usage</option>
        </select>

        <label>From:</label>
        <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>">

        <label>To:</label>
        <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>">

        <button type="submit">🔍 Filter</button>
        <button type="button" onclick="window.print()">🖨️ Export</button>
    </form>

    <?php if (!empty($data)): ?>
        <table>
            <tr>
                <?php foreach (array_keys($data[0]) as $header): ?>
                    <th><?= htmlspecialchars(ucwords(str_replace('_', ' ', $header))) ?></th>
                <?php endforeach; ?>
            </tr>
            <?php foreach ($data as $row): ?>
                <tr>
                    <?php foreach ($row as $value): ?>
                        <td><?= htmlspecialchars($value ?? 'N/A') ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class="text-muted">No data found for the selected report type and date range.</p>
    <?php endif; ?>

    <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
</div>
</body>
</html>
