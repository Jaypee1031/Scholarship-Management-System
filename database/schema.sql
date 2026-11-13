-- ============================================================
-- QSU OSAS Scholarship & Student Assistance Application System
-- Database Schema + Seed Data
-- QSU-SCH-F003 Rev. 00 (Dec. 27, 2024)
-- ============================================================

CREATE DATABASE IF NOT EXISTS scholarship_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE scholarship_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS form_fields, system_settings, audit_logs, notifications, application_status_history, evaluations, documents, requirements, educational_assistance, family_members, sa_positions, student_assistant_applications, academic_scholar_applications, scholarship_applications, applications, application_periods, application_types, admins, students, users, roles;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    permissions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    user_type ENUM('student','admin') NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    login_attempts INT DEFAULT 0,
    locked_until DATETIME NULL,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    student_id VARCHAR(50) UNIQUE NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    extension_name VARCHAR(20),
    degree_program VARCHAR(200),
    year_level TINYINT UNSIGNED DEFAULT 1,
    lrn_number VARCHAR(50),
    sex ENUM('Male','Female'),
    civil_status ENUM('Single','Married','Separated'),
    is_solo_parent TINYINT(1) DEFAULT 0,
    citizenship VARCHAR(100) DEFAULT 'Filipino',
    birthdate DATE,
    place_of_birth VARCHAR(200),
    physical_disability VARCHAR(200),
    ethnic_affiliation VARCHAR(100),
    street_barangay VARCHAR(255),
    municipality VARCHAR(100),
    province VARCHAR(100),
    zip_code VARCHAR(10),
    contact_number VARCHAR(20),
    email_address VARCHAR(150),
    is_dormitory TINYINT(1) DEFAULT 0,
    registering_as ENUM('New Student','Old Student','Transfer In','Lateral Transfer','Cross Enrollee'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    last_name VARCHAR(100),
    first_name VARCHAR(100),
    middle_name VARCHAR(100),
    office VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE application_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    is_enabled TINYINT(1) DEFAULT 0,
    start_date DATE NULL,
    end_date DATE NULL,
    max_applications INT NULL,
    requirements TEXT,
    instructions TEXT,
    form_configuration TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE application_periods (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    semester ENUM('First','Second','Summer'),
    academic_year VARCHAR(20),
    start_date DATE,
    end_date DATE,
    is_active TINYINT(1) DEFAULT 0,
    allow_reapplication TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_number VARCHAR(50) UNIQUE NOT NULL,
    student_id INT NOT NULL,
    application_type_id INT NOT NULL,
    application_period_id INT NULL,
    status ENUM('Draft','Submitted','Under Review','For Revision','Qualified','Disqualified','Approved','Rejected') DEFAULT 'Draft',
    submitted_at DATETIME NULL,
    assigned_admin_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_student_period (student_id, application_period_id),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (application_type_id) REFERENCES application_types(id),
    FOREIGN KEY (application_period_id) REFERENCES application_periods(id)
) ENGINE=InnoDB;

CREATE TABLE scholarship_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL UNIQUE,
    household_per_capita_income DECIMAL(12,2) NULL,
    dswd_household_number VARCHAR(100),
    has_other_assistance TINYINT(1) DEFAULT 0,
    certification_confirmed TINYINT(1) DEFAULT 0,
    certification_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE academic_scholar_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL UNIQUE,
    scholarship_type ENUM('Academic','Service') NOT NULL DEFAULT 'Academic',
    service_category VARCHAR(100),
    service_category_other VARCHAR(200),
    semester ENUM('First','Second') NULL,
    academic_year VARCHAR(20),
    certification_confirmed TINYINT(1) DEFAULT 0,
    certification_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE student_assistant_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL UNIQUE,
    preferred_position_id INT NULL,
    preferred_office VARCHAR(200),
    preferred_schedule VARCHAR(200),
    skills TEXT,
    previous_experience TEXT,
    reason_for_applying TEXT,
    sa_status ENUM('For Screening','Shortlisted','Interview','Qualified','Not Qualified','Assigned') DEFAULT 'For Screening',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE sa_positions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    office VARCHAR(200) NOT NULL,
    available_slots INT DEFAULT 1,
    schedule VARCHAR(200),
    qualifications TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE family_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    relationship ENUM('Father','Mother','Sibling') NOT NULL,
    last_name VARCHAR(100),
    first_name VARCHAR(100),
    middle_name VARCHAR(100),
    age TINYINT UNSIGNED NULL,
    ethnic_affiliation VARCHAR(100),
    occupation VARCHAR(200),
    member_status ENUM('Living','Deceased') DEFAULT 'Living',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE educational_assistance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    assistance_type VARCHAR(200),
    grantee VARCHAR(200),
    institution_agency VARCHAR(200),
    amount DECIMAL(12,2) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE requirements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_type_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    is_required TINYINT(1) DEFAULT 1,
    accepted_formats VARCHAR(100) DEFAULT 'pdf,jpg,jpeg,png',
    max_size_mb INT DEFAULT 5,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_type_id) REFERENCES application_types(id)
) ENGINE=InnoDB;

