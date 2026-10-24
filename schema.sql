CREATE DATABASE IF NOT EXISTS blood_report_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blood_report_system;

CREATE TABLE IF NOT EXISTS report_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    fields JSON NOT NULL
) ENGINE=InnoDB;

INSERT INTO report_templates (type, name, fields)
SELECT 'FBC', 'Full Blood Count (FBC)', '[{"name":"hemoglobin","label":"Hemoglobin (g/dL)","min":0.1,"max":35,"step":0.01,"minAge":20,"ranges":{"Male":{"min":14,"max":17,"strictMax":false,"label":"14 - 17"},"Female":{"min":12,"max":15,"strictMax":false,"label":"12 - 15"}}},{"name":"white_blood_cells","label":"White Blood Cell Count (x10^9/L)","min":0.1,"max":150,"step":0.01,"minAge":20,"ranges":{"all":{"min":4.5,"max":10,"strictMax":false,"label":"4.5 - 10"}}},{"name":"red_blood_cells","label":"Red Blood Cell Count (x10^12/L)","min":0.1,"max":15,"step":0.01,"minAge":20,"ranges":{"Male":{"min":5,"max":6,"strictMax":false,"label":"5 - 6"},"Female":{"min":4,"max":5,"strictMax":false,"label":"4 - 5"}}},{"name":"platelets","label":"Platelet Count (x10^9/L)","min":1,"max":2500,"step":1,"minAge":20,"ranges":{"all":{"min":140,"max":450,"strictMax":false,"label":"140 - 450"}}},{"name":"hematocrit","label":"Hematocrit (%)","min":1,"max":100,"step":0.01,"minAge":20,"ranges":{"Male":{"min":41,"max":50,"strictMax":false,"label":"41 - 50"},"Female":{"min":36,"max":44,"strictMax":false,"label":"36 - 44"}}}]'
WHERE NOT EXISTS (SELECT 1 FROM report_templates WHERE type = 'FBC');

INSERT INTO report_templates (type, name, fields)
SELECT 'Lipid Profile', 'Lipid Profile', '[{"name":"total_cholesterol","label":"Total Cholesterol (mg/dL)","min":1,"max":1200,"step":0.01,"minAge":20,"ranges":{"all":{"min":null,"max":200,"strictMax":true,"label":"< 200"}}},{"name":"triglycerides","label":"Triglycerides (mg/dL)","min":1,"max":2500,"step":0.01,"minAge":20,"ranges":{"all":{"min":null,"max":150,"strictMax":true,"label":"< 150"}}},{"name":"hdl_cholesterol","label":"HDL Cholesterol (mg/dL)","min":1,"max":300,"step":0.01,"minAge":20,"ranges":{"Male":{"min":40,"max":null,"strictMax":false,"label":">= 40"},"Female":{"min":50,"max":null,"strictMax":false,"label":">= 50"}}},{"name":"ldl_cholesterol","label":"LDL Cholesterol (mg/dL)","min":1,"max":800,"step":0.01,"minAge":20,"ranges":{"all":{"min":null,"max":100,"strictMax":true,"label":"< 100"}}}]'
WHERE NOT EXISTS (SELECT 1 FROM report_templates WHERE type = 'Lipid Profile');

INSERT INTO report_templates (type, name, fields)
SELECT 'Blood Sugar', 'Blood Sugar', '[{"name":"fasting_blood_sugar","label":"Fasting Blood Sugar (mg/dL)","min":10,"max":1200,"step":0.01,"minAge":20,"ranges":{"all":{"min":70,"max":100,"strictMax":true,"label":"70 - < 100"}}},{"name":"postprandial_blood_sugar","label":"Postprandial Blood Sugar (mg/dL)","min":10,"max":1500,"step":0.01,"minAge":20,"ranges":{"all":{"min":70,"max":140,"strictMax":true,"label":"70 - < 140"}},"hint":"Measure 2 hours after a meal."},{"name":"hba1c","label":"HbA1c (%)","min":1,"max":25,"step":0.01,"minAge":20,"ranges":{"all":{"min":null,"max":5.7,"strictMax":true,"label":"< 5.7"}}}]'
WHERE NOT EXISTS (SELECT 1 FROM report_templates WHERE type = 'Blood Sugar');


CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL DEFAULT '',
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

SET @name_column_exists = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'doctors' AND COLUMN_NAME = 'display_name'
);
SET @update_schema = IF(
    @name_column_exists = 0,
    'ALTER TABLE doctors ADD COLUMN display_name VARCHAR(100) NOT NULL DEFAULT '''' AFTER username',
    'SELECT 1'
);
PREPARE update_schema FROM @update_schema;
EXECUTE update_schema;
DEALLOCATE PREPARE update_schema;

UPDATE doctors
SET display_name = CASE WHEN username = 'demo_doctor' THEN 'Dr. Demo' ELSE username END
WHERE display_name = '';

INSERT INTO doctors (username, display_name, password)
SELECT 'demo_doctor', 'Dr. Demo', '$2y$10$k/ZKdm/viq.gcLsDYPidsewpjMZ1/0JD8Pi.vstt4eqxyeUMGwUL6'
WHERE NOT EXISTS (SELECT 1 FROM doctors WHERE username = 'demo_doctor');

CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(20) NOT NULL,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    data TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
) ENGINE=InnoDB;
START TRANSACTION;
SET @demo_doctor_id = (SELECT id FROM doctors WHERE username = 'demo_doctor');

INSERT INTO patients (doctor_id, name, age, gender)
SELECT @demo_doctor_id, 'Nimal Perera (Demo)', 35, 'Male'
WHERE NOT EXISTS (
    SELECT 1 FROM patients WHERE doctor_id = @demo_doctor_id AND name = 'Nimal Perera (Demo)'
);
INSERT INTO patients (doctor_id, name, age, gender)
SELECT @demo_doctor_id, 'Amaya Silva (Demo)', 28, 'Female'
WHERE NOT EXISTS (
    SELECT 1 FROM patients WHERE doctor_id = @demo_doctor_id AND name = 'Amaya Silva (Demo)'
);

SET @nimal_id = (SELECT MIN(id) FROM patients WHERE doctor_id = @demo_doctor_id AND name = 'Nimal Perera (Demo)');
SET @amaya_id = (SELECT MIN(id) FROM patients WHERE doctor_id = @demo_doctor_id AND name = 'Amaya Silva (Demo)');

INSERT INTO reports (patient_id, type, data, created_at)
SELECT @nimal_id, 'FBC',
    '{"Hemoglobin (g/dL)":"14.8","White Blood Cell Count (x10^9/L)":"7.2","Red Blood Cell Count (x10^12/L)":"5.1","Platelet Count (x10^9/L)":"250","Hematocrit (%)":"44"}',
    '2026-10-20 09:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @nimal_id AND type = 'FBC' AND created_at = '2026-10-20 09:00:00'
);
INSERT INTO reports (patient_id, type, data, created_at)
SELECT @nimal_id, 'Lipid Profile',
    '{"Total Cholesterol (mg/dL)":"185","Triglycerides (mg/dL)":"120","HDL Cholesterol (mg/dL)":"52","LDL Cholesterol (mg/dL)":"109"}',
    '2026-10-22 10:30:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @nimal_id AND type = 'Lipid Profile' AND created_at = '2026-10-22 10:30:00'
);
INSERT INTO reports (patient_id, type, data, created_at)
SELECT @amaya_id, 'FBC',
    '{"Hemoglobin (g/dL)":"13.2","White Blood Cell Count (x10^9/L)":"6.4","Red Blood Cell Count (x10^12/L)":"4.6","Platelet Count (x10^9/L)":"280","Hematocrit (%)":"40"}',
    '2026-10-21 08:45:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @amaya_id AND type = 'FBC' AND created_at = '2026-10-21 08:45:00'
);
INSERT INTO reports (patient_id, type, data, created_at)
SELECT @amaya_id, 'Blood Sugar',
    '{"Fasting Blood Sugar (mg/dL)":"92","Postprandial Blood Sugar (mg/dL)":"118","HbA1c (%)":"5.3"}',
    '2026-10-23 11:15:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @amaya_id AND type = 'Blood Sugar' AND created_at = '2026-10-23 11:15:00'
);
COMMIT;
START TRANSACTION;
SET @demo_doctor_id = (SELECT id FROM doctors WHERE username = 'demo_doctor');

INSERT INTO patients (doctor_id, name, age, gender)
SELECT @demo_doctor_id, 'Kasun Fernando (Demo)', 42, 'Male'
WHERE NOT EXISTS (
    SELECT 1 FROM patients WHERE doctor_id = @demo_doctor_id AND name = 'Kasun Fernando (Demo)'
);
SET @sample_patient_id = (SELECT MIN(id) FROM patients WHERE doctor_id = @demo_doctor_id AND name = 'Kasun Fernando (Demo)');

INSERT INTO reports (patient_id, type, data, created_at)
SELECT @sample_patient_id, 'FBC',
    '{"Hemoglobin (g/dL)":"15.1","White Blood Cell Count (x10^9/L)":"7.8","Red Blood Cell Count (x10^12/L)":"5.2","Platelet Count (x10^9/L)":"265","Hematocrit (%)":"45"}',
    '2026-10-18 09:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @sample_patient_id AND type = 'FBC' AND created_at = '2026-10-18 09:00:00'
);

INSERT INTO reports (patient_id, type, data, created_at)
SELECT @sample_patient_id, 'Lipid Profile',
    '{"Total Cholesterol (mg/dL)":"210","Triglycerides (mg/dL)":"155","HDL Cholesterol (mg/dL)":"48","LDL Cholesterol (mg/dL)":"131"}',
    '2026-10-19 09:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @sample_patient_id AND type = 'Lipid Profile' AND created_at = '2026-10-19 09:00:00'
);

INSERT INTO reports (patient_id, type, data, created_at)
SELECT @sample_patient_id, 'Blood Sugar',
    '{"Fasting Blood Sugar (mg/dL)":"106","Postprandial Blood Sugar (mg/dL)":"142","HbA1c (%)":"5.8"}',
    '2026-10-20 09:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @sample_patient_id AND type = 'Blood Sugar' AND created_at = '2026-10-20 09:00:00'
);

INSERT INTO patients (doctor_id, name, age, gender)
SELECT @demo_doctor_id, 'Dilini Jayasinghe (Demo)', 31, 'Female'
WHERE NOT EXISTS (
    SELECT 1 FROM patients WHERE doctor_id = @demo_doctor_id AND name = 'Dilini Jayasinghe (Demo)'
);
SET @sample_patient_id = (SELECT MIN(id) FROM patients WHERE doctor_id = @demo_doctor_id AND name = 'Dilini Jayasinghe (Demo)');

INSERT INTO reports (patient_id, type, data, created_at)
SELECT @sample_patient_id, 'FBC',
    '{"Hemoglobin (g/dL)":"13.5","White Blood Cell Count (x10^9/L)":"6.8","Red Blood Cell Count (x10^12/L)":"4.7","Platelet Count (x10^9/L)":"295","Hematocrit (%)":"41"}',
    '2026-10-18 10:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @sample_patient_id AND type = 'FBC' AND created_at = '2026-10-18 10:00:00'
);

INSERT INTO reports (patient_id, type, data, created_at)
SELECT @sample_patient_id, 'Lipid Profile',
    '{"Total Cholesterol (mg/dL)":"175","Triglycerides (mg/dL)":"105","HDL Cholesterol (mg/dL)":"61","LDL Cholesterol (mg/dL)":"93"}',
    '2026-10-19 10:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @sample_patient_id AND type = 'Lipid Profile' AND created_at = '2026-10-19 10:00:00'
);

INSERT INTO reports (patient_id, type, data, created_at)
SELECT @sample_patient_id, 'Blood Sugar',
    '{"Fasting Blood Sugar (mg/dL)":"89","Postprandial Blood Sugar (mg/dL)":"115","HbA1c (%)":"5.2"}',
    '2026-10-20 10:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @sample_patient_id AND type = 'Blood Sugar' AND created_at = '2026-10-20 10:00:00'
);

INSERT INTO patients (doctor_id, name, age, gender)
SELECT @demo_doctor_id, 'Saman Wijesinghe (Demo)', 57, 'Male'
WHERE NOT EXISTS (
    SELECT 1 FROM patients WHERE doctor_id = @demo_doctor_id AND name = 'Saman Wijesinghe (Demo)'
);
SET @sample_patient_id = (SELECT MIN(id) FROM patients WHERE doctor_id = @demo_doctor_id AND name = 'Saman Wijesinghe (Demo)');

INSERT INTO reports (patient_id, type, data, created_at)
SELECT @sample_patient_id, 'FBC',
    '{"Hemoglobin (g/dL)":"14.2","White Blood Cell Count (x10^9/L)":"8.1","Red Blood Cell Count (x10^12/L)":"4.9","Platelet Count (x10^9/L)":"230","Hematocrit (%)":"43"}',
    '2026-10-18 11:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @sample_patient_id AND type = 'FBC' AND created_at = '2026-10-18 11:00:00'
);

INSERT INTO reports (patient_id, type, data, created_at)
SELECT @sample_patient_id, 'Lipid Profile',
    '{"Total Cholesterol (mg/dL)":"225","Triglycerides (mg/dL)":"180","HDL Cholesterol (mg/dL)":"43","LDL Cholesterol (mg/dL)":"146"}',
    '2026-10-19 11:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @sample_patient_id AND type = 'Lipid Profile' AND created_at = '2026-10-19 11:00:00'
);

INSERT INTO reports (patient_id, type, data, created_at)
SELECT @sample_patient_id, 'Blood Sugar',
    '{"Fasting Blood Sugar (mg/dL)":"118","Postprandial Blood Sugar (mg/dL)":"165","HbA1c (%)":"6.1"}',
    '2026-10-20 11:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @sample_patient_id AND type = 'Blood Sugar' AND created_at = '2026-10-20 11:00:00'
);

INSERT INTO patients (doctor_id, name, age, gender)
SELECT @demo_doctor_id, 'Tharushi De Silva (Demo)', 24, 'Female'
WHERE NOT EXISTS (
    SELECT 1 FROM patients WHERE doctor_id = @demo_doctor_id AND name = 'Tharushi De Silva (Demo)'
);
SET @sample_patient_id = (SELECT MIN(id) FROM patients WHERE doctor_id = @demo_doctor_id AND name = 'Tharushi De Silva (Demo)');

INSERT INTO reports (patient_id, type, data, created_at)
SELECT @sample_patient_id, 'FBC',
    '{"Hemoglobin (g/dL)":"12.9","White Blood Cell Count (x10^9/L)":"5.9","Red Blood Cell Count (x10^12/L)":"4.5","Platelet Count (x10^9/L)":"310","Hematocrit (%)":"39"}',
    '2026-10-18 12:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @sample_patient_id AND type = 'FBC' AND created_at = '2026-10-18 12:00:00'
);

INSERT INTO reports (patient_id, type, data, created_at)
SELECT @sample_patient_id, 'Lipid Profile',
    '{"Total Cholesterol (mg/dL)":"162","Triglycerides (mg/dL)":"90","HDL Cholesterol (mg/dL)":"58","LDL Cholesterol (mg/dL)":"86"}',
    '2026-10-19 12:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @sample_patient_id AND type = 'Lipid Profile' AND created_at = '2026-10-19 12:00:00'
);

INSERT INTO reports (patient_id, type, data, created_at)
SELECT @sample_patient_id, 'Blood Sugar',
    '{"Fasting Blood Sugar (mg/dL)":"86","Postprandial Blood Sugar (mg/dL)":"110","HbA1c (%)":"5.0"}',
    '2026-10-20 12:00:00'
WHERE NOT EXISTS (
    SELECT 1 FROM reports WHERE patient_id = @sample_patient_id AND type = 'Blood Sugar' AND created_at = '2026-10-20 12:00:00'
);
COMMIT;

