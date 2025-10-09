-- CODM Community Hub Database Schema
CREATE DATABASE IF NOT EXISTS codm_community_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE codm_community_hub;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'moderator', 'content_creator', 'member') DEFAULT 'member',
    status ENUM('active', 'suspended', 'banned') DEFAULT 'active',
    avatar VARCHAR(255) DEFAULT NULL,
    bio TEXT,
    social_links JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(7) DEFAULT '#fdbb2d',
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Content types
CREATE TABLE content_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Guides table
CREATE TABLE guides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    author_id INT NOT NULL,
    category_id INT NOT NULL,
    content_type_id INT NOT NULL,
    status ENUM('draft', 'pending', 'published', 'archived') DEFAULT 'draft',
    featured_image VARCHAR(255),
    meta_description TEXT,
    tags JSON,
    view_count INT DEFAULT 0,
    like_count INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (content_type_id) REFERENCES content_types(id) ON DELETE RESTRICT
);

-- Videos table
CREATE TABLE videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    video_url VARCHAR(500) NOT NULL,
    thumbnail_url VARCHAR(500),
    platform ENUM('youtube', 'tiktok', 'vimeo', 'other') NOT NULL,
    creator_name VARCHAR(100),
    creator_url VARCHAR(500),
    category_id INT NOT NULL,
    status ENUM('pending', 'published', 'rejected') DEFAULT 'pending',
    view_count INT DEFAULT 0,
    like_count INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    submitted_by INT,
    approved_by INT,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (submitted_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- News table
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    author_id INT NOT NULL,
    category_id INT NOT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    featured_image VARCHAR(255),
    meta_description TEXT,
    tags JSON,
    view_count INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

-- Content submissions table
CREATE TABLE content_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content_type ENUM('guide', 'video', 'news', 'other') NOT NULL,
    content_data JSON NOT NULL,
    description TEXT,
    submitted_by INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    reviewed_by INT,
    review_notes TEXT,
    reviewed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (submitted_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Comments table
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content_id INT NOT NULL,
    content_type ENUM('guide', 'video', 'news') NOT NULL,
    user_id INT NOT NULL,
    parent_id INT DEFAULT NULL,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'spam', 'deleted') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
);

-- Likes table
CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content_id INT NOT NULL,
    content_type ENUM('guide', 'video', 'news') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (user_id, content_id, content_type),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Analytics table
CREATE TABLE analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page VARCHAR(255) NOT NULL,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Settings table
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default data
INSERT INTO categories (name, slug, description, icon, color) VALUES
('Beginner', 'beginner', 'Guides for new players', 'fas fa-user-graduate', '#28a745'),
('Loadouts', 'loadouts', 'Weapon loadouts and builds', 'fas fa-gun', '#dc3545'),
('Gameplay', 'gameplay', 'Advanced gameplay techniques', 'fas fa-gamepad', '#007bff'),
('News', 'news', 'Game updates and news', 'fas fa-newspaper', '#fdbb2d'),
('Highlights', 'highlights', 'Best plays and montages', 'fas fa-fire', '#ff6b6b'),
('Tutorials', 'tutorials', 'Step-by-step tutorials', 'fas fa-book', '#6f42c1');

INSERT INTO content_types (name, slug, description, icon) VALUES
('Guide', 'guide', 'Written guides and tutorials', 'fas fa-book'),
('Video', 'video', 'Video content and tutorials', 'fas fa-play-circle'),
('News', 'news', 'News articles and updates', 'fas fa-newspaper'),
('Loadout', 'loadout', 'Weapon loadout recommendations', 'fas fa-gun');

INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'CODM Community Hub', 'string', 'Website name'),
('site_description', 'Your all-in-one platform for Call of Duty: Mobile guides, news, and community content', 'string', 'Website description'),
('admin_email', 'admin@codmcommunity.com', 'string', 'Admin contact email'),
('contact_email', 'contact@codmcommunity.com', 'string', 'Public contact email'),
('allow_registration', 'true', 'boolean', 'Allow public user registration'),
('allow_guide_submissions', 'true', 'boolean', 'Allow guide submissions'),
('allow_video_submissions', 'true', 'boolean', 'Allow video submissions'),
('allow_news_submissions', 'false', 'boolean', 'Allow news submissions'),
('default_content_status', 'pending', 'string', 'Default status for new content'),
('max_file_size', '10485760', 'number', 'Maximum file upload size in bytes (10MB)');

-- Create indexes for better performance
CREATE INDEX idx_guides_status ON guides(status);
CREATE INDEX idx_guides_published ON guides(published_at);
CREATE INDEX idx_guides_author ON guides(author_id);
CREATE INDEX idx_guides_category ON guides(category_id);
CREATE INDEX idx_videos_status ON videos(status);
CREATE INDEX idx_videos_platform ON videos(platform);
CREATE INDEX idx_news_status ON news(status);
CREATE INDEX idx_news_published ON news(published_at);
CREATE INDEX idx_comments_content ON comments(content_id, content_type);
CREATE INDEX idx_analytics_page ON analytics(page);
CREATE INDEX idx_analytics_date ON analytics(created_at);
