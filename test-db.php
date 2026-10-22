<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Test database connection
    $db = getDBConnection();
    if (!$db) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    // Test if tables exist
    $tables = $db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'")->fetchAll();
    $tableNames = array_column($tables, 'table_name');
    
    echo json_encode([
        'success' => true,
        'message' => 'Database connected successfully',
        'tables' => $tableNames,
        'has_posts_table' => in_array('posts', $tableNames),
        'has_categories_table' => in_array('categories', $tableNames)
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
