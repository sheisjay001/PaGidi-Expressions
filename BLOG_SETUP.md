# Blog System Setup Guide

Your custom JSON-based blog system is now integrated! Here's how to use it:

## Files Created

1. **blog-data.json** - Stores all blog posts in JSON format
2. **blog-api.php** - PHP backend API for managing blog posts
3. **blog.php** - Dynamic blog page (replaces blog.html)
4. **admin-login.php** - Admin login page
5. **admin.php** - Admin panel for managing posts

## How to Access

### View the Blog
- Go to: `http://localhost/PaGidi%20Expressions/blog.php`
- This will display all featured blog posts dynamically

### Admin Panel
- Go to: `http://localhost/PaGidi%20Expressions/admin-login.php`
- Default password: `pagidi2025`
- **IMPORTANT**: Change the password in admin-login.php (line 7) to something secure

### Manage Posts
After logging in, you can:
- Add new blog posts
- Edit existing posts
- Delete posts
- Mark posts as "Featured" (they will appear on the main blog page)

## Security Note

The current authentication is simple (single password). For production, consider:
- Using a stronger password
- Implementing proper session management
- Adding CSRF protection (already included in contact.php, can be added to admin)

## Blog Post Structure

Each post in blog-data.json contains:
- `id` - Unique identifier
- `title` - Post title
- `excerpt` - Short description (auto-generated if blank)
- `content` - Full post content
- `tag` - Category/tag (default: #Expressions)
- `date` - Publication date
- `author` - Author name
- `facebook_url` - Link to Facebook post (optional)
- `featured` - Whether to show on main blog page

## Next Steps

1. Test the blog by visiting blog.php
2. Login to admin panel and try adding/editing posts
3. Change the default password in admin-login.php
4. Optionally delete the old blog.html file if you no longer need it
