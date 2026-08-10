<?php
require_once '../includes/config.php';

header('Content-Type: text/html; charset=utf-8');

function writeDemoImage(string $path): string {
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    if (file_exists($path)) {
        return $path;
    }

    $pngData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQABAA4A7wBAQwAAAABJRU5ErkJggg==');
    file_put_contents($path, $pngData);
    return $path;
}

try {
    $db = getDB();

    $uploadDir = __DIR__ . '/../assets/uploads/demo';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ticketImage = writeDemoImage($uploadDir . '/demo-ticket.png');
    $kingImage = writeDemoImage($uploadDir . '/demo-king.png');
    $queenImage = writeDemoImage($uploadDir . '/demo-queen.png');

    $existingTicket = $db->prepare("SELECT ticket_id FROM tickets WHERE full_name = ? AND phone = ? LIMIT 1");
    $existingTicket->execute(['Demo Ticket Holder', '0781234567']);
    $ticketRow = $existingTicket->fetch();

    $ticketId = $ticketRow['ticket_id'] ?? generateTicketID();
    if (!$ticketRow) {
        $db->prepare(
            "INSERT INTO tickets (ticket_id, qr_code, full_name, class_school, phone, student_type, payment_proof, payment_status, ticket_status, seat_number, amount_paid, momo_reference)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed', 'unused', ?, 20000, NULL)"
        )->execute([$ticketId, 'demo-' . $ticketId, 'Demo Ticket Holder', 'Demo School', '0781234567', 'general', 'assets/uploads/demo/demo-ticket.png', 'A999']);
    }

    $kingCandidate = $db->prepare("SELECT id FROM candidates WHERE full_name = ? AND category = 'king' LIMIT 1");
    $kingCandidate->execute(['Demo King Candidate']);
    $kingRow = $kingCandidate->fetch();

    $kingId = null;
    if (!$kingRow) {
        $db->prepare(
            "INSERT INTO candidates (full_name, photo, category, bio, class_school, status, vote_count)
            VALUES (?, ?, 'king', ?, ?, 'approved', 0)"
        )->execute(['Demo King Candidate', 'assets/uploads/demo/demo-king.png', 'This is a demo audition entry for Prom King created from the live Railway page.', 'Demo School']);
        $kingId = (int) $db->lastInsertId();
    } else {
        $kingId = (int) $kingRow['id'];
    }

    $queenCandidate = $db->prepare("SELECT id FROM candidates WHERE full_name = ? AND category = 'queen' LIMIT 1");
    $queenCandidate->execute(['Demo Queen Candidate']);
    $queenRow = $queenCandidate->fetch();

    $queenId = null;
    if (!$queenRow) {
        $db->prepare(
            "INSERT INTO candidates (full_name, photo, category, bio, class_school, status, vote_count)
            VALUES (?, ?, 'queen', ?, ?, 'approved', 0)"
        )->execute(['Demo Queen Candidate', 'assets/uploads/demo/demo-queen.png', 'This is a demo audition entry for Prom Queen created from the live Railway page.', 'Demo School']);
        $queenId = (int) $db->lastInsertId();
    } else {
        $queenId = (int) $queenRow['id'];
    }

    $voteCheck = $db->prepare("SELECT id FROM votes WHERE ticket_id = ? LIMIT 1");
    $voteCheck->execute([$ticketId]);
    if (!$voteCheck->fetch() && $kingId && $queenId) {
        $db->beginTransaction();
        $db->prepare("INSERT INTO votes (ticket_id, king_candidate_id, queen_candidate_id) VALUES (?, ?, ?)")->execute([$ticketId, $kingId, $queenId]);
        $db->prepare("UPDATE candidates SET vote_count = vote_count + 1 WHERE id = ?")->execute([$kingId]);
        $db->prepare("UPDATE candidates SET vote_count = vote_count + 1 WHERE id = ?")->execute([$queenId]);
        $db->commit();
    }

    $summary = [
        'ticket_id' => $ticketId,
        'king_name' => 'Demo King Candidate',
        'queen_name' => 'Demo Queen Candidate',
    ];
} catch (Throwable $e) {
    $summary = ['error' => $e->getMessage()];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Live Demo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; background: #0f0f10; color: #f6e7c1; }
        .box { max-width: 760px; margin: 0 auto; padding: 24px; border: 1px solid #d4af37; border-radius: 12px; background: #161616; }
        code { background: #222; padding: 2px 6px; border-radius: 4px; }
        ul { padding-left: 20px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Live Railway demo created</h1>
        <p>This page creates a demo ticket, a Prom King application, a Prom Queen application, and a vote entry so you can confirm the live site is working from the web.</p>

        <?php if (!empty($summary['error'])): ?>
            <p style="color:#ff8d8d;">Demo failed: <code><?= htmlspecialchars($summary['error']) ?></code></p>
        <?php else: ?>
            <ul>
                <li><strong>Ticket registration:</strong> <code><?= htmlspecialchars((string) ($summary['ticket_id'] ?? '')) ?></code></li>
                <li><strong>Prom King audition:</strong> <code>Demo King Candidate</code></li>
                <li><strong>Prom Queen audition:</strong> <code>Demo Queen Candidate</code></li>
            </ul>
            <p>You can now check the live site and confirm that these demo records were accepted.</p>
        <?php endif; ?>
    </div>
</body>
</html>
