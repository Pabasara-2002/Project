<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manager - Order Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <script>
        function fetchOrders() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_orders.php", true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById("orderTableBody").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Fetch orders every 5 seconds
        setInterval(fetchOrders, 5000);

        // Also fetch immediately on load
        window.onload = fetchOrders;
    </script>
</head>
<body class="bg-light p-4">
    <div class="container">
        <h3 class="mb-4">🧾 Current Order Summary (Auto Updating)</h3>
        <table class="table table-bordered bg-white shadow">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Table</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Total (Rs)</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody id="orderTableBody">
                <!-- Dynamic data will be loaded here -->
            </tbody>
        </table>
    </div>
</body>
</html>
