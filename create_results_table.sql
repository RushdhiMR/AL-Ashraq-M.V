-- ============================================================
-- SQL Script to Create Exam Results Table
-- Run this in your database to create the results system
-- ============================================================

CREATE TABLE IF NOT EXISTS exam_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    student_email VARCHAR(255) NOT NULL,
    class_grade VARCHAR(50) NOT NULL,
    exam_name VARCHAR(255) NOT NULL,
    exam_date DATE,
    subject VARCHAR(255) NOT NULL,
    marks_obtained DECIMAL(6,2) NOT NULL,
    marks_total DECIMAL(6,2) NOT NULL DEFAULT 100,
    grade VARCHAR(10),
    remarks TEXT,
    added_by VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_student (student_id),
    INDEX idx_exam (exam_name),
    INDEX idx_grade (class_grade),
    INDEX idx_subject (subject),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data (optional - remove if you don't want sample data)
-- This assumes you have students with IDs 1, 2, 3 in your database
INSERT INTO exam_results (student_id, student_name, student_email, class_grade, exam_name, exam_date, subject, marks_obtained, marks_total, grade, added_by) 
SELECT 
    id,
    full_name,
    email,
    class_grade,
    'Term 1 - 2024',
    '2024-03-15',
    'Mathematics',
    FLOOR(60 + RAND() * 40),
    100,
    CASE 
        WHEN FLOOR(60 + RAND() * 40) >= 85 THEN 'A+'
        WHEN FLOOR(60 + RAND() * 40) >= 75 THEN 'A'
        WHEN FLOOR(60 + RAND() * 40) >= 65 THEN 'B+'
        WHEN FLOOR(60 + RAND() * 40) >= 55 THEN 'B'
        ELSE 'C'
    END,
    'System Admin'
FROM students 
WHERE status = 'approved'
LIMIT 5;
