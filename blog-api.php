<?php
header('Content-Type: application/json');

// Path to blog data file
$dataFile = __DIR__ . '/blog-data.json';

// Helper function to read blog data
function getBlogPosts($dataFile) {
    if (!file_exists($dataFile)) {
        return ['posts' => []];
    }
    
    $json = file_get_contents($dataFile);
    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['posts' => []];
    }
    
    return $data;
}

// Helper function to save blog data
function saveBlogPosts($dataFile, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT);
    return file_put_contents($dataFile, $json, LOCK_EX) !== false;
}

// Handle different actions
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get':
        // Get all blog posts
        $data = getBlogPosts($dataFile);
        
        // Sort posts by date (newest first)
        usort($data['posts'], function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        echo json_encode($data);
        break;
        
    case 'get_featured':
        // Get only featured posts
        $data = getBlogPosts($dataFile);
        $featured = array_filter($data['posts'], function($post) {
            return isset($post['featured']) && $post['featured'] === true;
        });
        
        // Re-index array
        $featured = array_values($featured);
        
        // Sort by date
        usort($featured, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        echo json_encode(['posts' => $featured]);
        break;
        
    case 'get_single':
        // Get a single post by ID
        $id = intval($_GET['id'] ?? 0);
        $data = getBlogPosts($dataFile);
        
        $post = null;
        foreach ($data['posts'] as $p) {
            if ($p['id'] === $id) {
                $post = $p;
                break;
            }
        }
        
        if ($post) {
            echo json_encode(['post' => $post]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Post not found']);
        }
        break;
        
    case 'add':
        // Add a new post
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
        }
        
        session_start();
        // Simple authentication check (you should improve this)
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            break;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['title']) || !isset($input['content'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            break;
        }
        
        $data = getBlogPosts($dataFile);
        
        // Generate new ID
        $maxId = 0;
        foreach ($data['posts'] as $p) {
            if ($p['id'] > $maxId) {
                $maxId = $p['id'];
            }
        }
        
        $newPost = [
            'id' => $maxId + 1,
            'title' => $input['title'],
            'excerpt' => $input['excerpt'] ?? substr($input['content'], 0, 150) . '...',
            'content' => $input['content'],
            'tag' => $input['tag'] ?? '#Expressions',
            'date' => date('Y-m-d'),
            'author' => $input['author'] ?? 'PaGidi',
            'facebook_url' => $input['facebook_url'] ?? '',
            'featured' => $input['featured'] ?? false
        ];
        
        $data['posts'][] = $newPost;
        
        if (saveBlogPosts($dataFile, $data)) {
            echo json_encode(['success' => true, 'post' => $newPost]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save post']);
        }
        break;
        
    case 'update':
        // Update an existing post
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
        }
        
        session_start();
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            break;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($input['id'] ?? 0);
        
        if (!$id || !$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            break;
        }
        
        $data = getBlogPosts($dataFile);
        $found = false;
        
        foreach ($data['posts'] as &$post) {
            if ($post['id'] === $id) {
                $post['title'] = $input['title'] ?? $post['title'];
                $post['excerpt'] = $input['excerpt'] ?? $post['excerpt'];
                $post['content'] = $input['content'] ?? $post['content'];
                $post['tag'] = $input['tag'] ?? $post['tag'];
                $post['facebook_url'] = $input['facebook_url'] ?? $post['facebook_url'];
                $post['featured'] = $input['featured'] ?? $post['featured'];
                $found = true;
                break;
            }
        }
        
        if ($found && saveBlogPosts($dataFile, $data)) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Post not found or save failed']);
        }
        break;
        
    case 'delete':
        // Delete a post
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
        }
        
        session_start();
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            break;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($input['id'] ?? 0);
        
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            break;
        }
        
        $data = getBlogPosts($dataFile);
        $found = false;
        
        foreach ($data['posts'] as $key => $post) {
            if ($post['id'] === $id) {
                unset($data['posts'][$key]);
                $found = true;
                break;
            }
        }
        
        // Re-index array
        $data['posts'] = array_values($data['posts']);
        
        if ($found && saveBlogPosts($dataFile, $data)) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Post not found or delete failed']);
        }
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
}
?>
