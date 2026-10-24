<?php

require_once __DIR__ . '/../model/DoctorModel.php';

class AccountController
{
    private DoctorModel $doctorModel;

    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
    }

    public function register(): void
    {
        if (isset($_SESSION['doctor_id'])) {
            header('Location: index.php?action=patients');
            exit;
        }

        $errorMessage = '';

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $displayName = trim($_POST['display_name'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if ($displayName === '' || empty($username) || empty($password) || empty($confirmPassword)) {
                $errorMessage = 'All fields are required.';
            } elseif (mb_strlen($displayName, 'UTF-8') > 100) {
                $errorMessage = 'Display name must be 100 characters or fewer.';
            } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
                $errorMessage = 'Username must be 3-30 characters long and contain only letters, numbers, and underscores.';
            } elseif (strlen($password) < 6 || strlen($password) > 64) {
                $errorMessage = 'Password must be between 6 and 64 characters in length.';
            } elseif ($password !== $confirmPassword) {
                $errorMessage = 'Passwords do not match.';
            } else {
                $existingDoctor = $this->doctorModel->findDoctorByUsername($username);

                if ($existingDoctor) {
                    $errorMessage = 'Username is already taken.';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $registered = $this->doctorModel->createDoctor($username, $hashedPassword, $displayName);

                    if ($registered) {
                        header('Location: index.php?action=login&registered=1');
                        exit;
                    } else {
                        $errorMessage = 'Failed to register account. Please try again.';
                    }
                }
            }
        }

        require __DIR__ . '/../view/register.php';
    }

    public function manage(): void
    {
        if (!isset($_SESSION['doctor_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $doctorId = (int)$_SESSION['doctor_id'];
        $doctor = $this->doctorModel->findDoctorById($doctorId);

        $errorMessage = '';
        $successMessage = '';

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['account_action'] ?? '') === 'profile') {
            $displayName = trim($_POST['display_name'] ?? '');
            if ($displayName === '' || mb_strlen($displayName, 'UTF-8') > 100) {
                $errorMessage = 'Display name is required and must be 100 characters or fewer.';
            } elseif ($this->doctorModel->updateDisplayName($doctorId, $displayName)) {
                $_SESSION['doctor_display_name'] = $displayName;
                $doctor['display_name'] = $displayName;
                $successMessage = 'Display name updated successfully.';
            } else {
                $errorMessage = 'Unable to update display name. Please try again.';
            }
        } elseif (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $errorMessage = 'All password fields are required.';
            } elseif (strlen($newPassword) < 6 || strlen($newPassword) > 64) {
                $errorMessage = 'New password must be between 6 and 64 characters in length.';
            } elseif ($newPassword !== $confirmPassword) {
                $errorMessage = 'New passwords do not match.';
            } elseif (!password_verify($currentPassword, $doctor['password'])) {
                $errorMessage = 'Current password is incorrect.';
            } else {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updated = $this->doctorModel->updateDoctorPassword($doctorId, $hashedPassword);

                if ($updated) {
                    $successMessage = 'Password updated successfully.';
                    $doctor = $this->doctorModel->findDoctorById($doctorId);
                } else {
                    $errorMessage = 'Unable to update password. Please try again.';
                }
            }
        }

        require __DIR__ . '/../view/account.php';
    }
}
