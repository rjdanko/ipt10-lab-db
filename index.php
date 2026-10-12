<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>IPT10 Database Programming Laboratory</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="p-5 mb-4 bg-white rounded-3 shadow-sm border">
      <div class="container-fluid py-2">
        <h1 class="display-6 fw-bold">IPT10 Database Programming Laboratory Activity</h1>
        <p class="col-md-10 fs-5 text-muted">
          From mysqli to PDO: Building Database-Driven Web Applications with Student Records CRUD.
        </p>
        <hr class="my-4">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="card h-100 border-primary">
              <div class="card-body">
                <span class="badge bg-primary mb-2">Part 1</span>
                <h3 class="card-title h4">Procedural / Hybrid mysqli Version</h3>
                <p class="card-text text-muted">
                  Complete CRUD application built with <code>mysqli</code> extension, prepared statements with <code>bind_param()</code>, and manual result resource freeing.
                </p>
                <a href="ipt10_lab/index.php" class="btn btn-primary">Launch mysqli Version &rarr;</a>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card h-100 border-success">
              <div class="card-body">
                <span class="badge bg-success mb-2">Part 2</span>
                <h3 class="card-title h4">Object-Oriented PDO Version</h3>
                <p class="card-text text-muted">
                  Complete CRUD application built with <code>PDO</code>, DSN connection string, array-based parameter execution, and <code>rowCount()</code> confirmation.
                </p>
                <a href="ipt10_lab_pdo/index.php" class="btn btn-success">Launch PDO Version &rarr;</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex justify-content-between text-muted small px-2">
      <span>Database: <code>ip10_lab</code> &bull; Table: <code>students</code></span>
      <span><a href="phpinfo.php" class="text-decoration-none">Check phpinfo.php</a></span>
    </div>
  </div>
</body>
</html>
