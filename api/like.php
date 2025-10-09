<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$auth = new Auth();
$functions = new Functions();

// Require login
if (!$auth->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please log in to like content']);
    exit;
}

$content_id = $_POST['content_id'] ?? '';
$content_type = $_POST['content_type'] ?? '';
$action = $_POST['action'] ?? '';

if (empty($content_id) || empty($content_type) || empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($action === 'like') {
        // Add like
        $stmt = $db->prepare("INSERT INTO likes (user_id, content_id, content_type) VALUES (?, ?, ?)");
        $result = $stmt->execute([$_SESSION['user_id'], $content_id, $content_type]);
        
        if ($result) {
            // Update like count
            $update_stmt = $db->prepare("UPDATE {$content_type}s SET like_count = like_count + 1 WHERE id = ?");
            $update_stmt->execute([$content_id]);
            
            // Get updated count
            $count_stmt = $db->prepare("SELECT like_count FROM {$content_type}s WHERE id = ?");
            $count_stmt->execute([$content_id]);
            $like_count = $count_stmt->fetchColumn();
            
            echo json_encode(['success' => true, 'like_count' => $like_count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to like content']);
        }
    } else {
        // Remove like
        $stmt = $db->prepare("DELETE FROM likes WHERE user_id = ? AND content_id = ? AND content_type = ?");
        $result = $stmt->execute([$_SESSION['user_id'], $content_id, $content_type]);
        
        if ($result) {
            // Update like count
            $update_stmt = $db->prepare("UPDATE {$content_type}s SET like_count = GREATEST(like_count - 1, 0) WHERE id = ?");
            $update_stmt->execute([$content_id]);
            
            // Get updated count
            $count_stmt = $db->prepare("SELECT like_count FROM {$content_type}s WHERE id = ?");
            $count_stmt->execute([$content_id]);
            $like_count = $count_stmt->fetchColumn();
            
            echo json_encode(['success' => true, 'like_count' => $like_count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to unlike content']);
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
