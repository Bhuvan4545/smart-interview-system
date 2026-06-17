<?php
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->query(
    'SELECT r.id, r.score, r.total_questions, r.percentage, r.created_at,
            s.name AS student_name, s.email AS student_email
     FROM results r
     JOIN students s ON r.student_id = s.id
     ORDER BY r.created_at DESC'
);
$results = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Results - Smart Interview System</title>
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
            <a href="results.php" class="active">
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
        <div class="section-header">
    <div>
        <div class="section-title">All Quiz Results</div>
        <div class="section-subtitle">History of all quiz attempts.</div>
    </div>
    <div>
        <a href="results_export.php" class="btn btn-outline">Export CSV</a>
    </div>
</div>

        <div class="section">
            <?php if (empty($results)): ?>
                <div class="alert alert-error">
                    No quiz attempts recorded yet.
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Score</th>
                            <th>Total Questions</th>
                            <th>Percentage</th>
                            <th>Attempted On</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($results as $index => $r): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($r['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($r['student_email']); ?></td>
                                <td><?php echo (int)$r['score']; ?></td>
                                <td><?php echo (int)$r['total_questions']; ?></td>
                                <td><?php echo number_format($r['percentage'], 2); ?>%</td>
                                <td><?php echo htmlspecialchars($r['created_at']); ?></td>
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