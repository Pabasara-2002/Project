<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "restaurant"; // <-- Your database name

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$pdo = new PDO('mysql:host=localhost;dbname=restaurant', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
