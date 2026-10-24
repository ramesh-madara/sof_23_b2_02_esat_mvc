<?php

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dbHost = '127.0.0.1';
            $dbName = 'blood_report_system';
            $dbUser = 'root';
            $dbPassword = '';

            try {
                $connectionString = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
                self::$connection = new PDO($connectionString, $dbUser, $dbPassword, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $dbError) {
                $serverConnectionString = "mysql:host={$dbHost};charset=utf8mb4";
                $setupConnection = new PDO($serverConnectionString, $dbUser, $dbPassword, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
                $setupConnection->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $setupConnection->exec("USE `{$dbName}`");
                $setupConnection->exec("
                    CREATE TABLE IF NOT EXISTS doctors (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        username VARCHAR(50) NOT NULL UNIQUE,
                        display_name VARCHAR(100) NOT NULL DEFAULT '',
                        password VARCHAR(255) NOT NULL
                    ) ENGINE=InnoDB;
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
                ");
                $connectionString = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
                self::$connection = new PDO($connectionString, $dbUser, $dbPassword, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            }
        }
        return self::$connection;
    }
}
