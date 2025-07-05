<?php
session_start();
include('../db.php');

// Check if user is logged in and is a stockkeeper
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'stock') {
    header("Location: ../login.php");
    exit();
}

// Get item details by ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM stock WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        echo "Item not found!";
        exit();
    }
} else {
    echo "No ID provided!";
    exit();
}

// Handle update form submission
if (isset($_POST['update'])) {
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $expiration_date = $_POST['expiration_date'];
    $category = $_POST['category'];

    $update = $conn->prepare("UPDATE stock SET item_name = ?, quantity = ?, unit = ?, expiration_date = ?, category = ? WHERE id = ?");
    $update->execute([$item_name, $quantity, $unit, $expiration_date, $category, $id]);

    // Optional: Log stock movement
    $log = $conn->prepare("INSERT INTO stock_movements (item_name, action, quantity, unit, reason, user) VALUES (?, 'Updated', ?, ?, 'Manual update by stockkeeper', ?)");
    $stmt = $conn->prepare("UPDATE stock SET item_name=?, quantity=?, unit=?, expiration_date=?, category=?, user=? WHERE id=?");


    header("Location: view_stock.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Stock Item</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f0f2f5;
            padding: 40px;
        }

        form {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            width: 400px;
            margin: auto;
            box-shadow: 0px 0px 10px #ccc;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        button {
            background-color: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            width: 100%;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>

<form method="post">
    <h2>Update Stock Item</h2>
    <label>Item Name</label>
    <input type="text" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>" required>

    <label>Quantity</label>
    <input type="number" name="quantity" value="<?= htmlspecialchars($item['quantity']) ?>" required>

    <label>Unit</label>
    <input type="text" name="unit" value="<?= htmlspecialchars($item['unit']) ?>" required>

    <label>Expiration Date</label>
    <input type="date" name="expiration_date" value="<?= htmlspecialchars($item['expiration_date']) ?>">

    <label>Category</label>
    <input type="text" name="category" value="<?= htmlspecialchars($item['category']) ?>">

    <button type="submit" name="update">Update Item</button>
</form>

</body>
</html>
