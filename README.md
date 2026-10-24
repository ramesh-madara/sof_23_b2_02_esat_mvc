# Blood Report Generator

A plain PHP MVC application for doctors to manage patients and blood reports.

- `index.php` routes requests using the `action` query parameter.
- `controller/` handles authentication, accounts, patients, and reports.
- `model/` uses PDO with MySQL; reports belong to patients and patients belong to doctors.
- `view/` contains server-rendered HTML and styles.
- `assets/libs/fpdf.php` provides PDF export.

Supported reports: Full Blood Count (FBC), Lipid Profile, and Blood Sugar. Doctors can register, log in, change passwords, create/search/filter patients, and create/view/edit/delete/export reports.

## Local setup

URL: http://localhost/sof_23_b2_02_esat_mvc/

Runtime: XAMPP Apache, PHP 8.2, and MySQL/MariaDB. No Composer or Node dependencies are required. Place the project folder in `C:/xampp/htdocs/sof_23_b2_02_esat_mvc`.

Database settings are in `model/Database.php`:

- Host: `127.0.0.1`
- Database: `blood_report_system`
- User: `root`
- Password: empty (local XAMPP default)

Import `schema.sql` to create the database and its `doctors`, `patients`, and `reports` tables. It also creates a test doctor account: username `demo_doctor`, password `Doctor123!`. These credentials appear below the login form. Re-importing the schema preserves an existing account and its password. Use the Register page to create additional doctor accounts.

The test account includes six fictional patients and 16 blood reports: Nimal Perera and Amaya Silva have two reports each; Kasun Fernando, Dilini Jayasinghe, Saman Wijesinghe, and Tharushi De Silva each have FBC, Lipid Profile, and Blood Sugar reports. All sample patient names are marked (Demo). Re-importing the unchanged sample data does not create duplicates.

Doctor accounts have an editable display name (not unique and not a key), separate from their login username. Set it during registration or on the Account page. Import `schema.sql` for both new and existing databases; it adds the display-name field if missing and fills blank names while preserving existing names.

## Report reference ranges

`report_templates` stores report types, test fields, input limits, and reference ranges. `assets/libs/TestRanges.php` reads these definitions for entry-form hints, on-screen reports, and PDFs. Reports classify readings as Low, Within range, or High. Limits marked `<` exclude the cutoff; two-ended ranges include their endpoints. One-sided thresholds do not invent a lower or upper limit. Unsupported tests, patients under 20, and sex-dependent tests without a Male/Female record show Review needed. Invalid readings are flagged separately.

These are general non-pregnant adult reference values, not individualized treatment targets or diagnoses. CBC and HDL limits use the recorded gender as a proxy for the sex-specific reference group. HDL uses the low threshold rather than the ideal target; 60 mg/dL or more is desirable. Glucose assumes 8-hour fasting or a sample 2 hours after a meal. The post-meal interval combines the low-glucose threshold of 70 with the upper reference limit of 140. Local laboratory ranges and clinical circumstances take precedence.

Sources checked 2026-10-24:

- [NHLBI: adult CBC ranges](https://www.nhlbi.nih.gov/health/blood-tests)
- [MedlinePlus: cholesterol](https://medlineplus.gov/lab-tests/cholesterol-levels/)
- [MedlinePlus: triglycerides](https://medlineplus.gov/triglycerides.html)
- [NIDDK: glucose and A1C thresholds](https://www.niddk.nih.gov/health-information/diabetes/overview/tests-diagnosis)
- [NIDDK: low blood glucose](https://www.niddk.nih.gov/health-information/diabetes/overview/preventing-problems/low-blood-glucose-hypoglycemia)
- [University of Rochester: two-hour postprandial glucose](https://www.urmc.rochester.edu/encyclopedia/content?ContentID=glucose_two_hour_postprandial&ContentTypeID=167)

## Report templates and assets

`schema.sql` seeds three templates in `report_templates`: FBC (5 fields), Lipid Profile (4 fields), and Blood Sugar (3 fields). Each template stores its type, display name, and JSON field definitions, including age/sex-specific reference ranges. Re-importing the schema skips existing templates.

The app only reads these templates. There are no template-management pages or write actions. Creating, editing, and deleting patient reports affects `reports`, not the templates. This is application-level read-only behavior; the local XAMPP root database account retains administrative permissions.

The favicon is in `assets/favicon.svg`. PDF generation and range helpers are in `assets/libs/`.
