<?php
// ============================================================
//  config/db.php – Central database connection (PDO)
// ============================================================

define('rootUrl', '/CorpPortal/');


define('DB_HOST', 'localhost');
define('DB_NAME', 'role_portal');
define('DB_USER', 'root');       // change for production
define('DB_PASS', '');           // change for production
define('DB_CHARSET', 'utf8mb4');
define('DB_PORT', '3307');

$dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Show friendly error; never expose raw DB errors to users
    die('
    <div style="font-family:sans-serif;max-width:500px;margin:80px auto;padding:32px;
                border:1px solid #fca5a5;border-radius:12px;background:#fff5f5;color:#b91c1c;">
        <h2 style="margin:0 0 12px">Database Connection Failed</h2>
        <p>Could not connect to MySQL. Please check your <code>config/db.php</code> settings
        and ensure MySQL is running via XAMPP.</p>
        <pre style="font-size:12px;background:#fee2e2;padding:10px;border-radius:6px;">'
        . htmlspecialchars($e->getMessage()) . '</pre>
    </div>');
}
