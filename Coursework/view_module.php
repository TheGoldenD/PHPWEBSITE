<?php
session_start();
require 'db.php';

$module_id = $_GET['module_id'];
$stmt = $pdo->prepare("SELECT * FROM modules WHERE id = ?");
$stmt->execute([$module_id]);
$module = $stmt->fetch();

if (!$module) {
    die("Module not found.");
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE module_id = ? ORDER BY created_at DESC");
$stmt->execute([$module_id]);
$posts = $stmt->fetchAll();

include 'header.php';
?>

<h1><?= htmlspecialchars($module['name']) ?></h1>
<p><?= nl2br(htmlspecialchars($module['description'])) ?></p>

<h2>Posts</h2>
<?php if (empty($posts)): ?>
    <p>No posts in this module yet.</p>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <div class="post">
            <h2><?= htmlspecialchars($post['title']) ?></h2>
            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
            <a href="view_post.php?post_id=<?= $post['id'] ?>" class="button">View Post</a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'footer.php'; ?>
