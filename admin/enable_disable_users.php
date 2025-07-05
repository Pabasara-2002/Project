<?php
session_start();
include('../db.php');

// Admin check
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Toggle user status
if (isset($_GET['id'], $_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    $status = ($action === 'enable') ? 'active' : 'disabled';
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    header("Location: enable_disable_users.php");
    exit();
}

// Fetch users
$users = $conn->query("SELECT id, username, role, status FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enable / Disable Users</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Poppins', sans-serif;
        }
        .sidebar {
            width: 260px;
            background: linear-gradient(to bottom, #1e3a8a, #2563eb);
            color: white;
            height: 100vh;
            position: fixed;
            padding: 30px 20px;
            box-shadow: 3px 0 10px rgba(0,0,0,0.1);
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
        .sidebar .logout {
            background: #dc2626;
            text-align: center;
            margin-top: 40px;
        }
        .main-content {
            margin-left: 280px;
            padding: 40px;
        }
        .table th {
            background-color: #2c5282;
            color: white;
        }
        .btn-enable {
            background-color: #38a169;
            color: white;
        }
        .btn-disable {
            background-color: #e53e3e;
            color: white;
        }
    </style>
</head>
<body>

<!-- Sidebar Start -->
<div class="sidebar">
    <h4 class="text-center fw-bold mb-4"><i class="fas fa-utensils"></i> Admin Panel</h4>
    <a href="admin_dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="manage_users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : '' ?>"><i class="fas fa-users-cog"></i> Manage Users</a>
    <a href="manage_stock.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_stock.php' ? 'active' : '' ?>"><i class="fas fa-boxes"></i> Manage Stock</a>
    <a href="reports_dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports_dashboard.php' ? 'active' : '' ?>"><i class="fas fa-chart-bar"></i> Reports</a>
    <a href="approvals.php" class="<?= basename($_SERVER['PHP_SELF']) == 'approvals.php' ? 'active' : '' ?>"><i class="fas fa-check-circle"></i> Approvals</a>
    <a href="enable_disable_users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'enable_disable_users.php' ? 'active' : '' ?>"><i class="fas fa-user-lock"></i> Enable/Disable Users</a>
    <a href="request_reorder.php" class="<?= basename($_SERVER['PHP_SELF']) == 'request_reorder.php' ? 'active' : '' ?>"><i class="fas fa-undo-alt"></i> Request Reorder</a>
    <a href="../logout.php" class="logout p-2 d-block"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
<!-- Sidebar End -->

<!-- Main Content Start -->
<div class="main-content">
   <a href="admin_dashboard.php" class="btn btn-dark mb-4 float-end"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    <h3 class="mb-4">🔐 Enable / Disable Users</h3>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>👤 Username</th>
                    <th>🛡️ Role</th>
                    <th>📍 Status</th>
                    <th>⚙️ Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= htmlspecialchars($user['status']) ?></td>
                        <td>
                            <?php if ($user['status'] === 'disabled'): ?>
                                <a href="?id=<?= $user['id'] ?>&action=enable" class="btn btn-sm btn-enable">Enable</a>
                            <?php else: ?>
                                <a href="?id=<?= $user['id'] ?>&action=disable" class="btn btn-sm btn-disable">Disable</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Main Content End -->

</body>
</html>
