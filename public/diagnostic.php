<?php
/**
 * Diagnostic endpoint - Check all candidates in database
 */

require_once '../includes/config.php';

header('Content-Type: application/json');

try {
    $db = getDB();
    
    // Get ALL candidates regardless of status
    $all = $db->query("SELECT id, full_name, category, status, photo, submitted_at, approved_at, bio FROM candidates ORDER BY submitted_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
    
    // Count by status
    $counts = $db->query("SELECT status, COUNT(*) as count FROM candidates GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
    
    // Check for auditioned candidates
    $auditioned = $db->query("SELECT COUNT(*) FROM candidates WHERE submitted_at IS NOT NULL")->fetchColumn();
    
    jsonResponse([
        'success' => true,
        'total_candidates' => count($all),
        'auditioned_count' => $auditioned,
        'counts_by_status' => $counts,
        'candidates_list' => $all
    ]);
    
} catch (Throwable $e) {
    jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
}
?>
