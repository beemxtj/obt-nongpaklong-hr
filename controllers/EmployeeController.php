<?php
<<<<<<< HEAD
// controllers/EmployeeController.php - Enhanced with RoleHelper integration

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/PreviousIncome.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/RoleHelper.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class EmployeeController
{
    private $db;
    private $employee;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

=======
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
        
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
        $database = new Database();
        $this->db = $database->getConnection();
        $this->employee = new Employee($this->db);
    }

<<<<<<< HEAD
    public function index()
    {
        // Check permissions
        RoleHelper::requirePermission('manage_employees');
        
        $page_title = "จัดการข้อมูลพนักงาน";
        $stmt = $this->employee->read();
        $num = $stmt->rowCount();
        
        // Get user permissions for view
        $permissions = RoleHelper::getUserPermissions();
        
        require_once 'views/employee/index.php';
    }

    public function create() 
    {
        // Check permissions
        RoleHelper::requirePermission('manage_employees');
        
        $page_title = "เพิ่มพนักงานใหม่"; 
        $employee = new Employee($this->db);
        $employee->employee_code = $this->employee->generateNewEmployeeCode();
        
        // Set edit mode flag - this is CREATE mode
        $is_edit_mode = false;
        
        require_once 'views/employee/form.php';
    }

    public function edit($id) 
    {
        // Check permissions
        RoleHelper::requirePermission('manage_employees');
        
        $page_title = "แก้ไขข้อมูลพนักงาน"; 
        $this->employee->id = $id;
        $this->employee->readOne();
        $employee = $this->employee;
        
        if (!$employee->id) {
            $_SESSION['error_message'] = "ไม่พบข้อมูลพนักงานที่ต้องการแก้ไข";
            header('Location: ' . BASE_URL . '/employee');
            exit();
        }
        
        // Set edit mode flag - this is EDIT mode
        $is_edit_mode = true;
        
        require_once 'views/employee/form.php';
    }

    public function view($id) 
    {
        // Check if user can access this employee data
        if (!RoleHelper::canAccessEmployeeData($id)) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์ดูข้อมูลพนักงานนี้";
            header('Location: ' . BASE_URL . '/employee');
            exit();
        }
        
        $page_title = "ข้อมูลพนักงาน";
        $this->employee->id = $id;
        $this->employee->readOne();
        $employee = $this->employee;
        
        if (!$employee->id) {
            $_SESSION['error_message'] = "ไม่พบข้อมูลพนักงานที่ต้องการดู";
            header('Location: ' . BASE_URL . '/employee');
            exit();
        }
        
        // Get user permissions
        $permissions = RoleHelper::getUserPermissions();
        
        require_once 'views/employee/view.php';
    }

    public function store()
    {
        // Check permissions
        RoleHelper::requirePermission('manage_employees');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Generate new employee code for new employee
                $this->employee->employee_code = $this->employee->generateNewEmployeeCode();

                // Handle profile image upload first
                try {
                    $this->employee->profile_image_path = $this->handleProfileImageUpload();
                } catch (Exception $img_e) {
                    throw new Exception("ข้อผิดพลาดในการอัปโหลดรูปภาพ: " . $img_e->getMessage());
                }

                // Assign POST values to employee properties
                $this->employee->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $this->employee->prefix = $_POST['prefix'];
                $this->employee->first_name_th = $_POST['first_name_th'];
                $this->employee->last_name_th = $_POST['last_name_th'];
                $this->employee->first_name_en = $_POST['first_name_en'] ?? null;
                $this->employee->last_name_en = $_POST['last_name_en'] ?? null;
                $this->employee->gender = $_POST['gender'] ?? null;
                $this->employee->birth_date = $_POST['birth_date'] ?? null;
                $this->employee->nationality = $_POST['nationality'] ?? null;
                $this->employee->email = $_POST['email'];
                $this->employee->phone_number = $_POST['phone_number'] ?? null;
                $this->employee->work_phone = $_POST['work_phone'] ?? null;
                $this->employee->national_id = $_POST['national_id'] ?? null;
                $this->employee->address_line1 = $_POST['address_line1'] ?? null;
                $this->employee->district = $_POST['district'] ?? null;
                $this->employee->province = $_POST['province'] ?? null;
                $this->employee->postal_code = $_POST['postal_code'] ?? null;
                $this->employee->start_date = $_POST['start_date'];
                $this->employee->probation_days = $_POST['probation_days'] ?? 0;
                $this->employee->status = $_POST['status'];
                $this->employee->position_id = $_POST['position_id'];
                $this->employee->department_id = $_POST['department_id'];
                $this->employee->supervisor_id = $_POST['supervisor_id'] ?? null;
                $this->employee->role_id = $_POST['role_id'];
                $this->employee->salary = $_POST['salary'] ?? 0.00;
                $this->employee->bank_name = $_POST['bank_name'] ?? null;
                $this->employee->bank_account_number = $_POST['bank_account_number'] ?? null;
                $this->employee->tax_id = $_POST['tax_id'] ?? null;
                $this->employee->provident_fund_rate_employee = $_POST['provident_fund_rate_employee'] ?? 0.00;
                $this->employee->provident_fund_rate_company = $_POST['provident_fund_rate_company'] ?? 0.00;

                if ($this->employee->create()) {
                    $_SESSION['success_message'] = "เพิ่มพนักงานใหม่สำเร็จ!";
                    header('Location: ' . BASE_URL . '/employee');
                } else {
                    // หากบันทึกไม่สำเร็จ ลบรูปที่อัปโหลดออก
                    if ($this->employee->profile_image_path && file_exists($this->employee->profile_image_path)) {
                        unlink($this->employee->profile_image_path);
                    }
                    $_SESSION['error_message'] = "ไม่สามารถเพิ่มพนักงานใหม่ได้ โปรดลองอีกครั้ง";
                    header('Location: ' . BASE_URL . '/employee/create');
                }
            } catch (Exception $e) {
                // หากเกิด error ลบรูปที่อัปโหลดออก (ถ้ามี)
                if (isset($this->employee->profile_image_path) && 
                    $this->employee->profile_image_path && 
                    file_exists($this->employee->profile_image_path)) {
                    unlink($this->employee->profile_image_path);
                }
                $_SESSION['error_message'] = $e->getMessage();
                header('Location: ' . BASE_URL . '/employee/create');
            }
            exit();
        }
    }

    public function save($id)
    {
        // Check permissions
        RoleHelper::requirePermission('manage_employees');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->employee->id = $id;

                // Handle profile image upload for update
                try {
                    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                        $this->employee->profile_image_path = $this->handleProfileImageUpload();
                    } else {
                        $this->employee->profile_image_path = $_POST['current_profile_image_path'] ?? null;
                    }
                } catch (Exception $img_e) {
                    throw new Exception("ข้อผิดพลาดในการอัปโหลดรูปภาพ: " . $img_e->getMessage());
                }

                // Assign POST values to employee properties
                if (!empty($_POST['password'])) {
                    $this->employee->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                } else {
                    $this->employee->password = null; // Do not update password if empty
                }
                $this->employee->employee_code = $_POST['employee_code'];
                $this->employee->prefix = $_POST['prefix'];
                $this->employee->first_name_th = $_POST['first_name_th'];
                $this->employee->last_name_th = $_POST['last_name_th'];
                $this->employee->first_name_en = $_POST['first_name_en'] ?? null;
                $this->employee->last_name_en = $_POST['last_name_en'] ?? null;
                $this->employee->gender = $_POST['gender'] ?? null;
                $this->employee->birth_date = $_POST['birth_date'] ?? null;
                $this->employee->nationality = $_POST['nationality'] ?? null;
                $this->employee->email = $_POST['email'];
                $this->employee->phone_number = $_POST['phone_number'] ?? null;
                $this->employee->work_phone = $_POST['work_phone'] ?? null;
                $this->employee->national_id = $_POST['national_id'] ?? null;
                $this->employee->address_line1 = $_POST['address_line1'] ?? null;
                $this->employee->district = $_POST['district'] ?? null;
                $this->employee->province = $_POST['province'] ?? null;
                $this->employee->postal_code = $_POST['postal_code'] ?? null;
                $this->employee->start_date = $_POST['start_date'];
                $this->employee->probation_days = $_POST['probation_days'] ?? 0;
                $this->employee->status = $_POST['status'];
                $this->employee->position_id = $_POST['position_id'];
                $this->employee->department_id = $_POST['department_id'];
                $this->employee->supervisor_id = $_POST['supervisor_id'] ?? null;
                $this->employee->role_id = $_POST['role_id'];
                $this->employee->salary = $_POST['salary'] ?? 0.00;
                $this->employee->bank_name = $_POST['bank_name'] ?? null;
                $this->employee->bank_account_number = $_POST['bank_account_number'] ?? null;
                $this->employee->tax_id = $_POST['tax_id'] ?? null;
                $this->employee->provident_fund_rate_employee = $_POST['provident_fund_rate_employee'] ?? 0.00;
                $this->employee->provident_fund_rate_company = $_POST['provident_fund_rate_company'] ?? 0.00;

                if ($this->employee->update()) {
                    $_SESSION['success_message'] = "อัปเดตข้อมูลพนักงานสำเร็จ!";
                } else {
                    $_SESSION['error_message'] = "ไม่สามารถอัปเดตข้อมูลพนักงานได้ โปรดลองอีกครั้ง";
                }
            } catch (Exception $e) {
                $_SESSION['error_message'] = $e->getMessage();
            }

            header('Location: ' . BASE_URL . '/employee/edit/' . $id);
            exit();
        }
    }

    public function destroy($id)
    {
        // Check permissions
        RoleHelper::requirePermission('manage_employees');
        
        $this->employee->id = $id;
        if ($this->employee->delete()) {
            $_SESSION['success_message'] = "ลบข้อมูลพนักงานสำเร็จ!";
        } else {
            $_SESSION['error_message'] = "ไม่สามารถลบข้อมูลพนักงานได้";
=======
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
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
        }
        header('Location: ' . BASE_URL . '/employee');
        exit();
    }
