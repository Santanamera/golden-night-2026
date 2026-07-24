<?php
// Initialize Railway MySQL database

$host = 'thomas.proxy.rlwy.net';
$port = 16108;
$user = 'root';
$password = 'xHpfnunkpjODigjaItdDksjLXWfzaIWi';
$database = 'railway';

try {
    $conn = new PDO(
        "mysql:host=$host;port=$port;dbname=$database",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✓ Connected to Railway MySQL\n";
    
    // Read schema file
    $schema = file_get_contents('database/schema.sql');
    
    // Split queries by semicolon and execute
    $queries = explode(';', $schema);
    $executed = 0;
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (empty($query)) continue;
        
        try {
            $conn->exec($query);
            $executed++;
        } catch (Exception $e) {
            echo "⚠ Query error (may be duplicate): " . $e->getMessage() . "\n";
        }
    }
    
    echo "✓ Database initialized successfully ($executed queries executed)\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
