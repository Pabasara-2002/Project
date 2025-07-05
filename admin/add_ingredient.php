<?php
session_start();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['ingredient_name']);
    $quantity = floatval($_POST['quantity']);
    $unit = trim($_POST['unit']);
    $threshold = floatval($_POST['low_stock_threshold']);

    if (empty($name) || $quantity < 0 || empty($unit) || $threshold < 0) {
        echo "<script>alert('Invalid input values.'); window.history.back();</script>";
        exit;
    }

    try {
        $check = $conn->prepare("SELECT COUNT(*) FROM ingredients WHERE ingredient_name = ?");
        $check->execute([$name]);
        if ($check->fetchColumn() > 0) {
            echo "<script>alert('Ingredient already exists!'); window.history.back();</script>";
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO ingredients (ingredient_name, quantity, unit, low_stock_threshold) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $quantity, $unit, $threshold]);

        header("Location: add_menu_item.php?ingredient=success");
        exit;
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        exit;
    }
} else {
    echo "Invalid access.";
}
