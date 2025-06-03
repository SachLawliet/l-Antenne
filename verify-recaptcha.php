<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       $recaptchaConfig = require __DIR__ . '/../config/recaptcha.php';
        $siteKey   = $recaptchaConfig['site_key'];
        $secretKey = $recaptchaConfig['secret_key'];
    $token = $_POST['token'] ?? '';
    $action = $_POST['action'] ?? '';

    $response = file_get_contents(
        'https://www.google.com/recaptcha/api/siteverify?secret='
        . urlencode($secret)
        . '&response=' . urlencode($token)
        . '&remoteip=' . urlencode($_SERVER['REMOTE_ADDR'])
    );

    $result = json_decode($response, true);

    if (
        empty($result['success']) ||
        $result['score'] < 0.5 || 
        $result['action'] !== $action
    ) {
        http_response_code(403);
        error_log("Bot or invalid captcha from " . $_SERVER['REMOTE_ADDR']);
        exit;
    }

    // Optionally log success or attach to session
    $_SESSION['recaptcha_passed'] = true;
}
?>
