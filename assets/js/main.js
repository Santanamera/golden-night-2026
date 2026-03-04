/* ============================================
   GOLDEN NIGHT 2026 — Main JavaScript
   assets/js/main.js
   ============================================ */

'use strict';

// ============================================================
// TOAST NOTIFICATIONS
// ============================================================
const Toast = {
  show(message, type = 'default', duration = 3500) {
    // Remove existing
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = '✦ ' + message;
    document.body.appendChild(toast);

    // Trigger animation
    requestAnimationFrame(() => {
      requestAnimationFrame(() => toast.classList.add('show'));
    });

    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 400);
    }, duration);
  },

  success(msg) { this.show(msg, 'success'); },
  error(msg)   { this.show(msg, 'error'); },
  info(msg)    { this.show(msg, 'default'); }
};

// ============================================================
// SCROLL REVEAL
// ============================================================
function initScrollReveal() {
  const observer = new IntersectionObserver(entries => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        // Stagger children if any
        entry.target.querySelectorAll('[data-reveal-child]').forEach((el, idx) => {
          el.style.transitionDelay = (idx * 0.12) + 's';
        });
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
}

// ============================================================
// NAVBAR SCROLL EFFECT
// ============================================================
function initNavbar() {
  const nav = document.getElementById('navbar');
  if (!nav) return;

  window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 60);
  }, { passive: true });
}

// ============================================================
// COUNTDOWN TIMER
// ============================================================
function initCountdown(targetDateStr, elementIds = {}) {
  const target = new Date(targetDateStr);
  const ids = {
    days:  elementIds.days  || 'cd-days',
    hours: elementIds.hours || 'cd-hours',
    mins:  elementIds.mins  || 'cd-mins',
    secs:  elementIds.secs  || 'cd-secs',
  };

  function pad(n, len = 2) {
    return String(n).padStart(len, '0');
  }

  function tick() {
    const now  = new Date();
    const diff = target - now;

    if (diff <= 0) {
      setEl(ids.days,  '000');
      setEl(ids.hours, '00');
      setEl(ids.mins,  '00');
      setEl(ids.secs,  '00');
      return;
    }

    const d = Math.floor(diff / 86400000);
    const h = Math.floor((diff % 86400000) / 3600000);
    const m = Math.floor((diff % 3600000)  / 60000);
    const s = Math.floor((diff % 60000)    / 1000);

    setEl(ids.days,  pad(d, 3));
    setEl(ids.hours, pad(h));
    setEl(ids.mins,  pad(m));
    setEl(ids.secs,  pad(s));
  }

  function setEl(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
  }

  tick();
  setInterval(tick, 1000);
}

// ============================================================
// PARTICLE SYSTEM
// ============================================================
function initParticles(canvasId = 'particles') {
  const canvas = document.getElementById(canvasId);
  if (!canvas) return;

  const ctx = canvas.getContext('2d');
  let particles = [];

  function resize() {
    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;
  }
  resize();
  window.addEventListener('resize', resize, { passive: true });

  class Particle {
    constructor() { this.reset(true); }
    reset(init = false) {
      this.x     = Math.random() * canvas.width;
      this.y     = init ? Math.random() * canvas.height : canvas.height + 10;
      this.size  = Math.random() * 1.4 + 0.3;
      this.speedX = (Math.random() - 0.5) * 0.25;
      this.speedY = -(Math.random() * 0.45 + 0.1);
      this.life  = 1;
      this.decay = Math.random() * 0.003 + 0.001;
      this.opacity = Math.random() * 0.55 + 0.1;
    }
    update() {
      this.x += this.speedX;
      this.y += this.speedY;
      this.life -= this.decay;
      if (this.life <= 0 || this.y < -10) this.reset();
    }
    draw() {
      ctx.save();
      ctx.globalAlpha = this.life * this.opacity;
      ctx.fillStyle = '#D4AF37';
      ctx.shadowBlur = 4;
      ctx.shadowColor = '#D4AF37';
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
      ctx.fill();
      ctx.restore();
    }
  }

  for (let i = 0; i < 100; i++) particles.push(new Particle());

  function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    particles.forEach(p => { p.update(); p.draw(); });
    requestAnimationFrame(animate);
  }
  animate();
}

