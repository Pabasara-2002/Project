

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

        <!-- Main Courses -->
        <h2 class="category">Main Courses</h2>
        <div class="menu-grid">
           <div class="menu-item">
    <img src="4.png" alt="Fried Rice Chicken">
    <h3>Fried Rice (Chicken)</h3>
    <p>Rs. 950.00</p>
    <a href="add_to_cart.php?id=83&item=Fried+ Rice+(chicken)&price=950.00" class="order-btn">Order Now</a>
</div>

        <div class="menu-item">
            <img src="5.jpg" alt="Cheese Kottu">
            <h3>Cheese Kottu</h3>
            <p>Rs. 1200.00</p>
            <a href="add_to_cart.php?id=85&item=Cheese+kottu&price=1200.00" class="order-btn">Order Now</a>
        </div>
            
            <div class="menu-item">
                <img src="6.jpg" alt="Chicken Biryani">
                <h3>Chicken Biryani</h3>
                <p>Rs. 1550.00</p>
                 <a href="add_to_cart.php?id=86&item=Chicken+biryani&price=1550.00" class="order-btn">Order Now</a>
            </div>
                        <div class="menu-item">
                <img src="7.jpg" alt="Fries Rice (Egg)">
                <h3>Fried Rice (Egg)</h3>
                <p>Rs. 850.00</p>
                 <a href="add_to_cart.php?id=82&item=Fried+Rice+(Egg)&price=850.00" class="order-btn">Order Now</a>
            </div>
            <div class="menu-item">
                <img src="8.jpg" alt="Chicken Kottu">
                <h3>Chicken Kottu</h3>
                <p>Rs. 1300.00</p>
                 <a href="add_to_cart.php?id=81&item=Fried+Rice+(Chicken)&price=1300.00" class="order-btn">Order Now</a>
            </div>
            <div class="menu-item">
                <img src="9.jpg" alt="Nasi Goreng">
                <h3>Nasi Goreng</h3>
                <p>Rs. 1300.00</p>
                 <a href="add_to_cart.php?id=84&item=Nasi+Goreng&price=1300.00" class="order-btn">Order Now</a>
            </div>
             <div class="menu-item">
                <img src="10.jpg" alt="Devilled Chicken">
                <h3>Devilled Chicken</h3>
                <p>Rs. 1100.00</p>
                 <a href="add_to_cart.php?id=80&item=Devilled+Chiken&price=1100.00" class="order-btn">Order Now</a>
            </div>
             <div class="menu-item">
                <img src="11.jpg" alt="Vegetable Kottu">
                <h3>Vegetable Kottu</h3>
                <p>Rs. 1050.00</p>
                 <a href="add_to_cart.php?id=79&item=Vegetable+Kottu&price=1050.00" class="order-btn">Order Now</a>
            </div>
            <div class="menu-item">
                <img src="29.jpg" alt="Tomato Garlic Pasta">
                <h3>Tomato Garlic Pasta</h3>
                <p>Rs. 1050.00</p>
                 <a href="add_to_cart.php?id=89&item=TomatoGarlicPasta&price=1300.00" class="order-btn">Order Now</a>
            </div>
            <div class="menu-item">
                <img src="30.jpg" alt="Chinese Vegetable Stir Fry Noodles">
                <h3>Chinese Vegetable Stir Fry Noodles</h3>
                <p>Rs. 1250.00</p>
                 <a href="add_to_cart.php?id=88&item=ChineseVegetableStirFryNoodles&price=1250.00" class="order-btn">Order Now</a>
            </div>



        </div>

        <!-- Snacks -->
        <h2 class="category">Snacks</h2>
        <div class="menu-grid">
            <div class="menu-item">
                <img src="12.jpg" alt="Egg Roll">
                <h3>Egg Roll</h3>
                <p>Rs. 120.00</p>
                 <a href="add_to_cart.php?id=41&item=Fried+Rice+(Chicken)" class="order-btn">Order Now</a>
            </div>
            <div class="menu-item">
                <img src="13.jpg" alt="Fish Bun">
                <h3>Fish Bun</h3>
                <p>Rs. 100.00</p>
                 <a href="add_to_cart.php?id=40&item=Fish+Bun&price=100.00" class="order-btn">Order Now</a>
            </div>
            <div class="menu-item">
                <img src="14.jpg" alt="Cutlet">
                <h3>Cutlet</h3>
                <p>Rs. 90.00</p>
                 <a href="add_to_cart.php?id=39&item=Cutlet&price=90.00" class="order-btn">Order Now</a>
            </div>
            <div class="menu-item">
                <img src="15.jpg" alt="Chinese Roll Roll">
                <h3>Chinese Roll </h3>
                <p>Rs. 140.00</p>
                 <a href="add_to_cart.php?id=76&item=Chiness+Roll&price=140.00" class="order-btn">Order Now</a>
