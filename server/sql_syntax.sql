-- Create database
CREATE DATABASE snsu_management;
USE snsu_management;

-- Create users_auth table
CREATE TABLE users_auth (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    pwd VARCHAR(255) NOT NULL,
    uid VARCHAR(50) NOT NULL UNIQUE DEFAULT (CONCAT('UID', LPAD(FLOOR(RAND() * 1000000), 6, '0'))),
    role ENUM('Admin', 'User') DEFAULT 'User',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create user_info table
CREATE TABLE user_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(50) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    birth_date DATE,
    gender ENUM('M', 'F', 'Other'),
    address TEXT,
    phone VARCHAR(20),
    role ENUM('Student', 'Teacher') DEFAULT 'Student',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (uid) REFERENCES users_auth(uid) ON DELETE CASCADE ON UPDATE CASCADE
);