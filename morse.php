<?php
// morse.php
session_start(); // Optional: if you want session-based features

// (Optional) Any PHP logic here...
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Morss</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
  <style>
    /* Style for the antenna button */
    .antenna-button {
      background: none;
      border: none;
      cursor: pointer;
      margin-top: 40px;
      margin: 10rem;
    }
    .antenna-button img {
      width: 75px;
      height: auto;
      transition: transform 0.3s;
    }
    .antenna-button img:hover {
      transform: scale(1.1);
    }
    /* Style for the Morse code text overlay */
    .morse-text {
      display: none;
      position: fixed;
      top: 20%;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(0, 0, 0, 0.85);
      color: #ff0;
      padding: 15px 25px;
      border-radius: 5px;
      font-size: 1.2rem;
      z-index: 1000;
      box-shadow: 0 4px 8px rgba(0,0,0,0.5);
    }
  </style>
</head>
<body>
  <h1 class="glitch">AHHHHHHHHHH</h1>
  
  <!-- Antenna Button -->
  <button class="antenna-button" onclick="playMorse()">
    <img src="pics/antenne.webp" alt="AnTenne">
  </button>
  
  <!-- Hidden HTML5 Audio Element -->
  <audio id="morseAudio" src="audio/morse.wav"></audio>
  
  <!-- Hidden Morse Code Text Overlay -->
  <div id="morseText" class="morse-text">... --- ... --.. .. --.. ..</div>
  
  <script>
    function playMorse() {
      // Get the audio element and play the Morse code audio
      var audio = document.getElementById('morseAudio');
      audio.play();
      
      // Show the Morse code text overlay
      var morseText = document.getElementById('morseText');
      morseText.style.display = 'block';
      
      // After 3 seconds, hide the Morse code text overlay
      setTimeout(function() {
        morseText.style.display = 'none';
      }, 3000);
    }
  </script>
</body>
</html>
