-- ============================================================
-- SQL Script to Update Grade 12 & 13 to 1st Year & 2nd Year A/L
-- Run this script in your database to update existing records
-- ============================================================

-- Update students table
UPDATE students 
SET class_grade = '1st Year A/L' 
WHERE class_grade = 'Grade 12';

UPDATE students 
SET class_grade = '2nd Year A/L' 
WHERE class_grade = 'Grade 13';

-- Update admission_inquiries table
UPDATE admission_inquiries 
SET applying_grade = '1st Year A/L' 
WHERE applying_grade = 'Grade 12';

UPDATE admission_inquiries 
SET applying_grade = '2nd Year A/L' 
WHERE applying_grade = 'Grade 13';

-- Update timetable_entries table
UPDATE timetable_entries 
SET grade = '1st Year A/L' 
WHERE grade = 'Grade 12';

UPDATE timetable_entries 
SET grade = '2nd Year A/L' 
WHERE grade = 'Grade 13';

-- Verify the changes
SELECT 'Students with new grade format:' as Info;
SELECT class_grade, COUNT(*) as count 
FROM students 
WHERE class_grade IN ('1st Year A/L', '2nd Year A/L')
GROUP BY class_grade;

SELECT 'Admission inquiries with new grade format:' as Info;
SELECT applying_grade, COUNT(*) as count 
FROM admission_inquiries 
WHERE applying_grade IN ('1st Year A/L', '2nd Year A/L')
GROUP BY applying_grade;

SELECT 'Timetable entries with new grade format:' as Info;
SELECT grade, COUNT(*) as count 
FROM timetable_entries 
WHERE grade IN ('1st Year A/L', '2nd Year A/L')
GROUP BY grade;
