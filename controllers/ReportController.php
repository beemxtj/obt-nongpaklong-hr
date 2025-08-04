<?php
// controllers/ReportController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Report.php';
<<<<<<< HEAD
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/Leave.php';
=======
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
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
<<<<<<< HEAD
    // ===== แสดงหน้ารายงานใบลงเวลา =====
    public function timesheet() {
        $page_title = "รายงานใบลงเวลาทำงาน";
        $report_data = [];
        $selected_employee = null;
        $month = date('m');
        $year = date('Y');

        // ดึงรายชื่อพนักงานทั้งหมดสำหรับ dropdown
        $employee_model = new Employee($this->db);
        $employees = $employee_model->read();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $employee_id = $_POST['employee_id'];
            $month = $_POST['month'];
            $year = $_POST['year'];
            
            // ดึงข้อมูลพนักงานที่เลือก
            $employee_model->id = $employee_id;
            $employee_model->readOne();
            $selected_employee = $employee_model;

            $report_data = $this->report->getMonthlyTimesheet($employee_id, $month, $year);
        }

        require_once 'views/reports/timesheet.php';
    }
/**
     * ===== ฟังก์ชันใหม่: แสดงหน้ารายงานการลาแบบละเอียด =====
     */
    public function leaveSummary() {
        $page_title = "รายงานสรุปการลา";

        // รับค่า filter จากฟอร์ม
        $filters = [
            'start_date'    => $_POST['start_date'] ?? null,
            'end_date'      => $_POST['end_date'] ?? null,
            'department_id' => $_POST['department_id'] ?? null,
            'leave_type_id' => $_POST['leave_type_id'] ?? null,
        ];

        // ดึงข้อมูลรายงาน
        $report_data = $this->report->getLeaveReport($filters);

        // ดึงข้อมูลสำหรับ dropdown ในฟอร์ม
        $employee_model = new Employee($this->db);
        $departments = $employee_model->readDepartments();
        $leave_model = new Leave($this->db);
        $leave_types = $leave_model->getLeaveTypes();

        require_once 'views/reports/leave_summary.php';
    }

    /**
     * ===== ฟังก์ชันใหม่: ส่งออกรายงานเป็น CSV =====
     */
    public function exportLeaveSummary() {
        // รับค่า filter จาก URL parameters
        $filters = [
            'start_date'    => $_GET['start_date'] ?? null,
            'end_date'      => $_GET['end_date'] ?? null,
            'department_id' => $_GET['department_id'] ?? null,
            'leave_type_id' => $_GET['leave_type_id'] ?? null,
        ];

        $stmt = $this->report->getLeaveReport($filters);

        // ตั้งค่า HTTP Headers สำหรับการดาวน์โหลดไฟล์ CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=leave_report_' . date('Y-m-d') . '.csv');

        // เปิด output stream
        $output = fopen('php://output', 'w');
        // เพิ่ม BOM สำหรับ UTF-8 เพื่อให้ Excel เปิดไฟล์ภาษาไทยได้ถูกต้อง
        fputs($output, "\xEF\xBB\xBF");

        // เขียนหัวตาราง
        fputcsv($output, ['รหัสพนักงาน', 'ชื่อ-นามสกุล', 'แผนก', 'ประเภทการลา', 'วันที่เริ่ม', 'วันที่สิ้นสุด', 'จำนวนวัน', 'สถานะ', 'เหตุผล']);

        // วนลูปเขียนข้อมูลลงไฟล์
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, [
                $row['employee_code'],
                $row['full_name'],
                $row['department_name'],
                $row['leave_type_name'],
                $row['start_date'],
                $row['end_date'],
                $row['total_days'],
                $row['status'],
                $row['reason']
            ]);
        }

        fclose($output);
        exit();
    }
/**
     * ===== ฟังก์ชันใหม่: แสดงหน้าสำหรับ Export Payroll =====
     */
    public function payroll() {
        $page_title = "ส่งออกข้อมูลสำหรับ Payroll";
        // หน้านี้ไม่จำเป็นต้องดึงข้อมูลมาแสดง แค่แสดงฟอร์ม
        require_once 'views/reports/payroll.php';
    }


    /**
     * ===== ฟังก์ชันใหม่: จัดการการ Export ข้อมูลสำหรับ Payroll =====
     */
    public function exportForPayroll() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            exit('Invalid request method.');
        }

        $month = $_POST['month'];
        $year = $_POST['year'];

        $stmt = $this->report->getPayrollExportData($month, $year);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=payroll_export_' . $year . '-' . $month . '.csv');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // BOM for UTF-8

        // เขียนหัวตาราง
        fputcsv($output, ['รหัสพนักงาน', 'ชื่อ-นามสกุล', 'จำนวนวันลาที่ไม่ได้รับค่าจ้าง']);

        // เขียนข้อมูล
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, [
                $row['employee_code'],
                $row['full_name'],
                $row['total_unpaid_days']
            ]);
        }

        fclose($output);
        exit();
    }
}
?>
=======
}
?>
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
