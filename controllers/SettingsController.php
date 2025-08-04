<?php
// controllers/SettingsController.php - Enhanced with Role-Based Access Control

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Setting.php';
require_once __DIR__ . '/../models/WorkShift.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/RoleHelper.php';

class SettingsController {

    private $db;
    private $setting;
    private $workShift;
    private $permissions;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) { 
            session_start(); 
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        
        $database = new Database();
        $this->db = $database->getConnection();
        $this->setting = new Setting($this->db);
        $this->workShift = new WorkShift($this->db);
        $this->permissions = RoleHelper::getUserPermissions();
        
        // Check minimum permission (Admin, HR, or Manager can access settings)
        if (!$this->permissions['can_view_reports']) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
    }

    /**
     * Display the settings page based on user role
     */
    public function index() {
        $page_title = "ตั้งค่าระบบ";
        
        // Get settings based on role permissions
        $settings = $this->getSettingsByRole();
        $work_shifts = $this->workShift->getAllShifts();
        $employees = $this->getEmployeesForShiftAssignment();
        
        require_once 'views/settings/index.php';
    }

    /**
     * Get settings categorized by user role permissions
     */
    private function getSettingsByRole() {
        $all_settings = $this->setting->getAllSettings();
        $categories = [];
        
        // Organization Settings - Admin and HR only
        if ($this->permissions['can_manage_employees']) {
            $categories['organization'] = [
                'title' => 'ข้อมูลองค์กร',
                'icon' => 'fas fa-building',
                'description' => 'จัดการข้อมูลพื้นฐานขององค์กร เช่น ชื่อ ที่อยู่ โลโก้ และข้อมูลติดต่อ',
                'settings' => $this->filterSettings($all_settings, [
                    'org_name', 'org_address', 'org_logo', 'org_phone', 
                    'org_email', 'org_website', 'org_tax_id'
                ])
            ];
        }

        // Work Time Settings - Admin, HR, and Manager
        if ($this->permissions['can_view_reports']) {
            $categories['work_time'] = [
                'title' => 'การตั้งค่าเวลาทำงาน',
                'icon' => 'fas fa-clock',
                'description' => 'กำหนดเวลาทำงาน เวลาเลิกงาน และการตั้งค่าที่เกี่ยวข้องกับเวลาทำงาน',
                'settings' => $this->filterSettings($all_settings, [
                    'work_start_time', 'work_end_time', 'grace_period_minutes', 
                    'ot_start_time', 'break_start_time', 'break_end_time',
                    'lunch_break_duration', 'work_days_per_week'
                ])
            ];
        }

        // Work Shifts - Admin, HR, and Manager
        if ($this->permissions['can_view_reports']) {
            $categories['work_shifts'] = [
                'title' => 'การจัดการกะการทำงาน',
                'icon' => 'fas fa-user-clock',
                'description' => 'จัดการกะการทำงาน มอบหมายพนักงาน และกำหนดตารางงาน',
                'settings' => [],
                'has_custom_content' => true
            ];
        }

        // Theme Settings - Admin and HR only  
        if ($this->permissions['can_manage_employees']) {
            $categories['theme'] = [
                'title' => 'ธีมและสีสัน',
                'icon' => 'fas fa-palette',
                'description' => 'ปรับแต่งสีสันและธีมของระบบ รวมถึงไอคอนและรูปลักษณ์',
                'settings' => $this->filterSettings($all_settings, [
                    'favicon', 'primary_color', 'secondary_color', 'accent_color', 
                    'sidebar_bg_color', 'header_bg_color', 'login_bg_image'
                ])
            ];
        }

        // System Settings - Admin only
        if ($this->permissions['can_manage_settings']) {
            $categories['system'] = [
                'title' => 'การตั้งค่าระบบ',
                'icon' => 'fas fa-cogs',
                'description' => 'การตั้งค่าระบบพื้นฐาน เช่น เขตเวลา รูปแบบวันที่ และภาษา',
                'settings' => $this->filterSettings($all_settings, [
                    'system_timezone', 'date_format', 'time_format', 'language', 
                    'currency', 'backup_frequency', 'auto_logout_time'
                ])
            ];
        }

        // Notification Settings - Admin, HR, and Manager
        if ($this->permissions['can_view_reports']) {
            $categories['notifications'] = [
                'title' => 'การแจ้งเตือน',
                'icon' => 'fas fa-bell',
                'description' => 'จัดการการแจ้งเตือนต่างๆ ของระบบ',
                'settings' => $this->filterSettings($all_settings, [
                    'enable_email_notifications', 'enable_sms_notifications', 
                    'notification_sound', 'late_arrival_notification',
                    'leave_request_notification', 'overtime_notification'
                ])
            ];
        }

        // Security Settings - Admin only
        if ($this->permissions['can_manage_settings']) {
            $categories['security'] = [
                'title' => 'ความปลอดภัย',
                'icon' => 'fas fa-shield-alt',
                'description' => 'การตั้งค่าความปลอดภัยและการรักษาความปลอดภัยของระบบ',
                'settings' => $this->filterSettings($all_settings, [
                    'session_timeout', 'max_login_attempts', 'password_min_length',
                    'password_require_special', 'two_factor_auth', 'ip_whitelist'
                ])
            ];
        }

        return $categories;
    }

    /**
     * Filter settings array by keys
     */
    private function filterSettings($all_settings, $keys) {
        $filtered = [];
        foreach ($keys as $key) {
            if (isset($all_settings[$key])) {
                $filtered[$key] = $all_settings[$key];
            } else {
                // Create default setting if not exists
                $filtered[$key] = [
                    'value' => $this->getDefaultValue($key),
                    'description' => $this->getSettingDescription($key),
                    'type' => $this->getSettingType($key)
                ];
            }
        }
        return $filtered;
    }

    /**
     * Get default values for settings
     */
    private function getDefaultValue($key) {
        $defaults = [
            'org_name' => 'อ.บ.ต.หนองปากโลง',
            'org_address' => '',
            'org_phone' => '',
            'org_email' => '',
            'org_website' => '',
            'org_tax_id' => '',
            'work_start_time' => '08:30',
            'work_end_time' => '17:30',
            'grace_period_minutes' => '15',
            'ot_start_time' => '18:00',
            'break_start_time' => '12:00',
            'break_end_time' => '13:00',
            'lunch_break_duration' => '60',
            'work_days_per_week' => '5',
            'primary_color' => '#4f46e5',
            'secondary_color' => '#7c3aed',
            'accent_color' => '#06b6d4',
            'sidebar_bg_color' => '#1f2937',
            'header_bg_color' => '#ffffff',
            'system_timezone' => 'Asia/Bangkok',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'language' => 'th',
            'currency' => 'THB',
            'backup_frequency' => 'daily',
            'auto_logout_time' => '30',
            'enable_email_notifications' => '1',
            'enable_sms_notifications' => '0',
            'notification_sound' => '1',
            'late_arrival_notification' => '1',
            'leave_request_notification' => '1',
            'overtime_notification' => '1',
            'session_timeout' => '1800',
            'max_login_attempts' => '5',
            'password_min_length' => '8',
            'password_require_special' => '1',
            'two_factor_auth' => '0',
            'ip_whitelist' => ''
        ];
        
        return $defaults[$key] ?? '';
    }

    /**
     * Get setting descriptions
     */
    private function getSettingDescription($key) {
        $descriptions = [
            'org_name' => 'ชื่อองค์กร',
            'org_address' => 'ที่อยู่องค์กร',
            'org_phone' => 'เบอร์โทรศัพท์',
            'org_email' => 'อีเมลองค์กร',
            'org_website' => 'เว็บไซต์องค์กร',
            'org_tax_id' => 'เลขประจำตัวผู้เสียภาษี',
            'work_start_time' => 'เวลาเริ่มงาน',
            'work_end_time' => 'เวลาเลิกงาน',
            'grace_period_minutes' => 'เวลาผ่อนผัน (นาที)',
            'ot_start_time' => 'เวลาเริ่ม OT',
            'break_start_time' => 'เวลาเริ่มพัก',
            'break_end_time' => 'เวลาสิ้นสุดพัก',
            'lunch_break_duration' => 'ระยะเวลาพักกลางวัน (นาที)',
            'work_days_per_week' => 'วันทำงานต่อสัปดาห์',
            'primary_color' => 'สีหลัก',
            'secondary_color' => 'สีรอง',
            'accent_color' => 'สีเน้น',
            'sidebar_bg_color' => 'สีพื้นหลัง Sidebar',
            'header_bg_color' => 'สีพื้นหลัง Header',
            'login_bg_image' => 'รูปพื้นหลังหน้า Login',
            'system_timezone' => 'เขตเวลา',
            'date_format' => 'รูปแบบวันที่',
            'time_format' => 'รูปแบบเวลา',
            'language' => 'ภาษา',
            'currency' => 'สกุลเงิน',
            'backup_frequency' => 'ความถี่การสำรองข้อมูล',
            'auto_logout_time' => 'เวลาออกจากระบบอัตโนมัติ (นาที)',
            'enable_email_notifications' => 'เปิดการแจ้งเตือนทางอีเมล',
            'enable_sms_notifications' => 'เปิดการแจ้งเตือนทาง SMS',
            'notification_sound' => 'เสียงแจ้งเตือน',
            'late_arrival_notification' => 'แจ้งเตือนการมาสาย',
            'leave_request_notification' => 'แจ้งเตือนคำขอลา',
            'overtime_notification' => 'แจ้งเตือนการทำ OT',
            'session_timeout' => 'หมดเวลา Session (วินาที)',
            'max_login_attempts' => 'จำนวนครั้งที่พยายาม Login สูงสุด',
            'password_min_length' => 'ความยาวรหัสผ่านขั้นต่ำ',
            'password_require_special' => 'ต้องมีอักขระพิเศษในรหัสผ่าน',
            'two_factor_auth' => 'การยืนยันตัวตนสองชั้น',
            'ip_whitelist' => 'รายการ IP ที่อนุญาต'
        ];
        
        return $descriptions[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    /**
     * Get setting input types
     */
    private function getSettingType($key) {
        $types = [
            'org_name' => 'text',
            'org_address' => 'textarea',
            'org_phone' => 'tel',
            'org_email' => 'email',
            'org_website' => 'url',
            'org_tax_id' => 'text',
            'work_start_time' => 'time',
            'work_end_time' => 'time',
            'grace_period_minutes' => 'number',
            'ot_start_time' => 'time',
            'break_start_time' => 'time',
            'break_end_time' => 'time',
            'lunch_break_duration' => 'number',
            'work_days_per_week' => 'number',
            'primary_color' => 'color',
            'secondary_color' => 'color',
            'accent_color' => 'color',
            'sidebar_bg_color' => 'color',
            'header_bg_color' => 'color',
            'login_bg_image' => 'file',
            'org_logo' => 'file',
            'favicon' => 'file',
            'system_timezone' => 'select',
            'date_format' => 'select',
            'time_format' => 'select',
            'language' => 'select',
            'currency' => 'select',
            'backup_frequency' => 'select',
            'auto_logout_time' => 'number',
            'enable_email_notifications' => 'boolean',
            'enable_sms_notifications' => 'boolean',
            'notification_sound' => 'boolean',
            'late_arrival_notification' => 'boolean',
            'leave_request_notification' => 'boolean',
            'overtime_notification' => 'boolean',
            'session_timeout' => 'number',
            'max_login_attempts' => 'number',
            'password_min_length' => 'number',
            'password_require_special' => 'boolean',
            'two_factor_auth' => 'boolean',
            'ip_whitelist' => 'textarea'
        ];
        
        return $types[$key] ?? 'text';
    }

    /**
     * Get employees for shift assignment
     */
    private function getEmployeesForShiftAssignment() {
        if (!$this->permissions['can_view_reports']) {
            return [];
        }
        
        try {
            $query = "SELECT e.id, e.emp_code, e.first_name, e.last_name, 
                            d.name_th as department_name, p.name_th as position_name
                     FROM employees e 
                     LEFT JOIN departments d ON e.department_id = d.id
                     LEFT JOIN positions p ON e.position_id = p.id
                     WHERE e.status = 'ทำงาน'
                     ORDER BY e.first_name, e.last_name";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting employees: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update settings from the form submission
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settings_to_save = [];
            $upload_errors = [];

            // Get all POST data and filter by permissions
            foreach ($_POST as $key => $value) {
                if ($this->canUpdateSetting($key)) {
                    $settings_to_save[$key] = $value;
                }
            }

            // Handle boolean settings
            $boolean_settings = [
                'enable_email_notifications', 'enable_sms_notifications', 
                'notification_sound', 'late_arrival_notification',
                'leave_request_notification', 'overtime_notification',
                'password_require_special', 'two_factor_auth'
            ];

            foreach ($boolean_settings as $setting_key) {
                if ($this->canUpdateSetting($setting_key)) {
                    $settings_to_save[$setting_key] = isset($_POST[$setting_key]) ? '1' : '0';
                }
            }

            // Handle file uploads
            $file_settings = ['org_logo', 'favicon', 'login_bg_image'];
            foreach ($file_settings as $file_key) {
                if ($this->canUpdateSetting($file_key) && 
                    isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == 0) {
                    $upload_result = $this->handleFileUpload($_FILES[$file_key], $file_key);
                    if ($upload_result['success']) {
                        $settings_to_save[$file_key] = $upload_result['file_path'];
                    } else {
                        $upload_errors[] = $upload_result['error'];
                    }
                }
            }

            // Save settings
            if ($this->setting->saveSettings($settings_to_save)) {
                if (empty($upload_errors)) {
                    $_SESSION['success_message'] = "บันทึกการตั้งค่าสำเร็จ";
                } else {
                    $_SESSION['warning_message'] = "บันทึกการตั้งค่าสำเร็จ แต่มีปัญหาในการอัปโหลดไฟล์: " . implode(', ', $upload_errors);
                }
            } else {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการบันทึกการตั้งค่า";
            }
            
            header('Location: ' . BASE_URL . '/settings');
            exit();
        }
    }

    /**
     * Check if current user can update specific setting
     */
    private function canUpdateSetting($setting_key) {
        // Admin can update everything
        if ($this->permissions['can_manage_settings']) {
            return true;
        }

        // HR can update most settings except system and security
        if ($this->permissions['can_manage_employees']) {
            $restricted_for_hr = [
                'system_timezone', 'backup_frequency', 'auto_logout_time',
                'session_timeout', 'max_login_attempts', 'password_min_length',
                'password_require_special', 'two_factor_auth', 'ip_whitelist'
            ];
            return !in_array($setting_key, $restricted_for_hr);
        }

        // Manager can update work time and notification settings only
        if ($this->permissions['can_view_reports']) {
            $allowed_for_manager = [
                'work_start_time', 'work_end_time', 'grace_period_minutes',
                'ot_start_time', 'break_start_time', 'break_end_time',
                'lunch_break_duration', 'enable_email_notifications',
                'notification_sound', 'late_arrival_notification',
                'leave_request_notification', 'overtime_notification'
            ];
            return in_array($setting_key, $allowed_for_manager);
        }

        return false;
    }

    /**
     * Work Shift Management Methods
     */
    public function createShift() {
        if (!$this->permissions['can_view_reports']) {
            header('HTTP/1.1 403 Forbidden');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'shift_name' => $_POST['shift_name'],
                'start_time' => $_POST['start_time'],
                'end_time' => $_POST['end_time'],
                'break_start' => $_POST['break_start'],
                'break_end' => $_POST['break_end'],
                'work_days' => implode(',', $_POST['work_days'] ?? []),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if ($this->workShift->createShift($data)) {
                $_SESSION['success_message'] = "สร้างกะการทำงานสำเร็จ";
            } else {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการสร้างกะการทำงาน";
            }
        }

        header('Location: ' . BASE_URL . '/settings#work_shifts');
        exit();
    }

    public function updateShift() {
        if (!$this->permissions['can_view_reports']) {
            header('HTTP/1.1 403 Forbidden');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $shift_id = $_POST['shift_id'];
            $data = [
                'shift_name' => $_POST['shift_name'],
                'start_time' => $_POST['start_time'],
                'end_time' => $_POST['end_time'],
                'break_start' => $_POST['break_start'],
                'break_end' => $_POST['break_end'],
                'work_days' => implode(',', $_POST['work_days'] ?? []),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if ($this->workShift->updateShift($shift_id, $data)) {
                $_SESSION['success_message'] = "อัปเดตกะการทำงานสำเร็จ";
            } else {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการอัปเดตกะการทำงาน";
            }
        }

        header('Location: ' . BASE_URL . '/settings#work_shifts');
        exit();
    }

    public function deleteShift() {
        if (!$this->permissions['can_manage_employees']) {
            header('HTTP/1.1 403 Forbidden');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $shift_id = $_POST['shift_id'];

            if ($this->workShift->deleteShift($shift_id)) {
                $_SESSION['success_message'] = "ลบกะการทำงานสำเร็จ";
            } else {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบกะการทำงาน";
            }
        }

        header('Location: ' . BASE_URL . '/settings#work_shifts');
        exit();
    }

    public function assignShift() {
        if (!$this->permissions['can_view_reports']) {
            header('HTTP/1.1 403 Forbidden');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $employee_ids = $_POST['employee_ids'] ?? [];
            $shift_id = $_POST['shift_id'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'] ?? null;

            $success_count = 0;
            foreach ($employee_ids as $employee_id) {
                if ($this->workShift->assignEmployeeToShift($employee_id, $shift_id, $start_date, $end_date)) {
                    $success_count++;
                }
            }

            if ($success_count > 0) {
                $_SESSION['success_message'] = "มอบหมายกะการทำงานสำเร็จ {$success_count} คน";
            } else {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการมอบหมายกะการทำงาน";
            }
        }

        header('Location: ' . BASE_URL . '/settings#work_shifts');
        exit();
    }

    /**
     * Handle file upload with role-based permissions
     */
    private function handleFileUpload($file, $type) {
        // Check if user can upload this type of file
        if (!$this->canUpdateSetting($type)) {
            return [
                'success' => false,
                'error' => "คุณไม่มีสิทธิ์อัปโหลดไฟล์ประเภทนี้"
            ];
        }

        $allowed_types = [
            'org_logo' => ['jpg', 'jpeg', 'png', 'gif'],
            'favicon' => ['ico', 'png', 'jpg', 'jpeg'],
            'login_bg_image' => ['jpg', 'jpeg', 'png']
        ];

        $max_size = [
            'org_logo' => 2 * 1024 * 1024, // 2MB
            'favicon' => 1 * 1024 * 1024,   // 1MB
            'login_bg_image' => 5 * 1024 * 1024 // 5MB
        ];

        // Check file size
        if ($file['size'] > $max_size[$type]) {
            return [
                'success' => false,
                'error' => "ไฟล์ {$type} มีขนาดใหญ่เกินไป (สูงสุด " . ($max_size[$type] / 1024 / 1024) . "MB)"
            ];
        }

        // Check file type
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_types[$type])) {
            return [
                'success' => false,
                'error' => "ประเภทไฟล์ {$type} ไม่ถูกต้อง (อนุญาต: " . implode(', ', $allowed_types[$type]) . ")"
            ];
        }

        // Create directory if not exists
        $target_dir = "uploads/{$type}/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Generate unique filename
        $file_name = $type . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $file_name;

        // Upload file
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return [
                'success' => true,
                'file_path' => $target_file
            ];
        } else {
            return [
                'success' => false,
                'error' => "เกิดข้อผิดพลาดในการอัปโหลดไฟล์ {$type}"
            ];
        }
    }

    /**
     * Reset settings to defaults (Admin only)
     */
    public function reset() {
        RoleHelper::requirePermission('manage_settings');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->setting->resetToDefaults()) {
                $_SESSION['success_message'] = "รีเซ็ตการตั้งค่าเป็นค่าเริ่มต้นสำเร็จ";
            } else {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการรีเซ็ตการตั้งค่า";
            }
            
            header('Location: ' . BASE_URL . '/settings');
            exit();
        }
    }

    /**
     * Export settings as JSON (Admin and HR only)
     */
    public function export() {
        if (!$this->permissions['can_export_data']) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์ส่งออกข้อมูล";
            header('Location: ' . BASE_URL . '/settings');
            exit();
        }

        $settings = $this->setting->getAllSettings();
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="system_settings_' . date('Y-m-d_H-i-s') . '.json"');
        
        echo json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Import settings from JSON (Admin only)
     */
    public function import() {
        RoleHelper::requirePermission('manage_settings');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['settings_file'])) {
            $file = $_FILES['settings_file'];
            
            if ($file['error'] !== 0) {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการอัปโหลดไฟล์";
                header('Location: ' . BASE_URL . '/settings');
                exit();
            }

            $file_content = file_get_contents($file['tmp_name']);
            $settings_data = json_decode($file_content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $_SESSION['error_message'] = "ไฟล์ JSON ไม่ถูกต้อง";
                header('Location: ' . BASE_URL . '/settings');
                exit();
            }

            // Convert data format
            $settings_to_save = [];
            foreach ($settings_data as $key => $data) {
                if (is_array($data) && isset($data['value'])) {
                    $settings_to_save[$key] = $data['value'];
                } else {
                    $settings_to_save[$key] = $data;
                }
            }

            if ($this->setting->saveSettings($settings_to_save)) {
                $_SESSION['success_message'] = "นำเข้าการตั้งค่าสำเร็จ";
            } else {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการนำเข้าการตั้งค่า";
            }
        }
        
        header('Location: ' . BASE_URL . '/settings');
        exit();
    }

    /**
     * Preview theme colors
     */
    public function previewTheme() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $colors = [
                'primary_color' => $_POST['primary_color'] ?? '#4f46e5',
                'secondary_color' => $_POST['secondary_color'] ?? '#7c3aed',
                'accent_color' => $_POST['accent_color'] ?? '#06b6d4',
                'sidebar_bg_color' => $_POST['sidebar_bg_color'] ?? '#1f2937',
                'header_bg_color' => $_POST['header_bg_color'] ?? '#ffffff'
            ];

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'colors' => $colors,
                'css' => $this->generateThemeCSS($colors)
            ]);
            exit();
        }
    }

    /**
     * Generate CSS for theme preview
     */
    private function generateThemeCSS($colors) {
        return "
        :root {
            --primary-color: {$colors['primary_color']};
            --secondary-color: {$colors['secondary_color']};
            --accent-color: {$colors['accent_color']};
            --sidebar-bg-color: {$colors['sidebar_bg_color']};
            --header-bg-color: {$colors['header_bg_color']};
        }
        .btn-gradient { 
            background: linear-gradient(to right, {$colors['primary_color']}, {$colors['secondary_color']}); 
        }
        .sidebar { 
            background-color: {$colors['sidebar_bg_color']}; 
        }
        .header { 
            background-color: {$colors['header_bg_color']}; 
        }
        ";
    }
}
?>