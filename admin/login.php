<?php
require_once '../config/db.php';

$errors = [];

// Seed a default admin account only when the table is empty AND the
// ADMIN_SEED_EMAIL / ADMIN_SEED_PASS environment variables are set.
try {
    $countStmt = $pdo->query('SELECT COUNT(*) AS cnt FROM admin');
    $row = $countStmt->fetch();
    if ((int)$row['cnt'] === 0) {
        $seedEmail = getenv('ADMIN_SEED_EMAIL') ?: '';
        $seedPass  = getenv('ADMIN_SEED_PASS')  ?: '';
        if ($seedEmail !== '' && $seedPass !== '') {
            $defaultPassHash = password_hash($seedPass, PASSWORD_DEFAULT);
            $insertAdmin = $pdo->prepare('INSERT INTO admin (email, password) VALUES (?, ?)');
            $insertAdmin->execute([$seedEmail, $defaultPassHash]);
        }
    }
} catch (Exception $e) {
    error_log('Admin seed failed: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) {
        $errors[] = 'Invalid form submission. Please try again.';
    }

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id, email, password FROM admin WHERE email = ?');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']    = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Invalid email or password.';
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
        <?php csrf_field(); ?>
        <div class="form-group">
            <label for="email">Admin Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                placeholder="admin@example.com"
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
        </div>
    </form>
</div>
</body>
</html>
