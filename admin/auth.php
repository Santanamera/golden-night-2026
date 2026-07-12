<?php
// ============================================
// ADMIN AUTH API
// POST { username, password }
// ============================================

require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'logout') {
    session_unset();
    session_destroy();
    setcookie(session_name(), '', time() - 3600, '/');
    jsonResponse(['success' => true, 'message' => 'Logged out.']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$username = clean($input['username'] ?? '');
$password = $input['password'] ?? '';

if (empty($username) || empty($password)) {
    jsonResponse(['success' => false, 'message' => 'Username and password required.']);
}

try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    $storedPassword = $admin['password'] ?? $admin['password_hash'] ?? '';
    $displayName = $admin['full_name'] ?? $admin['name'] ?? $admin['username'] ?? $username;
    $forcedAdminPassword = trim((string) (getenv('ADMIN_PORTAL_PASSWORD') ?: ''));

    if ($admin && $admin['username'] === 'admin' && $forcedAdminPassword !== '') {
        if (!hash_equals($forcedAdminPassword, $password)) {
            sleep(1);
            jsonResponse(['success' => false, 'message' => 'Invalid username or password.']);
        }

        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name'] = $displayName;

        jsonResponse([
            'success' => true,
            'admin' => [
                'id'       => $admin['id'],
                'username' => $admin['username'],
                'name'     => $displayName
            ]
        ]);
    }

    if (!$admin || !$storedPassword || !password_verify($password, $storedPassword)) {
        // Slight delay to prevent brute force
        sleep(1);
        jsonResponse(['success' => false, 'message' => 'Invalid username or password.']);
    }

    // Set session
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_name'] = $displayName;

    jsonResponse([
        'success' => true,
        'admin' => [
            'id'       => $admin['id'],
            'username' => $admin['username'],
            'name'     => $displayName
        ]
    ]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Authentication error.'], 500);
}
