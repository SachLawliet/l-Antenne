<?php
session_start();
require '../config/db_connection.php'; // Centralized database connection

// Ensure the user is logged in and is the moderator
if (!isset($_SESSION['user']) || !in_array($_SESSION['user'], ['sac', 'LHERMITE'])) {
    header("Location: login.php");
  exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid CSRF token');
}

// Retrieve the media_id from POST
if (!isset($_POST['media_id']) || !is_numeric($_POST['media_id'])) {
    header("Location: modo.php");
    exit();
}
$mediaId = (int)$_POST['media_id'];

// Fetch the media information
$stmt = $pdo->prepare("SELECT media_path, user_id FROM user_media WHERE id = ?");
$stmt->execute([$mediaId]);
$media = $stmt->fetch();

if (!$media) {
    // Media not found
    header("Location: modo.php?error=Media not found");
    exit();
}

// Delete the media record from the database
$stmt = $pdo->prepare("DELETE FROM user_media WHERE id = ?");
$stmt->execute([$mediaId]);

// Optionally remove the actual file
$mediaPath = $media['media_path'];
if (file_exists($mediaPath)) {
    unlink($mediaPath);
}

// Log the deletion action (optional)
$stmt = $pdo->prepare("INSERT INTO moderation_logs (moderator, media_id, action) VALUES (?, ?, ?)");
$stmt->execute([$_SESSION['user'], $mediaId, 'delete']);

// Redirect back to the moderator page with a success message
header("Location: modo.php?message=Media deleted successfully");
exit();
