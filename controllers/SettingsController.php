<?php
// controllers/SettingsController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Setting.php';
require_once __DIR__ . '/../config/database.php';

class SettingsController {

    private $db;
    private $setting;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        
        // Restrict access to Admins only (role_id = 1)
        if ($_SESSION['role_id'] != 1) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        
        $database = new Database();
        $this->db = $database->getConnection();
        $this->setting = new Setting($this->db);
    }

    /**
     * Display the settings page.
     */
    public function index() {
        $page_title = "ตั้งค่าระบบ";
        $settings = $this->setting->getAllSettings();
        require_once 'views/settings/index.php';
    }

    /**
     * Update settings from the form submission.
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Array of settings to save from the form
            $settings_to_save = [
                'org_name' => $_POST['org_name'] ?? '',
                'org_address' => $_POST['org_address'] ?? '',
                'work_start_time' => $_POST['work_start_time'] ?? '08:30',
                'work_end_time' => $_POST['work_end_time'] ?? '17:30',
                'grace_period_minutes' => $_POST['grace_period_minutes'] ?? 15,
                'ot_start_time' => $_POST['ot_start_time'] ?? '18:00'
            ];

            // Handle logo upload
            if (isset($_FILES['org_logo']) && $_FILES['org_logo']['error'] == 0) {
                $target_dir = "uploads/logo/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $file_name = "logo_" . time() . "." . pathinfo($_FILES["org_logo"]["name"], PATHINFO_EXTENSION);
                $target_file = $target_dir . $file_name;

                // Attempt to move the uploaded file
                if (move_uploaded_file($_FILES["org_logo"]["tmp_name"], $target_file)) {
                    $settings_to_save['org_logo'] = $target_file;
                }
            }

            // Save all settings to the database
            if ($this->setting->saveSettings($settings_to_save)) {
                $_SESSION['success_message'] = "บันทึกการตั้งค่าสำเร็จ";
            } else {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการบันทึกการตั้งค่า";
            }
            
            header('Location: ' . BASE_URL . '/settings');
            exit();
        }
    }
}
?>
