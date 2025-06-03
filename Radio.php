<?php
// Si une requête AJAX est faite avec ?action=log, on enregistre l'événement et on renvoie un statut.
if (isset($_GET['action']) && $_GET['action'] === 'log') {
    $log_entry = date('Y-m-d H:i:s')
      . " - Bonne fréquence trouvée depuis "
      . $_SERVER['REMOTE_ADDR'] . "\n";
    file_put_contents('radio_log.txt', $log_entry, FILE_APPEND);
    echo json_encode(['status' => 'success']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <body style="
  background: url('pics/RADIAX.png');
">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Radio.PHP</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* Wrapper responsive centré */
    .radio-wrapper {
      position: absolute;
      left: 50%; top: 20vh;
      transform: translateX(-50%);
      width: 90vw; max-width: 50rem;
      padding: 1rem;
    }
    .radio-container {
      background: #2e2e2e;
      border-radius: 1rem;
      box-shadow: 0 0 1.25rem rgba(0,0,0,0.5);
      padding: 1.5rem;
    }
    .screen {
      background: #111;
      color: yellow;
      font-size: 2rem;
      text-align: center;
      padding: 1rem;
      border-radius: 0.5rem;
      box-shadow: inset 0 0 0.625rem yellow;
    }
    .slider-container {
      margin-top: 1.25rem;
    }
    input[type=range] {
      -webkit-appearance: none;
      width: 100%; height: 0.75rem;
      background: #333;
      border-radius: 0.375rem;
      outline: none;
      cursor: pointer;
    }
    input[type=range]::-webkit-slider-thumb {
      -webkit-appearance: none;
      width: 1.5rem; height: 1.5rem;
      background: yellow;
      border-radius: 50%;
      box-shadow: 0 0 0.3125rem rgba(0,0,0,0.5);
      margin-top: -0.375rem;
    }
    .control-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1rem;
    }
    .track-title {
      color: #fff;
      font-size: 2.5rem;
      font-weight: bold;
      flex: 1;
      text-align: center;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      background-color: BLACK;
      margin-top: 5vh;
      padding: 2rem;
      border-radius: 20px;
    }
    #source-button {
      width: 2rem;
      height: 2rem;
      cursor: pointer;
    }
    h2 {
      text-align: center;
      padding-top: 5rem ;
    }
    
    @media (max-width: 768px) {
      .track-title {
        font-size: 1.3rem;
      }
      
    }
  </style>
</head>
<body>
        <!-- Hamburger Menu for Mobile -->
    <div class="hamburger-menu" id="hamburgerMenu">
        ☰ Menu
    </div>
  <?php include 'menu.php'; ?>
  <h2 class="glitch">hear what’s happening on the waves!</h2>

  <div class="radio-wrapper">
    <div class="radio-container">
      <div class="screen">
        <span id="freq-display">89.5</span> MHz
      </div>
    </div>
    <div class="slider-container">
      <input id="freq-slider"
             type="range"
             min="85.0"
             max="96.3"
             step="0.1"
             value="89.5">
    </div>
    <div class="control-bar">
      <span id="track-title" class="track-title glitch"></span>
      <a id="source-link" href="#" target="_blank" style="display:none;">
        <img id="source-button" src="pics/antenne.webp" alt="Source">
      </a>
    </div>
  </div>

  <!-- Sons statiques et dynamiques -->
  <audio id="static-sound" src="audio/static.mp3" autoplay loop></audio>
  <!-- Ajouter un son: renseigner data-freq (MHz), data-title (facultatif), data-source (facultatif) -->
  <audio id="kk" data-freq="87.3" data-source="audio/cacacuit.m4a" src="audio/cacacuit.m4a"></audio>
  <audio id="skipe" data-freq="88.8" data-title="JOHN PORK IS CALLING" data-source="audio/skype.m4a" src="audio/skype.m4a"></audio>
  <audio id="watzap" data-freq="91.5" data-source="audio/watzap.m4a" src="audio/watzap.m4a"></audio>
  <audio id="midnight" data-freq="90.3" data-title="SPACECAST" data-source="https://www.netflix.com/title/80987903" src="audio/midnight.mp3"></audio>
  <audio id="Algorhythmic" data-freq="95.3" data-title="Algorhythmic Control" data-source="audio/Algorhythmic Control.mp3" src="audio/Algorhythmic Control.mp3"></audio>
  <audio id="electricfriends" data-freq="93.0" data-title="Are Friends Electric" data-source="audio/Are Friends Electric.mp3" src="audio/Are Friends Electric.mp3"></audio>
  <audio id="letting" data-freq="85.2" data-title="letting go childhood dreams" data-source="https://on.soundcloud.com/C3lj0cSTNd6tCvcT9w" src="audio/Letting Go of Childhood Dreams.mp3"></audio>
  <audio id="fortnite" data-freq="96.1" data-source="https://www.fortnite.com/download?lang=ru" src="audio/fortnite.mp3"></audio>
  <audio id="DANCEEEEEEEE" data-freq="92.1" data-title="Dance" data-source="audio/Dance.mp3" src="audio/Dance.mp3"></audio>
  <audio id="touch" data-freq="86.7" data-title="I MISS UR YOUR TOUCH" data-source="https://youtu.be/OYHCo2D4OP0" src="audio/YOURTOUCH.wav"></audio>
    
    <script src="script.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const slider      = document.getElementById('freq-slider');
      const freqDisplay = document.getElementById('freq-display');
      const staticSound = document.getElementById('static-sound');
      const titleDisplay= document.getElementById('track-title');
      const sourceLink  = document.getElementById('source-link');

      // Construire dynamiquement les cibles à partir des balises audio[data-freq]
      const targets = Array.from(document.querySelectorAll('audio[data-freq]')).map(el => ({
        freq:    parseFloat(el.dataset.freq),
        sound:   el,
        title:   el.dataset.title || '',
        source:  el.dataset.source || ''
      }));
      const tolerance = 0.2;
      let lastTarget = null;

      function vibrate() {
        if (navigator.vibrate) navigator.vibrate(50);
      }
      function logEvent() {
        fetch('?action=log').then(res => res.json()).then(data => console.log('Event logged:', data.status));
      }

      slider.addEventListener('input', () => {
        const freq = parseFloat(slider.value);
        freqDisplay.textContent = freq.toFixed(1);

        // Trouver la cible proche
        const hitIndex = targets.findIndex(t => Math.abs(freq - t.freq) < tolerance);

        if (hitIndex !== -1) {
          if (hitIndex !== lastTarget) {
            // Lecture du son détecté
            targets.forEach((t, i) => {
              if (i === hitIndex) t.sound.play();
              else { t.sound.pause(); t.sound.currentTime = 0; }
            });
            staticSound.pause();
            vibrate();
            logEvent();
            // Mise à jour du titre
            titleDisplay.textContent = targets[hitIndex].title;
            // Mise à jour du lien source
            if (targets[hitIndex].source) {
              sourceLink.href = targets[hitIndex].source;
              sourceLink.style.display = 'inline-block';
            } else {
              sourceLink.style.display = 'none';
            }
            lastTarget = hitIndex;
          }
        } else {
          // Retour au son statique
          targets.forEach(t => { t.sound.pause(); t.sound.currentTime = 0; });
          if (staticSound.paused) staticSound.play();
          titleDisplay.textContent = '';
          sourceLink.style.display = 'none';
          lastTarget = null;
        }
      });
    });
  </script>
  
  
</body>
</html>
