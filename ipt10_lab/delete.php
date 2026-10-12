<?php
require_once 'db_connect.php';

$id = trim($_GET['id'] ?? '');
if ($id === '') { die('Invalid student ID'); }

// Step 1: look up the student so the confirmation screen can name them.
// TODO (18): prepared SELECT of first_name and last_name by id.
$s = $conn->prepare('SELECT first_name, last_name FROM students WHERE id = ?');
$s->bind_param('s', $id);
$s->execute();

// Hint: get_result() is a method | do not forget its parentheses.
$r = $s->get_result()->fetch_assoc();
$s->close();

require 'header.php';
if (!$r) {
    echo '<div class="alert alert-warning">Student not found.</div>';
    echo '<a class="btn btn-secondary" href="index.php">Back to list</a>';
    $conn->close();
    require 'footer.php';
    exit;
}

// Step 2: delete only when the confirmation form was submitted.
// TODO (19): test the right request condition (GET shows the form,
// POST performs the delete), then run the DELETE and check its result.
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $d = $conn->prepare('DELETE FROM students WHERE id = ?');
    $d->bind_param('s', $id);
    $d->execute();
    if ($d->affected_rows === 1) {
        echo '<div class="alert alert-success">Student deleted successfully.</div>';
    } else {
        echo '<div class="alert alert-danger">Failed to delete student.</div>';
    }
    echo '<a class="btn btn-secondary" href="index.php">Back to list</a>';
    $conn->close();
} else {
    // TODO (20): render the confirmation screen with the student's name,
    // a hidden field carrying the id, the Yes/Cancel buttons, and
    // then link back to index.php.
?>
<h2>Delete Student (mysqli)</h2>
<p>Are you sure you want to delete <strong><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></strong>?</p>
<form method="POST">
  <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
  <button class="btn btn-danger">Yes, delete</button>
  <a class="btn btn-secondary" href="index.php">Cancel</a>
</form>
<?php
}
require 'footer.php';
