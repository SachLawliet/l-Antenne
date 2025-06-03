<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Access Denied</title>
  <style>
    html, body {
      background-color: black;
      color: red;
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Arial Black', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 999;
    }

    .blocked-message {
      text-align: center;
      font-size: 4rem;
      padding: 40px;
      max-width: 90%;
      z-index: 999;
    }

    @media (max-width: 768px) {
      .blocked-message {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>
  <div class="blocked-message">
    🚫 ACCESS FORBIDDEN<br>
    <small>Due to regional restrictions, this service is not available in your country.</small>
  </div>
</body>
</html>
