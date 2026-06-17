<?php
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

$studentId = $_SESSION['student_id'];
$studentName = $_SESSION['student_name'] ?? 'Student';

// Filters
$selectedCategory   = $_GET['category']   ?? '';
$selectedDifficulty = $_GET['difficulty'] ?? '';

// Load distinct categories/difficulties
$catsStmt = $pdo->query('SELECT DISTINCT category FROM questions WHERE category IS NOT NULL AND category <> "" ORDER BY category');
$categories = $catsStmt->fetchAll();

$diffStmt = $pdo->query('SELECT DISTINCT difficulty FROM questions WHERE difficulty IS NOT NULL ORDER BY FIELD(difficulty,"Easy","Medium","Hard"), difficulty');
$diffs = $diffStmt->fetchAll();

// Build question query based on filters
$query = 'SELECT * FROM questions WHERE 1=1';
$params = [];

if ($selectedCategory !== '') {
    $query .= ' AND category = ?';
    $params[] = $selectedCategory;
}
if ($selectedDifficulty !== '') {
    $query .= ' AND difficulty = ?';
    $params[] = $selectedDifficulty;
}

$query .= ' ORDER BY id ASC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$questions = $stmt->fetchAll();
$totalQuestions = count($questions);

$submitted = false;
$score = 0;
$percentage = 0.0;

// Timer (front-end): 10 minutes (600 seconds)
$quizDurationSeconds = 600;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $totalQuestions > 0) {
    if (!csrf_validate()) {
        die('Invalid form submission.');
    }
    $submitted = true;
    $answers = $_POST['answers'] ?? [];

    foreach ($questions as $q) {
        $qid = $q['id'];
        $correctOption = (int)$q['correct_option'];
        if (isset($answers[$qid]) && (int)$answers[$qid] === $correctOption) {
            $score++;
        }
    }

    $percentage = $totalQuestions > 0 ? ($score / $totalQuestions) * 100 : 0;

    $insert = $pdo->prepare(
        'INSERT INTO results (student_id, score, total_questions, percentage) VALUES (?, ?, ?, ?)'
    );
    $insert->execute([$studentId, $score, $totalQuestions, $percentage]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Quiz - Smart Interview System</title>
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
            <a href="quiz.php" class="active">
                <span class="label">Take Quiz</span>
            </a>
            <a href="result.php">
                <span class="label">Latest Result</span>
            </a>
            <a href="progress.php">
                <span class="label">Progress</span>
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
                <div class="topbar-title">Quiz Session</div>
                <div class="topbar-subtitle">
                    Select category and difficulty, then complete all questions before the timer ends.
                </div>
            </div>
            <div class="user-badge">
                <span class="user-badge-circle"></span>
                <span><?php echo htmlspecialchars($studentName); ?></span>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <div>
                    <div class="section-title">Quiz Filters</div>
                    <div class="section-subtitle">
                        Choose specific category/difficulty or leave as "All".
                    </div>
                </div>
            </div>

            <form method="get" action="quiz.php" style="margin-bottom:14px;">
                <div class="cards-grid">
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $c): ?>
                                <?php $catVal = $c['category']; ?>
                                <option value="<?php echo htmlspecialchars($catVal); ?>"
                                    <?php echo $selectedCategory === $catVal ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($catVal); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="difficulty">Difficulty</label>
                        <select id="difficulty" name="difficulty">
                            <option value="">All Levels</option>
                            <?php foreach ($diffs as $d): ?>
                                <?php $dv = $d['difficulty']; ?>
                                <option value="<?php echo htmlspecialchars($dv); ?>"
                                    <?php echo $selectedDifficulty === $dv ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dv); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="align-self:flex-end;">
                        <button type="submit" class="btn btn-outline">Apply Filters</button>
                    </div>
                </div>
            </form>

            <div class="section-header">
                <div>
                    <div class="section-title">Multiple Choice Quiz</div>
                    <div class="section-subtitle">
                        Total questions: <?php echo $totalQuestions; ?>.
                        Time: 10 minutes.
                    </div>
                </div>
                <?php if (!$submitted && $totalQuestions > 0): ?>
                    <div class="badge">
                        Time left: <span id="timer" style="margin-left:4px;">10:00</span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($totalQuestions === 0): ?>
                <div class="alert alert-error">
                    No questions are available for the selected filters. Try different category/difficulty.
                </div>
            <?php elseif ($submitted): ?>
                <div class="alert alert-success">
                    You scored <strong><?php echo $score; ?></strong> out of
                    <strong><?php echo $totalQuestions; ?></strong> (
                    <?php echo number_format($percentage, 2); ?>%).
                    Your result has been saved.
                </div>
                <div class="hero-actions">
                    <a href="quiz.php" class="btn btn-primary">Start New Quiz</a>
                    <a href="progress.php" class="btn btn-outline">View Progress</a>
                </div>
            <?php else: ?>
                <form id="quizForm" method="post" action="">
                    <?php csrf_field(); ?>
                    <?php foreach ($questions as $index => $q): ?>
                        <div class="quiz-question">
                            <h4>
                                Q<?php echo $index + 1; ?>.
                                <?php echo htmlspecialchars($q['question']); ?>
                                <?php if ($q['category'] || $q['difficulty']): ?>
                                    <span class="badge" style="margin-left:8px;">
                                        <?php if ($q['category']): ?>
                                            <?php echo htmlspecialchars($q['category']); ?>
                                        <?php endif; ?>
                                        <?php if ($q['difficulty']): ?>
                                            &middot; <?php echo htmlspecialchars($q['difficulty']); ?>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </h4>
                            <div class="quiz-options">
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <?php
                                    $field = 'option' . $i;
                                    $inputId = 'q' . $q['id'] . '_opt' . $i;
                                    ?>
                                    <label for="<?php echo $inputId; ?>">
                                        <input
                                            type="radio"
                                            id="<?php echo $inputId; ?>"
                                            name="answers[<?php echo $q['id']; ?>]"
                                            value="<?php echo $i; ?>"
                                        >
                                        <?php echo htmlspecialchars($q[$field]); ?>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <button type="submit" class="btn btn-primary">Submit Quiz</button>
                </form>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php if (!$submitted && $totalQuestions > 0): ?>
<script>
    (function () {
        var duration = <?php echo (int)$quizDurationSeconds; ?>; // seconds
        var display = document.getElementById('timer');
        var form = document.getElementById('quizForm');

        if (!display || !form) return;

        function updateTimer() {
            var minutes = Math.floor(duration / 60);
            var seconds = duration % 60;
            display.textContent =
                String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

            if (duration <= 0) {
                form.submit(); // auto-submit when time ends
            } else {
                duration--;
                setTimeout(updateTimer, 1000);
            }
        }

        updateTimer();
    })();
</script>
<?php endif; ?>
</body>
</html>