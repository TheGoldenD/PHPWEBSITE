<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle profile image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $uploadDir = 'uploads/profile_images/';
    $fileName = uniqid() . '_' . basename($_FILES['profile_image']['name']);
    $uploadFile = $uploadDir . $fileName;

    // Validate file type and size
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['profile_image']['type'], $allowedTypes) && $_FILES['profile_image']['size'] <= 2000000) {
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
            // Update the database with the new profile image path
            $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $stmt->execute([$uploadFile, $_SESSION['user_id']]);
            $_SESSION['success_message'] = "Profile image uploaded successfully.";
            header('Location: profile.php'); // Reload the page
            exit;
        } else {
            $error = "Failed to upload the image.";
        }
    } else {
        $error = "Invalid file type or size. Only JPG, PNG, and GIF under 2MB are allowed.";
    }
}

include 'header.php';
?>

<h1>Welcome, <?= htmlspecialchars($user['username']) ?></h1>
<p>Email: <?= htmlspecialchars($user['email']) ?></p>
<p>Member since: <?= $user['created_at'] ?></p>

<!-- Success and Error Messages -->
<?php if (isset($_SESSION['success_message'])): ?>
    <p class="success"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<!-- Profile Image Section -->
<div class="profile-image-section">
    <h2>Your Profile Image</h2>
    <?php if (!empty($user['profile_image'])): ?>
        <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" class="profile-image">
    <?php else: ?>
        <p>No profile image set.</p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <label>Upload Profile Image:</label>
        <input type="file" name="profile_image" accept="image/*" required>
        <input type="submit" value="Upload">
    </form>
</div>

<!-- User's Modules and Posts -->
<h2>Your Modules</h2>
<?php
$stmt = $pdo->prepare("SELECT * FROM modules WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$modules = $stmt->fetchAll();
?>
<?php if (empty($modules)): ?>
    <p>You have not created any modules yet. <a href="create_module.php" class="button">Create a Module</a></p>
<?php else: ?>
    <div class="modules-list">
        <?php foreach ($modules as $module): ?>
            <div class="module">
                <h3><?= htmlspecialchars($module['name']) ?></h3>
                <p><?= nl2br(htmlspecialchars($module['description'])) ?></p>
                <a href="delete_module.php?module_id=<?= $module['id'] ?>" 
                   class="button" 
                   onclick="return confirm('Are you sure you want to delete this module? This will also delete all associated posts.');">
                    Delete Module
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h2>Your Posts</h2>
<?php
$stmt = $pdo->prepare("SELECT posts.*, modules.name AS module_name FROM posts 
                       JOIN modules ON posts.module_id = modules.id 
                       WHERE posts.user_id = ? 
                       ORDER BY posts.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$posts = $stmt->fetchAll();
?>
<?php if (empty($posts)): ?>
    <p>You have not created any posts yet. <a href="create_post.php" class="button">Create a Post</a></p>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <div class="post">
            <h3><?= htmlspecialchars($post['title']) ?></h3>
            <p>
                In module: <strong><?= htmlspecialchars($post['module_name']) ?></strong><br>
                Created at: <?= $post['created_at'] ?>
            </p>
            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
            <?php if ($post['image_path']): ?>
                <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="Post Image">
            <?php endif; ?>
            <a href="view_post.php?post_id=<?= $post['id'] ?>" class="button">View Post</a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'footer.php'; ?>
