<?php
// ============================================
// auth.php — helper autentikasi & session
// ============================================

require_once __DIR__ . '/config.php';

// ── Session & Cookie ──────────────────────────

/**
 * Mulai session dengan pengaturan yang aman.
 */
function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => COOKIE_LIFETIME,
            'path'     => COOKIE_PATH,
            'domain'   => COOKIE_DOMAIN,
            'secure'   => COOKIE_SECURE,
            'httponly' => COOKIE_HTTPONLY,
            'samesite' => COOKIE_SAMESITE,
        ]);
        session_name(COOKIE_NAME);
        session_start();
    }
}

/**
 * Cek apakah user sudah login.
 */
function isLoggedIn(): bool {
    startSecureSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Ambil data user yang sedang login.
 */
function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    return [
        'id'       => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'nama'     => $_SESSION['nama'],
        'email'    => $_SESSION['email'],
        'role'     => $_SESSION['role'],
    ];
}

/**
 * Redirect ke login kalau belum login.
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /dashboard/login.php');
        exit;
    }
}

/**
 * Cek role user.
 */
function hasRole(string ...$roles): bool {
    $user = currentUser();
    return $user && in_array($user['role'], $roles, true);
}

// ── Login / Logout ────────────────────────────

/**
 * Proses login user.
 * Return: ['success' => bool, 'message' => string]
 */
function attemptLogin(string $username, string $password): array {
    $username = trim($username);

    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Username dan password wajib diisi.'];
    }

    $stmt = db()->prepare(
        'SELECT id, username, nama, email, password, role FROM users WHERE username = ? LIMIT 1'
    );
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Username atau password salah.'];
    }

    // Update last_login
    db()->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')
       ->execute([$user['id']]);

    // Set session
    startSecureSession();
    session_regenerate_id(true);
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama']     = $user['nama'];
    $_SESSION['email']    = $user['email'];
    $_SESSION['role']     = $user['role'];

    return ['success' => true, 'message' => 'Login berhasil.'];
}

/**
 * Logout user dan hapus session.
 */
function logout(): void {
    startSecureSession();
    $_SESSION = [];
    session_destroy();
    // Hapus cookie
    setcookie(COOKIE_NAME, '', [
        'expires'  => time() - 3600,
        'path'     => COOKIE_PATH,
        'domain'   => COOKIE_DOMAIN,
        'secure'   => COOKIE_SECURE,
        'httponly' => COOKIE_HTTPONLY,
        'samesite' => COOKIE_SAMESITE,
    ]);
    header('Location: /dashboard/login.php');
    exit;
}
