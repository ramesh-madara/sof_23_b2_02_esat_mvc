<?php

require_once __DIR__ . '/../model/PatientModel.php';
require_once __DIR__ . '/../model/ReportModel.php';

class PatientController
{
    private PatientModel $patientModel;
    private ReportModel $reportModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
        $this->reportModel = new ReportModel();
    }

    private function ensureDoctorIsAuthenticated(): int
    {
        if (!isset($_SESSION['doctor_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        return (int)$_SESSION['doctor_id'];
    }

    public function index(): void
    {
        $doctorId = $this->ensureDoctorIsAuthenticated();

        $searchTerm = trim($_GET['search'] ?? '');
        $genderFilter = trim($_GET['gender'] ?? '');
        $minAge = (isset($_GET['min_age']) && $_GET['min_age'] !== '') ? (int)$_GET['min_age'] : null;
        $maxAge = (isset($_GET['max_age']) && $_GET['max_age'] !== '') ? (int)$_GET['max_age'] : null;
        $reportStatusFilter = trim($_GET['report_status'] ?? '');
        $sortOption = trim($_GET['sort'] ?? 'id_desc');

        $hasActiveFilters = (
            !empty($searchTerm) ||
            !empty($genderFilter) ||
            $minAge !== null ||
            $maxAge !== null ||
            !empty($reportStatusFilter) ||
            $sortOption !== 'id_desc'
        );

        $patients = $this->patientModel->searchAndFilterPatients(
            $doctorId,
            $searchTerm,
            $genderFilter,
            $minAge,
            $maxAge,
            $reportStatusFilter,
            $sortOption
        );

        require __DIR__ . '/../view/patient_list.php';
    }

    public function create(): void
    {
        $doctorId = $this->ensureDoctorIsAuthenticated();
        $errorMessage = '';

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $patientName = trim($_POST['name'] ?? '');
            $ageInput = $_POST['age'] ?? '';
            $patientGender = trim($_POST['gender'] ?? '');

            if (empty($patientName) || $ageInput === '' || empty($patientGender)) {
                $errorMessage = 'All patient fields are required.';
            } elseif (!preg_match("/^[a-zA-Z\s\.\-']{2,100}$/", $patientName)) {
                $errorMessage = 'Patient name must contain only letters, spaces, hyphens, and periods (2-100 characters).';
            } elseif (!filter_var($ageInput, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 125]])) {
                $errorMessage = 'Patient age must be a whole number between 1 and 125.';
            } elseif (!in_array($patientGender, ['Male', 'Female', 'Other'], true)) {
                $errorMessage = 'Please select a valid gender option.';
            } else {
                $patientAge = (int)$ageInput;
                $created = $this->patientModel->createPatient(
                    $doctorId,
                    $patientName,
                    $patientAge,
                    $patientGender
                );

                if ($created) {
                    header('Location: index.php?action=patients&created=1');
                    exit;
                } else {
                    $errorMessage = 'Failed to register the patient.';
                }
            }
        }

        require __DIR__ . '/../view/patient_create.php';
    }

    public function view(): void
    {
        $doctorId = $this->ensureDoctorIsAuthenticated();
        $patientId = (int)($_GET['id'] ?? 0);

        $patient = $this->patientModel->getPatientByIdAndDoctorId($patientId, $doctorId);

        if (!$patient) {
            header('Location: index.php?action=patients&error=notfound');
            exit;
        }

        $reports = $this->reportModel->getReportsByPatientIdAndDoctorId($patientId, $doctorId);

        require __DIR__ . '/../view/patient_view.php';
    }
}
