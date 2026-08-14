<?php
require_once '../includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Golden Night — Event Details</title>
  <style>
    :root { --bg: #090909; --panel: #12110d; --gold: #d4af37; --gold-soft: #f0d060; --text: #f4ebd1; --muted: #b8ab7d; --line: rgba(212, 175, 55, 0.28); }
    * { box-sizing: border-box; }
    body { margin: 0; min-height: 100vh; background: var(--bg); color: var(--text); font-family: 'Cormorant Garamond', serif; }
    header { border-bottom: 1px solid var(--line); padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; }
    .logo { font-size: 1.3rem; color: var(--gold); letter-spacing: 2px; text-decoration: none; font-weight: bold; }
    nav a { color: var(--muted); text-decoration: none; margin: 0 20px; transition: color 0.3s; }
    nav a:hover { color: var(--gold-soft); }
    .wrap { max-width: 900px; margin: 60px auto; padding: 0 40px; }
    h1 { font-size: 2.8rem; color: var(--gold); margin-bottom: 30px; letter-spacing: 2px; }
    h2 { font-size: 1.6rem; color: var(--gold-soft); margin-top: 40px; margin-bottom: 20px; }
    p { font-size: 1.1rem; color: var(--muted); line-height: 1.8; margin: 20px 0; }
    .note { background: rgba(212, 175, 55, 0.08); border-left: 3px solid var(--gold); padding: 20px; margin: 30px 0; font-style: italic; color: var(--text); }
    .detail-box { background: rgba(212, 175, 55, 0.03); border: 1px solid var(--line); padding: 20px; margin: 20px 0; border-radius: 8px; }
    footer { text-align: center; padding: 40px; border-top: 1px solid var(--line); margin-top: 60px; color: var(--muted); font-size: 0.95rem; }
    a { color: var(--gold-soft); text-decoration: none; }
    a:hover { color: var(--gold); }
  </style>
</head>
<body>
  <header>
    <a href="/" class="logo">✦ GOLDEN NIGHT ✦</a>
    <nav>
      <a href="/">Home</a>
      <a href="/public/buy-ticket.php">About</a>
      <a href="/public/contact.php">Contact</a>
    </nav>
  </header>

  <main class="wrap">
    <h1>Event Information</h1>
    
    <div class="note">
      <strong>Status Update:</strong> Golden Night has been postponed. This page contains information about what was originally planned. We remain committed to making this celebration a reality and will announce new dates soon.
    </div>
    
    <h2>Original Event Details</h2>
    
    <div class="detail-box">
      <strong>Date:</strong> Friday, August 14th, 2026 (Postponed)
    </div>
    
    <div class="detail-box">
      <strong>Venue:</strong> RAKKA Hotel, Kigali, Rwanda<br>
      Elegant ballroom setting with full amenities
    </div>
    
    <div class="detail-box">
      <strong>Time:</strong> 4:00 PM – 10:00 PM<br>
      Doors open at 4:00 PM for early arrivals
    </div>
    
    <div class="detail-box">
      <strong>Dress Code:</strong> Black Tie / Formal Attire<br>
      Gowns, suits, tuxedos, and elegant evening wear
    </div>
    
    <h2>What Was Planned</h2>
    <p>Golden Night was designed as an elegant celebration bringing together our school community to mark a special milestone in our academic year. The event was to include:</p>
    <p>• Formal dinner and reception<br>
    • DJ and live music<br>
    • Candidate showcase and royalty voting<br>
    • Dancing and celebration<br>
    • Professional photography<br>
    • Special performances and surprises</p>
    
    <h2>Future Plans</h2>
    <p>We are working to reschedule Golden Night to a date that works better for our community. Stay tuned for announcements about the new date and any updates.</p>
    
    <p style="margin-top: 50px; text-align: center;">
      <a href="/">← Return Home</a>
    </p>
  </main>

  <footer>
    <p>Golden Night © 2026 • A Kigali Leading School Tradition</p>
  </footer>
</body>
</html>
