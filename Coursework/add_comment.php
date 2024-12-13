<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $content = $_POST['content'];

    $stmt = $pdo->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)');
    $stmt->execute([$post_id, $user_id, $content]);

    header('Location: view_post.php?id=' . $post_id);
    exit;
}
?>
<form method="POST">
    <textarea name="content" placeholder="Add a comment..." required></textarea>
    <input type="hidden" name="post_id" value="<?= $post_id ?>">
    <button type="submit">Post Comment</button>
</form>
