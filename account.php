<?php


session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Générer le token CSRF s'il n'est pas défini
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require '../config/db_connection.php';

// Connexion à la BDD
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer les détails de l'utilisateur
$sql = "SELECT username, email, id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['user']);
$stmt->execute();
$stmt->bind_result($username, $email, $userId);
$stmt->fetch();
$stmt->close();


function addWatermarkToImage($filePath, $username, $createdAt) {
    $info = @getimagesize($filePath);
    if (!$info) {
        return; // pas une image, on skip #edited
    }
    list($imgW, $imgH) = $info;
    switch ($info['mime']) {
        case 'image/jpeg':
            $img = imagecreatefromjpeg($filePath);
            break;
        case 'image/png':
            $img = imagecreatefrompng($filePath);
            imagealphablending($img, true);
            imagesavealpha($img, true);
            break;
        case 'image/gif':
            $img = imagecreatefromgif($filePath);
            break;
        case 'image/webp':
            $img = imagecreatefromwebp($filePath);
            imagealphablending($img, true);
            imagesavealpha($img, true);
            break;
        default:
            return; // skip non-image #edited
    }
    
    $black = imagecolorallocate($img, 0, 0, 0);
    $col = ($info['mime'] === 'image/jpeg')
        ? imagecolorallocate($img, 255, 255, 0)
        : imagecolorallocatealpha($img, 255, 255, 0, 60);

    $font = __DIR__ . '/fonts/Electrica Salsa.ttf';
    if (!file_exists($font)) {
        imagedestroy($img);
        return; // police manquante #edited
    }

    $text = sprintf("%s | %s | lantenne.io", $username, date('Y-m-d H:i', strtotime($createdAt)));

    // calcul taille adaptative pour occuper ~50% de la largeur #edited
    $targetRatio = 0.6; $min=8; $max=intval($imgW/5); $best=$min;
    while($min <= $max) {
        $mid = intdiv($min+$max,2);
        $b = imagettfbbox($mid,0,$font,$text);
        $w = abs($b[2]-$b[0]);
        if ($w > $imgW*$targetRatio) { $max=$mid-1; }
        else { $best=$mid; $min=$mid+1; }
    }

    // position bas‐droite, marge 10px #edited
    $b = imagettfbbox($best,0,$font,$text);
    $w = abs($b[2]-$b[0]);
    $h = abs($b[1]-$b[7]);
    $x = $imgW - $w - 10;
    $y = $imgH - 20;

    // texte en gras: on trace 2 fois pour effet bold #edited
    imagettftext($img, $best, 0, $x+1, $y, $col, $font, $text);
    imagettftext($img, $best, 0, $x,   $y-1, $col, $font, $text);

    // enregistrer #edited
    switch ($info['mime']) {
        case 'image/jpeg': imagejpeg($img,$filePath,90); break;
        case 'image/png':  imagepng($img,$filePath);        break;
        case 'image/gif':  imagegif($img,$filePath);        break;
        case 'image/webp': imagewebp($img,$filePath);       break;
    }
    imagedestroy($img);
}



