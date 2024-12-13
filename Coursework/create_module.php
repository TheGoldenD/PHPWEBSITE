<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO modules (user_id, name, description) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $name, $description]);

    header("Location: profile.php");
    exit;
}

include 'header.php';
?>

<h1>Create Module</h1>
<div class="form-container">
    <form method="POST">
        <label>Module Name:</label>
        <input type="text" name="name" required>
        <label>Description:</label>
        <textarea name="description" rows="4" required></textarea>
        <input type="submit" value="Create Module">
    </form>
</div>

<?php include 'footer.php'; ?>
