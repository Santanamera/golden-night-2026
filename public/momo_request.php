<?php
// ============================================
// MTN MOMO PUSH REQUEST
// POST { phone, amount, name, reason }
// Replace MOMO_CODE with real merchant code
// ============================================

require_once '../includes/config.php';
header('Content-Type: application/json');

$input  = json_decode(file_get_contents('php://input'), true);
$phone  = preg_replace('/[^0-9]/', '', $input['phone']  ?? '');
$amount = intval($input['amount'] ?? 0);
$name   = clean($input['name']   ?? '');
$reason = clean($input['reason'] ?? 'Golden Night 2026 Ticket');

if (!$phone || !$amount || !$name) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

// Validate amount
if ($amount !== 25000 && $amount !== 30000) {
    echo json_encode(['success' => false, 'message' => 'Invalid amount.']);
    exit;
}

// ============================================
// MTN MoMo Merchant Code
// Replace '11111' with the real code when received
// ============================================
define('MOMO_MERCHANT_CODE', '11111');
define('MOMO_MERCHANT_NAME', 'Kenny');

// ============================================
// OPTION 1: MTN MoMo Rwanda USSD Push
// When you have the real MTN Business API credentials,
// add them here (API Key, Subscription Key, etc.)
//
// For now this returns the USSD string for the user
// to dial manually, and logs the request to DB.
// ============================================

// Log the payment request to database
try {
    $db   = getDB();
    $ref  = 'MOMO-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

    // Save to a momo_requests table if it exists, otherwise just proceed
    try {
        $stmt = $db->prepare("
            INSERT INTO momo_requests (reference, phone, amount, name, reason, status, created_at)
            VALUES (?, ?, ?, ?, ?, 'pending', NOW())
        ");
        $stmt->execute([$ref, $phone, $amount, $name, $reason]);
    } catch (PDOException $e) {
        // Table might not exist yet — that's okay, still return success
    }

    // Build USSD dial string for Rwanda MTN
    // Format: *182*8*1*{MerchantCode}*{Amount}#
    $ussd = "*182*8*1*" . MOMO_MERCHANT_CODE . "*" . $amount . "#";

    echo json_encode([
        'success'  => true,
        'message'  => 'Payment request prepared.',
        'reference'=> $ref,
        'ussd'     => $ussd,
        'merchant' => MOMO_MERCHANT_NAME,
        'code'     => MOMO_MERCHANT_CODE,
        'amount'   => $amount,
        'phone'    => $phone,
        // When real MTN API is integrated, this will trigger a push notification
        'note'     => 'Dial ' . $ussd . ' or use MoMo app to complete payment.'
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
