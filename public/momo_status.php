<?php
// ============================================
// MTN MoMo — Poll Payment Status
// GET /collection/v1_0/requesttopay/{referenceId}
// Called by the frontend JS every few seconds
// after a RequestToPay is sent, so we do not
// need the callback URL / providerCallbackHost.
// ============================================
require_once '../includes/config.php';
header('Content-Type: application/json');

// Reuse credentials defined in momo_request.php approach — read env directly
$subKey  = trim((string) (getenv('MOMO_SUB_KEY') ?: ''));
$apiUser = trim((string) (getenv('MOMO_API_USER') ?: ''));
$apiKey  = trim((string) (getenv('MOMO_API_KEY') ?: ''));
$env     = getenv('MOMO_ENV')       ?: 'sandbox';
$baseUrl = getenv('MOMO_BASE_URL')  ?: 'https://sandbox.momodeveloper.mtn.com';

$refId = trim($_GET['ref'] ?? '');
if (!$subKey || !$apiUser || !$apiKey) {
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'MoMo API credentials are not configured.']);
    exit;
}

if (!$refId || !preg_match('/^[a-f0-9\-]{36}$/i', $refId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid reference ID.']);
    exit;
}

// ---- Get token ----
$ch = curl_init($baseUrl . '/collection/token/');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => '',
    CURLOPT_HTTPHEADER     => [
        'Authorization: Basic ' . base64_encode($apiUser . ':' . $apiKey),
        'Ocp-Apim-Subscription-Key: ' . $subKey,
    ],
    CURLOPT_TIMEOUT        => 12,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
]);
$tokenRes  = curl_exec($ch);
$tokenCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($tokenCode !== 200) {
    echo json_encode(['success' => false, 'message' => 'Token error.', 'http' => $tokenCode]);
    exit;
}

$token = json_decode($tokenRes, true)['access_token'] ?? null;
if (!$token) {
    echo json_encode(['success' => false, 'message' => 'No token.']);
    exit;
}

// ---- Poll status ----
$ch2 = curl_init($baseUrl . '/collection/v1_0/requesttopay/' . urlencode($refId));
curl_setopt_array($ch2, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPGET        => true,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer '      . $token,
        'X-Target-Environment: '      . $env,
        'Ocp-Apim-Subscription-Key: ' . $subKey,
    ],
    CURLOPT_TIMEOUT        => 12,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
]);
$statusRes  = curl_exec($ch2);
$statusCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

$data = json_decode($statusRes, true);
$mtnStatus = strtolower($data['status'] ?? 'pending');

// Map MTN status → our status
// MTN values: PENDING, SUCCESSFUL, FAILED
$map = ['successful' => 'successful', 'failed' => 'failed', 'pending' => 'pending'];
$status = $map[$mtnStatus] ?? 'pending';

// If successful, update momo_requests and tickets tables
if ($status === 'successful') {
    try {
        $db = getDB();
        $db->prepare("UPDATE momo_requests SET status='completed' WHERE reference=? AND status!='completed'")
           ->execute([$refId]);
        $db->prepare("UPDATE tickets SET payment_status='pending' WHERE momo_reference=? AND payment_status != 'confirmed'")
           ->execute([$refId]);
    } catch (Exception $e) { /* silent */ }
}

if ($status === 'failed') {
    try {
        $db = getDB();
        $db->prepare("UPDATE momo_requests SET status='failed' WHERE reference=? AND status='pending'")
           ->execute([$refId]);
    } catch (Exception $e) { /* silent */ }
}

echo json_encode([
    'success' => ($statusCode === 200),
    'status'  => $status,
    'http'    => $statusCode,
    'reason'  => $data['reason'] ?? null,
]);
?>
