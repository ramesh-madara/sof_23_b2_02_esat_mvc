<?php

require_once __DIR__ . '/Database.php';

class DoctorModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findDoctorByUsername(string $username): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM doctors WHERE username = :username LIMIT 1");
        $statement->execute([':username' => $username]);
        $doctor = $statement->fetch();
        return $doctor ?: null;
    }

    public function findDoctorById(int $doctorId): ?array
    {
        $statement = $this->db->prepare("SELECT id, username, display_name, password FROM doctors WHERE id = :id LIMIT 1");
        $statement->execute([':id' => $doctorId]);
        $doctor = $statement->fetch();
        return $doctor ?: null;
    }

    public function createDoctor(string $username, string $hashedPassword, string $displayName): bool
    {
        $statement = $this->db->prepare("INSERT INTO doctors (username, password, display_name) VALUES (:username, :password, :display_name)");
        return $statement->execute([
            ':username' => $username,
            ':display_name' => $displayName,
            ':password' => $hashedPassword,
        ]);
    }

    public function updateDisplayName(int $doctorId, string $displayName): bool
    {
        $statement = $this->db->prepare("UPDATE doctors SET display_name = :display_name WHERE id = :id");
        return $statement->execute([':display_name' => $displayName, ':id' => $doctorId]);
    }

    public function updateDoctorPassword(int $doctorId, string $hashedPassword): bool
    {
        $statement = $this->db->prepare("UPDATE doctors SET password = :password WHERE id = :id");
        return $statement->execute([
            ':password' => $hashedPassword,
            ':id' => $doctorId,
        ]);
    }
}
