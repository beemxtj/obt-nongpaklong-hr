<?php
// helpers/RoleHelper.php - Complete Role-based Access Control Helper

require_once __DIR__ . '/../config/database.php';

class RoleHelper 
{
    private static $db_instance = null;
    
    /**
     * Get database connection
     * @return PDO
     */
    private static function getDB() 
    {
        if (self::$db_instance === null) {
            $database = new Database();
            self::$db_instance = $database->getConnection();
        }
        return self::$db_instance;
    }
    
    /**
     * Check if current user has admin privileges
     * @return bool
     */
    public static function isAdmin() 
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Check cached role first
        if (isset($_SESSION['role_name'])) {
            return self::checkAdminRole($_SESSION['role_name']);
        }
        
        // Get role from database if not cached
        try {
            $db = self::getDB();
            
            $query = "SELECT r.role_name FROM employees e 
                     JOIN roles r ON e.role_id = r.id 
                     WHERE e.id = :user_id LIMIT 1";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $_SESSION['role_name'] = $result['role_name']; // Cache for future use
                return self::checkAdminRole($result['role_name']);
            }
        } catch (Exception $e) {
            error_log("Error checking user role: " . $e->getMessage());
        }
        
        return false;
    }
    
    /**
     * Check if current user has HR privileges
     * @return bool
     */
    public static function isHR() 
    {
        if (!isset($_SESSION['role_name'])) {
            self::isAdmin(); // This will cache the role
        }
        
        return isset($_SESSION['role_name']) && 
               in_array(strtolower($_SESSION['role_name']), ['hr', 'human resources', 'human resource']);
    }
    
    /**
     * Check if current user has manager privileges
     * @return bool
     */
    public static function isManager() 
    {
        if (!isset($_SESSION['role_name'])) {
            self::isAdmin(); // This will cache the role
        }
        
        return isset($_SESSION['role_name']) && 
               in_array(strtolower($_SESSION['role_name']), ['manager', 'ผู้จัดการ', 'หัวหน้า', 'supervisor']);
    }
    
    /**
     * Check if current user can view all attendance data
     * @return bool
     */
    public static function canViewAllAttendance() 
    {
        return self::isAdmin() || self::isHR() || self::isManager();
    }
    
    /**
     * Check if current user can export data
     * @return bool
     */
    public static function canExportData() 
    {
        return self::isAdmin() || self::isHR();
    }
    
    /**
     * Check if current user can manage employees
     * @return bool
     */
    public static function canManageEmployees() 
    {
        return self::isAdmin() || self::isHR();
    }
    
    /**
     * Check if current user can approve leave requests
     * @return bool
     */
    public static function canApproveLeave() 
    {
        return self::isAdmin() || self::isHR() || self::isManager();
    }
    
    /**
     * Check if current user can view payroll
     * @return bool
     */
    public static function canViewPayroll() 
    {
        return self::isAdmin() || self::isHR();
    }
    
    /**
     * Check if current user can edit attendance records
     * @return bool
     */
    public static function canEditAttendance() 
    {
        return self::isAdmin() || self::isHR();
    }
    
    /**
     * Check if current user can view reports
     * @return bool
     */
    public static function canViewReports() 
    {
        return self::isAdmin() || self::isHR() || self::isManager();
    }
    
    /**
     * Check if current user can manage system settings
     * @return bool
     */
    public static function canManageSettings() 
    {
        return self::isAdmin();
    }
    
    /**
     * Get current user's role name
     * @return string|null
     */
    public static function getCurrentRole() 
    {
        if (!isset($_SESSION['role_name'])) {
            self::isAdmin(); // This will cache the role
        }
        
        return $_SESSION['role_name'] ?? null;
    }
    
    /**
     * Get current user's department ID (if applicable)
     * @return int|null
     */
    public static function getCurrentDepartmentId() 
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        // Check cached department first
        if (isset($_SESSION['department_id'])) {
            return $_SESSION['department_id'];
        }
        
        try {
            $db = self::getDB();
            
            $query = "SELECT department_id FROM employees WHERE id = :user_id LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $_SESSION['department_id'] = $result['department_id']; // Cache for future use
                return $result['department_id'];
            }
        } catch (Exception $e) {
            error_log("Error getting user department: " . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Get current user's employee data
     * @return array|null
     */
    public static function getCurrentEmployee() 
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        // Check cache first
        if (isset($_SESSION['employee_data'])) {
            return $_SESSION['employee_data'];
        }
        
        try {
            $db = self::getDB();
            
            $query = "SELECT e.*, d.name_th as department_name, p.name_th as position_name, r.role_name
                     FROM employees e 
                     LEFT JOIN departments d ON e.department_id = d.id
                     LEFT JOIN positions p ON e.position_id = p.id
                     LEFT JOIN roles r ON e.role_id = r.id
                     WHERE e.id = :user_id LIMIT 1";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $_SESSION['employee_data'] = $result; // Cache for future use
                $_SESSION['role_name'] = $result['role_name'];
                $_SESSION['department_id'] = $result['department_id'];
                return $result;
            }
        } catch (Exception $e) {
            error_log("Error getting employee data: " . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Check if role name indicates admin privileges
     * @param string $role_name
     * @return bool
     */
    private static function checkAdminRole($role_name) 
    {
        $admin_roles = [
            'admin', 
            'administrator', 
            'hr', 
            'human resources', 
            'human resource',
            'manager',
            'ผู้จัดการ',
            'ผู้บริหาร',
            'แอดมิน',
            'ผู้ดูแลระบบ'
        ];
        
        return in_array(strtolower($role_name), $admin_roles);
    }
    
    /**
     * Redirect if user doesn't have required permissions
     * @param string $required_permission
     * @param string $redirect_url
     */
    public static function requirePermission($required_permission, $redirect_url = null) 
    {
        $has_permission = false;
        
        switch ($required_permission) {
            case 'admin':
                $has_permission = self::isAdmin();
                break;
            case 'hr':
                $has_permission = self::isHR();
                break;
            case 'manager':
                $has_permission = self::isManager();
                break;
            case 'view_all_attendance':
                $has_permission = self::canViewAllAttendance();
                break;
            case 'export_data':
                $has_permission = self::canExportData();
                break;
            case 'manage_employees':
                $has_permission = self::canManageEmployees();
                break;
            case 'approve_leave':
                $has_permission = self::canApproveLeave();
                break;
            case 'view_payroll':
                $has_permission = self::canViewPayroll();
                break;
            case 'edit_attendance':
                $has_permission = self::canEditAttendance();
                break;
            case 'view_reports':
                $has_permission = self::canViewReports();
                break;
            case 'manage_settings':
                $has_permission = self::canManageSettings();
                break;
        }
        
        if (!$has_permission) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
            $redirect_url = $redirect_url ?: BASE_URL . '/dashboard';
            header('Location: ' . $redirect_url);
            exit();
        }
    }
    
    /**
     * Get user permissions array
     * @return array
     */
    public static function getUserPermissions() 
    {
        return [
            'is_admin' => self::isAdmin(),
            'is_hr' => self::isHR(),
            'is_manager' => self::isManager(),
            'can_view_all_attendance' => self::canViewAllAttendance(),
            'can_export_data' => self::canExportData(),
            'can_manage_employees' => self::canManageEmployees(),
            'can_approve_leave' => self::canApproveLeave(),
            'can_view_payroll' => self::canViewPayroll(),
            'can_edit_attendance' => self::canEditAttendance(),
            'can_view_reports' => self::canViewReports(),
            'can_manage_settings' => self::canManageSettings(),
            'role_name' => self::getCurrentRole(),
            'department_id' => self::getCurrentDepartmentId(),
            'employee_data' => self::getCurrentEmployee()
        ];
    }
    
    /**
     * Clear cached user data (useful for logout or role changes)
     */
    public static function clearCache() 
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        unset($_SESSION['role_name']);
        unset($_SESSION['department_id']);
        unset($_SESSION['employee_data']);
    }
    
    /**
     * Check if user can access specific employee data
     * @param int $employee_id
     * @return bool
     */
    public static function canAccessEmployeeData($employee_id) 
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Admin and HR can access all employee data
        if (self::canViewAllAttendance()) {
            return true;
        }
        
        // Users can only access their own data
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] == $employee_id;
    }
    
    /**
     * Get accessible employee IDs for current user
     * @return array
     */
    public static function getAccessibleEmployeeIds() 
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return [];
        }
        
        // Admin and HR can access all employees
        if (self::canViewAllAttendance()) {
            try {
                $db = self::getDB();
                $query = "SELECT id FROM employees WHERE status = 'ทำงาน'";
                $stmt = $db->prepare($query);
                $stmt->execute();
                
                $employee_ids = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $employee_ids[] = $row['id'];
                }
                return $employee_ids;
            } catch (Exception $e) {
                error_log("Error getting accessible employee IDs: " . $e->getMessage());
                return [$_SESSION['user_id']];
            }
        }
        
        // Regular users can only access their own data
        return [$_SESSION['user_id']];
    }
    
    /**
     * Filter data based on user permissions
     * @param array $data
     * @param string $employee_id_field
     * @return array
     */
    public static function filterDataByPermission($data, $employee_id_field = 'employee_id') 
    {
        if (self::canViewAllAttendance()) {
            return $data;
        }
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return [];
        }
        
        return array_filter($data, function($item) use ($employee_id_field) {
            return isset($item[$employee_id_field]) && $item[$employee_id_field] == $_SESSION['user_id'];
        });
    }
    
    /**
     * Get user's menu items based on permissions
     * @return array
     */
    public static function getAuthorizedMenuItems() 
    {
        $permissions = self::getUserPermissions();
        $menu_items = [];
        
        // Dashboard - everyone can access
        $menu_items[] = [
            'name' => 'แดชบอร์ด',
            'url' => BASE_URL . '/dashboard',
            'icon' => 'fas fa-tachometer-alt'
        ];
        
        // Attendance - everyone can access their own
        $menu_items[] = [
            'name' => 'ลงเวลา',
            'url' => BASE_URL . '/attendance/history',
            'icon' => 'fas fa-clock'
        ];
        
        // Employee Management - Admin and HR only
        if ($permissions['can_manage_employees']) {
            $menu_items[] = [
                'name' => 'จัดการพนักงาน',
                'url' => BASE_URL . '/employee',
                'icon' => 'fas fa-users'
            ];
        }
        
        // Leave Management
        $menu_items[] = [
            'name' => 'ลางาน',
            'url' => BASE_URL . '/leave',
            'icon' => 'fas fa-calendar-times'
        ];
        
        // Payroll - Admin and HR only
        if ($permissions['can_view_payroll']) {
            $menu_items[] = [
                'name' => 'เงินเดือน',
                'url' => BASE_URL . '/payroll',
                'icon' => 'fas fa-money-bill-wave'
            ];
        }
        
        // Reports - Admin, HR, and Managers
        if ($permissions['can_view_reports']) {
            $menu_items[] = [
                'name' => 'รายงาน',
                'url' => BASE_URL . '/reports',
                'icon' => 'fas fa-chart-bar'
            ];
        }
        
        // Settings - Admin only
        if ($permissions['can_manage_settings']) {
            $menu_items[] = [
                'name' => 'ตั้งค่า',
                'url' => BASE_URL . '/settings',
                'icon' => 'fas fa-cog'
            ];
        }
        
        return $menu_items;
    }
}

