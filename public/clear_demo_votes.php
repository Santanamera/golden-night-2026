<?php
/**
 * Clear Demo Votes - Allows retesting vote functionality
 */

require_once '../includes/config.php';

header('Content-Type: application/json');

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$expectedToken = getenv('DEMO_TOKEN') ?: 'demo-clear-votes-2026';

if ($token !== $expectedToken && !isAdminLoggedIn()) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
}

try {
    $db = getDB();
    
    // Find and clear votes for demo ticket GN2026BBDCCE
    $result = $db->prepare("DELETE FROM votes WHERE ticket_id IN (SELECT id FROM tickets WHERE ticket_number = ?)");
    $result->execute(['GN2026BBDCCE']);
    
    // Clear vote count in candidates
    $db->query("UPDATE candidates SET vote_count = 0");
    
    jsonResponse([
        'success' => true,
        'message' => 'Demo votes cleared successfully',
        'cleared_votes' => $result->rowCount()
    ]);
    
} catch (Throwable $e) {
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
}
?>
