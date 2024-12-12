CREATE TABLE IF NOT EXISTS `programs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `program_code` VARCHAR(20) NOT NULL UNIQUE,
    `program_name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `department` VARCHAR(100) NOT NULL,
    `total_units` INT DEFAULT 0,
    `years_to_complete` INT DEFAULT 4,
    `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert all academic programs from academics.php
INSERT INTO `programs` (`program_code`, `program_name`, `description`, `department`, `total_units`, `years_to_complete`) VALUES
-- College of Engineering and Information Technology
('BSCE', 'Bachelor of Science in Civil Engineering', 'Civil Engineering Program', 'College of Engineering and Information Technology', 155, 4),
('BSEE', 'Bachelor of Science in Electrical Engineering', 'Electrical Engineering Program', 'College of Engineering and Information Technology', 155, 4),
('BSECE', 'Bachelor of Science in Electronics Engineering', 'Electronics Engineering Program', 'College of Engineering and Information Technology', 155, 4),
('BSCpE', 'Bachelor of Science in Computer Engineering', 'Computer Engineering Program', 'College of Engineering and Information Technology', 155, 4),
('BSCS', 'Bachelor of Science in Computer Science', 'Computer Science Program', 'College of Engineering and Information Technology', 155, 4),
('BSIT', 'Bachelor of Science in Information Technology', 'Information Technology Program', 'College of Engineering and Information Technology', 155, 4),
('BSIS', 'Bachelor of Science in Information Systems', 'Information Systems Program', 'College of Engineering and Information Technology', 155, 4),

-- College of Teacher Education
('BEEd', 'Bachelor of Elementary Education', 'Elementary Education Program', 'College of Teacher Education', 155, 4),
('BPEd', 'Bachelor of Physical Education', 'Physical Education Program', 'College of Teacher Education', 155, 4),
('BTVTEd', 'Bachelor of Technical-Vocational Teacher Education', 'Technical-Vocational Teacher Education with Major in Food and Services Management', 'College of Teacher Education', 155, 4),
('BSEd', 'Bachelor of Secondary Education', 'Secondary Education with Majors in English, Filipino, Mathematics, & Sciences', 'College of Teacher Education', 155, 4),

-- College of Technology
('BET', 'Bachelor of Engineering Technology', 'Engineering Technology Program', 'College of Technology', 155, 4),
('BAET', 'Bachelor of Automotive Engineering Technology', 'Automotive Engineering Technology Program', 'College of Technology', 155, 4),
('BEET', 'Bachelor of Electrical Engineering Technology', 'Electrical Engineering Technology Program', 'College of Technology', 155, 4),
('BEXET', 'Bachelor of Electronics Engineering Technology', 'Electronics Engineering Technology Program', 'College of Technology', 155, 4),
('BMET', 'Bachelor of Mechanical Engineering Technology', 'Mechanical Engineering Technology Program', 'College of Technology', 155, 4),
('BMET-MT', 'Bachelor of Mechanical Engineering Technology - Mechanical Technology', 'BMET with concentration in Mechanical Technology', 'College of Technology', 155, 4),
('BMET-RACT', 'Bachelor of Mechanical Engineering Technology - RACT', 'BMET with concentration in Refrigeration and Air-conditioning Technology', 'College of Technology', 155, 4),
('BMET-WAFT', 'Bachelor of Mechanical Engineering Technology - WAFT', 'BMET with concentration in Welding and Fabrication Technology', 'College of Technology', 155, 4),
('BIndTech', 'Bachelor in Industrial Technology', 'Industrial Technology Program', 'College of Technology', 155, 4),
('BIndTech-ADT', 'Bachelor in Industrial Technology - Architectural Drafting', 'Industrial Technology with Major in Architectural Drafting', 'College of Technology', 155, 4),
('BIndTech-AT', 'Bachelor in Industrial Technology - Automotive', 'Industrial Technology with Major in Automotive Technology', 'College of Technology', 155, 4),
('BIndTech-ELT', 'Bachelor in Industrial Technology - Electrical', 'Industrial Technology with Major in Electrical Technology', 'College of Technology', 155, 4),
('BIndTech-ELEX', 'Bachelor in Industrial Technology - Electronics', 'Industrial Technology with Major in Electronics Technology', 'College of Technology', 155, 4),
('BIndTech-MT', 'Bachelor in Industrial Technology - Mechanical', 'Industrial Technology with Major in Mechanical Technology', 'College of Technology', 155, 4),
('HVACR', 'Bachelor in Industrial Technology - HVACR', 'Industrial Technology with Major in Heating, Ventilating & Air-conditioning Technology', 'College of Technology', 155, 4),
('WAFT', 'Bachelor in Industrial Technology - WAFT', 'Industrial Technology with Major in Welding & Fabrication Technology', 'College of Technology', 155, 4),
('BSHM', 'Bachelor of Science in Hospitality Management', 'Hospitality Management Program', 'College of Technology', 155, 4),
('BSTM', 'Bachelor of Science in Tourism Management', 'Tourism Management Program', 'College of Technology', 155, 4),

-- College of Arts and Sciences
('BSMATH', 'Bachelor of Science in Mathematics', 'Mathematics Program', 'College of Arts and Sciences', 155, 4),
('BSENV', 'Bachelor of Science in Environmental Science', 'Environmental Science Program', 'College of Arts and Sciences', 155, 4),
('ABENG', 'Bachelor of Arts in English Language', 'English Language Program', 'College of Arts and Sciences', 155, 4);