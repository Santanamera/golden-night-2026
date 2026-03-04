-- ============================================
-- GOLDEN NIGHT PROM MANAGEMENT SYSTEM
-- Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS prom_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE prom_system;

-- ============================================
-- ADMIN USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin: username=admin, password=prom2026
INSERT INTO admins (username, password, full_name, email) VALUES
('admin', '$2y$10$TKh8H1.PfzsH4N93XuBBmuSL/zOE9e4YPQdZfGGSHdquEBRr9QCJK', 'Prom Administrator', 'admin@golden-night.rw');
-- Password above = "prom2026"
-- To change password, run in PHP: echo password_hash('YOUR_NEW_PASSWORD', PASSWORD_BCRYPT);

-- ============================================
-- TICKETS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id VARCHAR(20) NOT NULL UNIQUE,
    qr_code VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    class_school VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    student_type ENUM('internal','external') NOT NULL DEFAULT 'internal',
    payment_proof VARCHAR(255),
    payment_status ENUM('pending','confirmed','rejected') DEFAULT 'pending',
    ticket_status ENUM('unused','used','cancelled') DEFAULT 'unused',
    seat_number VARCHAR(10),
    amount_paid DECIMAL(10,2) DEFAULT 0.00,
    used_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ticket_id (ticket_id),
    INDEX idx_status (ticket_status),
    INDEX idx_payment (payment_status)
) ENGINE=InnoDB;

-- ============================================
-- CANDIDATES TABLE (King & Queen)
-- ============================================
CREATE TABLE IF NOT EXISTS candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    photo VARCHAR(255),
    category ENUM('king','queen') NOT NULL,
    bio TEXT,
    class_school VARCHAR(100),
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    vote_count INT DEFAULT 0,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL,
    INDEX idx_category (category),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============================================
-- VOTES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id VARCHAR(20) NOT NULL,
    king_candidate_id INT,
    queen_candidate_id INT,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_voter (ticket_id),
    FOREIGN KEY (king_candidate_id) REFERENCES candidates(id) ON DELETE SET NULL,
    FOREIGN KEY (queen_candidate_id) REFERENCES candidates(id) ON DELETE SET NULL,
    INDEX idx_ticket (ticket_id)
) ENGINE=InnoDB;

-- ============================================
-- PROM SETTINGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS prom_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default settings
INSERT INTO prom_settings (setting_key, setting_value) VALUES
('prom_name', 'Golden Night 2026'),
('prom_date', '2026-06-15 18:00:00'),
('prom_venue', 'Grand Ballroom, City Hotel'),
('dress_code', 'Black Tie / Evening Gown'),
('ticket_price_internal', '50000'),
('ticket_price_external', '75000'),
('tickets_available', '300'),
('voting_enabled', '1'),
('registration_enabled', '1'),
('prom_tagline', 'An Evening Under the Stars');

-- ============================================
-- SCAN LOGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS scan_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id VARCHAR(20) NOT NULL,
    scanned_by INT,
    scan_result ENUM('valid','already_used','invalid') NOT NULL,
    scanned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (scanned_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_scan_time (scanned_at)
) ENGINE=InnoDB;

-- ============================================
-- MOMO PAYMENT REQUESTS
-- ============================================
CREATE TABLE IF NOT EXISTS momo_requests (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  reference   VARCHAR(20)  NOT NULL UNIQUE,
  phone       VARCHAR(15)  NOT NULL,
  amount      INT          NOT NULL,
  name        VARCHAR(150) NOT NULL,
  reason      VARCHAR(200) DEFAULT '',
  status      ENUM('pending','completed','failed') DEFAULT 'pending',
  created_at  DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
