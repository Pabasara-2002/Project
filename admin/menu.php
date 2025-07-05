<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Full Menu</title>
    <link rel="stylesheet" href="menu.css">
</head>
<body>
<!-- ✅ Navigation Bar -->
<div class="navbar">
    <div class="nav-right">
        <a href="index.php">Home</a>
        <?php if (isset($_SESSION['customer_id'])): ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <h1 class="menu-title">Our Menu</h1>

    <!-- 🍛 Main Courses -->
    <h2 class="category">Main Courses</h2>
    <div class="menu-grid">
        <?php
        $main_courses = [
            ["Fried Rice (Chicken)", "4.png", 950],
            ["Cheese Kottu", "5.jpg", 1200],
            ["Chicken Biryani", "6.jpg", 1550],
            ["Fried Rice (Egg)", "7.jpg", 850],
            ["Chicken Kottu", "8.jpg", 1300],
            ["Nasi Goreng", "9.jpg", 1300],
            ["Devilled Chicken", "10.jpg", 1100],
            ["Vegetable Kottu", "11.jpg", 1050],
        ];
        foreach ($main_courses as $item) {
            echo '<div class="menu-item">
                <img src="'.$item[1].'" alt="'.$item[0].'">
                <h3>'.$item[0].'</h3>
                <p>Rs. '.$item[2].'.00</p>
                <form method="POST" action="add_to_cart.php">
                    <input type="hidden" name="item_name" value="'.$item[0].'">
                    <input type="hidden" name="price" value="'.$item[2].'">
                    <button type="submit" class="order-btn">Order Now</button>
                </form>
            </div>';
        }
        ?>
    </div>

    <!-- 🥟 Snacks -->
    <h2 class="category">Snacks</h2>
    <div class="menu-grid">
        <?php
        $snacks = [
            ["Egg Roll", "12.jpg", 120],
            ["Fish Bun", "13.jpg", 100],
            ["Cutlet", "14.jpg", 90],
            ["Chinese Roll", "15.jpg", 140],
            ["Sausage Bun", "16.jpg", 170],
            ["Patties", "17.jpg", 80],
            ["Chicken Club Sandwich", "18.jpg", 150],
            ["Burger", "19.jpg", 220],
        ];
        foreach ($snacks as $item) {
            echo '<div class="menu-item">
                <img src="'.$item[1].'" alt="'.$item[0].'">
                <h3>'.$item[0].'</h3>
                <p>Rs. '.$item[2].'.00</p>
                <form method="POST" action="add_to_cart.php">
                    <input type="hidden" name="item_name" value="'.$item[0].'">
                    <input type="hidden" name="price" value="'.$item[2].'">
                    <button type="submit" class="order-btn">Order Now</button>
                </form>
            </div>';
        }
        ?>
    </div>

    <!-- 🥤 Beverages -->
    <h2 class="category">Beverages</h2>
    <div class="menu-grid">
        <?php
        $beverages = [
            ["Iced Tea", "20.jpg", 150],
            ["Coffee", "21.jpg", 300],
            ["Orange Juice", "22.jpg", 200],
        ];
        foreach ($beverages as $item) {
            echo '<div class="menu-item">
                <img src="'.$item[1].'" alt="'.$item[0].'">
                <h3>'.$item[0].'</h3>
                <p>Rs. '.$item[2].'.00</p>
                <form method="POST" action="add_to_cart.php">
                    <input type="hidden" name="item_name" value="'.$item[0].'">
                    <input type="hidden" name="price" value="'.$item[2].'">
                    <button type="submit" class="order-btn">Order Now</button>
                </form>
            </div>';
        }
        ?>
    </div>

    <!-- 🍨 Desserts -->
    <h2 class="category">Desserts</h2>
    <div class="menu-grid">
        <?php
        $desserts = [
            ["Watalappan", "23.jpg", 150],
            ["Chocolate Cake", "24.jpg", 250],
            ["Fruit Salad", "25.jpg", 200],
        ];
        foreach ($desserts as $item) {
            echo '<div class="menu-item">
                <img src="'.$item[1].'" alt="'.$item[0].'">
                <h3>'.$item[0].'</h3>
                <p>Rs. '.$item[2].'.00</p>
                <form method="POST" action="add_to_cart.php">
                    <input type="hidden" name="item_name" value="'.$item[0].'">
                    <input type="hidden" name="price" value="'.$item[2].'">
                    <button type="submit" class="order-btn">Order Now</button>
                </form>
            </div>';
        }
        ?>
    </div>
</div>
</body>
</html>

