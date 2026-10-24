<?php

require_once __DIR__ . '/Database.php';

class PatientModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function createPatient(int $doctorId, string $patientName, int $patientAge, string $patientGender): bool
    {
        $statement = $this->db->prepare("INSERT INTO patients (doctor_id, name, age, gender) VALUES (:doctor_id, :name, :age, :gender)");
        return $statement->execute([
            ':doctor_id' => $doctorId,
            ':name' => $patientName,
            ':age' => $patientAge,
            ':gender' => $patientGender,
        ]);
    }

    public function getPatientsByDoctorId(int $doctorId): array
    {
        $statement = $this->db->prepare("SELECT * FROM patients WHERE doctor_id = :doctor_id ORDER BY id DESC");
        $statement->execute([':doctor_id' => $doctorId]);
        return $statement->fetchAll();
    }

    public function searchAndFilterPatients(
        int $doctorId,
        string $searchTerm = '',
        string $genderFilter = '',
        ?int $minAge = null,
        ?int $maxAge = null,
        string $reportStatusFilter = '',
        string $sortOption = 'id_desc'
    ): array {
        $query = "
            SELECT patients.*, COUNT(reports.id) AS report_count
            FROM patients
            LEFT JOIN reports ON patients.id = reports.patient_id
            WHERE patients.doctor_id = :doctor_id
        ";
        $filters = [
            ':doctor_id' => $doctorId,
        ];

        if (!empty($searchTerm)) {
            $query .= " AND (patients.name LIKE :search_name OR patients.id = :search_id)";
            $filters[':search_name'] = '%' . $searchTerm . '%';
            $filters[':search_id'] = is_numeric($searchTerm) ? (int)$searchTerm : 0;
        }

        if (!empty($genderFilter) && in_array($genderFilter, ['Male', 'Female', 'Other'], true)) {
            $query .= " AND patients.gender = :gender";
            $filters[':gender'] = $genderFilter;
        }

        if ($minAge !== null && $minAge > 0) {
            $query .= " AND patients.age >= :minimum_age";
            $filters[':minimum_age'] = $minAge;
        }

        if ($maxAge !== null && $maxAge > 0) {
            $query .= " AND patients.age <= :maximum_age";
            $filters[':maximum_age'] = $maxAge;
        }

        $query .= " GROUP BY patients.id";

        if ($reportStatusFilter === 'with_reports') {
            $query .= " HAVING report_count > 0";
        } elseif ($reportStatusFilter === 'without_reports') {
            $query .= " HAVING report_count = 0";
        }

        $sortChoices = [
            'id_desc' => 'patients.id DESC',
            'id_asc' => 'patients.id ASC',
            'name_asc' => 'patients.name ASC',
            'name_desc' => 'patients.name DESC',
            'age_asc' => 'patients.age ASC',
            'age_desc' => 'patients.age DESC',
            'reports_desc' => 'report_count DESC, patients.id DESC',
        ];

        $sortOrder = $sortChoices[$sortOption] ?? 'patients.id DESC';
        $query .= " ORDER BY " . $sortOrder;

        $statement = $this->db->prepare($query);
        $statement->execute($filters);
        return $statement->fetchAll();
    }

    public function getPatientByIdAndDoctorId(int $patientId, int $doctorId): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM patients WHERE id = :id AND doctor_id = :doctor_id LIMIT 1");
        $statement->execute([
            ':id' => $patientId,
            ':doctor_id' => $doctorId,
        ]);
        $patient = $statement->fetch();
        return $patient ?: null;
    }
}
