# Setup Guide

1. Install XAMPP with PHP 8.2 and start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Place the project folder inside XAMPP's `htdocs` directory at:
   ```text
   C:/xampp/htdocs/sof_23_b2_02_esat_mvc
   ```
3. Open phpMyAdmin at [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/) and import `schema.sql`. It creates the `blood_report_system` database, report templates, the demo doctor account, and sample patient records.
4. Check `model/Database.php` to verify your MySQL credentials:
   - Host: `127.0.0.1`
   - Database: `blood_report_system`
   - User: `root`
   - Password: `''` (empty string, local XAMPP default)

   Adjust these settings if your MySQL configuration differs.
5. Open your web browser and navigate directly to:
   ```text
   http://localhost/sof_23_b2_02_esat_mvc/
   ```
   *(No virtual host configuration or Windows `hosts` file editing is required).*

---

### Demo Login Credentials

- **Username:** `demo_doctor`
- **Password:** `Doctor123!`

