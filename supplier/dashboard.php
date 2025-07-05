<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'supplier') {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Supplier Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6 bg-gray-100">
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold">Supplier Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['username']; ?>!</p>
        <a href="../logout.php" class="text-red-500 hover:underline">Logout</a>
    </div>
</body>
</html>
