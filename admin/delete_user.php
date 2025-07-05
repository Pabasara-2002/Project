<?php
// Start the session
session_start();

// Include the database connection file
include('../db.php');

// Check if 'id' is set in the URL
if (isset($_GET['id'])) {
    // Get the ID from the URL
    $id = $_GET['id'];

    // Prepare and execute the DELETE query to remove the user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
}

// Redirect back to the 'manage_users.php' page after deletion
header("Location: manage_users.php");
exit();
?>
