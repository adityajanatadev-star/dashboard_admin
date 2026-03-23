<?php
// ============================================
// config.php — konfigurasi utama aplikasi
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dashboard_admin');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');

// Cookie settings
define('COOKIE_NAME',     'dash_session');
define('COOKIE_LIFETIME', 60 * 60 * 8);  // 8 jam
define('COOKIE_PATH',     '/');
define('COOKIE_DOMAIN',   '');            // kosong = current domain
define('COOKIE_SECURE',   false);         // set true kalau pakai HTTPS
define('COOKIE_HTTPONLY',  true);
define('COOKIE_SAMESITE', 'Strict');

// App
define('APP_NAME',    'AdminDash');
define('APP_VERSION', '1.0.0');

// Singleton PDO connection
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
        }
    }
    return $pdo;
}
