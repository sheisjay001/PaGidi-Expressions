<?php
require_once '../config.php';

// Serves images stored in the uploads table
$id = $_GET['id'] ?? null;

if (!$id || !ctype_digit((string) $id)) {
    http_response_code(404);
    exit;
}

try {
    $db = getDBConnection();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $stmt = $db->prepare("SELECT mime_type, data FROM uploads WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetch();
} catch (Exception $e) {
    error_log("Image fetch failed: " . $e->getMessage());
    http_response_code(404);
    exit;
}

if (!$image) {
    http_response_code(404);
    exit;
}

$binary = base64_decode($image['data']);
if ($binary === false) {
    http_response_code(404);
    exit;
}

header('Content-Type: ' . $image['mime_type']);
header('Content-Length: ' . strlen($binary));
// Each image gets a unique id, so it is safe to cache permanently
header('Cache-Control: public, max-age=31536000, immutable');
echo $binary;
?>
