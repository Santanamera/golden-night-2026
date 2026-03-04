/* ============================================
   GOLDEN NIGHT 2026 — Admin JavaScript
   assets/js/admin.js
   ============================================ */

'use strict';

// ============================================================
// ADMIN AUTH CHECK
// ============================================================
function checkAdminAuth() {
  // Check demo session (JS-only mode)
  if (!sessionStorage.getItem('admin_demo') && !getCookie('PHPSESSID')) {
    window.location.href = 'login.php';
    return false;
  }
  const name = sessionStorage.getItem('admin_name') || 'Admin';
  const el = document.getElementById('adminName');
  if (el) el.textContent = name;
  return true;
}

function getCookie(name) {
  return document.cookie.split(';').some(c => c.trim().startsWith(name + '='));
}

function adminLogout() {
  sessionStorage.clear();
  fetch('auth.php?action=logout').catch(() => {});
  window.location.href = 'login.php';
}

// ============================================================
// SIDEBAR NAVIGATION
// ============================================================
function initAdminNav() {
  // Clock
  const clockEl = document.getElementById('adminClock');
  if (clockEl) {
    setInterval(() => {
      clockEl.textContent = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    }, 1000);
  }

  // Mobile toggle
  const mobBtn  = document.getElementById('mobMenuBtn');
  const sidebar = document.getElementById('adminSidebar');
  if (mobBtn && sidebar) {
    mobBtn.addEventListener('click', () => sidebar.classList.toggle('open'));
    // Close when clicking outside
    document.addEventListener('click', e => {
      if (!sidebar.contains(e.target) && !mobBtn.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  }
}

// Navigate to a page within the SPA dashboard
function showAdminPage(pageId) {
  document.querySelectorAll('.admin-page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-link').forEach(n => n.classList.remove('active'));

  const page = document.getElementById('page-' + pageId);
  if (page) page.classList.add('active');

  document.querySelectorAll('.nav-link').forEach(n => {
    if (n.dataset.page === pageId) n.classList.add('active');
  });

  const titles = {
    dashboard:  'Dashboard',
    tickets:    'Ticket Management',
    scanner:    'QR Entry Scanner',
    candidates: 'Candidate Management',
    votes:      'Vote Results'
  };

  const titleEl = document.getElementById('pageTitle');
  if (titleEl) titleEl.textContent = titles[pageId] || pageId;

  // Lazy-load page data
  const loaders = { tickets: loadTickets, candidates: loadCandidates, votes: loadVotes };
  if (loaders[pageId]) loaders[pageId]();

  // Close mobile sidebar
  document.getElementById('adminSidebar')?.classList.remove('open');
}

// ============================================================
// ADMIN TOAST
// ============================================================
function adminToast(msg, type = 'default') {
  const t = document.getElementById('adminToast');
  if (!t) return;
  t.textContent = '✦ ' + msg;
  t.className = 'toast' + (type !== 'default' ? ' toast-' + type : '');
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}

// ============================================================
// API HELPER
// ============================================================
async function adminAPI(action, method = 'GET', body = null) {
  const url = `../public/admin_api.php?action=${action}`;
  const opts = {
    method,
    headers: method === 'POST' ? { 'Content-Type': 'application/json' } : {},
  };
  if (body && method === 'POST') opts.body = JSON.stringify(body);

  const res = await fetch(url, opts);
  return res.json();
}

// ============================================================
// DEMO DATA
// ============================================================
function getDemoTickets() {
  return [
    { ticket_id:'GN2026A1B2C3', full_name:'Alexandra Johnson', class_school:'XII IPA 1', phone:'081234567890', student_type:'internal', payment_status:'confirmed', ticket_status:'used',   amount_paid:50000, created_at:'2026-05-01 10:30' },
    { ticket_id:'GN2026D4E5F6', full_name:'Marcus Williams',   class_school:'XII IPS 2', phone:'082345678901', student_type:'external', payment_status:'confirmed', ticket_status:'unused', amount_paid:75000, created_at:'2026-05-02 14:15' },
    { ticket_id:'GN2026G7H8I9', full_name:'Sophia Chen',       class_school:'XII IPA 3', phone:'083456789012', student_type:'internal', payment_status:'pending',   ticket_status:'unused', amount_paid:50000, created_at:'2026-05-03 09:00' },
    { ticket_id:'GN2026J1K2L3', full_name:'Daniel Park',       class_school:'XII IPS 1', phone:'084567890123', student_type:'internal', payment_status:'confirmed', ticket_status:'unused', amount_paid:50000, created_at:'2026-05-04 11:45' },
    { ticket_id:'GN2026M4N5O6', full_name:'Isabella Santos',   class_school:'Alumni 2024',phone:'085678901234', student_type:'external', payment_status:'pending',  ticket_status:'unused', amount_paid:75000, created_at:'2026-05-05 16:30' },
    { ticket_id:'GN2026P7Q8R9', full_name:'Nathan Rivera',     class_school:'XII IPA 2', phone:'086789012345', student_type:'internal', payment_status:'confirmed', ticket_status:'unused', amount_paid:50000, created_at:'2026-05-06 13:20' },
  ];
}

function getDemoCandidates() {
  return [
    { id:1, full_name:'Alexander Kim',  category:'king',  class_school:'XII IPA 1', bio:'Student council president, basketball captain.', status:'approved', vote_count:45 },
    { id:2, full_name:'Marcus Santos',  category:'king',  class_school:'XII IPS 2', bio:'Theatre lead, debate champion.', status:'approved', vote_count:38 },
    { id:3, full_name:'Daniel Reyes',   category:'king',  class_school:'XII IPA 3', bio:'Valedictorian candidate, chess club president.', status:'pending', vote_count:0 },
    { id:4, full_name:'Sophia Tan',     category:'queen', class_school:'XII IPA 2', bio:'Student journalist, volunteer coordinator.', status:'approved', vote_count:52 },
    { id:5, full_name:'Isabella Park',  category:'queen', class_school:'XII IPS 3', bio:'Dance captain, art club leader.', status:'approved', vote_count:41 },
    { id:6, full_name:'Aurora Lim',     category:'queen', class_school:'XII IPA 1', bio:'Science olympiad winner, environmental advocate.', status:'pending', vote_count:0 },
  ];
}

// ============================================================
// DASHBOARD STATS
// ============================================================
async function loadDashboard() {
  try {
    const data = await adminAPI('stats');
    if (data.success) renderStats(data.stats);
  } catch {
    const t = getDemoTickets();
    const confirmed = t.filter(x => x.payment_status === 'confirmed');
    renderStats({
      total_tickets: t.length,
      confirmed: confirmed.length,
      used: t.filter(x => x.ticket_status === 'used').length,
      revenue: confirmed.reduce((s, x) => s + x.amount_paid, 0),
      votes: 83,
      candidates: 4,
      pending_payments: 2,
      pending_candidates: 2,
      internal_revenue: confirmed.filter(x => x.student_type === 'internal').reduce((s, x) => s + x.amount_paid, 0),
      external_revenue: confirmed.filter(x => x.student_type === 'external').reduce((s, x) => s + x.amount_paid, 0),
    });
  }

  // Recent tickets
  try {
    const data = await adminAPI('tickets');
    renderRecentTickets(data.success ? data.tickets.slice(0, 5) : getDemoTickets().slice(0, 5));
  } catch {
    renderRecentTickets(getDemoTickets().slice(0, 5));
  }
}

function renderStats(s) {
  setStat('statTotal',      s.total_tickets ?? 0);
  setStat('statConfirmed',  s.confirmed ?? 0);
  setStat('statUsed',       s.used ?? 0);
  setStat('statRevenue',    'Rp ' + ((s.revenue ?? 0) / 1000).toFixed(0) + 'K');
  setStat('statVotes',      s.votes ?? 0);
  setStat('statCandidates', s.candidates ?? 0);

  const pb = document.getElementById('pendingBadge');
  const cb = document.getElementById('candBadge');
  if (pb) pb.textContent = s.pending_payments ?? 0;
  if (cb) cb.textContent = s.pending_candidates ?? 0;

  setStat('revInternal', 'Rp ' + Number(s.internal_revenue ?? 0).toLocaleString('id-ID'));
  setStat('revExternal', 'Rp ' + Number(s.external_revenue ?? 0).toLocaleString('id-ID'));
  setStat('revTotal',    'Rp ' + Number(s.revenue ?? 0).toLocaleString('id-ID'));
}

function setStat(id, val) {
  const el = document.getElementById(id);
  if (el) el.textContent = val;
}

function renderRecentTickets(tickets) {
  const tbody = document.getElementById('recentTbody');
  if (!tbody) return;
  tbody.innerHTML = tickets.map(t => `
    <tr>
      <td class="td-id">${t.ticket_id}</td>
      <td class="td-primary">${t.full_name}</td>
      <td><span class="badge badge-${t.student_type}">${t.student_type}</span></td>
      <td><span class="badge badge-${t.payment_status}">${t.payment_status}</span></td>
    </tr>
  `).join('');
}

// ============================================================
// TICKET MANAGEMENT
// ============================================================
let allTickets = [];
let ticketFilter = 'all';

async function loadTickets() {
  try {
    const data = await adminAPI('tickets');
    allTickets = data.success ? data.tickets : getDemoTickets();
  } catch {
    allTickets = getDemoTickets();
  }
  renderTickets();
}

function setTicketFilter(filter, btn) {
  ticketFilter = filter;
  document.querySelectorAll('#page-tickets .filter-tab').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  renderTickets();
}

function renderTickets() {
  const search = (document.getElementById('ticketSearch')?.value || '').toLowerCase();
  let list = [...allTickets];

  if (ticketFilter === 'pending')   list = list.filter(t => t.payment_status === 'pending');
  if (ticketFilter === 'confirmed') list = list.filter(t => t.payment_status === 'confirmed');
  if (ticketFilter === 'used')      list = list.filter(t => t.ticket_status === 'used');
  if (search) list = list.filter(t => t.full_name.toLowerCase().includes(search) || t.ticket_id.toLowerCase().includes(search));

  const tbody = document.getElementById('ticketsTbody');
  if (!tbody) return;

  if (!list.length) {
    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-dim);font-style:italic;">No tickets found</td></tr>';
    return;
  }

  tbody.innerHTML = list.map(t => `
    <tr>
      <td class="td-id">${t.ticket_id}</td>
      <td class="td-primary">${t.full_name}</td>
      <td>${t.class_school}</td>
      <td>${t.phone}</td>
      <td><span class="badge badge-${t.student_type}">${t.student_type}</span></td>
      <td><span class="badge badge-${t.payment_status}">${t.payment_status}</span></td>
      <td><span class="badge badge-${t.ticket_status === 'used' ? 'used' : 'confirmed'}">${t.ticket_status}</span></td>
      <td>
        ${t.payment_status === 'pending' ? `<button class="btn-sm btn-confirm" onclick="confirmTicketPayment('${t.ticket_id}')">✓ Confirm</button>` : ''}
        ${t.payment_status === 'pending' ? `<button class="btn-sm btn-reject"  onclick="rejectTicketPayment('${t.ticket_id}')">✗ Reject</button>` : ''}
      </td>
    </tr>
  `).join('');
}

async function confirmTicketPayment(ticketId) {
  if (!confirm('Confirm payment for ticket ' + ticketId + '?')) return;
  try {
    await adminAPI('confirm_payment', 'POST', { ticket_id: ticketId });
  } catch {}
  const t = allTickets.find(x => x.ticket_id === ticketId);
  if (t) t.payment_status = 'confirmed';
  renderTickets();
  adminToast('Payment confirmed: ' + ticketId, 'success');
}

async function rejectTicketPayment(ticketId) {
  if (!confirm('Reject payment for ticket ' + ticketId + '?')) return;
  try {
    await adminAPI('reject_payment', 'POST', { ticket_id: ticketId });
  } catch {}
  const t = allTickets.find(x => x.ticket_id === ticketId);
  if (t) t.payment_status = 'rejected';
  renderTickets();
  adminToast('Payment rejected: ' + ticketId, 'error');
}

// ============================================================
// CANDIDATES
// ============================================================
let allCandidates = [];
let candFilter = 'all';

async function loadCandidates() {
  try {
    const data = await adminAPI('candidates');
    allCandidates = data.success ? data.candidates : getDemoCandidates();
  } catch {
    allCandidates = getDemoCandidates();
  }
  renderCandidates();
}

function setCandFilter(filter, btn) {
  candFilter = filter;
  document.querySelectorAll('#page-candidates .filter-tab').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  renderCandidates();
}

function renderCandidates() {
  let list = [...allCandidates];
  if (candFilter === 'pending') list = list.filter(c => c.status === 'pending');
  if (candFilter === 'king')    list = list.filter(c => c.category === 'king');
  if (candFilter === 'queen')   list = list.filter(c => c.category === 'queen');

  const container = document.getElementById('candidatesContainer');
  if (!container) return;

  if (!list.length) {
    container.innerHTML = '<div class="empty-state"><span class="empty-icon">👑</span><p>No candidates found</p></div>';
    return;
  }

  container.innerHTML = list.map(c => `
    <div class="candidate-row" id="cand-row-${c.id}">
      <div class="cand-avatar">${c.category === 'king' ? '👑' : '👸'}</div>
      <div class="cand-info">
        <div class="cand-name">${c.full_name}</div>
        <div class="cand-meta">
          ${c.class_school} &nbsp;·&nbsp;
          <span class="badge badge-${c.category}">${c.category.toUpperCase()}</span>
          &nbsp;·&nbsp;
          <span class="badge badge-${c.status}">${c.status}</span>
        </div>
        <div class="cand-bio">"${c.bio}"</div>
      </div>
      ${c.status === 'approved' ? `
        <div class="cand-votes">
          <div class="cand-vote-num">${c.vote_count}</div>
          <div class="cand-vote-lbl">votes</div>
        </div>` : ''}
      <div class="cand-actions">
        ${c.status === 'pending' ? `<button class="btn-sm btn-approve" onclick="approveCandidate(${c.id})">✓ Approve</button>` : ''}
        <button class="btn-sm btn-reject" onclick="removeCandidate(${c.id})">${c.status === 'pending' ? '✗ Reject' : 'Remove'}</button>
      </div>
    </div>
  `).join('');
}

async function approveCandidate(id) {
  try { await adminAPI('update_candidate', 'POST', { id, status: 'approved' }); } catch {}
  const c = allCandidates.find(x => x.id === id);
  if (c) c.status = 'approved';
  renderCandidates();
  adminToast('Candidate approved!', 'success');
}

async function removeCandidate(id) {
  if (!confirm('Remove this candidate?')) return;
  try { await adminAPI('update_candidate', 'POST', { id, status: 'rejected' }); } catch {}
  allCandidates = allCandidates.filter(x => x.id !== id);
  renderCandidates();
  adminToast('Candidate removed.');
}

// ============================================================
// VOTES
// ============================================================
async function loadVotes() {
  try {
    const data = await adminAPI('votes');
    if (data.success) {
      renderVoteResults(data.king, data.queen);
      renderVoterList(data.votes || []);
      return;
    }
  } catch {}

  // Demo
  const cands = getDemoCandidates().filter(c => c.status === 'approved');
  renderVoteResults(
    cands.filter(c => c.category === 'king').sort((a,b)  => b.vote_count - a.vote_count),
    cands.filter(c => c.category === 'queen').sort((a,b) => b.vote_count - a.vote_count)
  );
  renderVoterList([
    { ticket_id:'GN2026A1B2C3', king_name:'Alexander Kim', queen_name:'Sophia Tan',    voted_at:'2026-05-01 15:30' },
    { ticket_id:'GN2026D4E5F6', king_name:'Marcus Santos', queen_name:'Isabella Park', voted_at:'2026-05-02 10:00' },
  ]);
}

function renderVoteResults(kings, queens) {
  const maxK = kings.length  ? kings[0].vote_count  : 1;
  const maxQ = queens.length ? queens[0].vote_count : 1;

  const render = (list, max) => list.map((c, i) => `
    <div style="background:var(--black-mid);border:1px solid rgba(212,175,55,${i===0?'0.4':'0.08'});padding:16px;margin-bottom:10px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
        <div style="font-family:'Cinzel',serif;color:${i===0?'var(--gold)':'var(--text)'};font-size:0.95rem;">${i===0?'★ ':''}${c.full_name}</div>
        <div style="font-family:'Cinzel',serif;color:var(--gold);font-size:1.3rem;">${c.vote_count}</div>
      </div>
      <div style="font-family:'Montserrat',sans-serif;font-size:0.72rem;color:var(--text-dim);margin-bottom:8px;">${c.class_school || ''}</div>
      <div class="vote-bar-wrap"><div class="vote-bar-fill" style="width:${max > 0 ? (c.vote_count/max*100).toFixed(0) : 0}%"></div></div>
    </div>
  `).join('') || '<div class="empty-state"><p>No candidates yet</p></div>';

  const kingEl  = document.getElementById('kingResults');
  const queenEl = document.getElementById('queenResults');
  if (kingEl)  kingEl.innerHTML  = render(kings, maxK);
  if (queenEl) queenEl.innerHTML = render(queens, maxQ);
}

function renderVoterList(votes) {
  const tbody = document.getElementById('votersTbody');
  if (!tbody) return;
  tbody.innerHTML = votes.map(v => `
    <tr>
      <td class="td-id">${v.ticket_id}</td>
      <td class="td-primary">${v.king_name || '—'}</td>
      <td class="td-primary">${v.queen_name || '—'}</td>
      <td>${v.voted_at}</td>
    </tr>
  `).join('') || '<tr><td colspan="4" style="text-align:center;padding:30px;color:var(--text-dim);font-style:italic;">No votes yet</td></tr>';
}

// ============================================================
// QR SCANNER
// ============================================================
function initScanner() {
  const video  = document.getElementById('scannerVideo');
  const noVid  = document.getElementById('noCamera');
  if (!video) return;

  if (!navigator.mediaDevices?.getUserMedia) {
    if (video) video.style.display = 'none';
    if (noVid) noVid.style.display = 'block';
    return;
  }

  navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
    .then(stream => {
      video.srcObject = stream;
      video.play();
      // jsQR would be used here for real scanning
      // startQRLoop(video);
    })
    .catch(() => {
      if (video) video.style.display = 'none';
      if (noVid) noVid.style.display = 'block';
    });
}

async function manualScanTicket() {
  const input = document.getElementById('manualTicketInput');
  if (!input) return;
  const id = input.value.trim().toUpperCase();
  if (!id) return;
  input.value = '';
  await processTicketScan(id);
}

async function processTicketScan(ticketId) {
  try {
    const res  = await fetch('../public/scan_api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ticket_id: ticketId })
    });
    const data = await res.json();
    displayScanResult(data);
  } catch {
    // Demo fallback
    const demo = getDemoTickets().find(t => t.ticket_id === ticketId);
    if (!demo)            displayScanResult({ result: 'invalid',      message: 'Ticket not found.' });
    else if (demo.ticket_status === 'used')
                          displayScanResult({ result: 'already_used', message: 'Already scanned.', ticket: demo });
    else {
      demo.ticket_status = 'used';
      displayScanResult({ result: 'valid', message: 'Entry granted!', ticket: demo });
    }
  }
}

function displayScanResult(data) {
  const box    = document.getElementById('scanResultBox');
  const icon   = document.getElementById('scanIcon');
  const status = document.getElementById('scanStatus');
  const name   = document.getElementById('scanName');
  const detail = document.getElementById('scanDetail');
  if (!box) return;

  box.className = 'scan-result-box show';

  const map = {
    valid:        { cls: 'valid',   ico: '✅', txt: 'VALID — ENTRY GRANTED' },
    already_used: { cls: 'used',    ico: '🚫', txt: 'ALREADY USED' },
    invalid:      { cls: 'invalid', ico: '⚠️', txt: 'INVALID TICKET' },
  };

  const config = map[data.result] || map.invalid;
  box.classList.add(config.cls);
  if (icon)   icon.textContent   = config.ico;
  if (status) { status.textContent = config.txt; status.className = 'scan-result-status ' + config.cls; }
  if (name)   name.textContent   = data.ticket?.full_name || '';
  if (detail) detail.textContent = data.result === 'valid'
    ? (data.ticket?.class_school || '') + ' · ' + (data.ticket?.student_type || '')
    : (data.message || '');

  // Audio beep
  try {
    const ac  = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ac.createOscillator();
    osc.connect(ac.destination);
    osc.frequency.value = data.result === 'valid' ? 880 : 220;
    osc.start();
    osc.stop(ac.currentTime + 0.2);
  } catch {}

  setTimeout(() => box.classList.remove('show'), 6000);
}

// ============================================================
// EXPORT CSV
// ============================================================
function exportCSV(type) {
  let csv = '';
  let filename = '';

  if (type === 'tickets') {
    filename = 'golden-night-tickets.csv';
    csv = 'Ticket ID,Full Name,Class,Phone,Type,Payment,Entry,Amount,Date\n';
    (allTickets.length ? allTickets : getDemoTickets()).forEach(t => {
      csv += `${t.ticket_id},"${t.full_name}","${t.class_school}",${t.phone},${t.student_type},${t.payment_status},${t.ticket_status},${t.amount_paid},"${t.created_at}"\n`;
    });
  } else if (type === 'votes') {
    filename = 'golden-night-votes.csv';
    csv = 'Ticket ID,King Vote,Queen Vote,Voted At\n';
    csv += 'GN2026A1B2C3,Alexander Kim,Sophia Tan,2026-05-01 15:30\nGN2026D4E5F6,Marcus Santos,Isabella Park,2026-05-02 10:00\n';
  }

  const blob = new Blob([csv], { type: 'text/csv' });
  const url  = URL.createObjectURL(blob);
  const a    = Object.assign(document.createElement('a'), { href: url, download: filename });
  a.click();
  URL.revokeObjectURL(url);
  adminToast('Exported: ' + filename, 'success');
}

// ============================================================
// AUTO-INIT
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
  checkAdminAuth();
  initAdminNav();
  loadDashboard();
  initScanner();
});
