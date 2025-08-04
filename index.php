<?php
<<<<<<< HEAD
// index.php - Enhanced with new attendance routes
session_start(); // เริ่มต้น session สำหรับการเก็บข้อมูล login

// --- การตั้งค่าพื้นฐาน ---
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php'; // เพิ่ม: การเชื่อมต่อฐานข้อมูล

// --- Routing แบบปรับปรุง ---

// 1. แยกส่วนของ URL ออกมา
$request_uri = $_SERVER['REQUEST_URI'];
$path = trim(parse_url($request_uri, PHP_URL_PATH), '/');

// แก้ไข: ใช้เฉพาะ path หลัง domain แทนการใช้ BASE_URL
// เช่น จาก localhost/obt-nongpaklong-hr/departments/edit/2 
// จะได้ obt-nongpaklong-hr/departments/edit/2

// ลบ base directory ออก (obt-nongpaklong-hr)
$base_dir = 'obt-nongpaklong-hr'; // ชื่อโฟลเดอร์โปรเจกต์
if (strpos($path, $base_dir) === 0) {
    $route = substr($path, strlen($base_dir));
    $route = trim($route, '/');
} else {
    $route = $path;
}

// แยก segments
$segments = !empty($route) ? explode('/', $route) : [];

// 2. กำหนด controller, action, และ parameter เริ่มต้น
$controller_name_from_url = !empty($segments[0]) ? strtolower($segments[0]) : 'dashboard';
$action_name = $segments[1] ?? 'index';
// ตรวจสอบและส่ง parameter ทั้งหมดหลัง action ไปให้ controller
$params = array_slice($segments, 2); 

// Special handling for attendance routes with specific actions
if ($controller_name_from_url === 'attendance') {
    // Handle special attendance routes
    switch ($action_name) {
        case 'clock-in':
        case 'clockin':
            $action_name = 'clockIn';
            break;
        case 'clock-out':
        case 'clockout':
            $action_name = 'clockOut';
            break;
        case 'getEmployeesByDepartment':
        case 'get-employees':
            $action_name = 'getEmployeesByDepartment';
            break;
        case 'getStats':
        case 'get-stats':
            $action_name = 'getStats';
            break;
        case 'apiData':
        case 'api-data':
            $action_name = 'apiAttendanceData';
            break;
        case 'deviceLog':
        case 'device-log':
            $action_name = 'deviceLog';
            break;
        case 'todayStatus':
        case 'today-status':
            $action_name = 'todayStatus';
            break;
        case 'previous_income':
        case 'previous-income':
            $action_name = 'previous_income';
            break;
        case 'save_previous_income':
        case 'save-previous-income':
            $action_name = 'save_previous_income';
            break;
    }
}

// Special handling for employee routes
if ($controller_name_from_url === 'employee') {
    switch ($action_name) {
        case 'previous_income':
        case 'previous-income':
            $action_name = 'previous_income';
            break;
        case 'save_previous_income':
        case 'save-previous-income':
            $action_name = 'save_previous_income';
            break;
        case 'apiData':
        case 'api-data':
            $action_name = 'apiData';
            break;
    }
}

