<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
$functions = new Functions();

// Require admin access
$auth->requireRole('admin');

// Get analytics data
$analytics = $functions->getAnalytics(30);
$top_pages = $functions->getTopPages(30, 10);

// Get content counts
$guides_count = count($functions->getGuides(1000, 0, 'published'));
$videos_count = count($functions->getVideos(1000, 0, 'published'));
$news_count = count($functions->getNews(1000, 0, 'published'));

// Get recent content
$recent_guides = $functions->getGuides(5, 0, 'published');
$recent_videos = $functions->getVideos(5, 0, 'published');
$recent_news = $functions->getNews(5, 0, 'published');

$page_title = 'Admin Dashboard - CODM Community Hub';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-crosshairs"></i>
            <h2>CODM Admin</h2>
        </div>
        <div class="sidebar-menu">
            <div class="menu-item active" data-page="dashboard">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </div>
            <div class="menu-item" data-page="content">
                <i class="fas fa-file-alt"></i>
                <span>Content Management</span>
            </div>
            <div class="menu-item" data-page="videos">
                <i class="fas fa-play-circle"></i>
                <span>Video Management</span>
            </div>
            <div class="menu-item" data-page="users">
                <i class="fas fa-users"></i>
                <span>User Management</span>
            </div>
            <div class="menu-item" data-page="submissions">
                <i class="fas fa-inbox"></i>
                <span>Content Submissions</span>
            </div>
            <div class="menu-item" data-page="analytics">
                <i class="fas fa-chart-bar"></i>
                <span>Analytics</span>
            </div>
            <div class="menu-item" data-page="settings">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <button class="toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Admin Dashboard</h1>
            </div>
            <div class="header-right">
                <div class="user-profile">
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 2)); ?></div>
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content">
            <!-- Dashboard Page -->
            <div id="dashboard" class="page active">
                <h2>Dashboard Overview</h2>
                <p class="subtitle" style="color: var(--gray); margin-bottom: 20px;">Welcome to your CODM Community admin dashboard</p>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Total Visitors</h3>
                            <div class="stat-value"><?php echo array_sum(array_column($analytics, 'unique_visitors')); ?></div>
                            <div style="color: var(--success); font-size: 0.8em;">
                                <i class="fas fa-arrow-up"></i> Last 30 days
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Published Guides</h3>
                            <div class="stat-value"><?php echo $guides_count; ?></div>
                            <div style="color: var(--success); font-size: 0.8em;">
                                <i class="fas fa-arrow-up"></i> Total guides
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Videos Posted</h3>
                            <div class="stat-value"><?php echo $videos_count; ?></div>
                            <div style="color: var(--success); font-size: 0.8em;">
                                <i class="fas fa-arrow-up"></i> Total videos
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>News Articles</h3>
                            <div class="stat-value"><?php echo $news_count; ?></div>
                            <div style="color: var(--success); font-size: 0.8em;">
                                <i class="fas fa-arrow-up"></i> Total articles
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                    </div>
                </div>

                <div class="charts-container">
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title">Site Traffic</div>
                            <select class="form-control" style="width: auto;">
                                <option>Last 7 days</option>
                                <option>Last 30 days</option>
                                <option>Last 3 months</option>
                            </select>
                        </div>
                        <div class="chart-placeholder">
                            <canvas id="trafficChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title">Top Pages</div>
                        </div>
                        <div class="chart-placeholder">
                            <div class="top-pages-list">
                                <?php foreach ($top_pages as $page): ?>
                                <div class="page-item">
                                    <span class="page-name"><?php echo htmlspecialchars($page['page']); ?></span>
                                    <span class="page-views"><?php echo $page['views']; ?> views</span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <div class="chart-title">Recent Activity</div>
                    </div>
                    <div class="content-list">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_guides as $guide): ?>
                                <tr>
                                    <td><span class="badge badge-primary">Guide</span></td>
                                    <td><?php echo htmlspecialchars($guide['title']); ?></td>
                                    <td><?php echo htmlspecialchars($guide['author_name']); ?></td>
                                    <td><?php echo $functions->timeAgo($guide['published_at']); ?></td>
                                    <td><span class="status status-published">Published</span></td>
                                </tr>
                                <?php endforeach; ?>
                                
                                <?php foreach ($recent_videos as $video): ?>
                                <tr>
                                    <td><span class="badge badge-success">Video</span></td>
                                    <td><?php echo htmlspecialchars($video['title']); ?></td>
                                    <td><?php echo htmlspecialchars($video['creator_name']); ?></td>
                                    <td><?php echo $functions->timeAgo($video['created_at']); ?></td>
                                    <td><span class="status status-published">Published</span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Content Management Page -->
            <div id="content" class="page">
                <h2>Content Management</h2>
                <p class="subtitle" style="color: var(--gray); margin-bottom: 20px;">Manage guides, articles, and site content</p>

                <div class="content-tabs">
                    <div class="content-tab active" data-tab="guides">Guides</div>
                    <div class="content-tab" data-tab="news">News</div>
                </div>

                <div class="content-list">
                    <div class="content-header">
                        <div class="content-title">Published Content</div>
                        <button class="btn btn-primary" onclick="openModal('addContentModal')">
                            <i class="fas fa-plus"></i> Add New Content
                        </button>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_guides as $guide): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($guide['title']); ?></td>
                                <td><?php echo htmlspecialchars($guide['author_name']); ?></td>
                                <td><?php echo htmlspecialchars($guide['category_name']); ?></td>
                                <td><span class="status status-published">Published</span></td>
                                <td><?php echo $functions->formatDate($guide['published_at']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <div class="action-btn edit-btn" onclick="editContent(<?php echo $guide['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </div>
                                        <div class="action-btn delete-btn" onclick="deleteContent(<?php echo $guide['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Video Management Page -->
            <div id="videos" class="page">
                <h2>Video Management</h2>
                <p class="subtitle" style="color: var(--gray); margin-bottom: 20px;">Manage embedded videos from YouTube and TikTok</p>

                <div class="content-list">
                    <div class="content-header">
                        <div class="content-title">Featured Videos</div>
                        <button class="btn btn-primary" onclick="openModal('addVideoModal')">
                            <i class="fas fa-plus"></i> Add New Video
                        </button>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Creator</th>
                                <th>Platform</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Date Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_videos as $video): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($video['title']); ?></td>
                                <td><?php echo htmlspecialchars($video['creator_name']); ?></td>
                                <td><span class="badge badge-<?php echo $video['platform']; ?>"><?php echo ucfirst($video['platform']); ?></span></td>
                                <td><?php echo htmlspecialchars($video['category_name']); ?></td>
                                <td><span class="status status-published">Published</span></td>
                                <td><?php echo $functions->formatDate($video['created_at']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <div class="action-btn edit-btn" onclick="editVideo(<?php echo $video['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </div>
                                        <div class="action-btn delete-btn" onclick="deleteVideo(<?php echo $video['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- User Management Page -->
            <div id="users" class="page">
                <h2>User Management</h2>
                <p class="subtitle" style="color: var(--gray); margin-bottom: 20px;">Manage community members and permissions</p>

                <div class="content-list">
                    <div class="content-header">
                        <div class="content-title">Registered Users</div>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" class="form-control" placeholder="Search users..." style="width: 250px;">
                            <button class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Join Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo htmlspecialchars($_SESSION['username']); ?></td>
                                <td><?php echo htmlspecialchars($_SESSION['email']); ?></td>
                                <td><span class="badge badge-admin">Admin</span></td>
                                <td><?php echo $functions->formatDate(date('Y-m-d')); ?></td>
                                <td><span class="status status-published">Active</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <div class="action-btn edit-btn">
                                            <i class="fas fa-edit"></i>
                                        </div>
                                        <div class="action-btn delete-btn">
                                            <i class="fas fa-ban"></i>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Content Submissions Page -->
            <div id="submissions" class="page">
                <h2>Content Submissions</h2>
                <p class="subtitle" style="color: var(--gray); margin-bottom: 20px;">Review and approve user-submitted content</p>

                <div class="content-list">
                    <div class="content-header">
                        <div class="content-title">Pending Submissions</div>
                        <div style="display: flex; gap: 10px;">
                            <select class="form-control" style="width: auto;">
                                <option>All Types</option>
                                <option>Guides</option>
                                <option>Videos</option>
                                <option>News</option>
                            </select>
                        </div>
                    </div>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>No pending submissions</h3>
                        <p>All content has been reviewed</p>
                    </div>
                </div>
            </div>

            <!-- Analytics Page -->
            <div id="analytics" class="page">
                <h2>Analytics</h2>
                <p class="subtitle" style="color: var(--gray); margin-bottom: 20px;">Website performance and user engagement metrics</p>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Page Views</h3>
                            <div class="stat-value"><?php echo array_sum(array_column($analytics, 'page_views')); ?></div>
                            <div style="color: var(--success); font-size: 0.8em;">
                                <i class="fas fa-arrow-up"></i> Last 30 days
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Unique Visitors</h3>
                            <div class="stat-value"><?php echo array_sum(array_column($analytics, 'unique_visitors')); ?></div>
                            <div style="color: var(--success); font-size: 0.8em;">
                                <i class="fas fa-arrow-up"></i> Last 30 days
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Avg. Session Duration</h3>
                            <div class="stat-value">4m 32s</div>
                            <div style="color: var(--success); font-size: 0.8em;">
                                <i class="fas fa-arrow-up"></i> 8% from last month
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Bounce Rate</h3>
                            <div class="stat-value">42%</div>
                            <div style="color: var(--danger); font-size: 0.8em;">
                                <i class="fas fa-arrow-up"></i> 3% from last month
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="charts-container">
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title">Traffic Sources</div>
                        </div>
                        <div class="chart-placeholder">
                            Traffic Sources Chart Visualization
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title">Top Content</div>
                        </div>
                        <div class="chart-placeholder">
                            Top Content Chart Visualization
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Page -->
            <div id="settings" class="page">
                <h2>Settings</h2>
                <p class="subtitle" style="color: var(--gray); margin-bottom: 20px;">Configure your website and admin preferences</p>

                <div class="form-container">
                    <form method="POST" action="settings.php">
                        <div class="form-group">
                            <label class="form-label">Site Name</label>
                            <input type="text" class="form-control" name="site_name" value="<?php echo $functions->getSetting('site_name', 'CODM Community Hub'); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Site Description</label>
                            <textarea class="form-control" name="site_description"><?php echo $functions->getSetting('site_description', 'Your all-in-one platform for Call of Duty: Mobile guides, news, and community content.'); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Admin Email</label>
                                <input type="email" class="form-control" name="admin_email" value="<?php echo $functions->getSetting('admin_email', 'admin@codmcommunity.com'); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Contact Email</label>
                                <input type="email" class="form-control" name="contact_email" value="<?php echo $functions->getSetting('contact_email', 'contact@codmcommunity.com'); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Default Content Status</label>
                            <select class="form-control" name="default_content_status">
                                <option value="published" <?php echo $functions->getSetting('default_content_status') === 'published' ? 'selected' : ''; ?>>Published</option>
                                <option value="pending" <?php echo $functions->getSetting('default_content_status') === 'pending' ? 'selected' : ''; ?>>Pending Review</option>
                                <option value="draft" <?php echo $functions->getSetting('default_content_status') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">User Registration</label>
                            <div>
                                <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                    <input type="radio" name="allow_registration" value="true" <?php echo $functions->getSetting('allow_registration', true) ? 'checked' : ''; ?>> Allow public registration
                                </label>
                                <label style="display: flex; align-items: center; gap: 10px;">
                                    <input type="radio" name="allow_registration" value="false" <?php echo !$functions->getSetting('allow_registration', true) ? 'checked' : ''; ?>> Invite-only registration
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Content Submission</label>
                            <div>
                                <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                    <input type="checkbox" name="allow_guide_submissions" <?php echo $functions->getSetting('allow_guide_submissions', true) ? 'checked' : ''; ?>> Allow guide submissions
                                </label>
                                <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                    <input type="checkbox" name="allow_video_submissions" <?php echo $functions->getSetting('allow_video_submissions', true) ? 'checked' : ''; ?>> Allow video submissions
                                </label>
                                <label style="display: flex; align-items: center; gap: 10px;">
                                    <input type="checkbox" name="allow_news_submissions" <?php echo $functions->getSetting('allow_news_submissions', false) ? 'checked' : ''; ?>> Allow news submissions
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="padding: 12px 30px;">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>
