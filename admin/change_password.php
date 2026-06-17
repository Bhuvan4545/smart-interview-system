<?php
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$adminId = $_SESSION['admin_id'];
$adminEmail = $_SESSION['admin_email'] ?? 'admin';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) {
        $errors[] = 'Invalid form submission. Please try again.';
    }

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
        $errors[] = 'All fields are required.';
    } elseif (strlen($newPassword) < 6) {
        $errors[] = 'New password must be at least 6 characters.';
    } elseif ($newPassword !== $confirmPassword) {
        $errors[] = 'New password and confirm password do not match.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT password FROM admin WHERE id = ?');
        $stmt->execute([$adminId]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($currentPassword, $row['password'])) {
            $errors[] = 'Current password is incorrect.';
        } else {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $pdo->prepare('UPDATE admin SET password = ? WHERE id = ?');
            $update->execute([$newHash, $adminId]);
            $success = 'Password changed successfully.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password - Admin - Smart Interview System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-logo">SI</div>
            <div>
                <div class="sidebar-title">Smart Interview</div>
                <div class="sidebar-subtitle">Admin Panel</div>
            </div>
        </div>
        <nav class="sidebar-menu">
            <div class="sidebar-section-title">Navigation</div>
            <a href="dashboard.php">
                <span class="label">Dashboard</span>
            </a>
            <a href="add_question.php">
                <span class="label">Add Question</span>
            </a>
            <a href="delete_question.php">
                <span class="label">Manage Questions</span>
            </a>
            <a href="students.php">
                <span class="label">View Students</span>
            </a>
            <a href="results.php">
                <span class="label">View Results</span>
            </a>

            <div class="sidebar-section-title">Account</div>
            <a href="change_password.php" class="active">
                <span class="label">Change Password</span>
            </a>
            <a href="logout.php">
                <span class="label">Logout</span>
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <div class="topbar-title">Change Admin Password</div>
                <div class="topbar-subtitle">Secure your admin account.</div>
            </div>
            <div class="user-badge">
                <span class="user-badge-circle"></span>
                <span><?php echo htmlspecialchars($adminEmail); ?></span>
            </div>
        </div>

        <div class="section">
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

            <form method="post" action="">
                <?php csrf_field(); ?>
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        placeholder="Enter current password"
                    >
                </div>
                <div class="form-group">
                    <label for="new_password">New Password (min 6 chars)</label>
                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        placeholder="Enter new password"
                    >
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Re-enter new password"
                    >
                </div>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>