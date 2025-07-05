<?php
session_start();
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);

    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8fafc;
        }

        .sidebar {
            height: 100vh;
            width: 250px;
            background: linear-gradient(180deg, #1e3a8a, #2563eb);
            padding-top: 30px;
            position: fixed;
            color: white;
        }

        .sidebar a {
            color: #e2e8f0;
            padding: 15px 25px;
            display: block;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar a:hover, .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.2);
            padding-left: 35px;
        }

        .sidebar .logout {
            background: #dc2626;
            margin-top: 30px;
            text-align: center;
            font-weight: bold;
        }

        .main {
            margin-left: 250px;
            padding: 40px;
        }

        .form-container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .back-btn {
            text-align: right;
        }

    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center mb-4"><i class="fas fa-utensils me-2"></i>Admin Panel</h4>
    <a href="admin_dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-chart-line me-2"></i> Dashboard</a>
    <a href="manage_users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : '' ?>"><i class="fas fa-users-cog me-2"></i> Manage Users</a>
    <a href="manage_stock.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_stock.php' ? 'active' : '' ?>"><i class="fas fa-boxes me-2"></i> Manage Stock</a>
    <a href="reports_dashboards.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports_dashboards.php' ? 'active' : '' ?>"><i class="fas fa-chart-bar me-2"></i> Reports</a>
    <a href="approvals.php" class="<?= basename($_SERVER['PHP_SELF']) == 'approvals.php' ? 'active' : '' ?>"><i class="fas fa-check-circle me-2"></i> Approvals</a>
    <a href="enable_disable_users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'enable_disable_users.php' ? 'active' : '' ?>"><i class="fas fa-user-lock me-2"></i> Enable/Disable Users</a>
    <a href="request_reorder.php" class="<?= basename($_SERVER['PHP_SELF']) == 'request_reorder.php' ? 'active' : '' ?>"><i class="fas fa-undo-alt me-2"></i> Request Reorder</a>
    <a href="../logout.php" class="logout btn text-white"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main">
    <div class="form-container">
        <h2>➕ Add New User</h2>
        <form method="POST">
            <div class="mb-3">
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="mb-3">
                <select class="form-select" name="role" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="stock_keeper">Stock Keeper</option>
                    <option value="supplier">Supplier</option>
                    <option value="cashier">Cashier</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Create</button>
        </form>

        <div class="back-btn mt-3">
            <a href="manage_users.php" class="btn btn-secondary">⬅️ Back</a>
        </div>
    </div>
</div>

</body>
</html>
