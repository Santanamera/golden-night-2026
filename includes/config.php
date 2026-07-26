<?php
// ============================================
// DATABASE CONFIGURATION
// Golden Night Prom Management System
// ============================================

define('APP_NAME', 'Golden Night 2026');
$defaultAppUrl = 'http://localhost/prom-system';
if (!empty($_SERVER['HTTP_HOST'])) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $defaultAppUrl = $scheme . '://' . $_SERVER['HTTP_HOST'];
}
define('APP_URL', getenv('APP_URL') ?: $defaultAppUrl);
define('UPLOAD_PATH', __DIR__ . '/../assets/uploads/');
define('TICKET_PRICE_SINGLE', 30000);
define('TICKET_PRICE_COUPLE', 50000);
define('TICKET_PRICE', TICKET_PRICE_SINGLE);

define('DB_HOST', getenv('MYSQLHOST') ?: '');
define('DB_USER', getenv('MYSQLUSER') ?: '');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');
define('DB_NAME', getenv('MYSQLDATABASE') ?: '');
define('DB_PORT', getenv('MYSQLPORT') ?: '3306');
define('DB_CHARSET', 'utf8mb4');
define('DB_SQLITE_PATH', __DIR__ . '/../database/golden_night.sqlite');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sqliteSchema(PDO $db): void {
    $db->exec('PRAGMA foreign_keys = ON;');
    $db->exec("CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        full_name TEXT DEFAULT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");
    $db->exec("CREATE TABLE IF NOT EXISTS prom_settings (
        setting_key TEXT PRIMARY KEY,
        setting_value TEXT
    )");
    $db->exec("CREATE TABLE IF NOT EXISTS tickets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ticket_id TEXT NOT NULL UNIQUE,
        qr_code TEXT,
        full_name TEXT NOT NULL,
        class_school TEXT DEFAULT NULL,
        phone TEXT DEFAULT NULL,
        student_type TEXT DEFAULT 'general',
        ticket_package TEXT DEFAULT 'single',
        payment_proof TEXT DEFAULT NULL,
        payment_status TEXT DEFAULT 'pending',
        ticket_status TEXT DEFAULT 'unused',
        seat_number TEXT DEFAULT NULL,
        amount_paid INTEGER DEFAULT 0,
        momo_reference TEXT DEFAULT NULL,
        used_at TEXT DEFAULT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");
    $db->exec("CREATE TABLE IF NOT EXISTS candidates (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        full_name TEXT NOT NULL,
        photo TEXT DEFAULT NULL,
        category TEXT NOT NULL,
        bio TEXT,
        class_school TEXT DEFAULT NULL,
        status TEXT DEFAULT 'pending',
        vote_count INTEGER DEFAULT 0,
        approved_at TEXT DEFAULT NULL,
        submitted_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");
    $db->exec("CREATE TABLE IF NOT EXISTS votes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ticket_id TEXT NOT NULL UNIQUE,
        king_candidate_id INTEGER NOT NULL,
        queen_candidate_id INTEGER NOT NULL,
        voted_at TEXT DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (king_candidate_id) REFERENCES candidates(id) ON DELETE RESTRICT ON UPDATE CASCADE,
        FOREIGN KEY (queen_candidate_id) REFERENCES candidates(id) ON DELETE RESTRICT ON UPDATE CASCADE
    )");
    $db->exec("CREATE TABLE IF NOT EXISTS scan_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ticket_id TEXT DEFAULT NULL,
        scan_result TEXT DEFAULT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");
    $db->exec("CREATE TABLE IF NOT EXISTS momo_requests (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        reference TEXT NOT NULL UNIQUE,
        phone TEXT DEFAULT NULL,
        amount INTEGER DEFAULT 0,
        name TEXT DEFAULT NULL,
        reason TEXT DEFAULT NULL,
        status TEXT DEFAULT 'pending',
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");

    $defaultAdminPassword = trim((string) getenv('ADMIN_PORTAL_PASSWORD'));
    if ($defaultAdminPassword !== '') {
        $hash = password_hash($defaultAdminPassword, PASSWORD_BCRYPT);
        $stmt = $db->prepare('INSERT OR IGNORE INTO admins (username, password, full_name) VALUES (?, ?, ?)');
        $stmt->execute(['admin', $hash, 'Admin']);
    }

    $settings = [
        'prom_name' => 'Golden Night 2026',
        'prom_date' => '14th August 2026',
        'prom_time' => '4:00 PM - 10:00 PM',
        'prom_venue' => 'RAKKA Hotel',
        'prom_venue_address' => 'KN 4 Ave, Kigali, Rwanda',
        'prom_venue_description' => 'Experience elegance at RAKKA Hotel, Kigali\'s premier hospitality destination. Our grand ballroom features state-of-the-art lighting, premium acoustics, and a sophisticated atmosphere perfect for an unforgettable prom night.',
        'prom_venue_phone' => '+250 780153944',
        'tickets_available' => '300',
        'voting_enabled' => '1',
        'allow_registration_without_payment' => '1',
    ];
    $stmt = $db->prepare("INSERT INTO prom_settings (setting_key, setting_value) VALUES (?, ?) ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value");
    foreach ($settings as $key => $value) {
        $stmt->execute([$key, $value]);
    }
}

function mysqlSchema(PDO $db): void {
    $db->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) DEFAULT NULL,
        password_hash VARCHAR(255) DEFAULT NULL,
        full_name VARCHAR(150) DEFAULT NULL,
        name VARCHAR(150) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->exec("CREATE TABLE IF NOT EXISTS prom_settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->exec("CREATE TABLE IF NOT EXISTS tickets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ticket_id VARCHAR(64) NOT NULL UNIQUE,
        qr_code TEXT NULL,
        full_name VARCHAR(150) NOT NULL,
        class_school VARCHAR(150) DEFAULT NULL,
        phone VARCHAR(50) DEFAULT NULL,
        student_type VARCHAR(20) DEFAULT 'general',
        ticket_package VARCHAR(20) DEFAULT 'single',
        payment_proof TEXT NULL,
        payment_status VARCHAR(20) DEFAULT 'pending',
        ticket_status VARCHAR(20) DEFAULT 'unused',
        seat_number VARCHAR(20) DEFAULT NULL,
        amount_paid INT DEFAULT 0,
        momo_reference VARCHAR(64) DEFAULT NULL,
        used_at DATETIME DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->exec("CREATE TABLE IF NOT EXISTS candidates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(150) NOT NULL,
        photo TEXT DEFAULT NULL,
        category VARCHAR(20) NOT NULL,
        bio TEXT,
        class_school VARCHAR(150) DEFAULT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        vote_count INT DEFAULT 0,
        approved_at DATETIME DEFAULT NULL,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->exec("CREATE TABLE IF NOT EXISTS votes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ticket_id VARCHAR(64) NOT NULL UNIQUE,
        king_candidate_id INT NOT NULL,
        queen_candidate_id INT NOT NULL,
        voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_votes_king FOREIGN KEY (king_candidate_id) REFERENCES candidates(id) ON DELETE RESTRICT ON UPDATE CASCADE,
        CONSTRAINT fk_votes_queen FOREIGN KEY (queen_candidate_id) REFERENCES candidates(id) ON DELETE RESTRICT ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->exec("CREATE TABLE IF NOT EXISTS scan_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ticket_id VARCHAR(64) DEFAULT NULL,
        scan_result VARCHAR(50) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->exec("CREATE TABLE IF NOT EXISTS momo_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        reference VARCHAR(64) NOT NULL UNIQUE,
        phone VARCHAR(50) DEFAULT NULL,
        amount INT DEFAULT 0,
        name VARCHAR(150) DEFAULT NULL,
        reason TEXT,
        status VARCHAR(20) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $adminColumns = array_column($db->query("SHOW COLUMNS FROM admins")->fetchAll(PDO::FETCH_ASSOC), 'Field');
    $columnsToAdd = [
        'password' => "ALTER TABLE admins ADD COLUMN password VARCHAR(255) DEFAULT NULL",
        'password_hash' => "ALTER TABLE admins ADD COLUMN password_hash VARCHAR(255) DEFAULT NULL",
        'full_name' => "ALTER TABLE admins ADD COLUMN full_name VARCHAR(150) DEFAULT NULL",
    ];

    foreach ($columnsToAdd as $column => $sql) {
        if (!in_array($column, $adminColumns, true)) {
            try {
                $db->exec($sql);
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate column name') === false) {
                    throw $e;
                }
            }
        }
    }

    $adminColumns = array_column($db->query("SHOW COLUMNS FROM admins")->fetchAll(PDO::FETCH_ASSOC), 'Field');
    if (in_array('name', $adminColumns, true) && in_array('full_name', $adminColumns, true)) {
        $db->exec("UPDATE admins SET full_name = name WHERE full_name IS NULL AND name IS NOT NULL");
    }

    try {
        $ticketColumn = $db->query("SHOW COLUMNS FROM tickets LIKE 'student_type'")->fetch(PDO::FETCH_ASSOC);
        if ($ticketColumn) {
            $ticketType = strtolower($ticketColumn['Type']);
            if (strpos($ticketType, "'general'") === false) {
                $db->exec("ALTER TABLE tickets MODIFY COLUMN student_type ENUM('internal','external','general') DEFAULT 'general'");
            }
        } else {
            $db->exec("ALTER TABLE tickets ADD COLUMN student_type ENUM('internal','external','general') DEFAULT 'general'");
        }

        $ticketColumns = array_column($db->query("SHOW COLUMNS FROM tickets")->fetchAll(PDO::FETCH_ASSOC), 'Field');
        if (!in_array('ticket_package', $ticketColumns, true)) {
            $db->exec("ALTER TABLE tickets ADD COLUMN ticket_package VARCHAR(20) DEFAULT 'single'");
        }
    } catch (PDOException $e) {
        error_log('MySQL ticket_type migration failed: ' . $e->getMessage());
    }

    $defaultAdminPassword = trim((string) getenv('ADMIN_PORTAL_PASSWORD'));
    if ($defaultAdminPassword !== '') {
        $hash = password_hash($defaultAdminPassword, PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO admins (username, password_hash, full_name)
                              VALUES (?, ?, ?)
                              ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), full_name = VALUES(full_name)");
        $stmt->execute(['admin', $hash, 'Admin']);
    }

    $settings = [
        'prom_name' => 'Golden Night 2026',
        'prom_date' => '14th August 2026',
        'prom_time' => '4:00 PM - 10:00 PM',
        'prom_venue' => 'RAKKA Hotel',
        'prom_venue_address' => 'KN 4 Ave, Kigali, Rwanda',
        'prom_venue_description' => 'Experience elegance at RAKKA Hotel, Kigali\'s premier hospitality destination. Our grand ballroom features state-of-the-art lighting, premium acoustics, and a sophisticated atmosphere perfect for an unforgettable prom night.',
        'prom_venue_phone' => '+250 780153944',
        'tickets_available' => '300',
        'voting_enabled' => '1',
        'allow_registration_without_payment' => '1',
    ];
    $stmt = $db->prepare("INSERT INTO prom_settings (setting_key, setting_value)
                          VALUES (?, ?)
                          ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    foreach ($settings as $key => $value) {
        $stmt->execute([$key, $value]);
    }
}

function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    if (DB_HOST !== '' && DB_USER !== '' && DB_NAME !== '') {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            mysqlSchema($pdo);
            return $pdo;
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
            exit;
        }
    }

    $dbDir = dirname(DB_SQLITE_PATH);
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
    }

    try {
        $pdo = new PDO('sqlite:' . DB_SQLITE_PATH, null, null, $options);
        sqliteSchema($pdo);
        return $pdo;
    } catch (PDOException $e) {
        die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
    }
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

function generateTicketID(): string {
    $db = getDB();
    do {
        $id = 'GN' . date('Y') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $stmt = $db->prepare('SELECT id FROM tickets WHERE ticket_id = ?');
        $stmt->execute([$id]);
    } while ($stmt->fetch());
    return $id;
}

function getSetting(mixed $key, mixed $default = ''): mixed {
    static $settings = [];
    if (empty($settings)) {
        try {
            $db = getDB();
            $stmt = $db->query('SELECT setting_key, setting_value FROM prom_settings');
            while ($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            if (is_array($key)) {
                return array_fill_keys($key, $default);
            }
            return $default;
        }
    }

    if (is_array($key)) {
        $values = [];
        foreach ($key as $name) {
            $values[$name] = $settings[$name] ?? $default;
        }
        return $values;
    }

    return $settings[$key] ?? $default;
}

function isAdminLoggedIn(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireAdmin(): void {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function clean(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function formatCurrency(float $amount): string {
    return 'Rwf ' . number_format($amount, 0, '.', ',');
}

function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>
