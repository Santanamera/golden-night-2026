<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Vote — Golden Night 2026</title>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;900&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --gold:#D4AF37; --gold-light:#f0d060; --gold-dim:rgba(212,175,55,0.12);
      --black:#0a0a0a; --black-soft:#111108; --text:#e8e0cc; --text-dim:#8a7d5a;
      --error:#ff4444;
    }
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
    body{background:var(--black);color:var(--text);font-family:'Cormorant Garamond',serif;min-height:100vh;}

    .topbar{padding:20px 40px;display:flex;align-items:center;border-bottom:1px solid rgba(212,175,55,0.1);}
    .back-btn{font-family:'Montserrat',sans-serif;font-size:0.7rem;letter-spacing:3px;text-transform:uppercase;color:var(--text-dim);text-decoration:none;transition:color 0.3s;}
    .back-btn:hover{color:var(--gold);}
    .topbar-title{font-family:'Cinzel',serif;font-size:1rem;letter-spacing:4px;color:var(--gold);margin:0 auto;}

    .hero-section{text-align:center;padding:80px 20px 60px;background:radial-gradient(ellipse at top, rgba(212,175,55,0.06), transparent 60%);}
    .hero-section h1{font-family:'Cinzel',serif;font-size:clamp(2rem,6vw,4rem);color:var(--gold);letter-spacing:6px;margin-bottom:16px;}
    .hero-section p{font-style:italic;font-size:1.1rem;color:var(--text-dim);max-width:500px;margin:0 auto 40px;}

    /* Auth box */
    .auth-box{max-width:500px;margin:0 auto 60px;padding:0 20px;}
    .auth-card{background:var(--black-soft);border:1px solid rgba(212,175,55,0.2);padding:40px;position:relative;}
    .auth-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent);}
    .auth-title{font-family:'Cinzel',serif;font-size:1.1rem;color:var(--gold);letter-spacing:3px;margin-bottom:24px;text-align:center;}
    .form-label{display:block;font-family:'Montserrat',sans-serif;font-size:0.65rem;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:8px;}
    .form-input{width:100%;background:rgba(0,0,0,0.5);border:1px solid rgba(212,175,55,0.2);color:var(--text);font-family:'Cormorant Garamond',serif;font-size:1rem;padding:14px 16px;transition:all 0.3s;outline:none;margin-bottom:20px;}
    .form-input:focus{border-color:var(--gold);box-shadow:0 0 0 2px var(--gold-dim);}
    .btn-verify{width:100%;font-family:'Cinzel',serif;font-size:0.85rem;letter-spacing:3px;text-transform:uppercase;color:var(--black);background:linear-gradient(135deg,#D4AF37,#f0d060,#D4AF37);background-size:200%;border:none;padding:16px;cursor:pointer;transition:all 0.4s;}
    .btn-verify:hover{background-position:100%;box-shadow:0 0 30px rgba(212,175,55,0.3);}
    .error-msg{color:var(--error);font-size:0.85rem;margin-top:10px;font-family:'Montserrat',sans-serif;text-align:center;display:none;}
    .error-msg.show{display:block;}

    /* Voting section */
    .voting-section{display:none;padding:40px 20px 80px;}
    .voting-section.show{display:block;}
    .vote-category{max-width:1100px;margin:0 auto 70px;}
    .category-title{font-family:'Cinzel',serif;font-size:1.8rem;color:var(--gold);letter-spacing:5px;text-align:center;margin-bottom:8px;}
    .category-sub{text-align:center;color:var(--text-dim);font-style:italic;margin-bottom:40px;}
    .category-line{height:1px;background:linear-gradient(90deg,transparent,var(--gold),transparent);margin-bottom:40px;}

    .candidates-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:24px;max-width:1100px;margin:0 auto;}

    .candidate-card{
      background:var(--black-soft);border:2px solid rgba(212,175,55,0.1);
      overflow:hidden;cursor:pointer;transition:all 0.4s ease;position:relative;
    }
    .candidate-card:hover{border-color:rgba(212,175,55,0.4);transform:translateY(-6px);}
    .candidate-card.selected{border-color:var(--gold);box-shadow:0 0 30px rgba(212,175,55,0.2);}
    .candidate-card.selected::before{
      content:'✓ SELECTED';
      position:absolute;top:12px;right:12px;
      background:var(--gold);color:var(--black);
      font-family:'Montserrat',sans-serif;font-size:0.6rem;letter-spacing:2px;
      padding:4px 10px;font-weight:600;z-index:10;
    }

    .candidate-photo{width:100%;height:220px;object-fit:cover;display:block;background:#111;filter:grayscale(30%);}
    .candidate-photo-placeholder{width:100%;height:220px;background:linear-gradient(135deg,#1a1a10,#111);display:flex;align-items:center;justify-content:center;font-size:4rem;}
    .candidate-info{padding:20px;}
    .candidate-name{font-family:'Cinzel',serif;font-size:1.1rem;color:var(--gold);letter-spacing:2px;margin-bottom:4px;}
    .candidate-class{font-size:0.85rem;color:var(--text-dim);margin-bottom:10px;}
    .candidate-bio{font-size:0.9rem;color:var(--text-dim);line-height:1.6;font-style:italic;}

    /* Selected indicator */
    .vote-ring{
      position:absolute;inset:0;border:3px solid var(--gold);opacity:0;
      transition:opacity 0.3s;pointer-events:none;
    }
    .candidate-card.selected .vote-ring{opacity:1;}

    /* Vote summary */
    .vote-summary{
      max-width:600px;margin:0 auto 40px;
      background:var(--black-soft);border:1px solid rgba(212,175,55,0.2);padding:32px;
      display:none;
    }
    .vote-summary.show{display:block;}
    .vote-summary-title{font-family:'Cinzel',serif;font-size:1rem;color:var(--gold);letter-spacing:3px;text-align:center;margin-bottom:20px;}
    .vote-summary-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid rgba(212,175,55,0.1);}
    .vote-summary-row:last-child{border-bottom:none;}
    .vote-summary-label{font-family:'Montserrat',sans-serif;font-size:0.7rem;letter-spacing:2px;text-transform:uppercase;color:var(--text-dim);}
    .vote-summary-value{color:var(--gold);font-size:1rem;}

    .btn-submit-vote{
      display:block;width:100%;max-width:400px;margin:0 auto;
      font-family:'Cinzel',serif;font-size:0.9rem;letter-spacing:4px;text-transform:uppercase;
      color:var(--black);background:linear-gradient(135deg,#D4AF37,#f0d060,#D4AF37);
      background-size:200%;border:none;padding:20px;cursor:pointer;transition:all 0.4s;
    }
    .btn-submit-vote:hover{background-position:100%;box-shadow:0 0 40px rgba(212,175,55,0.4);}
    .btn-submit-vote:disabled{opacity:0.4;cursor:not-allowed;}

    /* Success */
    .success-vote{
      text-align:center;padding:60px 20px;display:none;
    }
    .success-vote.show{display:block;}
    .success-vote .emoji{font-size:5rem;margin-bottom:24px;display:block;}
    .success-vote h2{font-family:'Cinzel',serif;font-size:2.5rem;color:var(--gold);letter-spacing:4px;margin-bottom:16px;}
    .success-vote p{color:var(--text-dim);font-size:1.1rem;font-style:italic;}

    @media(max-width:600px){.candidates-grid{grid-template-columns:1fr 1fr;}}
    @media(max-width:400px){.candidates-grid{grid-template-columns:1fr;}}
  </style>
</head>
<body>

<div class="topbar">
  <a href="../index.html" class="back-btn">← Back</a>
  <div class="topbar-title">✦ VOTE FOR ROYALTY</div>
</div>

<!-- HERO -->
<div class="hero-section">
  <h1>Crown Your Royalty</h1>
  <p>Choose the students who embody the spirit of Golden Night 2026</p>
</div>

<!-- AUTH -->
<div class="auth-box" id="authBox">
  <div class="auth-card">
    <h3 class="auth-title">Enter Your Ticket ID to Vote</h3>
    <label class="form-label">Ticket ID</label>
    <input type="text" id="ticketInput" class="form-input" placeholder="e.g. GN2026XXXXXX" maxlength="20" style="text-transform:uppercase; letter-spacing:3px;" oninput="this.value=this.value.toUpperCase()"/>
    <button class="btn-verify" onclick="verifyTicket()">Verify & Continue →</button>
    <div class="error-msg" id="authError"></div>
  </div>
</div>

<!-- VOTING SECTION -->
<div class="voting-section" id="votingSection">

  <p style="text-align:center;color:var(--gold);font-family:'Montserrat',sans-serif;font-size:0.7rem;letter-spacing:3px;text-transform:uppercase;margin-bottom:40px;">
    Voting as: <span id="voterName" style="font-size:0.85rem;"></span>
  </p>

  <!-- KING CANDIDATES -->
  <div class="vote-category">
    <div class="category-title">👑 Prom King</div>
    <p class="category-sub">Vote for your Prom King 2026</p>
    <div class="category-line"></div>
    <div class="candidates-grid" id="kingGrid">
      <!-- loaded dynamically -->
    </div>
  </div>

  <!-- QUEEN CANDIDATES -->
  <div class="vote-category">
    <div class="category-title">👸 Prom Queen</div>
    <p class="category-sub">Vote for your Prom Queen 2026</p>
    <div class="category-line"></div>
    <div class="candidates-grid" id="queenGrid">
      <!-- loaded dynamically -->
    </div>
  </div>

  <!-- VOTE SUMMARY -->
  <div class="vote-summary" id="voteSummary">
    <div class="vote-summary-title">✦ Your Votes</div>
    <div class="vote-summary-row">
      <span class="vote-summary-label">Prom King</span>
      <span class="vote-summary-value" id="summaryKing">—</span>
    </div>
    <div class="vote-summary-row">
      <span class="vote-summary-label">Prom Queen</span>
      <span class="vote-summary-value" id="summaryQueen">—</span>
    </div>
  </div>

  <button class="btn-submit-vote" id="submitVoteBtn" onclick="submitVote()" disabled>
    ✦ &nbsp; Submit My Votes
  </button>

</div>

<!-- SUCCESS -->
<div class="success-vote" id="successVote">
  <span class="emoji">👑</span>
  <h2>Your Vote is Cast!</h2>
  <p>Thank you for voting. Results will be revealed at the Grand Ceremony.<br><br>See you on Golden Night 2026! ✨</p>
  <br><br>
  <a href="../index.html" style="font-family:'Cinzel',serif;font-size:0.8rem;letter-spacing:3px;color:var(--gold);text-decoration:none;border:1px solid var(--gold);padding:14px 28px;">← Return Home</a>
</div>

<script>
let verifiedTicket = null;
let selectedKing = null;
let selectedQueen = null;

async function verifyTicket() {
  const input = document.getElementById('ticketInput').value.trim().toUpperCase();
  const errDiv = document.getElementById('authError');
  errDiv.classList.remove('show');

  if (!input || input.length < 8) {
    errDiv.textContent = 'Please enter a valid ticket ID.';
    errDiv.classList.add('show');
    return;
  }

  try {
    const res  = await fetch(`vote_api.php?action=verify&ticket_id=${encodeURIComponent(input)}`);
    const data = await res.json();
    if (data.success) {
      verifiedTicket = data.ticket;
      showVoting(data.ticket);
    } else {
      errDiv.textContent = data.message;
      errDiv.classList.add('show');
    }
  } catch (e) {
    errDiv.textContent = 'Could not connect. Please try again.';
    errDiv.classList.add('show');
  }
}

function showVoting(ticket) {
  if (ticket.already_voted) {
    document.getElementById('authBox').style.display = 'none';
    document.getElementById('successVote').classList.add('show');
    document.querySelector('.success-vote h2').textContent = 'Already Voted!';
    document.querySelector('.success-vote p').textContent = 'You have already cast your votes. Results will be revealed at the ceremony.';
    return;
  }
  document.getElementById('authBox').style.display = 'none';
  document.getElementById('voterName').textContent = ticket.full_name + ' · ' + ticket.ticket_id;
  document.getElementById('votingSection').classList.add('show');
  loadCandidates();
}

async function loadCandidates() {
  try {
    const res  = await fetch('vote_api.php?action=candidates');
    const data = await res.json();

    if (!data.success) throw new Error(data.message);

    if (!data.king.length && !data.queen.length) {
      document.getElementById('kingGrid').innerHTML  = '<p style="color:var(--text-dim);font-style:italic;grid-column:1/-1;text-align:center;padding:30px;">No candidates registered yet.</p>';
      document.getElementById('queenGrid').innerHTML = '<p style="color:var(--text-dim);font-style:italic;grid-column:1/-1;text-align:center;padding:30px;">No candidates registered yet.</p>';
      return;
    }

    renderGrid('kingGrid',  data.king,  'king');
    renderGrid('queenGrid', data.queen, 'queen');

  } catch (e) {
    document.getElementById('kingGrid').innerHTML  = '<p style="color:var(--error);grid-column:1/-1;text-align:center;padding:30px;">Failed to load candidates. Please refresh.</p>';
    document.getElementById('queenGrid').innerHTML = '';
  }
}

function renderGrid(gridId, candidates, category) {
  const grid = document.getElementById(gridId);
  if (!candidates.length) {
    grid.innerHTML = '<p style="color:var(--text-dim);font-style:italic;grid-column:1/-1;text-align:center;padding:30px;">No candidates in this category yet.</p>';
    return;
  }
  grid.innerHTML = candidates.map(c => `
    <div class="candidate-card" id="card-${c.id}" onclick="selectCandidate(${c.id}, '${category}', '${c.full_name.replace(/'/g,"\\'")}')">
      <div class="vote-ring"></div>
      ${c.photo
        ? `<img src="/${c.photo}" class="candidate-photo" alt="${c.full_name}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/><div class="candidate-photo-placeholder" style="display:none">${category === 'king' ? '👑' : '👸'}</div>`
        : `<div class="candidate-photo-placeholder">${category === 'king' ? '👑' : '👸'}</div>`
      }
      <div class="candidate-info">
        <div class="candidate-name">${c.full_name}</div>
        <div class="candidate-class">${c.class_school}</div>
        <div class="candidate-bio">"${c.bio}"</div>
      </div>
    </div>
  `).join('');
}

function selectCandidate(id, category, name) {
  if (category === 'king') {
    if (selectedKing) document.getElementById('card-' + selectedKing)?.classList.remove('selected');
    selectedKing = id;
    document.getElementById('summaryKing').textContent = name;
  } else {
    if (selectedQueen) document.getElementById('card-' + selectedQueen)?.classList.remove('selected');
    selectedQueen = id;
    document.getElementById('summaryQueen').textContent = name;
  }
  document.getElementById('card-' + id).classList.add('selected');
  if (selectedKing || selectedQueen) document.getElementById('voteSummary').classList.add('show');
  document.getElementById('submitVoteBtn').disabled = !(selectedKing && selectedQueen);
}

async function submitVote() {
  if (!selectedKing || !selectedQueen || !verifiedTicket) return;

  const btn = document.getElementById('submitVoteBtn');
  btn.disabled = true;
  btn.textContent = 'Submitting...';

  try {
    const res  = await fetch('vote_api.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({
        ticket_id: verifiedTicket.ticket_id,
        king_id:   selectedKing,
        queen_id:  selectedQueen
      })
    });
    const data = await res.json();
    if (data.success) {
      showVoteSuccess();
    } else {
      alert(data.message || 'Voting failed. Please try again.');
      btn.disabled = false;
      btn.textContent = '✦   Submit My Votes';
    }
  } catch (e) {
    alert('Connection error. Please try again.');
    btn.disabled = false;
    btn.textContent = '✦   Submit My Votes';
  }
}

function showVoteSuccess() {
  document.getElementById('votingSection').style.display = 'none';
  document.getElementById('successVote').classList.add('show');
}
</script>
</body>
</html>
