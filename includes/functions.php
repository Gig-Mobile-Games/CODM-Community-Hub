<?php
require_once 'config/database.php';

class Functions {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Content Management Functions
    public function getGuides($limit = 10, $offset = 0, $status = 'published', $category = null) {
        try {
            $sql = "SELECT g.*, u.username as author_name, c.name as category_name, ct.name as content_type_name 
                    FROM guides g 
                    LEFT JOIN users u ON g.author_id = u.id 
                    LEFT JOIN categories c ON g.category_id = c.id 
                    LEFT JOIN content_types ct ON g.content_type_id = ct.id 
                    WHERE g.status = ?";
            $params = [$status];
            
            if ($category) {
                $sql .= " AND g.category_id = ?";
                $params[] = $category;
            }
            
            $sql .= " ORDER BY g.published_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function getGuide($id) {
        try {
            $stmt = $this->db->prepare("SELECT g.*, u.username as author_name, c.name as category_name, ct.name as content_type_name 
                                       FROM guides g 
                                       LEFT JOIN users u ON g.author_id = u.id 
                                       LEFT JOIN categories c ON g.category_id = c.id 
                                       LEFT JOIN content_types ct ON g.content_type_id = ct.id 
                                       WHERE g.id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
    
    public function getVideos($limit = 10, $offset = 0, $status = 'published', $platform = null) {
        try {
            $sql = "SELECT v.*, c.name as category_name 
                    FROM videos v 
                    LEFT JOIN categories c ON v.category_id = c.id 
                    WHERE v.status = ?";
            $params = [$status];
            
            if ($platform) {
                $sql .= " AND v.platform = ?";
                $params[] = $platform;
            }
            
            $sql .= " ORDER BY v.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function getNews($limit = 10, $offset = 0, $status = 'published') {
        try {
            $stmt = $this->db->prepare("SELECT n.*, u.username as author_name, c.name as category_name 
                                       FROM news n 
                                       LEFT JOIN users u ON n.author_id = u.id 
                                       LEFT JOIN categories c ON n.category_id = c.id 
                                       WHERE n.status = ? 
                                       ORDER BY n.published_at DESC LIMIT ? OFFSET ?");
            $stmt->execute([$status, $limit, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function getCategories() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order, name");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function getContentTypes() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM content_types ORDER BY name");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Analytics Functions
    public function trackPageView($page, $user_id = null) {
        try {
            $stmt = $this->db->prepare("INSERT INTO analytics (page, user_id, ip_address, user_agent, referrer) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $page,
                $user_id,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $_SERVER['HTTP_REFERER'] ?? ''
            ]);
        } catch (PDOException $e) {
            // Silently fail for analytics
        }
    }
    
    public function getAnalytics($days = 30) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as page_views,
                    COUNT(DISTINCT user_id) as unique_users,
                    COUNT(DISTINCT ip_address) as unique_visitors
                FROM analytics 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY DATE(created_at)
                ORDER BY date DESC
            ");
            $stmt->execute([$days]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function getTopPages($days = 30, $limit = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT page, COUNT(*) as views
                FROM analytics 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY page
                ORDER BY views DESC
                LIMIT ?
            ");
            $stmt->execute([$days, $limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Utility Functions
    public function generateSlug($title) {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
    
    public function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    public function formatDate($date, $format = 'M j, Y') {
        return date($format, strtotime($date));
    }
    
    public function timeAgo($datetime) {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time/60) . ' minutes ago';
        if ($time < 86400) return floor($time/3600) . ' hours ago';
        if ($time < 2592000) return floor($time/86400) . ' days ago';
        if ($time < 31536000) return floor($time/2592000) . ' months ago';
        return floor($time/31536000) . ' years ago';
    }
    
    public function truncateText($text, $length = 150) {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }
    
    // File Upload Functions
    public function uploadFile($file, $directory = 'uploads/', $allowed_types = ['jpg', 'jpeg', 'png', 'gif']) {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'message' => 'Invalid file'];
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload error'];
        }
        
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_types)) {
            return ['success' => false, 'message' => 'Invalid file type'];
        }
        
        $filename = uniqid() . '.' . $file_extension;
        $filepath = $directory . $filename;
        
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
        } else {
            return ['success' => false, 'message' => 'Failed to move uploaded file'];
        }
    }
    
    // Settings Functions
    public function getSetting($key, $default = null) {
        try {
            $stmt = $this->db->prepare("SELECT setting_value, setting_type FROM settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            $setting = $stmt->fetch();
            
            if (!$setting) {
                return $default;
            }
            
            switch ($setting['setting_type']) {
                case 'boolean':
                    return $setting['setting_value'] === 'true';
                case 'number':
                    return (int) $setting['setting_value'];
                case 'json':
                    return json_decode($setting['setting_value'], true);
                default:
                    return $setting['setting_value'];
            }
        } catch (PDOException $e) {
            return $default;
        }
    }
    
    public function setSetting($key, $value, $type = 'string') {
        try {
            $json_value = ($type === 'json') ? json_encode($value) : $value;
            
            $stmt = $this->db->prepare("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, ?) 
                                       ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), setting_type = VALUES(setting_type)");
            $stmt->execute([$key, $json_value, $type]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Search Functions
    public function searchContent($query, $type = 'all', $limit = 20) {
        $results = [];
        $query = '%' . $query . '%';
        
        try {
            if ($type === 'all' || $type === 'guides') {
                $stmt = $this->db->prepare("SELECT 'guide' as type, id, title, excerpt, created_at FROM guides WHERE (title LIKE ? OR content LIKE ?) AND status = 'published' LIMIT ?");
                $stmt->execute([$query, $query, $limit]);
                $results = array_merge($results, $stmt->fetchAll());
            }
            
            if ($type === 'all' || $type === 'videos') {
                $stmt = $this->db->prepare("SELECT 'video' as type, id, title, description as excerpt, created_at FROM videos WHERE (title LIKE ? OR description LIKE ?) AND status = 'published' LIMIT ?");
                $stmt->execute([$query, $query, $limit]);
                $results = array_merge($results, $stmt->fetchAll());
            }
            
            if ($type === 'all' || $type === 'news') {
                $stmt = $this->db->prepare("SELECT 'news' as type, id, title, excerpt, created_at FROM news WHERE (title LIKE ? OR content LIKE ?) AND status = 'published' LIMIT ?");
                $stmt->execute([$query, $query, $limit]);
                $results = array_merge($results, $stmt->fetchAll());
            }
            
            return $results;
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
