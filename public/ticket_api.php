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
$fullName     = clean($_POST['full_name'] ?? '');
$classSchool  = clean($_POST['index_number'] ?? ($_POST['class_school'] ?? ''));
$phone        = clean($_POST['phone'] ?? '');
$studentType  = $_POST['student_type'] ?? 'general';
$momoReference = clean($_POST['momo_reference'] ?? '');

if (empty($fullName) || empty($classSchool) || empty($phone)) {
    jsonResponse(['success' => false, 'message' => 'All fields are required.']);
}

if (!in_array($studentType, ['internal', 'external', 'general'])) {
    $studentType = 'general';
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

$uploadDir = __DIR__ . '/../assets/uploads/tickets/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($momoRequested && empty($momoReference)) {
    jsonResponse(['success' => false, 'message' => 'Payment reference is required when using MoMo prompt payment.']);
}

if ($momoReference && !preg_match('/^[a-f0-9\-]{36}$/i', $momoReference)) {
    jsonResponse(['success' => false, 'message' => 'Invalid payment reference format.']);
}

if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['payment_proof'];
    $maxSize = 5 * 1024 * 1024;
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']) ?: '';
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        jsonResponse(['success' => false, 'message' => 'Failed to upload payment proof.']);
    }
    if ($file['size'] > $maxSize) {
        jsonResponse(['success' => false, 'message' => 'File too large. Maximum 5MB.']);
    }
    if (!in_array($mimeType, $allowedMimeTypes) || !in_array($ext, $allowedExtensions)) {
        jsonResponse(['success' => false, 'message' => 'Invalid file type. Use JPG, PNG, or PDF.']);
    }

    if ($mimeType === 'application/pdf') {
        $header = file_get_contents($file['tmp_name'], false, null, 0, 4);
        if ($header !== '%PDF') {
            jsonResponse(['success' => false, 'message' => 'Invalid PDF file.']);
        }
    } else {
        $imageInfo = @getimagesize($file['tmp_name']);
        if (!$imageInfo || !in_array($imageInfo['mime'], ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true)) {
            jsonResponse(['success' => false, 'message' => 'Invalid image file.']);
        }
    }

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
    $price = TICKET_PRICE;
    
    // Generate QR code URL (saved as reference)
    $qrData = $ticketId; // Scan just returns the ticket ID
    $qrCodeUrl = generateQRCode($ticketId);
    
    // Assign seat number
    $seatNum = 'A' . str_pad($currentCount + 1, 3, '0', STR_PAD_LEFT);
    
    // Insert ticket (store momo_reference if provided)
    $stmt = $db->prepare("
        INSERT INTO tickets (ticket_id, qr_code, full_name, class_school, phone, student_type,
                             payment_proof, payment_status, ticket_status, seat_number, amount_paid, momo_reference)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', 'unused', ?, ?, ?)
    ");

    $stmt->execute([
        $ticketId, $qrCodeUrl, $fullName, $classSchool, $phone,
        $studentType, $paymentProofPath, $seatNum, $price,
        $momoReference ?: null
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
