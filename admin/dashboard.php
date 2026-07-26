<?php
require_once "../includes/config.php";
requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard — Golden Night 2026</title>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root{--gold:#D4AF37;--gold-light:#f0d060;--gold-dim:rgba(212,175,55,0.12);--black:#0a0a0a;--card:#0f0f09;--text:#e8e0cc;--dim:#8a7d5a;--ok:#4CAF50;--err:#f44336;--warn:#FFC107;--sw:220px;}
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
    html,body{height:100%;}
    body{background:var(--black);color:var(--text);font-family:'Montserrat',sans-serif;display:flex;overflow-x:hidden;}

    /* SIDEBAR */
    .sb{width:var(--sw);background:#080806;border-right:1px solid rgba(212,175,55,0.1);display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:300;transition:transform .3s;overflow-y:auto;}
    .sb-logo{padding:22px 18px;border-bottom:1px solid rgba(212,175,55,0.1);}
    .sb-logo .star{font-size:1.7rem;color:var(--gold);display:block;margin-bottom:5px;text-shadow:0 0 14px rgba(212,175,55,.5);}
    .sb-logo h2{font-family:'Cinzel',serif;font-size:.82rem;color:var(--gold);letter-spacing:3px;margin-bottom:2px;}
    .sb-logo p{font-size:.56rem;color:var(--dim);letter-spacing:2px;text-transform:uppercase;}
    .nav-grp{padding:13px 16px 4px;font-size:.52rem;letter-spacing:3px;text-transform:uppercase;color:var(--dim);}
    .nav-btn{display:flex;align-items:center;gap:10px;padding:10px 18px;font-size:.74rem;color:var(--dim);cursor:pointer;transition:all .22s;border:none;background:none;width:100%;text-align:left;text-decoration:none;position:relative;}
    .nav-btn:hover{color:var(--text);background:rgba(212,175,55,.04);}
    .nav-btn.on{color:var(--gold);background:rgba(212,175,55,.08);}
    .nav-btn.on::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--gold);}
    .ni{font-size:.95rem;width:18px;text-align:center;flex-shrink:0;}
    .nbadge{margin-left:auto;background:var(--gold);color:var(--black);font-size:.56rem;font-weight:700;padding:1px 6px;border-radius:10px;min-width:17px;text-align:center;}
    .sb-foot{margin-top:auto;padding:14px 18px;border-top:1px solid rgba(212,175,55,.1);}
    .sb-user{font-size:.62rem;color:var(--dim);margin-bottom:9px;}
    .sb-user span{color:var(--gold);}
    .lgout{display:block;width:100%;font-size:.6rem;letter-spacing:2px;text-transform:uppercase;color:var(--dim);background:transparent;border:1px solid rgba(212,175,55,.2);padding:7px;cursor:pointer;transition:all .25s;text-align:center;}
    .lgout:hover{color:#ff8888;border-color:rgba(244,67,54,.4);}

    /* MAIN */
    .mn{margin-left:var(--sw);flex:1;display:flex;flex-direction:column;min-height:100vh;min-width:0;}
    .tbar{padding:12px 22px;border-bottom:1px solid rgba(212,175,55,.1);display:flex;align-items:center;justify-content:space-between;background:rgba(8,8,6,.98);position:sticky;top:0;z-index:100;flex-shrink:0;}
    .tbar-l{display:flex;align-items:center;gap:10px;}
    .mob-btn{display:none;background:none;border:none;color:var(--gold);font-size:1.4rem;cursor:pointer;padding:3px;}
    .ptitle{font-family:'Cinzel',serif;font-size:.92rem;color:var(--gold);letter-spacing:4px;}
    .clk{font-size:.66rem;color:var(--dim);letter-spacing:2px;}
    .ct{padding:22px;flex:1;overflow-x:hidden;}

    /* PAGES */
    .pg{display:none;}.pg.on{display:block;}

    /* STATS */
    .sg{display:grid;grid-template-columns:repeat(auto-fill,minmax(155px,1fr));gap:11px;margin-bottom:22px;}
    .sc{background:var(--card);border:1px solid rgba(212,175,55,.1);padding:18px;position:relative;overflow:hidden;transition:border-color .3s;}
    .sc:hover{border-color:rgba(212,175,55,.3);}
    .sc::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--gold),transparent);}
    .sn{font-family:'Cinzel',serif;font-size:2rem;color:var(--gold);line-height:1;margin-bottom:4px;}
    .sl{font-size:.58rem;letter-spacing:2px;text-transform:uppercase;color:var(--dim);}
    .si{position:absolute;bottom:10px;right:10px;font-size:2rem;opacity:.1;pointer-events:none;}

    /* 2-COL */
    .g2{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
    @media(max-width:860px){.g2{grid-template-columns:1fr;}}

    /* TABLES */
    .th{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;flex-wrap:wrap;gap:9px;}
    .ttl{font-family:'Cinzel',serif;font-size:.92rem;color:var(--gold);letter-spacing:3px;}
    .tw{overflow-x:auto;background:var(--card);border:1px solid rgba(212,175,55,.1);}
    table{width:100%;border-collapse:collapse;}
    thead th{font-size:.56rem;letter-spacing:3px;text-transform:uppercase;color:var(--gold);padding:11px 13px;text-align:left;border-bottom:1px solid rgba(212,175,55,.14);background:rgba(212,175,55,.04);white-space:nowrap;}
    tbody td{padding:10px 13px;font-size:.76rem;color:var(--dim);border-bottom:1px solid rgba(212,175,55,.04);white-space:nowrap;}
    tbody tr:hover td{background:rgba(212,175,55,.02);}
    tbody tr:last-child td{border-bottom:none;}
    .tm{color:var(--text)!important;font-weight:500;}
    .tid{font-family:'Courier New',monospace!important;color:var(--gold)!important;font-size:.74rem!important;letter-spacing:1px;}

    /* BADGES */
    .bx{display:inline-block;padding:2px 8px;border-radius:10px;font-size:.58rem;letter-spacing:1px;text-transform:uppercase;font-weight:600;white-space:nowrap;}
    .bp{background:rgba(255,193,7,.14);color:#FFC107;border:1px solid rgba(255,193,7,.3);}
    .bc{background:rgba(76,175,80,.14);color:#81C784;border:1px solid rgba(76,175,80,.3);}
    .bu{background:rgba(33,150,243,.14);color:#64B5F6;border:1px solid rgba(33,150,243,.3);}
    .br{background:rgba(244,67,54,.14);color:#EF9A9A;border:1px solid rgba(244,67,54,.3);}
    .ba{background:rgba(76,175,80,.14);color:#81C784;border:1px solid rgba(76,175,80,.3);}
    .bi{background:rgba(212,175,55,.1);color:var(--gold);border:1px solid rgba(212,175,55,.25);}
    .bg{background:rgba(212,175,55,.1);color:var(--gold);border:1px solid rgba(212,175,55,.25);}
    .be{background:rgba(156,39,176,.14);color:#CE93D8;border:1px solid rgba(156,39,176,.3);}
    .bki{background:rgba(212,175,55,.1);color:var(--gold);border:1px solid rgba(212,175,55,.3);}
    .bq{background:rgba(236,64,122,.1);color:#F48FB1;border:1px solid rgba(236,64,122,.3);}
    .bun{background:rgba(158,158,158,.1);color:#aaa;border:1px solid rgba(158,158,158,.2);}

    /* FILTERS */
    .fts{display:flex;gap:5px;margin-bottom:12px;flex-wrap:wrap;}
    .ft{font-size:.58rem;letter-spacing:2px;text-transform:uppercase;padding:5px 12px;background:transparent;border:1px solid rgba(212,175,55,.2);color:var(--dim);cursor:pointer;transition:all .22s;}
    .ft:hover{border-color:rgba(212,175,55,.4);color:var(--text);}
    .ft.on{border-color:var(--gold);color:var(--gold);background:rgba(212,175,55,.08);}

    /* BUTTONS */
    .bexp{font-size:.6rem;letter-spacing:2px;text-transform:uppercase;color:var(--gold);background:transparent;border:1px solid rgba(212,175,55,.3);padding:6px 12px;cursor:pointer;transition:all .25s;}
    .bexp:hover{border-color:var(--gold);background:var(--gold-dim);}
    .bact{font-size:.58rem;letter-spacing:1px;text-transform:uppercase;padding:4px 8px;border:none;cursor:pointer;transition:all .2s;margin:1px;white-space:nowrap;}
    .bok{background:rgba(76,175,80,.2);color:#81C784;border:1px solid rgba(76,175,80,.4);}
    .bok:hover{background:rgba(76,175,80,.4);}
    .bno{background:rgba(244,67,54,.14);color:#EF9A9A;border:1px solid rgba(244,67,54,.3);}
    .bno:hover{background:rgba(244,67,54,.3);}
    .bap{background:rgba(212,175,55,.14);color:var(--gold);border:1px solid rgba(212,175,55,.3);}
    .bap:hover{background:rgba(212,175,55,.3);}
    .bvw{background:rgba(33,150,243,.14);color:#90CAF9;border:1px solid rgba(33,150,243,.3);}
    .bvw:hover{background:rgba(33,150,243,.3);}

    /* SEARCH */
    .srch{background:rgba(0,0,0,.5);border:1px solid rgba(212,175,55,.2);color:var(--text);font-family:'Montserrat',sans-serif;font-size:.74rem;padding:7px 13px;outline:none;transition:border .25s;width:190px;}
    .srch:focus{border-color:var(--gold);}
    .srch::placeholder{color:var(--dim);}

    /* REVENUE */
    .rv{background:var(--card);border:1px solid rgba(212,175,55,.1);padding:16px;margin-bottom:9px;}
    .rl{font-size:.58rem;letter-spacing:2px;text-transform:uppercase;color:var(--dim);margin-bottom:5px;}
    .rvl{font-family:'Cinzel',serif;font-size:1.3rem;color:var(--gold);}
    .rv.hi{border-color:rgba(212,175,55,.35);}

    /* SCANNER */
    .scw{max-width:440px;margin:0 auto;}
    .scvbox{background:var(--card);border:2px solid rgba(212,175,55,.2);padding:14px;margin-bottom:14px;text-align:center;}
    #scannerVideo{width:100%;max-width:370px;border:2px solid var(--gold);display:block;margin:0 auto;background:#111;min-height:180px;}
    .nocam{display:none;padding:36px 18px;color:var(--dim);font-style:italic;font-size:.88rem;text-align:center;line-height:1.8;}
    .sres{padding:18px;border:2px solid transparent;text-align:center;margin-bottom:14px;display:none;}
    .sres.on{display:block;}
    .sres.vd{border-color:var(--ok);background:rgba(76,175,80,.07);}
    .sres.ud{border-color:var(--err);background:rgba(244,67,54,.07);}
    .sres.iv{border-color:var(--warn);background:rgba(255,193,7,.05);}
    .semi{font-size:2.6rem;display:block;margin-bottom:7px;}
    .sst{font-family:'Cinzel',serif;font-size:1.1rem;letter-spacing:3px;margin-bottom:5px;}
    .sst.vd{color:var(--ok);}.sst.ud{color:var(--err);}.sst.iv{color:var(--warn);}
    .snm{color:var(--gold);font-size:.95rem;margin:4px 0;}
    .sdt{font-size:.75rem;color:var(--dim);}
    .mrow{display:flex;gap:9px;margin-top:11px;}
    .min{flex:1;background:rgba(0,0,0,.6);border:1px solid rgba(212,175,55,.25);color:var(--text);font-family:'Montserrat',sans-serif;font-size:.83rem;padding:10px 13px;outline:none;letter-spacing:2px;text-transform:uppercase;transition:border .25s;}
    .min:focus{border-color:var(--gold);}
    .min::placeholder{color:var(--dim);text-transform:none;letter-spacing:0;}
    .mbtn{font-family:'Cinzel',serif;font-size:.72rem;letter-spacing:2px;color:var(--black);background:var(--gold);border:none;padding:10px 16px;cursor:pointer;transition:all .25s;white-space:nowrap;}
    .mbtn:hover{background:var(--gold-light);}

    /* VOTE BARS */
    .vbw{background:rgba(0,0,0,.35);height:5px;border-radius:3px;overflow:hidden;margin-top:5px;}
    .vbf{height:100%;background:linear-gradient(90deg,var(--gold),var(--gold-light));transition:width 1.2s ease;}
    .rcard{background:var(--card);border:1px solid rgba(212,175,55,.09);padding:13px;margin-bottom:9px;transition:border-color .3s;}
    .rcard.ld{border-color:rgba(212,175,55,.42);}
    .rtop{display:flex;justify-content:space-between;align-items:center;margin-bottom:3px;}
    .rname{font-family:'Cinzel',serif;font-size:.87rem;}
    .rname.ld{color:var(--gold);}
    .rvotes{font-family:'Cinzel',serif;font-size:1.1rem;color:var(--gold);}
    .rcls{font-size:.7rem;color:var(--dim);margin-bottom:5px;}

    /* CAND ROWS */
    .crow{display:flex;align-items:center;gap:12px;background:var(--card);border:1px solid rgba(212,175,55,.09);padding:13px;margin-bottom:7px;transition:border-color .3s;}
    .crow:hover{border-color:rgba(212,175,55,.28);}
    .cav{width:48px;height:48px;border:1px solid rgba(212,175,55,.3);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;background:#111;}
    .cav img{width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;}
    .ci{flex:1;min-width:0;}
    .cn{font-family:'Cinzel',serif;font-size:.87rem;color:var(--gold);letter-spacing:1px;}
    .cm{font-size:.68rem;color:var(--dim);margin-top:2px;display:flex;gap:5px;align-items:center;flex-wrap:wrap;}
    .cb{font-size:.77rem;color:var(--dim);font-style:italic;margin-top:3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
    .cvb{text-align:center;padding:0 13px;border-left:1px solid rgba(212,175,55,.14);flex-shrink:0;}
    .cvn{font-family:'Cinzel',serif;font-size:1.3rem;color:var(--gold);line-height:1;}
    .cvl{font-size:.5rem;letter-spacing:2px;text-transform:uppercase;color:var(--dim);}
    .cacts{display:flex;flex-direction:column;gap:4px;flex-shrink:0;}

    /* EMPTY */
    .emp{text-align:center;padding:46px 18px;color:var(--dim);}
    .ei{font-size:2.6rem;opacity:.32;display:block;margin-bottom:9px;}

    /* TOAST */
    .toast{position:fixed;bottom:22px;right:22px;background:var(--card);border:1px solid var(--gold);color:var(--gold);padding:12px 18px;font-size:.7rem;letter-spacing:2px;z-index:9999;transform:translateX(130%);transition:transform .4s ease;box-shadow:0 0 22px rgba(212,175,55,.14);max-width:270px;}
    .toast.on{transform:translateX(0);}
    .toast.tok{border-color:var(--ok);color:#81C784;}
    .toast.ter{border-color:var(--err);color:#ff8888;}

    /* MEDIA MODAL */
    .mm{position:fixed;inset:0;background:rgba(0,0,0,.84);z-index:10000;display:none;align-items:center;justify-content:center;padding:24px;}
    .mm.on{display:flex;}
    .mmc{width:min(980px,100%);max-height:90vh;background:#0c0c07;border:1px solid rgba(212,175,55,.28);display:flex;flex-direction:column;overflow:hidden;}
    .mmh{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid rgba(212,175,55,.16);}
    .mmt{font-size:.66rem;letter-spacing:2px;text-transform:uppercase;color:var(--gold);}
    .mmx{background:transparent;border:1px solid rgba(212,175,55,.25);color:var(--gold);padding:6px 10px;cursor:pointer;font-size:.62rem;letter-spacing:1px;}
    .mmb{padding:12px;overflow:auto;}
    .mmi{max-width:100%;max-height:74vh;display:block;margin:0 auto;border:1px solid rgba(212,175,55,.18);}
    .mmf{width:100%;height:74vh;border:1px solid rgba(212,175,55,.18);background:#111;}
    .mml{display:flex;gap:8px;justify-content:flex-end;padding:8px 12px;border-top:1px solid rgba(212,175,55,.1);}
    .mml a{font-size:.62rem;letter-spacing:2px;text-transform:uppercase;color:var(--gold);text-decoration:none;border:1px solid rgba(212,175,55,.28);padding:7px 10px;}

    @media(max-width:860px){
      .sb{transform:translateX(-100%);}
      .sb.open{transform:translateX(0);box-shadow:6px 0 40px rgba(0,0,0,.9);}
      .mn{margin-left:0;}
      .mob-btn{display:block;}
      .sg{grid-template-columns:1fr 1fr;}
    }
    @media(max-width:480px){.sg{grid-template-columns:1fr;}}
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sb" id="sb">
  <div class="sb-logo">
    <span class="star">✦</span>
    <h2>Golden Night</h2>
    <p>Admin Panel</p>
  </div>
  <nav>
    <div class="nav-grp">Overview</div>
    <button class="nav-btn on" data-p="dashboard" onclick="go('dashboard')"><span class="ni">📊</span>Dashboard</button>
    <div class="nav-grp">Tickets</div>
    <button class="nav-btn" data-p="tickets" onclick="go('tickets')"><span class="ni">🎟️</span>All Tickets<span class="nbadge" id="bp">0</span></button>
    <button class="nav-btn" data-p="scanner" onclick="go('scanner')"><span class="ni">📷</span>QR Scanner</button>
    <div class="nav-grp">Royalty</div>
    <button class="nav-btn" data-p="candidates" onclick="go('candidates')"><span class="ni">👑</span>Candidates<span class="nbadge" id="bc">0</span></button>
    <button class="nav-btn" data-p="votes" onclick="go('votes')"><span class="ni">🗳️</span>Vote Results</button>
    <div class="nav-grp">Payments</div>
    <a class="nav-btn" href="payment-management.php"><span class="ni">💳</span>Payment Management</a>
    <div class="nav-grp">More</div>
    <a class="nav-btn" href="../index.html" target="_blank"><span class="ni">🌐</span>Public Site</a>
  </nav>
  <div class="sb-foot">
    <div class="sb-user">Logged in as <span id="adminName">Admin</span></div>
    <button class="lgout" onclick="doLogout()">Logout ↗</button>
  </div>
</aside>

<!-- MAIN -->
<div class="mn">
  <div class="tbar">
    <div class="tbar-l">
      <button class="mob-btn" onclick="document.getElementById('sb').classList.toggle('open')">☰</button>
      <div class="ptitle" id="ptitle">Dashboard</div>
    </div>
    <div class="clk" id="clk">—</div>
  </div>

  <div class="ct">

    <!-- DASHBOARD -->
    <div class="pg on" id="pg-dashboard">
      <div class="sg">
        <div class="sc"><div class="sn" id="s0">0</div><div class="sl">Total Tickets</div><span class="si">🎟️</span></div>
        <div class="sc"><div class="sn" id="s1">0</div><div class="sl">Confirmed</div><span class="si">✅</span></div>
        <div class="sc"><div class="sn" id="s2">0</div><div class="sl">Entry Used</div><span class="si">🚪</span></div>
        <div class="sc"><div class="sn" id="s3">Rwf 0</div><div class="sl">Revenue</div><span class="si">💰</span></div>
        <div class="sc"><div class="sn" id="s4">0</div><div class="sl">Total Votes</div><span class="si">🗳️</span></div>
        <div class="sc"><div class="sn" id="s5">0</div><div class="sl">Candidates</div><span class="si">👑</span></div>
      </div>
      <div class="g2">
        <div>
          <div class="th"><div class="ttl">Recent Tickets</div></div>
          <div class="tw"><table>
            <thead><tr><th>Ticket ID</th><th>Name</th><th>Type</th><th>Status</th></tr></thead>
            <tbody id="rtbody"><tr><td colspan="4" style="text-align:center;padding:30px;color:var(--dim);font-style:italic;">No tickets yet</td></tr></tbody>
          </table></div>
        </div>
        <div>
          <div class="th"><div class="ttl">Revenue</div></div>
          <div class="rv"><div class="rl">Confirmed Tickets</div><div class="rvl" id="ri">0</div></div>
          <div class="rv"><div class="rl">Revenue (confirmed tickets)</div><div class="rvl" id="re">Rwf 0</div></div>
          <div class="rv hi"><div class="rl">Grand Total</div><div class="rvl" id="rt">Rwf 0</div></div>
        </div>
      </div>
    </div>

    <!-- TICKETS -->
    <div class="pg" id="pg-tickets">
      <div class="th">
        <div class="ttl">All Tickets</div>
        <div style="display:flex;gap:9px;flex-wrap:wrap;align-items:center;">
          <input type="text" class="srch" id="tsrch" placeholder="Search name / ID..." oninput="renderTickets()"/>
          <button class="bexp" onclick="exportCSV('tickets')">⬇ Export CSV</button>
        </div>
      </div>
      <div class="fts">
        <button class="ft on" onclick="setTF('all',this)">All</button>
        <button class="ft"    onclick="setTF('pending',this)">Pending</button>
        <button class="ft"    onclick="setTF('confirmed',this)">Confirmed</button>
        <button class="ft"    onclick="setTF('used',this)">Used</button>
      </div>
      <div class="tw"><table>
        <thead><tr><th>Ticket ID</th><th>Name</th><th>Index</th><th>Phone</th><th>Type</th><th>Payment</th><th>Entry</th><th>Proof</th><th>Amount</th><th>Actions</th></tr></thead>
        <tbody id="ttbody"><tr><td colspan="9" style="text-align:center;padding:40px;color:var(--dim);font-style:italic;">No tickets yet</td></tr></tbody>
      </table></div>
    </div>

    <!-- SCANNER -->
    <div class="pg" id="pg-scanner">
      <div class="scw">
        <div class="ttl" style="text-align:center;margin-bottom:18px;">📷 QR Entry Scanner</div>
        <div class="scvbox">
          <video id="scannerVideo" autoplay muted playsinline></video>
          <div class="nocam" id="nocam">📵 Camera unavailable or permission denied.<br/>Use manual entry below.</div>
          <div style="margin-top:8px;font-size:.65rem;color:var(--dim);letter-spacing:1px;">Point camera at QR code</div>
        </div>
        <div class="sres" id="sres">
          <span class="semi" id="semi">✅</span>
          <div class="sst" id="sst">VALID</div>
          <div class="snm" id="snm"></div>
          <div class="sdt" id="sdt"></div>
        </div>
        <div class="ttl" style="font-size:.8rem;margin-bottom:9px;">Manual Entry</div>
        <div class="mrow">
          <input type="text" class="min" id="mid" placeholder="Type Ticket ID here..." maxlength="20" oninput="this.value=this.value.toUpperCase()" onkeypress="if(event.key==='Enter')doScan()"/>
          <button class="mbtn" onclick="doScan()">Scan ▶</button>
        </div>
      </div>
    </div>

    <!-- CANDIDATES -->
    <div class="pg" id="pg-candidates">
      <div class="th"><div class="ttl">Candidates</div></div>
      <div class="fts">
        <button class="ft on" onclick="setCF('all',this)">All</button>
        <button class="ft"    onclick="setCF('pending',this)">Pending</button>
        <button class="ft"    onclick="setCF('king',this)">Kings</button>
        <button class="ft"    onclick="setCF('queen',this)">Queens</button>
      </div>
      <div id="candWrap"><div class="emp"><span class="ei">👑</span><p style="font-style:italic;">No candidates submitted yet</p></div></div>
    </div>

    <!-- VOTES -->
    <div class="pg" id="pg-votes">
      <div class="th">
        <div class="ttl">Vote Results</div>
        <button class="bexp" onclick="exportCSV('votes')">⬇ Export CSV</button>
      </div>
      <div class="g2" style="margin-bottom:24px;">
        <div>
          <div style="font-family:'Cinzel',serif;font-size:.8rem;color:var(--gold);letter-spacing:3px;margin-bottom:12px;padding-bottom:9px;border-bottom:1px solid rgba(212,175,55,.1);">👑 KING STANDINGS</div>
          <div id="kresults"><div class="emp"><span class="ei">👑</span><p style="font-style:italic">No king candidates yet</p></div></div>
        </div>
        <div>
          <div style="font-family:'Cinzel',serif;font-size:.8rem;color:var(--gold);letter-spacing:3px;margin-bottom:12px;padding-bottom:9px;border-bottom:1px solid rgba(212,175,55,.1);">👸 QUEEN STANDINGS</div>
          <div id="qresults"><div class="emp"><span class="ei">👸</span><p style="font-style:italic">No queen candidates yet</p></div></div>
        </div>
      </div>
      <div class="th"><div class="ttl">Voter List</div></div>
      <div class="tw"><table>
        <thead><tr><th>Ticket ID</th><th>King Vote</th><th>Queen Vote</th><th>Time</th></tr></thead>
        <tbody id="vtbody"><tr><td colspan="4" style="text-align:center;padding:30px;color:var(--dim);font-style:italic;">No votes cast yet</td></tr></tbody>
      </table></div>
    </div>

  </div><!-- /ct -->
</div><!-- /mn -->

<div class="toast" id="toast"></div>

<div class="mm" id="mm" onclick="if(event.target.id==='mm')closeMedia()">
  <div class="mmc">
    <div class="mmh">
      <div class="mmt" id="mmt">Media Preview</div>
      <button class="mmx" onclick="closeMedia()">Close ✕</button>
    </div>
    <div class="mmb" id="mmb"></div>
    <div class="mml"><a id="mmd" href="#" target="_blank" rel="noopener">Open Original</a></div>
  </div>
</div>

<script>
// ---- STATE ----
let tickets = [];
let cands = [];
let votes = [];
let tFilter = 'all';
let cFilter = 'all';

// No demo data — all data comes from the real database
const DEMO_T = [];
const DEMO_C = [];

// ---- INIT ----
document.addEventListener('DOMContentLoaded',()=>{
  document.getElementById('adminName').textContent=<?php echo json_encode($_SESSION['admin_name'] ?? 'Admin', JSON_UNESCAPED_UNICODE); ?>;
  setInterval(()=>{document.getElementById('clk').textContent=new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit',second:'2-digit'});},1000);
  document.getElementById('clk').textContent=new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'});
  loadDash();
  initCam();
});

// ---- NAV ----
const titles={dashboard:'Dashboard',tickets:'Ticket Management',scanner:'QR Entry Scanner',candidates:'Candidate Management',votes:'Vote Results'};
function go(p){
  document.querySelectorAll('.pg').forEach(x=>x.classList.remove('on'));
  document.querySelectorAll('.nav-btn').forEach(x=>x.classList.remove('on'));
  document.getElementById('pg-'+p)?.classList.add('on');
  document.querySelector('[data-p="'+p+'"]')?.classList.add('on');
  document.getElementById('ptitle').textContent=titles[p]||p;
  if(p==='tickets')    loadTickets();
  if(p==='candidates') loadCandidates();
  if(p==='votes')      loadVotes();
  if(window.innerWidth<=860) document.getElementById('sb').classList.remove('open');
}
function doLogout(){
  fetch('auth.php?action=logout').catch(() => {});
  window.location.href='login.php';
}

// ---- TOAST ----
function toast(msg,t=''){
  const el=document.getElementById('toast');
  el.textContent='✦ '+msg;
  el.className='toast'+(t?' t'+t:'');
  el.classList.add('on');
  setTimeout(()=>el.classList.remove('on'),3200);
}

// ---- API ----
async function api(action,method='GET',body=null){
  try{
    const r=await fetch(`../public/admin_api.php?action=${action}`,{
      method,
      headers:method==='POST'?{'Content-Type':'application/json'}:{},
      body:body?JSON.stringify(body):null
    });
    if (r.status === 401) {
      redirectToLogin();
      return { success: false };
    }
    const data = await r.json();
    if (!data.success && data.message && /unauthorized/i.test(data.message)) {
      redirectToLogin();
      return { success: false };
    }
    return data;
  }catch{return{success:false};}
}

function redirectToLogin(){
  toast('Session expired. Redirecting to login...','er');
  setTimeout(()=>window.location.href='login.php',900);
}

// ---- DASHBOARD ----
async function loadDash(){
  tickets=[];cands=[];
  try{const d=await api('tickets');   if(d.success&&d.tickets)tickets=d.tickets;}catch{}
  try{const d=await api('candidates');if(d.success&&d.candidates)cands=d.candidates;}catch{}

  const conf=tickets.filter(t=>t.payment_status==='confirmed');
  const used=tickets.filter(t=>t.ticket_status==='used');
  const pend=tickets.filter(t=>t.payment_status==='pending');
  let voteCount=0;
  try{const vd=await api('votes');if(vd.success&&vd.votes)voteCount=vd.votes.length;}catch{}
  const pc  =cands.filter(c=>c.status==='pending');
  const rev =conf.reduce((s,t)=>s+Number(t.amount_paid),0);
  const ri  =conf.length;
  const re  =rev;

  set('s0',tickets.length);set('s1',conf.length);set('s2',used.length);
  set('s3','Rwf '+rev.toLocaleString());set('s4',voteCount);set('s5',cands.filter(c=>c.status==='approved').length);
  set('bp',pend.length);set('bc',pc.length);
  set('ri',ri.toLocaleString());set('re','Rwf '+re.toLocaleString());set('rt','Rwf '+rev.toLocaleString());

  document.getElementById('rtbody').innerHTML=tickets.slice(0,5).map(t=>`
    <tr>
      <td class="tid">${t.ticket_id}</td>
      <td class="tm">${t.full_name}</td>
      <td><span class="bx b${t.student_type[0]}">${t.student_type}</span></td>
      <td><span class="bx b${t.payment_status[0]}">${t.payment_status}</span></td>
    </tr>`).join('')||'<tr><td colspan="4" style="text-align:center;padding:28px;color:var(--dim);font-style:italic;">No tickets yet</td></tr>';
}

function set(id,val){const e=document.getElementById(id);if(e)e.textContent=val;}

// ---- TICKETS ----
async function loadTickets(){
  try{const d=await api('tickets');tickets=d.success?d.tickets:[];}catch{tickets=[];}
  renderTickets();
}
function setTF(f,btn){tFilter=f;document.querySelectorAll('#pg-tickets .ft').forEach(b=>b.classList.remove('on'));btn?.classList.add('on');renderTickets();}
function renderTickets(){
  const q=(document.getElementById('tsrch')?.value||'').toLowerCase();
  let list=tickets.filter(t=>{
    if(tFilter==='pending')  return t.payment_status==='pending';
    if(tFilter==='confirmed')return t.payment_status==='confirmed';
    if(tFilter==='used')     return t.ticket_status==='used';
    return true;
  }).filter(t=>!q||t.full_name.toLowerCase().includes(q)||t.ticket_id.toLowerCase().includes(q));
  document.getElementById('ttbody').innerHTML=list.length?list.map(t=>`
    <tr>
      <td class="tid">${t.ticket_id}</td>
      <td class="tm">${t.full_name}</td>
      <td>${t.class_school}</td>
      <td>${t.phone}</td>
      <td><span class="bx b${t.student_type[0]}">${t.student_type}</span></td>
      <td><span class="bx b${t.payment_status[0]}">${t.payment_status}</span></td>
      <td><span class="bx b${t.ticket_status==='used'?'u':'p'}">${t.ticket_status}</span></td>
      <td>${t.payment_proof?`<button class="bact bvw" onclick="showMedia('${safeJs(mediaHref(t.payment_proof))}','Payment proof: ${safeJs(t.ticket_id)}')">View</button>`:'<span style="color:var(--dim);font-size:.7rem;">—</span>'}</td>
      <td>Rwf ${Number(t.amount_paid).toLocaleString()}</td>
      <td>${t.payment_status==='pending'?`<button class="bact bok" onclick="confP('${t.ticket_id}')">✓</button><button class="bact bno" onclick="rejP('${t.ticket_id}')">✗</button>`:'<span style="color:var(--dim);font-size:.7rem;">—</span>'}</td>
    </tr>`).join(''):'<tr><td colspan="10" style="text-align:center;padding:36px;color:var(--dim);font-style:italic;">No tickets found</td></tr>';
}
async function confP(id){if(!confirm('Confirm MoMo payment for '+id+'?'))return;try{await api('confirm_payment','POST',{ticket_id:id});}catch{}const t=tickets.find(x=>x.ticket_id===id);if(t)t.payment_status='confirmed';renderTickets();toast('Payment confirmed: '+id,'ok');}
async function rejP(id){if(!confirm('Reject payment for '+id+'?'))return;try{await api('reject_payment','POST',{ticket_id:id});}catch{}const t=tickets.find(x=>x.ticket_id===id);if(t)t.payment_status='rejected';renderTickets();toast('Payment rejected','er');}

// ---- CANDIDATES ----
async function loadCandidates(){
  try{const d=await api('candidates');cands=d.success?d.candidates:[];}catch{cands=[];}
  renderCands();
}
function setCF(f,btn){cFilter=f;document.querySelectorAll('#pg-candidates .ft').forEach(b=>b.classList.remove('on'));btn?.classList.add('on');renderCands();}
function renderCands(){
  let list=cands.filter(c=>{if(cFilter==='pending')return c.status==='pending';if(cFilter==='king')return c.category==='king';if(cFilter==='queen')return c.category==='queen';return true;});
  document.getElementById('candWrap').innerHTML=list.length?list.map(c=>`
    <div class="crow">
      <div class="cav">${c.photo?`<img src="${mediaHref(c.photo)}" alt="${safeJs(c.full_name)}"/>`:(c.category==='king'?'👑':'👸')}</div>
      <div class="ci">
        <div class="cn">${c.full_name}</div>
        <div class="cm"><span>${c.class_school}</span><span class="bx b${c.category==='king'?'ki':'q'}">${c.category}</span><span class="bx b${c.status[0]}">${c.status}</span></div>
        <div class="cb">"${c.bio}"</div>
      </div>
      ${c.status==='approved'?`<div class="cvb"><div class="cvn">${c.vote_count}</div><div class="cvl">votes</div></div>`:''}
      <div class="cacts">${c.photo?`<button class="bact bvw" onclick="showMedia('${safeJs(mediaHref(c.photo))}','Candidate photo: ${safeJs(c.full_name)}')">Photo</button>`:''}${c.status==='pending'?`<button class="bact bap" onclick="appC(${c.id})">✓ Approve</button><button class="bact bno" onclick="remC(${c.id})">✗ Reject</button>`:`<button class="bact bno" onclick="remC(${c.id})">Remove</button>`}</div>
    </div>`).join(''):'<div class="emp"><span class="ei">👑</span><p style="font-style:italic">No candidates found</p></div>';
}
async function appC(id){try{await api('update_candidate','POST',{id,status:'approved'});}catch{}const c=cands.find(x=>x.id===id);if(c)c.status='approved';renderCands();toast('Candidate approved!','ok');}
async function remC(id){if(!confirm('Remove candidate?'))return;try{await api('update_candidate','POST',{id,status:'rejected'});}catch{}cands=cands.filter(x=>x.id!==id);renderCands();toast('Candidate removed.');}

// ---- VOTES ----
async function loadVotes(){
  let k=[],q=[],v=[];
  try{const d=await api('votes');if(d.success){k=d.king;q=d.queen;v=d.votes||[];}}catch{}
  if(!k.length&&!q.length){k=[];q=[];v=[];}
  renderStand('kresults',k,Math.max(...k.map(c=>c.vote_count),1));
  renderStand('qresults',q,Math.max(...q.map(c=>c.vote_count),1));
  document.getElementById('vtbody').innerHTML=v.length?v.map(x=>`<tr><td class="tid">${x.ticket_id}</td><td class="tm">${x.king_name||'—'}</td><td class="tm">${x.queen_name||'—'}</td><td>${x.voted_at}</td></tr>`).join(''):'<tr><td colspan="4" style="text-align:center;padding:28px;color:var(--dim);font-style:italic;">No votes yet</td></tr>';
}
function renderStand(id,list,max){
  document.getElementById(id).innerHTML=list.length?list.map((c,i)=>`
    <div class="rcard ${i===0?'ld':''}">
      <div class="rtop"><div class="rname ${i===0?'ld':''}">${i===0?'★ ':''}${c.full_name}</div><div class="rvotes">${c.vote_count}</div></div>
      <div class="rcls">${c.class_school||''}</div>
      <div class="vbw"><div class="vbf" style="width:${max>0?Math.round(c.vote_count/max*100):0}%"></div></div>
    </div>`).join(''):'<div class="emp"><p style="font-style:italic">No candidates yet</p></div>';
}

// ---- SCANNER ----
function initCam(){
  const v=document.getElementById('scannerVideo'),nc=document.getElementById('nocam');
  if(!navigator.mediaDevices?.getUserMedia){v.style.display='none';nc.style.display='block';return;}
  navigator.mediaDevices.getUserMedia({video:{facingMode:'environment'}})
    .then(s=>{v.srcObject=s;v.play();})
    .catch(()=>{v.style.display='none';nc.style.display='block';});
}
async function doScan(){
  const inp=document.getElementById('mid');
  const id=inp.value.trim().toUpperCase();
  if(!id){toast('Enter a Ticket ID first','er');return;}
  inp.value='';
  try{
    const r=await fetch('../public/scan_api.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({ticket_id:id})});
    if (r.status === 401) {
      redirectToLogin();
      return;
    }
    showScan(await r.json());
  }catch{
    const d=tickets.find(t=>t.ticket_id===id);
    if(!d) showScan({result:'invalid',message:'Ticket not found.'});
    else if(d.ticket_status==='used') showScan({result:'already_used',message:'Already scanned.',ticket:d});
    else{d.ticket_status='used';showScan({result:'valid',message:'Entry granted!',ticket:d});}
  }
}
function showScan(data){
  const map={valid:{cls:'vd',ico:'✅',txt:'VALID — ENTRY GRANTED'},already_used:{cls:'ud',ico:'🚫',txt:'ALREADY USED'},invalid:{cls:'iv',ico:'⚠️',txt:'INVALID TICKET'}};
  const cfg=map[data.result]||map.invalid;
  const box=document.getElementById('sres');
  box.className='sres on '+cfg.cls;
  document.getElementById('semi').textContent=cfg.ico;
  const st=document.getElementById('sst');st.textContent=cfg.txt;st.className='sst '+cfg.cls;
  document.getElementById('snm').textContent=data.ticket?.full_name||'';
  document.getElementById('sdt').textContent=data.result==='valid'?(data.ticket?.class_school||'')+(data.ticket?' · ':'')+( data.ticket?.student_type||''):(data.message||'');
  try{const ac=new(window.AudioContext||window.webkitAudioContext)();const o=ac.createOscillator();o.connect(ac.destination);o.frequency.value=data.result==='valid'?880:220;o.start();o.stop(ac.currentTime+0.2);}catch{}
  setTimeout(()=>box.classList.remove('on'),7000);
}

// ---- EXPORT ----
function exportCSV(t){
  let csv='',fn='';
  if(t==='tickets'){
    fn='golden-night-tickets.csv';
    csv='Ticket ID,Name,Class,Phone,Type,Payment,Entry,Amount\n';
    tickets.forEach(x=>{csv+=`${x.ticket_id},"${x.full_name}","${x.class_school}",${x.phone},${x.student_type},${x.payment_status},${x.ticket_status},${x.amount_paid}\n`;});
  } else {
    fn='golden-night-votes.csv';
    csv='Ticket ID,King Vote,Queen Vote,Time\n';
    votes.forEach(x=>{csv+=`${x.ticket_id},"${x.king_name||''}","${x.queen_name||''}",${x.voted_at}\n`;});
  }
  const a=document.createElement('a');a.href=URL.createObjectURL(new Blob([csv],{type:'text/csv'}));a.download=fn;a.click();
  toast('Exported: '+fn,'ok');
}

function mediaHref(path){
  if(!path) return '';
  if(/^https?:\/\//i.test(path)) return path;
  return '../'+String(path).replace(/^\/+/, '');
}

function safeJs(v){
  return String(v||'').replace(/'/g, '&#39;');
}

function showMedia(url,title){
  if(!url){toast('No file attached','er');return;}
  const lower=url.toLowerCase();
  const isPdf=lower.endsWith('.pdf');
  document.getElementById('mmt').textContent=title||'Media Preview';
  document.getElementById('mmb').innerHTML=isPdf
    ? `<iframe class="mmf" src="${url}"></iframe>`
    : `<img class="mmi" src="${url}" alt="preview"/>`;
  document.getElementById('mmd').href=url;
  document.getElementById('mm').classList.add('on');
}

function closeMedia(){
  document.getElementById('mm').classList.remove('on');
  document.getElementById('mmb').innerHTML='';
}
</script>
</body>
</html>
