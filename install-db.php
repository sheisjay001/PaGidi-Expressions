<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $db = getDBConnection();
    if (!$db) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    $errors = [];
    $executed = 0;
    
    // Create posts table
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS posts (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            excerpt TEXT,
            content TEXT NOT NULL,
            author VARCHAR(100) DEFAULT 'PaGidi',
            category VARCHAR(100),
            status VARCHAR(20) DEFAULT 'draft' CHECK (status IN ('draft', 'published')),
            featured BOOLEAN DEFAULT FALSE,
            cover_image VARCHAR(255),
            meta_title VARCHAR(255),
            meta_description TEXT,
            meta_keywords VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            published_at TIMESTAMP NULL
        )");
        $executed++;
    } catch (PDOException $e) {
        $errors[] = "Posts table: " . $e->getMessage();
    }
    
    // Create categories table
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS categories (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            slug VARCHAR(100) NOT NULL UNIQUE,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $executed++;
    } catch (PDOException $e) {
        $errors[] = "Categories table: " . $e->getMessage();
    }
    
    // Insert default categories
    try {
        $db->exec("INSERT INTO categories (name, slug, description) VALUES
            ('Expressions', 'expressions', 'Personal expressions and thoughts'),
            ('Mental Health', 'mental-health', 'Mental health and wellness topics'),
            ('Life Stories', 'life-stories', 'Personal stories and experiences')
            ON CONFLICT (name) DO NOTHING");
        $executed++;
    } catch (PDOException $e) {
        $errors[] = "Insert categories: " . $e->getMessage();
    }
    
    // Create indexes
    try {
        $db->exec("CREATE INDEX IF NOT EXISTS idx_status ON posts(status)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_featured ON posts(featured)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_published_at ON posts(published_at)");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_category ON posts(category)");
        $executed += 4;
    } catch (PDOException $e) {
        $errors[] = "Indexes: " . $e->getMessage();
    }
    
    // Create update function
    try {
        $db->exec("CREATE OR REPLACE FUNCTION update_updated_at_column()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ language 'plpgsql'");
        $executed++;
    } catch (PDOException $e) {
        $errors[] = "Function: " . $e->getMessage();
    }
    
    // Create trigger
    try {
        $db->exec("DROP TRIGGER IF EXISTS update_posts_updated_at ON posts");
        $db->exec("CREATE TRIGGER update_posts_updated_at BEFORE UPDATE ON posts
            FOR EACH ROW EXECUTE FUNCTION update_updated_at_column()");
        $executed++;
    } catch (PDOException $e) {
        $errors[] = "Trigger: " . $e->getMessage();
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
