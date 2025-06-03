<?php
session_start();
require '../config/db_connection.php'; // Centralized database connection


// Ensure only the moderator ("sex user") can access this page
if (!isset($_SESSION['user']) || !in_array($_SESSION['user'], ['sac', 'LHERMITE'])) {
    header("Location: login.php");
  exit();
}

//header("Location: login.php");

// Generate a CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch all media for moderation
$stmt = $pdo->prepare("SELECT id, media_path, media_type, user_id, created_at FROM user_media ORDER BY created_at DESC");
$stmt->execute();
$media = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderate Content</title>
    <link rel="stylesheet" href="style.css">
    <style>
                /* General layout for the moderator page */
        .modo-page {
            display: grid;
            grid-template-columns: 25% 50% 25%;
            gap: 10px;
            padding: 20px;
            height: 100vh;
            background-color: #f5f5f5;
            color: #fff;
            font-family: Arial, sans-serif;
        }
        
        .left-panel, .middle-panel, .right-panel {
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        /* Grid layout for displaying media */
        .media-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr); /* Three items per row */
            gap: 10px; /* Spacing between grid items */
        }
        
        /* Individual media grid item */
        .grid-item {
            position: relative;
            background-color: #222;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        /* Media elements (image, video, PDF) */
        .grid-item img, 
        .grid-item video, 
        .grid-item embed {
            width: 100%;
            height: 100%; /* Allow full height of the container */
            object-fit: contain; /* Keeps the entire image visible within the container */
            background-color: #000;
        }

        
        /* Delete button for each grid item */
        .grid-item .delete-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: yellow; /* Bright yellow for visibility */
            color: #000; /* Black text */
            border: none;
            padding: 5px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: bold;
            opacity: 0.8; /* Slight transparency */
            transition: background-color 0.3s ease, opacity 0.3s ease; /* Smooth transitions */
        }
        
        /* Delete button hover effect */
        .grid-item .delete-btn:hover {
            background-color: rgba(255, 0, 0, 1); /* Red on hover */
            color: #fff; /* White text */
            opacity: 1; /* Fully opaque */
        }
        
        /* Action button styling (e.g., for moderation actions) */
        .action-btn {
            background-color: yellow; /* Bright yellow button */
            color: black; /* Black text */
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            border-radius: 10px; /* Rounded corners */
            transition: background-color 0.3s ease; /* Smooth hover transition */
        }
        
        /* Action button hover effect */
        .action-btn:hover {
            background-color: orange; /* Orange on hover */
        }
        
        /* Success and error message styling */
        .success {
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .media-grid {
                grid-template-columns: repeat(2, 1fr); /* 2 columns for smaller screens */
            }
        }
        

    </style>
</head>
<body>
    <div class="middle-panel">
        <h2>Moderate Uploaded Content</h2>
        <?php if (isset($_GET['message'])): ?>
            <p class="success"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <div class="media-grid">
            <?php foreach ($media as $item): ?>
                <div class="grid-item">
                    <?php if ($item['media_type'] === 'image'): ?>
                        <img src="<?php echo htmlspecialchars($item['media_path']); ?>" alt="Media">
                    <?php elseif ($item['media_type'] === 'video'): ?>
                        <video controls>
                            <source src="<?php echo htmlspecialchars($item['media_path']); ?>" type="video/mp4">
                        </video>
                    <?php elseif ($item['media_type'] === 'pdf'): ?>
                        <embed src="<?php echo htmlspecialchars($item['media_path']); ?>" type="application/pdf">
                    <?php endif; ?>
                    
                    <!-- Delete Button -->
                    <form action="deletemedia.php" method="POST">
                        <input type="hidden" name="media_id" value="<?php echo $item['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" class="delete-btn">✖</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
