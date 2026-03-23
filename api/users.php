<?php
// api/users.php — REST-like API endpoint untuk CRUD users
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// Wajib login
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

// Hanya administrator boleh akses
if (!hasRole('administrator')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden. Administrator only.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Helper response
function resp(bool $ok, string $msg, array $data = []): void {
    echo json_encode(array_merge(['success' => $ok, 'message' => $msg], $data));
    exit;
}

// Helper sanitize
function str(string $key, array $src): string {
    return trim($src[$key] ?? '');
}

switch ($method) {

    // ── LIST all users ──
    case 'GET':
        $rows = db()->query(
            'SELECT id, username, nama, email, role, last_login, last_changed_password, created_at FROM users ORDER BY id ASC'
        )->fetchAll();
        resp(true, 'OK', ['data' => $rows]);

    // ── CREATE user ──
    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $username = str('username', $body);
        $nama     = str('nama',     $body);
        $email    = str('email',    $body);
        $password = str('password', $body);
        $role     = str('role',     $body);

        // Validasi
        if (!$username || !$nama || !$email || !$password || !$role) {
            resp(false, 'Semua field wajib diisi.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            resp(false, 'Format email tidak valid.');
        }
        if (!in_array($role, ['administrator','editor','viewer'], true)) {
            resp(false, 'Role tidak valid.');
        }
        if (strlen($password) < 6) {
            resp(false, 'Password minimal 6 karakter.');
        }

        // Cek duplikat
        $chk = db()->prepare('SELECT id FROM users WHERE username=? OR email=? LIMIT 1');
        $chk->execute([$username, $email]);
        if ($chk->fetch()) {
            resp(false, 'Username atau email sudah dipakai.');
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = db()->prepare(
            'INSERT INTO users (username, nama, email, password, role, last_changed_password) VALUES (?,?,?,?,?,NOW())'
        );
        $stmt->execute([$username, $nama, $email, $hash, $role]);
        resp(true, 'User berhasil dibuat.', ['id' => (int)db()->lastInsertId()]);

    // ── UPDATE user ──
    case 'PUT':
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $id   = (int)($body['id'] ?? 0);
        if (!$id) resp(false, 'ID tidak valid.');

        $nama     = str('nama',  $body);
        $email    = str('email', $body);
        $role     = str('role',  $body);
        $password = str('password', $body);

        if (!$nama || !$email || !$role) {
            resp(false, 'Nama, email, dan role wajib diisi.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            resp(false, 'Format email tidak valid.');
        }
        if (!in_array($role, ['administrator','editor','viewer'], true)) {
            resp(false, 'Role tidak valid.');
        }

        // Cek email duplikat (kecuali milik sendiri)
        $chk = db()->prepare('SELECT id FROM users WHERE email=? AND id!=? LIMIT 1');
        $chk->execute([$email, $id]);
        if ($chk->fetch()) resp(false, 'Email sudah dipakai user lain.');

        if ($password) {
            if (strlen($password) < 6) resp(false, 'Password minimal 6 karakter.');
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = db()->prepare(
                'UPDATE users SET nama=?, email=?, role=?, password=?, last_changed_password=NOW() WHERE id=?'
            );
            $stmt->execute([$nama, $email, $role, $hash, $id]);
        } else {
            $stmt = db()->prepare('UPDATE users SET nama=?, email=?, role=? WHERE id=?');
            $stmt->execute([$nama, $email, $role, $id]);
        }
        resp(true, 'User berhasil diupdate.');

    // ── DELETE user ──
    case 'DELETE':
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $id   = (int)($body['id'] ?? $_GET['id'] ?? 0);
        if (!$id) resp(false, 'ID tidak valid.');

        // Jangan hapus diri sendiri
        if ($id === (int)currentUser()['id']) {
            resp(false, 'Tidak bisa hapus akun sendiri.');
        }

        db()->prepare('DELETE FROM users WHERE id=?')->execute([$id]);
        resp(true, 'User berhasil dihapus.');

    default:
        http_response_code(405);
        resp(false, 'Method not allowed.');
}
