-- Sample Users Auth Data
INSERT INTO users_auth (email, pwd, role) VALUES
('juan.delacruz@snsu.edu.ph', '$2a$12$1234', 'Admin'),
('maria.santos@snsu.edu.ph', '$2a$12$5678', 'User'),
('pedro.garcia@snsu.edu.ph', '$2a$12$9012', 'User'),
('ana.reyes@snsu.edu.ph', '$2a$12$3456', 'User'),
('carlo.cruz@snsu.edu.ph', '$2a$12$7890', 'User'),
('sofia.mendoza@snsu.edu.ph', '$2a$12$1234', 'User'),
('miguel.ramos@snsu.edu.ph', '$2a$12$5678', 'User'),
('isabel.bautista@snsu.edu.ph', '$2a$12$9012', 'User'),
('rafael.santos@snsu.edu.ph', '$2a$12$3456', 'User'),
('carmen.villanueva@snsu.edu.ph', '$2a$12$7890', 'User');

-- Update User Info
UPDATE user_info SET
    first_name = 'Juan',
    last_name = 'Dela Cruz',
    type = 'Staff',
    birth_date = '1985-03-15',
    gender = 'M',
    address = 'Poblacion, Surigao City, Surigao del Norte',
    phone = '09171234567'
WHERE uid = (SELECT uid FROM users_auth WHERE email = 'juan.delacruz@snsu.edu.ph');

-- Continue updating other user_info records...
UPDATE user_info SET
    first_name = 'Maria',
    last_name = 'Santos',
    type = 'Teacher',
    birth_date = '1988-06-22',
    gender = 'F',
    address = 'Luna St., Surigao City, Surigao del Norte',
    phone = '09182345678'
WHERE uid = (SELECT uid FROM users_auth WHERE email = 'maria.santos@snsu.edu.ph');

-- Insert Admin Record
INSERT INTO admin (uid, role, status, position)
SELECT uid, 'System Administrator', 'Active', 'Head Administrator'
FROM users_auth WHERE email = 'juan.delacruz@snsu.edu.ph';

-- Insert Teacher Records
INSERT INTO teacher (uid, department, contact, status)
SELECT uid, 'Computer Studies', '09182345678', 'Active'
FROM users_auth WHERE email = 'maria.santos@snsu.edu.ph';

-- Insert Student Records
INSERT INTO student (uid, year, section, status, program, major, id_no)
VALUES
((SELECT uid FROM users_auth WHERE email = 'pedro.garcia@snsu.edu.ph'),
4, 'A', 'Active', 'BSIT', 'Web Development', '2020-0001'),
((SELECT uid FROM users_auth WHERE email = 'ana.reyes@snsu.edu.ph'),
3, 'B', 'Active', 'BSCS', 'Software Engineering', '2021-0002');

-- Insert Subject Data
INSERT INTO subject (subject_code, subject_name, units, department, status) VALUES
('CS101', 'Introduction to Computing', 3, 'Computer Studies', 'Active'),
('CS102', 'Programming 1', 3, 'Computer Studies', 'Active'),
('CS103', 'Web Development', 3, 'Computer Studies', 'Active');

-- Insert Student Subject
INSERT INTO student_subject (subject_code, subject_name, units, department, status)
SELECT subject_code, subject_name, units, department, status
FROM subject;

-- Insert System Log
INSERT INTO system_log (user_id, date, time, user_type, action, details, role_type)
SELECT uid, CURDATE(), CURTIME(), 'Admin', 'Login', 'System access', 'Administrator'
FROM users_auth WHERE email = 'juan.delacruz@snsu.edu.ph';