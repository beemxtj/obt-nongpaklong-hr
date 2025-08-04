<?php
// controllers/LeaveController.php (Updated - Admin can approve all leaves)

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

// เพิ่มการตรวจสอบไฟล์ก่อน require
$required_files = [
    __DIR__ . '/../models/Leave.php',
    __DIR__ . '/../models/Employee.php',
    __DIR__ . '/../models/Notification.php',
    __DIR__ . '/../models/LeavePolicy.php',
    __DIR__ . '/../models/ApprovalWorkflow.php',
    __DIR__ . '/../helpers/RoleHelper.php'
];

foreach ($required_files as $file) {
    if (!file_exists($file)) {
        error_log("Required file not found: " . $file);
        die("System Error: Required file not found - " . basename($file));
    }
    require_once $file;
}

class LeaveController
{
    private $db;
    private $leave;
    private $leavePolicy;
    private $approvalWorkflow;

    public function __construct()
    {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            // ตรวจสอบการล็อกอิน
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . BASE_URL . '/login');
                exit();
            }

            // เชื่อมต่อฐานข้อมูล
            $database = new Database();
            $this->db = $database->getConnection();
            
            if (!$this->db) {
                throw new Exception("Database connection failed");
            }
            
            // สร้าง model instances
            $this->leave = new Leave($this->db);
            
            // ตรวจสอบว่า class LeavePolicy และ ApprovalWorkflow มีอยู่จริง
            if (class_exists('LeavePolicy')) {
                $this->leavePolicy = new LeavePolicy($this->db);
            } else {
                error_log("LeavePolicy class not found");
                $this->leavePolicy = null;
            }
            
            if (class_exists('ApprovalWorkflow')) {
                $this->approvalWorkflow = new ApprovalWorkflow($this->db);
            } else {
                error_log("ApprovalWorkflow class not found");
                $this->approvalWorkflow = null;
            }
            
        } catch (Exception $e) {
            error_log("LeaveController constructor error: " . $e->getMessage());
            die("System initialization error. Please contact administrator.");
        }
    }

    // แสดงหน้าฟอร์มสำหรับยื่นใบลา
    public function create()
    {
        try {
            $page_title = "ยื่นใบลา";
            $leave_types = $this->leave->getLeaveTypes();
            
            $view_file = __DIR__ . '/../views/leave/create.php';
            if (!file_exists($view_file)) {
                throw new Exception("View file not found: create.php");
            }
            
            require_once $view_file;
        } catch (Exception $e) {
            error_log("Error in create method: " . $e->getMessage());
            $this->showError("ไม่สามารถแสดงหน้ายื่นใบลาได้ กรุณาลองใหม่อีกครั้ง");
        }
    }

    // บันทึกคำขอการลา
    public function store() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . BASE_URL . '/leave/create');
                exit();
            }

            $this->leave->employee_id = $_SESSION['user_id'];
            $this->leave->leave_type_id = $_POST['leave_type_id'] ?? '';
            $this->leave->start_date = $_POST['start_date'] ?? '';
            $this->leave->end_date = $_POST['end_date'] ?? '';
            $this->leave->reason = $_POST['reason'] ?? '';
            
            // ตรวจสอบข้อมูลพื้นฐาน
            if (empty($this->leave->leave_type_id) || empty($this->leave->start_date) || 
                empty($this->leave->end_date) || empty($this->leave->reason)) {
                $_SESSION['error_message'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
                header('Location: ' . BASE_URL . '/leave/create');
                exit();
            }
            
            // ตรวจสอบนโยบายการลาก่อนยื่นคำขอ (ถ้ามี LeavePolicy)
            if ($this->leavePolicy) {
                $validation_result = $this->validateLeaveRequest();
                if (!$validation_result['valid']) {
                    $_SESSION['error_message'] = $validation_result['message'];
                    header('Location: ' . BASE_URL . '/leave/create');
                    exit();
                }
            }
            
            // จัดการไฟล์แนบ
            $attachment_path = null;
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
                $target_dir = "uploads/attachments/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $file_extension = pathinfo($_FILES["attachment"]["name"], PATHINFO_EXTENSION);
                $file_name = "leave_" . $this->leave->employee_id . "_" . time() . "." . $file_extension;
                $target_file = $target_dir . $file_name;

                if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
                    $attachment_path = $target_file;
                }
            }
            $this->leave->attachment_path = $attachment_path;

            // สร้างใบลา
            if ($this->leave->createLeaveRequest()) {
                // ส่ง Notification (ถ้าสามารถทำได้)
                $this->sendNotificationToSupervisor();
                
                $_SESSION['success_message'] = "ยื่นใบลาสำเร็จ";
                header('Location: ' . BASE_URL . '/leave/history');
            } else {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการยื่นใบลา";
                header('Location: ' . BASE_URL . '/leave/create');
            }
            exit();
            
        } catch (Exception $e) {
            error_log("Error in store method: " . $e->getMessage());
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในระบบ กรุณาลองใหม่อีกครั้ง";
            header('Location: ' . BASE_URL . '/leave/create');
            exit();
        }
    }

    // ส่งการแจ้งเตือนให้หัวหน้า
    private function sendNotificationToSupervisor() {
        try {
            if (!class_exists('Employee') || !class_exists('Notification')) {
                return; // ข้าม ถ้าไม่มี class ที่จำเป็น
            }
            
            $employee = new Employee($this->db);
            $employee->id = $_SESSION['user_id'];
            $employee->readOne();

            if ($employee->supervisor_id) {
                $notification = new Notification($this->db);
                $notification->user_id = $employee->supervisor_id;
                $notification->message = "มีคำขอใบลาใหม่จากคุณ " . ($_SESSION['user_name'] ?? 'พนักงาน');
                $notification->link = BASE_URL . '/leave/approval';
                $notification->create();
            }
        } catch (Exception $e) {
            error_log("Error sending notification: " . $e->getMessage());
            // ไม่ต้อง throw error เพราะไม่ใช่ส่วนหลัก
        }
    }

    // ตรวจสอบนโยบายการลา
    private function validateLeaveRequest() {
        try {
            if (!$this->leavePolicy) {
                return ['valid' => true, 'message' => ''];
            }
            
            // ดึงนโยบายการลาตามประเภท
            $policy_stmt = $this->leavePolicy->readAll();
            $policy_data = null;
            
            while ($policy = $policy_stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($policy['leave_type_id'] == $this->leave->leave_type_id) {
                    $policy_data = $policy;
                    break;
                }
            }

            if (!$policy_data) {
                return ['valid' => true, 'message' => '']; // ไม่มีนโยบาย = อนุญาต
            }

            // คำนวณจำนวนวันลา
            $start_date = new DateTime($this->leave->start_date);
            $end_date = new DateTime($this->leave->end_date);
            $leave_days = $start_date->diff($end_date)->days + 1;

            // ตรวจสอบวันแจ้งล่วงหน้า
            $notice_days = (new DateTime())->diff($start_date)->days;
            if ($notice_days < $policy_data['min_notice_days']) {
                return ['valid' => false, 'message' => "ต้องแจ้งล่วงหน้าอย่างน้อย {$policy_data['min_notice_days']} วัน"];
            }

            // ตรวจสอบวันลาติดต่อกัน
            if ($policy_data['max_consecutive_days'] > 0 && $leave_days > $policy_data['max_consecutive_days']) {
                return ['valid' => false, 'message' => "ลาติดต่อกันได้สูงสุด {$policy_data['max_consecutive_days']} วัน"];
            }

            // ตรวจสอบยอดวันลาคงเหลือ (ถ้าไม่ใช่แบบไม่จำกัด)
            if (!$policy_data['is_unlimited']) {
                $used_days = $this->getUsedLeaveDays($this->leave->employee_id, $this->leave->leave_type_id);
                $remaining_days = $policy_data['days_allowed_per_year'] - $used_days;
                
                if ($leave_days > $remaining_days) {
                    return ['valid' => false, 'message' => "วันลาไม่เพียงพอ (คงเหลือ {$remaining_days} วัน)"];
                }
            }

            return ['valid' => true, 'message' => ''];
            
        } catch (Exception $e) {
            error_log("Error in validateLeaveRequest: " . $e->getMessage());
            return ['valid' => true, 'message' => '']; // กรณี error ให้ผ่าน
        }
    }

    // คำนวณวันลาที่ใช้ไปแล้วในปีปัจจุบัน
    private function getUsedLeaveDays($employee_id, $leave_type_id) {
        try {
            $current_year = date('Y');
            $query = "SELECT SUM(DATEDIFF(end_date, start_date) + 1) as total_used
                      FROM leave_requests
                      WHERE employee_id = :employee_id
                        AND leave_type_id = :leave_type_id
                        AND status = 'อนุมัติ'
                        AND YEAR(start_date) = :year";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':employee_id', $employee_id);
            $stmt->bindParam(':leave_type_id', $leave_type_id);
            $stmt->bindParam(':year', $current_year);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['total_used'] ?? 0);
        } catch (Exception $e) {
            error_log("Error in getUsedLeaveDays: " . $e->getMessage());
            return 0;
        }
    }

    // แสดงหน้าประวัติการลาของพนักงาน
    public function history()
    {
        try {
            $page_title = "ประวัติการลา";
            
            // ตรวจสอบว่า RoleHelper มีอยู่จริง
            if (class_exists('RoleHelper')) {
                $permissions = RoleHelper::getUserPermissions();
            } else {
                // ถ้าไม่มี RoleHelper ให้ใช้ค่าเริ่มต้น
                $permissions = [
                    'is_admin' => isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1,
                    'is_hr' => isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 2])
                ];
            }

            // ตรวจสอบสิทธิ์และดึงข้อมูล
            if ($permissions['is_admin'] || $permissions['is_hr']) {
                $stmt = $this->leave->readAllHistory();
            } else {
                $stmt = $this->leave->readHistoryByEmployee($_SESSION['user_id']);
            }
            
            $num = $stmt->rowCount();
            
            $view_file = __DIR__ . '/../views/leave/history.php';
            if (!file_exists($view_file)) {
                throw new Exception("View file not found: history.php");
            }
            
            require_once $view_file;
            
        } catch (Exception $e) {
            error_log("Error in history method: " . $e->getMessage());
            $this->showError("ไม่สามารถแสดงประวัติการลาได้ กรุณาลองใหม่อีกครั้ง");
        }
    }

    // จัดการการยกเลิกใบลาโดยพนักงาน
    public function cancel($id)
    {
        try {
            if (empty($id) || !is_numeric($id)) {
                $_SESSION['error_message'] = "รหัสใบลาไม่ถูกต้อง";
                header('Location: ' . BASE_URL . '/leave/history');
                exit();
            }
            
            if ($this->leave->cancelRequest($id, $_SESSION['user_id'])) {
                $_SESSION['success_message'] = "ยกเลิกคำขอการลาสำเร็จ";
            } else {
                $_SESSION['error_message'] = "ไม่สามารถยกเลิกคำขอนี้ได้";
            }
            header('Location: ' . BASE_URL . '/leave/history');
            exit();
            
        } catch (Exception $e) {
            error_log("Error in cancel method: " . $e->getMessage());
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการยกเลิกใบลา";
            header('Location: ' . BASE_URL . '/leave/history');
            exit();
        }
    }

    // แสดงหน้ารายการรออนุมัติสำหรับหัวหน้างาน
    public function approval() {
        try {
            // ตรวจสอบสิทธิ์ - ปรับแก้ให้ Admin อนุมัติได้หมด
            $role_id = $_SESSION['role_id'] ?? 0;
            
            // Admin (role_id = 1) สามารถอนุมัติได้หมดทุกใบลา
            // HR (role_id = 2) สามารถอนุมัติได้หมดทุกใบลา  
            // Supervisor (role_id = 3) สามารถอนุมัติได้เฉพาะลูกน้อง
            if (!in_array($role_id, [1, 2, 3])) {
                $this->showError('คุณไม่มีสิทธิ์เข้าถึงหน้านี้', BASE_URL . '/dashboard');
                return;
            }

            $page_title = "รายการอนุมัติใบลา";
            
            // ดึงคำขอที่รออนุมัติ - ปรับแก้ให้ Admin เห็นหมดทุกใบลา
            $stmt = $this->getPendingRequestsForApproval();
            $num = $stmt->rowCount();
            
            $view_file = __DIR__ . '/../views/leave/approval.php';
            if (!file_exists($view_file)) {
                // ใช้ view แบบง่ายถ้าไม่พบไฟล์หลัก
                $view_file = __DIR__ . '/../views/leave/approval-simple.php';
                if (!file_exists($view_file)) {
                    throw new Exception("View file not found: approval.php");
                }
            }
            
            require_once $view_file;
            
        } catch (Exception $e) {
            error_log("Error in approval method: " . $e->getMessage());
            $this->showError("ไม่สามารถแสดงรายการอนุมัติได้ กรุณาลองใหม่อีกครั้ง");
        }
    }

    // ดึงคำขอที่รออนุมัติ - ปรับแก้ให้ Admin เห็นหมดทุกใบลา
    private function getPendingRequestsForApproval() {
        try {
            $role_id = $_SESSION['role_id'] ?? 0;
            $user_id = $_SESSION['user_id'] ?? 0;
            
            // สำหรับ Admin (role_id = 1) ให้เห็นใบลาทั้งหมดที่รออนุมัติ
            if ($role_id == 1) {
                $query = "SELECT 
                            lr.id,
                            lr.employee_id,
                            lr.leave_type_id,
                            lr.start_date,
                            lr.end_date,
                            lr.reason,
                            lr.status,
                            lr.attachment_path,
                            COALESCE(lr.created_at, lr.start_date) as created_at,
                            lt.name as leave_type_name,
                            CONCAT(COALESCE(e.prefix, ''), e.first_name_th, ' ', e.last_name_th) as employee_name,
                            COALESCE(e.employee_code, e.id) as employee_code,
                            e.department_id,
                            d.name as department_name,
                            lp.id as policy_id,
                            lp.days_allowed_per_year,
                            lp.is_unlimited,
                            lp.can_be_carried_over,
                            lp.max_carry_over_days,
                            lp.min_notice_days,
                            lp.max_consecutive_days,
                            lp.requires_approval,
                            lp.description as policy_description
                          FROM 
                            leave_requests lr
                            JOIN employees e ON lr.employee_id = e.id
                            JOIN leave_types lt ON lr.leave_type_id = lt.id
                            LEFT JOIN departments d ON e.department_id = d.id
                            LEFT JOIN leave_policies lp ON lr.leave_type_id = lp.leave_type_id
                          WHERE 
                            lr.status = 'รออนุมัติ'
                          ORDER BY 
                            lr.created_at ASC, lr.id ASC";
                
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                
            } elseif ($role_id == 2) {
                // HR สามารถเห็นได้หมด (เหมือน Admin)
                $query = "SELECT 
                            lr.id,
                            lr.employee_id,
                            lr.leave_type_id,
                            lr.start_date,
                            lr.end_date,
                            lr.reason,
                            lr.status,
                            lr.attachment_path,
                            COALESCE(lr.created_at, lr.start_date) as created_at,
                            lt.name as leave_type_name,
                            CONCAT(COALESCE(e.prefix, ''), e.first_name_th, ' ', e.last_name_th) as employee_name,
                            COALESCE(e.employee_code, e.id) as employee_code,
                            e.department_id,
                            d.name as department_name,
                            lp.id as policy_id,
                            lp.days_allowed_per_year,
                            lp.is_unlimited,
                            lp.can_be_carried_over,
                            lp.max_carry_over_days,
                            lp.min_notice_days,
                            lp.max_consecutive_days,
                            lp.requires_approval,
                            lp.description as policy_description
                          FROM 
                            leave_requests lr
                            JOIN employees e ON lr.employee_id = e.id
                            JOIN leave_types lt ON lr.leave_type_id = lt.id
                            LEFT JOIN departments d ON e.department_id = d.id
                            LEFT JOIN leave_policies lp ON lr.leave_type_id = lp.leave_type_id
                          WHERE 
                            lr.status = 'รออนุมัติ'
                          ORDER BY 
                            lr.created_at ASC, lr.id ASC";
                
                $stmt = $this->db->prepare($query);
                $stmt->execute();
                
            } else {
                // Supervisor เห็นได้เฉพาะลูกน้องของตัวเอง
                $query = "SELECT 
                            lr.id,
                            lr.employee_id,
                            lr.leave_type_id,
                            lr.start_date,
                            lr.end_date,
                            lr.reason,
                            lr.status,
                            lr.attachment_path,
                            COALESCE(lr.created_at, lr.start_date) as created_at,
                            lt.name as leave_type_name,
                            CONCAT(COALESCE(e.prefix, ''), e.first_name_th, ' ', e.last_name_th) as employee_name,
                            COALESCE(e.employee_code, e.id) as employee_code,
                            e.department_id,
                            d.name as department_name,
                            lp.id as policy_id,
                            lp.days_allowed_per_year,
                            lp.is_unlimited,
                            lp.can_be_carried_over,
                            lp.max_carry_over_days,
                            lp.min_notice_days,
                            lp.max_consecutive_days,
                            lp.requires_approval,
                            lp.description as policy_description
                          FROM 
                            leave_requests lr
                            JOIN employees e ON lr.employee_id = e.id
                            JOIN leave_types lt ON lr.leave_type_id = lt.id
                            LEFT JOIN departments d ON e.department_id = d.id
                            LEFT JOIN leave_policies lp ON lr.leave_type_id = lp.leave_type_id
                          WHERE 
                            lr.status = 'รออนุมัติ'
                            AND e.supervisor_id = :user_id
                          ORDER BY 
                            lr.created_at ASC, lr.id ASC";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
            }
            
            return $stmt;
            
        } catch (Exception $e) {
            error_log("Error in getPendingRequestsForApproval: " . $e->getMessage());
            
            // ถ้า query ข้างบน error ให้ใช้ query ง่ายๆ แทน
            $simple_query = "SELECT 
                            lr.*,
                            lt.name as leave_type_name,
                            CONCAT(COALESCE(e.first_name_th, ''), ' ', COALESCE(e.last_name_th, '')) as employee_name,
                            COALESCE(e.employee_code, e.id) as employee_code
                          FROM 
                            leave_requests lr
                            LEFT JOIN employees e ON lr.employee_id = e.id
                            LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
                          WHERE 
                            lr.status = 'รออนุมัติ'
                          ORDER BY 
                            lr.id DESC";
            
            $stmt = $this->db->prepare($simple_query);
            $stmt->execute();
            return $stmt;
        }
    }

    // ตรวจสอบสิทธิ์ในการอนุมัติ - ปรับแก้ให้ Admin อนุมัติได้หมด
    private function canApproveRequest($leave_request) {
        $role_id = $_SESSION['role_id'] ?? 0;
        $user_id = $_SESSION['user_id'] ?? 0;
        
        // Admin (role_id = 1) สามารถอนุมัติได้ทุกคำขอ
        if ($role_id == 1) {
            return true;
        }
        
        // HR (role_id = 2) สามารถอนุมัติได้ทุกคำขอ
        if ($role_id == 2) {
            return true;
        }
        
        // Supervisor (role_id = 3) สามารถอนุมัติได้เฉพาะลูกน้องของตัวเอง
        if ($role_id == 3) {
            try {
                if (!class_exists('Employee')) {
                    return false;
                }
                
                $employee = new Employee($this->db);
                $employee->id = $leave_request['employee_id'];
                $employee->readOne();
                
                return $employee->supervisor_id == $user_id;
            } catch (Exception $e) {
                error_log("Error checking supervisor permission: " . $e->getMessage());
                return false;
            }
        }
        
        return false;
    }

    // อนุมัติใบลา - ปรับแก้ให้ Admin อนุมัติได้หมด
    public function approve($id) {
        try {
            if (empty($id) || !is_numeric($id)) {
                $_SESSION['error_message'] = "รหัสใบลาไม่ถูกต้อง";
                header('Location: ' . BASE_URL . '/leave/approval');
                exit();
            }
            
            $this->leave->id = $id;
            $leave_request = $this->leave->readOne();
            
            if (!$leave_request) {
                $_SESSION['error_message'] = "ไม่พบคำขอการลา";
                header('Location: ' . BASE_URL . '/leave/approval');
                exit();
            }
            
            // ตรวจสอบสิทธิ์ในการอนุมัติ
            if (!$this->canApproveRequest($leave_request)) {
                $_SESSION['error_message'] = "คุณไม่มีสิทธิ์อนุมัติคำขอนี้";
                header('Location: ' . BASE_URL . '/leave/approval');
                exit();
            }
            
            if ($this->leave->updateStatus('อนุมัติ')) {
                // บันทึกประวัติการอนุมัติ
                $this->logApprovalAction($id, 'approve', $_SESSION['user_id']);
                
                // ส่ง Notification กลับไปหาพนักงาน
                $this->sendApprovalNotification($leave_request['employee_id'], 'อนุมัติ');
                
                $_SESSION['success_message'] = "อนุมัติใบลาเรียบร้อยแล้ว";
            } else {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการอนุมัติใบลา";
            }

            header('Location: ' . BASE_URL . '/leave/approval');
            exit();
            
        } catch (Exception $e) {
            error_log("Error in approve method: " . $e->getMessage());
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการอนุมัติใบลา";
            header('Location: ' . BASE_URL . '/leave/approval');
            exit();
        }
    }

    // ปฏิเสธใบลา - ปรับแก้ให้ Admin ปฏิเสธได้หมด
    public function reject($id) {
        try {
            if (empty($id) || !is_numeric($id)) {
                $_SESSION['error_message'] = "รหัสใบลาไม่ถูกต้อง";
                header('Location: ' . BASE_URL . '/leave/approval');
                exit();
            }
            
            $this->leave->id = $id;
            $leave_request = $this->leave->readOne();

            if (!$leave_request) {
                $_SESSION['error_message'] = "ไม่พบคำขอการลา";
                header('Location: ' . BASE_URL . '/leave/approval');
                exit();
            }
            
            // ตรวจสอบสิทธิ์ในการปฏิเสธ
            if (!$this->canApproveRequest($leave_request)) {
                $_SESSION['error_message'] = "คุณไม่มีสิทธิ์ปฏิเสธคำขอนี้";
                header('Location: ' . BASE_URL . '/leave/approval');
                exit();
            }

            if ($this->leave->updateStatus('ไม่อนุมัติ')) {
                // บันทึกประวัติการอนุมัติ
                $this->logApprovalAction($id, 'reject', $_SESSION['user_id']);
                
                // ส่ง Notification กลับไปหาพนักงาน
                $this->sendApprovalNotification($leave_request['employee_id'], 'ไม่อนุมัติ');
                
                $_SESSION['success_message'] = "ปฏิเสธใบลาเรียบร้อยแล้ว";
            } else {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการปฏิเสธใบลา";
            }

            header('Location: ' . BASE_URL . '/leave/approval');
            exit();
            
        } catch (Exception $e) {
            error_log("Error in reject method: " . $e->getMessage());
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการปฏิเสธใบลา";
            header('Location: ' . BASE_URL . '/leave/approval');
            exit();
        }
    }

    // บันทึกประวัติการอนุมัติ
    private function logApprovalAction($leave_request_id, $action, $approver_id) {
        try {
            // ตรวจสอบว่าตาราง leave_approval_logs มีอยู่หรือไม่
            $check_table = "SHOW TABLES LIKE 'leave_approval_logs'";
            $stmt = $this->db->query($check_table);
            
            if ($stmt->rowCount() > 0) {
                $query = "INSERT INTO leave_approval_logs (leave_request_id, approver_id, action, action_date) 
                          VALUES (:leave_request_id, :approver_id, :action, NOW())";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':leave_request_id', $leave_request_id);
                $stmt->bindParam(':approver_id', $approver_id);
                $stmt->bindParam(':action', $action);
                $stmt->execute();
            }
        } catch (Exception $e) {
            error_log("Error logging approval action: " . $e->getMessage());
            // ไม่ throw error เพราะไม่ใช่ฟีเจอร์หลัก
        }
    }

    // ส่งการแจ้งเตือนผลการอนุมัติ
    private function sendApprovalNotification($employee_id, $status) {
        try {
            if (!class_exists('Notification')) {
                return;
            }
            
            $notification = new Notification($this->db);
            $notification->user_id = $employee_id;
            $notification->message = "ใบลาของคุณได้รับการ" . $status . "แล้ว";
            $notification->link = BASE_URL . '/leave/history';
            $notification->create();
        } catch (Exception $e) {
            error_log("Error sending approval notification: " . $e->getMessage());
        }
    }

    // แสดงหน้า error
    private function showError($message, $redirect_url = null) {
        $_SESSION['error_message'] = $message;
        
        if ($redirect_url) {
            header('Location: ' . $redirect_url);
            exit();
        } else {
            echo "<div style='padding: 20px; text-align: center;'>";
            echo "<h3>เกิดข้อผิดพลาด</h3>";
            echo "<p>" . htmlspecialchars($message) . "</p>";
            echo "<a href='" . BASE_URL . "/dashboard'>กลับหน้าหลัก</a>";
            echo "</div>";
        }
    }

    // เพิ่ม method สำหรับ debugging
    public function debug() {
        if (defined('APP_DEBUG') && APP_DEBUG) {
            echo "<h2>LeaveController Debug Information</h2>";
            echo "<p><strong>Session Data:</strong></p>";
            echo "<pre>" . print_r($_SESSION, true) . "</pre>";
            
            echo "<p><strong>Database Connection:</strong> " . ($this->db ? "Connected" : "Failed") . "</p>";
            
            echo "<p><strong>User Role ID:</strong> " . ($_SESSION['role_id'] ?? 'Not Set') . "</p>";
            echo "<p><strong>Can Access Approval:</strong> " . (in_array($_SESSION['role_id'] ?? 0, [1, 2, 3]) ? 'Yes' : 'No') . "</p>";
            
            if ($_SESSION['role_id'] ?? 0 == 1) {
                echo "<p><strong>Admin Status:</strong> ✅ Can approve ALL leave requests</p>";
            } elseif ($_SESSION['role_id'] ?? 0 == 2) {
                echo "<p><strong>HR Status:</strong> ✅ Can approve ALL leave requests</p>";
            } elseif ($_SESSION['role_id'] ?? 0 == 3) {
                echo "<p><strong>Supervisor Status:</strong> ⚠️ Can approve only subordinates' requests</p>";
            } else {
                echo "<p><strong>Employee Status:</strong> ❌ Cannot approve requests</p>";
            }
            
            echo "<p><strong>Available Methods:</strong></p>";
            echo "<ul>";
            $methods = get_class_methods($this);
            foreach ($methods as $method) {
                echo "<li>" . $method . "</li>";
            }
            echo "</ul>";
            
            if ($this->leave) {
                echo "<p><strong>Leave Model:</strong> Available</p>";
                
                // ทดสอบดึงข้อมูล
                try {
                    $stmt = $this->getPendingRequestsForApproval();
                    $count = $stmt->rowCount();
                    echo "<p><strong>Pending Requests:</strong> {$count} items</p>";
                } catch (Exception $e) {
                    echo "<p><strong>Pending Requests Error:</strong> " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p><strong>Leave Model:</strong> Not Available</p>";
            }
        } else {
            echo "Debug mode is disabled";
        }
    }
}
?>