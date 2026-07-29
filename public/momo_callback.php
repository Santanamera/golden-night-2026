<?php
// ============================================
// MTN MoMo Callback — Payment Status Update
// MTN calls this URL after payment is approved/failed.
// Also used as a passive log — any incoming call
// is appended to callback_log.txt for debugging.
// ============================================
require_once '../includes/config.php';
header('Content-Type: application/json');

$body  = file_get_contents('php://input');
$input = json_decode($body, true) ?? [];

// Append to debug log (non-fatal — file write errors are ignored)
@file_put_contents(
    __DIR__ . '/callback_log.txt',
    date('Y-m-d H:i:s') . "\n" .
    ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN') . "\n" .
    $body . "\n\n",
    FILE_APPEND
);

$ref    = $input['financialTransactionId'] ?? $input['referenceId'] ?? '';
$status = strtolower($input['status'] ?? '');

if (!$ref) {
    http_response_code(400);
    echo json_encode(['error' => 'missing reference']);
    exit;
}

try {
    $db = getDB();
    if ($status === 'successful') {
        $db->prepare("UPDATE momo_requests SET status='completed' WHERE reference=?")->execute([$ref]);
        $db->prepare("UPDATE tickets SET payment_status='pending' WHERE momo_reference=? AND payment_status != 'confirmed'")->execute([$ref]);
    } elseif (in_array($status, ['failed', 'rejected', 'timeout'])) {
        $db->prepare("UPDATE momo_requests SET status='failed' WHERE reference=?")->execute([$ref]);
    }
} catch (PDOException $e) { /* log silently */ }

http_response_code(200);
echo json_encode(['received' => true]);
?>
