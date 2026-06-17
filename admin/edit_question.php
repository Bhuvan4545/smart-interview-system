<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/validation.php';
require_once '../includes/html_head.php';
require_once '../includes/sidebar.php';
require_once '../includes/alerts.php';

require_admin();

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

    $errors = validate_question($question, $category, $difficulty, $option1, $option2, $option3, $option4, $correct);

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

render_html_head('Edit Question');
?>
<div class="layout">
    <?php render_admin_sidebar('delete_question.php'); ?>

    <main class="main-content">
        <div class="topbar">
            <div>
                <div class="topbar-title">Edit Question #<?php echo (int)$questionRow['id']; ?></div>
                <div class="topbar-subtitle">Update the question details.</div>
            </div>
        </div>

        <div class="section">
            <?php render_alerts($errors, $success); ?>

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
                    <select id="difficulty" name="difficulty">
                        <?php $d = $questionRow['difficulty']; ?>
                        <option value="Easy" <?php echo $d === 'Easy' ? 'selected' : ''; ?>>Easy</option>
                        <option value="Medium" <?php echo $d === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="Hard" <?php echo $d === 'Hard' ? 'selected' : ''; ?>>Hard</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Options</label>
                    <input type="text" name="option1" placeholder="Option 1"
                           value="<?php echo htmlspecialchars($questionRow['option1']); ?>">
                    <input type="text" name="option2" placeholder="Option 2"
                           value="<?php echo htmlspecialchars($questionRow['option2']); ?>">
                    <input type="text" name="option3" placeholder="Option 3"
                           value="<?php echo htmlspecialchars($questionRow['option3']); ?>">
                    <input type="text" name="option4" placeholder="Option 4"
                           value="<?php echo htmlspecialchars($questionRow['option4']); ?>">
                </div>

                <div class="form-group">
                    <label for="correct_option">Correct Option (1-4)</label>
                    <select id="correct_option" name="correct_option">
                        <?php $c = (int)$questionRow['correct_option']; ?>
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
