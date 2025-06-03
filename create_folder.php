<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Ensure the request method is POST and validate CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }
} else {
    header("Location: account.php");
    exit();
}

// Retrieve and sanitize the folder name
$folderName = trim($_POST['folder_name'] ?? '');
if (empty($folderName)) {
    header("Location: account.php?error=empty_folder_name");
    exit();
}

// Include your database connection file
require '../config/db_connection.php';

// Create a new database connection
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the user ID based on the logged in username
$sql = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['user']);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

// Prepare an empty JSON array for folder_media
$emptyJson = json_encode([]);

// Insert the new folder into the user_media table with is_folder set to 1
$stmt = $conn->prepare("INSERT INTO user_media (user_id, media_title, is_folder, folder_media) VALUES (?, ?, 1, ?)");
$stmt->bind_param("iss", $userId, $folderName, $emptyJson);
$stmt->execute();
$stmt->close();

// Redirect back to the account page after creation
header("Location: account.php");
exit();
?>
