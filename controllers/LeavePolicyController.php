<?php
// controllers/LeavePolicyController.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/LeavePolicy.php';
require_once __DIR__ . '/../helpers/RoleHelper.php';

class LeavePolicyController 
{
    private $db;
    private $leavePolicy;
    
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
        $this->leavePolicy = new LeavePolicy($this->db);
    }
    
    /**
     * Display leave policies list
     */
    public function index() 
    {
        // Check permissions - Admin and HR can manage leave policies
        RoleHelper::requirePermission('manage_settings');
        
        try {
            $stmt = $this->leavePolicy->readAll();
            $num = $stmt->rowCount();
            
            // Set page data
            $page_title = 'จัดการนโยบายการลา';
            
            // Include the view
            require_once __DIR__ . '/../views/settings/leave_policies/index.php';
            
        } catch (Exception $e) {
            error_log("Error in LeavePolicyController::index(): " . $e->getMessage());
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
        
        try {
            // Get available leave types (that don't have policies yet)
            $available_leave_types = $this->leavePolicy->getAvailableLeaveTypes();
            
            if (empty($available_leave_types)) {
                $_SESSION['error_message'] = 'ไม่มีประเภทการลาที่สามารถเพิ่มนโยบายได้ กรุณาเพิ่มประเภทการลาก่อน';
                header('Location: ' . BASE_URL . '/leavepolicies');
                exit();
            }
            
            $page_title = 'เพิ่มนโยบายการลาใหม่';
            $leave_types = $available_leave_types;
            
            require_once __DIR__ . '/../views/settings/leave_policies/form.php';
            
        } catch (Exception $e) {
            error_log("Error in LeavePolicyController::create(): " . $e->getMessage());
            $_SESSION['error_message'] = 'เกิดข้อผิดพลาดในการโหลดหน้าเพิ่มข้อมูล';
            header('Location: ' . BASE_URL . '/leavepolicies');
            exit();
        }
    }
    
    /**
     * Store new leave policy
     */
    public function store() 
    {
        RoleHelper::requirePermission('manage_settings');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/leavepolicies');
            exit();
        }
        
        try {
            // Set leave policy properties
            $this->leavePolicy->leave_type_id = (int)($_POST['leave_type_id'] ?? 0);
            $this->leavePolicy->days_allowed_per_year = (int)($_POST['days_allowed_per_year'] ?? 0);
            $this->leavePolicy->is_unlimited = isset($_POST['is_unlimited']) ? 1 : 0;
            $this->leavePolicy->can_be_carried_over = isset($_POST['can_be_carried_over']) ? 1 : 0;
            $this->leavePolicy->max_carry_over_days = (int)($_POST['max_carry_over_days'] ?? 0);
            $this->leavePolicy->min_notice_days = (int)($_POST['min_notice_days'] ?? 0);
            $this->leavePolicy->max_consecutive_days = (int)($_POST['max_consecutive_days'] ?? 0);
            $this->leavePolicy->requires_approval = isset($_POST['requires_approval']) ? 1 : 0;
            $this->leavePolicy->description = trim($_POST['description'] ?? '');
            
            // If unlimited, set days to 0
            if ($this->leavePolicy->is_unlimited) {
                $this->leavePolicy->days_allowed_per_year = 0;
            }
            
            // If no carry over, set max carry over days to 0
            if (!$this->leavePolicy->can_be_carried_over) {
                $this->leavePolicy->max_carry_over_days = 0;
            }
            
            // Validate data
            $errors = $this->leavePolicy->validate();
            
            if (!empty($errors)) {
                $_SESSION['error_message'] = implode('<br>', $errors);
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . '/leavepolicies/create');
                exit();
            }
            
            // Create leave policy
            if ($this->leavePolicy->create()) {
                $_SESSION['success_message'] = 'เพิ่มนโยบายการลาเรียบร้อยแล้ว';
                unset($_SESSION['form_data']);
            } else {
                $_SESSION['error_message'] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . '/leavepolicies/create');
                exit();
            }
            
        } catch (Exception $e) {
            error_log("Error in LeavePolicyController::store(): " . $e->getMessage());
            $_SESSION['error_message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/leavepolicies/create');
            exit();
        }
        
        header('Location: ' . BASE_URL . '/leavepolicies');
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
            $_SESSION['error_message'] = 'รหัสนโยบายการลาไม่ถูกต้อง';
            header('Location: ' . BASE_URL . '/leavepolicies');
            exit();
        }
        
        try {
            if (!$this->leavePolicy->readOne($id)) {
                $_SESSION['error_message'] = 'ไม่พบนโยบายการลาที่ต้องการแก้ไข';
                header('Location: ' . BASE_URL . '/leavepolicies');
                exit();
            }
            
            // Get all leave types for edit form
            $leave_types = $this->leavePolicy->getAllLeaveTypes();
            
            $page_title = 'แก้ไขนโยบายการลา';
            $leave_policy = $this->leavePolicy;
            
            require_once __DIR__ . '/../views/settings/leave_policies/form.php';
            
        } catch (Exception $e) {
            error_log("Error in LeavePolicyController::edit(): " . $e->getMessage());
            $_SESSION['error_message'] = 'เกิดข้อผิดพลาดในการโหลดข้อมูล';
            header('Location: ' . BASE_URL . '/leavepolicies');
            exit();
        }
    }
    
    /**
     * Update leave policy
     * @param int $id
     */
    public function update($id = null) 
    {
        RoleHelper::requirePermission('manage_settings');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/leavepolicies');
            exit();
        }
        
        // Handle both single update and bulk update
        if ($id !== null) {
            // Single update
            $this->updateSingle($id);
        } else {
            // Bulk update (original functionality)
            $this->updateBulk();
        }
    }
    
    /**
     * Update single leave policy
     * @param int $id
     */
    private function updateSingle($id) 
    {
        if (empty($id) || !is_numeric($id)) {
            $_SESSION['error_message'] = 'รหัสนโยบายการลาไม่ถูกต้อง';
            header('Location: ' . BASE_URL . '/leavepolicies');
            exit();
        }
        
        try {
            // Load current data
            if (!$this->leavePolicy->readOne($id)) {
                $_SESSION['error_message'] = 'ไม่พบนโยบายการลาที่ต้องการแก้ไข';
                header('Location: ' . BASE_URL . '/leavepolicies');
                exit();
            }
            
            // Update properties
            $this->leavePolicy->leave_type_id = (int)($_POST['leave_type_id'] ?? 0);
            $this->leavePolicy->days_allowed_per_year = (int)($_POST['days_allowed_per_year'] ?? 0);
            $this->leavePolicy->is_unlimited = isset($_POST['is_unlimited']) ? 1 : 0;
            $this->leavePolicy->can_be_carried_over = isset($_POST['can_be_carried_over']) ? 1 : 0;
            $this->leavePolicy->max_carry_over_days = (int)($_POST['max_carry_over_days'] ?? 0);
            $this->leavePolicy->min_notice_days = (int)($_POST['min_notice_days'] ?? 0);
            $this->leavePolicy->max_consecutive_days = (int)($_POST['max_consecutive_days'] ?? 0);
            $this->leavePolicy->requires_approval = isset($_POST['requires_approval']) ? 1 : 0;
            $this->leavePolicy->description = trim($_POST['description'] ?? '');
            
            // If unlimited, set days to 0
            if ($this->leavePolicy->is_unlimited) {
                $this->leavePolicy->days_allowed_per_year = 0;
            }
            
            // If no carry over, set max carry over days to 0
            if (!$this->leavePolicy->can_be_carried_over) {
                $this->leavePolicy->max_carry_over_days = 0;
            }
            
            // Validate data
            $errors = $this->leavePolicy->validate();
            
            if (!empty($errors)) {
                $_SESSION['error_message'] = implode('<br>', $errors);
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . '/leavepolicies/edit/' . $id);
                exit();
            }
            
            // Update leave policy
            if ($this->leavePolicy->update()) {
                $_SESSION['success_message'] = 'แก้ไขนโยบายการลาเรียบร้อยแล้ว';
                unset($_SESSION['form_data']);
            } else {
                $_SESSION['error_message'] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . BASE_URL . '/leavepolicies/edit/' . $id);
                exit();
            }
            
        } catch (Exception $e) {
            error_log("Error in LeavePolicyController::updateSingle(): " . $e->getMessage());
            $_SESSION['error_message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/leavepolicies/edit/' . $id);
            exit();
        }
        
        header('Location: ' . BASE_URL . '/leavepolicies');
        exit();
    }
    
    /**
     * Bulk update leave policies (original functionality)
     */
    private function updateBulk() 
    {
        try {
            if (isset($_POST['policies']) && is_array($_POST['policies'])) {
                $policies = $_POST['policies'];
                foreach ($policies as $leave_type_id => $data) {
                    // แปลงค่า checkbox เป็น 0 หรือ 1
                    $data['is_unlimited'] = isset($data['is_unlimited']) ? 1 : 0;
                    $data['can_be_carried_over'] = isset($data['can_be_carried_over']) ? 1 : 0;

                    $this->leavePolicy->updateByLeaveType($leave_type_id, $data);
                }
            }
            
            $_SESSION['success_message'] = "บันทึกนโยบายการลาสำเร็จ";
            
        } catch (Exception $e) {
            error_log("Error in LeavePolicyController::updateBulk(): " . $e->getMessage());
            $_SESSION['error_message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '/leavepolicies');
        exit();
    }
    
    /**
     * Delete leave policy
     * @param int $id
     */
    public function destroy($id) 
    {
        RoleHelper::requirePermission('manage_settings');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/leavepolicies');
            exit();
        }
        
        if (empty($id) || !is_numeric($id)) {
            $_SESSION['error_message'] = 'รหัสนโยบายการลาไม่ถูกต้อง';
            header('Location: ' . BASE_URL . '/leavepolicies');
            exit();
        }
        
        try {
            // Load leave policy data for confirmation
            if (!$this->leavePolicy->readOne($id)) {
                $_SESSION['error_message'] = 'ไม่พบนโยบายการลาที่ต้องการลบ';
                header('Location: ' . BASE_URL . '/leavepolicies');
                exit();
            }
            
            $policy_name = $this->leavePolicy->leave_type_name ?? 'นโยบายการลา';
            
            // Delete leave policy
            if ($this->leavePolicy->delete()) {
                $_SESSION['success_message'] = 'ลบนโยบายการลา "' . $policy_name . '" เรียบร้อยแล้ว';
            } else {
                $_SESSION['error_message'] = 'เกิดข้อผิดพลาดในการลบข้อมูล';
            }
            
        } catch (Exception $e) {
            error_log("Error in LeavePolicyController::destroy(): " . $e->getMessage());
            $_SESSION['error_message'] = $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '/leavepolicies');
        exit();
    }
    
    /**
     * Get statistics dashboard
     */
    public function stats() 
    {
        header('Content-Type: application/json');
        
        try {
            $stats = $this->leavePolicy->getStats();
            
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (Exception $e) {
            error_log("Error in LeavePolicyController::stats(): " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการดึงสถิติ'
            ]);
        }
    }
    
    /**
     * Export leave policies data
     */
    public function export() 
    {
        RoleHelper::requirePermission('export_data');
        
        try {
            $data = $this->leavePolicy->exportData();
            
            // Set headers for file download
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="leave_policies_' . date('Y-m-d_H-i-s') . '.csv"');
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
            error_log("Error in LeavePolicyController::export(): " . $e->getMessage());
            $_SESSION['error_message'] = 'เกิดข้อผิดพลาดในการส่งออกข้อมูล';
            header('Location: ' . BASE_URL . '/leavepolicies');
            exit();
        }
    }
    
    /**
     * Get leave policies for API/AJAX calls
     */
    public function apiData() 
    {
        header('Content-Type: application/json');
        
        try {
            $stmt = $this->leavePolicy->readAll();
            $policies = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $policies[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $policies
            ]);
            
        } catch (Exception $e) {
            error_log("Error in LeavePolicyController::apiData(): " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล'
            ]);
        }
    }
}
?>