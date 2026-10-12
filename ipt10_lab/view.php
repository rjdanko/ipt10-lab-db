<?php
require_once 'db_connect.php';

$id = trim($_GET['id'] ?? '');
if ($id === '') { die('Invalid student ID'); }

// TODO(7): one placeholder for the id; select every displayed column.
$stmt = $conn->prepare('SELECT id, first_name, middle_name, last_name, birthday, sex, email,
    student_number, program, enrolment_date, created_at, updated_at FROM students WHERE id = ?');
$stmt->bind_param('s', $id);
$stmt->execute();

// TODO(8): get_result() first, then one associative row.
$result = $stmt->get_result();
$row = $result->fetch_assoc();

require 'header.php';
if (!$row) {
    echo '<div class="alert alert-warning">Student not found.</div>';
    echo '<a class="btn btn-secondary" href="index.php">Back</a>';
} else {
    $name = trim($row['first_name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . $row['last_name']);
    // TODO(9): every field goes through htmlspecialchars().
    $fields = [
        'Student ID' => $row['id'], 'Full Name' => $name, 'Birthday' => $row['birthday'],
        'Sex' => $row['sex'], 'Email' => $row['email'], 'Student Number' => $row['student_number'],
        'Program' => $row['program'], 'Enrolment Date' => $row['enrolment_date'],
        'Created At' => $row['created_at'], 'Updated At' => $row['updated_at'],
    ];
?>
<h2>Student Details (mysqli)</h2>
<dl class="row card card-body">
  <?php foreach ($fields as $label => $value): ?>
    <dt class="col-sm-3"><?= $label ?></dt><dd class="col-sm-9"><?= htmlspecialchars((string)$value) ?></dd>
  <?php endforeach; ?>
</dl>
<a class="btn btn-secondary" href="index.php">Back</a>
<?php
}
$stmt->close();
$conn->close();
require 'footer.php';
