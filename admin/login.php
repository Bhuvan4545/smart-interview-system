<?php
require_once '../config/db.php';

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
    app_log_error('Admin seeding', $e);
    $errors[] = 'System initialization error. Please contact the administrator.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        try {
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
        } catch (PDOException $e) {
            app_log_error('Admin login query', $e);
            $errors[] = 'A system error occurred. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Smart Interview System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js" defer></script>
</head>
<body>
<div class="form-card">
    <div class="form-title">Admin Login</div>
    <div class="form-subtitle">Manage questions, students, and results.</div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): ?>
                <div><?php echo htmlspecialchars($e); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

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