CREATE TABLE documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    requirement_id INT NULL,
    original_filename VARCHAR(255),
    stored_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,
    upload_status ENUM('Pending','Verified','Rejected') DEFAULT 'Pending',
    remarks VARCHAR(500),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (requirement_id) REFERENCES requirements(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE evaluations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    evaluator_admin_id INT NOT NULL,
    documents_status ENUM('Complete','Incomplete','For Revision') NULL,
    decision ENUM('For Revision','Qualified','Disqualified','Approved','Rejected') NULL,
    remarks TEXT,
    disqualification_reason TEXT,
    required_corrections TEXT,
    evaluated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id),
    FOREIGN KEY (evaluator_admin_id) REFERENCES admins(id)
) ENGINE=InnoDB;

CREATE TABLE application_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50) NOT NULL,
    changed_by_admin_id INT NULL,
    changed_by_student_id INT NULL,
    remarks VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id),
    FOREIGN KEY (changed_by_admin_id) REFERENCES admins(id) ON DELETE SET NULL,
    FOREIGN KEY (changed_by_student_id) REFERENCES students(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    notification_type VARCHAR(50) DEFAULT 'general',
    related_application_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (related_application_id) REFERENCES applications(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NULL,
    student_id INT NULL,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(100),
    record_id INT NULL,
    field_name VARCHAR(100) NULL,
    old_value TEXT NULL,
    new_value TEXT NULL,
    description TEXT,
    ip_address VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type VARCHAR(50) DEFAULT 'text',
    description VARCHAR(300),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE form_fields (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_type_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    field_label VARCHAR(200) NOT NULL,
    field_type ENUM('text','number','date','dropdown','radio','checkbox','file','textarea') NOT NULL,
    field_options TEXT,
    is_required TINYINT(1) DEFAULT 0,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    section_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_type_id) REFERENCES application_types(id)
) ENGINE=InnoDB;

-- SEED DATA
INSERT INTO roles (name, slug, permissions) VALUES
('Super Admin', 'super_admin', '["view_applications","edit_applications","delete_applications","evaluate_applications","approve_applications","manage_application_types","manage_users","view_reports","export_reports","view_audit_logs","send_notifications","manage_requirements","override_applications","manage_periods","assign_applications","manage_sa_positions","reset_passwords"]'),
('Scholarship Staff', 'scholarship_staff', '["view_applications","edit_applications","review_documents","add_remarks","update_status","send_notifications","manage_requirements"]'),
('Scholarship Coordinator', 'scholarship_coordinator', '["view_applications","edit_applications","evaluate_applications","approve_applications","add_remarks","update_status","view_reports","export_reports","send_notifications","assign_applications"]'),
('OSAS Staff', 'osas_staff', '["view_applications","add_remarks","update_limited_fields","send_notifications","view_reports"]');
-- Admin users (password for all: Admin@123 / Staff@123 etc - all use same hash for testing: password = "Admin@123")
INSERT INTO users (username, password, email, user_type) VALUES
('admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@qsu.edu.ph', 'admin'),
('staff01', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff01@qsu.edu.ph', 'admin'),
('coordinator01', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'coordinator01@qsu.edu.ph', 'admin'),
('osas01', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'osas01@qsu.edu.ph', 'admin');

INSERT INTO admins (user_id, role_id, last_name, first_name, office) VALUES
(1, 1, 'Administrator', 'QSU', 'Office of Student Affairs and Services'),
(2, 2, 'Reyes', 'Maria', 'Scholarship Office'),
(3, 3, 'Santos', 'Jose', 'Scholarship Office'),
(4, 4, 'Cruz', 'Ana', 'Office of Student Affairs and Services');

-- Student users (password: Admin@123)
INSERT INTO users (username, password, email, user_type) VALUES
('QSU-2024-001', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dela.cruz@student.qsu.edu.ph', 'student'),
('QSU-2024-002', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'garcia@student.qsu.edu.ph', 'student'),
('QSU-2024-003', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mendoza@student.qsu.edu.ph', 'student'),
('QSU-2024-004', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'torres@student.qsu.edu.ph', 'student'),
('QSU-2024-005', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'villanueva@student.qsu.edu.ph', 'student');

INSERT INTO students (user_id, student_id, last_name, first_name, middle_name, degree_program, year_level, sex, civil_status, citizenship, birthdate, place_of_birth, municipality, province, contact_number, email_address, registering_as) VALUES
(5, 'QSU-2024-001', 'Dela Cruz', 'Juan', 'Santos', 'Bachelor of Science in Information Technology', 2, 'Male', 'Single', 'Filipino', '2003-05-15', 'Diffun, Quirino', 'Diffun', 'Quirino', '09123456781', 'dela.cruz@student.qsu.edu.ph', 'Old Student'),
(6, 'QSU-2024-002', 'Garcia', 'Maria', 'Reyes', 'Bachelor of Science in Education', 3, 'Female', 'Single', 'Filipino', '2002-08-22', 'Cabarroguis, Quirino', 'Cabarroguis', 'Quirino', '09123456782', 'garcia@student.qsu.edu.ph', 'Old Student'),
(7, 'QSU-2024-003', 'Mendoza', 'Pedro', 'Luna', 'Bachelor of Science in Business Administration', 1, 'Male', 'Single', 'Filipino', '2005-01-10', 'Maddela, Quirino', 'Maddela', 'Quirino', '09123456783', 'mendoza@student.qsu.edu.ph', 'New Student'),
(8, 'QSU-2024-004', 'Torres', 'Ana', 'Cruz', 'Bachelor of Science in Nursing', 4, 'Female', 'Single', 'Filipino', '2001-11-30', 'Aglipay, Quirino', 'Aglipay', 'Quirino', '09123456784', 'torres@student.qsu.edu.ph', 'Old Student'),
(9, 'QSU-2024-005', 'Villanueva', 'Jose', 'Ramos', 'Bachelor of Science in Civil Engineering', 2, 'Male', 'Single', 'Filipino', '2003-03-20', 'Nagtipunan, Quirino', 'Nagtipunan', 'Quirino', '09123456785', 'villanueva@student.qsu.edu.ph', 'Old Student');

INSERT INTO application_types (name, slug, description, is_enabled, start_date, end_date, instructions) VALUES
('Student Assistant', 'student_assistant', 'Apply for available Student Assistant opportunities under OSAS. Work part-time in a university office and receive a scholarship allowance.', 1, '2025-08-01', '2025-09-30', 'Please fill out all required fields completely. Ensure that your uploaded documents are clear and legible. Incomplete applications will not be processed.'),
('Academic Achiever', 'academic_achiever', 'Apply as an Academic or Service Scholar based on your academic achievements and qualifications set by QSU.', 1, '2025-08-01', '2025-09-30', 'Academic Scholarship is open to students with outstanding academic performance. Service Scholarship is open to students who are active members of recognized QSU organizations.'),
('Scholarship / Financial Assistance', 'scholarship', 'Apply for available scholarships and financial assistance programs offered by QSU for qualified students in need.', 1, '2025-08-01', '2025-09-30', 'This scholarship is intended for financially-challenged students. Provide complete and accurate family background information. False information will result in disqualification.');

INSERT INTO application_periods (name, semester, academic_year, start_date, end_date, is_active) VALUES
('First Semester AY 2025-2026', 'First', '2025-2026', '2025-08-01', '2025-09-30', 1);

INSERT INTO sa_positions (title, office, available_slots, schedule, qualifications, is_active) VALUES
('Library Assistant', 'University Library', 3, 'Monday-Friday, 8:00 AM - 12:00 PM or 1:00 PM - 5:00 PM', 'Must be in good academic standing. Preferably a student with Library Science background.', 1),
('Computer Laboratory Assistant', 'Information Technology Department', 2, 'Monday-Friday, flexible schedule (4 hours/day)', 'Must have basic computer knowledge. IT-related course preferred.', 1),
('Office Assistant - Registrar', 'Office of the University Registrar', 5, 'Monday-Friday, 8:00 AM - 12:00 PM', 'Good communication skills. Attention to detail. Any course accepted.', 1),
('Office Assistant - OSAS', 'Office of Student Affairs and Services', 2, 'Monday-Friday, 1:00 PM - 5:00 PM', 'Good interpersonal skills. Willing to assist students.', 1),
('Research Assistant', 'Research and Development Center', 1, 'Monday-Friday, flexible (4 hours/day)', 'With research background. Graduate student or 4th year preferred.', 1);

INSERT INTO requirements (application_type_id, name, description, is_required, accepted_formats, max_size_mb, display_order, is_active) VALUES
-- Student Assistant
(1, 'Certificate of Enrollment (COE)', 'Official Certificate of Enrollment (COE) for the current semester', 1, 'pdf,jpg,jpeg,png', 5, 1, 1),
(1, 'Certificate of Grades (COG)', 'Official Certificate of Grades (COG) / Copy of Grades from previous semester', 1, 'pdf,jpg,jpeg,png', 5, 2, 1),

-- Academic Achiever
(2, 'Certificate of Enrollment (COE)', 'Official Certificate of Enrollment (COE) for the current semester', 1, 'pdf,jpg,jpeg,png', 5, 1, 1),
(2, 'Certificate of Grades (COG)', 'Official Certificate of Grades (COG) / Copy of Grades from previous semester', 1, 'pdf,jpg,jpeg,png', 5, 2, 1),

-- Scholarship / Financial Assistance
(3, 'Certificate of Enrollment (COE)', 'Official Certificate of Enrollment (COE) for the current semester', 1, 'pdf,jpg,jpeg,png', 5, 1, 1),
(3, 'Certificate of Grades (COG)', 'Official Certificate of Grades (COG) / Copy of Grades from previous semester', 1, 'pdf,jpg,jpeg,png', 5, 2, 1);

INSERT INTO applications (application_number, student_id, application_type_id, application_period_id, status) VALUES
('QSU-2025-SCH-00001', 1, 3, 1, 'Draft'),
('QSU-2025-ACA-00002', 2, 2, 1, 'Submitted');

INSERT INTO scholarship_applications (application_id, household_per_capita_income, has_other_assistance, certification_confirmed) VALUES
(1, 2500.00, 0, 0);

INSERT INTO academic_scholar_applications (application_id, scholarship_type, semester, academic_year, certification_confirmed, certification_date) VALUES
(2, 'Academic', 'First', '2025-2026', 1, '2025-08-15');

INSERT INTO family_members (application_id, relationship, last_name, first_name, age, occupation, member_status) VALUES
(1, 'Father', 'Dela Cruz', 'Roberto', 45, 'Farmer', 'Living'),
(1, 'Mother', 'Dela Cruz', 'Luz', 42, 'Housewife', 'Living');

INSERT INTO application_status_history (application_id, old_status, new_status, changed_by_student_id, remarks) VALUES
(1, NULL, 'Draft', 1, 'Application created'),
(2, NULL, 'Draft', 2, 'Application created'),
(2, 'Draft', 'Submitted', 2, 'Application submitted by student');

INSERT INTO notifications (student_id, title, message, notification_type, related_application_id) VALUES
(1, 'Application Created', 'Your Scholarship/Financial Assistance application (QSU-2025-SCH-00001) has been created. Please complete all required fields and submit.', 'general', 1),
(2, 'Application Submitted', 'Your Academic Achiever application (QSU-2025-ACA-00002) has been successfully submitted. You will be notified of updates.', 'success', 2);

INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'QSU OSAS Scholarship System', 'text', 'System name'),
('academic_year', '2025-2026', 'text', 'Current academic year'),
('semester', 'First', 'text', 'Current semester'),
('max_login_attempts', '5', 'number', 'Maximum login attempts before lockout'),
('session_timeout', '3600', 'number', 'Session timeout in seconds'),
('school_name', 'Quirino State University', 'text', 'University name'),
('school_address', 'Diffun, Quirino, Philippines', 'text', 'University address'),
('allow_student_registration', '1', 'boolean', 'Allow students to self-register');
