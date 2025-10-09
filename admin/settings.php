<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
$functions = new Functions();

// Require admin access
$auth->requireRole('admin');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name' => $_POST['site_name'] ?? '',
        'site_description' => $_POST['site_description'] ?? '',
        'admin_email' => $_POST['admin_email'] ?? '',
        'contact_email' => $_POST['contact_email'] ?? '',
        'default_content_status' => $_POST['default_content_status'] ?? 'pending',
        'allow_registration' => isset($_POST['allow_registration']) ? ($_POST['allow_registration'] === 'true') : false,
        'allow_guide_submissions' => isset($_POST['allow_guide_submissions']),
        'allow_video_submissions' => isset($_POST['allow_video_submissions']),
        'allow_news_submissions' => isset($_POST['allow_news_submissions'])
    ];
    
    $success = true;
    $errors = [];
    
    foreach ($settings as $key => $value) {
        $type = is_bool($value) ? 'boolean' : 'string';
        if (!$functions->setSetting($key, $value, $type)) {
            $success = false;
            $errors[] = "Failed to update {$key}";
        }
    }
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Settings saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Some settings could not be saved: ' . implode(', ', $errors)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
