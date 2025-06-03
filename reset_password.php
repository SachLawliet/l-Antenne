<?php
session_start();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../config/db_connection.php';

    $token = $_GET['token'] ?? '';
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? ''; // Capture confirm password

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $message = "Passwords don’t match, bby girl.";
    } else {
        // Validate the token
        $stmt = $pdo->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reset || new DateTime() > new DateTime($reset['expires_at'])) {
            $message = "Invalid or expired token.";
        } else {
            // Validate password strength
            if (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
                $message = "Password must be at least 8 characters long, include an uppercase letter, and a number.";
            } else {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                // Update the user's password
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->execute([$hashed_password, $reset['email']]);

                // Delete the token
                $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
                $stmt->execute([$reset['email']]);

                $message = "Your password has been reset successfully!";
                
                // Redirect to the login page after 3 seconds
                header("Refresh:1; url=login.php");
                exit();
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
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="reset-password-form">
        <h1>Reset Your Password</h1>
        <?php if ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form action="reset_password.php?token=<?php echo htmlspecialchars($_GET['token']); ?>" method="POST">
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required><br><br>
        
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
