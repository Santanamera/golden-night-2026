-- Golden Night 2026 — Database schema
-- Run this on your MySQL server to create required tables

CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) DEFAULT NULL,
  `password_hash` VARCHAR(255) DEFAULT NULL,
  `name` VARCHAR(150) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `prom_settings` (
  `setting_key` VARCHAR(100) PRIMARY KEY,
  `setting_value` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ticket_id` VARCHAR(64) NOT NULL UNIQUE,
  `qr_code` TEXT,
  `full_name` VARCHAR(200) NOT NULL,
  `class_school` VARCHAR(200) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `student_type` ENUM('internal','external','general') DEFAULT 'general',
  `payment_proof` VARCHAR(255) DEFAULT NULL,
  `payment_status` ENUM('pending','confirmed','rejected') DEFAULT 'pending',
  `ticket_status` ENUM('unused','used','cancelled') DEFAULT 'unused',
  `seat_number` VARCHAR(30) DEFAULT NULL,
  `amount_paid` INT DEFAULT 0,
  `momo_reference` VARCHAR(128) DEFAULT NULL,
  `used_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `candidates` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(200) NOT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `category` ENUM('king','queen') NOT NULL,
  `bio` TEXT,
  `class_school` VARCHAR(200) DEFAULT NULL,
  `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
  `vote_count` INT DEFAULT 0,
  `approved_at` DATETIME DEFAULT NULL,
  `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `votes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ticket_id` VARCHAR(64) NOT NULL,
  `king_candidate_id` INT UNSIGNED NOT NULL,
  `queen_candidate_id` INT UNSIGNED NOT NULL,
  `voted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_ticket_vote` (`ticket_id`),
  FOREIGN KEY (`king_candidate_id`) REFERENCES `candidates`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (`queen_candidate_id`) REFERENCES `candidates`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `scan_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ticket_id` VARCHAR(64) DEFAULT NULL,
  `scan_result` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `momo_requests` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `reference` VARCHAR(128) NOT NULL UNIQUE,
  `phone` VARCHAR(50) DEFAULT NULL,
  `amount` INT DEFAULT 0,
  `name` VARCHAR(200) DEFAULT NULL,
  `reason` VARCHAR(255) DEFAULT NULL,
  `status` VARCHAR(50) DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed a few settings
INSERT INTO prom_settings (setting_key, setting_value) VALUES
('prom_name','Golden Night 2026') ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);

INSERT INTO prom_settings (setting_key, setting_value) VALUES
('prom_date','2026-06-15 18:00:00') ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);

INSERT INTO prom_settings (setting_key, setting_value) VALUES
('prom_venue','Iwacu Garden, Kicukiro') ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);

INSERT INTO prom_settings (setting_key, setting_value) VALUES
('tickets_available','300') ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);