/* ============================================================================
   6) TRAITEMENT de l'UPLOAD (section à **remplacer** dans votre fichier)
   ============================================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media'])) {
    // 6.1) Vérif CSRF
    if (!isset($_POST['csrf_token']) 
        || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }

    // 6.2) Titre & dossier
    $media_title = substr(trim($_POST['media_title'] ?? ''), 0, 20);
    $folderId    = (!empty($_POST['folder_id']) ? intval($_POST['folder_id']) : null);

    // 6.3) Quota
    $check = $conn->prepare("SELECT COUNT(*) FROM user_media WHERE user_id = ?");
    $check->bind_param("i", $userId);
    $check->execute();
    $check->bind_result($currentCount);
    $check->fetch();
    $check->close();

    $filesToUpload = count($_FILES['media']['tmp_name']);
    if ($currentCount + $filesToUpload > 35) {
        header("Location: errors/error_quota.php");
        exit();
    }

    // 6.4) Types & taille max
    $allowedTypes = [
    'image/jpeg','image/png','image/webp','image/gif',
    'video/mp4',
    'audio/mpeg','audio/mp3','audio/wav','audio/m4a',
    'application/pdf'
    ];
    $maxFileSize  = 15 * 1024 * 1024; // 15 MB

    // 6.5) Boucle sur chaque fichier
    foreach ($_FILES['media']['tmp_name'] as $key => $tmpName) {
        $origName = basename($_FILES['media']['name'][$key]);
        // Génération d'un nom unique pour éviter collisions
        $targetPath = 'uploads/' . uniqid() . '_' . $origName;

        $fileType = mime_content_type($tmpName);
        $fileSize = filesize($tmpName);

        // Validation
        if (!in_array($fileType, $allowedTypes) || $fileSize > $maxFileSize) {
            continue; // on skip les invalides
        }

        if (move_uploaded_file($tmpName, $targetPath)) {
            $createdAt = date('Y-m-d H:i:s');
        
                    // 6.6) Watermark **seulement** sur images #edited
        if (str_starts_with($fileType, 'image/')) {
            addWatermarkToImage($targetPath, $username, $createdAt);
            $mediaType = 'image';
        }
        // sinon on détecte le mediaType
        elseif ($fileType === 'video/mp4') {
            $mediaType = 'video';
        }
        elseif (str_starts_with($fileType, 'audio/')) {
            $mediaType = 'audio';
        }
        elseif ($fileType === 'application/pdf') {
            $mediaType = 'pdf';
        } else {
            // un type inattendu, on supprime et skip
            @unlink($targetPath);
            continue;
        }

            $ins = $conn->prepare(
                "INSERT INTO user_media
                   (user_id, media_path, media_type, media_title, folder_id, is_folder, created_at)
                 VALUES (?, ?, ?, ?, ?, 0, ?)"
            );
            $ins->bind_param(
                "isssis",
                $userId,
                $targetPath,
                $mediaType,
                $media_title,
                $folderId,
                $createdAt
            );
            $ins->execute();
            $ins->close();
        }
    }
    // 6.8) Retour à la page
    header("Location: account.php");
    exit();
}


// Récupérer les médias individuels de l'utilisateur (is_folder = 0)
$stmt = $conn->prepare("SELECT id, media_path, media_type, media_title FROM user_media WHERE user_id = ? AND is_folder = 0 AND folder_id IS NULL LIMIT 35");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$media = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Récupérer les dossiers (is_folder = 1)
$stmt = $conn->prepare("SELECT id, media_title FROM user_media WHERE user_id = ? AND is_folder = 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$folders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Pour le menu déroulant dans le formulaire d'upload, utilisez la liste des dossiers
$userFolders = $folders;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Page</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="/assets/account.css">

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Suppression de médias individuels
      document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', event => {
          event.preventDefault();
          if (confirm('Are you sure you want to erase this Antenna from the surface of Earth? :\'(')) {
            btn.closest('form').submit();
          }
        });
      });
      
      // Fonctionnalité du formulaire d'upload
      const fileInput = document.getElementById("media-input");
      const submitButton = document.getElementById("submit-button");
      fileInput.addEventListener("change", function() {
        submitButton.disabled = !(fileInput.files && fileInput.files.length > 0);
      });
      document.getElementById('upload-btn').addEventListener('click', () => {
        const form = document.getElementById('upload-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
      });
    });
  </script>
</head>
<body>
  <div class="account-page">
    <!-- Left Panel : Infos utilisateur, menu et création de dossier -->
    <div class="left-panel">
      <h3>Ciao <?php echo htmlspecialchars($username); ?>!</h3>
      <p>Email: <?php echo htmlspecialchars($email); ?></p>
      <nav id="Accmenu" class="Accmenu">
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="description.php">Description</a></li>
          <li><a href="bluehand.php">Bluehand</a></li>
          <li><a href="gallery.php">Gallery</a></li>
          <li><a href="Radio.php">Radio</a></li>
          <li><a href="logout.php">Log Out</a></li>
        </ul>
      </nav>
      <!-- Formulaire de création de dossier -->
      <div class="create-folder-form">
        <h4>Create a Folder</h4>
        <form action="create_folder.php" method="POST">
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
          <input type="text" name="folder_name" placeholder="Folder name" required>
          <button type="submit">Create</button>
        </form>
      </div>
    </div>

    <!-- Middle Panel : Médias individuels + Dossiers -->
    <div class="middle-panel">
        
      <h2 id="antennaa">Your Antennas</h2>
      <div class="media-grid">
        <?php foreach ($media as $item): ?>
          <div class="grid-item">
            <?php if ($item['media_type'] === 'image'): ?>
              <img src="<?php echo htmlspecialchars($item['media_path']); ?>" alt=":/">
            <?php elseif ($item['media_type'] === 'video'): ?>
              <video controls>
                <source src="<?php echo htmlspecialchars($item['media_path']); ?>" type="video/mp4">
              </video>
            <?php elseif ($item['media_type'] === 'audio'): ?>
                <audio controls preload="none">
                  <source 
                    src="<?=htmlspecialchars($item['media_path'])?>" 
                    type="<?=mime_content_type($item['media_path'])?>">
                  Ur device doesnt support this audio
                </audio>
            <?php elseif ($item['media_type'] === 'pdf'): ?>
                <embed src="<?php echo htmlspecialchars($item['media_path']); ?>" type="application/pdf">
                <a href="<?=htmlspecialchars($item['media_path'])?>" target="_blank" style="color:yellow;">Viewwww</a>
            <?php endif; ?>
            <form action="delete_media.php" method="POST">
              <input type="hidden" name="media_id" value="<?php echo $item['id']; ?>">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
              <button type="submit" class="delete-btn">✖</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>


        <h2 id="antennaa">Your Folders</h2>
      <div class="folder-grid">
        <?php 
        // Préparer une requête pour récupérer jusqu'à 9 fichiers d'un dossier
        $stmtFolderAll = $conn->prepare("SELECT id, media_path FROM user_media WHERE folder_id = ? AND is_folder = 0 LIMIT 9");
        ?>
        <?php foreach ($folders as $folder): ?>
          <?php 
          $stmtFolderAll->bind_param("i", $folder['id']);
          $stmtFolderAll->execute();
          $resultAll = $stmtFolderAll->get_result();
          $allFiles = [];
          while($row = $resultAll->fetch_assoc()){
              $allFiles[] = $row['media_path'];
          }
          $previewFiles = array_slice($allFiles, 0, 4);
          ?>
          <div class="folder-item"
         data-media='<?php echo json_encode($allFiles, JSON_HEX_APOS); ?>'
         data-title="<?php echo htmlspecialchars($folder['media_title']); ?>">
            <div class="folder-preview">
              <?php foreach ($previewFiles as $media): ?>
                <img src="<?php echo htmlspecialchars($media); ?>" alt=":)">
              <?php endforeach; ?>
            </div>
            <div class="folder-title"><?php echo htmlspecialchars($folder['media_title']); ?></div>
            <form action="delete_folder.php" method="POST">
              <input type="hidden" name="folder_id" value="<?php echo $folder['id']; ?>">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
              <button type="submit" class="delete-btn">✖</button>
            </form>
          </div>
        <?php endforeach; ?>
        <?php $stmtFolderAll->close(); ?>
        
      </div>
    </div>

    <!-- Right Panel : Formulaire d'upload -->
    <div class="right-panel">
      <button class="action-btn" id="upload-btn">Build an Antenna</button>
      <form class="action-btn" id="upload-form" style="display:none;" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <!-- Sélection du dossier (menu déroulant) -->
        <label for="folder-select">Select Folder:</label>
        <select name="folder_id" id="folder-select">
          <option value="">None</option>
          <?php foreach ($userFolders as $uf): ?>
            <option value="<?php echo $uf['id']; ?>">
              <?php echo htmlspecialchars($uf['media_title']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <br>
        <input type="file" name="media[]" multiple     accept="
          image/jpeg,
          image/png,
          image/webp,
          image/gif,
          video/mp4,
          application/pdf,
          audio/mpeg,
          audio/mp3,
          audio/m4a,
          audio/wav" 
          id="media-input">
        <input type="text" name="media_title" id="media-title-input" placeholder="Name (max. 20):" maxlength="20">
        <button class="action-btn" type="submit" id="submit-button" disabled>Submit</button>
      </form>
      <div style="color:yellow;">
          <h5>please follow the upload policy below thank uuu</h5>
          <h6>max 6x10MB at once</h6>
          <h6>do not share private info, except if you're ready to be contacted</h6>
          <h6>PDF and Audio outside of folders</h6>
          <h6>On pictures, a watermark will be added to prevent theft</h6>
          
      </div>
      
    </div>
  </div>
  <div class="fullscreenOverlay"></div>
  <script src="script.js"></script>
</body>
</html>