<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Audition — Golden Night 2026</title>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;900&family=Cormorant+Garamond:ital,wght@0,300;0,400;1,400&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet"/>
  <style>
    :root{--gold:#D4AF37;--gold-light:#f0d060;--gold-dim:rgba(212,175,55,0.12);--black:#0a0a0a;--black-soft:#111108;--text:#e8e0cc;--text-dim:#8a7d5a;--error:#ff4444;}
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
    body{background:var(--black);color:var(--text);font-family:'Cormorant Garamond',serif;min-height:100vh;}
    .topbar{padding:20px 40px;display:flex;align-items:center;border-bottom:1px solid rgba(212,175,55,0.1);}
    .back-btn{font-family:'Montserrat',sans-serif;font-size:0.7rem;letter-spacing:3px;text-transform:uppercase;color:var(--text-dim);text-decoration:none;transition:color 0.3s;}
    .back-btn:hover{color:var(--gold);}
    .topbar-title{font-family:'Cinzel',serif;font-size:1rem;letter-spacing:4px;color:var(--gold);margin:0 auto;}

    .page-container{max-width:800px;margin:0 auto;padding:60px 24px;}
    .page-header{text-align:center;margin-bottom:60px;}
    .page-header h1{font-family:'Cinzel',serif;font-size:clamp(2rem,5vw,3.5rem);color:var(--gold);letter-spacing:6px;margin-bottom:16px;}
    .page-header p{font-style:italic;color:var(--text-dim);font-size:1.1rem;max-width:500px;margin:0 auto;}

    .form-card{background:var(--black-soft);border:1px solid rgba(212,175,55,0.15);padding:50px;position:relative;overflow:hidden;}
    .form-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent);}

    .category-select{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:32px;}
    .cat-btn{
      padding:24px;border:2px solid rgba(212,175,55,0.15);background:transparent;
      cursor:pointer;text-align:center;transition:all 0.3s;position:relative;overflow:hidden;
    }
    .cat-btn:hover{border-color:rgba(212,175,55,0.4);}
    .cat-btn.selected{border-color:var(--gold);background:rgba(212,175,55,0.06);}
    .cat-btn .icon{font-size:2.5rem;display:block;margin-bottom:10px;}
    .cat-btn .label{font-family:'Cinzel',serif;font-size:0.9rem;color:var(--gold);letter-spacing:3px;}
    .cat-btn .desc{font-size:0.8rem;color:var(--text-dim);margin-top:4px;}

    .form-group{margin-bottom:24px;}
    .form-label{display:block;font-family:'Montserrat',sans-serif;font-size:0.65rem;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:8px;}
    .form-input{width:100%;background:rgba(0,0,0,0.5);border:1px solid rgba(212,175,55,0.2);color:var(--text);font-family:'Cormorant Garamond',serif;font-size:1rem;padding:14px 16px;transition:all 0.3s;outline:none;}
    .form-input:focus{border-color:var(--gold);box-shadow:0 0 0 2px var(--gold-dim);}
    .form-input::placeholder{color:var(--text-dim);}
    textarea.form-input{resize:vertical;min-height:120px;}

    .photo-upload{border:2px dashed rgba(212,175,55,0.3);padding:40px;text-align:center;cursor:pointer;transition:all 0.3s;position:relative;}
    .photo-upload:hover{border-color:var(--gold);background:var(--gold-dim);}
    .photo-upload input{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;}
    .photo-preview{width:120px;height:120px;object-fit:cover;border:2px solid var(--gold);border-radius:50%;display:none;margin:0 auto 12px;}
    .photo-preview.show{display:block;}
    .upload-text{font-size:1rem;color:var(--text-dim);}
    .upload-hint{font-family:'Montserrat',sans-serif;font-size:0.65rem;color:var(--text-dim);letter-spacing:2px;margin-top:8px;}

    .rules-box{background:rgba(212,175,55,0.04);border:1px solid rgba(212,175,55,0.15);padding:24px;margin:28px 0;}
    .rules-box h3{font-family:'Cinzel',serif;font-size:0.9rem;color:var(--gold);letter-spacing:2px;margin-bottom:12px;}
    .rules-box ul{list-style:none;}
    .rules-box li{font-size:0.9rem;color:var(--text-dim);padding:4px 0;padding-left:16px;position:relative;}
    .rules-box li::before{content:'✦';position:absolute;left:0;color:var(--gold);font-size:0.7rem;}

    .submit-btn{width:100%;font-family:'Cinzel',serif;font-size:0.9rem;letter-spacing:4px;text-transform:uppercase;color:var(--black);background:linear-gradient(135deg,#D4AF37,#f0d060,#D4AF37);background-size:200%;border:none;padding:18px;cursor:pointer;transition:all 0.4s;}
    .submit-btn:hover{background-position:100%;box-shadow:0 0 40px rgba(212,175,55,0.4);}
    .submit-btn:disabled{opacity:0.5;cursor:not-allowed;}

    .error-msg{color:var(--error);font-size:0.85rem;margin-top:8px;font-family:'Montserrat',sans-serif;display:none;}
    .error-msg.show{display:block;}

    .success-msg-box{text-align:center;padding:60px 20px;display:none;}
    .success-msg-box.show{display:block;}
    .success-msg-box .emoji{font-size:5rem;margin-bottom:20px;display:block;}
    .success-msg-box h2{font-family:'Cinzel',serif;font-size:2.5rem;color:var(--gold);letter-spacing:4px;margin-bottom:16px;}
    .success-msg-box p{color:var(--text-dim);font-size:1.05rem;line-height:1.8;max-width:400px;margin:0 auto 32px;}
    @media(max-width:600px){.form-card{padding:32px 20px;}.category-select{grid-template-columns:1fr;}}
  </style>
</head>
<body>

<div class="topbar">
  <a href="../index.html" class="back-btn">← Back</a>
  <div class="topbar-title">✦ AUDITION REGISTRATION</div>
</div>

<div class="page-container">
  <div class="page-header">
    <h1>Audition for Royalty</h1>
    <p>Think you have what it takes to be crowned Prom King or Queen? Register your candidacy here.</p>
  </div>

  <div class="form-card" id="formCard">
    <div id="formErrors" style="display:none;color:#ff4444;font-family:'Montserrat',sans-serif;font-size:0.85rem;padding:12px;background:rgba(255,68,68,0.1);border:1px solid rgba(255,68,68,0.3);margin-bottom:20px;"></div>

    <!-- Category selection -->
    <label class="form-label">I am auditioning as *</label>
    <div class="category-select">
      <button class="cat-btn" id="btn-king" type="button" onclick="selectCategory('king')">
        <span class="icon">👑</span>
        <div class="label">Prom King</div>
        <div class="desc">Male candidate</div>
      </button>
      <button class="cat-btn" id="btn-queen" type="button" onclick="selectCategory('queen')">
        <span class="icon">👸</span>
        <div class="label">Prom Queen</div>
        <div class="desc">Female candidate</div>
      </button>
    </div>
    <input type="hidden" id="category" value=""/>

    <div class="form-group">
      <label class="form-label">Full Name *</label>
      <input type="text" id="fullName" class="form-input" placeholder="Your full name as it will appear on screen" maxlength="100"/>
    </div>

    <div class="form-group">
      <label class="form-label">Class / School *</label>
      <input type="text" id="classSchool" class="form-input" placeholder="e.g. XII IPA 1 — SMA Negeri 1" maxlength="100"/>
    </div>

    <div class="form-group">
      <label class="form-label">Your Photo *</label>
      <label class="photo-upload">
        <input type="file" id="photo" accept="image/*" onchange="previewPhoto(this)"/>
        <img id="photoPreview" class="photo-preview" src="" alt="Preview"/>
        <div class="upload-text">📷 Upload your best photo</div>
        <div class="upload-hint">JPG or PNG · Min 400x400px · Max 5MB</div>
      </label>
    </div>

    <div class="form-group">
      <label class="form-label">Your Bio / Campaign Statement *</label>
      <textarea id="bio" class="form-input" placeholder="Tell everyone why you should be crowned. What makes you special? What will you do for the school? (Max 500 characters)" maxlength="500"></textarea>
      <div style="text-align:right;font-size:0.8rem;color:var(--text-dim);margin-top:4px;"><span id="bioCount">0</span>/500</div>
    </div>

    <div class="rules-box">
      <h3>Candidacy Guidelines</h3>
      <ul>
        <li>You must be a student or valid ticket holder</li>
        <li>One candidacy submission per person</li>
        <li>All submissions require admin approval before going live</li>
        <li>Your photo must be a clear, appropriate headshot</li>
        <li>Voting begins after all candidates are approved</li>
      </ul>
    </div>

    <button class="submit-btn" id="submitBtn" onclick="submitAudition()">✦ &nbsp; Submit My Candidacy</button>
  </div>

  <!-- SUCCESS -->
  <div class="success-msg-box" id="successBox">
    <span class="emoji">🌟</span>
    <h2>Application Submitted!</h2>
    <p>Your candidacy for <strong id="successCategory" style="color:var(--gold);"></strong> has been submitted successfully. It will be reviewed by the admin team before going live.</p>
    <p style="margin-top:16px; font-size:0.9rem;">Good luck, <strong id="successName" style="color:var(--gold);"></strong>! ✨</p>
    <br>
    <a href="../index.html" style="font-family:'Cinzel',serif;font-size:0.8rem;letter-spacing:3px;color:var(--gold);text-decoration:none;border:1px solid var(--gold);padding:14px 28px;display:inline-block;margin-top:16px;">← Return Home</a>
  </div>
</div>

<script>
let selectedCategory = '';

function selectCategory(cat) {
  selectedCategory = cat;
  document.getElementById('category').value = cat;
  document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('selected'));
  document.getElementById('btn-' + cat).classList.add('selected');
}

function previewPhoto(input) {
  const file = input.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      const img = document.getElementById('photoPreview');
      img.src = e.target.result;
      img.classList.add('show');
    };
    reader.readAsDataURL(file);
  }
}

