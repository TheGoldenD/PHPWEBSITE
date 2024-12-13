<?php
session_start();
require 'db.php';

$post_id = $_GET['post_id'];
$stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    die("Post not found.");
}

$stmt = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at DESC");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $content = $_POST['content'];
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $_SESSION['user_id'], $content]);
    header("Location: view_post.php?post_id=$post_id");
    exit;
}

include 'header.php';
?>

<h1><?= htmlspecialchars($post['title']) ?></h1>
<div class="post">
    <p>
        <strong><?= htmlspecialchars($post['username']) ?></strong> 
        at <?= $post['created_at'] ?>
    </p>
    <?php if ($post['image_path']): ?>
        <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="Post Image">
    <?php endif; ?>
    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
</div>

<?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
    <div class="post-actions">
        <a href="edit_post.php?post_id=<?= $post['id'] ?>" class="button">Edit Post</a>
        <a href="delete_post.php?post_id=<?= $post['id'] ?>" class="button" onclick="return confirm('Are you sure you want to delete this post?');">Delete Post</a>
    </div>
<?php endif; ?>

<h2>Comments</h2>
<?php if (empty($comments)): ?>
    <p>No comments yet. Be the first to comment!</p>
<?php else: ?>
    <?php foreach ($comments as $comment): ?>
        <div class="comment">
            <p>
                <strong><?= htmlspecialchars($comment['username']) ?>:</strong> 
                <?= nl2br(htmlspecialchars($comment['content'])) ?>
            </p>
            <small>Posted on <?= $comment['created_at'] ?></small>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (isset($_SESSION['user_id'])): ?>
    <div class="form-container">
        <h3>Add a Comment</h3>
        <form method="POST">
            <textarea name="content" rows="4" required placeholder="Write your comment..."></textarea>
            <input type="submit" value="Post Comment">
        </form>
    </div>
<?php else: ?>
    <p><a href="login.php">Login</a> to add a comment.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
