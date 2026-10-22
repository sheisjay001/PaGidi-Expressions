<?php
// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'pagidi_blog');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_TYPE', getenv('DB_TYPE') ?: 'mysql');

// Admin Configuration
define('ADMIN_PASSWORD', getenv('ADMIN_PASSWORD') ?: 'pagidi2025');
define('SESSION_NAME', 'pagidi_admin_session');

// Site Configuration
define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost');
define('MAX_UPLOAD_SIZE', 20 * 1024 * 1024); // 20MB

// Database Connection
function getDBConnection() {
    try {
        if (DB_TYPE === 'pgsql') {
            // PostgreSQL connection for Render
            $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            return new PDO($dsn, DB_USER, DB_PASS, $options);
        } else {
            // MySQL connection for local development
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            return new PDO($dsn, DB_USER, DB_PASS, $options);
        }
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        return null;
    }
}

// Start secure session
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Lax');
        session_start();
    }
}

// Check if admin is logged in
function isAdminLoggedIn() {
    startSecureSession();
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Require admin login
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: admin-login.php');
        exit;
    }
}

// Generate slug from title
function generateSlug($title) {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Create the uploads table if it does not exist yet
function ensureUploadsTable($db) {
    $idColumn = (DB_TYPE === 'pgsql') ? 'SERIAL PRIMARY KEY' : 'INT AUTO_INCREMENT PRIMARY KEY';

    $db->exec("CREATE TABLE IF NOT EXISTS uploads (
        id $idColumn,
        filename VARCHAR(255) NOT NULL,
        mime_type VARCHAR(100) NOT NULL,
        data TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

// Handle image upload.
// Images are stored in the database (base64) so they survive container restarts,
// which wipe the local filesystem on hosts like Render.
function handleImageUpload($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error'];
    }

    // Check file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'error' => 'File too large (max 20MB)'];
    }

    // Check file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type (only PNG, JPG, WEBP allowed)'];
    }

    $binary = file_get_contents($file['tmp_name']);
    if ($binary === false) {
        return ['success' => false, 'error' => 'Failed to read uploaded file'];
    }

    try {
        $db = getDBConnection();
        if (!$db) {
            throw new Exception('Database connection failed');
        }

        ensureUploadsTable($db);

        $stmt = $db->prepare("INSERT INTO uploads (filename, mime_type, data) VALUES (?, ?, ?)");
        $stmt->execute([$file['name'], $mimeType, base64_encode($binary)]);

        return [
            'success' => true,
            'filename' => $file['name'],
            'path' => 'api/image.php?id=' . $db->lastInsertId()
        ];
    } catch (Exception $e) {
        error_log('Image upload failed: ' . $e->getMessage());
        return ['success' => false, 'error' => 'Failed to save image'];
    }
}
?>
