<?php
// controllers/PayslipController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Payroll.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/RoleHelper.php';

class PayslipController
{
    private $db;
    private $payroll;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->payroll = new Payroll($this->db);
    }

    /**
     * แสดงหน้าประวัติสลิปเงินเดือนของพนักงานที่ล็อกอิน
     */
    public function history()
    {
        $page_title = "ประวัติสลิปเงินเดือน";
        $employee_id = $_SESSION['user_id'];
        
        $stmt = $this->payroll->readForEmployee($employee_id);
        $num = $stmt->rowCount();

        // Array เดือนภาษาไทยสำหรับ View
        $thai_months = [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม',
            4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน',
            7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน',
            10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
        ];

        // แก้ไขพาธที่นี่
        require_once 'views/payslips/history.php';
    }

    /**
     * แสดงรายละเอียดสลิปเงินเดือน 1 ใบ
     */
    public function view($id)
    {
        $payslip_data = $this->payroll->readOne($id);

        // ตรวจสอบสิทธิ์: อนุญาตให้เจ้าของสลิป หรือ Admin/HR ดูได้เท่านั้น
        if (!$payslip_data || !(RoleHelper::canAccessEmployeeData($payslip_data['employee_id']))) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงข้อมูลนี้";
            header('Location: ' . BASE_URL . '/payslip/history');
            exit();
        }
        
        // คำนวณยอดรวมสำหรับ View
        $payslip_data['total_earnings'] = $payslip_data['base_salary'] + $payslip_data['overtime_pay'] + $payslip_data['allowances'];
        $payslip_data['total_deductions'] = $payslip_data['late_deductions'] + $payslip_data['absence_deductions'] + $payslip_data['social_security'] + $payslip_data['tax'];

        $page_title = "สลิปเงินเดือน";
        
        $thai_months = [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม',
            4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน',
            7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน',
            10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
        ];
        
        // ส่งเดือนและปีไปให้ View
        $payslip_data['pay_period_month'] = date('n', strtotime($payslip_data['pay_period_start']));
        $payslip_data['pay_period_year'] = date('Y', strtotime($payslip_data['pay_period_start']));

        require_once 'views/payroll/view.php';
    }
}