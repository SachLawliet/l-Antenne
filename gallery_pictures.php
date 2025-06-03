<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
        <body style="
  background: url('pics/GALERIEN.png') no-repeat;  /* pas de répétition */
  background-position: center center;                  /* au centre */
  background-size: 115% 133%;
">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JPG Gallery</title>
  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="pics/antenne.ico" type="image/x-icon">
  <style>
    /* Loader overlay with GIF preloader */
    #loader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: #000 url('pics/loader.gif') no-repeat center center;
      z-index: 99999;
    }
    /* Grid container for an 8x8 layout */
    .image-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr); /* 6 columns */
      grid-template-rows: repeat(4, 1fr);    /* 6 rows */
      width: 90vw;
      height: 90vh;
      margin: 0; /* Remove default margin */
      padding: 20px; /* Remove default padding */
      gap: 10px;
    }
    /* Each grid item styling */
    .grid-item {
      width: 100%;
      height: 100%;
      overflow: hidden; /* Important for object-fit */
    }
    .grid-item img {
      width: 100%;
      height: 100%;
      object-fit: cover; /* Make image cover the item, cropping if needed */
      display: block; /* Removes extra space below image */
      border-radius: 5px;
      cursor: pointer;
      transition: transform 0.2s;
    }
    .grid-item img:hover {
      transform: scale(1.03); /* Slightly reduced scale for smaller grid items */
    }

    /* Fullscreen overlay container (hidden by default) */
    #fullscreenOverlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.8);
      z-index: 9999;
      display: none;
      align-items: center;
      justify-content: center;
    }
    /* When overlay is active */
    #fullscreenOverlay.show {
      display: flex;
    }
    /* Fullscreen image styling */
    #fullscreenOverlay img {
      max-width: 90vw;
      max-height: 90vh;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }
  </style>
</head>
<body>
  <!-- Loader overlay displayed until all images are loaded -->
  <div id="loader"></div>

  <header>
    <!-- Hamburger Menu -->
    <div class="hamburger-menu" id="hamburgerMenu">
      ☰ Menu
    </div>
    <?php include 'menu.php'; ?>
  </header>
  
  <!-- 8x8 Image Grid -->
  <section class="image-grid">
    <?php
    // Loop to generate 64 grid items (adjust image paths as needed)
    for ($i = 1; $i <= 16; $i++) {
        $imgPath = "pics/gallery/img{$i}.webp";
        echo '<div class="grid-item">';
        echo '<img class="lazy-gallery-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="' . htmlspecialchars($imgPath) . '" alt=":' . $i . '" data-full="' . htmlspecialchars($imgPath) . '">';
        echo '</div>';
    }
    ?>
  </section>
  
  <!-- Fullscreen overlay (hidden until an image is clicked) -->
  <div id="fullscreenOverlay"></div>
  
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const lazyGalleryImages = Array.from(document.querySelectorAll('img.lazy-gallery-image'));

        if ('IntersectionObserver' in window) {
            let imageObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        let img = entry.target;
                        const dataSrc = img.dataset.src;
                        if (dataSrc) {
                            img.src = dataSrc;
                        }
                        img.classList.remove('lazy-gallery-image');
                        observer.unobserve(img);
                    }
                });
            }, { rootMargin: "0px 0px 200px 0px" }); // Start loading images 200px before they enter viewport

            lazyGalleryImages.forEach(function (img) {
                imageObserver.observe(img);
            });
        } else {
            // Fallback for browsers that don't support IntersectionObserver
            lazyGalleryImages.forEach(function (img) {
                const dataSrc = img.dataset.src;
                if (dataSrc) {
                    img.src = dataSrc;
                }
                img.classList.remove('lazy-gallery-image');
            });
        }
    });


    // Hide loader when all assets have loaded
    window.addEventListener('load', function() {
      document.getElementById('loader').style.display = 'none';
    });
    
    // Fullscreen overlay functionality
    const overlay = document.getElementById('fullscreenOverlay');
    const gridImages = document.querySelectorAll('.grid-item img');
    
    gridImages.forEach(img => {
      img.addEventListener('click', () => {
        const fullImg = document.createElement('img');
        fullImg.src = img.getAttribute('data-full');
        overlay.innerHTML = '';  // Clear previous content
        overlay.appendChild(fullImg);
        overlay.classList.add('show');
      });
    });
    
    // Hide overlay on click
    overlay.addEventListener('click', () => {
      overlay.classList.remove('show');
    });
  </script>
  <script src="script.js"></script>
</body>
</html>