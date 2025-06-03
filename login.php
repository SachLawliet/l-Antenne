<?php
session_start();
$message = "";

// Initialize login attempts and block start time if not already set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['block_start_time'])) {
    $_SESSION['block_start_time'] = null;
}

// Check if the user is logged in
if (isset($_SESSION['user'])) {
    header("Location: account.php");
    exit();
}

// Check if the user is blocked and if the block duration has expired
$block_duration = 10 * 60; // 10 minutes in seconds
if ($_SESSION['block_start_time'] !== null) {
    $time_since_block = time() - $_SESSION['block_start_time'];
    if ($time_since_block > $block_duration) {
        // Reset block and login attempts
        $_SESSION['block_start_time'] = null;
        $_SESSION['login_attempts'] = 0;
    }
}

// Block login if attempts exceed the limit and block duration hasn't expired
if ($_SESSION['login_attempts'] >= 5 && $_SESSION['block_start_time'] !== null) {
    $remaining_time = $block_duration - (time() - $_SESSION['block_start_time']);
    $minutes = floor($remaining_time / 60);
    $seconds = $remaining_time % 60;
    $message = "Too many failed attempts. Please try again in $minutes minutes and $seconds seconds.";
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    require '../config/db_connection.php';

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Capture form data
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    // Check if the user exists and is verified
    $sql = "SELECT password, is_verified FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password, $is_verified);
        $stmt->fetch();

        // Check password and verification status
        if (password_verify($pass, $hashed_password)) {
            if ((int)$is_verified === 1) {
                $_SESSION['user'] = $user; // Store username in session
                $_SESSION['login_attempts'] = 0; // Reset login attempts
                $_SESSION['block_start_time'] = null; // Clear block start time
                header("Location: bluehand.php"); // Redirect on successful login
                exit();
            } else {
                $message = "Account not verified. Please check your email.";
            }
        } else {
            $message = "Incorrect infos bro.";
            $_SESSION['login_attempts']++;
        }
    } else {
        $message = "Incorrect infos bro.";
        $_SESSION['login_attempts']++;
    }

    // Start the block if the limit is reached
    if ($_SESSION['login_attempts'] >= 5) {
        $_SESSION['block_start_time'] = time();
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
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Include Hamburger Menu -->
    <div class="hamburger-menu" id="hamburgerMenu">
        ☰ Menu
    </div>
    
    <!-- Include Navigation Menu -->
    <?php include 'menu.php'; ?>

    <div class="loginform">
        <h1>Login</h1>
        <?php if ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <?php if ($_SESSION['login_attempts'] < 5 || $_SESSION['block_start_time'] === null): ?>
            <form class="form" action="login.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required><br><br>
        
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br><br>
        
                <button type="submit">Login</button>
            </form>
        <?php endif; ?>
        <h3>Still not <a href="register.php">plugged</a>?</h3>
        <h3><a href="forgot_password.php">Forgot Your Password?</a></h3>
    </div>

    <!-- Include JavaScript -->
    <script src="script.js"></script>
</body>
</html>
