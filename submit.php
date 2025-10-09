<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$auth = new Auth();
$functions = new Functions();

// Require login for content submission
$auth->requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $functions->sanitizeInput($_POST['title'] ?? '');
    $content_type = $_POST['content_type'] ?? '';
    $description = $functions->sanitizeInput($_POST['description'] ?? '');
    $content_data = [];
    
    // Validate required fields
    if (empty($title) || empty($content_type) || empty($description)) {
        $error = 'Please fill in all required fields';
    } else {
        // Prepare content data based on type
        switch ($content_type) {
            case 'guide':
                $content_data = [
                    'content' => $functions->sanitizeInput($_POST['content'] ?? ''),
                    'category_id' => intval($_POST['category_id'] ?? 0),
                    'content_type_id' => 1 // Guide type
                ];
                break;
            case 'video':
                $content_data = [
                    'video_url' => $functions->sanitizeInput($_POST['video_url'] ?? ''),
                    'platform' => $functions->sanitizeInput($_POST['platform'] ?? ''),
                    'creator_name' => $functions->sanitizeInput($_POST['creator_name'] ?? ''),
                    'category_id' => intval($_POST['category_id'] ?? 0)
                ];
                break;
            case 'news':
                $content_data = [
                    'content' => $functions->sanitizeInput($_POST['content'] ?? ''),
                    'category_id' => intval($_POST['category_id'] ?? 0)
                ];
                break;
        }
        
        if (empty($content_data)) {
            $error = 'Invalid content type';
        } else {
            // Submit content
            try {
                $database = new Database();
                $db = $database->getConnection();
                
                $stmt = $db->prepare("INSERT INTO content_submissions (title, content_type, content_data, description, submitted_by) VALUES (?, ?, ?, ?, ?)");
                $result = $stmt->execute([
                    $title,
                    $content_type,
                    json_encode($content_data),
                    $description,
                    $_SESSION['user_id']
                ]);
                
                if ($result) {
                    $success = 'Your content has been submitted for review. Thank you for contributing to the community!';
                } else {
                    $error = 'Failed to submit content. Please try again.';
                }
            } catch (Exception $e) {
                $error = 'An error occurred: ' . $e->getMessage();
            }
        }
    }
}

