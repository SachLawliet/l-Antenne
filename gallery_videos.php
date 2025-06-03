<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MP4 Gallery</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="pics/antenne.ico" type="image/x-icon">
</head>
<body style="  background: url('pics/rou2.png') no-repeat;  /* pas de répétition */
  background-position: center center;                  /* au centre */
  background-size: 100% 100%;
">">
    <header>
 <!-- Move the hamburger menu and the menu inside the header -->
            <div class="hamburger-menu" id="hamburgerMenu">
                ☰ Menu
            </div>
            <?php include 'menu.php'; ?>
            <h1 class="glitch galleryh1">Gallery</h1>

    </header>
    <section id="mp4Section">
        <a href="https://youtu.be/XqtkN60ErhU" target="_blank">
          <img src="pics/2/fridego.webp" alt="Video 1">
        </a>
        <a href="https://youtu.be/HY8PBe6FRKU" target="_blank">
          <img src="pics/2/bag.webp" alt="Video 2">
        </a>
        <a href="https://youtu.be/E3wvXbX_JH4" target="_blank">
          <img src="pics/2/pivko.webp" alt="Video 3">
        </a>
        <a href="https://youtu.be/gmPoJPmuXJI" target="_blank">
          <img src="pics/3/leo.webp" alt="Video 4">
        </a>
        <a href="https://youtu.be/Bx8ZhmVreS4" target="_blank">
          <img src="pics/4/chat.webp" alt="Video 5">
        </a>
        <a href="https://youtu.be/zELr1e9uA8Q" target="_blank">
          <img src="pics/4/ciele.webp" alt="Video 6">
        </a>
        <a href="https://youtu.be/evSiiHKIz4I?si=UmirqiRgcUTTxxm4" target="_blank">
          <img src="pics/4/en tenne.webp" alt="Video 7">
        </a>
     </section>
    <footer>
        <a href="gallery.php">Back to Gallery</a>
    </footer>
    <script src="script.js"></script>
</body>
</html>
