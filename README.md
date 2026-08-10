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

### 4. Configure Admin Password
Set `ADMIN_PORTAL_PASSWORD` in your environment or Apache/PHP config. Do not store admin passwords in repository files.

### 5. URLs
| Page | URL |
|------|-----|
| Landing | `http://localhost/prom-system/` |
| Buy Ticket | `http://localhost/prom-system/public/buy-ticket.php` |
| Vote | `http://localhost/prom-system/public/vote.php` |
| Audition | `http://localhost/prom-system/public/audition.php` |
| Admin | `http://localhost/prom-system/admin/login.php` |

Admin access is controlled by the `ADMIN_PORTAL_PASSWORD` environment variable.

---

## 💳 MTN MoMo Payment

Configure MTN MoMo environment variables instead of editing code. Set `MOMO_PAYEE_CODE`, `MOMO_PAYEE_NAME`, `MOMO_SUB_KEY`, `MOMO_API_USER`, and `MOMO_API_KEY` in your environment.  
Students pay by dialing: `*182*8*1*[CODE]*[AMOUNT]#`

---

## 📅 Event Details

- **Venue:** Iwacu Garden, Kicukiro, Kigali
- **Date:** June 2026 (TBD)
- **Prices:** Single Rwf 20,000 / Couple Rwf 35,000
