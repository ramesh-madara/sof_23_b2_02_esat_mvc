<?php

require_once __DIR__ . '/../model/DoctorModel.php';

class AuthenticationController
{
    private DoctorModel $doctorModel;

    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
    }

    public function login(): void
    {
        if (isset($_SESSION['doctor_id'])) {
            header('Location: index.php?action=patients');
            exit;
        }

        $errorMessage = '';

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $errorMessage = 'Please provide both username and password.';
            } elseif (strlen($username) > 50 || strlen($password) > 64) {
                $errorMessage = 'Provided credentials exceed maximum length.';
            } else {
                $doctor = $this->doctorModel->findDoctorByUsername($username);

                if ($doctor && password_verify($password, $doctor['password'])) {
                    $_SESSION['doctor_id'] = (int)$doctor['id'];
                    $_SESSION['doctor_username'] = $doctor['username'];
                    $_SESSION['doctor_display_name'] = $doctor['display_name'];
                    header('Location: index.php?action=patients');
                    exit;
                } else {
                    $errorMessage = 'Invalid username or password.';
                }
            }
        }

        require __DIR__ . '/../view/login.php';
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (session_id() !== '' || headers_sent() === false) {
            session_destroy();
        }
        header('Location: index.php?action=login');
        exit;
    }
}
