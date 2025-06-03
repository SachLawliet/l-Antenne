<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid CSRF token');
}

require '../config/db_connection.php';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the media_id from POST (since we are using a form)
if (!isset($_POST['media_id']) || !is_numeric($_POST['media_id'])) {
    header("Location: account.php");
    exit();
}
$mediaId = (int)$_POST['media_id'];

// Get the user’s ID from the session user
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['user']);
$stmt->execute();
$stmt->bind_result($currentUserId);
$stmt->fetch();
$stmt->close();

// Check if the media belongs to this user
$stmt = $conn->prepare("SELECT media_path FROM user_media WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $mediaId, $currentUserId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    // Media not found or not belonging to the user
    $stmt->close();
    $conn->close();
    header("Location: account.php");
    exit();
}
$row = $result->fetch_assoc();
$mediaPath = $row['media_path'];
$stmt->close();

// Delete the media record
$stmt = $conn->prepare("DELETE FROM user_media WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $mediaId, $currentUserId);
$stmt->execute();
$stmt->close();

// Optionally remove the actual file
if (file_exists($mediaPath)) {
    unlink($mediaPath);
}

$conn->close();
header("Location: account.php");
exit();
