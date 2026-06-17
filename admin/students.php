<?php
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->query(
    'SELECT s.id, s.name, s.email, s.created_at, 
            COALESCE(COUNT(r.id), 0) AS attempts
     FROM students s
     LEFT JOIN results r ON r.student_id = s.id
     GROUP BY s.id, s.name, s.email, s.created_at
     ORDER BY s.created_at DESC'
);
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students - Smart Interview System</title>
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
            <a href="students.php" class="active">
                <span class="label">View Students</span>
            </a>
            <a href="results.php">
                <span class="label">View Results</span>
            </a>

            <div class="sidebar-section-title">Account</div>
<a href="change_password.php">
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
                <div class="topbar-title">Registered Students</div>
                <div class="topbar-subtitle">All students enrolled in the system.</div>
            </div>
        </div>

        <div class="section">
            <?php if (empty($students)): ?>
                <div class="alert alert-error">
                    No students registered yet.
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Attempts</th>
                            <th>Registered On</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td><?php echo (int)$s['id']; ?></td>
                                <td><?php echo htmlspecialchars($s['name']); ?></td>
                                <td><?php echo htmlspecialchars($s['email']); ?></td>
                                <td><?php echo (int)$s['attempts']; ?></td>
                                <td><?php echo htmlspecialchars($s['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>