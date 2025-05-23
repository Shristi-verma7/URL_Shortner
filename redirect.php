<?php
require 'db.php';

$code = $_GET['c'] ?? '';

if ($code) {
    $stmt = $pdo->prepare("SELECT org_url FROM urls WHERE slug = ?");
    $stmt->execute([$code]);
    $url = $stmt->fetchColumn();

    if ($url) {
        header("Location: " . $url);
        exit;
    } else {
        echo "URL not found.";
    }
} else {
    echo "No short code provided.";
}
