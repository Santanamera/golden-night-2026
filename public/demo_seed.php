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

function postDemoRequest(string $url, array $fields = [], array $files = [], array $headers = []): array {
    $multipart = [];
    foreach ($fields as $name => $value) {
        $multipart[] = [
            'name' => $name,
            'contents' => (string) $value,
        ];
    }

    foreach ($files as $name => $filePath) {
        $multipart[] = [
            'name' => $name,
            'contents' => fopen($filePath, 'r'),
            'filename' => basename($filePath),
            'headers' => ['Content-Type' => 'image/png'],
        ];
    }

    $client = curl_init($url);
    curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($client, CURLOPT_POST, true);
    curl_setopt($client, CURLOPT_POSTFIELDS, $multipart);
    curl_setopt($client, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($client);
    $status = curl_getinfo($client, CURLINFO_HTTP_CODE);
    $error = curl_error($client);
    curl_close($client);

    $decoded = json_decode($response, true);
    return [
        'ok' => $status >= 200 && $status < 300,
        'status' => $status,
        'body' => $response,
        'json' => is_array($decoded) ? $decoded : null,
        'error' => $error,
    ];
}

$baseUrl = rtrim(APP_URL, '/');
$ticketPhotoPath = writeDemoImage(__DIR__ . '/../assets/uploads/demo/demo-ticket.png');
$kingPhotoPath = writeDemoImage(__DIR__ . '/../assets/uploads/demo/demo-king.png');
$queenPhotoPath = writeDemoImage(__DIR__ . '/../assets/uploads/demo/demo-queen.png');

$summary = [];

$ticketResponse = postDemoRequest($baseUrl . '/public/ticket_api.php', [
    'full_name' => 'Demo Ticket Holder',
    'index_number' => 'Demo School',
    'phone' => '0781234567',
    'student_type' => 'general',
    'momo_requested' => '0',
], [
    'payment_proof' => $ticketPhotoPath,
]);

$kingResponse = postDemoRequest($baseUrl . '/public/audition_api.php', [
    'full_name' => 'Demo King Candidate',
    'class_school' => 'Demo School',
    'bio' => 'This is a demo audition entry for Prom King created from the live Railway page.',
    'category' => 'king',
], [
    'photo' => $kingPhotoPath,
]);

$queenResponse = postDemoRequest($baseUrl . '/public/audition_api.php', [
    'full_name' => 'Demo Queen Candidate',
    'class_school' => 'Demo School',
    'bio' => 'This is a demo audition entry for Prom Queen created from the live Railway page.',
    'category' => 'queen',
], [
    'photo' => $queenPhotoPath,
]);

if (!empty($ticketResponse['json']['success']) && !empty($kingResponse['json']['success']) && !empty($queenResponse['json']['success'])) {
    $summary = [
        'ticket_id' => $ticketResponse['json']['ticket']['ticket_id'] ?? null,
        'king_name' => $kingResponse['json']['candidate']['name'] ?? null,
        'queen_name' => $queenResponse['json']['candidate']['name'] ?? null,
    ];
} else {
    $summary = [
        'error' => 'One or more demo requests did not complete successfully.',
        'details' => [
            'ticket' => $ticketResponse,
            'king' => $kingResponse,
            'queen' => $queenResponse,
        ],
    ];
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
        <p>This page submits a fake ticket registration and two audition applications through the live public endpoints so you can confirm the site is working from the web.</p>

        <?php if (!empty($summary['error'])): ?>
            <p style="color:#ff8d8d;">Demo failed: <code><?= htmlspecialchars($summary['error']) ?></code></p>
        <?php else: ?>
            <ul>
                <li><strong>Ticket registration:</strong> <code><?= htmlspecialchars((string) ($summary['ticket_id'] ?? '')) ?></code></li>
                <li><strong>Prom King audition:</strong> <code><?= htmlspecialchars((string) ($summary['king_name'] ?? '')) ?></code></li>
                <li><strong>Prom Queen audition:</strong> <code><?= htmlspecialchars((string) ($summary['queen_name'] ?? '')) ?></code></li>
            </ul>
            <p>You can now check the live site and confirm that these demo records were accepted.</p>
        <?php endif; ?>
    </div>
</body>
</html>
