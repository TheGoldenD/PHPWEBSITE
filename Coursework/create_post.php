<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM modules WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$modules = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $module_id = $_POST['module_id'];

    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'uploads/';
        $imagePath = $uploadDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    } else {
        $imagePath = null;
    }

    $stmt = $pdo->prepare("INSERT INTO posts (user_id, module_id, title, content, image_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $module_id, $title, $content, $imagePath]);

    header("Location: index.php");
    exit;
}

include 'header.php';
?>

<h1>Create Post</h1>
<div class="form-container">
    <form method="POST" enctype="multipart/form-data">
        <label>Title:</label>
        <input type="text" name="title" required>
        <label>Content:</label>
        <textarea name="content" rows="5" required></textarea>
        <label>Module:</label>
        <select name="module_id" required>
            <?php foreach ($modules as $module): ?>
                <option value="<?= $module['id'] ?>"><?= htmlspecialchars($module['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <label>Image:</label>
        <input type="file" name="image">
        <input type="submit" value="Create Post">
    </form>
</div>

<?php include 'footer.php'; ?>
