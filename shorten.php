<?php
require 'db.php';

function generateShortCode($length = 6) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

$short_url = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $org_url = trim($_POST['url']);

    if (!filter_var($org_url, FILTER_VALIDATE_URL)) {
        $error = "❌ Invalid URL.";
    } else {
        $slug = generateShortCode();

        $stmt = $pdo->prepare("SELECT id FROM urls WHERE slug = ?");
        $stmt->execute([$slug]);
        while ($stmt->rowCount() > 0) {
            $slug = generateShortCode();
            $stmt->execute([$slug]);
        }

        $stmt = $pdo->prepare("INSERT INTO urls (org_url, slug) VALUES (?, ?)");
        $stmt->execute([$org_url, $slug]);

        $short_url = "http://localhost/shortener/redirect.php?c=$slug";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>URL Shortener </title>
  <style>
    body {
      background: #b3d1f2;
      font-family: 'Comic Sans MS', cursive, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .card {
      background: #e6f0fa;
      border-radius: 20px;
      padding: 40px;
      width: 500px;
      text-align: center;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      position: relative;
    }

    .card h1 {
      font-size: 2em;
      margin-bottom: 20px;
    }

    .scissors {
      font-size: 80px;
      position: absolute;
      top: 40px;
      right: 40px;
      opacity: 0.8;
    }

    .url-box {
      display: flex;
      align-items: center;
      background: white;
      border-radius: 10px;
      padding: 10px;
      font-size: 16px;
      overflow-x: auto;
      border: 1px solid #ccc;
      margin-top: 20px;
    }

    .url-box span {
      margin-right: 10px;
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="scissors">✂️</div>
    <h1> 🔗 URL Shortener</h1>
    
   <div class="container">
        <form method="POST">
            <input type="url" name="url" required placeholder="Enter long URL here...">
            <button type="submit">Shorten URL</button>
        </form>

        <?php if ($short_url): ?>
            <div class="result">
                Short URL: <a href="<?= $short_url ?>" target="_blank"><?= $short_url ?></a>
            </div>
        <?php elseif ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
    </div>
    </div>
</body>
</html>


