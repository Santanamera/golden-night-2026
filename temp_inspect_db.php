<?php
$host = 'thomas.proxy.rlwy.net';
$port = 16108;
$user = 'root';
$pass = 'xHpfnunkpjODigjaItdDksjLXWfzaIWi';
$db = 'railway';
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    $stmt = $pdo->query('SHOW COLUMNS FROM tickets');
    foreach ($stmt->fetchAll() as $row) {
        echo $row['Field'] . ' ' . $row['Type'] . ' ' . $row['Null'] . ' ' . $row['Key'] . ' ' . $row['Default'] . "\n";
    }
} catch (Exception $e) {
    echo 'ERR: ' . $e->getMessage();
}
