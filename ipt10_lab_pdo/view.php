<?php
// view.php PDO
require_once 'config.php';

$id = trim($_GET['id'] ?? '');
if ($id === '') { die('Invalid student ID'); }

$stmt = $pdo->prepare('SELECT id, first_name, middle_name, last_name, birthday, sex, email,
    student_number, program, enrolment_date, created_at, updated_at FROM students WHERE id = ?');

// TODO: pass the id inside the execute() array | no bind_param() in PDO.
$stmt->execute([$id]);
$row = $stmt->fetch();

require 'header.php';
// TODO: branch on an empty $row and render every field with htmlspecialchars().
if (!$row) {
    echo '<div class="alert alert-warning">Student not found.</div>';
    echo '<a class="btn btn-secondary" href="index.php">Back to list</a>';
} else {
    $name = trim($row['first_name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . $row['last_name']);
    $fields = [
        'Student ID' => $row['id'], 'Full Name' => $name, 'Birthday' => $row['birthday'],
        'Sex' => $row['sex'], 'Email' => $row['email'], 'Student Number' => $row['student_number'],
        'Program' => $row['program'], 'Enrolment Date' => $row['enrolment_date'],
        'Created At' => $row['created_at'], 'Updated At' => $row['updated_at'],
    ];
?>
<h2>Student Details (PDO)</h2>
<dl class="row card card-body">
  <?php foreach ($fields as $label => $value): ?>
    <dt class="col-sm-3"><?= $label ?></dt><dd class="col-sm-9"><?= htmlspecialchars((string)$value) ?></dd>
  <?php endforeach; ?>
</dl>
<a class="btn btn-secondary" href="index.php">Back</a>
<?php
}
require 'footer.php';
