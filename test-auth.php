<?php
require_once 'config.php';

header('Content-Type: application/json');

startSecureSession();

echo json_encode([
    'session_active' => isset($_SESSION),
    'admin_logged_in' => isAdminLoggedIn(),
    'session_data' => $_SESSION,
    'session_name' => session_name()
]);
?>
