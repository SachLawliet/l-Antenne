<!-- error_quota.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Limit Reached</title>
    <style>
        body {
            width: 100vw;
            height: 100vh;
            background: no-repeat #000;
            background-image: url("erreur.webp");
            background-size: contain;   /* scales the image to fill the screen */
            background-attachment: fixed;
            text-align: center;
            margin-top: 50px;

        }
        .error-container {
            display: inline-block;
            background:#222;
            opacity: 0.8;
            color: yellow;
            padding: 40px;
            border-radius: 10px;
        }
        h1 {
            color: yellow;
            margin-bottom: 20px;
        }
        a {
            color: rgb(255,255,51);
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {

            body {
                background-image: url("erreurphone.webp");
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Upload Limit Reached</h1>
        <p>You have reached the maximum limit of 6 media files. Please delete one or more files before uploading new ones.</p>
        <p><a href="../account.php">Return to Your Account</a></p>
    </div>
</body>
</html>
