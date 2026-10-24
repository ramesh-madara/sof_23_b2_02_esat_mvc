<?php

session_start();
if (isset($_SESSION['doctor_id'])) {
    require_once __DIR__ . '/model/DoctorModel.php';
    $currentDoctor = (new DoctorModel())->findDoctorById((int)$_SESSION['doctor_id']);
    if ($currentDoctor) {
        $_SESSION['doctor_display_name'] = $currentDoctor['display_name'];
    } else {
        $_SESSION = [];
    }
}

$action = $_GET['action'] ?? 'patients';

switch ($action) {
    case 'login':
        require_once __DIR__ . '/controller/AuthenticationController.php';
        $authController = new AuthenticationController();
        $authController->login();
        break;

    case 'logout':
        require_once __DIR__ . '/controller/AuthenticationController.php';
        $authController = new AuthenticationController();
        $authController->logout();
        break;

    case 'register':
        require_once __DIR__ . '/controller/AccountController.php';
        $accountController = new AccountController();
        $accountController->register();
        break;

    case 'account':
        require_once __DIR__ . '/controller/AccountController.php';
        $accountController = new AccountController();
        $accountController->manage();
        break;

    case 'patients':
        require_once __DIR__ . '/controller/PatientController.php';
        $patientController = new PatientController();
        $patientController->index();
        break;

    case 'patient_create':
        require_once __DIR__ . '/controller/PatientController.php';
        $patientController = new PatientController();
        $patientController->create();
        break;

    case 'patient_view':
        require_once __DIR__ . '/controller/PatientController.php';
        $patientController = new PatientController();
        $patientController->view();
        break;

    case 'report_create':
        require_once __DIR__ . '/controller/ReportController.php';
        $reportController = new ReportController();
        $reportController->create();
        break;

    case 'report_view':
        require_once __DIR__ . '/controller/ReportController.php';
        $reportController = new ReportController();
        $reportController->view();
        break;

    case 'report_edit':
        require_once __DIR__ . '/controller/ReportController.php';
        $reportController = new ReportController();
        $reportController->edit();
        break;

    case 'report_delete':
        require_once __DIR__ . '/controller/ReportController.php';
        $reportController = new ReportController();
        $reportController->delete();
        break;

    case 'report_pdf':
        require_once __DIR__ . '/controller/ReportController.php';
        $reportController = new ReportController();
        $reportController->exportPdf();
        break;

    default:
        header('Location: index.php?action=patients');
        exit;
}
