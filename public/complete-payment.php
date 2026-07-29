<?php
require_once '../includes/config.php';

$momoCode = trim((string) (getenv('MOMO_PAYEE_CODE') ?: '')); 
$momoName = trim((string) (getenv('MOMO_PAYEE_NAME') ?: '')); 
$isMomoRecipientConfigured = $momoCode !== '' && $momoName !== '';

$db = getDB();
$settings = getSetting(['prom_name', 'tickets_available']);
$promName = $settings['prom_name'] ?? 'Golden Night 2026';

$ticketId = null;
$ticket = null;
$message = null;
$messageType = null;

// Check if ticket ID provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'verify') {
        $ticketId = clean($_POST['ticket_id'] ?? '');
        
        if (empty($ticketId)) {
            $message = 'Please enter your ticket ID.';
            $messageType = 'error';
        } else {
            $stmt = $db->prepare("SELECT * FROM tickets WHERE ticket_id = ?");
            $stmt->execute([$ticketId]);
            $ticket = $stmt->fetch();
            
            if (!$ticket) {
                $message = 'Ticket ID not found. Please check and try again.';
                $messageType = 'error';
                $ticketId = null;
            } elseif ($ticket['payment_status'] === 'confirmed') {
                $message = 'This ticket has already been paid for!';
                $messageType = 'success';
            }
        }
    } elseif ($_POST['action'] === 'complete_payment' && isset($_POST['ticket_id'])) {
        $ticketId = clean($_POST['ticket_id']);
        
        // Get ticket
        $stmt = $db->prepare("SELECT * FROM tickets WHERE ticket_id = ?");
        $stmt->execute([$ticketId]);
        $ticket = $stmt->fetch();
        
        if (!$ticket) {
            $message = 'Ticket not found.';
            $messageType = 'error';
        } elseif ($ticket['payment_status'] === 'confirmed') {
            $message = 'This ticket has already been paid!';
            $messageType = 'success';
        } else {
            // Handle file upload
            $uploadDir = __DIR__ . '/../assets/uploads/tickets/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['payment_proof'];
                $maxSize = 5 * 1024 * 1024;
                $mimeType = $file['type'] ?? '';
                
                if (function_exists('finfo_open')) {
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->file($file['tmp_name']) ?: $mimeType;
                }
                
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
                
                if ($file['size'] > $maxSize) {
                    $message = 'File too large. Maximum 5MB allowed.';
                    $messageType = 'error';
                } elseif (!in_array($mimeType, $allowedMimeTypes) || !in_array($ext, $allowedExtensions)) {
                    $message = 'Invalid file type. Use JPG, PNG, GIF, or PDF only.';
                    $messageType = 'error';
                } else {
                    // Validate PDF
                    if ($mimeType === 'application/pdf') {
                        $header = file_get_contents($file['tmp_name'], false, null, 0, 4);
                        if ($header !== '%PDF') {
                            $message = 'Invalid PDF file.';
                            $messageType = 'error';
                        }
                    }
                    
                    if ($messageType !== 'error') {
                        // Move file
                        $fileName = 'payment_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                        $uploadPath = $uploadDir . $fileName;
                        
                        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                            $paymentProofPath = 'assets/uploads/tickets/' . $fileName;
                            
                            // Save proof for admin review without auto-confirming payment
                            $updateStmt = $db->prepare("
                                UPDATE tickets 
                                SET payment_status = 'pending', 
                                    payment_proof = ?
                                WHERE ticket_id = ?
                            ");
                            $updateStmt->execute([$paymentProofPath, $ticketId]);
                            
                            $message = 'Payment proof uploaded. Your ticket will be reviewed and confirmed by an administrator shortly.';
                            $messageType = 'success';
                            
                            // Refresh ticket data
                            $stmt = $db->prepare("SELECT * FROM tickets WHERE ticket_id = ?");
                            $stmt->execute([$ticketId]);
                            $ticket = $stmt->fetch();
                        } else {
                            $message = 'Failed to upload payment proof. Please try again.';
                            $messageType = 'error';
                        }
                    }
                }
            } else {
                $message = 'Please upload a payment proof file (receipt, screenshot, etc.).';
                $messageType = 'error';
            }
        }
    }
}

