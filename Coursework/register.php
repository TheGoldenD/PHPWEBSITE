<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];

    $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $password, $email]);
        header("Location: login.php?success=1");
        exit;
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

include 'header.php';
?>

<h1>Register</h1>
<div class="form-container">
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <input type="submit" value="Register">
    </form>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</div>

<?php include 'footer.php'; ?>
