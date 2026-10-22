<?php
require_once 'config.php';

header('Content-Type: application/xml; charset=utf-8');

$urls = [
    [
        'loc' => SITE_URL . '/',
        'priority' => '1.0',
        'changefreq' => 'weekly',
    ],
    [
        'loc' => SITE_URL . '/blog.html',
        'priority' => '0.9',
        'changefreq' => 'daily',
    ],
];

try {
    $db = getDBConnection();
    if ($db) {
        $stmt = $db->query("SELECT slug, published_at, updated_at FROM posts WHERE status = 'published' ORDER BY published_at DESC");
        foreach ($stmt->fetchAll() as $post) {
            $lastmod = $post['updated_at'] ?: $post['published_at'];
            $urls[] = [
                'loc' => SITE_URL . '/blog-post.php?slug=' . urlencode($post['slug']),
                'lastmod' => $lastmod ? date('c', strtotime($lastmod)) : null,
                'priority' => '0.8',
                'changefreq' => 'monthly',
            ];
        }
    }
} catch (Exception $e) {
    error_log('Sitemap generation failed: ' . $e->getMessage());
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($urls as $url) {
    echo "  <url>\n";
    echo '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1) . "</loc>\n";
    if (!empty($url['lastmod'])) {
        echo '    <lastmod>' . htmlspecialchars($url['lastmod'], ENT_XML1) . "</lastmod>\n";
    }
    echo '    <changefreq>' . $url['changefreq'] . "</changefreq>\n";
    echo '    <priority>' . $url['priority'] . "</priority>\n";
    echo "  </url>\n";
}

echo '</urlset>' . "\n";
?>
