<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

require '../config/db_connection.php';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get verified users ordered by most‐recent post
$sql = "
    SELECT 
      u.username,
      MAX(um.created_at) AS last_post
    FROM users u
    INNER JOIN user_media um 
      ON u.id = um.user_id
    WHERE u.is_verified = 1
    GROUP BY u.id, u.username
    ORDER BY last_post DESC
";
$result = $conn->query($sql);

$verifiedUsers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $verifiedUsers[] = $row['username'];
    }
}


// Fetch media (individual files and folders) for a selected user
$selectedUser = $_GET['user'] ?? null;
$userMedia = [];

if ($selectedUser) {
    // Get the user ID for the selected username
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $selectedUser);
    $stmt->execute();
    $stmt->bind_result($selectedUserId);
    $stmt->fetch();
    $stmt->close();

    if (!empty($selectedUserId)) {
        // Fetch both individual files (folder_id IS NULL) and folders (is_folder = 1)
        $stmt = $conn->prepare("
            SELECT id, media_path, media_type, media_title, created_at, is_folder 
            FROM user_media 
            WHERE user_id = ? 
              AND ((is_folder = 0 AND folder_id IS NULL) OR is_folder = 1)
            ORDER BY created_at DESC
        ");
        $stmt->bind_param("i", $selectedUserId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $userMedia[] = $row;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bluehand</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="/assets/bluehand.css">
  <link rel="icon" href="pics/antenne.ico" type="image/x-icon">
  <style>

  </style>
</head>
<body>
    <!-- Hamburger Menu for Mobile -->
    <div class="hamburger-menu" id="hamburgerMenu">
        ☰ Menu
    </div>
    <!-- Main Navigation Menu -->
    <?php include 'menu.php'; ?>

    <div class="page-content">
        <!-- Header Section -->
        <header class="page-header">
            <h1 class="glitch">Bluehand</h1>
        </header>

        <?php if ($selectedUser): ?>
            <section class="media-grid">
                <h2><?php echo htmlspecialchars($selectedUser); ?>'s antennas:</h2>
                <?php foreach ($userMedia as $mediaItem): ?>
                    <?php if ($mediaItem['is_folder'] == 1): ?>
                        <?php
                            // Query up to 9 files contained in the folder
                            $stmtFolder = $conn->prepare("SELECT media_path FROM user_media WHERE folder_id = ? AND is_folder = 0 LIMIT 9");
                            $stmtFolder->bind_param("i", $mediaItem['id']);
                            $stmtFolder->execute();
                            $resultFolder = $stmtFolder->get_result();
                            $folderFiles = [];
                            while($folderRow = $resultFolder->fetch_assoc()){
                                $folderFiles[] = $folderRow['media_path'];
                            }
                            $stmtFolder->close();
                        ?>
                            <div class="folder-item" 
                                 data-media='<?php echo json_encode($folderFiles); ?>'
                                 data-title="<?php echo htmlspecialchars($mediaItem['media_title']); ?>"
                                 data-info="By <?php echo htmlspecialchars($selectedUser); ?> on <?php echo htmlspecialchars($mediaItem['created_at']); ?>">

                            <img src="pics/dossier.png">
                            <div class="folder-title"><?php echo htmlspecialchars($mediaItem['media_title']); ?></div>
                        </div>
                    <?php else: ?>
                        <div class="grid-item">
                            <?php if ($mediaItem['media_type'] === 'image'): ?>
                                <img
                                    data-src="<?php echo htmlspecialchars($mediaItem['media_path']); ?>"
                                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                    alt="<?php echo htmlspecialchars($mediaItem['media_title']); ?>"
                                    data-title="<?php echo htmlspecialchars($mediaItem['media_title']); ?>"
                                    data-created_at="<?php echo htmlspecialchars($mediaItem['created_at']); ?>"
                                    data-username="<?php echo htmlspecialchars($selectedUser); ?>"
                                    class="lazy"
                                />
                            <?php elseif ($mediaItem['media_type'] === 'video'): ?>
                                <video controls preload="none"
                                       data-title="<?php echo htmlspecialchars($mediaItem['media_title']); ?>"
                                       data-created_at="<?php echo htmlspecialchars($mediaItem['created_at']); ?>"
                                       data-username="<?php echo htmlspecialchars($selectedUser); ?>"
                                >
                                    <source src="<?php echo htmlspecialchars($mediaItem['media_path']); ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            <?php elseif ($mediaItem['media_type'] === 'audio'): ?>
                                <audio controls preload="none">
                                  <source 
                                    src="<?=htmlspecialchars($mediaItem['media_path'])?>" 
                                    type="<?=mime_content_type($mediaItem['media_path'])?>">
                                  Your device does not support this audio format.
                                </audio>
                            <?php elseif ($mediaItem['media_type'] === 'pdf'): ?>
                                <embed
                                    data-src="<?php echo htmlspecialchars($mediaItem['media_path']); ?>"
                                    type="application/pdf"
                                    class="lazy-embed"
                                    style="width:100%; height:200px;"
                                />
                                <a href="<?=htmlspecialchars($mediaItem['media_path'])?>" target="_blank" style="color:yellow;">Viewwww</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <p>Go on, feel the waves of your favourite Antenneux :)</p>
        <?php endif; ?>

        <!-- Verified Users Links -->
        <section class="user-links">
            <h2>Antenneux's Waves</h2>
            <?php foreach ($verifiedUsers as $user): ?>
                <a href="?user=<?php echo htmlspecialchars($user); ?>">
                    <?php echo htmlspecialchars($user); ?>
                </a>
            <?php endforeach; ?>
        </section>

        <!-- Fullscreen Overlay (hidden by default) -->
        <div id="fullscreenOverlay"></div>

        <!-- Footer -->
        <footer>
            <a href="index.php">Back to Home</a>
        </footer>
    </div>

    <script src="script.js"></script>
    <script src="/assets/bluehand.js"></script>

</body>
</html>
