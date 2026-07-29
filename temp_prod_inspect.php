<?php
$host = 'thomas.proxy.rlwy.net';
$port = 16108;
$user = 'root';
$pass = 'xHpfnunkpjODigjaItdDksjLXWfzaIWi';
$db = 'railway';
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    echo "TICKETS SCHEMA:\n";
    $stmt = $pdo->query('SHOW COLUMNS FROM tickets');
    foreach ($stmt->fetchAll() as $row) {
        echo $row['Field'] . ' ' . $row['Type'] . ' ' . ($row['Null'] ?: 'NO') . ' ' . ($row['Key'] ?: '-') . ' ' . ($row['Default'] ?? 'NULL') . "\n";
    }
    echo "\nADMINS:\n";
    $stmt = $pdo->query('SELECT id, username, password, password_hash, full_name, name FROM admins LIMIT 10');
    foreach ($stmt->fetchAll() as $row) {
        echo json_encode($row) . "\n";
    }
} catch (Exception $e) {
    echo 'ERR: ' . $e->getMessage() . "\n";
}