// ============================================================
// CUSTOM CURSOR
// ============================================================
function initCursor() {
  if (window.innerWidth <= 768) return; // skip on mobile

  const cursor    = document.getElementById('cursor');
  const cursorDot = document.getElementById('cursorDot');
  if (!cursor || !cursorDot) return;

  let mx = 0, my = 0, cx = 0, cy = 0;

  document.addEventListener('mousemove', e => {
    mx = e.clientX; my = e.clientY;
    cursorDot.style.left = mx + 'px';
    cursorDot.style.top  = my + 'px';
  });

  function animateCursor() {
    cx += (mx - cx) * 0.12;
    cy += (my - cy) * 0.12;
    cursor.style.left = cx + 'px';
    cursor.style.top  = cy + 'px';
    requestAnimationFrame(animateCursor);
  }
  animateCursor();

  // Hover effect on interactive elements
  document.querySelectorAll('a, button, .btn, .card, input, select, textarea, label').forEach(el => {
    el.addEventListener('mouseenter', () => cursor.classList.add('hovered'));
    el.addEventListener('mouseleave', () => cursor.classList.remove('hovered'));
  });

  // Sparkle on click
  document.addEventListener('click', e => {
    for (let i = 0; i < 6; i++) {
      const spark = document.createElement('div');
      spark.style.cssText = `
        position:fixed; pointer-events:none; z-index:9999;
        width:4px; height:4px; background:#D4AF37; border-radius:50%;
        animation: sparkle 0.6s ${i * 0.05}s ease forwards;
        left:${e.clientX + Math.cos(i / 6 * Math.PI * 2) * 25}px;
        top:${e.clientY  + Math.sin(i / 6 * Math.PI * 2) * 25}px;
      `;
      document.body.appendChild(spark);
      setTimeout(() => spark.remove(), 700);
    }
  });
}

// ============================================================
// MOBILE NAV TOGGLE
// ============================================================
function initMobileNav() {
  const hamburger  = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobileMenu');
  if (!hamburger || !mobileMenu) return;

  hamburger.addEventListener('click', () => {
    mobileMenu.classList.toggle('open');
  });
}

window.closeMobileMenu = function() {
  const m = document.getElementById('mobileMenu');
  if (m) m.classList.remove('open');
};

// ============================================================
// FILE UPLOAD PREVIEW
// ============================================================
function initFileUploads() {
  document.querySelectorAll('.file-upload-area').forEach(area => {
    const input    = area.querySelector('input[type="file"]');
    const nameEl   = area.querySelector('.file-selected-name');
    const previewEl = area.querySelector('.upload-preview-img');

    if (!input) return;

    input.addEventListener('change', () => {
      const file = input.files[0];
      if (!file) return;

      if (nameEl) nameEl.textContent = '✓ ' + file.name;

      // Image preview
      if (previewEl && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => {
          previewEl.src = e.target.result;
          previewEl.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    });

    // Drag and drop
    area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('dragover'); });
    area.addEventListener('dragleave', () => area.classList.remove('dragover'));
    area.addEventListener('drop', e => {
      e.preventDefault();
      area.classList.remove('dragover');
      if (e.dataTransfer.files.length) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
      }
    });
  });
}

// ============================================================
// FORM VALIDATION HELPER
// ============================================================
const FormValidator = {
  rules: {
    required: (val) => val.trim() !== '' || 'This field is required.',
    phone: (val) => /^[0-9+\-\s]{8,20}$/.test(val) || 'Invalid phone number.',
    minLen: (min) => (val) => val.trim().length >= min || `Minimum ${min} characters.`,
    maxLen: (max) => (val) => val.trim().length <= max || `Maximum ${max} characters.`,
    email: (val) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val) || 'Invalid email address.',
  },

  validate(fieldId, ...rules) {
    const el  = document.getElementById(fieldId);
    if (!el) return true;
    const val = el.value;

    for (const rule of rules) {
      const result = typeof rule === 'function' ? rule(val) : true;
      if (result !== true) {
        this.showError(el, result);
        return false;
      }
    }
    this.clearError(el);
    return true;
  },

  showError(el, msg) {
    el.style.borderColor = '#ff4444';
    let err = el.nextElementSibling;
    if (!err || !err.classList.contains('field-error')) {
      err = document.createElement('div');
      err.className = 'field-error';
      err.style.cssText = 'color:#ff6666;font-size:0.78rem;font-family:Montserrat,sans-serif;margin-top:5px;';
      el.parentNode.insertBefore(err, el.nextSibling);
    }
    err.textContent = msg;
  },

  clearError(el) {
    el.style.borderColor = '';
    const err = el.nextElementSibling;
    if (err && err.classList.contains('field-error')) err.remove();
  }
};

// ============================================================
// CURRENCY FORMATTER
// ============================================================
function formatRupiah(amount) {
  return 'Rp ' + Number(amount).toLocaleString('id-ID');
}

// ============================================================
// CLOCK
// ============================================================
function initClock(elementId = 'clock') {
  const el = document.getElementById(elementId);
  if (!el) return;
  setInterval(() => {
    el.textContent = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
  }, 1000);
}

// ============================================================
// AUTO-INIT ON DOM READY
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
  initScrollReveal();
  initNavbar();
  initCursor();
  initMobileNav();
  initFileUploads();

  // Add sparkle keyframe if not present
  if (!document.getElementById('sparkle-style')) {
    const style = document.createElement('style');
    style.id = 'sparkle-style';
    style.textContent = `
      @keyframes sparkle {
        0%   { opacity: 0; transform: scale(0); }
        50%  { opacity: 1; transform: scale(1); }
        100% { opacity: 0; transform: scale(0.5); }
      }
    `;
    document.head.appendChild(style);
  }
});
