<?php

require_once __DIR__ . '/Database.php';

class ReportModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function createReport(int $patientId, int $doctorId, string $reportType, string $reportJson): bool
    {
        $patientCheck = $this->db->prepare("SELECT id FROM patients WHERE id = :patient_id AND doctor_id = :doctor_id LIMIT 1");
        $patientCheck->execute([
            ':patient_id' => $patientId,
            ':doctor_id' => $doctorId,
        ]);
        $patientFound = $patientCheck->fetch();

        if (!$patientFound) {
            return false;
        }

        $statement = $this->db->prepare("INSERT INTO reports (patient_id, type, data) VALUES (:patient_id, :type, :data)");
        return $statement->execute([
            ':patient_id' => $patientId,
            ':type' => $reportType,
            ':data' => $reportJson,
        ]);
    }

    public function getReportsByPatientIdAndDoctorId(int $patientId, int $doctorId): array
    {
        $statement = $this->db->prepare("
            SELECT reports.* 
            FROM reports 
            INNER JOIN patients ON reports.patient_id = patients.id 
            WHERE reports.patient_id = :patient_id AND patients.doctor_id = :doctor_id 
            ORDER BY reports.created_at DESC
        ");
        $statement->execute([
            ':patient_id' => $patientId,
            ':doctor_id' => $doctorId,
        ]);
        return $statement->fetchAll();
    }

    public function getReportByIdAndDoctorId(int $reportId, int $doctorId): ?array
    {
        $statement = $this->db->prepare("
            SELECT reports.*, patients.name AS patient_name, patients.age AS patient_age, patients.gender AS patient_gender, patients.doctor_id 
            FROM reports 
            INNER JOIN patients ON reports.patient_id = patients.id 
            WHERE reports.id = :report_id AND patients.doctor_id = :doctor_id 
            LIMIT 1
        ");
        $statement->execute([
            ':report_id' => $reportId,
            ':doctor_id' => $doctorId,
        ]);
        $report = $statement->fetch();
        return $report ?: null;
    }

    public function updateReport(int $reportId, int $doctorId, string $reportType, string $reportJson): bool
    {
        $statement = $this->db->prepare("
            UPDATE reports 
            INNER JOIN patients ON reports.patient_id = patients.id 
            SET reports.type = :type, reports.data = :data 
            WHERE reports.id = :report_id AND patients.doctor_id = :doctor_id
        ");
        return $statement->execute([
            ':type' => $reportType,
            ':data' => $reportJson,
            ':report_id' => $reportId,
            ':doctor_id' => $doctorId,
        ]);
    }

    public function deleteReport(int $reportId, int $doctorId): bool
    {
        $statement = $this->db->prepare("
            DELETE reports FROM reports 
            INNER JOIN patients ON reports.patient_id = patients.id 
            WHERE reports.id = :report_id AND patients.doctor_id = :doctor_id
        ");
        return $statement->execute([
            ':report_id' => $reportId,
            ':doctor_id' => $doctorId,
        ]);
    }
}
