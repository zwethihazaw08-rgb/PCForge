<?php

function db(): PDO
{
    // Reuse the same connection whenever db() is called during a request.
    static $connection = null;

    if ($connection !== null) {
        return $connection;
    }

    // Default local XAMPP settings. Change these to match your MySQL setup.
    $host = '127.0.0.1';
    // XAMPP MariaDB uses 3307 because the separate MySQL80 service uses 3306.
    $port = 3307;
    $database = 'pcforge';
    $username = 'root';
    $password = '';

    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";

    try {
        $connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $exception) {
        // Keep connection details in the server log, away from visitors.
        error_log('PCForge database connection failed: ' . $exception->getMessage());
        if (PHP_SAPI === 'cli') {
            fwrite(STDERR, "Unable to connect to the database. Check config/database.php and the database service.\n");
            exit(1);
        }
        http_response_code(500);
        exit('Unable to connect to the database. Please try again later.');
    }

    return $connection;
}
