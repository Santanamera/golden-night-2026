<?php
/**
 * Candidate Data Seeding Endpoint
 * Used to populate the database with test candidate records
 * Run once to initialize candidate data across all deployments
 */

require_once '../includes/config.php';

header('Content-Type: application/json');

// Simple auth check - can be called from admin or deployment script
$token = $_GET['token'] ?? $_POST['token'] ?? '';
$expectedToken = getenv('RESEED_TOKEN') ?: 'dev-reseed-key-2026';

if ($token !== $expectedToken) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized. Provide ?token=...'], 401);
}

try {
    $db = getDB();
    
    // Check if candidates already exist
    $existing = $db->query("SELECT COUNT(*) FROM candidates WHERE status = 'approved'")->fetchColumn();
    
    if ($existing > 0) {
        jsonResponse(['success' => true, 'message' => "Already seeded ($existing approved candidates). Skipping.", 'count' => $existing]);
    }
    
    // Create demo candidate photos (1x1 PNG placeholder)
    $uploadDir = __DIR__ . '/../assets/uploads/candidates/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $pngData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQABAA4A7wBAQwAAAABJRU5ErkJggg==');
    
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
        file_put_contents($photoPath, $pngData);
        chmod($photoPath, 0644);
        
        $photoWebPath = 'assets/uploads/candidates/' . $photoFilename;
        
        // Insert candidate
        $stmt = $db->prepare("
            INSERT INTO candidates (full_name, photo, category, bio, class_school, status, vote_count)
            VALUES (?, ?, ?, ?, ?, 'approved', 0)
        ");
        $stmt->execute([$cand['name'], $photoWebPath, $cand['category'], $cand['bio'], $cand['class']]);
        
        usleep(100000); // Small delay to ensure unique filenames
    }
    
    $db->commit();
    
    $approved = $db->query("SELECT COUNT(*) FROM candidates WHERE status = 'approved'")->fetchColumn();
    
    jsonResponse([
        'success' => true,
        'message' => 'Candidates seeded successfully',
        'total_seeded' => count($candidates),
        'total_approved' => $approved
    ]);

} catch (Throwable $e) {
    if (isset($db)) $db->rollBack();
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
}
?>
