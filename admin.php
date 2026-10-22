<?php
require_once 'config.php';

requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Admin - PaGidi Expressions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .admin-header {
            background: white;
            padding: 20px 30px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .back-link {
            color: #666;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.2s;
        }
        
        .back-link:hover {
            color: #667eea;
        }
        
        .admin-title h1 {
            font-size: 24px;
            color: #333;
        }
        
        .admin-title p {
            color: #666;
            font-size: 14px;
            margin-top: 4px;
        }
        
        .admin-content {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .main-column {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card h3 {
            font-size: 16px;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }
        
        .form-group input[type="text"],
        .form-group input[type="url"],
        .form-group input[type="date"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .checkbox-group label {
            margin: 0;
            cursor: pointer;
            font-weight: normal;
        }
        
        #editor {
            min-height: 400px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        
        .ql-toolbar {
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
        }
        
        .ql-container {
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
            min-height: 350px;
        }
        
        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        
        .upload-area:hover {
            border-color: #667eea;
        }
        
        .upload-area i {
            font-size: 32px;
            color: #999;
            margin-bottom: 10px;
        }
        
        .upload-area p {
            color: #666;
            font-size: 14px;
        }
        
        .upload-area small {
            color: #999;
            display: block;
            margin-top: 8px;
        }
        
        .image-preview {
            margin-top: 15px;
            display: none;
        }
        
        .image-preview img {
            max-width: 100%;
            border-radius: 6px;
        }
        
        .btn {
            padding: 10px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .posts-list {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .posts-list h2 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .post-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .post-item:last-child {
            border-bottom: none;
        }
        
        .post-info h4 {
            margin: 0 0 5px 0;
            color: #333;
        }
        
        .post-info p {
            margin: 0;
            color: #666;
            font-size: 13px;
        }
        
        .post-actions {
            display: flex;
            gap: 10px;
        }
        
        .post-actions .btn {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .btn-edit {
            background: #28a745;
            color: white;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-draft {
            background: #ffc107;
            color: #333;
        }
        
        .status-published {
            background: #28a745;
            color: white;
        }
        
        .featured-badge {
            background: #667eea;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 8px;
        }
        
        .message {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .loading::after {
            content: 'Loading...';
        }
        
        @media (max-width: 1024px) {
            .admin-content {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                order: 2;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="admin-header-left">
            <a href="index.html" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to site
            </a>
            <div class="admin-title">
                <h1>Blog Admin</h1>
                <p>Manage your blog posts</p>
            </div>
        </div>
        <div>
            <a href="admin-login.php?logout=1" class="btn btn-secondary">Logout</a>
        </div>
    </div>
    
    <!-- Posts List View -->
    <div id="posts-view" class="posts-list" style="max-width: 1400px; margin: 30px auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>All Posts</h2>
            <button class="btn btn-primary" onclick="showEditor()">
                <i class="fas fa-plus"></i> New Post
            </button>
        </div>
        
        <div id="message" class="message"></div>
        <div class="loading" id="loading"></div>
        
        <div id="posts-container"></div>
    </div>
    
    <!-- Editor View -->
    <div id="editor-view" class="admin-content" style="display: none;">
        <div class="main-column">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 id="editor-title">New Post</h2>
                <button class="btn btn-secondary" onclick="showPostsList()">
                    <i class="fas fa-arrow-left"></i> Back to posts
                </button>
            </div>
            
            <div id="editor-message" class="message"></div>
            
            <form id="post-form">
                <input type="hidden" id="post-id" value="">
                
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" required placeholder="Enter post title">
                </div>
                
                <div class="form-group">
                    <label for="excerpt">Excerpt</label>
                    <textarea id="excerpt" placeholder="A short hook that makes readers want to click"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="slug">URL Slug</label>
                    <input type="text" id="slug" placeholder="/blog/post-slug" readonly>
                </div>
                
                <div class="form-group">
                    <label for="content">Content *</label>
                    <div id="editor"></div>
                    <textarea id="content" style="display: none;" required></textarea>
                </div>
            </form>
        </div>
        
        <div class="sidebar">
            <div class="card">
                <h3>Visibility</h3>
                <div class="checkbox-group">
                    <input type="checkbox" id="status-draft" name="status" value="draft" checked>
                    <label for="status-draft">Draft</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="status-published" name="status" value="published">
                    <label for="status-published">Published</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="featured">
                    <label for="featured">Featured Post</label>
                </div>
                <div class="form-group" style="margin-top: 15px;">
                    <label for="published_at">Publish Date</label>
                    <input type="date" id="published_at">
                </div>
            </div>
            
            <div class="card">
                <h3>Organize</h3>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category">
                        <option value="">No category</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="author">Author</label>
                    <input type="text" id="author" value="PaGidi">
                </div>
            </div>
            
            <div class="card">
                <h3>Cover Image</h3>
                <div class="upload-area" onclick="document.getElementById('cover-image').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Drag & drop, or browse to upload</p>
                    <small>PNG, JPG, WEBP up to 2MB</small>
                </div>
                <input type="file" id="cover-image" accept="image/png,image/jpeg,image/webp" style="display: none;" onchange="handleImageUpload(this)">
                <div class="image-preview" id="image-preview">
                    <img id="preview-img" src="" alt="Preview">
                </div>
                <input type="hidden" id="cover_image_url">
            </div>
            
            <div class="card">
                <h3>SEO & Social</h3>
                <div class="form-group">
                    <label for="meta_title">Meta Title</label>
                    <input type="text" id="meta_title" placeholder="SEO title">
                </div>
                <div class="form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea id="meta_description" placeholder="SEO description" style="min-height: 80px;"></textarea>
                </div>
                <div class="form-group">
                    <label for="meta_keywords">Meta Keywords</label>
                    <input type="text" id="meta_keywords" placeholder="keyword1, keyword2, keyword3">
                </div>
            </div>
            
            <div class="card">
                <div class="action-buttons">
                    <button type="button" class="btn btn-secondary" onclick="showPostsList()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="savePost()">Save Post</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script>
        let quill;
        let categories = [];
        
        // Initialize Quill editor
        document.addEventListener('DOMContentLoaded', function() {
            quill = new Quill('#editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['link', 'blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['clean']
                    ]
                }
            });
            
            // Sync Quill content to textarea
            quill.on('text-change', function() {
                document.getElementById('content').value = quill.root.innerHTML;
            });
            
            // Load categories
            loadCategories();
            
            // Load posts
            loadPosts();
            
            // Auto-generate slug from title
            document.getElementById('title').addEventListener('input', function() {
                const slug = this.value.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                document.getElementById('slug').value = '/blog/' + slug;
            });
            
            // Handle status checkboxes
            document.getElementById('status-draft').addEventListener('change', function() {
                if (this.checked) document.getElementById('status-published').checked = false;
            });
            
            document.getElementById('status-published').addEventListener('change', function() {
                if (this.checked) {
                    document.getElementById('status-draft').checked = false;
                    document.getElementById('published_at').value = new Date().toISOString().split('T')[0];
                }
            });
        });
        
        function loadCategories() {
            fetch('api/blog.php?action=get_categories')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        categories = data.categories;
                        const select = document.getElementById('category');
                        categories.forEach(cat => {
                            const option = document.createElement('option');
                            option.value = cat.name;
                            option.textContent = cat.name;
                            select.appendChild(option);
                        });
                    }
                });
        }
        
        function loadPosts() {
            document.getElementById('loading').style.display = 'block';
            
            fetch('api/blog.php?action=get_posts')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('loading').style.display = 'none';
                    if (data.success) {
                        renderPosts(data.posts);
                    }
                });
        }
        
        function renderPosts(posts) {
            const container = document.getElementById('posts-container');
            
            if (posts.length === 0) {
                container.innerHTML = '<p style="color: #666; text-align: center; padding: 40px;">No posts yet. Create your first post!</p>';
                return;
            }
            
            container.innerHTML = posts.map(post => `
                <div class="post-item">
                    <div class="post-info">
                        <h4>
                            ${escapeHtml(post.title)}
                            ${post.featured ? '<span class="featured-badge">Featured</span>' : ''}
                        </h4>
                        <p>
                            <span class="status-badge status-${post.status}">${post.status}</span>
                            ${post.category ? ` • ${escapeHtml(post.category)}` : ''}
                            ${post.published_at ? ` • ${new Date(post.published_at).toLocaleDateString()}` : ''}
                        </p>
                    </div>
                    <div class="post-actions">
                        <button class="btn btn-edit" onclick="editPost(${post.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-delete" onclick="deletePost(${post.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }
        
        function showEditor() {
            document.getElementById('posts-view').style.display = 'none';
            document.getElementById('editor-view').style.display = 'grid';
            document.getElementById('editor-title').textContent = 'New Post';
            resetForm();
        }
        
        function showPostsList() {
            document.getElementById('editor-view').style.display = 'none';
            document.getElementById('posts-view').style.display = 'block';
            loadPosts();
        }
        
        function editPost(id) {
            document.getElementById('loading').style.display = 'block';
            
            fetch(`api/blog.php?action=get_post&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('loading').style.display = 'none';
                    if (data.success) {
                        const post = data.post;
                        
                        document.getElementById('post-id').value = post.id;
                        document.getElementById('title').value = post.title;
                        document.getElementById('excerpt').value = post.excerpt || '';
                        document.getElementById('slug').value = post.slug;
                        document.getElementById('author').value = post.author;
                        document.getElementById('category').value = post.category || '';
                        document.getElementById('meta_title').value = post.meta_title || '';
                        document.getElementById('meta_description').value = post.meta_description || '';
                        document.getElementById('meta_keywords').value = post.meta_keywords || '';
                        
                        quill.root.innerHTML = post.content;
                        document.getElementById('content').value = post.content;
                        
                        document.getElementById('status-draft').checked = post.status === 'draft';
                        document.getElementById('status-published').checked = post.status === 'published';
                        document.getElementById('featured').checked = post.featured;
                        
                        if (post.published_at) {
                            document.getElementById('published_at').value = post.published_at.split(' ')[0];
                        }
                        
                        if (post.cover_image) {
                            document.getElementById('cover_image_url').value = post.cover_image;
                            document.getElementById('preview-img').src = post.cover_image;
                            document.getElementById('image-preview').style.display = 'block';
                        }
                        
                        document.getElementById('editor-title').textContent = 'Edit Post';
                        document.getElementById('posts-view').style.display = 'none';
                        document.getElementById('editor-view').style.display = 'grid';
                    }
                });
        }
        
        function deletePost(id) {
            if (!confirm('Are you sure you want to delete this post?')) return;
            
            fetch('api/blog.php?action=delete_post', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showMessage('Post deleted successfully!', 'success');
                    loadPosts();
                } else {
                    showMessage(data.error || 'Error deleting post', 'error');
                }
            });
        }
        
        function savePost() {
            const postId = document.getElementById('post-id').value;
            const isEdit = postId !== '';

            const featuredCheckbox = document.getElementById('featured');
            const featuredValue = featuredCheckbox ? featuredCheckbox.checked : false;

            const postData = {
                id: isEdit ? parseInt(postId) : null,
                title: document.getElementById('title').value,
                excerpt: document.getElementById('excerpt').value,
                content: quill.root.innerHTML,
                slug: document.getElementById('slug').value,
                author: document.getElementById('author').value,
                category: document.getElementById('category').value,
                status: document.getElementById('status-published').checked ? 'published' : 'draft',
                featured: featuredValue,
                published_at: document.getElementById('published_at').value || null,
                meta_title: document.getElementById('meta_title').value,
                meta_description: document.getElementById('meta_description').value,
                meta_keywords: document.getElementById('meta_keywords').value,
                cover_image: document.getElementById('cover_image_url').value
            };
            
            if (!postData.title) {
                showMessage('Title is required', 'error');
                return;
            }
            
            if (!postData.content) {
                showMessage('Content is required', 'error');
                return;
            }
            
            const action = isEdit ? 'update_post' : 'create_post';
            
            fetch(`api/blog.php?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(postData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showMessage(isEdit ? 'Post updated successfully!' : 'Post created successfully!', 'success');
                    setTimeout(() => showPostsList(), 1000);
                } else {
                    showMessage(data.error || 'Error saving post', 'error');
                }
            });
        }
        
        function handleImageUpload(input) {
            if (input.files && input.files[0]) {
                const formData = new FormData();
                formData.append('image', input.files[0]);
                
                fetch('api/blog.php?action=upload_image', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('cover_image_url').value = data.url;
                        document.getElementById('preview-img').src = data.url;
                        document.getElementById('image-preview').style.display = 'block';
                    } else {
                        showMessage(data.error || 'Error uploading image', 'error');
                    }
                });
            }
        }
        
        function resetForm() {
            document.getElementById('post-form').reset();
            document.getElementById('post-id').value = '';
            quill.root.innerHTML = '';
            document.getElementById('content').value = '';
            document.getElementById('status-draft').checked = true;
            document.getElementById('status-published').checked = false;
            document.getElementById('featured').checked = false;
            document.getElementById('published_at').value = '';
            document.getElementById('cover_image_url').value = '';
            document.getElementById('image-preview').style.display = 'none';
        }
        
        function showMessage(text, type) {
            const messageEl = document.getElementById('editor-message');
            messageEl.textContent = text;
            messageEl.className = `message ${type}`;
            messageEl.style.display = 'block';
            
            setTimeout(() => {
                messageEl.style.display = 'none';
            }, 5000);
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
