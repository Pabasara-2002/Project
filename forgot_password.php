<?php
include('db.php');
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch();

    if ($user) {
        // Generate a unique reset token
        $token = bin2hex(random_bytes(50));

        // Set the expiration time for the token (e.g., 1 hour)
        $expires = date("U") + 3600; // Token expires in 1 hour

        // Store the token in the database with expiration time
        $updateStmt = $conn->prepare("UPDATE users SET reset_token = :token, token_expires = :expires WHERE email = :email");
        $updateStmt->bindParam(':token', $token);
        $updateStmt->bindParam(':expires', $expires);
        $updateStmt->bindParam(':email', $email);
        $updateStmt->execute();

        // Send the reset email
        $resetLink = "http://yourdomain.com/reset_password.php?email=" . urlencode($email) . "&token=" . $token;
        $subject = "Password Reset Request";
        $message = "Click the following link to reset your password: " . $resetLink;
        $headers = "From: no-reply@yourdomain.com";

        if (mail($email, $subject, $message, $headers)) {
            header("Location: reset_link_sent.php"); // Redirect to a confirmation page
            exit();
        } else {
            $error = "Failed to send the reset email!";
        }
    } else {
        $error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md border-t-4 border-blue-500">
    <h2 class="text-2xl font-bold text-center text-gray-700 mb-4">🔒 Forgot Your Password?</h2>

    <?php if ($error): ?>
        <p class="text-red-600 text-center mb-3"><?= $error ?></p>
    <?php endif; ?>

    <form method="post" class="space-y-4">
        <input type="email" name="email" placeholder="Enter your email"
               class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        <button type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-2 rounded hover:bg-blue-700 transition">
            Send Reset Link
        </button>
    </form>
    <p class="text-center mt-4">
        <a href="index.php" class="text-blue-600 hover:underline">Back to Login</a>
    </p>

</div>

</body>
</html>
