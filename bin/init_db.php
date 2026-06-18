<?php
// Run: php bin/init_db.php
require_once __DIR__ . '/../includes/config.php';

try {
    $db = getDB();
    $sql = file_get_contents(__DIR__ . '/../database/schema.sql');
    $statements = array_filter(array_map('trim', explode(";\n", $sql)));
    foreach ($statements as $stmt) {
        if ($stmt) {
            $db->exec($stmt);
        }
    }
    echo "Database initialized or already up-to-date.\n";
} catch (Exception $e) {
    echo "Failed to initialize DB: " . $e->getMessage() . "\n";
    exit(1);
}
