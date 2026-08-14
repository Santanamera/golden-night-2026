<?php
require_once '../includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Golden Night — Contact</title>
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
    .contact-block { background: rgba(212, 175, 55, 0.04); border-left: 3px solid var(--gold); padding: 20px; margin: 20px 0; }
    .contact-block strong { color: var(--gold-soft); }
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
    <h1>Get In Touch</h1>
    
    <p>For inquiries about Golden Night or to stay updated on future events, please reach out to us.</p>
    
    <h2>Contact Information</h2>
    
    <div class="contact-block">
      <strong>Organization:</strong><br>
      Kigali Leading School - Student Activities
    </div>
    
    <div class="contact-block">
      <strong>Email:</strong><br>
      <a href="mailto:events@kigalileading.rw">events@kigalileading.rw</a>
    </div>
    
    <div class="contact-block">
      <strong>Phone:</strong><br>
      <a href="tel:+250788123456">+250 788 123 456</a>
    </div>
    
    <div class="contact-block">
      <strong>Social Media:</strong><br>
      <a href="https://www.instagram.com/kigali_leading_tss" target="_blank">Instagram</a> • <a href="https://www.facebook.com/kigalileading" target="_blank">Facebook</a>
    </div>
    
    <h2>Office Hours</h2>
    <p>Monday – Friday: 8:00 AM – 5:00 PM<br>
    Saturday – Sunday: Closed</p>
    
    <h2>Mailing Address</h2>
    <p>Kigali Leading School<br>
    Student Activities Office<br>
    KN 4 Ave, Kigali<br>
    Rwanda</p>
    
    <p style="margin-top: 50px; text-align: center;">
      <a href="/">← Return Home</a>
    </p>
  </main>

  <footer>
    <p>Golden Night © 2026 • A Kigali Leading School Tradition</p>
  </footer>
</body>
</html>
