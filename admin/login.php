<?php
require_once '../config/db.php';
require_once '../includes/validation.php';
require_once '../includes/html_head.php';
require_once '../includes/alerts.php';

$errors = [];

// Ensure there is at least one admin user (default: admin@admin.com / admin123)
try {
    $countStmt = $pdo->query('SELECT COUNT(*) AS cnt FROM admin');
    $row = $countStmt->fetch();
    if ((int)$row['cnt'] === 0) {
        $defaultEmail = 'admin@admin.com';
        $defaultPassHash = password_hash('admin123', PASSWORD_DEFAULT);
        $insertAdmin = $pdo->prepare('INSERT INTO admin (email, password) VALUES (?, ?)');
        $insertAdmin->execute([$defaultEmail, $defaultPassHash]);
    }
} catch (Exception $e) {
    // optional: log error
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = validate_login($email, $password);

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id, email, password FROM admin WHERE email = ?');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']    = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}

render_html_head('Admin Login', ['../assets/js/script.js']);
?>
<div class="form-card">
    <div class="form-title">Admin Login</div>
    <div class="form-subtitle">Manage questions, students, and results.</div>

    <?php render_alerts($errors); ?>

    <form id="adminLoginForm" method="post" action="">
        <div class="form-group">
            <label for="email">Admin Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                placeholder="admin@admin.com"
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter password"
            >
        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-primary">Login</button>
            <span style="font-size:0.75rem;color:#9ca3af;">
                Default: admin@admin.com / admin123
            </span>
        </div>
    </form>
</div>
</body>
</html>
