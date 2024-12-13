<?php
session_start();
require 'db.php';

$stmt = $pdo->query("SELECT * FROM modules ORDER BY created_at DESC");
$modules = $stmt->fetchAll();
$module_id = isset($_GET['module_id']) ? $_GET['module_id'] : null;
if ($module_id) {
    $stmt = $pdo->prepare("SELECT posts.*, users.username, modules.name AS module_name 
                           FROM posts 
                           JOIN users ON posts.user_id = users.id
                           JOIN modules ON posts.module_id = modules.id
                           WHERE posts.module_id = ? 
                           ORDER BY posts.created_at DESC");
    $stmt->execute([$module_id]);
    $posts = $stmt->fetchAll();
} else {
    $stmt = $pdo->query("SELECT posts.*, users.username, modules.name AS module_name 
                         FROM posts 
                         JOIN users ON posts.user_id = users.id
                         JOIN modules ON posts.module_id = modules.id
                         ORDER BY posts.created_at DESC");
    $posts = $stmt->fetchAll();
}
include 'header.php';
?>

<h1>Welcome to the Domanda Forum</h1>

<!--------------------------------- Module ------------------------------------->
<div class="module-navigation">
    <h2>Modules</h2>
    <div class="modules-list">
        <a href="index.php" class="button <?= !$module_id ? 'active' : '' ?>">All Modules</a>
        <?php foreach ($modules as $module): ?>
            <a href="index.php?module_id=<?= $module['id'] ?>" class="button <?= $module_id == $module['id'] ? 'active' : '' ?>">
                <?= htmlspecialchars($module['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!----------------------------------------- Posts ----------------------------------------------------->
<h2><?= $module_id ? "Posts in Module: " . htmlspecialchars($modules[array_search($module_id, array_column($modules, 'id'))]['name']) : "All Posts" ?></h2>
<?php if (empty($posts)): ?>
    <p>No posts available in this module.</p>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <div class="post">
            <h2><?= htmlspecialchars($post['title']) ?></h2>
            <p>
                <strong><?= htmlspecialchars($post['username']) ?></strong> in 
                <em><?= htmlspecialchars($post['module_name']) ?></em> at <?= $post['created_at'] ?>
            </p>
            <?php if ($post['image_path']): ?>
                <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="Post Image">
            <?php endif; ?>
            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
            <a href="view_post.php?post_id=<?= $post['id'] ?>" class="button">View Comments</a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>



<?php include 'footer.php'; ?>