// 3. สร้าง Mapping ของ Route ไปยัง Controller Class (ฉบับรวมเมนูทั้งหมด)
$controller_map = [
    // Core System
    'login'         => 'AuthController',
    'auth'          => 'AuthController',
    'dashboard'     => 'DashboardController',
    'profile'       => 'ProfileController',
    
    // HR Management
    'employee'      => 'EmployeeController',
    'employees'     => 'EmployeeController', // เพิ่มรูปแบบพหูพจน์
    'department'    => 'DepartmentController',
    'departments'   => 'DepartmentController', // เพิ่มรูปแบบพหูพจน์
    'position'      => 'PositionController',
    'positions'     => 'PositionController', // เพิ่มรูปแบบพหูพจน์
    'movement'      => 'MovementController',
    'movements'     => 'MovementController', // เพิ่มรูปแบบพหูพจน์
    
    // Attendance & Leave
    'attendance'    => 'AttendanceController',
    'leave'         => 'LeaveController',
    'leaves'        => 'LeaveController', // เพิ่มรูปแบบพหูพจน์
    'leave_type'    => 'LeaveTypeController',
    'leave_types'   => 'LeaveTypeController', // เพิ่มรูปแบบพหูพจน์
    'workflow'      => 'ApprovalWorkflowController',
    'workflows'     => 'ApprovalWorkflowController', // เพิ่มรูปแบบพหูพจน์
    'holiday'       => 'HolidayController',
    'holidays'      => 'HolidayController', // เพิ่มรูปแบบพหูพจน์
    
    // Payroll
    'payslip'       => 'PayslipController',
    'payslips'      => 'PayslipController', // เพิ่มรูปแบบพหูพจน์
    'payroll'       => 'PayrollController',
    'payrolls'      => 'PayrollController', // เพิ่มรูปแบบพหูพจน์

    // Reports
    'report'        => 'ReportController',
    'reports'       => 'ReportController', // เพิ่มรูปแบบพหูพจน์
    
    // Settings
    'settings'      => 'SettingsController',
    'role'          => 'RoleController',
    'roles'         => 'RoleController', // เพิ่มรูปแบบพหูพจน์
    'leavepolicy'   => 'LeavePolicyController',
    'leavepolicies' => 'LeavePolicyController', // เพิ่มรูปแบบพหูพจน์

    // Communication
    'announcement'  => 'AnnouncementController',
    'announcements' => 'AnnouncementController', // เพิ่มรูปแบบพหูพจน์
    'notification'  => 'NotificationController',
    'notifications' => 'NotificationController', // เพิ่มรูปแบบพหูพจน์

    // ZK-BIOTIMEAPP Integration
    'zk-device'     => 'ZkDeviceController',
    'zk-devices'    => 'ZkDeviceController', // เพิ่มรูปแบบพหูพจน์

    // Data Management
    'data_import'   => 'DataImportController',
    'data_export'   => 'DataExportController',
    'backup'        => 'BackupController',
    'backups'       => 'BackupController', // เพิ่มรูปแบบพหูพจน์
    'audit_log'     => 'AuditLogController',
    'audit_logs'    => 'AuditLogController', // เพิ่มรูปแบบพหูพจน์

    // API Endpoints
    'api'           => 'ApiController'
];

// Debug: เพิ่มการ debug เพื่อตรวจสอบ routing (comment out เมื่อไม่ใช้)
/*
echo "DEBUG INFO:<br>";
echo "Request URI: " . $request_uri . "<br>";
echo "Path: " . $path . "<br>";
echo "Route: " . $route . "<br>";
echo "Controller: " . $controller_name_from_url . "<br>";
echo "Action: " . $action_name . "<br>";
echo "Params: " . implode(', ', $params) . "<br>";
echo "Segments: " . implode(', ', $segments) . "<br>";
echo "<hr>";
*/

