<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$auth = new Auth();
$functions = new Functions();

// Track page view
$functions->trackPageView('home', $auth->isLoggedIn() ? $_SESSION['user_id'] : null);

// Get featured content
$featured_guides = $functions->getGuides(3, 0, 'published');
$featured_videos = $functions->getVideos(6, 0, 'published');
$latest_news = $functions->getNews(3, 0, 'published');
$categories = $functions->getCategories();

$page_title = 'CODM Community Hub';
$page_description = 'Your all-in-one platform for Call of Duty: Mobile guides, news, and community content';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
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
                    <li><a href="index.php" class="nav-link active">Home</a></li>
                    <li><a href="guides.php" class="nav-link">Guides</a></li>
                    <li><a href="videos.php" class="nav-link">Videos</a></li>
                    <li><a href="news.php" class="nav-link">News</a></li>
                    <li><a href="submit.php" class="nav-link">Submit Content</a></li>
                    <li><a href="about.php" class="nav-link">About</a></li>
                    <?php if ($auth->isLoggedIn()): ?>
                        <li><a href="profile.php" class="nav-link">Profile</a></li>
                        <?php if ($auth->hasRole('admin')): ?>
                            <li><a href="admin/" class="nav-link">Admin</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php" class="nav-link">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="nav-link">Login</a></li>
                        <li><a href="register.php" class="nav-link">Register</a></li>
                    <?php endif; ?>
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
            <h1>CODM Community Hub</h1>
            <p>Your all-in-one platform for Call of Duty: Mobile guides, news, and top community content. Join thousands of players improving their skills and sharing their passion.</p>
            <a href="guides.php" class="cta-button">Explore Guides <i class="fas fa-arrow-right"></i></a>
        </div>
    </section>

    <!-- Featured Guides -->
    <section class="section">
        <div class="container">
            <h2 class="section-title"><i class="fas fa-book"></i> Featured Guides</h2>
            <div class="guides-grid">
                <?php foreach ($featured_guides as $guide): ?>
                <div class="guide-card">
                    <div class="guide-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="guide-content">
                        <h3><?php echo htmlspecialchars($guide['title']); ?></h3>
                        <p><?php echo $functions->truncateText($guide['excerpt'], 100); ?></p>
                        <div class="guide-meta">
                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($guide['author_name']); ?></span>
                            <span><i class="fas fa-eye"></i> <?php echo $guide['view_count']; ?></span>
                        </div>
                        <a href="guide.php?id=<?php echo $guide['id']; ?>" class="guide-link">Read Guide <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Videos -->
    <section class="section">
        <div class="container">
            <h2 class="section-title"><i class="fas fa-fire"></i> Trending Videos</h2>
            <div class="video-grid">
                <?php foreach ($featured_videos as $video): ?>
                <div class="video-card">
                    <div class="video-thumbnail">
                        <?php if ($video['platform'] === 'youtube'): ?>
                            <i class="fab fa-youtube"></i>
                        <?php elseif ($video['platform'] === 'tiktok'): ?>
                            <i class="fab fa-tiktok"></i>
                        <?php else: ?>
                            <i class="fas fa-play-circle"></i>
                        <?php endif; ?>
                    </div>
                    <div class="video-info">
                        <h3><?php echo htmlspecialchars($video['title']); ?></h3>
                        <div class="video-meta">
                            <span><?php echo htmlspecialchars($video['creator_name']); ?></span>
                            <span><?php echo $functions->timeAgo($video['created_at']); ?></span>
                        </div>
                        <div class="video-stats">
                            <span><i class="fas fa-eye"></i> <?php echo $video['view_count']; ?></span>
                            <span><i class="fas fa-heart"></i> <?php echo $video['like_count']; ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Latest News -->
    <section class="section">
        <div class="container">
            <h2 class="section-title"><i class="fas fa-newspaper"></i> Latest News</h2>
            <div class="news-list">
                <?php foreach ($latest_news as $news): ?>
                <div class="news-item">
                    <div class="news-date">
                        <span class="day"><?php echo date('j', strtotime($news['published_at'])); ?></span>
                        <span class="month"><?php echo strtoupper(date('M', strtotime($news['published_at']))); ?></span>
                    </div>
                    <div class="news-content">
                        <h3><a href="news.php?id=<?php echo $news['id']; ?>"><?php echo htmlspecialchars($news['title']); ?></a></h3>
                        <p><?php echo $functions->truncateText($news['excerpt'], 150); ?></p>
                        <div class="news-meta">
                            <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($news['author_name']); ?></span>
                            <span><i class="fas fa-eye"></i> <?php echo $news['view_count']; ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="section">
        <div class="container">
            <h2 class="section-title"><i class="fas fa-tags"></i> Browse by Category</h2>
            <div class="categories-grid">
                <?php foreach ($categories as $category): ?>
                <div class="category-card">
                    <div class="category-icon" style="background-color: <?php echo $category['color']; ?>">
                        <i class="<?php echo $category['icon']; ?>"></i>
                    </div>
                    <div class="category-content">
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                        <p><?php echo htmlspecialchars($category['description']); ?></p>
                        <a href="category.php?slug=<?php echo $category['slug']; ?>" class="category-link">Explore <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>
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
</body>
</html>
