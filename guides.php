<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$auth = new Auth();
$functions = new Functions();

// Track page view
$functions->trackPageView('guides', $auth->isLoggedIn() ? $_SESSION['user_id'] : null);

// Get parameters
$page = max(1, intval($_GET['page'] ?? 1));
$category = $_GET['category'] ?? null;
$search = $_GET['search'] ?? '';
$limit = 12;
$offset = ($page - 1) * $limit;

// Get guides
$guides = $functions->getGuides($limit, $offset, 'published', $category);
$categories = $functions->getCategories();

// Get total count for pagination
$total_guides = count($functions->getGuides(1000, 0, 'published', $category));
$total_pages = ceil($total_guides / $limit);

$page_title = 'Guides - CODM Community Hub';
$page_description = 'Master Call of Duty: Mobile with our comprehensive guides, weapon loadouts, and strategy tips from top players.';
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
                    <li><a href="index.php" class="nav-link">Home</a></li>
                    <li><a href="guides.php" class="nav-link active">Guides</a></li>
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
            <h1>Guides & Loadouts</h1>
            <p>Master Call of Duty: Mobile with our comprehensive guides, weapon loadouts, and strategy tips from top players.</p>
        </div>
    </section>

    <!-- Search and Filters -->
    <section class="section">
        <div class="container">
            <div class="search-filters">
                <form method="GET" class="search-form">
                    <div class="search-input-group">
                        <input type="text" name="search" placeholder="Search guides..." value="<?php echo htmlspecialchars($search); ?>" class="form-control">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <div class="filter-buttons">
                    <a href="guides.php" class="filter-btn <?php echo !$category ? 'active' : ''; ?>">All</a>
                    <?php foreach ($categories as $cat): ?>
                    <a href="guides.php?category=<?php echo $cat['id']; ?>" class="filter-btn <?php echo $category == $cat['id'] ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Guides Grid -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-book"></i> 
                <?php echo $category ? $categories[array_search($category, array_column($categories, 'id'))]['name'] . ' Guides' : 'All Guides'; ?>
            </h2>
            
            <?php if (empty($guides)): ?>
                <div class="empty-state">
                    <i class="fas fa-book"></i>
                    <h3>No guides found</h3>
                    <p>Try adjusting your search or browse different categories</p>
                </div>
            <?php else: ?>
                <div class="guides-grid">
                    <?php foreach ($guides as $guide): ?>
                    <div class="guide-card">
                        <div class="guide-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="guide-content">
                            <h3><a href="guide.php?id=<?php echo $guide['id']; ?>"><?php echo htmlspecialchars($guide['title']); ?></a></h3>
                            <p><?php echo $functions->truncateText($guide['excerpt'], 120); ?></p>
                            <div class="guide-meta">
                                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($guide['author_name']); ?></span>
                                <span><i class="fas fa-eye"></i> <?php echo $guide['view_count']; ?></span>
                                <span><i class="fas fa-heart"></i> <?php echo $guide['like_count']; ?></span>
                            </div>
                            <div class="guide-tags">
                                <span class="tag"><?php echo htmlspecialchars($guide['category_name']); ?></span>
                                <span class="tag"><?php echo htmlspecialchars($guide['content_type_name']); ?></span>
                            </div>
                            <a href="guide.php?id=<?php echo $guide['id']; ?>" class="guide-link">Read Guide <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <section class="section">
        <div class="container">
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-secondary">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                <?php endif; ?>
                
                <div class="pagination-info">
                    Page <?php echo $page; ?> of <?php echo $total_pages; ?>
                </div>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-secondary">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

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