// 4. ตรวจสอบและเรียกใช้งาน Controller
if (array_key_exists($controller_name_from_url, $controller_map)) {
    $controller_class = $controller_map[$controller_name_from_url];
    $controller_file = __DIR__ . '/controllers/' . $controller_class . '.php';

    if (file_exists($controller_file)) {
        require_once $controller_file;
        
        if (class_exists($controller_class)) {
            try {
                $controller = new $controller_class();
                
                if (method_exists($controller, $action_name)) {
                    // เรียกใช้งาน action พร้อมส่ง parameter ทั้งหมด
                    call_user_func_array([$controller, $action_name], $params);
                } else {
                    // ถ้า action ไม่พบ, ให้เรียก index() แทน หรือแสดง 404
                    if (method_exists($controller, 'index')) {
                        $controller->index();
                    } else {
                        http_response_code(404);
                        
                        // For API requests, return JSON
                        if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false || 
                            strpos($controller_name_from_url, 'api') === 0) {
                            header('Content-Type: application/json');
                            echo json_encode([
                                'error' => true,
                                'message' => "Action '{$action_name}' not found in {$controller_class}",
                                'code' => 404
                            ]);
                        } else {
                            echo "404 - Action '{$action_name}' not found in {$controller_class}";
                            // require_once __DIR__ . '/views/404.php'; // แสดงหน้า 404
                        }
                    }
                }
            } catch (Exception $e) {
                error_log("Controller Error: " . $e->getMessage());
                http_response_code(500);
                
                // For API requests, return JSON
                if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false || 
                    strpos($controller_name_from_url, 'api') === 0) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'error' => true,
                        'message' => 'Internal server error',
                        'code' => 500
                    ]);
                } else {
                    if (defined('APP_DEBUG') && APP_DEBUG) {
                        echo "500 - Controller Error: " . $e->getMessage();
                    } else {
                        echo "500 - Internal Server Error";
                    }
                    // require_once __DIR__ . '/views/500.php'; // แสดงหน้า 500
                }
            }
        } else {
            http_response_code(404);
            echo "404 - Class '{$controller_class}' not found";
            // require_once __DIR__ . '/views/404.php'; // แสดงหน้า 404
        }
    } else {
        http_response_code(404);
        echo "404 - Controller file not found: {$controller_file}";
        // require_once __DIR__ . '/views/404.php'; // แสดงหน้า 404
    }
} else {
    // กรณีที่ controller_name_from_url ไม่พบใน $controller_map
    http_response_code(404);
    
    // For API requests, return JSON
    if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true,
            'message' => "Controller '{$controller_name_from_url}' not mapped",
            'code' => 404
        ]);
    } else {
        echo "404 - Controller '{$controller_name_from_url}' not mapped";
        // require_once __DIR__ . '/views/404.php'; // แสดงหน้า 404
    }
}

// Log requests for debugging (optional)
if (defined('APP_LOG_REQUESTS') && APP_LOG_REQUESTS) {
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'method' => $_SERVER['REQUEST_METHOD'],
        'uri' => $request_uri,
        'controller' => $controller_name_from_url,
        'action' => $action_name,
        'params' => $params,
        'user_id' => $_SESSION['user_id'] ?? null,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ];
    
    error_log("Request Log: " . json_encode($log_entry));
}
?>
=======
// index.php
session_start(); // เริ่มต้น session สำหรับการเก็บข้อมูล login

// --- Routing แบบง่าย ---
// แยกส่วนของ URL ออกมา
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'login';
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

// กำหนด controller และ action เริ่มต้น
$controllerName = !empty($urlParts[0]) ? ucfirst($urlParts[0]) . 'Controller' : 'AuthController';
$actionName = isset($urlParts[1]) ? $urlParts[1] : 'index';
$params = array_slice($urlParts, 2);

// ตรวจสอบว่าไฟล์ controller มีอยู่จริงหรือไม่
$controllerFile = 'controllers/' . $controllerName . '.php';
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    // ตรวจสอบว่า class และ method มีอยู่จริงหรือไม่
    if (class_exists($controllerName) && method_exists($controllerName, $actionName)) {
        $controller = new $controllerName();
        // เรียกใช้งาน action พร้อมส่ง parameter (ถ้ามี)
        call_user_func_array([$controller, $actionName], $params);
    } else {
        // หาไม่เจอ ให้ไปหน้า 404
        echo "404 - Method or Class Not Found";
    }
} else {
    // ถ้าเข้ามาหน้าแรกสุด หรือไม่มี controller ให้ไปหน้า login
    if ($controllerName === 'LoginController' || $controllerName === 'AuthController') {
         require_once 'controllers/AuthController.php';
         $controller = new AuthController();
         $controller->index();
    } else {
        // หาไม่เจอ ให้ไปหน้า 404
        echo "404 - Controller Not Found";
    }
}
?>
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
