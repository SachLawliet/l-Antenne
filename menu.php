<?php

session_set_cookie_params([
  'lifetime' => 0,
  'path'     => '/',
  'domain'   => 'lantenne.io',
  'secure'   => true,       // cookie only over HTTPS
  'httponly' => true,       // inaccessible to JavaScript
  'samesite' => 'Strict'    // prevents CSRF
]);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



// Check only once per session
if (!isset($_SESSION['geo_checked'])) {
    $blocked_countries = ['RU', 'CH', 'US', 'IL'];
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // Simple API to check country
    $ip_info = @json_decode(file_get_contents("http://ip-api.com/json/$user_ip?fields=countryCode"), true);

    $_SESSION['geo_checked'] = true;

    if ($ip_info && in_array($ip_info['countryCode'], $blocked_countries)) {
        include 'blocked-region.php';
        exit;
    }
}
?>
<!-- Menu HTML -->
<nav id="menu" class="menu">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="description.php">Description</a></li>
        <li><a href="gallery.php">Gallery</a></li>
        <?php if (isset($_SESSION['user'])): ?>
            <li><a href="account.php">Account</a></li>
            <li><a href="bluehand.php">Bluehand</a></li>
            <li><a href="Radio.php">Radio</a></li>
            <li><a href="logout.php">Log Out</a></li>
        <?php else: ?>
            <li><a href="register.php">Register</a></li>
            <li><a href="login.php">Log In</a></li>
        <?php endif; ?>
        <li><a href="static/tc.html">Rules and Contact</a></li>
    </ul>
    <li>
      <button id="toggle-invert-btn" style="background:none;border:none;color:inherit;cursor:pointer;">
        S
      </button>
    </li>
</nav>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('toggle-invert-btn');
    if (!btn) return;

    btn.addEventListener('click', e => {
      e.preventDefault();
      document.documentElement.classList.toggle('invert-mode');
      // Optionally, remember the user’s choice in localStorage:
      if (document.documentElement.classList.contains('invert-mode')) {
        localStorage.setItem('invertMode', '1');
      } else {
        localStorage.removeItem('invertMode');
      }
    });

    // On page load, check if user had previously enabled “invert”:
    if (localStorage.getItem('invertMode') === '1') {
      document.documentElement.classList.add('invert-mode');
    }
  });
</script>


<!-- reCAPTCHA protection -->
<script src="https://www.google.com/recaptcha/api.js?render=6Lenuy4rAAAAAIjNcZAaeme5Cya6hzDtp73V8AEl"></script>
<script>
  const siteKey = '6Lenuy4rAAAAAIjNcZAaeme5Cya6hzDtp73V8AEl';

  document.body.addEventListener('click', () => {
    grecaptcha.ready(() => {
      grecaptcha.execute(siteKey, { action: 'click' }).then((token) => {
        // Send to server for verification
        fetch('/verify-recaptcha.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: `token=${encodeURIComponent(token)}&action=click`
        });
      });
    });
  });
</script>
