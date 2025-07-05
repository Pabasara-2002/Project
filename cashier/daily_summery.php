<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=restaurant", "root", "");

// Determine filter
$filter = $_GET['filter'] ?? 'daily';

if ($filter === 'daily') {
    // Daily Sales Report - Breakdown per item for today
    $sql = "
        SELECT 
            m.item_name AS item_name,
            SUM(oi.quantity) AS qty_sold,
            SUM(oi.quantity * m.price) AS revenue
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN menu_items m ON oi.item_id = m.id
        WHERE DATE(o.order_date) = CURDATE()
        GROUP BY m.id, m.item_name
        ORDER BY revenue DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $report_type = "Daily Sales by Item";
    $total_sales = array_sum(array_column($items, 'revenue'));

} else {
    // Monthly Sales Report - Totals for current month
    $sql = "
    SELECT 
        DATE(o.order_date) AS date,
        SUM(oi.quantity * mi.price) AS total
    FROM order_items oi
    INNER JOIN menu_items mi ON oi.item_id = mi.id
    INNER JOIN orders o ON o.id = oi.order_id
    WHERE MONTH(o.order_date) = MONTH(CURDATE()) 
      AND YEAR(o.order_date) = YEAR(CURDATE())
    GROUP BY DATE(o.order_date)
    ORDER BY date ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$report_type = "Monthly Sales Report";
$total_sales = array_sum(array_column($orders, 'total'));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $report_type ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body{background:#f7f9fc;font-family:Segoe UI,sans-serif;}
    .container{max-width:1100px;margin:50px auto;background:#fff;padding:30px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.1);}
    .header{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;margin-bottom:20px;}
    .header h2{color:#333;}
    .totals{font-size:20px;color:#28a745;text-align:right;margin-bottom:15px;}
    .filter a{margin-left:8px;}
    .btn-export {
      margin-top: 10px;
      float: right;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="header">
    <h2><?= $report_type ?></h2>
    <div class="filter">
      <a href="?filter=daily"  class="btn btn-sm btn-outline-success <?= $filter==='daily'?'active':'' ?>">Daily</a>
      <a href="?filter=monthly" class="btn btn-sm btn-outline-primary <?= $filter==='monthly'?'active':'' ?>">Monthly</a>
      <button onclick="window.print()" class="btn btn-sm btn-secondary btn-export"><i class="fas fa-print"></i> Print Report</button>
    </div>
  </div>

  <div class="totals">
    Total Sales: Rs. <?= number_format($total_sales,2) ?>
  </div>

  <?php if ($filter==='daily'): ?>
    <!-- Daily: show item breakdown table -->
    <table class="table table-striped">
      <thead class="table-success">
        <tr>
          <th>#</th>
          <th>Item Name</th>
          <th>Qty Sold</th>
          <th>Revenue (Rs.)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($items as $i => $it): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($it['item_name']) ?></td>
          <td><?= $it['qty_sold'] ?></td>
          <td><?= number_format($it['revenue'],2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Bar chart of revenue by item -->
    <div style="margin-top:30px;">
      <canvas id="itemChart" height="100"></canvas>
    </div>
    <script>
      const ctx = document.getElementById('itemChart').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: <?= json_encode(array_column($items,'item_name')) ?>,
          datasets: [{
            label: 'Revenue (Rs.)',
            data: <?= json_encode(array_column($items,'revenue')) ?>,
            backgroundColor: 'rgba(40,167,69,0.6)'
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display:false },
            title: {
              display: true,
              text: 'Revenue by Item Today'
            }
          },
          scales: {
            x: { ticks:{ autoSkip:false }, grid:{ display:false } },
            y: { beginAtZero:true }
          }
        }
      });
    </script>

  <?php else: ?>
    <!-- Monthly: existing table/chart -->
    <table class="table table-striped">
      <thead class="table-success">
        <tr><th>#</th><th>Date</th><th>Total (Rs.)</th></tr>
      </thead>
      <tbody>
      <?php foreach($orders as $i=>$o): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= $o['date'] ?></td>
          <td><?= number_format($o['total'],2) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <div style="margin-top:30px;">
      <canvas id="salesChart" height="100"></canvas>
    </div>
    <script>
      const ctx2 = document.getElementById('salesChart').getContext('2d');
      new Chart(ctx2, {
        type: 'line',
        data: {
          labels: <?= json_encode(array_column($orders,'date')) ?>,
          datasets:[{
            label:'Daily Total',
            data: <?= json_encode(array_column($orders,'total')) ?>,
            borderColor:'rgba(40,167,69,1)', fill:true, backgroundColor:'rgba(40,167,69,0.2)', tension:0.3
          }]
        },
        options:{ responsive:true }
      });
    </script>
  <?php endif; ?>
</div>
<div class="text-start ms-4 mt-3 back-link">
    <a href="dashboard.php" class="btn btn-outline-primary btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

</body>
</html>
