<?php
// Initialize Railway MySQL database

$host = getenv('MYSQLHOST') ?: 'localhost';
$port = getenv('MYSQLPORT') ?: 3306;
$user = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: '';
$database = getenv('MYSQLDATABASE') ?: 'railway';

if ($password === '') {
    die("Set MYSQLPASSWORD in your environment before running this script.\n");
}

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
