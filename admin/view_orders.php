<?php
require '../db.php';

$sql = "SELECT o.id, o.order_time, o.total_price, o.order_status, o.payment_status, c.name AS customer_name
        FROM orders o
        LEFT JOIN customers c ON o.customer_id = c.id
        ORDER BY o.order_time DESC";
$stmt = $conn->query($sql);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cashier - View Orders</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8fafc;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      margin-top: 40px;
    }
    h2 {
      color: #1e293b;
      margin-bottom: 25px;
    }
    table th {
      background-color: #1e3a8a;
      color: #fff;
    }
    table tr:hover {
      background-color: #f1f5f9;
    }
    .badge-warning {
      background-color: #facc15;
      color: #000;
    }
    .btn-back {
      display: inline-block;
      background: #3b82f6;
      color: #fff;
      border-radius: 25px;
      padding: 8px 20px;
      margin-top: 20px;
      text-decoration: none;
      transition: 0.3s ease;
    }
    .btn-back:hover {
      background-color: #1d4ed8;
      color: #fff;
    }
  </style>
</head>
<body>

<div class="container">
  <?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($_GET['msg']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <h2><i class="fas fa-receipt me-2"></i>All Orders</h2>

  <div class="table-responsive">
    <table class="table table-bordered align-middle table-hover">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Items</th>
          <th>Total Price</th>
          <th>Order Time</th>
          <th>Status / Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($orders as $order): ?>
        <?php
          $order_id = $order['id'];
          $customer_name = $order['customer_name'] ?? 'N/A';
          $order_time = $order['order_time'] ?? 'N/A';
          $order_status = strtolower(trim($order['order_status']));
          $total_price = 0;

          $item_stmt = $conn->prepare("SELECT item_name, price, quantity FROM order_items WHERE order_id = ?");
          $item_stmt->execute([$order_id]);
          $items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <tr>
          <td>#<?= htmlspecialchars($order_id) ?></td>
          <td><?= htmlspecialchars($customer_name) ?></td>
          <td>
            <?php foreach ($items as $item): 
              $total_price += $item['price'] * $item['quantity'];
            ?>
              <?= htmlspecialchars($item['item_name']) ?> (Qty: <?= intval($item['quantity']) ?>)<br>
            <?php endforeach; ?>
          </td>
          <td>Rs. <?= number_format($total_price, 2) ?></td>
          <td><?= date('Y-m-d H:i A', strtotime($order_time)) ?></td>
          <td>
            <?php if ($order_status === 'approved'): ?>
              <a href="mark_paid.php?id=<?= $order_id ?>" class="btn btn-success btn-sm">
                <i class="fas fa-money-bill-wave"></i> Mark as Paid
              </a>
            <?php elseif ($order_status === 'paid'): ?>
              <span class="badge bg-success">Paid</span>
            <?php else: ?>
              <span class="badge badge-warning">Pending Approval</span>
            <?php endif; ?>
            <a href="delete_order.php?id=<?= $order_id ?>" class="btn btn-danger btn-sm mt-1" onclick="return confirm('Are you sure you want to delete this order?')">
              <i class="fas fa-trash"></i> Delete
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <a href="admin_dashboard.php" class="btn-back"><i class="fas fa-arrow-left me-2"></i>Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
