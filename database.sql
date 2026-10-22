-- PaGidi Expressions Blog Database Schema

-- Posts table
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    author VARCHAR(100) DEFAULT 'PaGidi',
    category VARCHAR(100),
    status ENUM('draft', 'published') DEFAULT 'draft',
    featured BOOLEAN DEFAULT FALSE,
    cover_image VARCHAR(255),
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published_at TIMESTAMP NULL
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default categories
INSERT INTO categories (name, slug, description) VALUES
('Expressions', 'expressions', 'Personal expressions and thoughts'),
('Mental Health', 'mental-health', 'Mental health and wellness topics'),
('Life Stories', 'life-stories', 'Personal stories and experiences')
ON DUPLICATE KEY UPDATE name=name;

-- Index for faster queries
CREATE INDEX idx_status ON posts(status);
CREATE INDEX idx_featured ON posts(featured);
CREATE INDEX idx_published_at ON posts(published_at);
CREATE INDEX idx_category ON posts(category);
