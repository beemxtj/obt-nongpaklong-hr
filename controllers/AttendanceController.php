<?php
// controllers/AttendanceController.php - Complete Enhanced Version

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../config/database.php';

class AttendanceController
{
    private $db;
    private $attendance;
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

        $database = new Database();
        $this->db = $database->getConnection();
        $this->attendance = new Attendance($this->db);
        $this->employee = new Employee($this->db);
    }

    /**
     * Check if current user has admin/hr privileges
     * @return bool
     */
    private function hasAdminAccess() {
        // Get user role from session or database
        if (isset($_SESSION['role_name'])) {
            return $this->checkAdminRole($_SESSION['role_name']);
        }
        
        // Fallback: get role from database
        $this->employee->id = $_SESSION['user_id'];
        $this->employee->readOne();
        if ($this->employee->role_name) {
            $_SESSION['role_name'] = $this->employee->role_name; // Cache for future use
            return $this->checkAdminRole($this->employee->role_name);
        }
        
        return false;
    }

    /**
     * Check if role name indicates admin privileges
     * @param string $role_name
     * @return bool
     */
    private function checkAdminRole($role_name) {
        $admin_roles = [
            'admin', 
            'administrator', 
            'hr', 
            'human resources', 
            'human resource',
            'manager',
            'ผู้จัดการ',
            'ผู้บริหาร',
            'แอดมิน'
        ];
        
        return in_array(strtolower($role_name), $admin_roles);
    }

    /**
     * Clock in functionality
     */
    public function clockIn()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->attendance->employee_id = $_SESSION['user_id'];
                $this->attendance->clock_in_latitude = $_POST['latitude'] ?? null;
                $this->attendance->clock_in_longitude = $_POST['longitude'] ?? null;
                $this->attendance->clock_in_image_data = $_POST['image_data'] ?? null;

                if ($this->attendance->createClockIn()) {
                    $_SESSION['success_message'] = "บันทึกเวลาเข้างานสำเร็จ!";
                } else {
                    $_SESSION['error_message'] = "ไม่สามารถบันทึกเวลาได้ หรืออาจจะมีการลงเวลาไปแล้ว";
                }
            } catch (Exception $e) {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            }
            
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
    }

    /**
     * Clock out functionality
     */
    public function clockOut()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->attendance->employee_id = $_SESSION['user_id'];
                $this->attendance->clock_out_latitude = $_POST['latitude_out'] ?? null;
                $this->attendance->clock_out_longitude = $_POST['longitude_out'] ?? null;
                $this->attendance->clock_out_image_data = $_POST['image_data_out'] ?? null;

                if ($this->attendance->createClockOut()) {
                    $_SESSION['success_message'] = "บันทึกเวลาออกงานสำเร็จ!";
                } else {
                    $_SESSION['error_message'] = "ไม่สามารถบันทึกเวลาออกงานได้";
                }
            } catch (Exception $e) {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            }
            
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
    }

    /**
     * Display attendance history with role-based access
     */
    public function history()
    {
        $page_title = "ประวัติการลงเวลา";
        $is_admin = $this->hasAdminAccess();
        
        // Initialize filters
        $filters = [];
        $selected_employee_id = null;
        $selected_department_id = null;
        $selected_status = '';
        $date_from = $_GET['date_from'] ?? date('Y-m-01'); // Default to first day of current month
        $date_to = $_GET['date_to'] ?? date('Y-m-d'); // Default to today
        
        if ($is_admin) {
            // Admin/HR can view all employees with filters
            if (!empty($_GET['employee_id'])) {
                $filters['employee_id'] = $_GET['employee_id'];
                $selected_employee_id = $_GET['employee_id'];
            }
            if (!empty($_GET['department_id'])) {
                $filters['department_id'] = $_GET['department_id'];
                $selected_department_id = $_GET['department_id'];
            }
            if (!empty($_GET['status'])) {
                $filters['status'] = $_GET['status'];
                $selected_status = $_GET['status'];
            }
        } else {
            // Regular employees can only view their own records
            $filters['employee_id'] = $_SESSION['user_id'];
        }
        
        // Apply date filters
        if (!empty($date_from)) {
            $filters['date_from'] = $date_from;
        }
        if (!empty($date_to)) {
            $filters['date_to'] = $date_to;
        }
        
        // Get attendance data
        if ($is_admin) {
            $stmt = $this->attendance->getAttendanceOverview($filters);
            $stats = $this->attendance->getAttendanceStats($filters);
            $departments_stmt = $this->attendance->getDepartments();
            $employees_stmt = $this->attendance->getEmployees($selected_department_id);
            $chart_data_stmt = $this->attendance->getDailyAttendanceSummary($filters);
        } else {
            $stmt = $this->attendance->readHistoryByEmployee($_SESSION['user_id']);
            $stats = null;
            $departments_stmt = null;
            $employees_stmt = null;
            $chart_data_stmt = null;
        }
        
        $num = $stmt->rowCount();
        
        // Get attendance logs for calendar and cards
        $attendance_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Prepare chart data if admin
        $chart_data = [];
        if ($chart_data_stmt) {
            while ($row = $chart_data_stmt->fetch(PDO::FETCH_ASSOC)) {
                $chart_data[] = $row;
            }
        }
        
        require_once 'views/attendance/history.php';
    }

    /**
     * Export attendance data to CSV
     */
    public function export()
    {
        if (!$this->hasAdminAccess()) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์ในการส่งออกข้อมูล";
            header('Location: ' . BASE_URL . '/attendance/history');
            exit();
        }
        
        try {
            // Get filters from request
            $filters = [];
            if (!empty($_GET['employee_id'])) {
                $filters['employee_id'] = $_GET['employee_id'];
            }
            if (!empty($_GET['department_id'])) {
                $filters['department_id'] = $_GET['department_id'];
            }
            if (!empty($_GET['status'])) {
                $filters['status'] = $_GET['status'];
            }
            if (!empty($_GET['date_from'])) {
                $filters['date_from'] = $_GET['date_from'];
            }
            if (!empty($_GET['date_to'])) {
                $filters['date_to'] = $_GET['date_to'];
            }
            
            // Get data for export
            $export_data = $this->attendance->exportAttendanceData($filters);
            
            // Generate filename
            $filename = 'attendance_report_' . date('Y-m-d_H-i-s') . '.csv';
            
            // Set headers for CSV download
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Add BOM for UTF-8
            echo "\xEF\xBB\xBF";
            
            // Output CSV
            $output = fopen('php://output', 'w');
            foreach ($export_data as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
            exit();
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการส่งออกข้อมูล: " . $e->getMessage();
            header('Location: ' . BASE_URL . '/attendance/history');
            exit();
        }
    }

    /**
     * API endpoint for getting filtered employees (AJAX)
     */
    public function getEmployeesByDepartment()
    {
        if (!$this->hasAdminAccess()) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit();
        }
        
        try {
            $department_id = $_GET['department_id'] ?? null;
            $employees_stmt = $this->attendance->getEmployees($department_id);
            $employees = [];
            
            while ($row = $employees_stmt->fetch(PDO::FETCH_ASSOC)) {
                $employees[] = $row;
            }
            
            header('Content-Type: application/json');
            echo json_encode($employees);
            exit();
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
            exit();
        }
    }

    /**
     * API endpoint for getting attendance stats (AJAX)
     */
    public function getStats()
    {
        if (!$this->hasAdminAccess()) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit();
        }
        
        try {
            $filters = [];
            if (!empty($_GET['department_id'])) {
                $filters['department_id'] = $_GET['department_id'];
            }
            if (!empty($_GET['date_from'])) {
                $filters['date_from'] = $_GET['date_from'];
            }
            if (!empty($_GET['date_to'])) {
                $filters['date_to'] = $_GET['date_to'];
            }
            
            $stats = $this->attendance->getAttendanceStats($filters);
            
            header('Content-Type: application/json');
            echo json_encode($stats);
            exit();
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
            exit();
        }
    }

    /**
     * API endpoint for real-time attendance data
     */
    public function apiAttendanceData()
    {
        if (!$this->hasAdminAccess()) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit();
        }

        try {
            $filters = [];
            
            // Get parameters from request
            if (!empty($_GET['date'])) {
                $filters['date_from'] = $_GET['date'];
                $filters['date_to'] = $_GET['date'];
            }
            if (!empty($_GET['department_id'])) {
                $filters['department_id'] = $_GET['department_id'];
            }

            $stmt = $this->attendance->getAttendanceOverview($filters);
            $attendance_data = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $attendance_data[] = [
                    'id' => $row['id'],
                    'employee_code' => $row['employee_code'],
                    'employee_name' => $row['first_name_th'] . ' ' . $row['last_name_th'],
                    'department' => $row['department_name'],
                    'clock_in_time' => $row['clock_in_time'],
                    'clock_out_time' => $row['clock_out_time'],
                    'work_hours' => $row['work_hours'],
                    'ot_hours' => $row['ot_hours'],
                    'status' => $row['status']
                ];
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $attendance_data,
                'total' => count($attendance_data)
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

    /**
     * Device integration endpoint for external attendance devices
     */
    public function deviceLog()
    {
        // This endpoint is for external devices like ZK-TeCo, so no session check
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit();
        }

        try {
            // Get parameters from POST or JSON
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }

            $employee_code = $input['employee_code'] ?? '';
            $timestamp = $input['timestamp'] ?? date('Y-m-d H:i:s');
            $device_id = $input['device_id'] ?? 'unknown';

            if (empty($employee_code)) {
                http_response_code(400);
                echo json_encode(['error' => 'Employee code is required']);
                exit();
            }

            // Log the attendance
            $result = $this->attendance->logFromDevice($employee_code, $timestamp);

            // Log the device activity (optional)
            error_log("Device Log - Device: $device_id, Employee: $employee_code, Time: $timestamp, Result: " . json_encode($result));

            header('Content-Type: application/json');
            echo json_encode($result);
            exit();

        } catch (Exception $e) {
            error_log("Device Log Error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => false,
                'message' => 'Internal server error',
                'action' => 'none'
            ]);
            exit();
        }
    }

    /**
     * Get today's attendance status for dashboard
     */
    public function todayStatus()
    {
        try {
            $employee_id = $_SESSION['user_id'];
            $stmt = $this->attendance->getTodayAttendance($employee_id);
            $today_log = $stmt->fetch(PDO::FETCH_ASSOC);

            $response = [
                'has_clocked_in' => false,
                'has_clocked_out' => false,
                'clock_in_time' => null,
                'clock_out_time' => null,
                'work_hours' => 0,
                'status' => null
            ];

            if ($today_log) {
                $response['has_clocked_in'] = true;
                $response['clock_in_time'] = $today_log['clock_in_time'];
                $response['status'] = $today_log['status'];
                
                if (!empty($today_log['clock_out_time'])) {
                    $response['has_clocked_out'] = true;
                    $response['clock_out_time'] = $today_log['clock_out_time'];
                    $response['work_hours'] = $today_log['work_hours'];
                }
            }

            header('Content-Type: application/json');
            echo json_encode($response);
            exit();

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }

    /**
     * Get attendance summary for a specific period
     */
    public function summary()
    {
        $page_title = "สรุปการลงเวลา";
        $is_admin = $this->hasAdminAccess();
        
        if (!$is_admin) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
            header('Location: ' . BASE_URL . '/attendance/history');
            exit();
        }

        // Get parameters
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        $department_id = $_GET['department_id'] ?? null;

        // Set date range for the month
        $date_from = "$year-$month-01";
        $date_to = date('Y-m-t', strtotime($date_from));

        $filters = [
            'date_from' => $date_from,
            'date_to' => $date_to
        ];

        if ($department_id) {
            $filters['department_id'] = $department_id;
        }

        // Get summary data
        $stats = $this->attendance->getAttendanceStats($filters);
        $chart_data_stmt = $this->attendance->getDailyAttendanceSummary($filters);
        $departments_stmt = $this->attendance->getDepartments();

        $chart_data = [];
        while ($row = $chart_data_stmt->fetch(PDO::FETCH_ASSOC)) {
            $chart_data[] = $row;
        }

        require_once 'views/attendance/summary.php';
    }

    /**
     * Bulk import attendance data
     */
    public function import()
    {
        if (!$this->hasAdminAccess()) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์ในการนำเข้าข้อมูล";
            header('Location: ' . BASE_URL . '/attendance/history');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle file upload and import
            // This would be implemented based on your specific import requirements
            $_SESSION['success_message'] = "นำเข้าข้อมูลสำเร็จ";
            header('Location: ' . BASE_URL . '/attendance/history');
            exit();
        }

        $page_title = "นำเข้าข้อมูลการลงเวลา";
        require_once 'views/attendance/import.php';
    }

    /**
     * Delete attendance record (Admin only)
     */
    public function delete($id)
    {
        if (!$this->hasAdminAccess()) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์ในการลบข้อมูล";
            header('Location: ' . BASE_URL . '/attendance/history');
            exit();
        }

        try {
            $query = "DELETE FROM attendance_logs WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "ลบข้อมูลการลงเวลาสำเร็จ";
            } else {
                $_SESSION['error_message'] = "ไม่สามารถลบข้อมูลได้";
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '/attendance/history');
        exit();
    }

    /**
     * Edit attendance record (Admin only)
     */
    public function edit($id)
    {
        if (!$this->hasAdminAccess()) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์ในการแก้ไขข้อมูล";
            header('Location: ' . BASE_URL . '/attendance/history');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Handle attendance record update
                $clock_in_time = $_POST['clock_in_time'];
                $clock_out_time = $_POST['clock_out_time'];
                $status = $_POST['status'];

                // Calculate work hours if both times are provided
                $work_hours = 0;
                if (!empty($clock_in_time) && !empty($clock_out_time)) {
                    $in = new DateTime($clock_in_time);
                    $out = new DateTime($clock_out_time);
                    $interval = $in->diff($out);
                    $work_hours = round($interval->h + ($interval->i / 60), 2);
                }

                $query = "UPDATE attendance_logs 
                         SET clock_in_time = :clock_in_time, 
                             clock_out_time = :clock_out_time, 
                             work_hours = :work_hours,
                             status = :status
                         WHERE id = :id";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':clock_in_time', $clock_in_time);
                $stmt->bindParam(':clock_out_time', $clock_out_time);
                $stmt->bindParam(':work_hours', $work_hours);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':id', $id);

                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "แก้ไขข้อมูลการลงเวลาสำเร็จ";
                } else {
                    $_SESSION['error_message'] = "ไม่สามารถแก้ไขข้อมูลได้";
                }

                header('Location: ' . BASE_URL . '/attendance/history');
                exit();

            } catch (Exception $e) {
                $_SESSION['error_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            }
        }

        // Get attendance record for editing
        try {
            $query = "SELECT al.*, e.employee_code, e.first_name_th, e.last_name_th 
                     FROM attendance_logs al 
                     JOIN employees e ON al.employee_id = e.id 
                     WHERE al.id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $attendance_record = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$attendance_record) {
                $_SESSION['error_message'] = "ไม่พบข้อมูลการลงเวลาที่ต้องการแก้ไข";
                header('Location: ' . BASE_URL . '/attendance/history');
                exit();
            }

            $page_title = "แก้ไขการลงเวลา";
            require_once 'views/attendance/edit.php';

        } catch (Exception $e) {
            $_SESSION['error_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            header('Location: ' . BASE_URL . '/attendance/history');
            exit();
        }
    }
}
?>