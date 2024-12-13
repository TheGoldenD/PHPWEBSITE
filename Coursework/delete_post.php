<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$post_id = $_GET['post_id'];
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$post_id, $_SESSION['user_id']]);

header("Location: index.php");
exit;
?>
