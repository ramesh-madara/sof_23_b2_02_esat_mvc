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
                $setupConnection->exec(file_get_contents(__DIR__ . '/../schema.sql'));
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
