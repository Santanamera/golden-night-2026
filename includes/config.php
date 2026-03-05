<?php
// ============================================
// DATABASE CONFIGURATION
// Golden Night Prom Management System
// ============================================

define('DB_HOST', 'sql311.infinityfree.com');
define('DB_USER', 'if0_41311380');
define('DB_PASS', 'F3glBaEgnP');
define('DB_NAME', 'if0_41311380_prom');
define('DB_CHARSET', 'utf8mb4');

// Application settings
define('APP_NAME', 'Golden Night 2026');
define('APP_URL', 'http://goldennight2026.kesug.com');
define('UPLOAD_PATH', __DIR__ . '/../assets/uploads/');
define('TICKET_PRICE_INTERNAL', 25000);
define('TICKET_PRICE_EXTERNAL', 30000);

// Session config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// DATABASE CONNECTION (PDO)
// ============================================
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

/**
 * Generate unique ticket ID
 */
function generateTicketID(): string {
    $db = getDB();
    do {
        $id = 'GN' . date('Y') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $stmt = $db->prepare("SELECT id FROM tickets WHERE ticket_id = ?");
        $stmt->execute([$id]);
    } while ($stmt->fetch());
    return $id;
}

/**
 * Get prom setting
 */
function getSetting(string $key, string $default = ''): string {
    static $settings = [];
    if (empty($settings)) {
        try {
            $db = getDB();
            $stmt = $db->query("SELECT setting_key, setting_value FROM prom_settings");
            while ($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            return $default;
        }
    }
    return $settings[$key] ?? $default;
}

/**
 * Check admin login
 */
function isAdminLoggedIn(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Require admin login
 */
function requireAdmin(): void {
    if (!isAdminLoggedIn()) {
        header('Location: ' . APP_URL . '/admin/login.php');
        exit;
    }
}

/**
 * Sanitize input
 */
function clean(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency (IDR)
 */
function formatCurrency(float $amount): string {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * JSON response helper
 */
function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>
