<?php
include('../db.php'); // PDO connection

$sql = "SELECT name, quantity_in_stock FROM ingredients ORDER BY quantity_in_stock ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Ingredient Stock Levels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            padding: 30px;
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        h3 {
            margin-bottom: 20px;
            color: #343a40;
            font-weight: 700;
        }
        table {
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .low-stock {
            color: #dc3545; /* Bootstrap red */
            font-weight: 700;
        }
        .sufficient-stock {
            color: #198754; /* Bootstrap green */
            font-weight: 700;
        }
    </style>
</head>
<body>

    <div class="container">
        <h3>Ingredient Stock Levels</h3>
        <table class="table table-bordered table-hover">
            <thead class="table-secondary">
                <tr>
                    <th>Ingredient</th>
                    <th>Quantity in Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : 
                    $qty = $row['quantity_in_stock'];
                    $class = ($qty < 10) ? 'low-stock' : 'sufficient-stock';
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td class="<?= $class ?>"><?= $qty ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
