<?php
// ============================================
// TICKET PURCHASE API
// POST /public/ticket_api.php
// ============================================

require_once '../includes/config.php';
require_once '../includes/qr_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

// ============================================
// Validate inputs
// ============================================
$fullName    = clean($_POST['full_name'] ?? '');
$classSchool = clean($_POST['class_school'] ?? '');
$phone       = clean($_POST['phone'] ?? '');
$studentType = $_POST['student_type'] ?? 'internal';

if (empty($fullName) || empty($classSchool) || empty($phone)) {
    jsonResponse(['success' => false, 'message' => 'All fields are required.']);
}

if (!in_array($studentType, ['internal', 'external'])) {
    $studentType = 'internal';
}

// Validate phone
if (!preg_match('/^[0-9+\-\s]{8,20}$/', $phone)) {
    jsonResponse(['success' => false, 'message' => 'Invalid phone number format.']);
}

// ============================================
// Handle file upload (optional if MoMo used)
// ============================================
$paymentProofPath = null;
$momoRequested = ($_POST['momo_requested'] ?? '0') === '1';

if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['payment_proof'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    $maxSize = 5 * 1024 * 1024;

    if (!in_array($file['type'], $allowedTypes)) {
        jsonResponse(['success' => false, 'message' => 'Invalid file type. Use JPG, PNG, or PDF.']);
    }
    if ($file['size'] > $maxSize) {
        jsonResponse(['success' => false, 'message' => 'File too large. Maximum 5MB.']);
    }

    $uploadDir = __DIR__ . '/../assets/uploads/tickets/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'payment_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $uploadPath = $uploadDir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        jsonResponse(['success' => false, 'message' => 'Failed to upload payment proof.']);
    }
    $paymentProofPath = 'assets/uploads/tickets/' . $fileName;

} elseif (!$momoRequested) {
    jsonResponse(['success' => false, 'message' => 'Please upload payment proof or send a MoMo request first.']);
}

// ============================================
// Generate ticket ID and save to DB
// ============================================
try {
    $db = getDB();
    
    // Check tickets availability
    $settingStmt = $db->query("SELECT setting_value FROM prom_settings WHERE setting_key = 'tickets_available'");
    $maxTickets = (int) ($settingStmt->fetchColumn() ?? 300);
    
    $countStmt = $db->query("SELECT COUNT(*) FROM tickets WHERE ticket_status != 'cancelled'");
    $currentCount = (int) $countStmt->fetchColumn();
    
    if ($currentCount >= $maxTickets) {
        jsonResponse(['success' => false, 'message' => 'Sorry, all tickets are sold out.']);
    }
    
    // Generate unique ticket ID
    $ticketId = generateTicketID();
    
    // Determine price
    $price = ($studentType === 'internal') ? TICKET_PRICE_INTERNAL : TICKET_PRICE_EXTERNAL;
    
    // Generate QR code URL (saved as reference)
    $qrData = $ticketId; // Scan just returns the ticket ID
    $qrCodeUrl = generateQRCode($ticketId);
    
    // Assign seat number
    $seatNum = 'A' . str_pad($currentCount + 1, 3, '0', STR_PAD_LEFT);
    
    // Insert ticket
    $stmt = $db->prepare("
        INSERT INTO tickets (ticket_id, qr_code, full_name, class_school, phone, student_type, 
                             payment_proof, payment_status, ticket_status, seat_number, amount_paid)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', 'unused', ?, ?)
    ");
    
    $stmt->execute([
        $ticketId, $qrCodeUrl, $fullName, $classSchool, $phone,
        $studentType, $paymentProofPath, $seatNum, $price
    ]);
    
    // Return success with ticket data
    jsonResponse([
        'success'  => true,
        'message'  => 'Ticket registered successfully!',
        'ticket'   => [
            'ticket_id'    => $ticketId,
            'full_name'    => $fullName,
            'class_school' => $classSchool,
            'phone'        => $phone,
            'student_type' => $studentType,
            'seat_number'  => $seatNum,
            'amount_paid'  => $price,
            'qr_url'       => $qrCodeUrl,
            'status'       => 'pending'
        ]
    ]);

} catch (PDOException $e) {
    // Delete uploaded file on error
    if ($paymentProofPath && file_exists($uploadDir . basename($paymentProofPath))) {
        unlink($uploadDir . basename($paymentProofPath));
    }
    jsonResponse(['success' => false, 'message' => 'Database error. Please try again.'], 500);
}
?>
