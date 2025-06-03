<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="pics/antenne.ico" type="image/x-icon">
</head>
<body class="gallery_body">
    <header>
 <!-- Move the hamburger menu and the menu inside the header -->
            <div class="hamburger-menu" id="hamburgerMenu">
                ☰ Menu
            </div>
            <?php include 'menu.php'; ?>
            <h1 class="glitch galleryh1">Gallery</h1>

    </header>
    <div class="gallery-navigation">
        <a href="gallery_pictures.php">
          <img src="pics/IMG.jpg" alt="Pictures" class="nav-image">
        </a>
        <a href="gallery_videos.php">
          <img src="pics/VUD.jpg" alt="Videos" class="nav-image">
        </a>
        <a href="gallery_audio.php">
          <img src="pics/SONC.jpg" alt="Audio" class="nav-image">
        </a>
    </div>
    <script src="script.js"></script>
</body>
</html>
