<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/html_head.php';
require_once '../includes/sidebar.php';

require_student();

$studentId = $_SESSION['student_id'];
$studentName = $_SESSION['student_name'] ?? 'Student';

// Overall stats
$stmt = $pdo->prepare(
    'SELECT 
        COUNT(*) AS attempts,
        COALESCE(AVG(score), 0) AS avg_score,
        COALESCE(MAX(score), 0) AS max_score,
        COALESCE(AVG(percentage), 0) AS avg_percentage
     FROM results
     WHERE student_id = ?'
);
$stmt->execute([$studentId]);
$stats = $stmt->fetch();

$totalAttempts  = (int)$stats['attempts'];
$avgScore       = (float)$stats['avg_score'];
$maxScore       = (int)$stats['max_score'];
$avgPercentage  = (float)$stats['avg_percentage'];

// Recent attempts
$stmtR = $pdo->prepare(
    'SELECT score, total_questions, percentage, created_at
     FROM results
     WHERE student_id = ?
     ORDER BY created_at DESC
     LIMIT 10'
);
$stmtR->execute([$studentId]);
$recent = $stmtR->fetchAll();

render_html_head('Progress');
?>
<div class="layout">
    <?php render_student_sidebar('progress.php'); ?>

    <main class="main-content">
        <div class="topbar">
            <div>
                <div class="topbar-title">Performance Overview</div>
                <div class="topbar-subtitle">Your learning journey at a glance.</div>
            </div>
            <div class="user-badge">
                <span class="user-badge-circle"></span>
                <span><?php echo htmlspecialchars($studentName); ?></span>
            </div>
        </div>

        <div class="progress-grid">
            <div class="card">
                <div class="card-title">Total Attempts</div>
                <div class="card-value"><?php echo $totalAttempts; ?></div>
                <div class="card-tag">Practice sessions</div>
            </div>
            <div class="card">
                <div class="card-title">Average Score</div>
                <div class="card-value"><?php echo number_format($avgScore, 1); ?></div>
                <div class="card-tag">Across all attempts</div>
            </div>
            <div class="card">
                <div class="card-title">Highest Score</div>
                <div class="card-value"><?php echo $maxScore; ?></div>
                <div class="card-tag success">Best performance</div>
            </div>
            <div class="card">
                <div class="card-title">Average Percentage</div>
                <div class="card-value"><?php echo number_format($avgPercentage, 2); ?>%</div>
                <div class="card-tag">Overall accuracy</div>
            </div>
        </div>

        <div class="section" style="margin-top:18px;">
            <div class="section-header">
                <div>
                    <div class="section-title">Recent Attempts</div>
                    <div class="section-subtitle">Last 10 quiz sessions.</div>
                </div>
            </div>

            <?php if (empty($recent)): ?>
                <div class="alert alert-error">
                    No attempts recorded yet. Start practicing to see your progress.
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Score</th>
                            <th>Total Questions</th>
                            <th>Percentage</th>
                            <th>Attempted On</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recent as $index => $row): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
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
