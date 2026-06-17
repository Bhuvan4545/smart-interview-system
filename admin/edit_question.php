<?php
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: delete_question.php');
    exit;
}

// Load existing question
$stmt = $pdo->prepare('SELECT * FROM questions WHERE id = ?');
$stmt->execute([$id]);
$questionRow = $stmt->fetch();

if (!$questionRow) {
    header('Location: delete_question.php');
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
        $upd = $pdo->prepare(
            'UPDATE questions
             SET question = ?, category = ?, difficulty = ?, option1 = ?, option2 = ?, option3 = ?, option4 = ?, correct_option = ?
             WHERE id = ?'
        );
        $upd->execute([$question, $category, $difficulty, $option1, $option2, $option3, $option4, $correct, $id]);
        $success = 'Question updated successfully.';

        // reload updated data
        $stmt->execute([$id]);
        $questionRow = $stmt->fetch();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Question - Smart Interview System</title>
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
            <a href="delete_question.php" class="active">
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
                <div class="topbar-title">Edit Question #<?php echo (int)$questionRow['id']; ?></div>
                <div class="topbar-subtitle">Update the question details.</div>
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

            <form method="post" action="">
                <div class="form-group">
                    <label for="question">Question Text</label>
                    <textarea id="question" name="question"><?php
                        echo htmlspecialchars($questionRow['question']);
                    ?></textarea>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category"
                           value="<?php echo htmlspecialchars($questionRow['category']); ?>">
                </div>

                <div class="form-group">
                    <label for="difficulty">Difficulty Level</label>
                    <?php $d = $questionRow['difficulty'] ?: 'Medium'; ?>
                    <select id="difficulty" name="difficulty">
                        <option value="Easy"   <?php echo $d === 'Easy'   ? 'selected' : ''; ?>>Easy</option>
                        <option value="Medium" <?php echo $d === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="Hard"   <?php echo $d === 'Hard'   ? 'selected' : ''; ?>>Hard</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="option1">Option 1</label>
                    <input type="text" id="option1" name="option1"
                           value="<?php echo htmlspecialchars($questionRow['option1']); ?>">
                </div>
                <div class="form-group">
                    <label for="option2">Option 2</label>
                    <input type="text" id="option2" name="option2"
                           value="<?php echo htmlspecialchars($questionRow['option2']); ?>">
                </div>
                <div class="form-group">
                    <label for="option3">Option 3</label>
                    <input type="text" id="option3" name="option3"
                           value="<?php echo htmlspecialchars($questionRow['option3']); ?>">
                </div>
                <div class="form-group">
                    <label for="option4">Option 4</label>
                    <input type="text" id="option4" name="option4"
                           value="<?php echo htmlspecialchars($questionRow['option4']); ?>">
                </div>

                <div class="form-group">
                    <label for="correct_option">Correct Option</label>
                    <?php $c = (int)$questionRow['correct_option']; ?>
                    <select id="correct_option" name="correct_option">
                        <option value="1" <?php echo $c === 1 ? 'selected' : ''; ?>>Option 1</option>
                        <option value="2" <?php echo $c === 2 ? 'selected' : ''; ?>>Option 2</option>
                        <option value="3" <?php echo $c === 3 ? 'selected' : ''; ?>>Option 3</option>
                        <option value="4" <?php echo $c === 4 ? 'selected' : ''; ?>>Option 4</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update Question</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>