<?php
session_start();
$message = "";

// Initialize attempts tracking
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['block_until'] = null;
}

// Check if the page is currently blocked
if ($_SESSION['block_until'] && time() < $_SESSION['block_until']) {
    $remaining_time = $_SESSION['block_until'] - time();
    $message = "Too many attempts. Please try again after " . ceil($remaining_time / 60) . " minutes.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../config/db_connection.php';

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
    } else {
        // Increment the attempt counter
        $_SESSION['attempts']++;

        if ($_SESSION['attempts'] > 3) {
            // Block for 10 minutes after 3 attempts
            $_SESSION['block_until'] = time() + (10 * 60); // 10 minutes in seconds
            $message = "Too many attempts. Please try again after 10 minutes.";
        } else {
            // Check if the email exists in the database
            $stmt = $pdo->prepare("SELECT email FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                // Generate a secure token
                $token = bin2hex(random_bytes(32));
                $expires_at = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

                // Store token in the database
                $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)");
                $stmt->execute([$email, $token, $expires_at]);

                // Send reset link via email
                $reset_link = "https://lantenne.io/reset_password.php?token=$token";
                $subject = "Password Reset Request";
                $body = "Hi,\n\nClick the link below to reset your password:\n\n$reset_link\n\nIf you did not request this, please ignore this email.";
                $headers = "From: noreply@lantenne.io";

                if (mail($email, $subject, $body, $headers)) {
                    $message = "I  sent  you  smth  that  might  interest  u  by  mail  ;)";
                } else {
                    $message = "Failed to send the reset email.";
                }
            } else {
                $message = "I  sent  you  smth  that  might  interest  u  by  mail  ;)";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="forgot-password-form">
        <h1>Forgot Your Password?</h1>
        <?php if ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <?php if (!$_SESSION['block_until'] || time() >= $_SESSION['block_until']): ?>
        <form action="forgot_password.php" method="POST">
            <label for="email">Enter your email:</label>
            <input type="email" id="email" name="email" required><br><br>
            <button type="submit">Send Reset Link</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
