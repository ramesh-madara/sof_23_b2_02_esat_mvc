USE blood_report_system;
ALTER TABLE doctors ADD COLUMN display_name VARCHAR(100) NOT NULL DEFAULT '' AFTER username;
UPDATE doctors SET display_name = CASE WHEN username = 'demo_doctor' THEN 'Dr. Demo' ELSE username END WHERE display_name = '';
