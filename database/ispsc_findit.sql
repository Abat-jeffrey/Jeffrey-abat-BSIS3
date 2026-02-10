CREATE DATABASE IF NOT EXISTS ispsc_findit;
USE ispsc_findit;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    role ENUM('student','teacher','staff','admin') NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL
);

CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    location VARCHAR(150) NOT NULL,
    status ENUM('lost','found') NOT NULL,
    image_path VARCHAR(200) NULL,
    claim_status ENUM('open','claimed') NOT NULL DEFAULT 'open',
    posted_by INT NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (posted_by) REFERENCES users(id)
);

CREATE TABLE claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    user_id INT NOT NULL,
    claimant_name VARCHAR(120) NOT NULL,
    status ENUM('pending','approved') NOT NULL DEFAULT 'pending',
    approved_by INT NULL,
    created_at DATETIME NOT NULL,
    approved_at DATETIME NULL,
    FOREIGN KEY (item_id) REFERENCES items(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    body TEXT NULL,
    image_path VARCHAR(200) NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    actor_id INT NULL,
    action VARCHAR(255) NOT NULL,
    item_id INT NULL,
    target_user_id INT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (actor_id) REFERENCES users(id),
    FOREIGN KEY (item_id) REFERENCES items(id),
    FOREIGN KEY (target_user_id) REFERENCES users(id)
);

-- Pre-created single staff/admin accounts (password: password123)
INSERT INTO users (full_name, email, role, password_hash, created_at)
VALUES
('ISPSC Staff', 'staff@ispsc.edu', 'staff', '$2y$10$QfXnH6QMH8ua5QfNfVwJwOF6nWXhWf8pn6xE7YQDdyCjTiMQuu2cK', NOW()),
('ISPSC Admin', 'admin@ispsc.edu', 'admin', '$2y$10$QfXnH6QMH8ua5QfNfVwJwOF6nWXhWf8pn6xE7YQDdyCjTiMQuu2cK', NOW());
