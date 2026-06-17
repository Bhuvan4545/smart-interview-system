<?php
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$adminEmail = $_SESSION['admin_email'] ?? 'admin';

// Stats
$studentsCount = (int)$pdo->query('SELECT COUNT(*) AS c FROM students')->fetch()['c'];
$questionsCount = (int)$pdo->query('SELECT COUNT(*) AS c FROM questions')->fetch()['c'];
$resultsCount = (int)$pdo->query('SELECT COUNT(*) AS c FROM results')->fetch()['c'];

// Latest 5 results
$stmt = $pdo->query(
    'SELECT r.score, r.total_questions, r.percentage, r.created_at, s.name AS student_name
     FROM results r
     JOIN students s ON r.student_id = s.id
     ORDER BY r.created_at DESC
     LIMIT 5'
);
$latestResults = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Smart Interview System</title>
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
            <a href="dashboard.php" class="active">
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
            <a href="logout.php">
                <span class="label">Logout</span>
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <div class="topbar-title">Admin Dashboard</div>
                <div class="topbar-subtitle">Overview of students, questions, and performance.</div>
            </div>
            <div class="user-badge">
                <span class="user-badge-circle"></span>
                <span><?php echo htmlspecialchars($adminEmail); ?></span>
            </div>
        </div>

        <div class="cards-grid">
            <div class="card">
                <div class="card-title">Total Students</div>
                <div class="card-value"><?php echo $studentsCount; ?></div>
                <div class="card-tag">Registered candidates</div>
            </div>
            <div class="card">
                <div class="card-title">Question Bank</div>
                <div class="card-value"><?php echo $questionsCount; ?></div>
                <div class="card-tag">MCQs available</div>
            </div>
            <div class="card">
                <div class="card-title">Total Attempts</div>
                <div class="card-value"><?php echo $resultsCount; ?></div>
                <div class="card-tag">Quiz submissions</div>
            </div>
        </div>

        <div class="section" style="margin-top:18px;">
            <div class="section-header">
                <div>
                    <div class="section-title">Recent Results</div>
                    <div class="section-subtitle">Most recent quiz attempts by students.</div>
                </div>
                <a href="results.php" class="btn btn-outline">View all results</a>
            </div>

            <?php if (empty($latestResults)): ?>
                <div class="alert alert-error">
                    No quiz attempts recorded yet.
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Student</th>
                            <th>Score</th>
                            <th>Total Questions</th>
                            <th>Percentage</th>
                            <th>Attempted On</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($latestResults as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                <td><?php echo (int)$row['score']; ?></td>
                                <td><?php echo (int)$row['total_questions']; ?></td>
                                <td><?php echo number_format($row['percentage'], 2); ?>%</td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
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