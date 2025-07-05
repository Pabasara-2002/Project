<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim inputs to remove extra spaces
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Check if both fields are filled
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, password, name FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $password_hash, $name);
            $stmt->fetch();
            if (password_verify($password, $password_hash)) {
                $_SESSION['customer_id'] = $id;
                $_SESSION['customer_name'] = $name;
                header("Location:view_menu.php"); // Redirect to order page after login
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No user found with this email.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Customer Login - Gagul Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: url('images9.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            position: relative;
        }

        .login-card h2 {
            margin-bottom: 1.8rem;
            color: #0011ff;
            font-weight: 800;
            text-align: center;
        }

        .form-label {
            font-weight: 600;
            color: #333;
        }

        .form-control:focus {
            border-color: #0011ff;
            box-shadow: 0 0 6px rgba(0, 17, 255, 0.4);
        }

        .btn-login {
            background: linear-gradient(135deg, #0011ff, #3f86ff);
            border: none;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #0030ff, #599eff);
            transform: scale(1.02);
        }

        .error-msg {
            color: #d93025;
            text-align: center;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        @media (max-width: 576px) {
            .login-card {
                margin: 1rem;
                padding: 1.5rem;
            }
        }

        .top-nav-outside {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 999;
        }

        .home-button {
            background-color: white;
            color: #0011ff;
            padding: 6px 16px;
            text-decoration: none;
            border: 2px solid #0011ff;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            margin-left: 8px;
            transition: all 0.3s ease;
        }

        .home-button:hover {
            background-color: #0011ff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Customer Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off" novalidate>
            <input type="text" name="fakeusernameremembered" style="display:none;">
            <input type="password" name="fakepasswordremembered" style="display:none;">
            
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    autocomplete="off"
                    required
                />
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="new-password"
                    required
                />
            </div>
            <button type="submit" class="btn btn-login w-100 py-2">Login</button>
        </form>

        <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>

    <div class="top-nav-outside">
        <a href="index.php" class="home-button">Home</a>
        <a href="login.php" class="home-button">Order now</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
