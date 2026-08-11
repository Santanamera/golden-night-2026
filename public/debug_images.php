<?php
/**
 * Debug endpoint to check if candidate images exist
 */

require_once '../includes/config.php';

header('Content-Type: application/json');

try {
    $db = getDB();
    
    // Get all candidate photo paths
    $candidates = $db->query("SELECT id, full_name, photo FROM candidates WHERE status = 'approved' LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
    
    $results = [];
    $uploadDir = __DIR__ . '/../assets/uploads/candidates/';
    
    foreach ($candidates as $cand) {
        $fullPath = $uploadDir . basename($cand['photo']);
        $exists = file_exists($fullPath);
        $size = $exists ? filesize($fullPath) : 0;
        $readable = $exists ? is_readable($fullPath) : false;
        $perms = $exists ? decoct(fileperms($fullPath) & 0777) : 'N/A';
        
        $results[] = [
            'name' => $cand['full_name'],
            'db_path' => $cand['photo'],
            'full_path' => $fullPath,
            'exists' => $exists,
            'size_bytes' => $size,
            'readable' => $readable,
            'permissions' => $perms,
            'url' => '/' . $cand['photo']
        ];
    }
    
    // Also list directory contents
    $dirContents = [];
    if (is_dir($uploadDir)) {
        $files = scandir($uploadDir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $uploadDir . $file;
                if (is_file($filePath)) {
                    $dirContents[] = [
                        'filename' => $file,
                        'size' => filesize($filePath),
                        'readable' => is_readable($filePath),
                        'permissions' => decoct(fileperms($filePath) & 0777)
                    ];
                }
            }
        }
    }
    
    jsonResponse([
        'success' => true,
        'upload_dir' => $uploadDir,
        'dir_exists' => is_dir($uploadDir),
        'dir_readable' => is_readable($uploadDir),
        'candidates' => $results,
        'directory_contents' => $dirContents,
        'upload_dir_permissions' => is_dir($uploadDir) ? decoct(fileperms($uploadDir) & 0777) : 'N/A'
    ]);
    
} catch (Throwable $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
?>
