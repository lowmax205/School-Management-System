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
    uid VARCHAR(50),
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    type ENUM('Student', 'Teacher', 'Staff'),
    birth_date DATE,
    gender ENUM('M', 'F', 'Other'),
    address TEXT,
    phone VARCHAR(20),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (uid) REFERENCES users_auth(uid) ON DELETE CASCADE
);

-- Create admin table
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(50),
    role VARCHAR(50),
    status ENUM('Active', 'Inactive'),
    position VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (uid) REFERENCES users_auth(uid) ON DELETE CASCADE,
    FOREIGN KEY (uid) REFERENCES user_info(uid) ON DELETE CASCADE
);

-- Create student table
CREATE TABLE student (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(50),
    year INT,
    section VARCHAR(10),
    status ENUM('Active', 'Inactive'),
    program VARCHAR(50),
    major VARCHAR(50),
    id_no VARCHAR(50),
    FOREIGN KEY (uid) REFERENCES users_auth(uid) ON DELETE CASCADE,
    FOREIGN KEY (uid) REFERENCES user_info(uid) ON DELETE CASCADE
);

-- Create teacher table
CREATE TABLE teacher (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(50),
    department VARCHAR(50),
    contact VARCHAR(20),
    status ENUM('Active', 'Inactive'),
    FOREIGN KEY (uid) REFERENCES users_auth(uid) ON DELETE CASCADE,
    FOREIGN KEY (uid) REFERENCES user_info(uid) ON DELETE CASCADE
);

-- Create subject table
CREATE TABLE subject (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(50),
    subject_name VARCHAR(100),
    units INT,
    department VARCHAR(50),
    status ENUM('Active', 'Inactive')
);

-- Create student subject table
CREATE TABLE student_subject (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(50),
    subject_name VARCHAR(100),
    units INT,
    department VARCHAR(50),
    status ENUM('Active', 'Inactive')
);

-- Create system_log table
CREATE TABLE system_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50),
    date DATE,
    time TIME,
    user_type VARCHAR(50),
    action VARCHAR(50),
    details TEXT,
    role_type VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users_auth(uid) ON DELETE CASCADE
);

-- Create trigger for automatic user_info creation
DELIMITER //
CREATE TRIGGER after_user_auth_insert 
AFTER INSERT ON users_auth
FOR EACH ROW
BEGIN
    INSERT INTO user_info (uid)
    VALUES (NEW.uid);
END//
DELIMITER ;