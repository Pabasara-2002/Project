<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #f1f5f9;
}

.sidebar {
    width: 240px;
    height: 100vh;
    background: rgb(19, 64, 160);
    padding: 20px 0;
    position: fixed;
    top: 0;
    left: 0;
    overflow-y: auto;
    box-shadow: 2px 0 10px ;
}

.sidebar h2 {
    color: #ffffff;
    margin-bottom: 30px;
    font-size: 22px;
    text-align: center;
    font-weight: 600;
    letter-spacing: 1px;
}

.sidebar a {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #adb5bd;
    padding: 12px 20px;
    margin-bottom: 8px;
    text-decoration: none;
    font-size: 16px;
    border-left: 4px solid transparent;
    transition: 0.3s all ease;
}

.sidebar a:hover {
    background: rgb(19, 64, 160);
    color: #fff;
    border-left: 4px solid #0d6efd;
}

.sidebar a.active {
    background: #0d6efd;
    color: #fff;
    border-left: 4px solid #ffffff;
}

.content {
    margin-left: 260px;
    padding: 30px;
}
.sidebar a.logout {
      background: #ef4444;
    }

</style>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="manage_users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : '' ?>">👤 <span>Manage Users</span></a>
    <a href="manage_stock.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_stock.php' ? 'active' : '' ?>">📦 <span>Manage Stock</span></a>
    
    <a href="reports_dashboards.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports_dashboards.php' ? 'active' : '' ?>">📊 <span>Reports & Dashboards</span></a>
    <a href="system_settings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'system_settings.php' ? 'active' : '' ?>">⚙️ <span>System Settings</span></a>
    <a href="approvals.php" class="<?= basename($_SERVER['PHP_SELF']) == 'approvals.php' ? 'active' : '' ?>">✅ <span>Approvals</span></a>
    <a href="enable_disable_users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'enable_disable_users.php' ? 'active' : '' ?>">🔍 <span>Enable/Disable Users</span></a>
    <a href="integrations.php" class="<?= basename($_SERVER['PHP_SELF']) == 'integrations.php' ? 'active' : '' ?>">📝 <span>Integrations</span></a>
    <a href="logout.php">🚪 <span>Logout</span></a>
</div>
