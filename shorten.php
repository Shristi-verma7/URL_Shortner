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
    <title>URL Shortener</title>
    <style>
        body {
    background: #8585a9;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

    .container {
    background: #c8c8c8;
    padding: 40px;
    border-radius: 8px;
    width: 400px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        input[type="url"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .result {
            margin-top: 20px;
            font-size: 15px;
        }

        .result a {
            color: #007bff;
            text-decoration: none;
        }

        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔗 URL Shortener</h2>
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
</body>
</html>
