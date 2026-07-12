<?php
require_once '../includes/config.php';
if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login — Golden Night 2026</title>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Cormorant+Garamond:ital,wght@0,400;1,400&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet"/>
  <style>
    :root{--gold:#D4AF37;--gold-light:#f0d060;--gold-dim:rgba(212,175,55,0.12);--black:#0a0a0a;--black-soft:#111108;--text:#e8e0cc;--text-dim:#8a7d5a;--error:#ff4444;}
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
    body{
      background:var(--black);color:var(--text);
      font-family:'Cormorant Garamond',serif;
      min-height:100vh;
      display:flex;align-items:center;justify-content:center;
      position:relative;overflow:hidden;
    }

    /* Subtle background text */
    .bg-text{
      position:absolute;font-family:'Cinzel',serif;
      font-size:clamp(5rem,11vw,11rem);font-weight:900;
      color:rgba(212,175,55,0.025);top:50%;left:50%;
      transform:translate(-50%,-50%);white-space:nowrap;
      pointer-events:none;letter-spacing:16px;user-select:none;
    }

    /* Floating gold dots — CSS only, no canvas, no lag */
    .dot{
      position:fixed;width:2px;height:2px;
      background:var(--gold);border-radius:50%;
      pointer-events:none;opacity:0;
      animation:fdot linear infinite;
    }
    @keyframes fdot{
      0%  {opacity:0;transform:translateY(0);}
      15% {opacity:0.5;}
      85% {opacity:0.3;}
      100%{opacity:0;transform:translateY(-100vh);}
    }

    .wrap{width:100%;max-width:420px;padding:20px;position:relative;z-index:1;}

    /* Logo */
    .logo{text-align:center;margin-bottom:44px;}
    .logo-star{
      font-size:3.5rem;color:var(--gold);display:block;margin-bottom:10px;
      text-shadow:0 0 30px rgba(212,175,55,0.5);
      animation:pulse 3s ease-in-out infinite;
    }
    @keyframes pulse{0%,100%{text-shadow:0 0 20px rgba(212,175,55,0.4);}50%{text-shadow:0 0 50px rgba(212,175,55,0.7),0 0 80px rgba(212,175,55,0.3);}}
    .logo h1{font-family:'Cinzel',serif;font-size:1.25rem;color:var(--gold);letter-spacing:6px;margin-bottom:4px;}
    .logo p{font-family:'Montserrat',sans-serif;font-size:0.6rem;letter-spacing:4px;text-transform:uppercase;color:var(--text-dim);}

    /* Card */
    .card{
      background:var(--black-soft);
      border:1px solid rgba(212,175,55,0.2);
      padding:44px;position:relative;overflow:hidden;
    }
    .card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent);}
    .card::after {content:'';position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(212,175,55,0.25),transparent);}

    /* Corner decorations */
    .corner{position:absolute;width:18px;height:18px;border-color:rgba(212,175,55,0.3);border-style:solid;}
    .c-tl{top:0;left:0;border-width:1px 0 0 1px;}
    .c-tr{top:0;right:0;border-width:1px 1px 0 0;}
    .c-bl{bottom:0;left:0;border-width:0 0 1px 1px;}
    .c-br{bottom:0;right:0;border-width:0 1px 1px 0;}

    .card-title{font-family:'Cinzel',serif;font-size:1rem;color:var(--gold);letter-spacing:4px;text-align:center;margin-bottom:32px;}

    /* Error alert */
    .alert{
      background:rgba(255,68,68,0.1);border:1px solid rgba(255,68,68,0.35);
      color:#ff7777;padding:11px 15px;
      font-family:'Montserrat',sans-serif;font-size:0.78rem;letter-spacing:0.5px;
      margin-bottom:20px;display:none;
      animation:shake 0.4s ease;
    }
    .alert.show{display:block;}
    @keyframes shake{0%,100%{transform:translateX(0);}25%{transform:translateX(-6px);}75%{transform:translateX(6px);}50%{transform:translateX(6px);}}

    /* Form */
    .fg{margin-bottom:20px;position:relative;}
    .lbl{display:block;font-family:'Montserrat',sans-serif;font-size:0.6rem;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:7px;}
    .inp{
      width:100%;background:rgba(0,0,0,0.55);
      border:1px solid rgba(212,175,55,0.2);
      color:var(--text);font-family:'Montserrat',sans-serif;
      font-size:0.88rem;padding:13px 42px 13px 15px;
      transition:all 0.3s;outline:none;letter-spacing:0.5px;
    }
    .inp:focus{border-color:var(--gold);box-shadow:0 0 0 2px var(--gold-dim);}
    .inp::placeholder{color:var(--text-dim);font-size:0.82rem;}
    .inp-icon{position:absolute;right:14px;top:35px;color:var(--text-dim);}
    .eye{position:absolute;right:13px;top:34px;background:none;border:none;cursor:pointer;color:var(--text-dim);font-size:1.1rem;transition:color 0.3s;padding:2px;}
    .eye:hover{color:var(--gold);}

    /* Login button */
    .btn{
      width:100%;font-family:'Cinzel',serif;font-size:0.83rem;letter-spacing:4px;
      text-transform:uppercase;color:var(--black);
      background:linear-gradient(135deg,#D4AF37,#f0d060,#D4AF37);
      background-size:200% 200%;
      border:none;padding:17px;cursor:pointer;
      transition:all 0.4s ease;position:relative;overflow:hidden;
      margin-top:6px;
    }
    .btn:hover:not(:disabled){background-position:100% 100%;box-shadow:0 0 40px rgba(212,175,55,0.45);transform:translateY(-1px);}
    .btn:disabled{opacity:0.5;cursor:not-allowed;transform:none;}
    .btn::after{content:'';position:absolute;inset:0;background:linear-gradient(45deg,transparent 30%,rgba(255,255,255,0.25) 50%,transparent 70%);transform:translateX(-100%);transition:transform 0.5s ease;}
    .btn:hover::after{transform:translateX(100%);}

    /* Hint box */
    .hint{
      margin-top:18px;padding:12px 16px;
      background:rgba(212,175,55,0.05);
      border:1px solid rgba(212,175,55,0.15);
      font-family:'Montserrat',sans-serif;font-size:0.68rem;
      color:var(--text-dim);letter-spacing:0.5px;line-height:1.7;
      text-align:center;
    }
    .hint strong{color:var(--gold);}

    /* Back link */
    .back{display:block;text-align:center;margin-top:18px;font-family:'Montserrat',sans-serif;font-size:0.62rem;letter-spacing:3px;text-transform:uppercase;color:var(--text-dim);text-decoration:none;transition:color 0.3s;}
    .back:hover{color:var(--gold);}

    /* Success state */
    .success-flash{
      display:none;text-align:center;padding:20px;
    }
    .success-flash.show{display:block;}
    .success-flash .s-icon{font-size:3.5rem;display:block;margin-bottom:12px;animation:bounce 0.5s ease;}
    .success-flash h3{font-family:'Cinzel',serif;color:var(--gold);letter-spacing:3px;font-size:1.1rem;}
    @keyframes bounce{0%{transform:scale(0)}60%{transform:scale(1.2)}100%{transform:scale(1)}}
  </style>
</head>
<body>

<!-- Background text -->
<div class="bg-text">GN 2026</div>

<!-- CSS-only floating dots (no canvas = no lag) -->
<script>
  // Create 18 lightweight CSS dots
  for(let i=0;i<18;i++){
    const d=document.createElement('div');
    d.className='dot';
    d.style.left=Math.random()*100+'vw';
    d.style.top=Math.random()*100+'vh';
    d.style.animationDuration=(Math.random()*10+8)+'s';
    d.style.animationDelay=(Math.random()*8)+'s';
    d.style.width=d.style.height=(Math.random()<0.4?3:2)+'px';
    document.body.appendChild(d);
  }
</script>

<div class="wrap">

  <!-- Logo -->
  <div class="logo">
    <span class="logo-star">✦</span>
    <h1>GOLDEN NIGHT 2026</h1>
    <p>Prom Management System</p>
  </div>

  <!-- Login Card -->
  <div class="card" id="loginCard">
    <div class="corner c-tl"></div>
    <div class="corner c-tr"></div>
    <div class="corner c-bl"></div>
    <div class="corner c-br"></div>

    <h2 class="card-title">Admin Portal</h2>

    <!-- Error message -->
    <div class="alert" id="errMsg"></div>

    <!-- Username -->
    <div class="fg">
      <label class="lbl">Username</label>
      <input type="text" id="uname" class="inp" placeholder="Enter username" autocomplete="username"/>
      <span class="inp-icon">◈</span>
    </div>

    <!-- Password -->
    <div class="fg">
      <label class="lbl">Password</label>
      <input type="password" id="pword" class="inp" placeholder="Enter password" autocomplete="current-password"
             onkeypress="if(event.key==='Enter') login()"/>
      <button class="eye" id="eyeBtn" onclick="toggleEye()" type="button">👁</button>
    </div>

    <!-- Button -->
    <button class="btn" id="loginBtn" onclick="login()">
      ✦ &nbsp; Enter Admin Portal
    </button>

    <!-- Hint removed for security -->
  </div>

  <!-- Success flash (shown briefly before redirect) -->
  <div class="card" id="successCard" style="display:none;">
    <div class="success-flash show">
      <span class="s-icon">✦</span>
      <h3>Access Granted</h3>
      <p style="color:var(--text-dim);font-family:'Montserrat',sans-serif;font-size:0.75rem;margin-top:8px;letter-spacing:1px;">Redirecting to dashboard...</p>
    </div>
  </div>

  <a href="../index.html" class="back">← Return to Public Site</a>
</div>

<script>
// ============================================================
// Admin login uses server-side credentials only.
// ============================================================

// ============================================================
// LOGIN FUNCTION
// Uses PHP auth and redirects to dashboard on success.
// ============================================================
async function login() {
  const u   = document.getElementById('uname').value.trim();
  const p   = document.getElementById('pword').value;
  const err = document.getElementById('errMsg');
  const btn = document.getElementById('loginBtn');

  // Clear error
  err.className = 'alert';

  // Basic validation
  if (!u || !p) {
    showErr('Please enter both username and password.');
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Verifying...';

  // ---- Try PHP/DB auth first ----
  let phpSuccess = false;
  try {
    const res  = await fetch('auth.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ username: u, password: p })
    });
    const data = await res.json();
    if (data.success) {
      phpSuccess = true;
    }
  } catch {
    // Login failed due to a server or network error.
  }

  // ---- Result ----
  if (phpSuccess) {
    // Show success flash
    document.getElementById('loginCard').style.display    = 'none';
    document.getElementById('successCard').style.display  = 'block';
    // Redirect after short delay
    setTimeout(() => window.location.href = 'dashboard.php', 1200);
  } else {
    showErr('Invalid username or password.');
    btn.disabled = false;
    btn.textContent = '✦   Enter Admin Portal';
    // Shake the card
    const card = document.getElementById('loginCard');
    card.style.animation = 'none';
    void card.offsetWidth;
    card.style.animation = '';
  }
}

function showErr(msg) {
  const e = document.getElementById('errMsg');
  e.textContent = msg;
  e.classList.add('show');
}

function toggleEye() {
  const p   = document.getElementById('pword');
  const btn = document.getElementById('eyeBtn');
  if (p.type === 'password') { p.type = 'text';     btn.textContent = '🙈'; }
  else                       { p.type = 'password'; btn.textContent = '👁'; }
}

window.addEventListener('DOMContentLoaded', () => {
  document.getElementById('uname').focus();
});
</script>
</body>
</html>
