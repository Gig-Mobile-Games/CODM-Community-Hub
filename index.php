<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

// Fetch latest posts
$stmt = $db->prepare("SELECT p.id, p.title, p.content, p.created_at, u.username FROM posts p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 20");
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="container">
  <h1>CODM Community Hub</h1>
  <p>Welcome to the community hub. Share news, guides, and events.</p>

  <div class="nav">
    <?php if (isset($_SESSION['user_id'])): ?>
      <span>Hi, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
      <a href="create_post.php">New Post</a>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="register.php">Register</a>
      <a href="login.php">Login</a>
    <?php endif; ?>
  </div>

  <section class="posts">
    <?php while ($row = $result->fetch_assoc()): ?>
      <article class="post">
        <h2><a href="view_post.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h2>
        <p class="meta">by <?php echo htmlspecialchars($row['username'] ?? 'Unknown'); ?> • <?php echo htmlspecialchars($row['created_at']); ?></p>
        <p><?php echo nl2br(htmlspecialchars(substr($row['content'], 0, 400))); ?><?php if (strlen($row['content']) > 400) echo '...'; ?></p>
      </article>
    <?php endwhile; ?>
  </section>
</div>

<?php
require_once __DIR__ . '/footer.php';
?>