<?php
// edit.php PDO
require_once 'config.php';

$id = trim($_GET['id'] ?? '');
if ($id === '') { die('Invalid student ID'); }

// 1. Read ?id=, then SELECT the row with execute([$id]) and fetch().
$stmt = $pdo->prepare('SELECT first_name, middle_name, last_name, birthday, sex, email, student_number, program, enrolment_date
    FROM students WHERE id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
    require 'header.php';
    echo '<div class="alert alert-warning">Student not found.</div>';
    echo '<a class="btn btn-secondary" href="index.php">Back to list</a>';
    require 'footer.php';
    exit;
}

$errors = [];
$msg = '';
$first_name = $row['first_name'];
$middle_name = $row['middle_name'] ?? '';
$last_name = $row['last_name'];
$email = $row['email'];
$birthday = $row['birthday'];
$sex = $row['sex'];
$student_number = $row['student_number'];
$program = $row['program'];
$enrolment_date = $row['enrolment_date'];

// 3. On POST -> validate exactly as before, then:
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $birthday = trim($_POST['birthday'] ?? '');
    $sex = trim($_POST['sex'] ?? '');
    $student_number = trim($_POST['student_number'] ?? '');
    $program = trim($_POST['program'] ?? '');
    $enrolment_date = trim($_POST['enrolment_date'] ?? '');

    if (empty($first_name) || strlen($first_name) < 2 || strlen($first_name) > 100 || !preg_match('/^[A-Za-z\s]+$/', $first_name)) {
        $errors['first_name'] = 'First name is required: 2-100 letters and spaces only.';
    }
    if (empty($last_name) || strlen($last_name) < 2 || strlen($last_name) > 100 || !preg_match('/^[A-Za-z\s]+$/', $last_name)) {
        $errors['last_name'] = 'Last name is required: 2-100 letters and spaces only.';
    }
    if ($middle_name !== '' && (strlen($middle_name) > 100 || !preg_match('/^[A-Za-z\s]+$/', $middle_name))) {
        $errors['middle_name'] = 'Middle name: letters and spaces only, max 100.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
        $errors['email'] = 'A valid email address is required.';
    }
    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $birthday, $m) || !checkdate((int)$m[2], (int)$m[3], (int)$m[1])) {
        $errors['birthday'] = 'Birthday must be a valid date (YYYY-MM-DD).';
    }
    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $enrolment_date, $m) || !checkdate((int)$m[2], (int)$m[3], (int)$m[1])) {
        $errors['enrolment_date'] = 'Enrolment date must be a valid date (YYYY-MM-DD).';
    }
    if (!in_array($sex, ['Male', 'Female'], true)) {
        $errors['sex'] = 'Select Male or Female.';
    }
    if (!preg_match('/^[A-Za-z0-9]{1,50}$/', $student_number)) {
        $errors['student_number'] = 'Student number is required: alphanumeric, max 50.';
    }
    if ($program === '' || strlen($program) > 200) {
        $errors['program'] = 'Program is required, max 200 characters.';
    }

    if (empty($errors)) {
        try {
            $sql = 'UPDATE students
                    SET first_name = ?, middle_name = ?, last_name = ?,
                        birthday = ?, sex = ?, email = ?, student_number = ?,
                        program = ?, enrolment_date = ?
                    WHERE id = ?';

            $mid = $middle_name === '' ? null : $middle_name;
            // TODO: which field must be the LAST element of the execute() array? (LOOK!)
            // The $id must be the last element to bind to WHERE id = ?
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $first_name,
                $mid,
                $last_name,
                $birthday,
                $sex,
                $email,
                $student_number,
                $program,
                $enrolment_date,
                $id
            ]);

            // TODO: check rowCount(). Explain in your report why a successful
            // update can still report 0.
            $msg = $stmt->rowCount() > 0 ? 'Student updated successfully.' : 'No changes were made.';
        } catch (PDOException $e) {
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1062) {
                if (str_contains($e->getMessage(), 'student_number')) {
                    $errors['student_number'] = 'Student number already exists.';
                } else {
                    $errors['email'] = 'Email address already exists.';
                }
            } else {
                throw $e;
            }
        }
    }
}
require 'header.php';
// 2. On GET -> render the pre-filled form (reuse Part 1 markup).
?>
<h2>Edit Student (PDO)</h2>
<?php if ($msg): ?><div class="alert alert-info"><?= $msg ?> <a href="view.php?id=<?= urlencode($id) ?>">View</a></div><?php endif; ?>
<form method="POST" class="card card-body" novalidate>
  <div class="mb-3">
    <label class="form-label" for="first_name">First Name</label>
    <input class="form-control" type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>">
    <?php if (isset($errors['first_name'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['first_name']) ?></div><?php endif; ?>
  </div>
  <div class="mb-3">
    <label class="form-label" for="middle_name">Middle Name (optional)</label>
    <input class="form-control" type="text" id="middle_name" name="middle_name" value="<?= htmlspecialchars($middle_name) ?>">
    <?php if (isset($errors['middle_name'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['middle_name']) ?></div><?php endif; ?>
  </div>
  <div class="mb-3">
    <label class="form-label" for="last_name">Last Name</label>
    <input class="form-control" type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>">
    <?php if (isset($errors['last_name'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['last_name']) ?></div><?php endif; ?>
  </div>
  <div class="mb-3">
    <label class="form-label" for="birthday">Birthday</label>
    <input class="form-control" type="date" id="birthday" name="birthday" value="<?= htmlspecialchars($birthday) ?>">
    <?php if (isset($errors['birthday'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['birthday']) ?></div><?php endif; ?>
  </div>
  <div class="mb-3">
    <label class="form-label" for="sex">Sex</label>
    <select class="form-select" id="sex" name="sex">
      <option value="">-- select --</option>
      <option value="Male" <?= $sex === 'Male' ? 'selected' : '' ?>>Male</option>
      <option value="Female" <?= $sex === 'Female' ? 'selected' : '' ?>>Female</option>
    </select>
    <?php if (isset($errors['sex'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['sex']) ?></div><?php endif; ?>
  </div>
  <div class="mb-3">
    <label class="form-label" for="email">Email</label>
    <input class="form-control" type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>">
    <?php if (isset($errors['email'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['email']) ?></div><?php endif; ?>
  </div>
  <div class="mb-3">
    <label class="form-label" for="student_number">Student Number</label>
    <input class="form-control" type="text" id="student_number" name="student_number" value="<?= htmlspecialchars($student_number) ?>">
    <?php if (isset($errors['student_number'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['student_number']) ?></div><?php endif; ?>
  </div>
  <div class="mb-3">
    <label class="form-label" for="program">Program</label>
    <input class="form-control" type="text" id="program" name="program" value="<?= htmlspecialchars($program) ?>">
    <?php if (isset($errors['program'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['program']) ?></div><?php endif; ?>
  </div>
  <div class="mb-3">
    <label class="form-label" for="enrolment_date">Enrolment Date</label>
    <input class="form-control" type="date" id="enrolment_date" name="enrolment_date" value="<?= htmlspecialchars($enrolment_date) ?>">
    <?php if (isset($errors['enrolment_date'])): ?><div class="text-danger small"><?= htmlspecialchars($errors['enrolment_date']) ?></div><?php endif; ?>
  </div>
  <div><button class="btn btn-primary">Save Changes</button> <a class="btn btn-secondary" href="index.php">Cancel</a></div>
</form>
<?php require 'footer.php'; ?>
