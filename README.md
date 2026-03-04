# ✦ Golden Night 2026 — Prom Management System

A full-stack web application for managing a high school prom event. Built with PHP, MySQL, and vanilla HTML/CSS/JS.

---

## 🌟 Features

- **Ticket Sales** — Students buy tickets online with MTN MoMo payment
- **QR Code Entry** — Each ticket gets a unique QR code for door scanning
- **Prom Royalty Voting** — Students vote for King & Queen using their ticket ID
- **Candidate Registration** — Students apply to be King/Queen candidates
- **Admin Dashboard** — Confirm payments, approve candidates, scan QR codes, live vote results

---

## 🛠 Tech Stack

| Layer    | Technology              |
|----------|-------------------------|
| Frontend | HTML5, CSS3, JavaScript |
| Backend  | PHP 8+                  |
| Database | MySQL (via XAMPP)       |
| Server   | Apache (XAMPP)          |

---

## 🚀 Setup

### 1. Requirements
- [XAMPP](https://www.apachefriends.org/) with Apache + MySQL running

### 2. Install
Copy this folder into `C:\xampp\htdocs\prom-system\`

### 3. Database
1. Open `http://localhost/phpmyadmin`
2. Create database: `prom_system`
3. Import: `database/schema.sql`

### 4. Fix Admin Password
Visit: `http://localhost/prom-system/reset_password.php`  
**Delete `reset_password.php` after.**

### 5. URLs
| Page | URL |
|------|-----|
| Landing | `http://localhost/prom-system/` |
| Buy Ticket | `http://localhost/prom-system/public/buy-ticket.php` |
| Vote | `http://localhost/prom-system/public/vote.php` |
| Audition | `http://localhost/prom-system/public/audition.php` |
| Admin | `http://localhost/prom-system/admin/login.php` |

**Admin:** `admin` / `prom2026`

---

## 💳 MTN MoMo Payment

Update `MOMO_MERCHANT_CODE` in `public/momo_request.php` with your real merchant code.  
Students pay by dialing: `*182*8*1*[CODE]*[AMOUNT]#`

---

## 📅 Event Details

- **Venue:** Iwacu Garden, Kicukiro, Kigali
- **Date:** June 2026 (TBD)
- **Prices:** Rwf 25,000 (internal) · Rwf 30,000 (external)
