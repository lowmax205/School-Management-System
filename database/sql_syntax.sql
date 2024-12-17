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

-- Create enhanced user_logs table
CREATE TABLE user_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(50),
    log_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('success', 'error', 'warning') DEFAULT 'success',
    description TEXT,
    ip_address VARCHAR(45),
    FOREIGN KEY (uid) REFERENCES users_auth(uid) ON DELETE CASCADE
);

CREATE TABLE system_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(50),
    action VARCHAR(50),
    details TEXT,
    status ENUM('success', 'warning', 'error') DEFAULT 'success',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
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

CREATE OR REPLACE VIEW user_details_view AS
SELECT 
    ua.uid,
    ua.email,
    ua.role,
    DATE_FORMAT(ua.created_at, '%Y-%m-%d %H:%i:%s') as account_created,
    ui.first_name,
    ui.last_name,
    ui.type,
    ui.birth_date,
    ui.gender,
    ui.address,
    ui.phone,
    DATE_FORMAT(ui.updated_at, '%Y-%m-%d %H:%i:%s') as info_updated,
    CASE 
        WHEN ui.type = 'Student' THEN s.status
        WHEN ui.type = 'Teacher' THEN t.status
        WHEN ui.type = 'Staff' THEN st.status
        WHEN ua.role = 'Admin' THEN a.status
        ELSE 'Active'
    END as status
FROM users_auth ua
LEFT JOIN user_info ui ON ua.uid = ui.uid
LEFT JOIN student s ON ui.uid = s.uid AND ui.type = 'Student'
LEFT JOIN teacher t ON ui.uid = t.uid AND ui.type = 'Teacher'
LEFT JOIN staff st ON ui.uid = st.uid AND ui.type = 'Staff'
LEFT JOIN admin a ON ui.uid = a.uid AND ua.role = 'Admin';

-- Add Admin Users Auth Data
INSERT INTO users_auth (email, pwd, role) VALUES
('admin@admin.com', 'admin123', 'Admin'),

-- Add sample Users Info Data
('juan.delacruz@gmail.com', 'juan12345', 'User'),
('maria.santos@gmail.com', 'marias2024', 'User'),
('pedro.mercado@gmail.com', 'mercado678', 'User'),
('anne.soriano@gmail.com', 'anneSor123', 'User'),
('carlos.reyes@gmail.com', 'carlos987', 'User'),
('rosa.bautista@gmail.com', 'bautistaXyz', 'User'),
('leo.garcia@gmail.com', 'garciaLeo1', 'User'),
('ella.lopez@gmail.com', 'ellaL2023', 'User'),
('mark.cruz@gmail.com', 'mark2024!', 'User'),
('jose.manalo@gmail.com', 'manalo456', 'User'),
('kris.aquino@gmail.com', 'krisK789', 'User'),
('roberto.gutierrez@gmail.com', 'robertG123', 'User'),
('lucy.torres@gmail.com', 'lucyT2023', 'User'),
('daniel.padilla@gmail.com', 'daniel321', 'User'),
('glenda.madrigal@gmail.com', 'glendaXyz', 'User'),
('paulo.avila@gmail.com', 'pauloPwd!', 'User'),
('nina.valencia@gmail.com', 'valencia456', 'User'),
('julia.barcelo@gmail.com', 'barcelo890', 'User'),
('marco.villanueva@gmail.com', 'marco2024', 'User');
