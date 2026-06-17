<?php
require_once '../config/db.php';

$errors = [];

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
        } catch (PDOException $e) {
            app_log_error('Student login query', $e);
            $errors[] = 'A system error occurred. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login - Smart Interview System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js" defer></script>
</head>
<body>
<div class="form-card">
    <div class="form-title">Student Login</div>
    <div class="form-subtitle">Access your personalized interview preparation dashboard.</div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): ?>
                <div><?php echo htmlspecialchars($e); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

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