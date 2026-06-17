<?php
require_once '../config/db.php';
require_once '../includes/html_head.php';
require_once '../includes/alerts.php';

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
    }
}

render_html_head('Student Registration', ['../assets/js/script.js']);
?>
<div class="form-card">
    <div class="form-title">Student Registration</div>
    <div class="form-subtitle">Create your account to start practicing for interviews.</div>

    <?php render_alerts($errors, $success); ?>

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
