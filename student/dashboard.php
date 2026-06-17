<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/html_head.php';
require_once '../includes/sidebar.php';

require_student();

$studentId = $_SESSION['student_id'];
$studentName = $_SESSION['student_name'] ?? 'Student';

// Fetch summary stats
$totalAttempts = 0;
$averageScore = 0;
$highestScore = 0;

$stmt = $pdo->prepare('SELECT 
                            COUNT(*) AS attempts,
                            COALESCE(AVG(score), 0) AS avg_score,
                            COALESCE(MAX(score), 0) AS max_score
                       FROM results
                       WHERE student_id = ?');
$stmt->execute([$studentId]);
$stats = $stmt->fetch();

if ($stats) {
    $totalAttempts = (int)$stats['attempts'];
    $averageScore = (float)$stats['avg_score'];
    $highestScore = (int)$stats['max_score'];
}

// Count total questions
$stmtQ = $pdo->query('SELECT COUNT(*) AS total_q FROM questions');
$totalQuestions = (int)$stmtQ->fetch()['total_q'];

render_html_head('Student Dashboard');
?>
<div class="layout">
    <?php render_student_sidebar('dashboard.php'); ?>

    <main class="main-content">
        <div class="topbar">
            <div>
                <div class="topbar-title">Welcome back, <?php echo htmlspecialchars($studentName); ?>.</div>
                <div class="topbar-subtitle">Track your interview preparation and continue your practice.</div>
            </div>
            <div class="user-badge">
                <span class="user-badge-circle"></span>
                <span><?php echo htmlspecialchars($studentName); ?></span>
            </div>
        </div>

        <div class="cards-grid">
            <div class="card">
                <div class="card-title">Total Quiz Attempts</div>
                <div class="card-value"><?php echo $totalAttempts; ?></div>
                <div class="card-tag">Practice sessions completed</div>
            </div>

            <div class="card">
                <div class="card-title">Average Score</div>
                <div class="card-value"><?php echo number_format($averageScore, 1); ?></div>
                <div class="card-tag">Across all attempts</div>
            </div>

            <div class="card">
                <div class="card-title">Highest Score</div>
                <div class="card-value"><?php echo $highestScore; ?></div>
                <div class="card-tag success">Best performance</div>
            </div>

            <div class="card">
                <div class="card-title">Questions Available</div>
                <div class="card-value"><?php echo $totalQuestions; ?></div>
                <div class="card-tag">In current question bank</div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <div>
                    <div class="section-title">Quick Actions</div>
                    <div class="section-subtitle">Jump directly into practice or review your recent performance.</div>
                </div>
            </div>
            <div class="hero-actions">
                <a href="quiz.php" class="btn btn-primary">Start New Quiz</a>
                <a href="progress.php" class="btn btn-outline">View Progress</a>
                <a href="result.php" class="btn btn-outline">Last Attempt Summary</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
