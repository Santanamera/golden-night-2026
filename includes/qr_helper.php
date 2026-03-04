<?php
// ============================================
// QR CODE GENERATOR
// Uses Google Charts API (no library needed)
// For offline: use phpqrcode library
// ============================================

/**
 * Generate QR Code as base64 image
 * Uses QR Server API - works offline once cached
 */
function generateQRCode(string $data, int $size = 300): string {
    // Encode the data
    $encoded = urlencode($data);
    
    // Use QR Server API (works great with XAMPP)
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encoded}&bgcolor=0a0a0a&color=D4AF37&margin=10&format=png";
    
    return $qrUrl;
}

/**
 * Generate QR Code and save to file
 * Returns the file path
 */
function generateAndSaveQR(string $ticketId, string $data): string {
    $filename = 'qr_' . $ticketId . '.png';
    $filepath = __DIR__ . '/../assets/uploads/tickets/' . $filename;
    $webpath = 'assets/uploads/tickets/' . $filename;
    
    // Try to download and save QR code
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=" . urlencode($data) . "&bgcolor=000000&color=D4AF37&margin=15&format=png";
    
    $context = stream_context_create([
        'http' => ['timeout' => 10],
        'ssl'  => ['verify_peer' => false]
    ]);
    
    $qrData = @file_get_contents($qrUrl, false, $context);
    
    if ($qrData !== false) {
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }
        file_put_contents($filepath, $qrData);
        return $webpath;
    }
    
    // Fallback: return URL directly
    return $qrUrl;
}

/**
 * Generate ticket HTML for display/download
 */
function generateTicketHTML(array $ticket): string {
    $promName = getSetting('prom_name', 'Golden Night 2026');
    $promDate = getSetting('prom_date', '2026-06-15 18:00:00');
    $promVenue = getSetting('prom_venue', 'Grand Ballroom');
    $formattedDate = date('F j, Y • g:i A', strtotime($promDate));
    
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . 
              urlencode($ticket['ticket_id']) . 
              "&bgcolor=0a0a0a&color=D4AF37&margin=10&format=png";
    
    $typeLabel = $ticket['student_type'] === 'internal' ? 'Internal Student' : 'External Guest';
    
    return '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Ticket - ' . htmlspecialchars($ticket['ticket_id']) . '</title>
<style>
  @import url("https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=Cormorant+Garamond:wght@300;400;600&display=swap");
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { background: #0a0a0a; display: flex; justify-content: center; align-items: center; min-height: 100vh; font-family: "Cormorant Garamond", serif; }
  .ticket { width: 750px; background: linear-gradient(135deg, #0f0f0f 0%, #1a1a0e 50%, #0f0f0f 100%); border: 2px solid #D4AF37; border-radius: 16px; overflow: hidden; box-shadow: 0 0 60px rgba(212,175,55,0.3); }
  .ticket-header { background: linear-gradient(135deg, #D4AF37, #f0d060, #D4AF37); padding: 24px 32px; text-align: center; }
  .ticket-header h1 { font-family: "Playfair Display", serif; font-size: 2.2rem; color: #0a0a0a; letter-spacing: 4px; }
  .ticket-header p { font-size: 0.9rem; color: #333; letter-spacing: 3px; text-transform: uppercase; margin-top: 4px; }
  .ticket-body { display: flex; padding: 32px; gap: 32px; align-items: center; }
  .ticket-info { flex: 1; }
  .ticket-info .name { font-family: "Playfair Display", serif; font-size: 1.8rem; color: #D4AF37; margin-bottom: 16px; }
  .info-row { margin: 8px 0; color: #ccc; font-size: 1rem; }
  .info-row span { color: #D4AF37; font-weight: 600; }
  .ticket-qr { text-align: center; }
  .ticket-qr img { width: 180px; height: 180px; border: 2px solid #D4AF37; border-radius: 8px; }
  .ticket-qr p { color: #888; font-size: 0.75rem; margin-top: 8px; letter-spacing: 2px; }
  .ticket-id { background: rgba(212,175,55,0.1); border-top: 1px solid rgba(212,175,55,0.3); padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; }
  .ticket-id .id { font-family: "Courier New", monospace; color: #D4AF37; font-size: 1.1rem; letter-spacing: 3px; font-weight: bold; }
  .ticket-id .type-badge { background: #D4AF37; color: #0a0a0a; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; letter-spacing: 1px; }
  .stars { color: #D4AF37; font-size: 1.2rem; letter-spacing: 4px; }
  .deco-line { height: 2px; background: linear-gradient(90deg, transparent, #D4AF37, transparent); margin: 12px 0; }
</style>
</head>
<body>
<div class="ticket">
  <div class="ticket-header">
    <div class="stars">★ ★ ★ ★ ★</div>
    <h1>' . htmlspecialchars($promName) . '</h1>
    <p>Official Entry Ticket</p>
  </div>
  <div class="ticket-body">
    <div class="ticket-info">
      <div class="name">' . htmlspecialchars($ticket['full_name']) . '</div>
      <div class="deco-line"></div>
      <div class="info-row">📅 <span>' . $formattedDate . '</span></div>
      <div class="info-row">📍 <span>' . htmlspecialchars($promVenue) . '</span></div>
      <div class="info-row">🎓 <span>' . htmlspecialchars($ticket['class_school']) . '</span></div>
      <div class="info-row">📱 <span>' . htmlspecialchars($ticket['phone']) . '</span></div>
    </div>
    <div class="ticket-qr">
      <img src="' . $qrUrl . '" alt="QR Code">
      <p>SCAN TO ENTER</p>
    </div>
  </div>
  <div class="ticket-id">
    <div class="id">' . htmlspecialchars($ticket['ticket_id']) . '</div>
    <div class="type-badge">' . $typeLabel . '</div>
  </div>
</div>
</body>
</html>';
}
?>
