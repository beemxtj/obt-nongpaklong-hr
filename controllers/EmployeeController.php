<?php
// controllers/EmployeeController.php

// ===== จุดที่แก้ไข: เพิ่มการเรียกใช้ไฟล์ Autoload ของ Composer =====
require_once __DIR__ . '/../vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\IOFactory;

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../config/database.php';

class EmployeeController {

    private $db;
    private $employee;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . '/login'); exit(); }
        
        $database = new Database();
        $this->db = $database->getConnection();
        $this->employee = new Employee($this->db);
    }

    public function index() {
        $stmt = $this->employee->read();
        $num = $stmt->rowCount();
        require_once 'views/employee/index.php';
    }

    public function create() {
        $page_title = "เพิ่มพนักงานใหม่";
        $employee = new Employee($this->db);
        $employee->employee_code = $this->employee->generateNewEmployeeCode();
        $positions = $this->employee->readPositions();
        $departments = $this->employee->readDepartments();
        $roles = $this->employee->readRoles();
        $supervisors = $this->employee->read();
        require_once 'views/employee/form.php';
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->employee->prefix = $_POST['prefix'];
            $this->employee->first_name_th = $_POST['first_name_th'];
            $this->employee->last_name_th = $_POST['last_name_th'];
            $this->employee->email = $_POST['email'];
            $this->employee->start_date = $_POST['start_date'];
            $this->employee->status = $_POST['status'];
            $this->employee->position_id = $_POST['position_id'];
            $this->employee->department_id = $_POST['department_id'];
            $this->employee->supervisor_id = !empty($_POST['supervisor_id']) ? $_POST['supervisor_id'] : null;
            $this->employee->role_id = $_POST['role_id'];
            $this->employee->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            if($this->employee->create()) {
                $_SESSION['success_message'] = "บันทึกข้อมูลพนักงานใหม่สำเร็จ";
                header('Location: ' . BASE_URL . '/employee');
                exit();
            } else {
                $_SESSION['error_message'] = "ไม่สามารถบันทึกข้อมูลได้";
                header('Location: ' . BASE_URL . '/employee/create');
                exit();
            }
        }
    }
    
    public function edit($id) {
        $page_title = "แก้ไขข้อมูลพนักงาน";
        $this->employee->id = $id;
        $this->employee->readOne();
        $employee = $this->employee;
        $positions = $this->employee->readPositions();
        $departments = $this->employee->readDepartments();
        $roles = $this->employee->readRoles();
        $supervisors = $this->employee->read();
        require_once 'views/employee/form.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->employee->id = $_POST['id'];
            $this->employee->employee_code = $_POST['employee_code'];
            $this->employee->prefix = $_POST['prefix'];
            $this->employee->first_name_th = $_POST['first_name_th'];
            $this->employee->last_name_th = $_POST['last_name_th'];
            $this->employee->email = $_POST['email'];
            $this->employee->start_date = $_POST['start_date'];
            $this->employee->status = $_POST['status'];
            $this->employee->position_id = $_POST['position_id'];
            $this->employee->department_id = $_POST['department_id'];
            $this->employee->supervisor_id = !empty($_POST['supervisor_id']) ? $_POST['supervisor_id'] : null;
            $this->employee->role_id = $_POST['role_id'];
            if (!empty($_POST['password'])) {
                $this->employee->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            } else {
                $this->employee->password = null;
            }
            if ($this->employee->update()) {
                $_SESSION['success_message'] = "อัปเดตข้อมูลพนักงานสำเร็จ";
                header('Location: ' . BASE_URL . '/employee');
                exit();
            } else {
                $_SESSION['error_message'] = "ไม่สามารถอัปเดตข้อมูลได้";
                header('Location: ' . BASE_URL . '/employee/edit/' . $_POST['id']);
                exit();
            }
        }
    }

    public function destroy($id) {
        $this->employee->id = $id;
        if ($this->employee->delete()) {
            $_SESSION['success_message'] = "ลบข้อมูลพนักงานสำเร็จ";
        } else {
            $_SESSION['error_message'] = "ไม่สามารถลบข้อมูลได้";
        }
        header('Location: ' . BASE_URL . '/employee');
        exit();
    }
    
    public function import() {
        require_once 'views/employee/import.php';
    }

    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
            
            $file = $_FILES['excel_file']['tmp_name'];

            // ตรวจสอบว่าไฟล์ถูกอัปโหลดสำเร็จหรือไม่
            if ($_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['import_status'] = "เกิดข้อผิดพลาดในการอัปโหลดไฟล์: Error Code " . $_FILES['excel_file']['error'];
                header('Location: ' . BASE_URL . '/employee/import');
                exit();
            }
            
            try {
                $spreadsheet = IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();
                
                // ===== จุดที่แก้ไข: เพิ่มการตรวจสอบจำนวนแถวในไฟล์ =====
                if ($highestRow <= 1) {
                    $_SESSION['import_status'] = "ไม่พบข้อมูลสำหรับนำเข้าในไฟล์ Excel\nกรุณาตรวจสอบว่าไฟล์มีข้อมูลตั้งแต่แถวที่ 2 เป็นต้นไป";
                    header('Location: ' . BASE_URL . '/employee/import');
                    exit();
                }
                
                $successCount = 0;
                $errorCount = 0;
                $errorDetails = [];

                // เริ่มอ่านข้อมูลตั้งแต่แถวที่ 2 (แถวแรกคือ Header)
                for ($row = 2; $row <= $highestRow; $row++) {
                    // รูปแบบคอลัมน์ที่คาดหวัง (ปรับแก้ตามไฟล์ Excel ของคุณ)
                    // A=คำนำหน้า, B=ชื่อ, C=นามสกุล, D=อีเมล, E=รหัสผ่านเริ่มต้น
                    $prefix = $sheet->getCell('A' . $row)->getValue();
                    $firstName = $sheet->getCell('B' . $row)->getValue();
                    $lastName = $sheet->getCell('C' . $row)->getValue();
                    $email = $sheet->getCell('D' . $row)->getValue();
                    $password = $sheet->getCell('E' . $row)->getValue() ?? 'password123';

                    // ตรวจสอบข้อมูลเบื้องต้น
                    if (empty($firstName) || empty($lastName) || empty($email)) {
                        $errorCount++;
                        $errorDetails[] = "แถวที่ {$row}: ข้อมูลไม่ครบถ้วน";
                        continue; // ข้ามไปแถวถัดไป
                    }

                    // เตรียมข้อมูลสำหรับบันทึก
                    $this->employee->prefix = $prefix;
                    $this->employee->first_name_th = $firstName;
                    $this->employee->last_name_th = $lastName;
                    $this->employee->email = $email;
                    $this->employee->password = password_hash($password, PASSWORD_DEFAULT);
                    // กำหนดค่าเริ่มต้นอื่นๆ
                    $this->employee->start_date = date('Y-m-d');
                    $this->employee->status = 'ทดลองงาน';
                    $this->employee->position_id = 1; // ID ตำแหน่งเริ่มต้น
                    $this->employee->department_id = 1; // ID แผนกเริ่มต้น
                    $this->employee->role_id = 4; // ID สิทธิ์พนักงานเริ่มต้น

                    if ($this->employee->create()) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errorDetails[] = "แถวที่ {$row}: ไม่สามารถบันทึกข้อมูลได้ (อาจมีอีเมลซ้ำ)";
                    }
                }

                $statusMessage = "นำเข้าข้อมูลสำเร็จ!\n";
                $statusMessage .= "- บันทึกสำเร็จ: {$successCount} รายการ\n";
                $statusMessage .= "- เกิดข้อผิดพลาด: {$errorCount} รายการ";
                if(!empty($errorDetails)) {
                    $statusMessage .= "\n\nรายละเอียดข้อผิดพลาด:\n" . implode("\n", $errorDetails);
                }
                $_SESSION['import_status'] = $statusMessage;

            } catch (Exception $e) {
                $_SESSION['import_status'] = "เกิดข้อผิดพลาดในการอ่านไฟล์ Excel: " . $e->getMessage();
            }

            header('Location: ' . BASE_URL . '/employee/import');
            exit();
        }
    }
}
?>
