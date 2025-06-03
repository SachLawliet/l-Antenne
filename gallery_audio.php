<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WAV Gallery</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="pics/antenne.ico" type="image/x-icon">
</head>

<style>
    
    body {
      background: url('pics/2allery.jpg') no-repeat center center;
      min-height: 100vh;
    }
    
    /* Mobile: two-layer background */
    @media only screen and (max-width: 768px) {
      body {
        background-image:
          url('pics/mauxzaik.jpg');
        background-repeat: no-repeat;
        height: 300vh;
      }
    }
</style>

<body>
    <header>
 <!-- Move the hamburger menu and the menu inside the header -->
            <div class="hamburger-menu" id="hamburgerMenu">
                ☰ Menu
            </div>
            <?php include 'menu.php'; ?>
    </header>
    <section id="wavSection" class="audio-section">
        <div class="audio-container">
          <!-- Left side: Big audio icon, description, and playlist -->
          <div class="left-section">
            <div class="large-audio-container">
              <img id="audio-icon" src="pics/big_wav.webp" alt="Big Audio Icon">
              <h1 id="audio-description" class="glitch">OUVREZ LES OREILLES</h1>
            </div>
            <div class="playlist">
              <iframe src="https://open.spotify.com/embed/playlist/1mdEwsEJewftZnSu6Z0wKT?utm_source=generator" frameborder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
            </div>
          </div>
          <!-- Right side: Audio player grid -->
          <div class="audio-grid-container">
            <div class="audio-item">
              <audio controls>
                <source src="https://media.githubusercontent.com/media/SachLawliet/l-Antenne/refs/heads/main/audio/ogfortnitr.mp3" type="audio/mp3">
                Your browser does not support the audio element.
              </audio>
            </div>
            <div class="audio-item">
              <audio controls>
                <source src="https://media.githubusercontent.com/media/SachLawliet/l-Antenne/refs/heads/main/audio/goofy.wav" type="audio/wav">
                Your browser does not support the audio element.
              </audio>
            </div>
            <div class="audio-item">
              <audio controls>
                <source src="https://media.githubusercontent.com/media/SachLawliet/l-Antenne/refs/heads/main/audio/gtar.wav" type="audio/wav">
                Your browser does not support the audio element.
              </audio>
            </div>
            <div class="audio-item">
              <audio controls>
                <source src="https://media.githubusercontent.com/media/SachLawliet/l-Antenne/refs/heads/main/audio/temps_bourre.wav" type="audio/wav">
                Your browser does not support the audio element.
              </audio>
            </div>
            <div class="audio-item">
              <iframe scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/1830500022&color=%23ff5500&inverse=false&auto_play=false&show_user=true"></iframe>
            </div>
            <div class="audio-item">
              <iframe width="100%" height="300" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/527616312&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true&visual=true"></iframe>
              <div style="font-size: 10px; color: #cccccc; line-break: anywhere; word-break: normal; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; font-family: Interstate, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Garuda, Verdana, Tahoma, sans-serif; font-weight: 100;">
                <a href="https://soundcloud.com/saturnctz" title="Saturn Citizen" target="_blank" style="color: #cccccc; text-decoration: none;">Saturn Citizen</a>
                ·
                <a href="https://soundcloud.com/saturnctz/saturn-extra-freestyle1-ftvito" title="Saturn - EXTRA Freestyle#1 ft.Vito (prod.AMNEZZIA)" target="_blank" style="color: #cccccc; text-decoration: none;">Saturn - EXTRA Freestyle#1 ft.Vito (prod.AMNEZZIA)</a>
              </div>
            </div>
            <div class="audio-item">
              <iframe scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/1908941597&color=%239af66b&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true&visual=true"></iframe>
            </div>
            <div class="audio-item">
              <iframe width="100%" height="300" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/1666365459&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true&visual=true"></iframe>
              <div style="font-size: 10px; color: #cccccc; line-break: anywhere; word-break: normal; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; font-family: Interstate, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Garuda, Verdana, Tahoma, sans-serif; font-weight: 100;">
                <a href="https://soundcloud.com/myra_dj" title="𝗠𝗬𝗥𝗔" target="_blank" style="color: #cccccc; text-decoration: none;">𝗠𝗬𝗥𝗔</a>
                ·
                <a href="https://soundcloud.com/myra_dj/myra-into-the-woods-festival-beukenbos" title="Into The Woods Festival 2023" target="_blank" style="color: #cccccc; text-decoration: none;">Into The Woods Festival 2023</a>
              </div>
            </div>
          </div>
        </div>
    </section>
  <footer>
    <a href="gallery.php">Back to Gallery</a>
  </footer>
    <script src="script.js"></script>
</body>
</html>
