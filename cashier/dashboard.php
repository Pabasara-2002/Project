<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cashier Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', sans-serif;
        }

        .sidebar {
            height: 100vh;
            background: linear-gradient(180deg, #1d4ed8, #1e40af);
            color: white;
            position: fixed;
            width: 240px;
            padding-top: 30px;
        }

        .sidebar h4 {
            text-align: center;
            font-weight: bold;
            color: #e0f2fe;
            margin-bottom: 40px;
        }

        .sidebar a {
            color: white;
            display: block;
            padding: 14px 22px;
            text-decoration: none;
            font-size: 15px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: #1e3a8a;
            color: #93c5fd;
        }

        .logout-btn {
            display: block;
            margin: 50px auto 0;
            background-color: #ef4444;
            padding: 10px 24px;
            border-radius: 25px;
            text-align: center;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            color: white;
            width: 80%;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
        }

        .content {
            margin-left: 260px;
            padding: 50px 30px;
        }

        .dashboard-title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 40px;
            color: #1e293b;
        }

        .dashboard-card {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 35px 20px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            transition: 0.3s ease;
            text-align: center;
            height: 100%;
            border-bottom: 4px solid #0ea5e9;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.12);
        }

        .card-icon {
            font-size: 38px;
            color: #0284c7;
        }

        .card-title {
            margin-top: 18px;
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
        }

        .go-btn {
            margin-top: 14px;
            background-color: #0ea5e9;
            color: white;
            border: none;
            padding: 8px 22px;
            border-radius: 20px;
            font-size: 14px;
            transition: 0.3s;
        }

        .go-btn:hover {
            background-color: #0369a1;
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 20px;
            }
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                padding: 15px 0;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h4><i class="fas fa-cash-register me-2"></i>Cashier</h4>
    <a href="dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
    <a href="view_bills.php"><i class="fas fa-file-invoice-dollar me-2"></i> View Bills</a>
    <a href="daily_summery.php"><i class="fas fa-chart-line me-2"></i> Daily Sales</a>
    <a href="cashier_view_orders.php"><i class="fas fa-box me-2"></i> View Orders</a>
   
    <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<div class="content">
    <div class="dashboard-title">💵 Cashier Dashboard</div>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <div class="col">
            <div class="dashboard-card">
                <i class="fas fa-file-invoice-dollar card-icon"></i>
                <div class="card-title">View & Print Bills</div>
                <a href="view_bills.php"><button class="go-btn">Go</button></a>
            </div>
        </div>
        <div class="col">
            <div class="dashboard-card">
                <i class="fas fa-chart-line card-icon"></i>
                <div class="card-title">Daily Sales Report</div>
                <a href="daily_summery.php"><button class="go-btn">Go</button></a>
            </div>
        </div>
        <div class="col">
            <div class="dashboard-card">
                <i class="fas fa-box card-icon"></i>
                <div class="card-title">View Orders</div>
                <a href="cashier_view_orders.php"><button class="go-btn">Go</button></a>
            </div>
        </div>
        
        </div>
    </div>
</div>

</body>
</html>
