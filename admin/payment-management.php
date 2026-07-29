<?php
require_once '../includes/config.php';
requireAdmin();

$db = getDB();
$message = null;
$messageType = null;

// Handle payment approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $ticketId = clean($_POST['ticket_id'] ?? '');
    
    if ($_POST['action'] === 'approve') {
        if (!empty($ticketId)) {
            $stmt = $db->prepare("UPDATE tickets SET payment_status = 'confirmed' WHERE ticket_id = ?");
            $stmt->execute([$ticketId]);
            $message = 'Payment approved successfully!';
            $messageType = 'success';
        }
    } elseif ($_POST['action'] === 'reject') {
        if (!empty($ticketId)) {
            $stmt = $db->prepare("UPDATE tickets SET payment_status = 'rejected' WHERE ticket_id = ?");
            $stmt->execute([$ticketId]);
            $message = 'Payment rejected.';
            $messageType = 'error';
        }
    }
}

// Get pending payments
$stmt = $db->query("
    SELECT * FROM tickets 
    WHERE payment_status IN ('pending', 'pending_payment') 
    ORDER BY created_at DESC
");
$pendingTickets = $stmt->fetchAll();

// Get confirmed payments
$stmt = $db->query("
    SELECT * FROM tickets 
    WHERE payment_status = 'confirmed' 
    ORDER BY created_at DESC
");
$confirmedTickets = $stmt->fetchAll();

// Get payment statistics
$totalStmt = $db->query("SELECT COUNT(*) as total FROM tickets");
$totalTickets = $totalStmt->fetch()['total'] ?? 0;

$paidStmt = $db->query("SELECT COUNT(*) as total FROM tickets WHERE payment_status = 'confirmed'");
$paidTickets = $paidStmt->fetch()['total'] ?? 0;

$pendingStmt = $db->query("SELECT COUNT(*) as total FROM tickets WHERE payment_status IN ('pending', 'pending_payment')");
$pendingCount = $pendingStmt->fetch()['total'] ?? 0;

$revenueStmt = $db->query("SELECT SUM(amount_paid) as total FROM tickets WHERE payment_status = 'confirmed'");
$totalRevenue = $revenueStmt->fetch()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Management - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #D4AF37;
        }
        
        .stat-card h3 {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .stat-card .value {
            font-size: 2em;
            font-weight: bold;
            color: #1a1a1a;
        }
        
        .stat-card .subtext {
            color: #999;
            font-size: 0.85em;
            margin-top: 5px;
        }
        
        .section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .section h2 {
            color: #1a1a1a;
            margin-bottom: 20px;
            border-bottom: 2px solid #D4AF37;
            padding-bottom: 10px;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #f5f5f5;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #1a1a1a;
            border-bottom: 2px solid #D4AF37;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-small {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85em;
            transition: all 0.3s;
        }
        
        .btn-approve {
            background: #28a745;
            color: white;
        }
        
        .btn-approve:hover {
            background: #218838;
        }
        
        .btn-reject {
            background: #dc3545;
            color: white;
        }
        
        .btn-reject:hover {
            background: #c82333;
        }
        
        .btn-view {
            background: #D4AF37;
            color: white;
        }
        
        .btn-view:hover {
            background: #b8931a;
        }
        
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }
        
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #D4AF37;
            padding-bottom: 10px;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5em;
            cursor: pointer;
            color: #999;
        }
        
        .payment-proof-img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            margin: 15px 0;
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
            
            table {
                font-size: 0.9em;
            }
            
            th, td {
                padding: 8px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <nav class="admin-navbar">
            <div class="navbar-brand">Golden Night - Admin</div>
            <ul class="navbar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="payment-management.php" class="active">Payments</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <div class="admin-content">
            <h1>💳 Payment Management</h1>

            <?php if ($message): ?>
                <div class="alert <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="dashboard-grid">
                <div class="stat-card">
                    <h3>Total Registrations</h3>
                    <div class="value"><?php echo $totalTickets; ?></div>
                    <div class="subtext">All registered attendees</div>
                </div>
                <div class="stat-card">
                    <h3>Paid Tickets</h3>
                    <div class="value"><?php echo $paidTickets; ?></div>
                    <div class="subtext"><?php echo round(($paidTickets / max($totalTickets, 1)) * 100); ?>% payment rate</div>
                </div>
                <div class="stat-card">
                    <h3>Pending Payments</h3>
                    <div class="value"><?php echo $pendingCount; ?></div>
                    <div class="subtext">Awaiting payment confirmation</div>
                </div>
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <div class="value">Rwf <?php echo number_format($totalRevenue, 0); ?></div>
                    <div class="subtext">From confirmed payments</div>
                </div>
            </div>

            <!-- Pending Payments Section -->
            <div class="section">
                <h2>⏳ Pending Payments (<?php echo count($pendingTickets); ?>)</h2>

                <?php if (count($pendingTickets) > 0): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Full Name</th>
                                    <th>Phone</th>
                                    <th>School</th>
                                    <th>Date Registered</th>
                                    <th>Proof</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingTickets as $ticket): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($ticket['ticket_id']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($ticket['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['class_school']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></td>
                                        <td>
                                            <?php if ($ticket['payment_proof']): ?>
                                                <button class="btn-small btn-view" onclick="showPaymentProof('<?php echo htmlspecialchars($ticket['payment_proof']); ?>')">View</button>
                                            <?php else: ?>
                                                <span style="color: #999;">No file</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="approve">
                                                    <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($ticket['ticket_id']); ?>">
                                                    <button type="submit" class="btn-small btn-approve" onclick="return confirm('Approve payment for <?php echo htmlspecialchars($ticket['full_name']); ?>?')">✓ Approve</button>
                                                </form>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="reject">
                                                    <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($ticket['ticket_id']); ?>">
                                                    <button type="submit" class="btn-small btn-reject" onclick="return confirm('Reject payment for <?php echo htmlspecialchars($ticket['full_name']); ?>?')">✗ Reject</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        ✓ All payments have been processed! No pending payments.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Confirmed Payments Section -->
            <div class="section">
                <h2>✓ Confirmed Payments (<?php echo count($confirmedTickets); ?>)</h2>

                <?php if (count($confirmedTickets) > 0): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Full Name</th>
                                    <th>Amount Paid</th>
                                    <th>Seat</th>
                                    <th>Confirmed Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($confirmedTickets as $ticket): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($ticket['ticket_id']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($ticket['full_name']); ?></td>
                                        <td>GHS <?php echo number_format($ticket['amount_paid'], 0); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['seat_number']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></td>
                                        <td>
                                            <span class="status-badge status-confirmed">✓ Confirmed</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        No confirmed payments yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Payment Proof Modal -->
    <div id="proofModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Payment Proof</h3>
                <button class="modal-close" onclick="closePaymentProof()">×</button>
            </div>
            <div id="proofContent"></div>
        </div>
    </div>

    <script>
        function showPaymentProof(proofPath) {
            const ext = proofPath.toLowerCase().split('.').pop();
            const fullUrl = '../' + proofPath;
            let content = '';
            
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                content = `<img src="${fullUrl}" alt="Payment Proof" class="payment-proof-img">`;
            } else if (ext === 'pdf') {
                content = `<iframe src="${fullUrl}" width="100%" height="500" style="border: 1px solid #ddd; border-radius: 8px;"></iframe>`;
            } else {
                content = `<p><a href="${fullUrl}" target="_blank" class="btn-small btn-view">Download File</a></p>`;
            }
            
            document.getElementById('proofContent').innerHTML = content;
            document.getElementById('proofModal').classList.add('active');
        }
        
        function closePaymentProof() {
            document.getElementById('proofModal').classList.remove('active');
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('proofModal');
            if (event.target == modal) {
                modal.classList.remove('active');
            }
        }
    </script>
</body>
</html>
