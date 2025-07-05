<?php
// connect to DB
$conn = new mysqli("localhost", "root", "", "restaurant");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];

    // Handle image uploadif 
    //($condition) {
    // some code
}


    // Check if uploads directory exists, if not create it
$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
$target_file = $uploadDir . uniqid() . "." . $imageFileType;

// Check if file was uploaded without errors
if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Save relative path to DB for use in website
        $db_path = "uploads/" . basename($target_file);

        $stmt = $conn->prepare("INSERT INTO menu_items (name, price, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $name, $price, $db_path);
        $stmt->execute();
        $stmt->close();

        echo "Menu item added successfully!";
    } else {
        echo "Error: Failed to move uploaded file.";
    }
} else {
    echo "File upload error code: " . $_FILES['image']['error'];
}

?>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f9f9f9;
        padding: 30px;
    }

    form {
        background: #fff;
        max-width: 400px;
        margin: auto;
        padding: 25px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    label {
        font-weight: bold;
        margin-bottom: 6px;
        display: block;
        color: #333;
    }

    input[type="text"],
    input[type="number"],
    input[type="file"] {
        width: 100%;
        padding: 10px 12px;
        margin-bottom: 20px;
        border: 1.8px solid #ccc;
        border-radius: 6px;
        font-size: 15px;
        transition: border-color 0.3s ease;
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="file"]:focus {
        border-color: #4CAF50;
        outline: none;
    }

    button {
        width: 100%;
        background-color: #4CAF50;
        color: white;
        font-size: 17px;
        padding: 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-weight: bold;
    }

    button:hover {
        background-color: #45a049;
    }
</style>

<form method="POST" enctype="multipart/form-data">
    <label>Menu Item Name:</label>
    <input type="text" name="name" required>

    <label>Price:</label>
    <input type="number" step="0.01" name="price" required>

    <label>Image:</label>
    <input type="file" name="image" accept="image/*" required>

    <button type="submit">Add Menu Item</button>
</form>


