<?php
// ============================================
// MTN MoMo Callback — Payment Status Update
// MTN calls this URL after payment is approved/failed
// ============================================
require_once '../includes/config.php';
header('Content-Type: application/json');

$input  = json_decode(file_get_contents('php://input'), true);
$ref    = $input['financialTransactionId'] ?? $input['referenceId'] ?? '';
$status = strtolower($input['status'] ?? '');

if (!$ref) { http_response_code(400); exit; }

try {
    $db = getDB();
    if ($status === 'successful') {
        $db->prepare("UPDATE momo_requests SET status='completed' WHERE reference=?")->execute([$ref]);
        // Also auto-confirm the ticket if reference matches
        $db->prepare("UPDATE tickets SET payment_status='confirmed' WHERE momo_reference=?")->execute([$ref]);
    } elseif (in_array($status, ['failed','rejected','timeout'])) {
        $db->prepare("UPDATE momo_requests SET status='failed' WHERE reference=?")->execute([$ref]);
    }
} catch (PDOException $e) { /* log silently */ }

http_response_code(200);
echo json_encode(['received' => true]);
?>
