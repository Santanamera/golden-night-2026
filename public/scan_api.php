<?php
// ============================================
// QR SCAN API (for admin scanner)
// POST { ticket_id }
// ============================================

require_once '../includes/config.php';

header('Content-Type: application/json');

// Basic admin check (session or API key)
// In production, add proper auth check: requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$ticketId = clean($input['ticket_id'] ?? '');

if (empty($ticketId)) {
    jsonResponse(['result' => 'invalid', 'message' => 'No ticket ID provided.']);
}

try {
    $db = getDB();
    
    // Find ticket
    $stmt = $db->prepare("SELECT * FROM tickets WHERE ticket_id = ?");
    $stmt->execute([$ticketId]);
    $ticket = $stmt->fetch();
    
    if (!$ticket) {
        // Log invalid scan
        $db->prepare("INSERT INTO scan_logs (ticket_id, scan_result) VALUES (?, 'invalid')")
           ->execute([$ticketId]);
           
        jsonResponse([
            'result'  => 'invalid',
            'message' => 'Ticket not found in system.'
        ]);
    }
    
    // Check if payment confirmed
    if ($ticket['payment_status'] !== 'confirmed') {
        jsonResponse([
            'result'  => 'invalid',
            'message' => 'Payment not confirmed for this ticket.',
            'ticket'  => ['full_name' => $ticket['full_name']]
        ]);
    }
    
    // Check if already used
    if ($ticket['ticket_status'] === 'used') {
        $db->prepare("INSERT INTO scan_logs (ticket_id, scan_result) VALUES (?, 'already_used')")
           ->execute([$ticketId]);
           
        jsonResponse([
            'result'  => 'already_used',
            'message' => 'This ticket has already been used for entry.',
            'ticket'  => [
                'full_name'    => $ticket['full_name'],
                'class_school' => $ticket['class_school'],
                'used_at'      => $ticket['used_at']
            ]
        ]);
    }
    
    // VALID - mark as used
    $db->prepare("UPDATE tickets SET ticket_status = 'used', used_at = NOW() WHERE ticket_id = ?")
       ->execute([$ticketId]);
       
    $db->prepare("INSERT INTO scan_logs (ticket_id, scan_result) VALUES (?, 'valid')")
       ->execute([$ticketId]);
    
    jsonResponse([
        'result'  => 'valid',
        'message' => 'Entry granted!',
        'ticket'  => [
            'ticket_id'    => $ticket['ticket_id'],
            'full_name'    => $ticket['full_name'],
            'class_school' => $ticket['class_school'],
            'student_type' => $ticket['student_type'],
            'seat_number'  => $ticket['seat_number']
        ]
    ]);

} catch (PDOException $e) {
    jsonResponse(['result' => 'error', 'message' => 'Database error.'], 500);
}
?>
