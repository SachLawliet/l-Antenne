<?php
session_start();

// 1) Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// 2) Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid CSRF token');
}

require '../config/db_connection.php';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3) Get and validate folder_id
if (!isset($_POST['folder_id']) || !is_numeric($_POST['folder_id'])) {
    header("Location: account.php");
    exit();
}
$folderId = (int)$_POST['folder_id'];

// 4) Lookup current user's ID
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['user']);
$stmt->execute();
$stmt->bind_result($currentUserId);
$stmt->fetch();
$stmt->close();

// 5) Verify folder belongs to user
$stmt = $conn->prepare("
    SELECT id 
      FROM user_media 
     WHERE id = ? 
       AND user_id = ? 
       AND is_folder = 1
");
$stmt->bind_param("ii", $folderId, $currentUserId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    // Folder not found or not owned by user
    $stmt->close();
    $conn->close();
    header("Location: account.php");
    exit();
}
$stmt->close();

// 6) Begin transaction
$conn->begin_transaction();

try {
    // 7) Fetch all child media (files) in this folder
    $stmt = $conn->prepare("
        SELECT media_path 
          FROM user_media 
         WHERE folder_id = ? 
           AND is_folder = 0
    ");
    $stmt->bind_param("i", $folderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $filesToDelete = [];
    while ($row = $result->fetch_assoc()) {
        $filesToDelete[] = $row['media_path'];
    }
    $stmt->close();

    // 8) Delete child records
    $stmt = $conn->prepare("
        DELETE 
          FROM user_media 
         WHERE folder_id = ? 
           AND is_folder = 0
    ");
    $stmt->bind_param("i", $folderId);
    $stmt->execute();
    $stmt->close();

    // 9) Delete the folder record itself
    $stmt = $conn->prepare("
        DELETE 
          FROM user_media 
         WHERE id = ? 
           AND is_folder = 1
    ");
    $stmt->bind_param("i", $folderId);
    $stmt->execute();
    $stmt->close();

    // 10) Commit DB changes
    $conn->commit();

    // 11) Remove files from disk
    foreach ($filesToDelete as $path) {
        if (is_file($path)) {
            @unlink($path);
        }
    }

} catch (Exception $e) {
    // Roll back on error
    $conn->rollback();
    // In production, log $e->getMessage() somewhere safe
    die("An error occurred while deleting the folder.");
}

$conn->close();

// 12) Redirect back
header("Location: account.php");
exit();
?>
