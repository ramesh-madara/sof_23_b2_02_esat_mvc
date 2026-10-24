<?php

require_once __DIR__ . '/../model/PatientModel.php';
require_once __DIR__ . '/../model/ReportModel.php';
require_once __DIR__ . '/../libs/fpdf.php';

class ReportController
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

    private function validateNumericReading(?string $value): bool
    {
        if ($value === null || trim($value) === '') {
            return false;
        }
        return is_numeric($value) && (float)$value >= 0;
    }

    public function create(): void
    {
        $doctorId = $this->ensureDoctorIsAuthenticated();
        $patientId = (int)($_GET['patient_id'] ?? 0);

        $patient = $this->patientModel->getPatientByIdAndDoctorId($patientId, $doctorId);

        if (!$patient) {
            header('Location: index.php?action=patients&error=unauthorized');
            exit;
        }

        $errorMessage = '';

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $reportType = trim($_POST['report_type'] ?? '');
            $results = [];

            if ($reportType === 'FBC') {
                $hemoglobin = trim($_POST['hemoglobin'] ?? '');
                $whiteBloodCells = trim($_POST['white_blood_cells'] ?? '');
                $redBloodCells = trim($_POST['red_blood_cells'] ?? '');
                $platelets = trim($_POST['platelets'] ?? '');
                $hematocrit = trim($_POST['hematocrit'] ?? '');

                if (
                    !$this->validateNumericReading($hemoglobin) ||
                    !$this->validateNumericReading($whiteBloodCells) ||
                    !$this->validateNumericReading($redBloodCells) ||
                    !$this->validateNumericReading($platelets) ||
                    !$this->validateNumericReading($hematocrit)
                ) {
                    $errorMessage = 'All Full Blood Count parameters are required and must be valid non-negative numbers.';
                } else {
                    $results = [
                        'Hemoglobin (g/dL)' => $hemoglobin,
                        'White Blood Cell Count (x10^9/L)' => $whiteBloodCells,
                        'Red Blood Cell Count (x10^12/L)' => $redBloodCells,
                        'Platelet Count (x10^9/L)' => $platelets,
                        'Hematocrit (%)' => $hematocrit,
                    ];
                }
            } elseif ($reportType === 'Lipid Profile') {
                $totalCholesterol = trim($_POST['total_cholesterol'] ?? '');
                $triglycerides = trim($_POST['triglycerides'] ?? '');
                $hdlCholesterol = trim($_POST['hdl_cholesterol'] ?? '');
                $ldlCholesterol = trim($_POST['ldl_cholesterol'] ?? '');

                if (
                    !$this->validateNumericReading($totalCholesterol) ||
                    !$this->validateNumericReading($triglycerides) ||
                    !$this->validateNumericReading($hdlCholesterol) ||
                    !$this->validateNumericReading($ldlCholesterol)
                ) {
                    $errorMessage = 'All Lipid Profile parameters are required and must be valid non-negative numbers.';
                } else {
                    $results = [
                        'Total Cholesterol (mg/dL)' => $totalCholesterol,
                        'Triglycerides (mg/dL)' => $triglycerides,
                        'HDL Cholesterol (mg/dL)' => $hdlCholesterol,
                        'LDL Cholesterol (mg/dL)' => $ldlCholesterol,
                    ];
                }
            } elseif ($reportType === 'Blood Sugar') {
                $fastingBloodSugar = trim($_POST['fasting_blood_sugar'] ?? '');
                $postprandialBloodSugar = trim($_POST['postprandial_blood_sugar'] ?? '');
                $hba1c = trim($_POST['hba1c'] ?? '');

                if (
                    !$this->validateNumericReading($fastingBloodSugar) ||
                    !$this->validateNumericReading($postprandialBloodSugar) ||
                    !$this->validateNumericReading($hba1c)
                ) {
                    $errorMessage = 'All Blood Sugar parameters are required and must be valid non-negative numbers.';
                } else {
                    $results = [
                        'Fasting Blood Sugar (mg/dL)' => $fastingBloodSugar,
                        'Postprandial Blood Sugar (mg/dL)' => $postprandialBloodSugar,
                        'HbA1c (%)' => $hba1c,
                    ];
                }
            } else {
                $errorMessage = 'Please select a valid report type.';
            }

            if (empty($errorMessage)) {
                $reportJson = json_encode($results);
                $created = $this->reportModel->createReport(
                    $patientId,
                    $doctorId,
                    $reportType,
                    $reportJson
                );

                if ($created) {
                    header('Location: index.php?action=patient_view&id=' . $patientId . '&report_created=1');
                    exit;
                } else {
                    $errorMessage = 'Failed to generate blood report.';
                }
            }
        }

        require __DIR__ . '/../view/report_create.php';
    }

    public function view(): void
    {
        $doctorId = $this->ensureDoctorIsAuthenticated();
        $reportId = (int)($_GET['id'] ?? 0);

        $report = $this->reportModel->getReportByIdAndDoctorId($reportId, $doctorId);

        if (!$report) {
            header('Location: index.php?action=patients&error=notfound');
            exit;
        }

        $results = json_decode($report['data'], true) ?? [];

        require __DIR__ . '/../view/report_view.php';
    }

    public function edit(): void
    {
        $doctorId = $this->ensureDoctorIsAuthenticated();
        $reportId = (int)($_GET['id'] ?? 0);

        $report = $this->reportModel->getReportByIdAndDoctorId($reportId, $doctorId);

        if (!$report) {
            header('Location: index.php?action=patients&error=unauthorized');
            exit;
        }

        $errorMessage = '';
        $results = json_decode($report['data'], true) ?? [];

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $reportType = trim($_POST['report_type'] ?? '');
            $newResults = [];

            if ($reportType === 'FBC') {
                $hemoglobin = trim($_POST['hemoglobin'] ?? '');
                $whiteBloodCells = trim($_POST['white_blood_cells'] ?? '');
                $redBloodCells = trim($_POST['red_blood_cells'] ?? '');
                $platelets = trim($_POST['platelets'] ?? '');
                $hematocrit = trim($_POST['hematocrit'] ?? '');

                if (
                    !$this->validateNumericReading($hemoglobin) ||
                    !$this->validateNumericReading($whiteBloodCells) ||
                    !$this->validateNumericReading($redBloodCells) ||
                    !$this->validateNumericReading($platelets) ||
                    !$this->validateNumericReading($hematocrit)
                ) {
                    $errorMessage = 'All Full Blood Count parameters are required and must be valid non-negative numbers.';
                } else {
                    $newResults = [
                        'Hemoglobin (g/dL)' => $hemoglobin,
                        'White Blood Cell Count (x10^9/L)' => $whiteBloodCells,
                        'Red Blood Cell Count (x10^12/L)' => $redBloodCells,
                        'Platelet Count (x10^9/L)' => $platelets,
                        'Hematocrit (%)' => $hematocrit,
                    ];
                }
            } elseif ($reportType === 'Lipid Profile') {
                $totalCholesterol = trim($_POST['total_cholesterol'] ?? '');
                $triglycerides = trim($_POST['triglycerides'] ?? '');
                $hdlCholesterol = trim($_POST['hdl_cholesterol'] ?? '');
                $ldlCholesterol = trim($_POST['ldl_cholesterol'] ?? '');

                if (
                    !$this->validateNumericReading($totalCholesterol) ||
                    !$this->validateNumericReading($triglycerides) ||
                    !$this->validateNumericReading($hdlCholesterol) ||
                    !$this->validateNumericReading($ldlCholesterol)
                ) {
                    $errorMessage = 'All Lipid Profile parameters are required and must be valid non-negative numbers.';
                } else {
                    $newResults = [
                        'Total Cholesterol (mg/dL)' => $totalCholesterol,
                        'Triglycerides (mg/dL)' => $triglycerides,
                        'HDL Cholesterol (mg/dL)' => $hdlCholesterol,
                        'LDL Cholesterol (mg/dL)' => $ldlCholesterol,
                    ];
                }
            } elseif ($reportType === 'Blood Sugar') {
                $fastingBloodSugar = trim($_POST['fasting_blood_sugar'] ?? '');
                $postprandialBloodSugar = trim($_POST['postprandial_blood_sugar'] ?? '');
                $hba1c = trim($_POST['hba1c'] ?? '');

                if (
                    !$this->validateNumericReading($fastingBloodSugar) ||
                    !$this->validateNumericReading($postprandialBloodSugar) ||
                    !$this->validateNumericReading($hba1c)
                ) {
                    $errorMessage = 'All Blood Sugar parameters are required and must be valid non-negative numbers.';
                } else {
                    $newResults = [
                        'Fasting Blood Sugar (mg/dL)' => $fastingBloodSugar,
                        'Postprandial Blood Sugar (mg/dL)' => $postprandialBloodSugar,
                        'HbA1c (%)' => $hba1c,
                    ];
                }
            } else {
                $errorMessage = 'Please select a valid report type.';
            }

            if (empty($errorMessage)) {
                $reportJson = json_encode($newResults);
                $updated = $this->reportModel->updateReport(
                    $reportId,
                    $doctorId,
                    $reportType,
                    $reportJson
                );

                if ($updated) {
                    header('Location: index.php?action=report_view&id=' . $reportId . '&updated=1');
                    exit;
                } else {
                    $errorMessage = 'Failed to update the report.';
                }
            }
        }

        require __DIR__ . '/../view/report_edit.php';
    }

    public function delete(): void
    {
        $doctorId = $this->ensureDoctorIsAuthenticated();
        $reportId = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

        $report = $this->reportModel->getReportByIdAndDoctorId($reportId, $doctorId);

        if ($report) {
            $patientId = (int)$report['patient_id'];
            $this->reportModel->deleteReport($reportId, $doctorId);
            header('Location: index.php?action=patient_view&id=' . $patientId . '&deleted=1');
            exit;
        }

        header('Location: index.php?action=patients&error=unauthorized');
        exit;
    }

    public function exportPdf(): void
    {
        $doctorId = $this->ensureDoctorIsAuthenticated();
        $reportId = (int)($_GET['id'] ?? 0);

        $report = $this->reportModel->getReportByIdAndDoctorId($reportId, $doctorId);

        if (!$report) {
            header('Location: index.php?action=patients&error=notfound');
            exit;
        }

        $results = json_decode($report['data'], true) ?? [];

        $pdf = new FPDF();
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'BLOOD TEST REPORT', 0, 1, 'L');
        $pdf->Ln(4);

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(45, 8, 'Patient Name:', 1, 0);
        $pdf->Cell(0, 8, $report['patient_name'], 1, 1);

        $pdf->Cell(45, 8, 'Patient Age:', 1, 0);
        $pdf->Cell(0, 8, (string)$report['patient_age'] . ' Years', 1, 1);

        $pdf->Cell(45, 8, 'Patient Gender:', 1, 0);
        $pdf->Cell(0, 8, $report['patient_gender'], 1, 1);

        $pdf->Cell(45, 8, 'Attending Doctor:', 1, 0);
        $pdf->Cell(0, 8, ($_SESSION['doctor_display_name'] ?? $_SESSION['doctor_username']), 1, 1);

        $pdf->Cell(45, 8, 'Report Type:', 1, 0);
        $pdf->Cell(0, 8, $report['type'], 1, 1);

        $pdf->Cell(45, 8, 'Generated Date:', 1, 0);
        $pdf->Cell(0, 8, $report['created_at'], 1, 1);
        $pdf->Ln(6);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(110, 8, 'Test Parameter', 1, 0, 'L');
        $pdf->Cell(70, 8, 'Observed Result', 1, 1, 'L');

        $pdf->SetFont('Arial', '', 10);
        foreach ($results as $testName => $testValue) {
            $pdf->Cell(110, 7, (string)$testName, 1, 0, 'L');
            $pdf->Cell(70, 7, (string)$testValue, 1, 1, 'L');
        }

        $outputMode = isset($_GET['download']) ? 'D' : 'I';
        $exportTime = (new DateTimeImmutable('now', new DateTimeZone('Asia/Colombo')))->format('Ymd_His');
        $safePatientName = trim(preg_replace('/[^A-Za-z0-9_-]+/', '_', $report['patient_name']), '_');
        $safePatientName = $safePatientName !== '' ? $safePatientName : 'Patient';
        $fileName = sprintf(
            '%s_%d_%d_%s.pdf',
            $exportTime,
            (int)$report['patient_id'],
            $reportId,
            $safePatientName
        );
        $pdf->Output($outputMode, $fileName);
        exit;
    }
}