<<<<<<< HEAD

    private function handleProfileImageUpload()
    {
        // ตรวจสอบว่ามีการส่งไฟล์มาหรือไม่
        if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            // หากไม่มีไฟล์หรือมี error ให้ return null
            if (isset($_FILES['profile_image']['error'])) {
                switch ($_FILES['profile_image']['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                        throw new Exception("ไฟล์ใหญ่เกินกว่าที่กำหนดในระบบ");
                    case UPLOAD_ERR_FORM_SIZE:
                        throw new Exception("ไฟล์ใหญ่เกินกว่าที่กำหนดในฟอร์ม");
                    case UPLOAD_ERR_PARTIAL:
                        throw new Exception("ไฟล์อัปโหลดไม่สมบูรณ์");
                    case UPLOAD_ERR_NO_FILE:
                        return null; // ไม่มีไฟล์ - ไม่ถือเป็น error
                    case UPLOAD_ERR_NO_TMP_DIR:
                        throw new Exception("ไม่พบ temporary directory");
                    case UPLOAD_ERR_CANT_WRITE:
                        throw new Exception("ไม่สามารถเขียนไฟล์ลงดิสก์ได้");
                    case UPLOAD_ERR_EXTENSION:
                        throw new Exception("การอัปโหลดถูกหยุดโดย extension");
                    default:
                        throw new Exception("เกิดข้อผิดพลาดในการอัปโหลดไฟล์");
                }
            }
            return null;
        }

        $file = $_FILES['profile_image'];
        
        // ตรวจสอบประเภทไฟล์
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($file['tmp_name']);
        
        if (!in_array($file_type, $allowed_types)) {
            throw new Exception("ประเภทไฟล์ไม่ถูกต้อง กรุณาเลือกไฟล์ JPG, PNG หรือ GIF เท่านั้น");
        }
        
        // ตรวจสอบขนาดไฟล์ (5MB)
        $max_size = 5 * 1024 * 1024; // 5MB in bytes
        if ($file['size'] > $max_size) {
            throw new Exception("ไฟล์ใหญ่เกินไป กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน 5MB");
        }
        
        // สร้างโฟลเดอร์หากยังไม่มี
        $target_dir = "uploads/profiles/";
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0755, true)) {
                throw new Exception("ไม่สามารถสร้างโฟลเดอร์สำหรับเก็บไฟล์ได้");
            }
        }
        
        // ตรวจสอบสิทธิ์การเขียนของโฟลเดอร์
        if (!is_writable($target_dir)) {
            throw new Exception("ไม่มีสิทธิ์เขียนไฟล์ในโฟลเดอร์ uploads/profiles/");
        }
        
        // สร้างชื่อไฟล์ใหม่เพื่อป้องกันชื่อซ้ำ
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // ตรวจสอบ extension อีกครั้ง
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_extension, $allowed_extensions)) {
            throw new Exception("นามสกุลไฟล์ไม่ถูกต้อง กรุณาเลือกไฟล์ .jpg, .jpeg, .png หรือ .gif เท่านั้น");
        }
        
        $new_file_name = uniqid('profile_' . date('Ymd_His') . '_') . '.' . $file_extension;
        $target_file = $target_dir . $new_file_name;
        
        // ลบไฟล์เก่าหากมี (เฉพาะในการอัปเดต)
        if (isset($_POST['current_profile_image_path']) && 
            !empty($_POST['current_profile_image_path']) && 
            file_exists($_POST['current_profile_image_path']) &&
            $_POST['current_profile_image_path'] !== 'assets/images/default-profile.png') {
            
            if (!unlink($_POST['current_profile_image_path'])) {
                // แค่ log warning แต่ไม่ throw exception
                error_log("Warning: ไม่สามารถลบไฟล์เก่าได้: " . $_POST['current_profile_image_path']);
            }
        }
        
        // ย้ายไฟล์ไปยังโฟลเดอร์ปลายทาง
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // ตั้งค่า permission ของไฟล์
            chmod($target_file, 0644);
            return $target_file;
        } else {
            throw new Exception("ไม่สามารถบันทึกไฟล์ได้ กรุณาตรวจสอบสิทธิ์การเขียนของโฟลเดอร์");
        }
    }

    public function import()
    {
        // Check permissions
        RoleHelper::requirePermission('manage_employees');
        
        $page_title = "นำเข้าข้อมูลพนักงานจาก Excel";
        require_once 'views/employee/import.php';
    }

    public function upload()
    {
        // Check permissions
        RoleHelper::requirePermission('manage_employees');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file']['tmp_name'])) {
            $inputFileName = $_FILES['excel_file']['tmp_name'];

            try {
                $spreadsheet = IOFactory::load($inputFileName);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                // Assuming the first row is headers
                $headers = array_map('trim', $sheetData[1]);
                $data_rows = array_slice($sheetData, 1); // Get data rows starting from the second row

                $imported_count = 0;
                $updated_count = 0;
                $failed_count = 0;
                $error_log = [];

                foreach ($data_rows as $row_number => $row) {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $employee_data = [];
                    // Map Excel columns to database fields
                    $column_mapping = [
                        'รหัสพนักงาน' => 'employee_code',
                        'รหัสผ่าน' => 'password',
                        'คำนำหน้า' => 'prefix',
                        'ชื่อ (ไทย)' => 'first_name_th',
                        'นามสกุล (ไทย)' => 'last_name_th',
                        'ชื่อ (อังกฤษ)' => 'first_name_en',
                        'นามสกุล (อังกฤษ)' => 'last_name_en',
                        'เพศ' => 'gender',
                        'วันเกิด' => 'birth_date',
                        'สัญชาติ' => 'nationality',
                        'อีเมล' => 'email',
                        'เบอร์โทรศัพท์' => 'phone_number',
                        'เบอร์โทรศัพท์ที่ทำงาน' => 'work_phone',
                        'เลขบัตรประชาชน' => 'national_id',
                        'ที่อยู่' => 'address_line1',
                        'อำเภอ/เขต' => 'district',
                        'จังหวัด' => 'province',
                        'รหัสไปรษณีย์' => 'postal_code',
                        'วันที่เริ่มงาน' => 'start_date',
                        'วันทดลองงาน' => 'probation_days',
                        'สถานะ' => 'status',
                        'ID ตำแหน่ง' => 'position_id',
                        'ID แผนก' => 'department_id',
                        'ID หัวหน้างาน' => 'supervisor_id',
                        'ID บทบาท' => 'role_id',
                        'เงินเดือน' => 'salary',
                        'ธนาคาร' => 'bank_name',
                        'เลขบัญชีธนาคาร' => 'bank_account_number',
                        'เลขประจำตัวผู้เสียภาษี' => 'tax_id',
                        'อัตรากองทุนสำรองเลี้ยงชีพ (พนักงาน)' => 'provident_fund_rate_employee',
                        'อัตรากองทุนสำรองเลี้ยงชีพ (บริษัท)' => 'provident_fund_rate_company',
                    ];

                    foreach ($column_mapping as $excel_header => $prop_name) {
                        $col_idx = array_search($excel_header, $headers);
                        if ($col_idx !== false && isset($row[$col_idx])) {
                            $employee_data[$prop_name] = trim($row[$col_idx]);
                        } else {
                            $employee_data[$prop_name] = null;
                        }
                    }

                    // Handle specific data types and hashing
                    if (isset($employee_data['password']) && !empty($employee_data['password'])) {
                        $employee_data['password'] = password_hash($employee_data['password'], PASSWORD_DEFAULT);
                    } else {
                        $employee_data['password'] = null;
                    }

                    // Convert Excel dates (numeric) to YYYY-MM-DD
                    if (isset($employee_data['birth_date']) && is_numeric($employee_data['birth_date'])) {
                        $employee_data['birth_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($employee_data['birth_date'])->format('Y-m-d');
                    }
                    if (isset($employee_data['start_date']) && is_numeric($employee_data['start_date'])) {
                        $employee_data['start_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($employee_data['start_date'])->format('Y-m-d');
                    }

                    $this->employee->employee_code = $employee_data['employee_code'];
                    $existing_employee = $this->employee->readOneByEmployeeCode();

                    // Populate employee object
                    foreach ($employee_data as $key => $value) {
                        if (property_exists($this->employee, $key)) {
                            $this->employee->$key = $value;
                        }
                    }

                    // Check if employee_code already exists to decide between create or update
                    if ($existing_employee && $existing_employee->id) {
                        $this->employee->id = $existing_employee->id;
                        try {
                            if ($this->employee->update()) {
                                $updated_count++;
                            } else {
                                $failed_count++;
                                $error_log[] = "Row $row_number: Failed to update employee with code {$employee_data['employee_code']}";
                            }
                        } catch (Exception $e) {
                            $failed_count++;
                            $error_log[] = "Row $row_number: Error updating employee with code {$employee_data['employee_code']}: " . $e->getMessage();
                        }
                    } else {
                        try {
                            if ($this->employee->create()) {
                                $imported_count++;
                            } else {
                                $failed_count++;
                                $error_log[] = "Row $row_number: Failed to create employee with code {$employee_data['employee_code']}";
                            }
                        } catch (Exception $e) {
                            $failed_count++;
                            $error_log[] = "Row $row_number: Error creating employee with code {$employee_data['employee_code']}: " . $e->getMessage();
                        }
                    }
                }

                $message = "นำเข้าข้อมูลสำเร็จ: {$imported_count} รายการ, อัปเดต: {$updated_count} รายการ, ข้อผิดพลาด: {$failed_count} รายการ";
                if (!empty($error_log)) {
                    $message .= "<br>ข้อผิดพลาด:<ul>" . implode('', array_map(fn($err) => "<li>$err</li>", $error_log)) . "</ul>";
                }
                $_SESSION['import_status'] = $message;
            } catch (Exception $e) {
                $_SESSION['import_status'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            }
=======
    
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

>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
            header('Location: ' . BASE_URL . '/employee/import');
            exit();
        }
    }
<<<<<<< HEAD

    public function previous_income($employee_id)
    {
        // Check if user can access this employee data
        if (!RoleHelper::canAccessEmployeeData($employee_id)) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงข้อมูลพนักงานนี้";
            header('Location: ' . BASE_URL . '/employee');
            exit();
        }

        // Handle POST request for saving previous income
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_previous_income') {
            // Check permissions for editing
            if (!RoleHelper::canManageEmployees() && !RoleHelper::canAccessEmployeeData($employee_id)) {
                $_SESSION['error_message'] = "คุณไม่มีสิทธิ์แก้ไขข้อมูลนี้";
                header('Location: ' . BASE_URL . '/employee/previous_income/' . $employee_id);
                exit();
            }

            $year = $_POST['tax_year'];
            $previous_income_model = new PreviousIncome($this->db);

            if ($previous_income_model->save($employee_id, $year, $_POST['total_income'], $_POST['total_tax'], $_POST['social_security'], $_POST['provident_fund'])) {
                $_SESSION['success_message'] = "บันทึกข้อมูลรายได้สะสมสำเร็จ";
            } else {
                $_SESSION['error_message'] = "ไม่สามารถบันทึกข้อมูลรายได้สะสมได้";
            }
            header('Location: ' . BASE_URL . '/employee/previous_income/' . $employee_id);
            exit();
        }

        // Handle GET request for displaying the form
        $page_title = "บันทึกรายได้สะสม (ที่ทำงานเก่า)";
        $this->employee->id = $employee_id;
        $this->employee->readOne();
        $employee = $this->employee;

        if (!$employee->id) {
            $_SESSION['error_message'] = "ไม่พบข้อมูลพนักงานที่ต้องการ";
            header('Location: ' . BASE_URL . '/employee');
            exit();
        }

        $previous_income_model = new PreviousIncome($this->db);
        $current_year = date('Y');
        $previous_income = $previous_income_model->findByEmployeeAndYear($employee_id, $current_year);

        // Get user permissions
        $permissions = RoleHelper::getUserPermissions();

        require_once 'views/employee/previous_income.php';
    }

    public function save_previous_income()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $employee_id = $_POST['employee_id'];
            
            // Check permissions
            if (!RoleHelper::canManageEmployees() && !RoleHelper::canAccessEmployeeData($employee_id)) {
                $_SESSION['error_message'] = "คุณไม่มีสิทธิ์แก้ไขข้อมูลนี้";
                header('Location: ' . BASE_URL . '/employee/previous_income/' . $employee_id);
                exit();
            }

            $year = $_POST['tax_year'];
            $previous_income_model = new PreviousIncome($this->db);
            if ($previous_income_model->save($employee_id, $year, $_POST['total_income'], $_POST['total_tax'], $_POST['social_security'], $_POST['provident_fund'])) {
                $_SESSION['success_message'] = "บันทึกข้อมูลรายได้สะสมสำเร็จ";
            } else {
                $_SESSION['error_message'] = "ไม่สามารถบันทึกข้อมูลรายได้สะสมได้";
            }
            header('Location: ' . BASE_URL . '/employee/previous_income/' . $employee_id);
            exit();
        }
    }

    /**
     * Export employee data to CSV
     */
    public function export()
    {
        // Check permissions
        RoleHelper::requirePermission('export_data');
        
        try {
            // Get all employees
            $stmt = $this->employee->read();
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Generate filename
            $filename = 'employees_export_' . date('Y-m-d_H-i-s') . '.csv';
            
            // Set headers for CSV download
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Add BOM for UTF-8
            echo "\xEF\xBB\xBF";
            
            // Output CSV
            $output = fopen('php://output', 'w');
            
            // Header row
            fputcsv($output, [
                'รหัสพนักงาน',
                'ชื่อ-นามสกุล',
                'แผนก',
                'ตำแหน่ง',
                'อีเมล',
                'เบอร์โทรศัพท์',
                'วันที่เริ่มงาน',
                'สถานะ',
                'เงินเดือน'
            ]);
            
            // Data rows
            foreach ($employees as $employee) {
                fputcsv($output, [
                    $employee['employee_code'],
                    $employee['first_name_th'] . ' ' . $employee['last_name_th'],
                    $employee['department_name'] ?? '-',
                    $employee['position_name'] ?? '-',
                    $employee['email'],
                    $employee['phone_number'] ?? '-',
                    $employee['start_date'],
                    $employee['status'],
                    number_format($employee['salary'], 2)
                ]);
            }
            
            fclose($output);
            exit();
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการส่งออกข้อมูล: " . $e->getMessage();
            header('Location: ' . BASE_URL . '/employee');
            exit();
        }
    }

    /**
     * API endpoint for getting employee data (AJAX)
     */
    public function apiData()
    {
        // Check permissions
        if (!RoleHelper::canViewAllAttendance()) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit();
        }

        try {
            $stmt = $this->employee->read();
            $employees = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $employees[] = [
                    'id' => $row['id'],
                    'employee_code' => $row['employee_code'],
                    'full_name' => $row['first_name_th'] . ' ' . $row['last_name_th'],
                    'department' => $row['department_name'],
                    'position' => $row['position_name'],
                    'email' => $row['email'],
                    'status' => $row['status']
                ];
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $employees,
                'total' => count($employees)
            ]);
            exit();

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit();
        }
    }
}
?>
=======
}
?>
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
