<?php
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question  = trim($_POST['question'] ?? '');
    $category  = trim($_POST['category'] ?? '');
    $difficulty = trim($_POST['difficulty'] ?? 'Medium');
    $option1   = trim($_POST['option1'] ?? '');
    $option2   = trim($_POST['option2'] ?? '');
    $option3   = trim($_POST['option3'] ?? '');
    $option4   = trim($_POST['option4'] ?? '');
    $correct   = (int)($_POST['correct_option'] ?? 0);

    if ($question === '') {
        $errors[] = 'Question text is required.';
    }
    if ($category === '') {
        $errors[] = 'Category is required.';
    }
    if (!in_array($difficulty, ['Easy','Medium','Hard'], true)) {
        $errors[] = 'Select a valid difficulty.';
    }
    if ($option1 === '' || $option2 === '' || $option3 === '' || $option4 === '') {
        $errors[] = 'All options are required.';
    }
    if ($correct < 1 || $correct > 4) {
        $errors[] = 'Select a valid correct option.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO questions (question, category, difficulty, option1, option2, option3, option4, correct_option)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$question, $category, $difficulty, $option1, $option2, $option3, $option4, $correct]);
            $success = 'Question added successfully.';
            // clear form
            $question = $category = $option1 = $option2 = $option3 = $option4 = '';
            $difficulty = 'Medium';
            $correct = 0;
        } catch (PDOException $e) {
            app_log_error('Admin add question', $e);
            $errors[] = 'Failed to add question due to a system error. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Question - Smart Interview System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js" defer></script>
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
            <a href="add_question.php" class="active">
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
                <div class="topbar-title">Add New Question</div>
                <div class="topbar-subtitle">Build and maintain your interview question bank.</div>
            </div>
        </div>

        <div class="section">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $e): ?>
                        <div><?php echo htmlspecialchars($e); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form id="addQuestionForm" method="post" action="">
                <div class="form-group">
                    <label for="question">Question Text</label>
                    <textarea id="question" name="question" placeholder="Enter the question here..."><?php
                        echo isset($question) ? htmlspecialchars($question) : '';
                    ?></textarea>
                </div>

                <div class="form-group">
                    <label for="category">Category (e.g. Data Structures, OOP)</label>
                    <input type="text" id="category" name="category"
                           value="<?php echo isset($category) ? htmlspecialchars($category) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="difficulty">Difficulty Level</label>
                    <select id="difficulty" name="difficulty">
                        <?php $d = $difficulty ?? 'Medium'; ?>
                        <option value="Easy"   <?php echo $d === 'Easy'   ? 'selected' : ''; ?>>Easy</option>
                        <option value="Medium" <?php echo $d === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="Hard"   <?php echo $d === 'Hard'   ? 'selected' : ''; ?>>Hard</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="option1">Option 1</label>
                    <input type="text" id="option1" name="option1"
                           value="<?php echo isset($option1) ? htmlspecialchars($option1) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="option2">Option 2</label>
                    <input type="text" id="option2" name="option2"
                           value="<?php echo isset($option2) ? htmlspecialchars($option2) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="option3">Option 3</label>
                    <input type="text" id="option3" name="option3"
                           value="<?php echo isset($option3) ? htmlspecialchars($option3) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="option4">Option 4</label>
                    <input type="text" id="option4" name="option4"
                           value="<?php echo isset($option4) ? htmlspecialchars($option4) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="correct_option">Correct Option</label>
                    <?php $c = $correct ?? 0; ?>
                    <select id="correct_option" name="correct_option">
                        <option value="">Select</option>
                        <option value="1" <?php echo $c === 1 ? 'selected' : ''; ?>>Option 1</option>
                        <option value="2" <?php echo $c === 2 ? 'selected' : ''; ?>>Option 2</option>
                        <option value="3" <?php echo $c === 3 ? 'selected' : ''; ?>>Option 3</option>
                        <option value="4" <?php echo $c === 4 ? 'selected' : ''; ?>>Option 4</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Save Question</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>