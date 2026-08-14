<?php
require_once '../includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Golden Night — Candidates</title>
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
    <h1>Meet Our Candidates</h1>
    
    <p>Golden Night celebrates the exceptional students of our community. Our candidates were selected for their leadership, character, and contributions to school life.</p>
    
    <div class="note">
      <strong>Event Status:</strong> As Golden Night has been postponed, the candidate showcase has also been rescheduled. Our candidates remain exceptional and we look forward to celebrating them at the rescheduled event.
    </div>
    
    <h2>About the Candidate Selection</h2>
    <p>Each candidate was carefully selected through a democratic process involving nominations from their peers and evaluation by the student council. They represent the best of our school community—leaders, athletes, artists, scholars, and advocates for positive change.</p>
    
    <h2>Categories</h2>
    <p>• Prom King & Queen<br>
    • Class Representatives<br>
    • Spirit & Leadership Awards<br>
    • Most Likely To (Fun Categories)</p>
    
    <p style="margin-top: 50px; text-align: center;">
      <a href="/">← Return Home</a>
    </p>
  </main>

  <footer>
    <p>Golden Night © 2026 • A Kigali Leading School Tradition</p>
  </footer>
</body>
</html>