</div>
                <div class="menu-item">
                <img src="16.jpg" alt="Sausage Bun">
                <h3>Sausage Bun</h3>
                <p>Rs. 170.00</p>
                 <a href="add_to_cart.php?id=74&item=Sausage+Bun&price=170.00" class="order-btn">Order Now</a>
</div>
                <div class="menu-item">
                <img src="17.jpg" alt="Patties">
                <h3> Patties</h3>
                
                <p>Rs. 80.00</p>
                 <a href="add_to_cart.php?id=&item=patties&price=80.00" class="order-btn">Order Now</a>
        </div>
        <div class="menu-item">
                <img src="18.jpg" alt="Chicken club Sandwhich">
                <h3>Chicken club Sandwhich </h3>
                
                <p>Rs. 150.00</p>
                 <a href="add_to_cart.php?id=77&item=Chicken+club+Sandwhich&price=150.00" class="order-btn">Order Now</a>
        </div>
        <div class="menu-item">
                <img src="19.jpg" alt="Burger">
                <h3>Burger </h3>
                
                <p>Rs. 220.00</p>
                 <a href="add_to_cart.php?id=78&item=Burger&price=220.00" class="order-btn">Order Now</a>
        </div>
</div>
        <!-- Beverages -->
        <h2 class="category">Beverages</h2>
        <div class="menu-grid">
            <div class="menu-item">
                <img src="20.jpg" alt="Iced Tea">
                <h3>Iced Tea</h3>
                <p>Rs. 150.00</p>
                 <a href="add_to_cart.php?id=70&item=Iced+Tea&price=150.00" class="order-btn">Order Now</a>
            </div>
            <div class="menu-item">
                <img src="21.jpg" alt="Coffee">
                <h3>Coffee</h3>
                <p>Rs. 300.00</p>
                 <a href="add_to_cart.php?id=71&item=Coffee&price=300.00" class="order-btn">Order Now</a>
            </div>
            <div class="menu-item">
                <img src="22.jpg" alt="Orange Juice">
                <h3>Orange Juice</h3>
                <p>Rs. 200.00</p>
                 <a href="add_to_cart.php?id=66&item=Orange+Juice&price=200.00" class="order-btn">Order Now</a>
            </div>
</div>
            <!-- Desserts-->
        <h2 class="category">desserts</h2>
        <div class="menu-grid">
            <div class="menu-item">
                <img src="23.jpg" alt="Watalappan">
                <h3>Watalappan</h3>
                <p>Rs. 150.00</p>
                 <a href="add_to_cart.php?id=68&item=Watalappan&price=150.00" class="order-btn">Order Now</a>
            </div>
            <div class="menu-item">
                <img src="24.jpg" alt="Chocolate Cake(Slice)">
                <h3>Chocolate Cake(Slice)</h3>
                <p>Rs.250.00</p>
                 <a href="add_to_cart.php?id=72&item=Chocolate+cakeSlice&price=250.00" class="order-btn">Order Now</a>
            </div>
            <div class="menu-item">
                <img src="25.jpg" alt="Fruit Salad">
                <h3>Fruit Salad</h3>
                <p>Rs.200.00</p>
                 <a href="add_to_cart.php?id=65&item=Fruit+Salad&price=200.00" class="order-btn">Order Now</a>
            </div>
            <div class="menu-item">
                <img src="28.jpg" alt="Fruit Salad">
                <h3>Mango Lassi</h3>
                <p>Rs.400.00</p>
                 <a href="add_to_cart.php?id=67&item=MangoLassi&price=400.00" class="order-btn">Order Now</a>
            </div>
           
        </div>
    </div>
</body>
</html>
