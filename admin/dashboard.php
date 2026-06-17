<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/html_head.php';
require_once '../includes/sidebar.php';

require_admin();

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

render_html_head('Admin Dashboard');
?>
<div class="layout">
    <?php render_admin_sidebar('dashboard.php'); ?>

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
