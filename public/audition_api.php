<?php
// ============================================
// AUDITION/CANDIDATE REGISTRATION API
// POST (multipart form data)
// ============================================

require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

// Validate inputs
$fullName    = clean($_POST['full_name'] ?? '');
$classSchool = clean($_POST['class_school'] ?? '');
$bio         = clean($_POST['bio'] ?? '');
$category    = $_POST['category'] ?? '';

if (empty($fullName) || empty($classSchool) || empty($bio) || empty($category)) {
    jsonResponse(['success' => false, 'message' => 'All fields are required.']);
}

if (!in_array($category, ['king', 'queen'])) {
    jsonResponse(['success' => false, 'message' => 'Invalid category.']);
}

if (strlen($bio) < 30 || strlen($bio) > 500) {
    jsonResponse(['success' => false, 'message' => 'Bio must be between 30 and 500 characters.']);
}

// Handle photo upload
$photoPath = null;
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(['success' => false, 'message' => 'Photo is required.']);
}

$file = $_FILES['photo'];
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

$mimeType = $file['type'] ?? '';
if (function_exists('finfo_open') && is_uploaded_file($file['tmp_name'])) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']) ?: $mimeType;
}

if (!in_array($mimeType, $allowedTypes, true)) {
    jsonResponse(['success' => false, 'message' => 'Photo must be JPG, PNG, GIF, or WebP.']);
}

if ($file['size'] > 5 * 1024 * 1024) {
    jsonResponse(['success' => false, 'message' => 'Photo must be under 5MB.']);
}

$uploadDir = __DIR__ . '/../assets/uploads/candidates/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$photoFilename = 'cand_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$uploadPath = $uploadDir . $photoFilename;

if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    jsonResponse(['success' => false, 'message' => 'Failed to upload photo.']);
}

$photoPath = 'assets/uploads/candidates/' . $photoFilename;

try {
    $db = getDB();
    
    // Check for duplicate (same name + category)
    $stmt = $db->prepare("SELECT id FROM candidates WHERE full_name = ? AND category = ? AND status != 'rejected'");
    $stmt->execute([$fullName, $category]);
    if ($stmt->fetch()) {
        @unlink($uploadPath);
        jsonResponse(['success' => false, 'message' => 'A candidate with this name already exists for this category.']);
    }
    
    // Insert candidate
    $stmt = $db->prepare("
        INSERT INTO candidates (full_name, photo, category, bio, class_school, status)
        VALUES (?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([$fullName, $photoPath, $category, $bio, $classSchool]);
    
    $candidateId = $db->lastInsertId();
    
    jsonResponse([
        'success'     => true,
        'message'     => 'Candidacy submitted! Awaiting admin approval.',
        'candidate'   => [
            'id'       => $candidateId,
            'name'     => $fullName,
            'category' => $category,
            'status'   => 'pending'
        ]
    ]);

} catch (PDOException $e) {
    @unlink($uploadPath);
    jsonResponse(['success' => false, 'message' => 'Database error.'], 500);
}
?>
