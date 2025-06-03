<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Random 3 Images</title>
    <style>
        /* Reset body & html to fill the viewport height */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        /* Container that spans the full viewport height */
        .full-height-container {
            display: flex;
            flex-direction: row;
            
            width: 100%;
            height: 100vh; /* Occupies the entire browser height */
            overflow: hidden; /* Hide any overflow */
        }
        /* Each image takes a third of the width and full height */
        .random-image {
            width: 33.3333vw;
            height: 100%;
            object-fit: cover; /* Scale the image to fill its container */
        }
    </style>
</head>
<body>
    <?php
    // 1. Grab all images from "pics" directory with certain extensions
    $images = glob('pics/*.{jpg,jpeg,png,webp}', GLOB_BRACE);

    // 2. Shuffle them to get a random order
    shuffle($images);

    // 3. Take only the first 3 images
    $randomImages = array_slice($images, 0, 3);

    // 4. Output them side by side in a full-height container
    echo '<div class="full-height-container">';
    foreach ($randomImages as $img) {
        echo '<img src="' . $img . '" class="random-image" alt="Random Image">';
    }
    echo '</div>';
    ?>
</body>
</html>
