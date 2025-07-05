<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Welcome to Gagul - Order Now</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Segoe UI', sans-serif;
      overflow-x: hidden;
    }

    .top-bar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 60px;
      background: linear-gradient(90deg, rgba(243, 100, 17, 0.86), rgb(240, 139, 38));
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      font-weight: bold;
      z-index: 1000;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .top-bar i {
      margin-right: 10px;
    }

    .top-buttons {
      position: absolute;
      right: 20px;
      top: 10px;
      display: flex;
      gap: 10px;
    }

    .btn-light {
      padding: 5px 14px;
      font-size: 14px;
      border-radius: 25px;
      font-weight: 500;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    .hero {
      height: 100vh;
      width: 100%;
      color: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      position: relative;
      text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.8);
      background-size: cover;
      background-position: center;
      transition: background-image 1s ease-in-out;
      margin-top: 60px;
    }

    .hero h1 {
      font-size: 3.5rem;
      z-index: 2;
      animation: fadeInDown 1.2s ease-in-out;
      margin-top: 40px;
    }

    .hero p {
      font-size: 1.2rem;
      z-index: 2;
      margin-top: 10px;
      animation: fadeInUp 1.5s ease-in-out;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1;
    }

    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .order-btn {
        background-color: #a3f7b5;
        color: #000;
        font-weight: 500;
        transition: all 0.3s ease-in-out;
        border: none;
    }

    .order-btn:hover {
        background-color: #7edc9e;
        transform: scale(1.03);
    }

    .menu-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        background-color: #fff;
    }

    .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.18);
    }

    .card-img-top {
        border-radius: 12px 12px 0 0;
        transition: transform 0.4s ease;
        box-shadow: inset 0 0 20px rgba(0,0,0,0.05);
        display: block;
        width: 100%;
        height: 180px;
        object-fit: cover;
        position: relative;
        z-index: 1;
    }
  </style>
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
  <i class="fas fa-utensils"></i> Welcome to Gagul Restaurant
  <div class="top-buttons">
    <a href="register.php" class="btn btn-light">Register</a>
    <a href="login.php" class="btn btn-light">Login</a>
  </div>
</div>

<!-- Hero Section -->
<div class="hero" id="hero">
  <h1>Enjoy Delicious Moments</h1>
  <p><strong>We are open daily from 10:00 AM to 10:00 PM.</strong> Place your orders now!</p>
</div>

<!-- Background Rotation -->
<script>
  const images = ['3.jpeg','home1.jpg','26.jpg'];
  let currentIndex = 0;
  const hero = document.getElementById('hero');

  function changeBackground() {
    hero.style.backgroundImage = `url('${images[currentIndex]}')`;
    currentIndex = (currentIndex + 1) % images.length;
  }

  changeBackground();
  setInterval(changeBackground, 1500);
</script>

<!-- Popular Dishes -->
<div class="container my-5">
  <h2 class="text-center mb-4">Popular Dishes</h2>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card h-100 shadow">
        <img src="6.jpg" class="card-img-top" alt="Chicken Biryani">
        <div class="card-body text-center">
          <h5 class="card-title">Chicken Biryani</h5>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 shadow">
        <img src="5.jpg" class="card-img-top" alt="Cheese Kottu">
        <div class="card-body text-center">
          <h5 class="card-title">Cheese Kottu</h5>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 shadow">
        <img src="9.jpg" class="card-img-top" alt="Nasi Goreng">
        <div class="card-body text-center">
          <h5 class="card-title">Nasi Goreng</h5>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Menu Section (Dynamic) -->
<?php
$conn = new mysqli("localhost", "root", "", "restaurant");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM menu_items ORDER BY category, id DESC");
$menu = [];

function normalizeCategory($category) {
    $map = [
        'main course' => 'Main Courses', 'main courses' => 'Main Courses',
        'snack' => 'Snacks', 'snacks' => 'Snacks',
        'beverage' => 'Beverages', 'beverages' => 'Beverages',
        'dessert' => 'Desserts', 'desserts' => 'Desserts'
    ];
    $lower = strtolower(trim($category));
    return $map[$lower] ?? ucwords($lower);
}

