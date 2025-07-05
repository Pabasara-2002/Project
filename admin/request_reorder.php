<?php
include('../db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("SELECT stock_item_id, quantity FROM reorder_requests WHERE id = ?");
        $stmt->execute([$request_id]);
        $reorder = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reorder) {
            $update = $conn->prepare("UPDATE stock_items SET quantity = quantity + ? WHERE id = ?");
            $update->execute([$reorder['quantity'], $reorder['stock_item_id']]);

            $updateStatus = $conn->prepare("UPDATE reorder_requests SET status = 'Approved' WHERE id = ?");
            $updateStatus->execute([$request_id]);
        }
    } elseif ($action === 'reject') {
        $updateStatus = $conn->prepare("UPDATE reorder_requests SET status = 'Rejected' WHERE id = ?");
        $updateStatus->execute([$request_id]);
    }

    header("Location: request_reorder.php");
    exit();
}

$sql = "SELECT id, item_name, quantity, requested_by, request_date 
        FROM reorder_request 
        WHERE status = 'Pending' 
        ORDER BY request_date DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Pending Reorder Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding: 0;
            margin: 0;
            background: #f1f5f9;
        }

        /* Sidebar */
        /* Sidebar Styles */
.sidebar {
      width: 260px;
      background: linear-gradient(to bottom, #1e3a8a, #2563eb);
      color: white;
      height: 100vh;
      position: fixed;
      padding: 30px 20px;
      box-shadow: 3px 0 10px rgba(0,0,0,0.1);
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 40px;
      font-size: 24px;
    }

    .sidebar a {
      display: flex;
      align-items: center;
      padding: 12px 18px;
      color: #e2e8f0;
      text-decoration: none;
      border-radius: 6px;
      margin-bottom: 8px;
      transition: background 0.3s ease;
    }

    .sidebar a i {
      margin-right: 12px;
    }

    .sidebar a:hover, .sidebar a.active {
      background-color: rgba(255,255,255,0.2);
    }

    .sidebar a.logout {
      background: #dc2626;
      color: #fff;
      margin-top: 40px;
      text-align: center;
    }

        /* Main Container */
        .container {
            margin-left: 260px;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 8px hsla(224, 74.70%, 61.20%, 0.84);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #3b82f6;
            color: white;
        }

        form {
            display: inline;
        }

        button.approve {
            background: #16a34a;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        button.reject {
            background: #dc2626;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        a.back-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            padding: 10px 18px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<!-- Sidebar Start -->
<!-- Sidebar Start -->
    <div class="sidebar">
  <h2><i class="fas fa-utensils"></i> Admin Panel</h2>
  <a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
  <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
  <a href="manage_stock.php"><i class="fas fa-boxes"></i> Manage Stock</a>
  <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
  <a href="approvals.php"><i class="fas fa-check-circle"></i> Approvals</a>
  <a href="add_menu_item.php"><i class="fas fa-plus-circle"></i> Add Menu Item</a>
      <a href="view_menu_items.php" class="active"><i class="fas fa-eye"></i> View Menu Items</a>
  
  <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
<!-- Sidebar End -->

<!-- Main Content Start -->
<div class="container">
    <h2>📦 Pending Reorder Requests</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Stock Item</th>
            <th>Quantity</th>
            <th>Requested By</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

       
    </table>
</div>
<!-- Main Content End -->

<!-- Back Button -->
<a href="admin_dashboard.php" class="back-btn">🔙 Back to Dashboard</a>

</body>
</html>
