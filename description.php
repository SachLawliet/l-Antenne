<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Description</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="pics/antenne.ico" type="image/x-icon">
    <style>
        /* Add the scrollable styles here */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        .responsive-images {
        display: flex;
        height:100vh;
        flex-wrap: wrap;
        gap: 10px; /* Space between images */
        justify-content: center;
        }

        .responsive-images img {
            max-width: 100%;      /* Ensure images don't exceed the container width */
            height: auto;         /* Maintain aspect ratio */
            object-fit: contain;  /* Ensure the entire image is visible */
        }
    </style>
</head>
<body class="desc">
    <div class="hamburger-menu" id="hamburgerMenu">
        ☰ Menu
    </div>
    <?php include 'menu.php'; ?>

    <div class="responsive-images">
        <img src="pics/first.webp" alt="First Image">
        <img src="pics/second.webp" alt="Second Image">
        <img src="pics/third.webp" alt="Third Image">
        <img src="pics/3/Description 111.webp" alt="111">
        <img src='pics/lantennesenvole.png' alt='jtm'>
    </div>
    <script src="script.js"></script>
</body>
</html>
