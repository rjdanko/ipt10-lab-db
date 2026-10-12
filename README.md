# IPT10 Database Programming Laboratory Activity
## From mysqli to PDO: Building Database-Driven Web Applications

This repository contains two complete implementations of a student record CRUD (Create, Read, Update, Delete) web application built in PHP 8.2+ and MySQL:

1. **`ipt10_lab/`**: Procedural and hybrid **mysqli** implementation.
2. **`ipt10_lab_pdo/`**: Object-oriented **PDO (PHP Data Objects)** implementation.

---

## 1. Project Structure

```text
db-programming-labact/
├── ipt10_lab/              # Part 1: mysqli CRUD Application
│   ├── db_connect.php      # Database connection & error reporting (mysqli)
│   ├── header.php          # Shared navigation and UI header
│   ├── footer.php          # Shared HTML footer
│   ├── index.php           # Student listing (Read All)
│   ├── view.php            # Single student profile (Read One)
│   ├── create.php          # Add new student form & validation (Create)
│   ├── edit.php            # Edit student form & validation (Update)
│   └── delete.php          # Two-step deletion confirmation (Delete)
├── ipt10_lab_pdo/          # Part 2: PDO CRUD Application
│   ├── config.php          # PDO connection & attributes configuration
│   ├── header.php          # Shared navigation and UI header
│   ├── footer.php          # Shared HTML footer
│   ├── index.php           # Student listing (PDO query/fetchAll)
│   ├── view.php            # Single student profile (PDO prepared statement)
│   ├── create.php          # Add new student form & validation (PDO execute)
│   ├── edit.php            # Edit student form & validation (PDO rowCount)
│   └── delete.php          # Two-step deletion confirmation (PDO rowCount)
├── index.php               # Main portal linking to both applications
├── phpinfo.php             # PHP configuration & extension verification
├── README.md               # Setup and execution guide
└── .gitignore              # Ignored files and local environment configs
```

---

## 2. Environment Setup & Requirements

- **PHP**: 8.2 or higher
- **MySQL / MariaDB**: 8.0+ / 10.4+
- **Apache Web Server** (via XAMPP)

### Step 2.1: Enable Required PHP Extensions
1. Open XAMPP Control Panel.
2. Click **Config** next to Apache and select `php.ini`.
3. Ensure the following extension lines are uncommented (no leading semicolon `;`):
   ```ini
   extension=mysqli
   extension=pdo_mysql
   ```
4. Save the file and restart the Apache service via XAMPP Control Panel.
5. Verify extensions by browsing to `http://localhost/db-programming-labact/phpinfo.php` and searching for **mysqli** and **pdo_mysql** (both must indicate "enabled").

---

## 3. Database Configuration & Schema

1. Open phpMyAdmin (`http://localhost/phpmyadmin`) or the MySQL CLI.
2. Execute the following SQL script to create the database and `students` table:

```sql
CREATE DATABASE IF NOT EXISTS ip10_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE ip10_lab;

CREATE TABLE students (
  id CHAR(36) NOT NULL,
  first_name VARCHAR(100) NOT NULL,
  middle_name VARCHAR(100) NULL,
  last_name VARCHAR(100) NOT NULL,
  birthday DATE NOT NULL,
  sex ENUM('Male', 'Female') NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  student_number VARCHAR(50) UNIQUE NOT NULL,
  program VARCHAR(200) NOT NULL,
  enrolment_date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO students
  (id, first_name, middle_name, last_name, birthday, sex, email, student_number, program, enrolment_date)
VALUES
  (UUID(), 'Maria', 'Cruz', 'Santos', '2005-06-15', 'Female', 'maria@school.edu', 'STU001', 'Bachelor of Science in Information Technology', '2024-09-01'),
  (UUID(), 'Juan', 'Delos', 'Dela Cruz', '2005-03-22', 'Male', 'juan@school.edu', 'STU002', 'Bachelor of Science in Information Technology', '2024-09-01'),
  (UUID(), 'Ana', 'Lopez', 'Reyes', '2005-11-08', 'Female', 'ana@school.edu', 'STU003', 'Bachelor of Science in Computer Science', '2024-09-02');
```

---

## 4. Running the Applications

Ensure Apache and MySQL are running in the XAMPP Control Panel.

- **Main Navigation Portal**:
  `http://localhost/db-programming-labact/`
- **Part 1 (mysqli Version)**:
  `http://localhost/db-programming-labact/ipt10_lab/`
- **Part 2 (PDO Version)**:
  `http://localhost/db-programming-labact/ipt10_lab_pdo/`

---

## 5. Form Validation Rules

Both applications enforce strict server-side validation:
- **First Name**: Required, 2-100 characters, letters and spaces only (`preg_match('/^[A-Za-z\s]+$/', ...)`).
- **Middle Name**: Optional, max 100 characters, letters and spaces only.
- **Last Name**: Required, 2-100 characters, letters and spaces only.
- **Birthday**: Required, valid calendar date in `YYYY-MM-DD` format.
- **Sex**: Required, strictly `Male` or `Female` (matching table `ENUM`).
- **Email**: Required, valid email format verified via `filter_var(..., FILTER_VALIDATE_EMAIL)`.
- **Student Number**: Required, alphanumeric up to 50 characters, unique.
- **Program**: Required, up to 200 characters.
- **Enrolment Date**: Required, valid calendar date in `YYYY-MM-DD` format.

---

## 6. Comparison: mysqli vs PDO

| Feature | `mysqli` | `PDO` |
| :--- | :--- | :--- |
| **Database Support** | MySQL only | 12+ database drivers |
| **Connection Method** | `new mysqli($host, $user, $pass, $db)` | `new PDO($dsn, $user, $pass, $options)` |
| **Error Handling** | `mysqli_report(MYSQLI_REPORT_ERROR \| ...)` | `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` |
| **Parameter Binding** | Separate `bind_param('sss...', $v1, $v2)` | Array passed directly into `execute([$v1, $v2])` |
| **Default Fetch Mode** | Must call `get_result()->fetch_assoc()` | Configured via `PDO::ATTR_DEFAULT_FETCH_MODE` |
| **Affected Rows** | `$stmt->affected_rows` | `$stmt->rowCount()` |
