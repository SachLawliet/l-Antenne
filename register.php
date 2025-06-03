<?php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../config/db_connection.php';
    


    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }



    
    // Capture form data
$user = $_POST['username'];
$email = $_POST['email'];
$pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
$token = bin2hex(random_bytes(50)); // Generate a unique token

if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Badly formatted e-mail → redirect to your “wrong details” page
    header('Location: errors/wrong_details.html');
    exit();
}
if (
    empty($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    die("Invalid CSRF token");
}


// 1. Check if the email already exists
$checkEmailSql = "SELECT email FROM users WHERE email = ?";
$checkEmailStmt = $conn->prepare($checkEmailSql);
$checkEmailStmt->bind_param("s", $email);
$checkEmailStmt->execute();
$checkEmailResult = $checkEmailStmt->get_result();

        // 2. Check if the username already exists
$checkUsernameSql = "SELECT username FROM users WHERE username = ?";
$checkUsernameStmt = $conn->prepare($checkUsernameSql);
$checkUsernameStmt->bind_param("s", $user);
$checkUsernameStmt->execute();
$checkUsernameResult = $checkUsernameStmt->get_result();



if ($checkEmailResult->num_rows > 0 || $checkUsernameResult->num_rows > 0) {
    // Email already in the database
    header('Location: errors/wrong_details.html');
    exit();
} else {
    
    // 2. If email doesn’t exist, proceed with the INSERT
    $sql = "INSERT INTO users (username, email, password, token, is_verified) VALUES (?, ?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $user, $email, $pass, $token);

    if ($stmt->execute()) {
        // Send verification email
            // Prepare the HTML email
        $subject = "Anonymous Love Letter";
        
        // Use embedded CSS for a dark background & cursive font
        // We're loading the "Great Vibes" font from Google Fonts in a <link> tag. 
        // Some email clients may block or ignore external fonts; consider a fallback.
        $message = "
        <html>
        <head>
            <meta charset='UTF-8' />
            <title>Anonymous Love Letter</title>
            <link href='https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap' rel='stylesheet' type='text/css' />
            <style>
                /* Fallback font is a generic cursive in case Great Vibes isn't loaded */
                body {
                    background-color: pink;       /* Pink background */
                    color: darkred;               /* Dark red text */
                    font-family: 'Great Vibes', cursive, 'Times New Roman', sans-serif;
                    padding: 20px;
                    line-height: 1.6;
                }
                
                h1 {
                    text-align: center;
                    margin-bottom: 30px;
                }
                
                p {
                    margin: 1em 0;
                }
                
                /* You can adjust the timestamp color to a darker red if needed */
                .timestamp {
                    color: darkred;  
                }
            </style>
        </head>
        <body>
            <h1>Anonymous Love Letter</h1>
            
            <p><span class='timestamp'>[21:07:53]</span> A: Hey it's me again ;)</p>
            <p><span class='timestamp'>[21:53:46]</span> A: Wanna Link?</p>
            <p><span class='timestamp'>[23:46:18]</span> A: What have you been up to recently?</p>
            <p><span class='timestamp'>[01:18:19]</span> A: Je peux parler français si c plu romantik ahah^^</p>
            <p><span class='timestamp'>[04:19:59]</span> A: Bon bah bonne nuit haha, may we meet some time &lt;3</p>
            <p><span class='timestamp'>[04:59:59]</span> A: I left you my link just in case you want to ride the waves together :')</p>
            
            <p><span class='timestamp'>[04:77:33]</span> A sent you a link... 
            <a href='https://lantenne.io/verify.php?token=$token' style='color:#ff69b4;'>
                https://lantenne.io/verify.php?token=$token
            </a>
            </p>
        </body>
        </html>
        ";
    
        // Set the headers for HTML email
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: noreply@lantenne.io\r\n";
        

        if (mail($email, $subject, $message, $headers)) {
            echo "<div class='message success'>
                    Let's gooo l'Antenne loved your vibe!! Check your mailbox baby;)
                  </div>";
        } else {
            echo "<div class='message warning'>
                    Arghhhhhh I beg u retry or use another email!
                  </div>";
        }
    } else {
        echo "<div class='message error'>
                Arghhh Antenna didn't catch your waves!! Please try to send them again!
              </div>";
    }
}

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>\Branche-toi</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://www.google.com/recaptcha/api.js?render=6Lenuy4rAAAAAIjNcZAaeme5Cya6hzDtp73V8AEl"></script>
    <script src="https://www.google.com/recaptcha/api.js"></script>


</head>
<body>
    <div class="hamburger-menu" id="hamburgerMenu">
        ☰ Menu
    </div>
    <!-- Main Navigation Menu -->
    <?php include 'menu.php'; ?>
    <!-- Register Form -->
    <div class="registerform">
        <h1>Register</h1>
        <form class="form" action="register.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>
    
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>
            
            <label for="password">Password:</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required 
                pattern="(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}" 
                title="Minimum 8 characters, at least one uppercase letter, one number and one special character"
            ><br><br>
            
            <label for="confirm-password">Confirm Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" required><br><br>
            
            
            <div class="terms-checkbox">
                <label for="acceptTerms">I agree with the <a href="static/tc.html" id="tc-button" target="_blank">Electric Vibes</a> de l'Antenne.</label>
                <input type="checkbox" id="acceptTerms" required>
            </div>

            <button type="submit" id="registerButton">BRANCHE</button>
    
        </form>
    
        <h3>ALREADY <a href="login.php">PLUGGED</a>?</h3>
    </div>
    
    <script src="script.js"></script>
</body>
</html>