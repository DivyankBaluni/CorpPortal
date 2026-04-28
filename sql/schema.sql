-- ============================================================
--  Role-Based Company Portal – Database Schema
--  Run this in phpMyAdmin or MySQL CLI before starting
-- ============================================================

DROP DATABASE IF EXISTS role_portal;
CREATE DATABASE role_portal
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE role_portal;

CREATE TABLE users (
  id         INT(11)      NOT NULL AUTO_INCREMENT,
  name       VARCHAR(100) NOT NULL,
  email      VARCHAR(150) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  role       ENUM('admin','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------
--  Seed: default admin account
--  Password: Admin@123  (bcrypt hash below)
-- ----------------------------------------------------------------
INSERT INTO users (name, email, password, role) VALUES
(
  'Super Admin',
  'admin@company.com',
  '$2y$12$YH3a5BDLqSBGTWlQvCT0V.kGCO9Xs6M3RiO6w5N1d1cRW5L.Dn4Oq',
  'admin'
),
(
  'Jane Employee',
  'jane@company.com',
  '$2y$12$YH3a5BDLqSBGTWlQvCT0V.kGCO9Xs6M3RiO6w5N1d1cRW5L.Dn4Oq',
  'user'
);
-- NOTE: The hash above maps to password "Admin@123"
--       Change it after first login.
