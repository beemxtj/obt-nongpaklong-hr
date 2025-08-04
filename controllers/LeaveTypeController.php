<?php
// controllers/LeaveTypeController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/LeaveType.php';
require_once __DIR__ . '/../helpers/RoleHelper.php';

class LeaveTypeController 
{
    private $db;
    private $leaveType;
    
    public function __construct() 
    {
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
        $this->leaveType = new LeaveType($this->db);
    }
    
    /**
     * Display leave types list
     */
    public function index() 
    {
        // Check permissions - Admin and HR can manage leave types
        RoleHelper::requirePermission('manage_settings');
        
        try {
            $stmt = $this->leaveType->readAll();
            $num = $stmt->rowCount();
            
            // Set page data
            $page_title = 'จัดการประเภทการลา';
            
            // Include the view
            require_once __DIR__ . '/../views/settings/leave_types/index.php';
            
        } catch (Exception $e) {
            error_log("Error in LeaveTypeController::index(): " . $e->getMessage());
            $_SESSION['error_message'] = 'เกิดข้อผิดพลาดในการโหลดข้อมูล';
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
    }
    
    /**
     * Show create form
     */
    public function create() 
    {
        RoleHelper::requirePermission('manage_settings');
        
        $page_title = 'เพิ่มประเภทการลาใหม่';
        
        require_once __DIR__ . '/../views/settings/leave_types/form.php';
    }
    
    /**
     * Store new leave type
     */
    public function store() 
    {
        RoleHelper::requirePermission('manage_settings');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/leave_types');
            exit();
        }
        
        try {
            // Set leave type properties
            $this->leaveType->name = trim($_POST['name'] ?? '');
            $this->leaveType->max_days_per_year = (int)($_POST['max_days_per_year'] ?? 0);
            $this->leaveType->is_paid = isset($_POST['is_paid']) ? 1 : 0;
            
            // Validate data
            $errors = $this->leaveType->validate();
            
            if (!empty($errors)) {
                $_SESSION['error_message'] = implode('<br>', $errors);
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . '/leave_types/create');
                exit();
            }
            
            // Create leave type
            if ($this->leaveType->create()) {
                $_SESSION['success_message'] = 'เพิ่มประเภทการลาเรียบร้อยแล้ว';
                unset($_SESSION['form_data']);
            } else {
                $_SESSION['error_message'] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . '/leave_types/create');
                exit();
            }
            
        } catch (Exception $e) {
            error_log("Error in LeaveTypeController::store(): " . $e->getMessage());
            $_SESSION['error_message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/leave_types/create');
            exit();
        }
        
        header('Location: ' . BASE_URL . '/leave_types');
        exit();
    }
    
    /**
     * Show edit form
     * @param int $id
     */
    public function edit($id) 
    {
        RoleHelper::requirePermission('manage_settings');
        
        if (empty($id) || !is_numeric($id)) {
            $_SESSION['error_message'] = 'รหัสประเภทการลาไม่ถูกต้อง';
            header('Location: ' . BASE_URL . '/leave_types');
            exit();
        }
        
        try {
            if (!$this->leaveType->readOne($id)) {
                $_SESSION['error_message'] = 'ไม่พบประเภทการลาที่ต้องการแก้ไข';
                header('Location: ' . BASE_URL . '/leave_types');
                exit();
            }
            
            $page_title = 'แก้ไขประเภทการลา';
            $leave_type = $this->leaveType;
            
            require_once __DIR__ . '/../views/settings/leave_types/form.php';
            
        } catch (Exception $e) {
            error_log("Error in LeaveTypeController::edit(): " . $e->getMessage());
            $_SESSION['error_message'] = 'เกิดข้อผิดพลาดในการโหลดข้อมูล';
            header('Location: ' . BASE_URL . '/leave_types');
            exit();
        }
    }
    
    /**
     * Update leave type
     * @param int $id
     */
    public function update($id) 
    {
        RoleHelper::requirePermission('manage_settings');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/leave_types');
            exit();
        }
        
        if (empty($id) || !is_numeric($id)) {
            $_SESSION['error_message'] = 'รหัสประเภทการลาไม่ถูกต้อง';
            header('Location: ' . BASE_URL . '/leave_types');
            exit();
        }
        
        try {
            // Load current data
            if (!$this->leaveType->readOne($id)) {
                $_SESSION['error_message'] = 'ไม่พบประเภทการลาที่ต้องการแก้ไข';
                header('Location: ' . BASE_URL . '/leave_types');
                exit();
            }
            
            // Update properties
            $this->leaveType->name = trim($_POST['name'] ?? '');
            $this->leaveType->max_days_per_year = (int)($_POST['max_days_per_year'] ?? 0);
            $this->leaveType->is_paid = isset($_POST['is_paid']) ? 1 : 0;
            
            // Validate data
            $errors = $this->leaveType->validate();
            
            if (!empty($errors)) {
                $_SESSION['error_message'] = implode('<br>', $errors);
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . '/leave_types/edit/' . $id);
                exit();
            }
            
            // Update leave type
            if ($this->leaveType->update()) {
                $_SESSION['success_message'] = 'แก้ไขประเภทการลาเรียบร้อยแล้ว';
                unset($_SESSION['form_data']);
            } else {
                $_SESSION['error_message'] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . '/leave_types/edit/' . $id);
                exit();
            }
            
        } catch (Exception $e) {
            error_log("Error in LeaveTypeController::update(): " . $e->getMessage());
            $_SESSION['error_message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/leave_types/edit/' . $id);
            exit();
        }
        
        header('Location: ' . BASE_URL . '/leave_types');
        exit();
    }
    
    /**
     * Delete leave type
     * @param int $id
     */
    public function destroy($id) 
    {
        RoleHelper::requirePermission('manage_settings');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/leave_types');
            exit();
        }
        
        if (empty($id) || !is_numeric($id)) {
            $_SESSION['error_message'] = 'รหัสประเภทการลาไม่ถูกต้อง';
            header('Location: ' . BASE_URL . '/leave_types');
            exit();
        }
        
        try {
            // Load leave type data for confirmation
            if (!$this->leaveType->readOne($id)) {
                $_SESSION['error_message'] = 'ไม่พบประเภทการลาที่ต้องการลบ';
                header('Location: ' . BASE_URL . '/leave_types');
                exit();
            }
            
            $leave_type_name = $this->leaveType->name;
            
            // Delete leave type
            if ($this->leaveType->delete()) {
                $_SESSION['success_message'] = 'ลบประเภทการลา "' . $leave_type_name . '" เรียบร้อยแล้ว';
            } else {
                $_SESSION['error_message'] = 'เกิดข้อผิดพลาดในการลบข้อมูล';
            }
            
        } catch (Exception $e) {
            error_log("Error in LeaveTypeController::destroy(): " . $e->getMessage());
            $_SESSION['error_message'] = $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '/leave_types');
        exit();
    }
    
    /**
     * Get leave types for API/AJAX calls
     */
    public function apiData() 
    {
        header('Content-Type: application/json');
        
        try {
            $leave_types = $this->leaveType->getActiveLeaveTypes();
            
            echo json_encode([
                'success' => true,
                'data' => $leave_types
            ]);
            
        } catch (Exception $e) {
            error_log("Error in LeaveTypeController::apiData(): " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล'
            ]);
        }
    }
    
    /**
     * Export leave types data
     */
    public function export() 
    {
        RoleHelper::requirePermission('export_data');
        
        try {
            $data = $this->leaveType->exportData();
            
            // Set headers for file download
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="leave_types_' . date('Y-m-d_H-i-s') . '.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Add BOM for UTF-8
            echo "\xEF\xBB\xBF";
            
            // Create file pointer
            $output = fopen('php://output', 'w');
            
            // Add headers
            if (!empty($data)) {
                fputcsv($output, array_keys($data[0]));
                
                // Add data rows
                foreach ($data as $row) {
                    fputcsv($output, $row);
                }
            }
            
            fclose($output);
            
        } catch (Exception $e) {
            error_log("Error in LeaveTypeController::export(): " . $e->getMessage());
            $_SESSION['error_message'] = 'เกิดข้อผิดพลาดในการส่งออกข้อมูล';
            header('Location: ' . BASE_URL . '/leave_types');
            exit();
        }
    }
    
    /**
     * Get statistics dashboard
     */
    public function stats() 
    {
        header('Content-Type: application/json');
        
        try {
            $stats = $this->leaveType->getStats();
            
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (Exception $e) {
            error_log("Error in LeaveTypeController::stats(): " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการดึงสถิติ'
            ]);
        }
    }
    
    /**
     * Bulk operations
     */
    public function bulk() 
    {
        RoleHelper::requirePermission('manage_settings');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/leave_types');
            exit();
        }
        
        $action = $_POST['bulk_action'] ?? '';
        $selected_ids = $_POST['selected_ids'] ?? [];
        
        if (empty($action) || empty($selected_ids)) {
            $_SESSION['error_message'] = 'กรุณาเลือกรายการและการดำเนินการ';
            header('Location: ' . BASE_URL . '/leave_types');
            exit();
        }
        
        try {
            $success_count = 0;
            $error_count = 0;
            
            foreach ($selected_ids as $id) {
                if (!is_numeric($id)) continue;
                
                switch ($action) {
                    case 'delete':
                        if ($this->leaveType->readOne($id)) {
                            if ($this->leaveType->delete()) {
                                $success_count++;
                            } else {
                                $error_count++;
                            }
                        }
                        break;
                        
                    case 'make_paid':
                        if ($this->leaveType->readOne($id)) {
                            $this->leaveType->is_paid = 1;
                            if ($this->leaveType->update()) {
                                $success_count++;
                            } else {
                                $error_count++;
                            }
                        }
                        break;
                        
                    case 'make_unpaid':
                        if ($this->leaveType->readOne($id)) {
                            $this->leaveType->is_paid = 0;
                            if ($this->leaveType->update()) {
                                $success_count++;
                            } else {
                                $error_count++;
                            }
                        }
                        break;
                }
            }
            
            if ($success_count > 0) {
                $_SESSION['success_message'] = "ดำเนินการสำเร็จ {$success_count} รายการ";
            }
            
            if ($error_count > 0) {
                $_SESSION['error_message'] = "ดำเนินการไม่สำเร็จ {$error_count} รายการ";
            }
            
        } catch (Exception $e) {
            error_log("Error in LeaveTypeController::bulk(): " . $e->getMessage());
            $_SESSION['error_message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '/leave_types');
        exit();
    }
}
?>