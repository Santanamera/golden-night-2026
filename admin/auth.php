<?php
// ============================================
// ADMIN AUTH API
// POST { username, password }
// ============================================

require_once '../includes/config.php';

header('Content-Type: application/json');

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

    if (!$admin || !password_verify($password, $admin['password'])) {
        // Slight delay to prevent brute force
        sleep(1);
        jsonResponse(['success' => false, 'message' => 'Invalid username or password.']);
    }

    // Set session
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_name'] = $admin['full_name'];

    jsonResponse([
        'success' => true,
        'admin' => [
            'id'       => $admin['id'],
            'username' => $admin['username'],
            'name'     => $admin['full_name']
        ]
    ]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Authentication error.'], 500);
}
