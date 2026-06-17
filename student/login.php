<?php
require_once '../config/db.php';
require_once '../includes/validation.php';
require_once '../includes/html_head.php';
require_once '../includes/alerts.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = validate_login($email, $password);

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id, name, password FROM students WHERE email = ?');
        $stmt->execute([$email]);
        $student = $stmt->fetch();

        if ($student && password_verify($password, $student['password'])) {
            $_SESSION['student_id']   = $student['id'];
            $_SESSION['student_name'] = $student['name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}

render_html_head('Student Login', ['../assets/js/script.js']);
?>
<div class="form-card">
    <div class="form-title">Student Login</div>
    <div class="form-subtitle">Access your personalized interview preparation dashboard.</div>

    <?php render_alerts($errors); ?>

    <form id="studentLoginForm" method="post" action="">
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
            <a href="register.php">New here? Register</a>
        </div>
    </form>
</div>
</body>
</html>
