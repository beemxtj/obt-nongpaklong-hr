<?php
// controllers/PayrollController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Payroll.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/RoleHelper.php';

class PayrollController
{
    private $db;
    private $payroll;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        RoleHelper::requirePermission('view_payroll'); // HR & Admin only

        $database = new Database();
        $this->db = $database->getConnection();
        $this->payroll = new Payroll($this->db);
    }

    /**
     * แสดงหน้าหลักของระบบเงินเดือน
     */
    public function index()
    {
        $page_title = "จัดการเงินเดือน";
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');

        $payrolls_stmt = $this->payroll->readByPeriod($month, $year);
        $is_generated = $payrolls_stmt->rowCount() > 0;
        
        require_once 'views/payroll/index.php';
    }

    /**
     * สร้างเงินเดือนสำหรับพนักงานทุกคนในเดือนที่เลือก
     */
    public function generate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $month = $_POST['month'];
            $year = $_POST['year'];

            // Check if already generated for this period
            if ($this->payroll->checkIfGenerated($month, $year)) {
                $_SESSION['error_message'] = "เงินเดือนสำหรับงวด $month/$year ถูกสร้างไว้แล้ว";
                header('Location: ' . BASE_URL . "/payroll?month=$month&year=$year");
                exit();
            }

            // Get all active employees
            $employee = new Employee($this->db);
            $employees_stmt = $employee->read();

            $pay_period_start = "$year-$month-01";
            $pay_period_end = date("Y-m-t", strtotime($pay_period_start));
            
            while ($emp = $employees_stmt->fetch(PDO::FETCH_ASSOC)) {
                $calculated_data = $this->payroll->calculatePayrollForEmployee($emp['id'], $month, $year);

                $this->payroll->employee_id = $emp['id'];
                $this->payroll->pay_period_start = $pay_period_start;
                $this->payroll->pay_period_end = $pay_period_end;
                
                // Assign calculated values
                foreach ($calculated_data as $key => $value) {
                    $this->payroll->{$key} = $value;
                }
                
                $this->payroll->create();
            }

            $_SESSION['success_message'] = "สร้างข้อมูลเงินเดือนสำหรับงวด $month/$year สำเร็จ";
            header('Location: ' . BASE_URL . "/payroll?month=$month&year=$year");
            exit();
        }
    }
    
    /**
     * แสดงสลิปเงินเดือน
     */
    public function view($id) {
        $page_title = "สลิปเงินเดือน";
        $payslip = $this->payroll->readOne($id);
        
        if (!$payslip) {
            $_SESSION['error_message'] = "ไม่พบข้อมูลสลิปเงินเดือน";
            header('Location: ' . BASE_URL . '/payroll');
            exit();
        }
        
        require_once 'views/payroll/view.php';
    }
}