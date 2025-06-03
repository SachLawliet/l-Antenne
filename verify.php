<?php
require '../config/db_connection.php';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get token from URL
$token = $_GET['token'];

// Update the user's verification status
$sql = "UPDATE users SET is_verified = 1 WHERE token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['success_message'] = "Account created successfully! Please verify your email before logging in.";
    header("Location: login.php");
} else {
    echo "Invalid or expired token.";
}

$stmt->close();
$conn->close();
?>
