# Blood Report Generator

A plain PHP MVC application for doctors to manage patients and blood reports.

- `index.php` routes requests using the `action` query parameter.
- `controller/` handles authentication, accounts, patients, and reports.
- `model/` uses PDO with MySQL; reports belong to patients and patients belong to doctors.
- `view/` contains server-rendered HTML and styles.
- `libs/fpdf.php` provides PDF export.

Supported reports: Full Blood Count (FBC), Lipid Profile, and Blood Sugar. Doctors can register, log in, change passwords, create/search/filter patients, and create/view/edit/delete/export reports.

## Local setup

URL: http://mvc_assignment.local/

Runtime: XAMPP Apache, PHP 8.2, and MySQL/MariaDB. No Composer or Node dependencies are required.

Database settings are in `model/Database.php`:

- Host: `127.0.0.1`
- Database: `blood_report_system`
- User: `root`
- Password: empty (local XAMPP default)

Import `schema.sql` to create the database and its `doctors`, `patients`, and `reports` tables. It also creates a test doctor account: username `demo_doctor`, password `Doctor123!`. These credentials appear below the login form. Re-importing the schema preserves an existing account and its password. Use the Register page to create additional doctor accounts.

The Apache virtual host is configured in `C:/xampp/apache/conf/extra/httpd-vhosts.conf`, with this project directory as its document root. The Windows hosts file maps `mvc_assignment.local` to `127.0.0.1`. Restart Apache after changing its configuration.

The local virtual host disables directory listings and blocks access to SQL, Markdown, and Git metadata. Apache access/error logs are under `C:/xampp/apache/logs/mvc_assignment.local-*.log`.

The test account includes six fictional patients and 16 blood reports: Nimal Perera and Amaya Silva have two reports each; Kasun Fernando, Dilini Jayasinghe, Saman Wijesinghe, and Tharushi De Silva each have FBC, Lipid Profile, and Blood Sugar reports. All sample patient names are marked (Demo). Re-importing the unchanged sample data does not create duplicates.

Doctor accounts have an editable display name (not unique and not a key), separate from their login username. Set it during registration or on the Account page. For databases created before this field was added, apply migrations/001_doctor_display_name.sql once before importing the updated schema.
