<?php
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

$studentId = $_SESSION['student_id'];
$studentName = $_SESSION['student_name'] ?? 'Student';

$stmt = $pdo->prepare('SELECT * FROM results WHERE student_id = ? ORDER BY created_at DESC LIMIT 1');
$stmt->execute([$studentId]);
$latest = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Latest Result - Smart Interview System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-logo">SI</div>
            <div>
                <div class="sidebar-title">Smart Interview</div>
                <div class="sidebar-subtitle">Student Panel</div>
            </div>
        </div>
        <nav class="sidebar-menu">
            <div class="sidebar-section-title">Navigation</div>
            <a href="dashboard.php">
                <span class="label">Dashboard</span>
            </a>
            <a href="quiz.php">
                <span class="label">Take Quiz</span>
            </a>
            <a href="result.php" class="active">
                <span class="label">Latest Result</span>
            </a>
            <a href="progress.php">
                <span class="label">Progress</span>
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
                <div class="topbar-title">Latest Quiz Result</div>
                <div class="topbar-subtitle">Your most recent performance summary.</div>
            </div>
            <div class="user-badge">
                <span class="user-badge-circle"></span>
                <span><?php echo htmlspecialchars($studentName); ?></span>
            </div>
        </div>

        <div class="section">
            <?php if (!$latest): ?>
                <div class="alert alert-error">
                    You have not attempted any quiz yet.
                </div>
                <a href="quiz.php" class="btn btn-primary">Take your first quiz</a>
            <?php else: ?>
                <div class="cards-grid">
                    <div class="card">
                        <div class="card-title">Score</div>
                        <div class="card-value"><?php echo (int)$latest['score']; ?></div>
                        <div class="card-tag">Correct answers</div>
                    </div>
                    <div class="card">
                        <div class="card-title">Total Questions</div>
                        <div class="card-value"><?php echo (int)$latest['total_questions']; ?></div>
                        <div class="card-tag">Questions attempted</div>
                    </div>
                    <div class="card">
                        <div class="card-title">Percentage</div>
                        <div class="card-value"><?php echo number_format($latest['percentage'], 2); ?>%</div>
                        <div class="card-tag">Performance</div>
                    </div>
                    <div class="card">
                        <div class="card-title">Attempted On</div>
                        <div class="card-value" style="font-size:0.9rem;">
                            <?php echo htmlspecialchars($latest['created_at']); ?>
                        </div>
                        <div class="card-tag">Timestamp</div>
                    </div>
                </div>
                <div class="hero-actions" style="margin-top:16px;">
                    <a href="quiz.php" class="btn btn-primary">Retake Quiz</a>
                    <a href="progress.php" class="btn btn-outline">View Detailed Progress</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>