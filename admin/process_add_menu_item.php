<?php
session_start();
require_once '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = trim($_POST['item_name']);
    $price = floatval($_POST['item_price']);

    $image = $_FILES['item_image']['name'];
    $temp_name = $_FILES['item_image']['tmp_name'];
    $upload_dir = "../uploads/";

    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
    $file_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_exts)) {
        die("Invalid image type.");
    }

    $image_path = $upload_dir . basename($image);
    if (!move_uploaded_file($temp_name, $image_path)) {
        die("Image upload failed.");
    }

    // Insert into menu_items
    $stmt = $conn->prepare("INSERT INTO menu_items (item_name, price, image) VALUES (?, ?, ?)");
    $stmt->execute([$item_name, $price, $image]);
    $menu_item_id = $conn->lastInsertId();

    // Handle ingredients
    $ingredients = $_POST['ingredients'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $units = $_POST['units'] ?? [];

    for ($i = 0; $i < count($ingredients); $i++) {
        $ingredient_name = trim($ingredients[$i]);
        $quantity_required = isset($quantities[$i]) ? floatval($quantities[$i]) : 0;
        $unit = trim($units[$i]);

        if (empty($ingredient_name) || $quantity_required <= 0 || empty($unit)) {
            continue; // skip invalid entries
        }

        // Check if ingredient exists
        $stmtCheck = $conn->prepare("SELECT ingredient_id, unit FROM ingredients WHERE ingredient_name = ?");
        $stmtCheck->execute([$ingredient_name]);
        $ingredient = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($ingredient) {
            $ingredient_id = $ingredient['id'];

            // Update unit if missing
            if (empty($ingredient['unit'])) {
                $stmtUpdate = $conn->prepare("UPDATE ingredients SET unit = ? WHERE id = ?");
                $stmtUpdate->execute([$unit, $ingredient_id]);
            }
        } else {
            // Insert new ingredient
            $stmtInsert = $conn->prepare("INSERT INTO ingredients (ingredient_name, quantity, unit, low_stock_threshold) VALUES (?, 0, ?, 0)");
            $stmtInsert->execute([$ingredient_name, $unit]);
            $ingredient_id = $conn->lastInsertId();
        }

        // Link to menu item
        $stmtLink = $conn->prepare("INSERT INTO menu_ingredients (menu_item_id, ingredient_id, quantity_required) VALUES (?, ?, ?)");
        $stmtLink->execute([$menu_item_id, $ingredient_id, $quantity_required]);
    }

    header("Location: add_menu_item.php?success=1");
    exit();
} else {
    echo "Invalid request method.";
}
