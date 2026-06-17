<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/validation.php';
require_once '../includes/password.php';
require_once '../includes/html_head.php';
require_once '../includes/sidebar.php';
require_once '../includes/alerts.php';

require_student();

$studentId = $_SESSION['student_id'];
$studentName = $_SESSION['student_name'] ?? 'Student';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = process_change_password(
        $pdo,
        'students',
        $studentId,
        $_POST['current_password'] ?? '',
        $_POST['new_password'] ?? '',
        $_POST['confirm_password'] ?? ''
    );
    $errors = $result['errors'];
    $success = $result['success'];
}

render_html_head('Change Password');
?>
<div class="layout">
    <?php render_student_sidebar('change_password.php'); ?>

    <main class="main-content">
        <div class="topbar">
            <div>
                <div class="topbar-title">Change Password</div>
                <div class="topbar-subtitle">Update your account password securely.</div>
            </div>
            <div class="user-badge">
                <span class="user-badge-circle"></span>
                <span><?php echo htmlspecialchars($studentName); ?></span>
            </div>
        </div>

        <div class="section">
            <?php render_alerts($errors, $success); ?>

            <form method="post" action="">
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
