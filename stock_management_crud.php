
<?php
session_start();
include('../db.php');

// Add Stock Item
if (isset($_POST['add_stock'])) {
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];

    $stmt = $conn->prepare("INSERT INTO stock (item_name, quantity, unit) VALUES (:item_name, :quantity, :unit)");
    $stmt->bindParam(':item_name', $item_name);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':unit', $unit);
    $stmt->execute();
    header('Location: stock_dashboard.php');
}

// Update Stock Item
if (isset($_POST['update_stock'])) {
    $id = $_POST['id'];
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];

    $stmt = $conn->prepare("UPDATE stock SET item_name = :item_name, quantity = :quantity, unit = :unit WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':item_name', $item_name);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':unit', $unit);
    $stmt->execute();
    header('Location: stock_dashboard.php');
}

// Delete Stock Item
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM stock WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    header('Location: stock_dashboard.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6 bg-gray-100">

    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Stock Management</h1>

        <!-- Add Stock Form -->
        <form method="POST" class="space-y-4 mb-6">
            <input type="text" name="item_name" placeholder="Item Name" class="w-full p-2 border rounded" required>
            <input type="number" name="quantity" placeholder="Quantity" class="w-full p-2 border rounded" required>
            <input type="text" name="unit" placeholder="Unit" class="w-full p-2 border rounded" required>
            <button type="submit" name="add_stock" class="w-full bg-blue-500 text-white py-2 rounded">Add Stock</button>
        </form>

        <!-- Stock List -->
        <h2 class="text-xl font-semibold mb-4">Current Stock</h2>
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="border p-2">Item Name</th>
                    <th class="border p-2">Quantity</th>
                    <th class="border p-2">Unit</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $stmt = $conn->query("SELECT * FROM stock");
                    while ($row = $stmt->fetch()) {
                        echo '<tr>';
                        echo '<td class="border p-2">' . $row['item_name'] . '</td>';
                        echo '<td class="border p-2">' . $row['quantity'] . '</td>';
                        echo '<td class="border p-2">' . $row['unit'] . '</td>';
                        echo '<td class="border p-2">
                                <a href="edit_stock.php?id=' . $row['id'] . '" class="text-blue-500">Edit</a> | 
                                <a href="?delete=' . $row['id'] . '" class="text-red-500">Delete</a>
                              </td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>
