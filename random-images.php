<?php
// 1. Grab all images from the "pics" directory with certain extensions.
$images = glob('randump/*.{jpg,jpeg,png,webp}', GLOB_BRACE);

// 2. Shuffle the array to randomize the order
shuffle($images);

// 3. Take the first 3 images from the shuffled list
$randomImages = array_slice($images, 0, 3);

foreach ($randomImages as $img) {
    // Wrap each image with a link
    echo '<a href="bluehand.php" style="margin: 0 10px;">';
    echo '<img src="' . $img . '" alt="Random Antenne"';
    echo '</a>';
}

echo '</div>';
