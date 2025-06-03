<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>l'Antenne</title>
  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="pics/antenne.ico" type="image/x-icon">

</head>
<body>
  <div class="hamburger-menu" id="hamburgerMenu">
    ☰ Menu
  </div>

  <div class="content">
    <div class="antenne-container">
      <img src="pics/antenne.webp" alt="Central Antenne" class="central-antenne" id="centralAntenne">
    </div>
    <?php include 'menu.php'; ?>
  </div>

  <script src="script.js"></script>
</body>
</html>
