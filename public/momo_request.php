<?php
// ============================================
// MTN MoMo Collection API — Request To Pay
// Golden Night 2026
// Docs: momodeveloper.mtn.com
// ============================================

require_once '../includes/config.php';
header('Content-Type: application/json');

// ============================================
// CREDENTIALS
// ============================================
define('MOMO_SUB_KEY',    'd71acf6855ad4c1391ab52e041f6e783');
define('MOMO_API_USER',   '2f36f198-ae44-46f0-b2de-59cde6a3f033');
define('MOMO_API_KEY',    'e96530b0795745f893d3cc74abc7bd88');
define('MOMO_ENV',        'sandbox');
define('MOMO_BASE_URL',   'https://sandbox.momodeveloper.mtn.com');
define('MOMO_CURRENCY',   'EUR');
define('MOMO_CALLBACK',   'https://goldennight2026.kesug.com/public/momo_callback.php');

// ============================================
// READ INPUT
// ============================================
$input    = json_decode(file_get_contents('php://input'), true);
$phone    = trim($input['phone']    ?? '');
$amount   = intval($input['amount'] ?? 0);
$name     = clean($input['name']    ?? '');
$ticketId = clean($input['ticket_id'] ?? ('GN-' . time()));

if (!$phone || !$amount || !$name) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

// ============================================
// FORMAT PHONE: must be 2507XXXXXXXX
// ============================================
$phone = preg_replace('/[^0-9]/', '', $phone);
if (strlen($phone) === 9)        $phone = '250' . $phone;       // 7XXXXXXXX
elseif (substr($phone,0,1)==='0') $phone = '250' . substr($phone,1); // 07X → 2507X
// Already 2507... leave as is

if (!preg_match('/^2507[0-9]{8}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Invalid MTN number. Use format: 07XXXXXXXX']);
    exit;
}

// Credentials are configured — proceed with API call

// ============================================
// STEP 1 — Get Access Token
// POST /collection/token/
// Basic Auth: base64(API_USER:API_KEY)
// ============================================
$basicAuth = base64_encode(MOMO_API_USER . ':' . MOMO_API_KEY);

$ch = curl_init(MOMO_BASE_URL . '/collection/token/');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => '',
    CURLOPT_HTTPHEADER     => [
        'Authorization: Basic ' . $basicAuth,
        'Ocp-Apim-Subscription-Key: ' . MOMO_SUB_KEY,
    ],
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_SSL_VERIFYPEER => false,
]);

$tokenRes  = curl_exec($ch);
$tokenCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($tokenCode !== 200) {
    echo json_encode(['success' => false, 'message' => 'Token request failed. HTTP ' . $tokenCode, 'debug' => $tokenRes]);
    exit;
}

$token = json_decode($tokenRes, true)['access_token'] ?? null;
if (!$token) {
    echo json_encode(['success' => false, 'message' => 'No access token received.']);
    exit;
}

// ============================================
// STEP 2 — Generate unique UUID reference
// ============================================
$refId = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    mt_rand(0,0xffff), mt_rand(0,0xffff),
    mt_rand(0,0xffff),
    mt_rand(0,0x0fff)|0x4000,
    mt_rand(0,0x3fff)|0x8000,
    mt_rand(0,0xffff), mt_rand(0,0xffff), mt_rand(0,0xffff)
);

// ============================================
// STEP 3 — Request To Pay
// POST /collection/v1_0/requesttopay
// ============================================
$payload = json_encode([
    'amount'       => (string)$amount,
    'currency'     => MOMO_CURRENCY,
    'externalId'   => $ticketId,
    'payer'        => [
        'partyIdType' => 'MSISDN',
        'partyId'     => $phone,
    ],
    'payerMessage' => 'Golden Night 2026 Prom Ticket',
    'payeeNote'    => 'Prom 2026 - ' . $name,
]);

$ch2 = curl_init(MOMO_BASE_URL . '/collection/v1_0/requesttopay');
curl_setopt_array($ch2, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer '         . $token,
        'X-Reference-Id: '               . $refId,
        'X-Target-Environment: '         . MOMO_ENV,
        'Ocp-Apim-Subscription-Key: '    . MOMO_SUB_KEY,
        'Content-Type: application/json',
        'X-Callback-Url: '               . MOMO_CALLBACK,
    ],
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_SSL_VERIFYPEER => false,
]);

$payRes  = curl_exec($ch2);
$payCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

// ============================================
// 202 Accepted = prompt sent ✅
// ============================================
if ($payCode === 202) {
    // Log to DB
    try {
        $db = getDB();
        $db->prepare("
            INSERT INTO momo_requests (reference, phone, amount, name, reason, status, created_at)
            VALUES (?, ?, ?, ?, 'Golden Night 2026 Ticket', 'pending', NOW())
        ")->execute([$refId, $phone, $amount, $name]);
    } catch (PDOException $e) {}

    echo json_encode([
        'success'      => true,
        'message'      => 'Payment prompt sent! Check your phone and approve.',
        'reference_id' => $refId,
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'MTN rejected request. HTTP ' . $payCode,
        'debug'   => $payRes,
    ]);
}
?>
