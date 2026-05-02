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
--  Password: PASSWORD  (bcrypt hash below)
-- ----------------------------------------------------------------
INSERT INTO users (name, email, password, role) VALUES
(
  'Admin',
  'manager@company.com',
  '$2y$12$LWphXyof4X2h2OYiChbQpulA2eiqPMOS5tc3rmaMhzja05oagucKi',
  'admin'
),
(
  'User',
  'user@company.com',
  '$2y$12$8GGa5Ca6r.x9j2z3mwTUVeM7ZONMMs.ZeSmZrLM6WpjjWl9aa/DXu',
  'user'
);