$categories = $functions->getCategories();
$page_title = 'Submit Content - CODM Community Hub';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container nav-container">
            <div class="logo">
                <i class="fas fa-crosshairs"></i>
                <span>CODM HUB</span>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php" class="nav-link">Home</a></li>
                    <li><a href="guides.php" class="nav-link">Guides</a></li>
                    <li><a href="videos.php" class="nav-link">Videos</a></li>
                    <li><a href="news.php" class="nav-link">News</a></li>
                    <li><a href="submit.php" class="nav-link active">Submit Content</a></li>
                    <li><a href="about.php" class="nav-link">About</a></li>
                    <li><a href="profile.php" class="nav-link">Profile</a></li>
                    <?php if ($auth->hasRole('admin')): ?>
                        <li><a href="admin/" class="nav-link">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="nav-link">Logout</a></li>
                </ul>
            </nav>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Submit Content</h1>
            <p>Share your best gameplay clips, guides, or suggestions with the CODM community.</p>
        </div>
    </section>

    <!-- Submission Form -->
    <section class="section">
        <div class="container">
            <div class="form-container">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="title">Title *</label>
                        <input type="text" id="title" name="title" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="content_type">Content Type *</label>
                        <select id="content_type" name="content_type" class="form-control" required onchange="toggleContentFields()">
                            <option value="">Select content type</option>
                            <option value="guide" <?php echo ($_POST['content_type'] ?? '') === 'guide' ? 'selected' : ''; ?>>Guide/Tutorial</option>
                            <option value="video" <?php echo ($_POST['content_type'] ?? '') === 'video' ? 'selected' : ''; ?>>Video Content</option>
                            <option value="news" <?php echo ($_POST['content_type'] ?? '') === 'news' ? 'selected' : ''; ?>>News Article</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category_id">Category *</label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            <option value="">Select category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo ($_POST['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Guide/News Content -->
                    <div id="text-content-fields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label" for="content">Content *</label>
                            <textarea id="content" name="content" class="form-control" rows="10" 
                                      placeholder="Write your guide or article content here..."><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Video Content -->
                    <div id="video-content-fields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label" for="video_url">Video URL *</label>
                            <input type="url" id="video_url" name="video_url" class="form-control" 
                                   value="<?php echo htmlspecialchars($_POST['video_url'] ?? ''); ?>" 
                                   placeholder="https://youtube.com/watch?v=... or https://tiktok.com/@user/video/...">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="platform">Platform *</label>
                            <select id="platform" name="platform" class="form-control">
                                <option value="">Select platform</option>
                                <option value="youtube" <?php echo ($_POST['platform'] ?? '') === 'youtube' ? 'selected' : ''; ?>>YouTube</option>
                                <option value="tiktok" <?php echo ($_POST['platform'] ?? '') === 'tiktok' ? 'selected' : ''; ?>>TikTok</option>
                                <option value="vimeo" <?php echo ($_POST['platform'] ?? '') === 'vimeo' ? 'selected' : ''; ?>>Vimeo</option>
                                <option value="other" <?php echo ($_POST['platform'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="creator_name">Creator Name</label>
                            <input type="text" id="creator_name" name="creator_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($_POST['creator_name'] ?? ''); ?>" 
                                   placeholder="Name of the video creator">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Description *</label>
                        <textarea id="description" name="description" class="form-control" rows="4" 
                                  placeholder="Brief description of your content..." required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" name="terms" value="1" required>
                            I agree to the <a href="terms.php" style="color: var(--accent);">Terms of Service</a> and confirm this content is original or I have permission to share it.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i> Submit Content
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Submission Guidelines -->
    <section class="section">
        <div class="container">
            <h2 class="section-title"><i class="fas fa-question-circle"></i> Submission Guidelines</h2>
            
            <div class="guidelines-grid">
                <div class="guideline-card">
                    <div class="guideline-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="guideline-content">
                        <h3>Video Quality</h3>
                        <p>Ensure your videos are at least 720p resolution and have clear audio.</p>
                    </div>
                </div>
                
                <div class="guideline-card">
                    <div class="guideline-icon">
                        <i class="fas fa-copyright"></i>
                    </div>
                    <div class="guideline-content">
                        <h3>Original Content</h3>
                        <p>Only submit content that you've created yourself or have permission to share.</p>
                    </div>
                </div>
                
                <div class="guideline-card">
                    <div class="guideline-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="guideline-content">
                        <h3>Appropriate Content</h3>
                        <p>Content must follow CODM's terms of service and community guidelines.</p>
                    </div>
                </div>
                
                <div class="guideline-card">
                    <div class="guideline-icon">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    <div class="guideline-content">
                        <h3>Attribution</h3>
                        <p>We will credit you as the content creator when featuring your work.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>CODM Hub</h3>
                    <p>Your all-in-one platform for Call of Duty: Mobile guides, news, and community content.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-discord"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="guides.php">Guides</a></li>
                        <li><a href="videos.php">Videos</a></li>
                        <li><a href="news.php">News</a></li>
                        <li><a href="submit.php">Submit Content</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Resources</h3>
                    <ul class="footer-links">
                        <li><a href="#">Weapon Stats</a></li>
                        <li><a href="#">Map Strategies</a></li>
                        <li><a href="#">Ranked Tips</a></li>
                        <li><a href="#">Esports</a></li>
                        <li><a href="#">Patch Notes</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Support</h3>
                    <ul class="footer-links">
                        <li><a href="contact.php">Contact Us</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="terms.php">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2023 CODM Community Hub. This is a fan-made website and is not affiliated with Activision or Call of Duty.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script>
        function toggleContentFields() {
            const contentType = document.getElementById('content_type').value;
            const textFields = document.getElementById('text-content-fields');
            const videoFields = document.getElementById('video-content-fields');
            
            // Hide all fields first
            textFields.style.display = 'none';
            videoFields.style.display = 'none';
            
            // Show relevant fields
            if (contentType === 'guide' || contentType === 'news') {
                textFields.style.display = 'block';
            } else if (contentType === 'video') {
                videoFields.style.display = 'block';
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleContentFields();
        });
    </script>
</body>
</html>
