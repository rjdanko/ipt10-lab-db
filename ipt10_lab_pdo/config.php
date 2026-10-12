<?php
// config.php GUIDE (fill in TODO 21-22)
declare(strict_types=1);

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'ip10_lab';

try {
    // TODO (21): build the DSN from the same four values you used with
    // mysqli. Hint: 'mysql:host=...;dbname=...;charset=utf8mb4'
    $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
    $pdo = new PDO(
        $dsn,
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_STRINGIFY_FETCHES  => false,
            // TODO (22): you enabled manual transaction control. Add
            // the AUTOCOMMIT attribute and explain in your report what
            // changes in every page you port from Part 1.
            PDO::ATTR_AUTOCOMMIT         => true,
        ]
    );
} catch (PDOException $e) {
    error_log('DB connect failed: ' . $e->getMessage());
    die('Database unavailable');
}
