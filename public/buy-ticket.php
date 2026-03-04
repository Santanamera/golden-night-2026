<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Buy Ticket — Golden Night 2026</title>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;900&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --gold:#D4AF37; --gold-light:#f0d060; --gold-dim:rgba(212,175,55,0.12);
      --black:#0a0a0a; --black-soft:#111108; --text:#e8e0cc; --text-dim:#8a7d5a;
      --error:#ff4444; --success:#4CAF50; --momo:#FFCB05;
    }
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
    body{background:var(--black);color:var(--text);font-family:'Cormorant Garamond',serif;min-height:100vh;}

    .topbar{padding:18px 40px;display:flex;align-items:center;border-bottom:1px solid rgba(212,175,55,0.1);position:sticky;top:0;background:rgba(10,10,10,0.97);z-index:100;}
    .back-btn{font-family:'Montserrat',sans-serif;font-size:0.68rem;letter-spacing:3px;text-transform:uppercase;color:var(--text-dim);text-decoration:none;transition:color 0.3s;}
    .back-btn:hover{color:var(--gold);}
    .topbar-title{font-family:'Cinzel',serif;font-size:1rem;letter-spacing:4px;color:var(--gold);margin:0 auto;}

    .page-wrap{max-width:920px;margin:0 auto;padding:60px 24px;display:grid;grid-template-columns:1fr 1fr;gap:56px;align-items:start;}
    @media(max-width:780px){.page-wrap{grid-template-columns:1fr;gap:36px;padding:36px 18px;}}

    /* LEFT */
    .info-panel h1{font-family:'Cinzel',serif;font-size:2.2rem;color:var(--gold);line-height:1.2;margin-bottom:14px;letter-spacing:3px;}
    .info-panel p{color:var(--text-dim);font-size:1rem;line-height:1.8;margin-bottom:28px;}

    .price-card{background:var(--black-soft);border:1px solid rgba(212,175,55,0.15);padding:22px 20px;margin-bottom:14px;position:relative;cursor:pointer;transition:all 0.3s;}
    .price-card::before{content:'';position:absolute;top:0;left:0;width:3px;height:0;background:var(--gold);transition:height 0.35s;}
    .price-card:hover::before,.price-card.sel::before{height:100%;}
    .price-card.sel{border-color:var(--gold);background:rgba(212,175,55,0.05);}
    .pc-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;}
    .pc-type{font-family:'Cinzel',serif;font-size:0.95rem;color:var(--gold);letter-spacing:2px;}
    .pc-amt{font-family:'Cinzel',serif;font-size:1.3rem;color:var(--gold-light);}
    .pc-desc{font-size:0.88rem;color:var(--text-dim);}
    .pc-dot{position:absolute;top:18px;right:18px;width:15px;height:15px;border:1px solid var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;}
    .pc-dot::after{content:'';width:7px;height:7px;background:var(--gold);border-radius:50%;display:none;}
    .price-card.sel .pc-dot::after{display:block;}

    .event-info{margin-top:28px;padding:18px 20px;background:rgba(212,175,55,0.04);border-left:2px solid var(--gold);}
    .event-info p{font-size:0.9rem;color:var(--text-dim);line-height:2;}
    .event-info strong{color:var(--text);}

    /* RIGHT — Form */
    .form-panel{background:var(--black-soft);border:1px solid rgba(212,175,55,0.15);padding:38px;position:relative;overflow:hidden;}
    .form-panel::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent);}
    .form-title{font-family:'Cinzel',serif;font-size:1.2rem;color:var(--gold);letter-spacing:3px;margin-bottom:28px;text-align:center;}

    .fg{margin-bottom:20px;}
    .fl{display:block;font-family:'Montserrat',sans-serif;font-size:0.6rem;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:7px;}
    .fi{width:100%;background:rgba(0,0,0,0.5);border:1px solid rgba(212,175,55,0.2);color:var(--text);font-family:'Cormorant Garamond',serif;font-size:1rem;padding:13px 15px;outline:none;transition:all 0.3s;}
    .fi:focus{border-color:var(--gold);box-shadow:0 0 0 2px var(--gold-dim);}
    .fi::placeholder{color:var(--text-dim);}
    select.fi{cursor:pointer;}
    select.fi option{background:#111;color:var(--text);}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}

    /* PAYMENT STEPS */
    .pay-section{border-top:1px solid rgba(212,175,55,0.1);padding-top:22px;margin-top:6px;}
    .pay-title{font-family:'Cinzel',serif;font-size:0.88rem;color:var(--gold);letter-spacing:2px;margin-bottom:16px;}

    /* Step 1 — Amount display */
    .total-box{background:rgba(212,175,55,0.08);border:1px solid rgba(212,175,55,0.3);padding:14px 18px;display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;}
    .total-lbl{font-family:'Montserrat',sans-serif;font-size:0.62rem;letter-spacing:3px;text-transform:uppercase;color:var(--text-dim);}
    .total-val{font-family:'Cinzel',serif;font-size:1.5rem;color:var(--gold);}

    /* Step 2 — MoMo Push Button */
    .momo-box{background:rgba(255,203,5,0.07);border:1px solid rgba(255,203,5,0.3);padding:20px;margin-bottom:16px;}
    .momo-logo{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
    .momo-dot{width:32px;height:32px;background:var(--momo);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:900;color:#000;font-family:'Montserrat',sans-serif;flex-shrink:0;}
    .momo-label{font-family:'Cinzel',serif;font-size:0.9rem;color:var(--momo);letter-spacing:2px;}
    .momo-info{font-family:'Montserrat',sans-serif;font-size:0.76rem;color:var(--text-dim);line-height:1.7;margin-bottom:14px;}
    .momo-info strong{color:var(--text);}
    .momo-info .momo-num{font-size:1.1rem;color:var(--momo);font-family:'Courier New',monospace;letter-spacing:3px;}

    .momo-btn{
      width:100%;font-family:'Cinzel',serif;font-size:0.82rem;letter-spacing:3px;text-transform:uppercase;
      color:#000;background:var(--momo);border:none;padding:15px;cursor:pointer;
      transition:all 0.3s;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;gap:10px;
    }
    .momo-btn:hover{background:#ffd93d;box-shadow:0 0 30px rgba(255,203,5,0.4);}
    .momo-btn:disabled{opacity:0.5;cursor:not-allowed;}

    /* Phone input for MoMo */
    .phone-row{display:flex;gap:10px;margin-bottom:12px;}
    .phone-input{flex:1;background:rgba(0,0,0,0.5);border:1px solid rgba(255,203,5,0.3);color:var(--text);font-family:'Montserrat',sans-serif;font-size:1rem;padding:11px 14px;outline:none;transition:border 0.3s;letter-spacing:2px;}
    .phone-input:focus{border-color:var(--momo);}
    .phone-input::placeholder{color:var(--text-dim);letter-spacing:0;}

    /* Payment status */
    .pay-status{display:none;padding:12px 16px;margin-bottom:12px;font-family:'Montserrat',sans-serif;font-size:0.78rem;letter-spacing:1px;}
    .pay-status.pending{background:rgba(255,193,7,0.1);border:1px solid rgba(255,193,7,0.3);color:#FFC107;display:block;}
    .pay-status.done{background:rgba(76,175,80,0.1);border:1px solid rgba(76,175,80,0.3);color:#81C784;display:block;}
    .pay-status.fail{background:rgba(255,68,68,0.1);border:1px solid rgba(255,68,68,0.3);color:#ff8888;display:block;}

    /* Divider */
    .or-divider{display:flex;align-items:center;gap:12px;margin:16px 0;}
    .or-divider .line{flex:1;height:1px;background:rgba(212,175,55,0.1);}
    .or-divider span{font-family:'Montserrat',sans-serif;font-size:0.6rem;color:var(--text-dim);letter-spacing:3px;text-transform:uppercase;}

    /* Manual proof upload */
    .upload-area{border:2px dashed rgba(212,175,55,0.25);padding:28px;text-align:center;cursor:pointer;transition:all 0.3s;position:relative;}
    .upload-area:hover{border-color:var(--gold);background:var(--gold-dim);}
    .upload-area input{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;}
    .upload-icon{font-size:1.8rem;display:block;margin-bottom:7px;}
    .upload-text{font-family:'Montserrat',sans-serif;font-size:0.78rem;color:var(--text-dim);}
    .upload-name{color:var(--gold);font-size:0.8rem;margin-top:8px;font-family:'Montserrat',sans-serif;}

    /* Submit */
    .submit-btn{
      width:100%;font-family:'Cinzel',serif;font-size:0.88rem;letter-spacing:4px;text-transform:uppercase;
      color:var(--black);background:linear-gradient(135deg,#D4AF37,#f0d060,#D4AF37);background-size:200% 200%;
      border:none;padding:17px;cursor:pointer;transition:all 0.4s;margin-top:18px;
    }
    .submit-btn:hover{background-position:100% 100%;box-shadow:0 0 35px rgba(212,175,55,0.4);}
    .submit-btn:disabled{opacity:0.4;cursor:not-allowed;}

    /* Error */
    .err-box{background:rgba(255,68,68,0.1);border:1px solid rgba(255,68,68,0.3);color:#ff8888;padding:11px 14px;font-family:'Montserrat',sans-serif;font-size:0.78rem;margin-bottom:14px;display:none;}
    .err-box.show{display:block;}

    /* SUCCESS OVERLAY */
    .success-overlay{position:fixed;inset:0;background:rgba(10,10,10,0.98);z-index:9999;display:none;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:40px;overflow-y:auto;}
    .success-overlay.show{display:flex;animation:fadeIn 0.4s ease;}
    @keyframes fadeIn{from{opacity:0;transform:scale(0.95)}to{opacity:1;transform:scale(1)}}
    .s-icon{font-size:4.5rem;margin-bottom:20px;animation:bounce 0.5s ease;}
    @keyframes bounce{0%{transform:scale(0)}60%{transform:scale(1.2)}100%{transform:scale(1)}}
    .s-title{font-family:'Cinzel',serif;font-size:2.2rem;color:var(--gold);letter-spacing:4px;margin-bottom:12px;}
    .s-msg{font-size:1rem;color:var(--text-dim);max-width:400px;line-height:1.9;margin-bottom:28px;}
    .ticket-card{background:var(--black-soft);border:2px solid var(--gold);padding:30px;max-width:380px;width:100%;margin-bottom:28px;box-shadow:0 0 50px rgba(212,175,55,0.18);}
    .tc-brand{font-family:'Cinzel',serif;font-size:0.82rem;letter-spacing:4px;color:var(--gold-light);margin-bottom:4px;}
    .tc-sub{font-size:0.65rem;letter-spacing:2px;color:var(--text-dim);font-family:'Montserrat',sans-serif;}
    .tc-divider{height:1px;background:linear-gradient(90deg,transparent,var(--gold),transparent);margin:14px 0;}
    .tc-name{font-family:'Cinzel',serif;font-size:1.15rem;color:var(--text);margin-bottom:5px;}
    .tc-class{font-size:0.88rem;color:var(--text-dim);}
    .tc-qr{margin:18px 0;}
    .tc-qr img{width:170px;height:170px;border:2px solid var(--gold);}
    .tc-id{font-family:'Courier New',monospace;font-size:1.1rem;color:var(--gold);letter-spacing:4px;margin-bottom:4px;}
    .tc-scan{font-family:'Montserrat',sans-serif;font-size:0.6rem;letter-spacing:3px;color:var(--text-dim);text-transform:uppercase;}
    .tc-pending{margin-top:12px;padding:7px;background:rgba(255,193,7,0.1);font-family:'Montserrat',sans-serif;font-size:0.72rem;color:#FFC107;letter-spacing:1px;}
    .s-actions{display:flex;gap:14px;flex-wrap:wrap;justify-content:center;}
    .btn-dl{font-family:'Cinzel',serif;font-size:0.78rem;letter-spacing:3px;text-transform:uppercase;color:var(--black);background:var(--gold);padding:13px 26px;border:none;cursor:pointer;text-decoration:none;transition:all 0.3s;}
    .btn-home{font-family:'Cinzel',serif;font-size:0.78rem;letter-spacing:3px;text-transform:uppercase;color:var(--gold);background:transparent;padding:13px 26px;border:1px solid var(--gold);text-decoration:none;cursor:pointer;transition:all 0.3s;}
    .btn-home:hover{background:var(--gold-dim);}
  </style>
</head>
<body>

<div class="topbar">
  <a href="../index.html" class="back-btn">← Back</a>
  <div class="topbar-title">✦ GOLDEN NIGHT 2026</div>
</div>

<div class="page-wrap">

  <!-- LEFT PANEL -->
  <div class="info-panel">
    <h1>Secure Your Ticket</h1>
    <p>Join us for an unforgettable evening. Limited seats — reserve yours now before they're gone.</p>

    <div class="price-card sel" id="card-internal" onclick="selectType('internal')">
      <div class="pc-dot" id="dot-internal"></div>
      <div class="pc-top"><div class="pc-type">Internal Student</div><div class="pc-amt">Rwf 25,000</div></div>
      <div class="pc-desc">Currently enrolled TVET / REB student of the school</div>
    </div>

    <div class="price-card" id="card-external" onclick="selectType('external')">
      <div class="pc-dot" id="dot-external"></div>
      <div class="pc-top"><div class="pc-type">External Guest</div><div class="pc-amt">Rwf 30,000</div></div>
      <div class="pc-desc">Family members, alumni, and invited guests</div>
    </div>

    <div class="event-info">
      <p>
        📅 <strong>June 2026</strong> (Date TBD)<br>
        📍 <strong>Iwacu Garden, Kicukiro</strong><br>
        ⏰ Doors open at 6:00 PM<br>
        🎟️ QR ticket generated instantly<br>
        📱 Payment via MTN MoMo
      </p>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="form-panel">
    <h2 class="form-title">Registration Form</h2>

    <div class="err-box" id="errBox"></div>

    <div class="fg">
      <label class="fl">Full Name *</label>
      <input type="text" id="fullName" class="fi" placeholder="Enter your full name" maxlength="100"/>
    </div>

    <div class="form-row">
      <div class="fg">
        <label class="fl">Year / Class *</label>
        <input type="text" id="classSchool" class="fi" placeholder="e.g. S6 TVET" maxlength="100"/>
      </div>
      <div class="fg">
        <label class="fl">Your Phone *</label>
        <input type="tel" id="phone" class="fi" placeholder="+250 7XX XXX XXX" maxlength="20"/>
      </div>
    </div>

    <div class="fg">
      <label class="fl">Student Type *</label>
      <select id="studentType" class="fi" onchange="onTypeChange()">
        <option value="internal">Internal Student — Rwf 25,000</option>
        <option value="external">External Guest — Rwf 30,000</option>
      </select>
    </div>

    <!-- PAYMENT SECTION -->
    <div class="pay-section">
      <div class="pay-title">Payment</div>

      <div class="total-box">
        <span class="total-lbl">Amount Due</span>
        <span class="total-val" id="totalAmt">Rwf 25,000</span>
      </div>

      <!-- MTN MoMo Push -->
      <div class="momo-box">
        <div class="momo-logo">
          <div class="momo-dot">M</div>
          <div class="momo-label">MTN MoMo Pay</div>
        </div>
        <div class="momo-info">
          Paying to: <strong>Kenny</strong> &nbsp;·&nbsp; Code: <span class="momo-num">11111</span><br>
          Enter your MoMo number below and click <strong>"Send MoMo Request"</strong>.<br>
          You will receive a prompt on your phone to approve the payment.
        </div>

        <label class="fl" style="color:var(--momo);">Your MTN MoMo Number *</label>
        <div class="phone-row">
          <input type="tel" id="momoPhone" class="phone-input" placeholder="e.g. 0791234567" maxlength="13"/>
        </div>

        <div class="pay-status" id="payStatus"></div>

        <button class="momo-btn" id="momoBtn" onclick="sendMoMoRequest()">
          <span>📱</span>
          <span id="momoBtnText">Send MoMo Request</span>
        </button>
      </div>

      <!-- OR divider -->
      <div class="or-divider">
        <div class="line"></div>
        <span>or upload proof manually</span>
        <div class="line"></div>
      </div>

      <!-- Manual Upload -->
      <label class="fl">Payment Screenshot / Proof</label>
      <label class="upload-area" id="uploadArea">
        <input type="file" id="paymentProof" accept="image/*,application/pdf" onchange="onFile(this)"/>
        <span class="upload-icon">📎</span>
        <div class="upload-text">Click to upload screenshot<br><small>JPG, PNG, PDF · Max 5MB</small></div>
        <div class="upload-name" id="fileName"></div>
      </label>
    </div>

    <button class="submit-btn" id="submitBtn" onclick="submitTicket()">
      ✦ &nbsp; Complete Registration
    </button>
  </div>

</div>

<!-- SUCCESS OVERLAY -->
<div class="success-overlay" id="successOverlay">
  <div class="s-icon">🎉</div>
  <h2 class="s-title">You're In!</h2>
  <p class="s-msg">Your ticket for Golden Night 2026 has been registered. Show this QR code at the entrance on the night.</p>

  <div class="ticket-card">
    <div class="tc-brand">GOLDEN NIGHT 2026</div>
    <div class="tc-sub">Official Entry Ticket — Iwacu Garden, Kicukiro</div>
    <div class="tc-divider"></div>
    <div class="tc-name" id="sName"></div>
    <div class="tc-class" id="sClass"></div>
    <div class="tc-qr"><img id="sQR" src="" alt="QR Code"/></div>
    <div class="tc-id" id="sId"></div>
    <div class="tc-scan">Scan to enter</div>
    <div class="tc-pending">⏳ Awaiting payment confirmation from admin</div>
  </div>

  <div class="s-actions">
    <button class="btn-dl" onclick="printTicket()">🖨️ Print / Save Ticket</button>
    <a href="../index.html" class="btn-home">← Back to Home</a>
  </div>
</div>

<script>
// ---- STATE ----
let selType = 'internal';
let momoRequested = false;
let ticketData = null;

// MTN MoMo merchant code — replace 11111 with real code when ready
const MOMO_CODE = '11111';
const MOMO_NAME = 'Kenny';

// ---- TYPE SELECTION ----
function selectType(t) {
  selType = t;
  document.getElementById('studentType').value = t;
  document.querySelectorAll('.price-card').forEach(c => c.classList.remove('sel'));
  document.getElementById('card-' + t).classList.add('sel');
  updateTotal();
}

function onTypeChange() {
  selType = document.getElementById('studentType').value;
  document.querySelectorAll('.price-card').forEach(c => c.classList.remove('sel'));
  document.getElementById('card-' + selType)?.classList.add('sel');
  updateTotal();
}

function updateTotal() {
  const amt = selType === 'internal' ? 25000 : 30000;
  document.getElementById('totalAmt').textContent = 'Rwf ' + amt.toLocaleString();
}

// ---- FILE UPLOAD ----
function onFile(input) {
  const f = input.files[0];
  if (f) document.getElementById('fileName').textContent = '✓ ' + f.name;
}

// ---- MOMO PUSH REQUEST ----
async function sendMoMoRequest() {
  const phone = document.getElementById('momoPhone').value.trim().replace(/\s/g,'');
  const name  = document.getElementById('fullName').value.trim();
  const amt   = selType === 'internal' ? 25000 : 30000;
  const btn   = document.getElementById('momoBtn');
  const txt   = document.getElementById('momoBtnText');
  const status= document.getElementById('payStatus');

  if (!phone) {
    showErr('Please enter your MTN MoMo number first.');
    return;
  }
  if (!name) {
    showErr('Please enter your full name first.');
    return;
  }

  // Validate phone — must start with 07 or +2507 and be 10 digits
  const clean = phone.replace(/^\+250/, '0');
  if (!/^07[0-9]{8}$/.test(clean)) {
    showErr('Please enter a valid MTN Rwanda number (e.g. 0791234567).');
    return;
  }

  btn.disabled = true;
  txt.textContent = 'Sending request...';
  status.className = 'pay-status pending';
  status.textContent = '⏳ Sending payment request to ' + phone + '...';

  try {
    // Call our backend to trigger MoMo push via MTN MoMo API
    const res = await fetch('momo_request.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        phone:  clean,
        amount: amt,
        name:   name,
        reason: 'Golden Night 2026 Prom Ticket'
      })
    });
    const data = await res.json();

    if (data.success) {
      status.className = 'pay-status done';
      status.textContent = '✅ Request sent! Check your phone and approve the payment of Rwf ' + amt.toLocaleString();
      txt.textContent = '✓ Request Sent';
      momoRequested = true;
    } else {
      throw new Error(data.message || 'Request failed');
    }
  } catch (e) {
    // MTN API not yet configured — show USSD dial instruction clearly
    status.className = 'pay-status pending';
    status.innerHTML = `
      <div style="margin-bottom:10px;font-size:1rem;">📱 <strong>Pay via MoMo — 2 ways:</strong></div>
      <div style="margin-bottom:8px;">
        <strong>Option 1 — Dial this code:</strong><br>
        <span style="font-family:'Courier New',monospace;font-size:1.3rem;color:var(--momo);letter-spacing:3px;display:block;margin-top:4px;">*182*8*1*${MOMO_CODE}*${amt}#</span>
      </div>
      <div>
        <strong>Option 2 — MoMo App:</strong><br>
        Open MoMo app → Pay → Enter code <strong style="color:var(--momo)">${MOMO_CODE}</strong> → Amount: <strong>Rwf ${amt.toLocaleString()}</strong>
      </div>
    `;
    txt.textContent = '✓ Instructions Shown ↑';
    momoRequested = true;
    btn.disabled = false;
  }
}

// ---- SUBMIT ----
async function submitTicket() {
  const btn   = document.getElementById('submitBtn');
  const errBox= document.getElementById('errBox');

  const name  = document.getElementById('fullName').value.trim();
  const cls   = document.getElementById('classSchool').value.trim();
  const phone = document.getElementById('phone').value.trim();
  const type  = document.getElementById('studentType').value;
  const file  = document.getElementById('paymentProof').files[0];

  errBox.className = 'err-box';

  // Validation
  if (!name)  return showErr('Please enter your full name.');
  if (!cls)   return showErr('Please enter your class or school.');
  if (!phone) return showErr('Please enter your phone number.');
  if (!momoRequested && !file) return showErr('Please send a MoMo request or upload payment proof.');
  if (file && file.size > 5 * 1024 * 1024) return showErr('File too large. Max 5MB.');

  btn.disabled = true;
  btn.textContent = '⏳  Registering...';

  const fd = new FormData();
  fd.append('full_name',    name);
  fd.append('class_school', cls);
  fd.append('phone',        phone);
  fd.append('student_type', type);
  fd.append('momo_requested', momoRequested ? '1' : '0');
  if (file) fd.append('payment_proof', file);

  try {
    const res  = await fetch('ticket_api.php', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.success) {
      ticketData = data.ticket;
      showSuccess(data.ticket);
    } else {
      showErr(data.message || 'Registration failed. Please try again.');
      btn.disabled = false;
      btn.textContent = '✦   Complete Registration';
    }
  } catch {
    // Demo fallback — still saves visual ticket
    const fakeId = 'GN2026' + Math.random().toString(36).substr(2,6).toUpperCase();
    ticketData = { ticket_id: fakeId, full_name: name, class_school: cls, student_type: type };
    showSuccess(ticketData);
  }
}

function showErr(msg) {
  const e = document.getElementById('errBox');
  e.textContent = '⚠ ' + msg;
  e.className = 'err-box show';
  e.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function showSuccess(t) {
  const qr = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(t.ticket_id)}&bgcolor=111108&color=D4AF37&margin=10`;
  document.getElementById('sQR').src    = qr;
  document.getElementById('sId').textContent    = t.ticket_id;
  document.getElementById('sName').textContent  = t.full_name;
  document.getElementById('sClass').textContent = t.class_school + ' · ' + t.student_type;
  document.getElementById('successOverlay').classList.add('show');
}

function printTicket() {
  window.print();
}
</script>
</body>
</html>
