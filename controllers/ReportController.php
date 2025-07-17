<?php
// controllers/ReportController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Report.php';
require_once __DIR__ . '/../config/database.php';

class ReportController {

    private $db;
    private $report;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . '/login'); exit(); }
        
        // อาจจะจำกัดสิทธิ์ให้เฉพาะ HR หรือผู้ดูแล
        if (!in_array($_SESSION['role_id'], [1, 2, 3])) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        
        $database = new Database();
        $this->db = $database->getConnection();
        $this->report = new Report($this->db);
    }

    // แสดงหน้ารายงานสรุปการลงเวลา
    public function attendance() {
        $page_title = "รายงานสรุปการลงเวลา";
        $report_data = [];
        $month = date('m');
        $year = date('Y');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $month = $_POST['month'];
            $year = $_POST['year'];
            $report_data = $this->report->getAttendanceSummary($month, $year);
        }

        require_once 'views/reports/attendance.php';
    }
}
?>