while ($row = $result->fetch_assoc()) {
    $normalizedCategory = normalizeCategory($row['category']);
    $menu[$normalizedCategory][] = $row;
}

if (!empty($menu)) {
    echo '<div class="container my-5">';
    echo '<h2 class="text-center mb-5 fw-bold">Our Menu</h2>';

    $categoryOrder = ['Main Courses', 'Snacks', 'Desserts', 'Beverages'];
    $categoryColors = [
        'Main Courses' => 'text-warning',
        'Snacks' => 'text-danger',
        'Desserts' => 'text-info',
        'Beverages' => 'text-success'
    ];

    foreach ($categoryOrder as $category) {
        if (isset($menu[$category])) {
            echo '<h3 class="mb-4 fw-semibold ' . $categoryColors[$category] . '">' . htmlspecialchars($category) . '</h3>';
            echo '<div class="row g-4 mb-5">';
            foreach ($menu[$category] as $item) {
                echo '<div class="col-md-4">';
                echo '<div class="card menu-card h-100 shadow-lg border-0">';
                echo '<img src="' . htmlspecialchars($item['image']) . '" class="card-img-top" alt="' . htmlspecialchars($item['item_name']) . '">';
                echo '<div class="card-body d-flex flex-column">';
                echo '<h5 class="card-title">' . htmlspecialchars($item['item_name']) . '</h5>';
                echo '<p class="card-text text-success fw-bold">Rs. ' . number_format($item['price'], 2) . '</p>';
                echo '<a href="login.php" class="btn order-btn mt-auto w-100 text-center">Order Now</a>';
                echo '</div></div></div>';
            }
            echo '</div>';
        }
    }

    echo '</div>';
} else {
    echo '<p class="text-center mt-5">No menu items found.</p>';
}

$conn->close();
?>
</body>
</html>


<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom Styles for Button & Animation -->
<style>
    .order-btn {
        background-color: #a3f7b5; /* Light green */
        color: #000;
        font-weight: 500;
        transition: all 0.3s ease-in-out;
        border: none;
    }

    .order-btn:hover {
        background-color: #7edc9e; /* Slightly darker green */
        transform: scale(1.03);
    }

    .menu-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    background-color: #fff;
}

.menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.18);
}

.card-img-top {
    border-radius: 12px 12px 0 0;
    transition: transform 0.4s ease;
    box-shadow: inset 0 0 20px rgba(0,0,0,0.05);
    display: block;
    width: 100%;
    height: 180px;
    object-fit: cover;
    position: relative;
    z-index: 1;
}

/* Add a subtle dark overlay on hover */
.menu-card:hover .card-img-top::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.15);
    border-radius: 12px 12px 0 0;
    z-index: 2;
    pointer-events: none;
}

/* Slight scale effect on image on hover */
.menu-card:hover .card-img-top {
    transform: scale(1.07);
    box-shadow: inset 0 0 30px rgba(0,0,0,0.1);
    z-index: 1;
}


</style>

               
</div>
        </div>
    </div>
</div>
    
</div>
<!-- About Us Section -->
<div class="container about-us my-5 py-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2>About Our Restaurnt</h2>
            <p>
                Welcome to Gagul Restaurant! We serve delicious, authentic dishes made with fresh ingredients 
                and love. Our passion is to provide you with an unforgettable dining experience. 
                Whether you're craving traditional favorites or new flavors, our menu has something for everyone.
            </p>
            <p>
                Visit us to taste the magic and enjoy our warm hospitality.
            </p>
        </div>
        <div class="col-md-6 text-center">
            <img src="1.jpg" alt="About Gagul Restaurant" class="img-fluid rounded shadow" />
        </div>
    </div>
</div>



<footer class="text-center py-4" style="background-color: #f1f1f1;">
    <p>&copy; 2025 Gagul Restaurant. All rights reserved.</p>
</footer>


           
            </div>
        </div>
    </div>
</footer>


</body>
</html>
