<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/html_head.php';
require_once '../includes/sidebar.php';

require_admin();

$stmt = $pdo->query(
    'SELECT r.id, r.score, r.total_questions, r.percentage, r.created_at,
            s.name AS student_name, s.email AS student_email
     FROM results r
     JOIN students s ON r.student_id = s.id
     ORDER BY r.created_at DESC'
);
$results = $stmt->fetchAll();

render_html_head('Quiz Results');
?>
<div class="layout">
    <?php render_admin_sidebar('results.php'); ?>

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