// Get ticket info if ticket_id in URL
if (isset($_GET['ticket_id']) && !$ticket) {
    $ticketId = clean($_GET['ticket_id']);
    $stmt = $db->prepare("SELECT * FROM tickets WHERE ticket_id = ?");
    $stmt->execute([$ticketId]);
    $ticket = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Payment - <?php echo htmlspecialchars($promName); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .payment-container {
            max-width: 700px;
            margin: 60px auto;
            padding: 40px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.1);
        }
        
        .payment-header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #D4AF37;
            padding-bottom: 20px;
        }
        
        .payment-header h1 {
            color: #1a1a1a;
            font-size: 2em;
            margin-bottom: 5px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        
        .alert.info {
            background: #d1ecf1;
            color: #0c5460;
            border-color: #bee5eb;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1a1a1a;
            font-weight: 600;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 1em;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #D4AF37;
            box-shadow: 0 0 5px rgba(212, 175, 55, 0.3);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #D4AF37;
            color: white;
        }
        
        .btn-primary:hover {
            background: #b8931a;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .ticket-info {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
            border: 2px solid #D4AF37;
        }
        
        .ticket-info h3 {
            color: #D4AF37;
            margin-bottom: 15px;
            border-bottom: 2px solid #D4AF37;
            padding-bottom: 10px;
        }
        
        .ticket-detail {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .ticket-detail:last-child {
            border-bottom: none;
        }
        
        .ticket-detail-label {
            font-weight: 600;
            color: #1a1a1a;
        }
        
        .ticket-detail-value {
            color: #555;
        }
        
        .payment-amount {
            background: #D4AF37;
            color: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 1.5em;
            font-weight: 600;
            margin: 20px 0;
        }
        
        .file-input-wrapper {
            position: relative;
            border: 2px dashed #D4AF37;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .file-input-wrapper:hover {
            background: rgba(212, 175, 55, 0.1);
        }
        
        .file-input-wrapper input[type="file"] {
            display: none;
        }
        
        .file-input-text {
            color: #D4AF37;
            font-weight: 600;
        }
        
        .file-selected {
            color: #28a745;
            margin-top: 10px;
        }
        
        .help-text {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .payment-container {
                margin: 20px;
                padding: 20px;
            }
            
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">Golden Night 2026</div>
        <ul class="nav-menu">
            <li><a href="../">Home</a></li>
            <li><a href="prom-info.php">Event Info</a></li>
            <li><a href="buy-ticket.php">Get Ticket</a></li>
            <li><a href="complete-payment.php" class="active">Complete Payment</a></li>
        </ul>
    </nav>

    <div class="payment-container">
        <div class="payment-header">
            <h1>💳 Complete Payment</h1>
            <p>Finish registering your ticket for the prom</p>
        </div>

        <?php if ($message): ?>
            <div class="alert <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($ticket && $ticket['payment_status'] !== 'confirmed'): ?>
            <!-- Show ticket details and payment form -->
            <form method="POST">
                <input type="hidden" name="action" value="complete_payment">
                <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($ticket['ticket_id']); ?>">

                <div class="ticket-info">
                    <h3>Your Registration Details</h3>
                    <div class="ticket-detail">
                        <span class="ticket-detail-label">Ticket ID:</span>
                        <span class="ticket-detail-value"><?php echo htmlspecialchars($ticket['ticket_id']); ?></span>
                    </div>
                    <div class="ticket-detail">
                        <span class="ticket-detail-label">Name:</span>
                        <span class="ticket-detail-value"><?php echo htmlspecialchars($ticket['full_name']); ?></span>
                    </div>
                    <div class="ticket-detail">
                        <span class="ticket-detail-label">School/Class:</span>
                        <span class="ticket-detail-value"><?php echo htmlspecialchars($ticket['class_school']); ?></span>
                    </div>
                    <div class="ticket-detail">
                        <span class="ticket-detail-label">Seat Number:</span>
                        <span class="ticket-detail-value"><?php echo htmlspecialchars($ticket['seat_number']); ?></span>
                    </div>
                    <div class="ticket-detail">
                        <span class="ticket-detail-label">Status:</span>
                        <span class="ticket-detail-value" style="color: #D4AF37;">
                            <?php echo ucfirst(str_replace('_', ' ', $ticket['payment_status'])); ?>
                        </span>
                    </div>
                </div>

                <div class="payment-amount">
                    Amount Due: Rwf <?php echo number_format((int)($ticket['amount_paid'] ?? TICKET_PRICE_SINGLE), 0, '.', ','); ?>
                </div>

                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <div class="alert info">
                        <strong>💰 Pay via MTN MoMo:</strong><br>
                        Send Rwf <?php echo number_format((int)($ticket['amount_paid'] ?? TICKET_PRICE_SINGLE), 0, '.', ','); ?> to <strong><?= $isMomoRecipientConfigured ? htmlspecialchars($momoName, ENT_QUOTES) : '+250 726 123 456' ?></strong><br>
                        Code: <strong><?= $isMomoRecipientConfigured ? htmlspecialchars($momoCode, ENT_QUOTES) : 'Enter the code shown on the registration page' ?></strong><br>
                        Reference: <strong><?php echo htmlspecialchars($ticket['ticket_id']); ?></strong><br>
                        <br>
                        Then upload your payment receipt or screenshot below to confirm your spot.
                    </div>
                </div>

                <div class="form-group">
                    <label>Upload Payment Proof</label>
                    <div class="file-input-wrapper" onclick="document.getElementById('payment_proof').click()">
                        <div class="file-input-text">
                            📁 Click to upload or drag and drop<br>
                            <small class="help-text">JPG, PNG, GIF, or PDF (Max 5MB)</small>
                        </div>
                        <input type="file" id="payment_proof" name="payment_proof" accept=".jpg,.jpeg,.png,.gif,.pdf" required>
                        <div class="file-selected" id="file-selected"></div>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">✓ Confirm Payment</button>
                    <a href="../" class="btn btn-secondary" style="text-decoration: none; text-align: center;">← Back to Home</a>
                </div>
            </form>

        <?php elseif ($ticket && $ticket['payment_status'] === 'confirmed'): ?>
            <!-- Show confirmation -->
            <div class="alert success">
                <strong>✓ Payment Confirmed!</strong><br>
                Your ticket has been paid in full. You're all set for the prom!
            </div>

            <div class="ticket-info">
                <h3>Your Confirmed Ticket</h3>
                <div class="ticket-detail">
                    <span class="ticket-detail-label">Ticket ID:</span>
                    <span class="ticket-detail-value"><?php echo htmlspecialchars($ticket['ticket_id']); ?></span>
                </div>
                <div class="ticket-detail">
                    <span class="ticket-detail-label">Name:</span>
                    <span class="ticket-detail-value"><?php echo htmlspecialchars($ticket['full_name']); ?></span>
                </div>
                <div class="ticket-detail">
                    <span class="ticket-detail-label">Seat:</span>
                    <span class="ticket-detail-value"><?php echo htmlspecialchars($ticket['seat_number']); ?></span>
                </div>
                <div class="ticket-detail">
                    <span class="ticket-detail-label">Status:</span>
                    <span class="ticket-detail-value" style="color: #28a745;">✓ Confirmed</span>
                </div>
            </div>

            <div class="button-group">
                <a href="../" class="btn btn-primary" style="text-decoration: none; text-align: center;">← Back to Home</a>
            </div>

        <?php else: ?>
            <!-- Show lookup form -->
            <div class="alert info">
                <strong>ℹ️ How it works:</strong><br>
                1. Enter your ticket ID to check your registration<br>
                2. Upload your MoMo payment receipt<br>
                3. Your payment will be reviewed by an administrator and confirmed if approved
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="verify">

                <div class="form-group">
                    <label for="ticket_id">Your Ticket ID</label>
                    <input type="text" id="ticket_id" name="ticket_id" placeholder="e.g., GN2026A001" required>
                    <p class="help-text">You received this when you registered</p>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Find My Ticket</button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        // File input handling
        const fileInput = document.getElementById('payment_proof');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const fileName = e.target.files[0]?.name;
                const fileSelected = document.getElementById('file-selected');
                if (fileName) {
                    fileSelected.textContent = '✓ ' + fileName + ' selected';
                }
            });
            
            // Drag and drop
            const wrapper = document.querySelector('.file-input-wrapper');
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                wrapper.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                wrapper.addEventListener(eventName, () => {
                    wrapper.style.background = 'rgba(212, 175, 55, 0.2)';
                });
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                wrapper.addEventListener(eventName, () => {
                    wrapper.style.background = '';
                });
            });
            
            wrapper.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                fileInput.files = files;
                const event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);
            });
        }
    </script>
</body>
</html>
