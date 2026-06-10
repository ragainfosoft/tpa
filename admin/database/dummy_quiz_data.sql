-- =====================================================
-- TPA IMS — Dummy Quiz Data
-- =====================================================

USE `tpa_ims`;

-- 1. Seed Batches (if not exists)
INSERT IGNORE INTO `batches` (`id`, `name`, `course_type`, `year_group`, `teacher_id`, `centre`, `day_of_week`, `start_time`, `end_time`, `is_active`) VALUES
(1, 'Year 5 Maths Booster', 'ks2', 'Year 5', NULL, 'Romford', 'Saturday', '10:00:00', '12:00:00', 1),
(2, '11+ English Advanced', '11plus', 'Year 5', NULL, 'Chelmsford', 'Sunday', '09:00:00', '11:00:00', 1),
(3, 'GCSE Science Prep', 'gcse', 'Year 11', NULL, 'Online', 'Wednesday', '18:00:00', '20:00:00', 1);

-- 2. Seed Quiz Sets
INSERT INTO `quiz_sets` (`id`, `title`, `subject_id`, `year_group`, `lesson`, `description`, `time_limit_min`, `attempt_limit`, `pass_mark_pct`, `is_active`, `created_by`) VALUES
(1, 'Fractions and Decimals Quiz', 1, 'Year 5', 'Fractions', 'A quick quiz to test your knowledge of fractions and their decimal equivalents.', 15, 2, 70.00, 1, 1),
(2, 'English Grammar Basics', 2, 'Year 5', 'Grammar', 'Test your understanding of nouns, verbs, and adjectives.', 10, 3, 60.00, 1, 1),
(3, '11+ Verbal Reasoning Practice', 5, 'Year 5', 'Verbal Reasoning', 'Practice questions for 11+ Verbal Reasoning.', 20, 1, 80.00, 1, 1);

-- 3. Seed Quiz Questions
-- Quiz 1 Questions
INSERT INTO `quiz_questions` (`quiz_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct`, `explanation`) VALUES
(1, 'What is 1/2 as a decimal?', '0.2', '0.5', '1.2', '0.05', 'b', '1 divided by 2 is 0.5.'),
(1, 'Which is larger: 3/4 or 0.7?', '3/4', '0.7', 'They are equal', 'None of these', 'a', '3/4 is 0.75, which is larger than 0.7.'),
(1, 'What is 0.25 as a fraction?', '1/2', '1/3', '1/4', '1/5', 'c', '0.25 is 25/100, which simplifies to 1/4.');

-- Quiz 2 Questions
INSERT INTO `quiz_questions` (`quiz_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct`, `explanation`) VALUES
(2, 'Which of these is a noun?', 'Run', 'Blue', 'Cat', 'Quickly', 'c', 'A cat is a person, place, or thing.'),
(2, 'Identify the verb in this sentence: "The boy sleeps."', 'The', 'boy', 'sleeps', '.', 'c', 'Sleeps is the action word.'),
(2, 'Which word is an adjective?', 'Happily', 'Cloud', 'Large', 'Jump', 'c', 'Large describes a noun.');

-- Quiz 3 Questions
INSERT INTO `quiz_questions` (`quiz_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct`, `explanation`) VALUES
(3, 'Find the missing letter: "B A _ D"', 'C', 'N', 'R', 'S', 'b', 'BAND is a common word.'),
(3, 'Which word is the odd one out?', 'Apple', 'Banana', 'Carrot', 'Orange', 'c', 'Carrot is a vegetable, others are fruits.');

-- 4. Seed Quiz Assignments
INSERT INTO `quiz_assignments` (`quiz_id`, `batch_id`, `assigned_by`, `due_date`) VALUES
(1, 1, 1, DATE_ADD(CURDATE(), INTERVAL 7 DAY)),
(2, 2, 1, DATE_ADD(CURDATE(), INTERVAL 5 DAY)),
(3, 2, 1, DATE_ADD(CURDATE(), INTERVAL 10 DAY));
