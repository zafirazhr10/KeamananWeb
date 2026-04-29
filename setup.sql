-- ============================================================
-- SQL Injection Demo - Database Setup
-- Jalankan file ini di MySQL/phpMyAdmin sebelum memulai demo
-- ============================================================

CREATE DATABASE IF NOT EXISTS sqli_demo;
USE sqli_demo;

-- Tabel users (target serangan)
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'user'
);

-- Tabel secret_data (data sensitif yang ingin diambil attacker)
DROP TABLE IF EXISTS secret_data;
CREATE TABLE secret_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data_name VARCHAR(100),
    data_value VARCHAR(255)
);

-- Insert sample users
INSERT INTO users (username, password, email, role) VALUES
('admin', 'admin123', 'admin@demo.com', 'admin'),
('budi', 'budi456', 'budi@demo.com', 'user'),
('siti', 'siti789', 'siti@demo.com', 'user'),
('dani', 'dani321', 'dani@demo.com', 'user');

-- Insert sample secret data
INSERT INTO secret_data (data_name, data_value) VALUES
('api_key', 'sk-prod-1a2b3c4d5e6f7g8h9i'),
('db_password', 'SuperSecretDBPass!'),
('internal_note', 'Server backup runs at 03:00 AM'),
('credit_card', '4111-1111-1111-1111');

SELECT 'Database setup berhasil!' AS status;
