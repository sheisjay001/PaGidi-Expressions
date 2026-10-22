<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $db = getDBConnection();
    if (!$db) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    // Read the SQL schema file
    $sql = file_get_contents(__DIR__ . '/database.sql');
    
    // Split by semicolon to get individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $errors = [];
    $executed = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos(trim($statement), '--') === 0) {
            continue;
        }
        
        try {
            $db->exec($statement);
            $executed++;
        } catch (PDOException $e) {
            $errors[] = $e->getMessage();
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Database schema installed successfully",
        'executed_statements' => $executed,
        'errors' => $errors
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