// AttendanceHelper.php - Helper functions สำหรับระบบ attendance
class AttendanceHelper 
{
    /**
     * Format work hours for display
     * @param float $hours
     * @return string
     */
    public static function formatWorkHours($hours) 
    {
        if (empty($hours) || $hours <= 0) {
            return '-';
        }
        
        $whole_hours = floor($hours);
        $minutes = round(($hours - $whole_hours) * 60);
        
        if ($minutes == 0) {
            return $whole_hours . ' ชม.';
        }
        
        return $whole_hours . ' ชม. ' . $minutes . ' นาที';
    }
    
    /**
     * Get status color class
     * @param string $status
     * @return string
     */
    public static function getStatusColorClass($status) 
    {
        switch ($status) {
            case 'ปกติ':
                return 'status-normal';
            case 'สาย':
                return 'status-late';
            case 'ขาดงาน':
                return 'status-absent';
            default:
                return 'status-normal';
        }
    }
    
    /**
     * Get status icon
     * @param string $status
     * @return string
     */
    public static function getStatusIcon($status) 
    {
        switch ($status) {
            case 'ปกติ':
                return '<i class="fas fa-check-circle text-green-500"></i>';
            case 'สาย':
                return '<i class="fas fa-exclamation-triangle text-yellow-500"></i>';
            case 'ขาดงาน':
                return '<i class="fas fa-times-circle text-red-500"></i>';
            default:
                return '<i class="fas fa-question-circle text-gray-500"></i>';
        }
    }
    
