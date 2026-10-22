<?php
require_once '../config.php';

header('Content-Type: application/json');

// Start session for admin check
startSecureSession();

// Get action
$action = $_GET['action'] ?? '';

try {
    $db = getDBConnection();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    switch ($action) {
        case 'get_posts':
            // Get all posts or filter by status
            $status = $_GET['status'] ?? null;
            $featured = $_GET['featured'] ?? null;
            
            $sql = "SELECT * FROM posts";
            $params = [];
            $conditions = [];
            
            if ($status) {
                $conditions[] = "status = ?";
                $params[] = $status;
            }
            
            if ($featured === 'true') {
                $conditions[] = "featured = 1";
            }
            
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }
            
            $sql .= " ORDER BY published_at DESC, created_at DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $posts = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'posts' => $posts]);
            break;

        case 'get_post':
            // Get single post by ID or slug
            $id = $_GET['id'] ?? null;
            $slug = $_GET['slug'] ?? null;
            
            if ($id) {
                $stmt = $db->prepare("SELECT * FROM posts WHERE id = ?");
                $stmt->execute([$id]);
            } elseif ($slug) {
                $stmt = $db->prepare("SELECT * FROM posts WHERE slug = ?");
                $stmt->execute([$slug]);
            } else {
                throw new Exception('ID or slug required');
            }
            
            $post = $stmt->fetch();
            
            if (!$post) {
                throw new Exception('Post not found');
            }
            
            echo json_encode(['success' => true, 'post' => $post]);
            break;

        case 'get_categories':
            // Get all categories
            $stmt = $db->query("SELECT * FROM categories ORDER BY name");
            $categories = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'categories' => $categories]);
            break;

        case 'create_post':
            // Create new post (admin only)
            requireAdmin();
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $slug = generateSlug($data['title']);
            
            // Check if slug exists
            $stmt = $db->prepare("SELECT id FROM posts WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetch()) {
                $slug .= '-' . time();
            }
            
            $sql = "INSERT INTO posts (title, slug, excerpt, content, author, category, status, featured, cover_image, meta_title, meta_description, meta_keywords, published_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $publishedAt = ($data['status'] === 'published') ? date('Y-m-d H:i:s') : null;

            // Convert featured to boolean (handle empty strings, null, etc.)
            $featured = filter_var($data['featured'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($featured === null) {
                $featured = false;
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $data['title'],
                $slug,
                $data['excerpt'] ?? null,
                $data['content'],
                $data['author'] ?? 'PaGidi',
                $data['category'] ?? null,
                $data['status'] ?? 'draft',
                $featured,
                $data['cover_image'] ?? null,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['meta_keywords'] ?? null,
                $publishedAt
            ]);
            
            echo json_encode(['success' => true, 'id' => $db->lastInsertId(), 'slug' => $slug]);
            break;

        case 'update_post':
            // Update post (admin only)
            requireAdmin();
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                throw new Exception('Post ID required');
            }
            
            // Update slug if title changed
            $slug = $data['slug'] ?? generateSlug($data['title']);

            // Convert featured to boolean (handle empty strings, null, etc.)
            $featured = filter_var($data['featured'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($featured === null) {
                $featured = false;
            }
            
            $sql = "UPDATE posts SET
                    title = ?,
                    excerpt = ?,
                    content = ?,
                    author = ?,
                    category = ?,
                    status = ?,
                    featured = ?,
                    cover_image = ?,
                    meta_title = ?,
                    meta_description = ?,
                    meta_keywords = ?,
                    published_at = CASE
                        WHEN ? = 'published' AND published_at IS NULL THEN NOW()
                        WHEN ? = 'draft' THEN NULL
                        ELSE published_at
                    END,
                    slug = ?
                    WHERE id = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $data['title'],
                $data['excerpt'] ?? null,
                $data['content'],
                $data['author'] ?? 'PaGidi',
                $data['category'] ?? null,
                $data['status'] ?? 'draft',
                $featured,
                $data['cover_image'] ?? null,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['meta_keywords'] ?? null,
                $data['status'] ?? 'draft',
                $data['status'] ?? 'draft',
                $slug,
                $data['id']
            ]);
            
            echo json_encode(['success' => true]);
            break;

        case 'delete_post':
            // Delete post (admin only)
            requireAdmin();
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                throw new Exception('Post ID required');
            }
            
            $stmt = $db->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->execute([$data['id']]);
            
            echo json_encode(['success' => true]);
            break;

        case 'upload_image':
            // Upload image (admin only)
            requireAdmin();
            
            if (!isset($_FILES['image'])) {
                throw new Exception('No image file provided');
            }
            
            $result = handleImageUpload($_FILES['image']);
            
            if (!$result['success']) {
                throw new Exception($result['error']);
            }
            
            echo json_encode(['success' => true, 'url' => SITE_URL . '/' . $result['path']]);
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    error_log("Blog API Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
