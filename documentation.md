# Blood Report Generator — System Documentation

**Student ID:** SOF/23/B2/02  
**Student Name:** B. Ramesh Madara  
**Assessment:** Take Home Individual Assignment  
**Course:** [Enterprise System Technologies & Architectures - IT 403061 - 2023/24 Batch 22023/24 - B2](https://classroom.google.com/u/0/c/ODc1NDc1ODA4NzE4)  

---

## 1. Overview

The **Blood Report Generator** is a specialized PHP web application designed for medical practitioners and clinical laboratories. Built from first principles on the **Model-View-Controller (MVC)** architectural pattern, the application enables doctors to securely manage patient demographics, record clinical blood test metrics, evaluate physiological readings against standardized reference intervals, and generate downloadable, publication-grade PDF medical reports.

The system natively supports three primary clinical investigations:
- **Full Blood Count (FBC):** Hemoglobin, White Blood Cells (WBC), Red Blood Cells (RBC), Platelets, and Hematocrit.
- **Lipid Profile:** Total Cholesterol, Triglycerides, High-Density Lipoprotein (HDL), and Low-Density Lipoprotein (LDL).
- **Blood Sugar:** Fasting Blood Glucose, 2-Hour Postprandial Glucose, and HbA1c (Glycated Hemoglobin).

The application is entirely self-contained, requiring only PHP 8.2, Apache, and MySQL/MariaDB via XAMPP, with no external package managers (such as Composer or npm).

---

## 2. Requirements Analysis

### 2.1 List of Functional Requirements

The system requirements are specified across twelve core functional requirements (FR-01 through FR-12):

| Requirement ID | Functional Requirement | Primary Objective & Description |
| :--- | :--- | :--- |
| **FR-01** | **Admin Authentication** | Secure login mechanism verifying doctor credentials. |
| **FR-02** | **Admin Logout** | Mechanism to securely terminate an active session. |
| **FR-03** | **Admin Account Management** | Capability for authenticated doctors to create new doctor accounts. |
| **FR-04** | **Data Isolation** | Strict privacy rule filtering patients and reports so they are only visible to the doctor who created them. |
| **FR-05** | **Patient Registration** | Capability to create and save new patient demographic profiles. |
| **FR-06** | **Patient Record Viewing** | Dashboard interface listing registered patients for quick access. |
| **FR-07** | **Report Creation** | System to generate and save new blood reports under a patient using predefined templates (FBC, Lipid Profile, Blood Sugar). |
| **FR-08** | **Report Viewing** | Interface to retrieve and display the detailed metrics of a saved blood report. |
| **FR-09** | **Report Modification** | Capability to edit and update metrics within an existing saved report. |
| **FR-10** | **Report Deletion** | Capability to permanently remove a specific blood report from the database. |
| **FR-11** | **PDF Export** | Function to format and download a saved report as a printable PDF document. |
| **FR-12** | **Input Validation** | Backend verification ensuring submitted patient and report data meets required formats before database insertion. |

---

### 2.2 Functional Requirements Elaboration & MVC Implementation

The following subsections briefly describe each functional requirement and its corresponding **Model**, **View**, and **Controller** implementation.

#### 2.2.1 FR-01: Admin Authentication
- **Description:** Verifies doctor credentials to establish an authenticated session.
- **Controller:** `AuthenticationController::login()` validates inputs, checks password hash with `password_verify()`, and sets session variables (`doctor_id`, `doctor_username`, `doctor_display_name`).
- **Model:** `DoctorModel::findDoctorByUsername()` fetches the doctor record via parameterized PDO query.
- **View:** `view/login.php` renders the login form and credential validation alerts.

#### 2.2.2 FR-02: Admin Logout
- **Description:** Terminates the active session and clears stored doctor credentials.
- **Controller:** `AuthenticationController::logout()` clears `$_SESSION`, destroys the session via `session_destroy()`, and redirects to `?action=login`.
- **View:** Persistent logout link rendered in `view/header.php`.

#### 2.2.3 FR-03: Admin Account Management
- **Description:** Allows doctors to register new accounts, update display names, and change passwords.
- **Controller:** `AccountController::register()` handles doctor creation; `AccountController::manage()` handles profile and password changes.
- **Model:** `DoctorModel` methods (`createDoctor`, `updateDisplayName`, `updateDoctorPassword`).
- **View:** `view/register.php` (registration form) and `view/account.php` (profile and password forms).

#### 2.2.4 FR-04: Data Isolation
- **Description:** Enforces multi-tenant data privacy so doctors can only view and manage their own patients and reports.
- **Controller:** `ensureDoctorIsAuthenticated()` checks session ownership and passes `$doctorId` to all model calls.
- **Model:** `PatientModel` and `ReportModel` enforce `WHERE doctor_id = :doctor_id` and relational join checks on all queries.
- **View:** Renders only authorized records; unauthorized ID lookups redirect with error alerts.

#### 2.2.5 FR-05: Patient Registration
- **Description:** Registers new patient demographic profiles (Full Name, Age, Gender).
- **Controller:** `PatientController::create()` validates demographic inputs and redirects on success.
- **Model:** `PatientModel::createPatient()` inserts the new record linked to the active doctor.
- **View:** `view/patient_create.php` renders the registration form with validation feedback.

#### 2.2.6 FR-06: Patient Record Viewing
- **Description:** Lists registered patients with search, filtering, and sorting, and displays individual patient dossiers.
- **Controller:** `PatientController::index()` parses search/filter/sort parameters; `PatientController::view()` loads a patient dossier.
- **Model:** `PatientModel::searchAndFilterPatients()` and `ReportModel::getReportsByPatientIdAndDoctorId()`.
- **View:** `view/patient_list.php` (patient directory with filter controls) and `view/patient_view.php` (patient profile and report history).

#### 2.2.7 FR-07: Report Creation
- **Description:** Generates a blood report under a patient using a selected template (FBC, Lipid Profile, Blood Sugar).
- **Controller:** `ReportController::create()` validates readings via `ReportTemplateModel::readValues()` and saves JSON results.
- **Model:** `ReportTemplateModel::all()` loads templates; `ReportModel::createReport()` persists report data.
- **View:** `view/report_create.php` and `view/report_fields.php` (dynamic category switcher and reference range hints).

#### 2.2.8 FR-08: Report Viewing
- **Description:** Displays recorded blood test metrics evaluated against age- and gender-specific clinical reference ranges.
- **Controller:** `ReportController::view()` fetches the report and forwards decoded JSON metrics to the view.
- **Model & Helper:** `ReportModel::getReportByIdAndDoctorId()` and `TestRanges::check()` to compute status badges.
- **View:** `view/report_view.php` displays metrics, reference intervals, and status badges (Within range, Low, High, Review needed).

#### 2.2.9 FR-09: Report Modification
- **Description:** Allows doctors to edit existing blood report readings and re-validate updated metrics.
- **Controller:** `ReportController::edit()` populates existing values, validates changes, and saves updates.
- **Model:** `ReportModel::updateReport()` updates report data with doctor ownership verification.
- **View:** `view/report_edit.php` renders the editable parameter form.

#### 2.2.10 FR-10: Report Deletion
- **Description:** Permanently deletes a blood report from the database after confirmation.
- **Controller:** `ReportController::delete()` verifies doctor authorization and deletes the record.
- **Model:** `ReportModel::deleteReport()` removes the record scoped to the logged-in doctor.
- **View:** Deletion action buttons and confirmation prompts in `view/patient_view.php` and `view/report_view.php`.

#### 2.2.11 FR-11: PDF Export
- **Description:** Formats and exports a saved report as a printable, publication-grade PDF document.
- **Controller:** `ReportController::exportPdf()` renders patient details, test metrics, and status labels via FPDF.
- **Library:** `assets/libs/fpdf.php` builds the PDF stream; supports inline preview or direct download.
- **Output:** Timestamped PDF file (`YYYYMMDD_HHMMSS_patientId_reportId_patientName.pdf`).

#### 2.2.12 FR-12: Input Validation
- **Description:** Server-side verification ensuring submitted patient and report data meets required formats and limits.
- **Controller:** Validates credentials (regex, length limits), patient fields (name regex, age 1–125, gender whitelist), and displays error alerts.
- **Model:** `ReportTemplateModel::readValues()` enforces numeric, finite, and min/max boundaries for all test inputs.
- **View:** Displays descriptive validation error alerts on forms without losing user input.

---

## 3. System Architecture

### 3.1 Architecture Overview & Separation of Concerns

The system is architected around the classic **Model-View-Controller (MVC)** design pattern, supplemented by the **Front Controller** pattern. The application strictly adheres to the principle of **Separation of Concerns (SoC)**:

1. **Model Layer (`model/`):** Solely responsible for domain business logic, data persistence, and SQL execution. Models contain no HTML markup, CSS styling, or HTTP header logic.
2. **View Layer (`view/`):** Pure presentation layer that translates application state into accessible HTML5 markup. Views contain no database queries or mutating operations; they consume variables prepared by the Controller.
3. **Controller Layer (`controller/`):** Orchestrates application flow. Controllers handle HTTP request dispatching, input validation, authentication guards, invocation of Model operations, and the rendering of Views or redirection.
4. **Front Controller (`index.php`):** Central entry point that initializes the runtime environment, starts sessions, synchronizes user state, and routes incoming actions.

```
+-----------------------------------------------------------------------------------+
|                                  Client Browser                                   |
+-----------------------------------------------------------------------------------+
         |                                                                  ^
   1. HTTP Request                                                   6. HTTP Response
   (GET/POST index.php?action=...)                                    (HTML / Streamed PDF)
         |                                                                  |
         v                                                                  |
+-----------------------------------------------------------------------------------+
| FRONT CONTROLLER (`index.php`)                                                    |
| - Initializes PHP session (`session_start()`)                                     |
| - Verifies and syncs logged-in doctor state via `DoctorModel`                     |
| - Inspects `action` parameter and routes to appropriate Controller               |
+-----------------------------------------------------------------------------------+
         |
         | 2. Instantiates Controller & executes action method
         v
+-----------------------------------------------------------------------------------+
| CONTROLLER LAYER (`controller/`)                                                  |
| - AuthenticationController: Login & session destruction                           |
| - AccountController: Doctor registration, profile & password management           |
| - PatientController: Patient directory search/filter/sort, creation & profile     |
| - ReportController: Test parameter validation, CRUD & PDF compilation             |
+-----------------------------------------------------------------------------------+
         |                                                          |
         | 3. Invokes Model logic                                   | 5. Supplies data
         v                                                          |    to View
+------------------------------------+                              v
| MODEL LAYER (`model/`)             |                    +-------------------------+
| - Database: Singleton PDO factory  |                    | VIEW LAYER (`view/`)    |
| - DoctorModel: Account entities    |                    | - header.php (Navbar)   |
| - PatientModel: Directory & query  |                    | - Page Views (*.php)    |
| - ReportModel: Blood test CRUD     |                    | - footer.php (Closure)  |
| - ReportTemplateModel: Schema/JSON |                    +-------------------------+
+------------------------------------+
         |                   ^
   SQL   |                   | Data
 Query   |                   | Records
         v                   |
+------------------------------------+
| DATABASE (MySQL / MariaDB)         |
+------------------------------------+
```

### 3.2 Directory and Folder Structure

The project structure is organized cleanly by architectural responsibility:

```text
sof_23_b2_02_esat_mvc/
├── index.php                      # Front Controller & central request router
├── schema.sql                     # Database schema, table structures, and seed data
├── README.md                      # Architecture overview and clinical reference notes
├── setup guide.md                 # Setup guide for local XAMPP Apache/MySQL
├── documentation.md               # Complete system and technical documentation
├── controller/                    # Application and Flow Controllers
│   ├── AccountController.php      # Registration, display name, and password management
│   ├── AuthenticationController.php # Doctor login and session termination
│   ├── PatientController.php      # Patient directory search, creation, and detail views
│   └── ReportController.php       # Blood test CRUD operations and PDF export
├── model/                         # Data Access Objects and Domain Models
│   ├── Database.php               # Singleton PDO connection wrapper with auto-bootstrap
│   ├── DoctorModel.php            # Doctor account database operations
│   ├── PatientModel.php           # Patient CRUD and advanced parameterized search/filtering
│   ├── ReportModel.php            # Blood report storage (JSON data) and relational queries
│   └── ReportTemplateModel.php    # Template definitions, JSON parsing, and range validation
├── view/                          # Presentation Templates (HTML5 + PHP)
│   ├── header.php                 # Master header, CSS design system, and navigation bar
│   ├── footer.php                 # Master footer layout termination
│   ├── login.php                  # Doctor login interface
│   ├── register.php               # Doctor account registration form
│   ├── account.php                # Account settings (display name & password forms)
│   ├── patient_list.php           # Patient directory dashboard with search, filters & sort
│   ├── patient_create.php         # Patient registration form
│   ├── patient_view.php           # Patient dossier and chronological report history
│   ├── report_create.php          # Blood report generation form
│   ├── report_edit.php            # Blood report editing interface
│   ├── report_fields.php          # Dynamic category parameter input panel partial
│   └── report_view.php            # Blood report clinical results inspection table
└── assets/                        # Static resources and PHP support libraries
    ├── favicon.svg                # Application vector favicon
    └── libs/
        ├── fpdf.php               # Native PDF rendering engine
        └── TestRanges.php         # Clinical reference range evaluation engine
```

### 3.3 Front Controller Routing Map

`index.php` acts as the single gateway for all HTTP traffic. Requests are routed according to the `action` query parameter:

| Action (`?action=`) | Controller | Method | HTTP Method | Target View / Output | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `login` | `AuthenticationController` | `login()` | GET / POST | `view/login.php` | Validates credentials and starts session |
| `logout` | `AuthenticationController` | `logout()` | GET | Redirect `?action=login` | Destroys session and logs out |
| `register` | `AccountController` | `register()` | GET / POST | `view/register.php` | Registers new doctor accounts |
| `account` | `AccountController` | `manage()` | GET / POST | `view/account.php` | Manages doctor display name and password |
| `patients` *(default)* | `PatientController` | `index()` | GET | `view/patient_list.php` | Dashboard listing patients with search & filters |
| `patient_create` | `PatientController` | `create()` | GET / POST | `view/patient_create.php` | Captures and persists new patient profiles |
| `patient_view` | `PatientController` | `view()` | GET | `view/patient_view.php` | Displays patient profile and report history |
| `report_create` | `ReportController` | `create()` | GET / POST | `view/report_create.php` | Validates and saves new blood report |
| `report_view` | `ReportController` | `view()` | GET | `view/report_view.php` | Detailed report view with clinical ranges |
| `report_edit` | `ReportController` | `edit()` | GET / POST | `view/report_edit.php` | Modifies existing blood report values |
| `report_delete` | `ReportController` | `delete()` | POST / GET | Redirect to `patient_view` | Deletes report with confirmation |
| `report_pdf` | `ReportController` | `exportPdf()` | GET | Binary Stream (`FPDF`) | Generates downloadable or printable PDF report |

### 3.4 Database Architecture

The relational database (`blood_report_system`) consists of four primary tables:

1. **`doctors`:** Stores administrative accounts (`id`, `username`, `password`, `display_name`, `created_at`). The `password` column stores secure bcrypt hashes (`VARCHAR(255)`). The `display_name` is non-unique and appears on medical reports.
2. **`patients`:** Stores registered patient profiles (`id`, `doctor_id`, `name`, `age`, `gender`, `created_at`). `doctor_id` establishes multi-tenant isolation via a foreign key to `doctors.id`.
3. **`reports`:** Stores generated medical reports (`id`, `patient_id`, `type`, `data`, `created_at`). `data` stores clinical test values as structured JSON text. `patient_id` references `patients.id`.
4. **`report_templates`:** Stores standardized test configurations (`id`, `type`, `name`, `fields`). The `fields` column contains JSON schemas defining parameter names, units, min/max entry bounds, and age/gender-specific clinical reference ranges.

---

## 4. Screenshots and Feature Showcases

> *Note for Submission: Replace each placeholder box below with the corresponding captured application screenshot before final submission.*

### Screenshot 1 — Database Architecture & Tables
```text
+-----------------------------------------------------------------------------------+
|                                                                                   |
|                   [ INSERT SCREENSHOT: PHPMYADMIN DATABASE ]                      |
|                                                                                   |
| Showcase the 'blood_report_system' database showing the four tables:               |
| 'doctors', 'patients', 'reports', and 'report_templates'.                         |
+-----------------------------------------------------------------------------------+
```
*Figure 1: Relational database structure in phpMyAdmin displaying table definitions and indexes.*

---

### Screenshot 2 — Admin Authentication (Login Screen)
```text
+-----------------------------------------------------------------------------------+
|                                                                                   |
|                      [ INSERT SCREENSHOT: DOCTOR LOGIN ]                          |
|                                                                                   |
| Showcase the login form displaying the username and password inputs, along with   |
| the demo doctor credentials banner ('demo_doctor / Doctor123!').                   |
+-----------------------------------------------------------------------------------+
```
*Figure 2: Authentication portal verifying doctor credentials with demo assistance.*

---

### Screenshot 3 — Admin Account Registration
```text
+-----------------------------------------------------------------------------------+
|                                                                                   |
|                   [ INSERT SCREENSHOT: DOCTOR REGISTRATION ]                      |
|                                                                                   |
| Showcase the registration page containing fields for Display Name, Username,      |
| Password, and Password Confirmation.                                              |
+-----------------------------------------------------------------------------------+
```
*Figure 3: Registration interface enabling new practitioners to create accounts.*

---

### Screenshot 4 — Admin Account Management (Profile & Password Settings)
```text
+-----------------------------------------------------------------------------------+
|                                                                                   |
|                   [ INSERT SCREENSHOT: ACCOUNT MANAGEMENT ]                       |
|                                                                                   |
| Showcase the Account page featuring separate forms for updating the Display       |
| Name and changing the doctor password with current password verification.         |
+-----------------------------------------------------------------------------------+
```
*Figure 4: Practitioner profile management and credential update interface.*

---

### Screenshot 5 — Patient Record Viewing (Dashboard & Directory)
```text
+-----------------------------------------------------------------------------------+
|                                                                                   |
|                   [ INSERT SCREENSHOT: PATIENT DIRECTORY ]                        |
|                                                                                   |
| Showcase the 'My Patients' directory featuring the live search input, gender      |
| filter, age range inputs, report status filter, sort dropdown, and patient table. |
+-----------------------------------------------------------------------------------+
```
*Figure 5: Patient management dashboard with multi-criteria search, filtering, and sorting.*

---

### Screenshot 6 — Patient Registration Form
```text
+-----------------------------------------------------------------------------------+
|                                                                                   |
|                  [ INSERT SCREENSHOT: REGISTER NEW PATIENT ]                      |
|                                                                                   |
| Showcase the patient registration form with Full Name, Age (numeric), and Gender  |
| selection controls.                                                               |
+-----------------------------------------------------------------------------------+
```
*Figure 6: Patient registration interface validating demographic inputs.*

---

### Screenshot 7 — Patient Profile & Historical Reports Dossier
```text
+-----------------------------------------------------------------------------------+
|                                                                                   |
|                   [ INSERT SCREENSHOT: PATIENT RECORD DOSSIER ]                   |
|                                                                                   |
| Showcase the patient details card displaying ID, Name, Age, Gender, and the list  |
| of historical blood reports with action buttons (+ Report, View, Edit, PDF).      |
+-----------------------------------------------------------------------------------+
```
*Figure 7: Comprehensive patient dossier displaying past blood test reports and quick actions.*

---

### Screenshot 8 — Report Creation (Dynamic Parameter Selection)
```text
+-----------------------------------------------------------------------------------+
|                                                                                   |
|                   [ INSERT SCREENSHOT: CREATE BLOOD REPORT ]                      |
|                                                                                   |
| Showcase the dynamic blood report entry form with the Report Category dropdown    |
| (FBC selected) and physiological reference range hints beneath each input.        |
+-----------------------------------------------------------------------------------+
```
*Figure 8: Report generation form dynamically rendering fields and clinical reference hints.*

---

### Screenshot 9 — Report Viewing & Metric Interpretation
```text
+-----------------------------------------------------------------------------------+
|                                                                                   |
|                   [ INSERT SCREENSHOT: VIEW REPORT METRICS ]                      |
|                                                                                   |
| Showcase the detailed report view displaying test parameters, recorded readings,   |
| standard reference ranges, and diagnostic status badges (e.g., 'Within range').   |
+-----------------------------------------------------------------------------------+
```
*Figure 9: Clinical report viewing interface with colored status badges evaluating patient metrics.*

---

### Screenshot 10 — Report Modification Interface
```text
+-----------------------------------------------------------------------------------+
|                                                                                   |
|                    [ INSERT SCREENSHOT: EDIT BLOOD REPORT ]                       |
|                                                                                   |
| Showcase the report editing page pre-populated with existing test values and the  |
| 'Back to Patient Record' navigation link.                                         |
+-----------------------------------------------------------------------------------+
```
*Figure 10: Report modification interface allowing practitioners to revise test metrics.*

---

### Screenshot 11 — Report Deletion Confirmation
```text
+-----------------------------------------------------------------------------------+
|                                                                                   |
|                 [ INSERT SCREENSHOT: DELETE CONFIRMATION DIALOGUE ]               |
|                                                                                   |
| Showcase the on-screen confirmation dialogue verifying intentional removal        |
| of a blood test report.                                                           |
+-----------------------------------------------------------------------------------+
```
*Figure 11: Safety confirmation prompt preventing accidental deletion of clinical records.*

---

### Screenshot 12 — PDF Report Export & Preview
```text
+-----------------------------------------------------------------------------------+
|                                                                                   |
|                    [ INSERT SCREENSHOT: PDF EXPORT PREVIEW ]                      |
|                                                                                   |
| Showcase the generated PDF report rendered in the browser with clinical header,   |
| patient demographics, attending doctor name, results table, and download button.  |
+-----------------------------------------------------------------------------------+
```
*Figure 12: Publication-grade PDF medical report generated via FPDF.*

---

## 5. Sample Data and Setup Summary

The application includes seeded sample records in `schema.sql` to demonstrate full operational functionality immediately upon deployment:

- **Demo Doctor Account:**
  - **Username:** `demo_doctor`
  - **Password:** `Doctor123!`
  - **Display Name:** `Dr. Alexander Wright, MD`
- **Sample Patients:** 6 fictional patient records with varying age profiles, genders, and test histories:
  - *Nimal Perera (Demo)* (Age: 45, Male) — 2 Reports
  - *Amaya Silva (Demo)* (Age: 29, Female) — 2 Reports
  - *Kasun Fernando (Demo)* (Age: 52, Male) — 3 Reports (FBC, Lipid Profile, Blood Sugar)
  - *Dilini Jayasinghe (Demo)* (Age: 38, Female) — 3 Reports (FBC, Lipid Profile, Blood Sugar)
  - *Saman Wijesinghe (Demo)* (Age: 61, Male) — 3 Reports (FBC, Lipid Profile, Blood Sugar)
  - *Tharushi De Silva (Demo)* (Age: 24, Female) — 3 Reports (FBC, Lipid Profile, Blood Sugar)
- **Sample Reports:** 16 complete blood test reports containing diverse normal and abnormal readings to showcase the reference range evaluation engine.

### Quick Setup Verification
1. Verify XAMPP Apache and MySQL are running.
2. Confirm project placement at `C:/xampp/htdocs/sof_23_b2_02_esat_mvc`.
3. Open `http://localhost/sof_23_b2_02_esat_mvc/` in any modern web browser.
4. Sign in with the demo credentials above. Detailed setup steps are available in [setup guide.md](file:///m:/UoVT_Material_SOF/Semester_4/Enterprise_System_Technologies_and_Architectures/Assignments/sof_23_b2_02_esat_mvc/setup%20guide.md).