    /**
     * Calculate work hours between two timestamps
     * @param string $clock_in
     * @param string $clock_out
     * @return float
     */
    public static function calculateWorkHours($clock_in, $clock_out) 
    {
        if (empty($clock_in) || empty($clock_out)) {
            return 0;
        }
        
        $in = new DateTime($clock_in);
        $out = new DateTime($clock_out);
        $interval = $in->diff($out);
        
        return round($interval->h + ($interval->i / 60), 2);
    }
    
    /**
     * Generate attendance report data
     * @param array $attendance_data
     * @return array
     */
    public static function generateReportData($attendance_data) 
    {
        $report = [
            'total_days' => count($attendance_data),
            'on_time' => 0,
            'late' => 0,
            'absent' => 0,
            'total_hours' => 0,
            'total_ot_hours' => 0,
            'average_hours' => 0
        ];
        
        foreach ($attendance_data as $record) {
            switch ($record['status']) {
                case 'ปกติ':
                    $report['on_time']++;
                    break;
                case 'สาย':
                    $report['late']++;
                    break;
                case 'ขาดงาน':
                    $report['absent']++;
                    break;
            }
            
            $report['total_hours'] += floatval($record['work_hours']);
            $report['total_ot_hours'] += floatval($record['ot_hours']);
        }
        
        $report['average_hours'] = $report['total_days'] > 0 ? 
            round($report['total_hours'] / $report['total_days'], 2) : 0;
        
        return $report;
    }
    
