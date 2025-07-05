<?php
session_start();
include('db.php'); // Assumes $conn as PDO

$error = '';

if (isset($_POST['login'])) {
    $username = $_POST['user_field'] ?? '';
    $password = $_POST['pass_field'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Please enter username and password.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'disabled') {
                $error = "Your account has been disabled. Please contact the administrator.";
            } else {
                session_regenerate_id(true);
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                switch ($user['role']) {
                    case 'admin':
                        header('Location: admin/admin_dashboard.php'); exit;
                    case 'manager':
                        header('Location: manager/dashboard.php'); exit;
                    case 'stock':
                        header('Location: stock/dashboard.php'); exit;
                    case 'supplier':
                        header('Location: supplier/dashboard.php'); exit;
                    case 'cashier':
                        header('Location: cashier/dashboard.php'); exit;
                    default:
                        $error = "Unknown user role!";
                }
            }
        } else {
            $error = "Invalid username or password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Restaurant Stock Control System</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Inside your <head> -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">


    <style>
        body {
            background: url('images11.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
        }
        .login-wrapper {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 360px;
        }
        .logo-circle {
            background: #2563eb;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 36px;
            color: white;
            user-select: none;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.5);
        }
        .login-title {
            color: #1e3a8a;
            font-weight: 700;
            text-align: center;
            margin-bottom: 5px;
        }
        .login-subtitle {
            color: #4b5563;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 500;
        }
        .btn-primary {
            background-color: #2563eb;
            border: none;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #1e40af;
        }
        .error-message {
            font-size: 14px;
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        a.forgot-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #2563eb;
            text-decoration: none;
        }
        a.forgot-link:hover {
            text-decoration: underline;
        }
        .form-control-compact {
    font-size: 12px;
    padding: 6px 10px;
    height: 40px;
    border-radius: 6px;
}
.input-group-text-compact {
    font-size: 12px;
    padding: 6px 10px;
    height: 40px;
    border-radius: 6px 0 0 6px;
}

    /* Smaller input font and padding */
   


    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="logo-circle">🍽️</div>
        <h2 class="login-title">Login</h2>
        <p class="login-subtitle">Restaurant Stock Control System</p>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off" novalidate>
    <div class="mb-3 input-group">
        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
        <input type="text" name="user_field" class="form-control form-control-lg" placeholder="Username" required autofocus autocomplete="off" />
    </div>
    <div class="mb-4 input-group">
        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
        <input type="password" name="pass_field" class="form-control form-control-lg" placeholder="Password" required autocomplete="new-password" />
    </div>
    <button type="submit" name="login" class="btn btn-primary btn-lg w-100">Login</button>
</form>


        <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
    </div>
</div>

<!-- Bootstrap JS Bundle (optional if you want interactive features) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