// Bio counter
document.getElementById('bio').addEventListener('input', function() {
  document.getElementById('bioCount').textContent = this.value.length;
});

async function submitAudition() {
  const errDiv = document.getElementById('formErrors');
  const name = document.getElementById('fullName').value.trim();
  const cls = document.getElementById('classSchool').value.trim();
  const bio = document.getElementById('bio').value.trim();
  const photo = document.getElementById('photo').files[0];
  const cat = selectedCategory;

  if (!cat) {
    errDiv.textContent = 'Please select a category (King or Queen).';
    errDiv.style.display = 'block';
    return;
  }
  if (!name || !cls || !bio) {
    errDiv.textContent = 'Please fill in all required fields.';
    errDiv.style.display = 'block';
    return;
  }
  if (!photo) {
    errDiv.textContent = 'Please upload your photo.';
    errDiv.style.display = 'block';
    return;
  }
  if (bio.length < 30) {
    errDiv.textContent = 'Bio must be at least 30 characters.';
    errDiv.style.display = 'block';
    return;
  }

  errDiv.style.display = 'none';
  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  btn.textContent = 'Submitting...';

  const formData = new FormData();
  formData.append('full_name', name);
  formData.append('class_school', cls);
  formData.append('bio', bio);
  formData.append('category', cat);
  formData.append('photo', photo);

  try {
    const res = await fetch('audition_api.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
      showSuccess(name, cat);
    } else {
      errDiv.textContent = data.message || 'Submission failed.';
      errDiv.style.display = 'block';
    }
  } catch (e) {
    // Demo mode
    showSuccess(name, cat);
  }

  btn.disabled = false;
  btn.textContent = '✦   Submit My Candidacy';
}

function showSuccess(name, cat) {
  document.getElementById('formCard').style.display = 'none';
  document.getElementById('successName').textContent = name;
  document.getElementById('successCategory').textContent = cat === 'king' ? 'Prom King' : 'Prom Queen';
  document.getElementById('successBox').classList.add('show');
}
</script>
</body>
</html>