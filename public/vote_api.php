<?php
// ============================================
// VOTING API
// GET  ?action=verify&ticket_id=XXX
// GET  ?action=candidates
// POST { ticket_id, king_id, queen_id }
// ============================================

require_once '../includes/config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// ============================================
// GET: VERIFY TICKET
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'verify') {
    $ticketId = clean($_GET['ticket_id'] ?? '');
    
    if (empty($ticketId)) {
        jsonResponse(['success' => false, 'message' => 'Ticket ID is required.']);
    }
    
    try {
        $db = getDB();
        
        // Check voting enabled
        $votingEnabled = getSetting('voting_enabled', '1');
        if ($votingEnabled !== '1') {
            jsonResponse(['success' => false, 'message' => 'Voting is currently closed.']);
        }
        
        // Find ticket
        $stmt = $db->prepare("SELECT * FROM tickets WHERE ticket_id = ? AND payment_status = 'confirmed' AND ticket_status != 'cancelled'");
        $stmt->execute([$ticketId]);
        $ticket = $stmt->fetch();
        
        if (!$ticket) {
            jsonResponse(['success' => false, 'message' => 'Invalid or unconfirmed ticket. Payment must be confirmed to vote.']);
        }
        
        // Check if already voted
        $voteStmt = $db->prepare("SELECT id FROM votes WHERE ticket_id = ?");
        $voteStmt->execute([$ticketId]);
        $alreadyVoted = (bool) $voteStmt->fetch();
        
        jsonResponse([
            'success'       => true,
            'ticket'        => [
                'ticket_id'    => $ticket['ticket_id'],
                'full_name'    => $ticket['full_name'],
                'class_school' => $ticket['class_school'],
                'student_type' => $ticket['student_type'],
                'already_voted'=> $alreadyVoted
            ]
        ]);
        
    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Database error.'], 500);
    }
}

// ============================================
// GET: LOAD CANDIDATES
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'candidates') {
    try {
        $db = getDB();
        $stmt = $db->query("SELECT id, full_name, photo, category, bio, class_school FROM candidates WHERE status = 'approved' ORDER BY category, full_name");
        $all = $stmt->fetchAll();
        
        $king = array_filter($all, fn($c) => $c['category'] === 'king');
        $queen = array_filter($all, fn($c) => $c['category'] === 'queen');
        
        jsonResponse([
            'success' => true,
            'king'    => array_values($king),
            'queen'   => array_values($queen)
        ]);
    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Failed to load candidates.'], 500);
    }
}

// ============================================
// POST: SUBMIT VOTE
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $ticketId  = clean($input['ticket_id'] ?? '');
    $kingId    = (int) ($input['king_id'] ?? 0);
    $queenId   = (int) ($input['queen_id'] ?? 0);
    
    if (empty($ticketId) || !$kingId || !$queenId) {
        jsonResponse(['success' => false, 'message' => 'Invalid vote data.']);
    }
    
    try {
        $db = getDB();
        
        // Verify ticket again
        $stmt = $db->prepare("SELECT id FROM tickets WHERE ticket_id = ? AND payment_status = 'confirmed'");
        $stmt->execute([$ticketId]);
        if (!$stmt->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Invalid or unconfirmed ticket.']);
        }
        
        // Check not already voted
        $stmt = $db->prepare("SELECT id FROM votes WHERE ticket_id = ?");
        $stmt->execute([$ticketId]);
        if ($stmt->fetch()) {
            jsonResponse(['success' => false, 'message' => 'You have already voted.']);
        }
        
        // Verify candidates exist and are approved
        $stmt = $db->prepare("SELECT id FROM candidates WHERE id = ? AND status = 'approved' AND category = 'king'");
        $stmt->execute([$kingId]);
        if (!$stmt->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Invalid king candidate.']);
        }
        
        $stmt = $db->prepare("SELECT id FROM candidates WHERE id = ? AND status = 'approved' AND category = 'queen'");
        $stmt->execute([$queenId]);
        if (!$stmt->fetch()) {
            jsonResponse(['success' => false, 'message' => 'Invalid queen candidate.']);
        }
        
        // Begin transaction
        $db->beginTransaction();
        
        // Insert vote
        $stmt = $db->prepare("INSERT INTO votes (ticket_id, king_candidate_id, queen_candidate_id) VALUES (?,?,?)");
        $stmt->execute([$ticketId, $kingId, $queenId]);
        
        // Update vote counts
        $db->prepare("UPDATE candidates SET vote_count = vote_count + 1 WHERE id = ?")->execute([$kingId]);
        $db->prepare("UPDATE candidates SET vote_count = vote_count + 1 WHERE id = ?")->execute([$queenId]);
        
        $db->commit();
        
        jsonResponse(['success' => true, 'message' => 'Vote submitted successfully!']);
        
    } catch (PDOException $e) {
        $db->rollBack();
        // Handle duplicate vote
        if ($e->getCode() == 23000) {
            jsonResponse(['success' => false, 'message' => 'You have already voted.']);
        }
        jsonResponse(['success' => false, 'message' => 'Database error.'], 500);
    }
}

jsonResponse(['success' => false, 'message' => 'Invalid request.'], 400);
?>
