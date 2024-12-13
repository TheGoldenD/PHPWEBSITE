<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$module_id = $_GET['module_id'] ?? null;

if (!$module_id) {
    $_SESSION['error_message'] = "Invalid module ID.";
    header('Location: profile.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM modules WHERE id = ? AND user_id = ?");
$stmt->execute([$module_id, $_SESSION['user_id']]);
$module = $stmt->fetch();

if (!$module) {
    $_SESSION['error_message'] = "You do not have permission to delete this module.";
    header('Location: profile.php');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM modules WHERE id = ?");
$stmt->execute([$module_id]);

$_SESSION['success_message'] = "Module deleted successfully.";
header('Location: profile.php');
exit;
?>
