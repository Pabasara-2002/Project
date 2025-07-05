<?php
session_start();
include('../db.php'); // root folder eken db connection ekak daganna

$error = '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND role = 'manager' LIMIT 1");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id();
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard.php'); // manager dashboard
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manager Login</title>
    <link rel="stylesheet" href="../styles.css"> <!-- External CSS if any -->
</head>
<body>
    <div class="login-container">
        <h2>Manager Login</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="off">

            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
