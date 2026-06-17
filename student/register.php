<?php
require_once '../config/db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('SELECT id FROM students WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email is already registered.';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare('INSERT INTO students (name, email, password) VALUES (?, ?, ?)');
                $insert->execute([$name, $email, $hashedPassword]);
                $success = 'Registration successful. You can now login.';
            }
        } catch (PDOException $e) {
            app_log_error('Student registration', $e);
            $errors[] = 'Registration failed due to a system error. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration - Smart Interview System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js" defer></script>
</head>
<body>
<div class="form-card">
    <div class="form-title">Student Registration</div>
    <div class="form-subtitle">Create your account to start practicing for interviews.</div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): ?>
                <div><?php echo htmlspecialchars($e); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <form id="studentRegisterForm" method="post" action="">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
                placeholder="Enter your full name"
            >
        </div>

        <div class="form-group">
            <label for="email">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                placeholder="you@example.com"
            >
        </div>

        <div class="form-group">
            <label for="password">Password (min 6 characters)</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter password"
            >
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm password</label>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Confirm password"
            >
        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary">Register</button>
            <a href="login.php">Already have an account? Login</a>
        </div>
    </form>
</div>
</body>
</html>