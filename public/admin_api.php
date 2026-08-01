<?php
// ============================================
// ADMIN DATA API
// Handles: stats, tickets, candidates, votes
// ============================================

require_once '../includes/config.php';

header('Content-Type: application/json');

// Require admin login
if (!isAdminLoggedIn()) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
}

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

// Handle JSON body POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    $action = $body['action'] ?? $action;
}

$db = getDB();

switch ($action) {

    // ============================================
    // GET DASHBOARD STATS
    // ============================================
    case 'stats':
        $stats = [];
        
        $stats['total_tickets'] = $db->query("SELECT COUNT(*) FROM tickets WHERE ticket_status != 'cancelled'")->fetchColumn();
        $stats['confirmed']     = $db->query("SELECT COUNT(*) FROM tickets WHERE payment_status = 'confirmed'")->fetchColumn();
        $stats['used']          = $db->query("SELECT COUNT(*) FROM tickets WHERE ticket_status = 'used'")->fetchColumn();
        $stats['pending_payments'] = $db->query("SELECT COUNT(*) FROM tickets WHERE payment_status = 'pending'")->fetchColumn();
        $stats['rejected_payments'] = $db->query("SELECT COUNT(*) FROM tickets WHERE payment_status = 'rejected'")->fetchColumn();
        $stats['review_queue'] = $db->query("SELECT COUNT(*) FROM tickets WHERE payment_status IN ('pending', 'rejected')")->fetchColumn();
        $stats['revenue']       = $db->query("SELECT COALESCE(SUM(amount_paid),0) FROM tickets WHERE payment_status = 'confirmed'")->fetchColumn();
        $stats['votes']         = $db->query("SELECT COUNT(*) FROM votes")->fetchColumn();
        $stats['candidates']    = $db->query("SELECT COUNT(*) FROM candidates WHERE status = 'approved'")->fetchColumn();
        $stats['pending_candidates'] = $db->query("SELECT COUNT(*) FROM candidates WHERE status = 'pending'")->fetchColumn();
        
        $stats['internal_count']   = $stats['confirmed'];
        $stats['external_count']   = 0;
        $stats['internal_revenue'] = $stats['revenue'];
        $stats['external_revenue'] = 0;
        
        jsonResponse(['success' => true, 'stats' => $stats]);

    // ============================================
    // GET ALL TICKETS
    // ============================================
    case 'tickets':
        $search = clean($_GET['search'] ?? '');
        $filter = clean($_GET['filter'] ?? 'all');
        
        $sql = "SELECT * FROM tickets WHERE 1=1";
        $params = [];
        
        if ($search) {
            $sql .= " AND (full_name LIKE ? OR ticket_id LIKE ? OR phone LIKE ?)";
            $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
        }
        if ($filter === 'pending') { $sql .= " AND payment_status = 'pending'"; }
        if ($filter === 'rejected') { $sql .= " AND payment_status = 'rejected'"; }
        if ($filter === 'confirmed') { $sql .= " AND payment_status = 'confirmed'"; }
        if ($filter === 'used') { $sql .= " AND ticket_status = 'used'"; }
        
        $sql .= " ORDER BY created_at DESC LIMIT 500";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        jsonResponse(['success' => true, 'tickets' => $stmt->fetchAll()]);

    // ============================================
    // CONFIRM PAYMENT
    // ============================================
    case 'confirm_payment':
        $ticketId = clean($body['ticket_id'] ?? '');
        if (!$ticketId) jsonResponse(['success' => false, 'message' => 'Ticket ID required']);
        
        $stmt = $db->prepare("UPDATE tickets SET payment_status = 'confirmed' WHERE ticket_id = ?");
        $stmt->execute([$ticketId]);
        
        if ($stmt->rowCount()) {
            jsonResponse(['success' => true, 'message' => 'Payment confirmed.']);
        } else {
            jsonResponse(['success' => false, 'message' => 'Ticket not found.']);
        }

    // ============================================
    // REJECT PAYMENT
    // ============================================
    case 'reject_payment':
        $ticketId = clean($body['ticket_id'] ?? '');
        $db->prepare("UPDATE tickets SET payment_status = 'rejected' WHERE ticket_id = ?")
           ->execute([$ticketId]);
        jsonResponse(['success' => true, 'message' => 'Payment rejected.']);

    // ============================================
    // GET CANDIDATES
    // ============================================
    case 'candidates':
        $stmt = $db->query("SELECT * FROM candidates ORDER BY status DESC, category, full_name");
        jsonResponse(['success' => true, 'candidates' => $stmt->fetchAll()]);

    // ============================================
    // UPDATE CANDIDATE STATUS
    // ============================================
    case 'update_candidate':
        $id     = (int) ($body['id'] ?? 0);
        $status = $body['status'] ?? 'pending';
        if (!in_array($status, ['approved', 'rejected', 'pending'])) {
            jsonResponse(['success' => false, 'message' => 'Invalid status']);
        }
        if ($status === 'approved') {
            $stmt = $db->prepare("UPDATE candidates SET status = ?, approved_at = CURRENT_TIMESTAMP WHERE id = ?");
        } else {
            $stmt = $db->prepare("UPDATE candidates SET status = ?, approved_at = NULL WHERE id = ?");
        }
        $stmt->execute([$status, $id]);
        jsonResponse(['success' => true, 'message' => "Candidate {$status}."]);

    // ============================================
    // GET VOTES DATA
    // ============================================
    case 'votes':
        // King standings
        $kingStmt = $db->query("
            SELECT c.id, c.full_name, c.class_school, c.vote_count
            FROM candidates c
            WHERE c.category = 'king' AND c.status = 'approved'
            ORDER BY c.vote_count DESC
        ");
        $kings = $kingStmt->fetchAll();
        
        // Queen standings
        $queenStmt = $db->query("
            SELECT c.id, c.full_name, c.class_school, c.vote_count
            FROM candidates c
            WHERE c.category = 'queen' AND c.status = 'approved'
            ORDER BY c.vote_count DESC
        ");
        $queens = $queenStmt->fetchAll();
        
        // Voter list
        $voteStmt = $db->query("
            SELECT v.ticket_id, v.voted_at,
                   k.full_name as king_name, q.full_name as queen_name
            FROM votes v
            LEFT JOIN candidates k ON v.king_candidate_id = k.id
            LEFT JOIN candidates q ON v.queen_candidate_id = q.id
            ORDER BY v.voted_at DESC
            LIMIT 200
        ");
        $votes = $voteStmt->fetchAll();
        
        jsonResponse(['success' => true, 'king' => $kings, 'queen' => $queens, 'votes' => $votes]);

    // ============================================
    // AUDITION SUBMISSIONS
    // ============================================
    case 'auditions':
        $stmt = $db->query("SELECT * FROM candidates ORDER BY submitted_at DESC");
        jsonResponse(['success' => true, 'candidates' => $stmt->fetchAll()]);

    default:
        jsonResponse(['success' => false, 'message' => 'Unknown action.'], 400);
}
?>
