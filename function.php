<?php
function reduceStockAndCheckAlert($conn, $item_name, $reduce_quantity, $username = 'system') {
    // Get current stock and reorder level
    $stmt = $conn->prepare("SELECT quantity, reorder_level FROM stock_items WHERE item_name = ?");
    $stmt->execute([$item_name]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        return "❌ Stock item not found.";
    }

    $new_qty = max(0, $item['quantity'] - $reduce_quantity);

    // Update stock quantity
    $update = $conn->prepare("UPDATE stock_items SET quantity = ? WHERE item_name = ?");
    $update->execute([$new_qty, $item_name]);

    // Add to stock adjustment history
    $log = $conn->prepare("INSERT INTO stock_adjustment_history (item_name, adjustment_type, quantity, adjusted_by, adjusted_at) VALUES (?, 'remove', ?, ?, NOW())");
    $log->execute([$item_name, $reduce_quantity, $username]);

    // Check for low stock
    if ($new_qty <= $item['reorder_level']) {
        // Avoid duplicate alerts
        $check = $conn->prepare("SELECT COUNT(*) FROM low_stock_alerts WHERE item_name = ? AND is_read = 0");
        $check->execute([$item_name]);
        if ($check->fetchColumn() == 0) {
            $msg = "$item_name stock is low: $new_qty units remaining.";
            $alert = $conn->prepare("INSERT INTO low_stock_alerts (item_name, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
            $alert->execute([$item_name, $msg]);
        }
    }

    return "✅ $item_name stock reduced by $reduce_quantity.";
}
?>
