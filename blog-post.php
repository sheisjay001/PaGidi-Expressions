<?php
require_once 'config.php';

$slug = $_GET['slug'] ?? null;

if (!$slug) {
    header('Location: blog.html');
    exit;
}

$db = getDBConnection();
if (!$db) {
    die('Database connection failed');
}

$stmt = $db->prepare("SELECT * FROM posts WHERE slug = ? AND status = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: blog.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - PaGidi Expressions</title>
    
    <?php if ($post['meta_title']): ?>
        <meta name="title" content="<?php echo htmlspecialchars($post['meta_title']); ?>">
    <?php endif; ?>
    
    <?php if ($post['meta_description']): ?>
        <meta name="description" content="<?php echo htmlspecialchars($post['meta_description']); ?>">
    <?php else: ?>
        <meta name="description" content="<?php echo htmlspecialchars($post['excerpt']); ?>">
    <?php endif; ?>
    
    <?php if ($post['meta_keywords']): ?>
        <meta name="keywords" content="<?php echo htmlspecialchars($post['meta_keywords']); ?>">
    <?php endif; ?>
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($post['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($post['excerpt']); ?>">
    <?php if ($post['cover_image']): ?>
        <meta property="og:image" content="<?php echo htmlspecialchars($post['cover_image']); ?>">
    <?php else: ?>
        <meta property="og:image" content="assets/images/hero.jpg">
    <?php endif; ?>
    <meta property="og:url" content="<?php echo SITE_URL; ?>/blog-post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>">
    <meta property="og:type" content="article">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .blog-post-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .blog-post-header {
            margin-bottom: 40px;
        }
        
        .blog-post-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .blog-post-meta span {
            margin-right: 20px;
        }
        
        .blog-post-title {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
            line-height: 1.3;
        }
        
        .blog-post-excerpt {
            font-size: 18px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .blog-post-cover {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 40px;
        }
        
        .blog-post-content {
            font-size: 18px;
            line-height: 1.8;
            color: #333;
        }
        
        .blog-post-content h2 {
            font-size: 28px;
            margin-top: 40px;
            margin-bottom: 20px;
            color: #333;
        }
        
        .blog-post-content h3 {
            font-size: 24px;
            margin-top: 30px;
            margin-bottom: 15px;
            color: #333;
        }
        
        .blog-post-content p {
            margin-bottom: 20px;
        }
        
        .blog-post-content blockquote {
            border-left: 4px solid #667eea;
            padding-left: 20px;
            margin: 30px 0;
            font-style: italic;
            color: #666;
        }
        
        .blog-post-content ul, .blog-post-content ol {
            margin-bottom: 20px;
            padding-left: 30px;
        }
        
        .blog-post-content li {
            margin-bottom: 10px;
        }
        
        .blog-post-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .blog-post-content code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
        }
        
        .blog-post-content pre {
            background: #f4f4f4;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 20px 0;
        }
        
        .blog-post-content pre code {
            background: none;
            padding: 0;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            margin-bottom: 30px;
            font-weight: 600;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .author-box {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            margin-top: 50px;
        }
        
        .author-box h3 {
            margin-bottom: 10px;
            color: #333;
        }
        
        .author-box p {
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.html" class="logo">PaGidi Expressions</a>
            <div class="nav-right" style="display: flex; align-items: center;">
                <button id="theme-toggle" class="theme-toggle" aria-label="Toggle Dark Mode">
                    <i class="fas fa-moon"></i>
                </button>
                <div class="menu-toggle" id="mobile-menu">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
            </div>
            <ul class="nav-menu">
                <li><a href="index.html" class="nav-link">Home</a></li>
                <li><a href="index.html#about" class="nav-link">About</a></li>
                <li><a href="index.html#mission" class="nav-link">Mission</a></li>
                <li><a href="blog.html" class="nav-link active">Expressions</a></li>
                <li><a href="index.html#books" class="nav-link">Books</a></li>
                <li><a href="index.html#contact" class="nav-link">Contact</a></li>
            </ul>
        </div>
    </nav>

    <div class="blog-post-container">
        <a href="blog.html" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Expressions
        </a>
        
        <div class="blog-post-header">
            <div class="blog-post-meta">
                <?php if ($post['category']): ?>
                    <span><i class="fas fa-folder"></i> <?php echo htmlspecialchars($post['category']); ?></span>
                <?php endif; ?>
                <?php if ($post['author']): ?>
                    <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['author']); ?></span>
                <?php endif; ?>
                <?php if ($post['published_at']): ?>
                    <span><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($post['published_at'])); ?></span>
                <?php endif; ?>
            </div>
            
            <h1 class="blog-post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            <?php if ($post['excerpt']): ?>
                <p class="blog-post-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
            <?php endif; ?>
        </div>
        
        <?php if ($post['cover_image']): ?>
            <img src="<?php echo htmlspecialchars($post['cover_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="blog-post-cover">
        <?php endif; ?>
        
        <div class="blog-post-content">
            <?php echo $post['content']; ?>
        </div>
        
        <div class="author-box">
            <h3>About the Author</h3>
            <p>
                <strong><?php echo htmlspecialchars($post['author']); ?></strong> is the voice behind PaGidi Expressions. 
                A Lawyer and Suicide survivor, PaGidi uses a straight-from-the-heart, non-conformist approach to discuss 
                mental health, emotions, and life experiences. Through these expressions, the goal is to help people 
                shed mental weights and find healing.
            </p>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; <span id="year">2025</span> PaGidi Expressions. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
