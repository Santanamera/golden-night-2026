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

    $image = imagecreatetruecolor(400, 400);
    $bg = imagecolorallocate($image, 10, 10, 10);
    $gold = imagecolorallocate($image, 212, 175, 55);
    $white = imagecolorallocate($image, 248, 240, 220);
    imagefill($image, 0, 0, $bg);
    imagefilledellipse($image, 200, 200, 220, 220, $gold);
    imagerectangle($image, 70, 70, 330, 330, $white);
    imageline($image, 110, 200, 290, 200, $white);
    imageline($image, 200, 110, 200, 290, $white);
    imagestring($image, 5, 110, 340, 'DEMO DATA', $white);
    imagepng($image, $path);
    imagedestroy($image);

    return $path;
}

function relativePath(string $absolutePath): string {
    return str_replace($_SERVER['DOCUMENT_ROOT'] . '/', '', $absolutePath);
}

try {
    $db = getDB();

    $existingTicket = $db->prepare("SELECT ticket_id, full_name, payment_status FROM tickets WHERE ticket_id LIKE 'DEMO-%' ORDER BY id DESC LIMIT 1");
    $existingTicket->execute();
    $ticketRow = $existingTicket->fetch();

    $existingKing = $db->prepare("SELECT id, full_name, status FROM candidates WHERE full_name = ? AND category = 'king' LIMIT 1");
    $existingKing->execute(['Demo King Candidate']);
    $kingRow = $existingKing->fetch();

    $existingQueen = $db->prepare("SELECT id, full_name, status FROM candidates WHERE full_name = ? AND category = 'queen' LIMIT 1");
    $existingQueen->execute(['Demo Queen Candidate']);
    $queenRow = $existingQueen->fetch();

    $ticketId = $ticketRow['ticket_id'] ?? 'DEMO-' . date('YmdHis');
    $kingId = $kingRow['id'] ?? null;
    $queenId = $queenRow['id'] ?? null;

    if (!$ticketRow) {
        $ticketProofPath = writeDemoImage(__DIR__ . '/../assets/uploads/tickets/demo_payment.png');
        $ticketProofRelative = relativePath($ticketProofPath);

        $stmt = $db->prepare(
            "INSERT INTO tickets (ticket_id, qr_code, full_name, class_school, phone, student_type, payment_proof, payment_status, ticket_status, seat_number, amount_paid, momo_reference)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed', 'unused', ?, 30000, NULL)"
        );
        $stmt->execute([
            $ticketId,
            'demo-qr-' . $ticketId,
            'Demo Ticket Holder',
            'Demo School',
            '0781234567',
            'general',
            $ticketProofRelative,
            'A999'
        ]);
    }

    if (!$kingRow) {
        $kingPhotoPath = writeDemoImage(__DIR__ . '/../assets/uploads/candidates/demo_king.png');
        $kingPhotoRelative = relativePath($kingPhotoPath);

        $stmt = $db->prepare(
            "INSERT INTO candidates (full_name, photo, category, bio, class_school, status, vote_count)
            VALUES (?, ?, 'king', ?, ?, 'approved', 0)"
        );
        $stmt->execute([
            'Demo King Candidate',
            $kingPhotoRelative,
            'A cheerful demo candidate for the king category with strong school spirit.',
            'Demo School'
        ]);
        $kingId = (int) $db->lastInsertId();
    }

    if (!$queenRow) {
        $queenPhotoPath = writeDemoImage(__DIR__ . '/../assets/uploads/candidates/demo_queen.png');
        $queenPhotoRelative = relativePath($queenPhotoPath);

        $stmt = $db->prepare(
            "INSERT INTO candidates (full_name, photo, category, bio, class_school, status, vote_count)
            VALUES (?, ?, 'queen', ?, ?, 'approved', 0)"
        );
        $stmt->execute([
            'Demo Queen Candidate',
            $queenPhotoRelative,
            'A polished demo candidate for the queen category who represents elegance and leadership.',
            'Demo School'
        ]);
        $queenId = (int) $db->lastInsertId();
    }

    if ($kingId && $queenId) {
        $existingVote = $db->prepare("SELECT id FROM votes WHERE ticket_id = ? LIMIT 1");
        $existingVote->execute([$ticketId]);
        if (!$existingVote->fetch()) {
            $db->beginTransaction();
            $db->prepare("INSERT INTO votes (ticket_id, king_candidate_id, queen_candidate_id) VALUES (?, ?, ?)")
                ->execute([$ticketId, $kingId, $queenId]);
            $db->prepare("UPDATE candidates SET vote_count = vote_count + 1 WHERE id = ?")->execute([$kingId]);
            $db->prepare("UPDATE candidates SET vote_count = vote_count + 1 WHERE id = ?")->execute([$queenId]);
            $db->commit();
        }
    }

    $summary = [
        'ticket_id' => $ticketId,
        'king_id' => $kingId,
        'queen_id' => $queenId,
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
    <title>Demo Seed</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; background: #0f0f10; color: #f6e7c1; }
        .box { max-width: 720px; margin: 0 auto; padding: 24px; border: 1px solid #d4af37; border-radius: 12px; background: #161616; }
        code { background: #222; padding: 2px 6px; border-radius: 4px; }
        ul { padding-left: 20px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Demo data created</h1>
        <p>This page seeds one sample ticket, one king application, one queen application, and one vote entry so you can confirm the live Railway flow.</p>

        <?php if (!empty($summary['error'])): ?>
            <p style="color:#ff8d8d;">Seed failed: <code><?= htmlspecialchars($summary['error']) ?></code></p>
        <?php else: ?>
            <ul>
                <li><strong>Ticket:</strong> <code><?= htmlspecialchars($summary['ticket_id']) ?></code></li>
                <li><strong>King candidate:</strong> <code><?= htmlspecialchars((string) ($summary['king_id'] ?? '')) ?></code></li>
                <li><strong>Queen candidate:</strong> <code><?= htmlspecialchars((string) ($summary['queen_id'] ?? '')) ?></code></li>
            </ul>
            <p>You can now open the public pages and verify the demo records in the live app.</p>
        <?php endif; ?>
    </div>
</body>
</html>
