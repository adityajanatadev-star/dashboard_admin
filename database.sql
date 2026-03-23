-- ============================================
-- Dashboard Admin Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS `dashboard_admin`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `dashboard_admin`;

-- ============================================
-- Table: users
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`                    INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `username`              VARCHAR(50)     NOT NULL,
  `nama`                  VARCHAR(100)    NOT NULL,
  `password`              VARCHAR(255)    NOT NULL,  -- bcrypt hash
  `email`                 VARCHAR(100)    NOT NULL,
  `role`                  ENUM('administrator','editor','viewer') NOT NULL DEFAULT 'viewer',
  `last_login`            DATETIME        NULL DEFAULT NULL,
  `last_changed_password` DATETIME        NULL DEFAULT NULL,
  `created_at`            DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`),
  UNIQUE KEY `uq_email`    (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Seed: default users
-- Passwords below are bcrypt of "Password123!"
-- ============================================
INSERT INTO `users` (`username`, `nama`, `password`, `email`, `role`) VALUES
(
  'admin',
  'Administrator',
  '$2y$10$dHlftj59QQh.3tzaBwsC5OYCbqvKYSncPrY0ZgzaEyQLkFELcYOoC',
  'admin@example.com',
  'administrator'
),
(
  'editor01',
  'Editor Pertama',
  '$2y$12$XcV3ZkO.2s9Hb5fYqW1E6OdJ3Kl7mN0pQrT8uV4wX5yZ6aA7bB8c',
  'editor@example.com',
  'editor'
),
(
  'viewer01',
  'Viewer Pertama',
  '$2y$12$XcV3ZkO.2s9Hb5fYqW1E6OdJ3Kl7mN0pQrT8uV4wX5yZ6aA7bB8c',
  'viewer@example.com',
  'viewer'
);
