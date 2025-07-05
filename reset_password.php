<?php
include('db.php');
$error = "";

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    // Check if the token is valid and not expired
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email AND reset_token = :token AND token_expires > :current_time");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':token', $token);
    $stmt->bindParam(':current_time', time());
    $stmt->execute();

    $user = $stmt->fetch();

    if ($user) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_password = $_POST['password'];
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the user's password and reset the token
            $updateStmt = $conn->prepare("UPDATE users SET password = :password, reset_token = NULL, token_expires = NULL WHERE email = :email");
            $updateStmt->bindParam(':password', $hashed_password);
            $updateStmt->bindParam(':email', $email);
            $updateStmt->execute();

            header("Location: login.php"); // Redirect to login page
            exit();
        }
    } else {
        $error = "Invalid or expired token.";
    }
} else {
    $error = "Invalid request.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md border-t-4 border-blue-500">
    <h2 class="text-2xl font-bold text-center text-gray-700 mb-4">🔑 Reset Your Password</h2>

    <?php if ($error): ?>
        <p class="text-red-600 text-center mb-3"><?= $error ?></p>
    <?php endif; ?>

    <form method="post" class="space-y-4">
        <input type="password" name="password" placeholder="Enter new password"
               class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        <button type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-2 rounded hover:bg-blue-700 transition">
            Reset Password
        </button>
    </form>

</div>

</body>
</html>
