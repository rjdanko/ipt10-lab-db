<?php
// delete.php PDO
require_once 'config.php';

$id = trim($_GET['id'] ?? '');
if ($id === '') { die('Invalid student ID'); }

// Keep your two-step confirm-then-delete flow from Part 1.

// Step 1: confirm screen
$stmt = $pdo->prepare('SELECT first_name, last_name FROM students WHERE id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();

require 'header.php';
// TODO: handle the not-found case
if (!$student) {
    echo '<div class="alert alert-warning">Student not found.</div>';
    echo '<a class="btn btn-secondary" href="index.php">Back to list</a>';
    require 'footer.php';
    exit;
}

// Step 2: deletion, only after the POST confirmation
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $d = $pdo->prepare('DELETE FROM students WHERE id = ?');
    $d->execute([$id]);
    
    // TODO: branch on rowCount() to report success or failure.
    if ($d->rowCount() === 1) {
        echo '<div class="alert alert-success">Student deleted successfully.</div>';
    } else {
        echo '<div class="alert alert-danger">Failed to delete student.</div>';
    }
    echo '<a class="btn btn-secondary" href="index.php">Back to list</a>';
} else {
?>
<h2>Delete Student (PDO)</h2>
<p>Are you sure you want to delete <strong><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></strong>?</p>
<form method="POST">
  <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
  <button class="btn btn-danger">Yes, delete</button>
  <a class="btn btn-secondary" href="index.php">Cancel</a>
</form>
<?php
}
require 'footer.php';
