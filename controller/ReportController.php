<?php

require_once __DIR__ . '/../model/PatientModel.php';
require_once __DIR__ . '/../model/ReportModel.php';
require_once __DIR__ . '/../assets/libs/fpdf.php';
require_once __DIR__ . '/../assets/libs/TestRanges.php';

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
            try {
                $results = ReportTemplateModel::readValues($reportType, $_POST);
            } catch (InvalidArgumentException $error) {
                $errorMessage = $error->getMessage();
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
            try {
                $newResults = ReportTemplateModel::readValues($reportType, $_POST);
            } catch (InvalidArgumentException $error) {
                $errorMessage = $error->getMessage();
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

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(85, 8, 'Test Parameter', 1, 0, 'L');
        $pdf->Cell(25, 8, 'Reading', 1, 0, 'L');
        $pdf->Cell(43, 8, 'Reference Range', 1, 0, 'L');
        $pdf->Cell(37, 8, 'Status', 1, 1, 'L');

        $pdf->SetFont('Arial', '', 9);
        foreach ($results as $testName => $testValue) {
            $check = TestRanges::check((string)$testName, $testValue, (int)$report['patient_age'], $report['patient_gender']);
            $pdf->Cell(85, 8, (string)$testName, 1, 0, 'L');
            $pdf->Cell(25, 8, (string)$testValue, 1, 0, 'L');
            $pdf->Cell(43, 8, $check['range'], 1, 0, 'L');
            $pdf->SetFont('Arial', $check['style'] === 'okay' ? '' : 'B', 9);
            $pdf->Cell(37, 8, $check['status'], 1, 1, 'L');
            $pdf->SetFont('Arial', '', 9);
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
