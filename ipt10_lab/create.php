<?php
require_once 'db_connect.php';

$errors = [];
$first_name = $middle_name = $last_name = $email = $birthday = $sex = '';
$student_number = $program = $enrolment_date = '';
$ok = false;

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

    // TODO(10): validate first and last names.
    // Rules: required, 2-100 chars, letters and spaces only.
    if (empty($first_name) || strlen($first_name) < 2 || strlen($first_name) > 100 || !preg_match('/^[A-Za-z\s]+$/', $first_name)) {
        $errors['first_name'] = 'First name is required: 2-100 letters and spaces only.';
    }
    if (empty($last_name) || strlen($last_name) < 2 || strlen($last_name) > 100 || !preg_match('/^[A-Za-z\s]+$/', $last_name)) {
        $errors['last_name'] = 'Last name is required: 2-100 letters and spaces only.';
    }
    if ($middle_name !== '' && (strlen($middle_name) > 100 || !preg_match('/^[A-Za-z\s]+$/', $middle_name))) {
        $errors['middle_name'] = 'Middle name: letters and spaces only, max 100.';
    }

    // TODO(11): validate email and birthday.
    // Hint: filter_var() with FILTER_VALIDATE_EMAIL; date pattern YYYY-MM-DD.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
        $errors['email'] = 'A valid email address is required.';
    }
    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $birthday, $m) || !checkdate((int)$m[2], (int)$m[3], (int)$m[1])) {
        $errors['birthday'] = 'Birthday must be a valid date (YYYY-MM-DD).';
    }
    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $enrolment_date, $m) || !checkdate((int)$m[2], (int)$m[3], (int)$m[1])) {
        $errors['enrolment_date'] = 'Enrolment date must be a valid date (YYYY-MM-DD).';
    }

    // TODO(12): validate sex against the ENUM values.
    // Hint: in_array() with strict comparison against which two strings?
    if (!in_array($sex, ['Male', 'Female'], true)) {
        $errors['sex'] = 'Select Male or Female.';
    }

    // Schema makes these NOT NULL (student_number also UNIQUE), so they are required.
    if (!preg_match('/^[A-Za-z0-9]{1,50}$/', $student_number)) {
        $errors['student_number'] = 'Student number is required: alphanumeric, max 50.';
    }
    if ($program === '' || strlen($program) > 200) {
        $errors['program'] = 'Program is required, max 200 characters.';
    }

    if (empty($errors)) {
        try {
            // TODO(13): write the INSERT. The id column takes UUID() in SQL;
            // every other column takes a placeholder. How many placeholders?
            $stmt = $conn->prepare('INSERT INTO students
                (id, first_name, middle_name, last_name, birthday, sex, email, student_number, program, enrolment_date)
                VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $mid = $middle_name === '' ? null : $middle_name;
            // TODO(14): bind every value. What single type code fits ten strings?
            $stmt->bind_param('sssssssss', $first_name, $mid, $last_name, $birthday, $sex,
                $email, $student_number, $program, $enrolment_date);
            $stmt->execute();
            // No insert_id with a UUID key: affected_rows confirms the write.
            if ($stmt->affected_rows === 1) {
                $ok = true;
                $first_name = $middle_name = $last_name = $email = $birthday = $sex = '';
                $student_number = $program = $enrolment_date = '';
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() !== 1062) { throw $e; }
            if (str_contains($e->getMessage(), 'student_number')) {
                $errors['student_number'] = 'Student number already exists.';
            } else {
                $errors['email'] = 'Email address already exists.';
            }
        }
    }
}
require 'header.php';
?>
<h2>Add New Student (mysqli)</h2>
<?php if ($ok): ?><div class="alert alert-success">Student created successfully!</div><?php endif; ?>
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
  <div><button class="btn btn-primary">Create</button> <a class="btn btn-secondary" href="index.php">Cancel</a></div>
</form>
<?php $conn->close(); require 'footer.php'; ?>
