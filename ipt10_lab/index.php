<?php
require_once 'db_connect.php';

// TODO(4): static SELECT, newest enrolments first, ties broken by id.
$sql = 'SELECT id, first_name, last_name, email, enrolment_date FROM students ORDER BY enrolment_date DESC, id DESC';
$result = mysqli_query($conn, $sql);
require 'header.php';
?>
<h2>All Student Records (mysqli)</h2>
<table class="table table-striped table-bordered bg-white">
  <thead>
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Enrolled</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php // TODO(5): mysqli_fetch_assoc returns ['col' => value] rows, false at the end. ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <tr>
      <td><?= htmlspecialchars($row['id']) ?></td>
      <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
      <td><?= htmlspecialchars($row['email']) ?></td>
      <td><?= htmlspecialchars($row['enrolment_date']) ?></td>
      <td>
        <a class="btn btn-sm btn-info" href="view.php?id=<?= urlencode($row['id']) ?>">View</a>
        <a class="btn btn-sm btn-warning" href="edit.php?id=<?= urlencode($row['id']) ?>">Edit</a>
        <a class="btn btn-sm btn-danger" href="delete.php?id=<?= urlencode($row['id']) ?>">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
<?php
// TODO(6): free the result first, then close the connection it belongs to.
mysqli_free_result($result);
mysqli_close($conn);
require 'footer.php';
