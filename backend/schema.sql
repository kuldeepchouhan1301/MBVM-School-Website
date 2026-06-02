CREATE DATABASE IF NOT EXISTS mbvm_school
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE mbvm_school;

CREATE TABLE IF NOT EXISTS contact_enquiries (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL,
  subject VARCHAR(180) NOT NULL,
  message TEXT NOT NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admission_enquiries (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(160) NOT NULL,
  nationality VARCHAR(80) NOT NULL,
  id_card VARCHAR(255) NOT NULL,
  dob DATE NOT NULL,
  class_name VARCHAR(40) NOT NULL,
  session_year VARCHAR(40) NOT NULL,
  father_name VARCHAR(160) NOT NULL,
  father_mobile VARCHAR(30) NOT NULL,
  mother_name VARCHAR(160) NOT NULL,
  mother_mobile VARCHAR(30) NOT NULL,
  email VARCHAR(180) NOT NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE admission_enquiries
  ADD COLUMN IF NOT EXISTS id_card VARCHAR(255) NOT NULL AFTER nationality;

ALTER TABLE admission_enquiries
  DROP COLUMN IF EXISTS photo;

CREATE TABLE IF NOT EXISTS student_results (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_name VARCHAR(160) NOT NULL,
  registration_no VARCHAR(80) NOT NULL,
  class_name VARCHAR(40) NOT NULL,
  session_year VARCHAR(40) NOT NULL,
  roll_no VARCHAR(40) NULL,
  total_marks DECIMAL(7,2) NOT NULL,
  obtained_marks DECIMAL(7,2) NOT NULL,
  percentage DECIMAL(5,2) NOT NULL,
  grade VARCHAR(20) NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'Pass',
  remarks TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_student_result (registration_no, class_name, session_year)
);

CREATE TABLE IF NOT EXISTS school_events (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  event_date DATE NOT NULL,
  event_time VARCHAR(30) NULL,
  description TEXT NOT NULL,
  youtube_url VARCHAR(255) NULL,
  image_path VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS gallery_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NULL,
  category VARCHAR(80) NULL,
  image_path VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS teacher_profiles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  designation VARCHAR(120) NOT NULL,
  subject VARCHAR(120) NULL,
  qualification VARCHAR(160) NULL,
  bio TEXT NULL,
  photo_path VARCHAR(255) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO teacher_profiles (name, designation, subject, qualification, bio, photo_path, sort_order)
SELECT 'Teacher', 'Teacher', 'General Studies', '', 
'Dedicated faculty supporting students with discipline, care, and regular guidance.',
 '/frontend/uploads/teacher/210x220-img-1.jpg', 10
WHERE NOT EXISTS (SELECT 1 FROM teacher_profiles LIMIT 1);

INSERT INTO teacher_profiles (name, designation, subject, qualification, bio, photo_path, sort_order)
SELECT 'Teacher', 'Teacher', 'Hindi Medium', '', 
'Dedicated faculty supporting students with discipline, care, and regular guidance.', 
'/frontend/uploads/teacher/210x220-img-2.jpg', 20
WHERE (SELECT COUNT(*) FROM teacher_profiles) = 1;

INSERT INTO teacher_profiles (name, designation, subject, qualification, bio, photo_path, sort_order)
SELECT 'Teacher', 'Assistant', 'Primary Classes', '', 
'Dedicated faculty supporting students with discipline, care, and regular guidance.',
 '/frontend/uploads/teacher/210x220-img-3.jpg', 30
WHERE (SELECT COUNT(*) FROM teacher_profiles) = 2;
