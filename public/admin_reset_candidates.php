<?php
/**
 * Admin Reset Endpoint - Clears and reseeds candidate data
 * Requires admin authentication or reset token
 */

require_once '../includes/config.php';

header('Content-Type: application/json');

// Check token or admin session
$token = $_GET['token'] ?? $_POST['token'] ?? '';
$resetToken = getenv('RESET_TOKEN') ?: 'admin-reset-key-2026';

if ($token !== $resetToken && !isAdminLoggedIn()) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
}

try {
    $db = getDB();
    
    // Clear related data
    $db->query("DELETE FROM votes");
    $db->query("DELETE FROM candidates");
    
    // Create demo photos
    $uploadDir = __DIR__ . '/../assets/uploads/candidates/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Create a 200x200 placeholder image
    $img = imagecreatetruecolor(200, 200);
    $bgColor = imagecolorallocate($img, 212, 175, 55); // Gold color
    $textColor = imagecolorallocate($img, 15, 15, 16); // Dark color
    imagefill($img, 0, 0, $bgColor);
    imagestring($img, 5, 45, 85, 'CANDIDATE', $textColor);
    imagestring($img, 2, 60, 110, 'PHOTO', $textColor);
    
    ob_start();
    imagepng($img);
    $pngData = ob_get_clean();
    imagedestroy($img);
    
    $candidates = [
        ['name' => 'Alain Uwizeye', 'category' => 'king', 'bio' => 'I am a passionate leader dedicated to bringing joy and unity to our prom night. Leadership and charisma define my candidacy.', 'class' => 'Senior A'],
        ['name' => 'David Mugabo', 'category' => 'king', 'bio' => 'Bringing energy and positive vibes to make this prom night unforgettable for everyone attending the celebration.', 'class' => 'Senior B'],
        ['name' => 'Felix Ndayisaba', 'category' => 'king', 'bio' => 'Committed to creating lasting memories and ensuring every student has an amazing time at the prom celebration.', 'class' => 'Senior A'],
        ['name' => 'Umwiza shanitah', 'category' => 'queen', 'bio' => 'Thank you very much. I think I\'m the best candidate and I believe I\'m the best choice because I\'ll try my best to support and be friendly with everyone and I hope everyone here have a great night love💔', 'class' => 'Wisdom school'],
        ['name' => 'Elise Ingabire', 'category' => 'queen', 'bio' => 'Excited to represent our class and make this prom a night filled with elegance, grace, and wonderful memories for all.', 'class' => 'Senior C'],
        ['name' => 'Grace Uwamahoro', 'category' => 'queen', 'bio' => 'Bringing sophistication and kindness to the prom celebration. I promise to make this night special for everyone.', 'class' => 'Senior B'],
    ];
    
    $db->beginTransaction();
    
    foreach ($candidates as $cand) {
        // Create placeholder photo file
        $photoFilename = 'cand_' . time() . '_' . bin2hex(random_bytes(4)) . '.png';
        $photoPath = $uploadDir . $photoFilename;
        
        $bytesWritten = file_put_contents($photoPath, $pngData);
        if ($bytesWritten === false) {
            throw new Exception("Failed to write image file: $photoPath. Check directory permissions.");
        }
        
        if (!chmod($photoPath, 0644)) {
            throw new Exception("Failed to chmod image file: $photoPath");
        }
        
        $photoWebPath = 'assets/uploads/candidates/' . $photoFilename;
        
        // Insert candidate
        $stmt = $db->prepare("
            INSERT INTO candidates (full_name, photo, category, bio, class_school, status, vote_count)
            VALUES (?, ?, ?, ?, ?, 'approved', 0)
        ");
        $stmt->execute([$cand['name'], $photoWebPath, $cand['category'], $cand['bio'], $cand['class']]);
        
        usleep(100000);
    }
    
    $db->commit();
    
    $approved = $db->query("SELECT COUNT(*) FROM candidates WHERE status = 'approved'")->fetchColumn();
    
    jsonResponse([
        'success' => true,
        'message' => 'Candidates reset and reseeded successfully',
        'total_seeded' => count($candidates),
        'total_approved' => $approved
    ]);

} catch (Throwable $e) {
    if (isset($db)) $db->rollBack();
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
}
?>
