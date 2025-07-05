<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'stock') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: record_stock.php");
    exit();
}

$item_name = $_POST['item_name'] ?? '';
$quantity = floatval($_POST['quantity'] ?? 0);
$reason = $_POST['reason'] ?? '';
$other_reason = trim($_POST['other_reason'] ?? '');

if (empty($item_name) || $quantity <= 0 || empty($reason)) {
    $_SESSION['error'] = "Please fill all required fields correctly.";
    header("Location: record_stock.php");
    exit();
}

if ($reason === 'Other') {
    if (empty($other_reason)) {
        $_SESSION['error'] = "Please specify the other reason.";
        header("Location: record_stock.php");
        exit();
    }
    $reason = $other_reason;
}

try {
    $recorded_by = $_SESSION['username'];
    $conn->beginTransaction();

    // Get item info
    $checkStmt = $conn->prepare("SELECT id, quantity, reorder_level FROM stock_items WHERE item_name = ?");
    $checkStmt->execute([$item_name]);
    $item = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        throw new Exception("Item not found in stock.");
    }

    if ($item['quantity'] < $quantity) {
        throw new Exception("Not enough stock available for wastage.");
    }

    // Deduct stock
    $updateStmt = $conn->prepare("UPDATE stock_items SET quantity = quantity - ? WHERE id = ?");
    $updateStmt->execute([$quantity, $item['id']]);

    // Record wastage
    $insertStmt = $conn->prepare("
        INSERT INTO stock_wastage (item_name, quantity, reason, recorded_by, recorded_at) 
        VALUES (:item_name, :quantity, :reason, :recorded_by, NOW())"
    );

    $insertStmt->bindParam(':item_name', $item_name, PDO::PARAM_STR);
    $insertStmt->bindParam(':quantity', $quantity);
    $insertStmt->bindParam(':reason', $reason, PDO::PARAM_STR);
    $insertStmt->bindParam(':recorded_by', $recorded_by, PDO::PARAM_STR);
    $insertStmt->execute();

    // ✅ Check for low stock and create alert
    $checkLevelStmt = $conn->prepare("SELECT quantity, reorder_level FROM stock_items WHERE id = ?");
    $checkLevelStmt->execute([$item['id']]);
    $levelData = $checkLevelStmt->fetch(PDO::FETCH_ASSOC);

    if ($levelData && floatval($levelData['quantity']) <= floatval($levelData['reorder_level'])) {
        $alertCheck = $conn->prepare("SELECT id FROM low_stock_alerts WHERE item_name = ? AND is_read = 0");
        $alertCheck->execute([$item_name]);

        if ($alertCheck->rowCount() == 0) {
            $message = $item_name . " is low (" . number_format($levelData['quantity'], 2) . ")";
            $insertAlert = $conn->prepare("INSERT INTO low_stock_alerts (item_name, message, created_at, is_read) VALUES (?, ?, NOW(), 0)");
            $insertAlert->execute([$item_name, $message]);
        }
    }

    $conn->commit();
    $_SESSION['success'] = "Stock wastage recorded and stock updated successfully.";
    header("Location: record_stock.php");
    exit();

} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header("Location: record_stock.php");
}
?>