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
    type ENUM('Student', 'Teacher', 'Staff') DEFAULT 'Student',
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
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
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
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
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
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    FOREIGN KEY (uid) REFERENCES users_auth(uid) ON DELETE CASCADE,
    FOREIGN KEY (uid) REFERENCES user_info(uid) ON DELETE CASCADE
);

-- Create staff table
CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(50),
    department VARCHAR(50),
    position VARCHAR(50),
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    FOREIGN KEY (uid) REFERENCES users_auth(uid) ON DELETE CASCADE,
    FOREIGN KEY (uid) REFERENCES user_info(uid) ON DELETE CASCADE
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

-- Drop and recreate the role change trigger
DROP TRIGGER IF EXISTS after_role_change;
DELIMITER //
CREATE TRIGGER after_role_change
AFTER UPDATE ON user_info
FOR EACH ROW
BEGIN
    IF NEW.type != OLD.type THEN
        -- Delete old role records first
        DELETE FROM teacher WHERE uid = NEW.uid;
        DELETE FROM student WHERE uid = NEW.uid;
        DELETE FROM staff WHERE uid = NEW.uid;
        
        -- Insert new role record based on type
        CASE NEW.type
            WHEN 'Teacher' THEN
                INSERT INTO teacher (uid, department, status)
                VALUES (NEW.uid, 'Unassigned', 'Active');
            WHEN 'Staff' THEN 
                INSERT INTO staff (uid, department, position)
                VALUES (NEW.uid, 'Unassigned', 'General Staff');
            WHEN 'Student' THEN
                -- Get current count of students for this year
                SET @year = YEAR(CURDATE());
                SET @count = (
                    SELECT COUNT(*) + 1
                    FROM student 
                    WHERE LEFT(id_no, 4) = @year
                );
                
                INSERT INTO student (uid, year, section, status, program, major, id_no)
                VALUES (
                    NEW.uid, 
                    1, 
                    'A', 
                    'Active', 
                    'Unassigned', 
                    'Undeclared',
                    CONCAT(@year, '-', LPAD(@count, 6, '0'))
                );
        END CASE;
    END IF;
END//
DELIMITER ;

-- Add Admin Users Auth Data
INSERT INTO users_auth (email, pwd, role) VALUES
('admin@admin.com', 'admin123', 'Admin')