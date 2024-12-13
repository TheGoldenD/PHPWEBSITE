<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$post_id = $_GET['post_id'];
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$post_id, $_SESSION['user_id']]);
$post = $stmt->fetch();

if (!$post) {
    die("Post not found or you do not have permission to edit this post.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'uploads/';
        $imagePath = $uploadDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    } else {
        $imagePath = $post['image_path'];
    }

    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, image_path = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$title, $content, $imagePath, $post_id, $_SESSION['user_id']]);

    header("Location: view_post.php?post_id=$post_id");
    exit;
}

include 'header.php';
?>

<h1>Edit Post</h1>
<div class="form-container">
    <form method="POST" enctype="multipart/form-data">
        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
        <label>Content:</label>
        <textarea name="content" rows="5" required><?= htmlspecialchars($post['content']) ?></textarea>
        <label>Image:</label>
        <input type="file" name="image">
        <input type="submit" value="Update Post">
    </form>
</div>

<?php include 'footer.php'; ?>
