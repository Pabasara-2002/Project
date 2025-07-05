<?php
session_start();
include('db.php'); // Make sure this file sets up $conn as PDO object

$error = '';

if (isset($_POST['login'])) {
    $username = isset($_POST['login_user']) ? trim($_POST['login_user']) : '';
    $password = isset($_POST['login_pass']) ? $_POST['login_pass'] : '';

    if ($username === '' || $password === '') {
        $error = "Please enter username and password.";
    } else {
        // Prepare and execute
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check password and status
            if (password_verify($password, $user['password'])) {
                if ($user['status'] === 'disabled') {
                    $error = "Your account has been disabled. Please contact the administrator.";
                } else {
                    session_regenerate_id(true);
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    // Redirect based on role
                    switch ($user['role']) {
                        case 'admin':
                            header('Location: admin/dashboard.php'); exit;
                        case 'manager':
                            header('Location: manager/dashboard.php'); exit;
                        case 'cashier':
                            header('Location: cashier/dashboard.php'); exit;
                        case 'stock':
                        case 'stock_keeper':
                            header('Location: stock_keeper/dashboard.php'); exit;
                        case 'supplier':
                            header('Location: supplier/dashboard.php'); exit;
                        default:
                            $error = "Unknown user role!";
                    }
                }
            } else {
                $error = "Invalid username or password!";
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
    

    <meta charset="UTF-8">
    <title>Login - Restaurant Stock Control System</title>
    <style>
        /* Keep your styles here, or move to external CSS */
        body {
            margin: 0; padding: 0;
            background: url('images.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background: white;
            padding: 30px 25px;
            border-radius: 12px;
            border: 2px solid #93c5fd;
            width: 100%;
            max-width: 280px;
            text-align: center;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .logo {
            width: 60px;
            height: 60px;
            background: #bfdbfe;
            color: #1e3a8a;
            font-size: 30px;
            font-weight: bold;
            margin: 0 auto 15px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        h2 {
            margin: 0 0 5px;
            font-size: 22px;
            color: #1e3a8a;
        }
        p {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 20px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
        }
        button {
            width: 100%;
            background-color: #2563eb;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #1d4ed8;
        }
        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 8px;
            margin-bottom: 12px;
            border-radius: 8px;
            font-size: 12px;
        }
        a {
            display: inline-block;
            margin-top: 10px;
            font-size: 12px;
            color: #2563eb;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="logo">🍽️</div>
    <h2>Login</h2>
    <h3>Restaurant Stock Control System</h3>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <input type="text" name="login_user" placeholder="Username" required autocomplete="off">
        <input type="password" name="login_pass" placeholder="Password" required autocomplete="off">
        <button type="submit" name="login">Login</button>
    </form>

    <a href="forgot_password.php">Forgot Password?</a>
</div>

</body>
</html>