    /**
     * Get Thai day name
     * @param string $date
     * @return string
     */
    public static function getThaiDayName($date) 
    {
        $thai_days = [
            'Sunday' => 'อาทิตย์',
            'Monday' => 'จันทร์',
            'Tuesday' => 'อังคาร',
            'Wednesday' => 'พุธ',
            'Thursday' => 'พฤหัสบดี',
            'Friday' => 'ศุกร์',
            'Saturday' => 'เสาร์'
        ];
        
        $day_name = date('l', strtotime($date));
        return $thai_days[$day_name] ?? $day_name;
    }
    
    /**
     * Format Thai date
     * @param string $date
     * @param bool $include_day
     * @return string
     */
    public static function formatThaiDate($date, $include_day = false) 
    {
        $thai_months = [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม',
            4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน',
            7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน',
            10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
        ];
        
        $timestamp = strtotime($date);
        $day = date('j', $timestamp);
        $month = $thai_months[intval(date('n', $timestamp))];
        $year = date('Y', $timestamp) + 543; // Convert to Buddhist year
        
        $formatted = "{$day} {$month} {$year}";
        
        if ($include_day) {
            $day_name = self::getThaiDayName($date);
            $formatted = "วัน{$day_name}ที่ {$formatted}";
        }
        
        return $formatted;
    }
    
    /**
     * Check if date is weekend
     * @param string $date
     * @return bool
     */
    public static function isWeekend($date) 
    {
        $day_of_week = date('N', strtotime($date));
        return in_array($day_of_week, [6, 7]); // Saturday = 6, Sunday = 7
    }
    
    /**
     * Check if date is holiday
     * @param string $date
     * @param PDO $db
     * @return bool
     */
    public static function isHoliday($date, $db) 
    {
        try {
            $query = "SELECT COUNT(*) as count FROM holidays WHERE holiday_date = :date";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (Exception $e) {
            error_log("Error checking holiday: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get late threshold time
     * @param PDO $db
     * @return string
     */
    public static function getLateThreshold($db) 
    {
        try {
            require_once __DIR__ . '/../models/Setting.php';
            $setting = new Setting($db);
            $work_start_time = $setting->getSettingValue('work_start_time', '08:30');
            $grace_period = (int)$setting->getSettingValue('grace_period_minutes', 15);
            
            $threshold = new DateTime(date('Y-m-d') . ' ' . $work_start_time);
            $threshold->modify("+{$grace_period} minutes");
            
            return $threshold->format('H:i:s');
        } catch (Exception $e) {
            error_log("Error getting late threshold: " . $e->getMessage());
            return '08:45:00'; // Default fallback
        }
    }
}
?>