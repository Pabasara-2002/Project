<?php
session_start();
include('../db.php');

// Check admin login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch stock items
$stmt = $conn->query("SELECT * FROM stock_items ORDER BY updated_at DESC");
$stock_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }

        .report-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.05);
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
        }

        .report-header h1 {
            font-size: 26px;
            font-weight: 600;
            color: #1e293b;
        }

        .btn-print, .btn-back {
            font-weight: 500;
            border-radius: 6px;
            padding: 8px 16px;
        }

        .btn-print {
            background-color: #2563eb;
            color: white;
        }

        .btn-back {
            background-color: #64748b;
            color: white;
            margin-top: 20px;
        }

        .btn-print:hover {
            background-color: #1d4ed8;
        }

        .btn-back:hover {
            background-color: #475569;
        }

        table th {
            background-color: #e2e8f0;
            color: #1e293b;
        }

        table td, table th {
            vertical-align: middle;
        }

        @media print {
            .btn-print, .btn-back, .report-header {
                display: none !important;
            }

            body {
                background: white;
                margin: 0;
                padding: 0;
            }

            .report-container {
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            table, th, td {
                border: 1px solid #000 !important;
                border-collapse: collapse;
                font-size: 12pt;
            }

            th {
                background-color: #ccc !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="report-container">
    <div class="report-header">
        <h1><i class="fas fa-clipboard-list text-primary"></i> Stock Report</h1>
        <button class="btn btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Print Report
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Reorder Level</th>
                    <th>Unit</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($stock_items) > 0): ?>
                    <?php foreach ($stock_items as $index => $item): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= $item['reorder_level'] ?></td>
                            <td><?= $item['unit'] ?></td>
                            <td><?= $item['updated_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">No stock items found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="admin_dashboard.php" class="btn btn-back">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
