-- SQL schema additions for custom quizzes and quiz type selection

-- 1) Add quiz_type column to course_materials to control AI vs custom quiz
ALTER TABLE course_materials
ADD COLUMN IF NOT EXISTS quiz_type ENUM('ai', 'custom') NOT NULL DEFAULT 'ai';

-- 2) Table to store lecturer-authored quiz questions per material
CREATE TABLE IF NOT EXISTS material_quiz_questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT NOT NULL,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option ENUM('A', 'B', 'C', 'D') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (material_id) REFERENCES course_materials(material_id) ON DELETE CASCADE
);

