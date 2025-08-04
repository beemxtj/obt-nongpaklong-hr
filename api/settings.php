<?php
// api/settings.php - API Endpoints for Settings Management

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Setting.php';
require_once __DIR__ . '/../models/WorkShift.php';
require_once __DIR__ . '/../helpers/RoleHelper.php';
require_once __DIR__ . '/../helpers/ValidationHelper.php';

class SettingsAPI {
    private $db;
    private $setting;
    private $workShift;
    private $permissions;

    public function __construct() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $this->sendError('Unauthorized', 401);
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->setting = new Setting($this->db);
        $this->workShift = new WorkShift($this->db);
        $this->permissions = RoleHelper::getUserPermissions();

        $this->handleRequest();
    }

    private function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_replace('/api/settings', '', $path);
        $segments = array_filter(explode('/', $path));

        try {
            switch ($method) {
                case 'GET':
                    $this->handleGet($segments);
                    break;
                case 'POST':
                    $this->handlePost($segments);
                    break;
                case 'PUT':
                    $this->handlePut($segments);
                    break;
                case 'DELETE':
                    $this->handleDelete($segments);
                    break;
                default:
                    $this->sendError('Method not allowed', 405);
            }
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    private function handleGet($segments) {
        if (empty($segments)) {
            // GET /api/settings - Get all settings
            $this->getAllSettings();
        } elseif ($segments[0] === 'shifts') {
            if (isset($segments[1])) {
                // GET /api/settings/shifts/{id} - Get specific shift
                $this->getShift($segments[1]);
            } else {
                // GET /api/settings/shifts - Get all shifts
                $this->getAllShifts();
            }
        } elseif ($segments[0] === 'shift-employees') {
            // GET /api/settings/shift-employees/{shift_id} - Get employees in shift
            $this->getShiftEmployees($segments[1] ?? null);
        } elseif ($segments[0] === 'permissions') {
            // GET /api/settings/permissions - Get user permissions
            $this->getUserPermissions();
        } elseif ($segments[0] === 'validation') {
            // GET /api/settings/validation/{key} - Validate setting value
            $this->validateSetting($segments[1] ?? null);
        } else {
            // GET /api/settings/{key} - Get specific setting
            $this->getSetting($segments[0]);
        }
    }

    private function handlePost($segments) {
        if (empty($segments)) {
            // POST /api/settings - Create/Update settings
            $this->updateSettings();
        } elseif ($segments[0] === 'shifts') {
            // POST /api/settings/shifts - Create new shift
            $this->createShift();
        } elseif ($segments[0] === 'assign-shift') {
            // POST /api/settings/assign-shift - Assign employees to shift
            $this->assignShift();
        } elseif ($segments[0] === 'validate') {
            // POST /api/settings/validate - Validate multiple settings
            $this->validateSettings();
        } elseif ($segments[0] === 'backup') {
            // POST /api/settings/backup - Create settings backup
            $this->createBackup();
        } elseif ($segments[0] === 'restore') {
            // POST /api/settings/restore - Restore from backup
            $this->restoreBackup();
        } elseif ($segments[0] === 'preview-theme') {
            // POST /api/settings/preview-theme - Preview theme colors
            $this->previewTheme();
        } else {
            $this->sendError('Endpoint not found', 404);
        }
    }

    private function handlePut($segments) {
        if (isset($segments[0]) && $segments[0] === 'shifts' && isset($segments[1])) {
            // PUT /api/settings/shifts/{id} - Update shift
            $this->updateShift($segments[1]);
        } elseif (isset($segments[0])) {
            // PUT /api/settings/{key} - Update single setting
            $this->updateSingleSetting($segments[0]);
        } else {
            $this->sendError('Endpoint not found', 404);
        }
    }

    private function handleDelete($segments) {
        if (isset($segments[0]) && $segments[0] === 'shifts' && isset($segments[1])) {
            // DELETE /api/settings/shifts/{id} - Delete shift
            $this->deleteShift($segments[1]);
        } elseif (isset($segments[0])) {
            // DELETE /api/settings/{key} - Delete setting
            $this->deleteSetting($segments[0]);
        } else {
            $this->sendError('Endpoint not found', 404);
        }
    }

    // Settings Methods
    private function getAllSettings() {
        $settings = $this->setting->getAllSettings();
        $this->sendSuccess($settings);
    }

    private function getSetting($key) {
        $setting = $this->setting->getSettingDetails($key);
        if ($setting) {
            $this->sendSuccess($setting);
        } else {
            $this->sendError('Setting not found', 404);
        }
    }

    private function updateSettings() {
        if (!$this->permissions['can_view_reports']) {
            $this->sendError('Insufficient permissions', 403);
        }

        $data = $this->getJsonInput();
        
        // Validate input
        $validation = ValidationHelper::validateSettings($data);
        if (!$validation['valid']) {
            $this->sendError($validation['message'], 400);
        }

        // Filter settings by permissions
        $allowedSettings = $this->filterSettingsByPermissions($data);

        if ($this->setting->saveSettings($allowedSettings)) {
            $this->sendSuccess(['message' => 'Settings updated successfully']);
        } else {
            $this->sendError('Failed to update settings', 500);
        }
    }

    private function updateSingleSetting($key) {
        $data = $this->getJsonInput();
        
        if (!isset($data['value'])) {
            $this->sendError('Value is required', 400);
        }

        if (!$this->canUpdateSetting($key)) {
            $this->sendError('Insufficient permissions for this setting', 403);
        }

        // Validate the setting value
        $validation = ValidationHelper::validateSettingValue($key, $data['value']);
        if (!$validation['valid']) {
            $this->sendError($validation['message'], 400);
        }

        $description = $data['description'] ?? null;
        $type = $data['type'] ?? 'text';

        if ($this->setting->saveSetting($key, $data['value'], $description, $type)) {
            $this->sendSuccess(['message' => 'Setting updated successfully']);
        } else {
            $this->sendError('Failed to update setting', 500);
        }
    }

    private function deleteSetting($key) {
        if (!$this->permissions['can_manage_settings']) {
            $this->sendError('Insufficient permissions', 403);
        }

        if ($this->setting->deleteSetting($key)) {
            $this->sendSuccess(['message' => 'Setting deleted successfully']);
        } else {
            $this->sendError('Failed to delete setting', 500);
        }
    }

    private function validateSetting($key) {
        $value = $_GET['value'] ?? '';
        $validation = ValidationHelper::validateSettingValue($key, $value);
        $this->sendSuccess($validation);
    }

    private function validateSettings() {
        $data = $this->getJsonInput();
        $results = [];

        foreach ($data as $key => $value) {
            $results[$key] = ValidationHelper::validateSettingValue($key, $value);
        }

        $this->sendSuccess($results);
    }

    // Work Shift Methods
    private function getAllShifts() {
        if (!$this->permissions['can_view_reports']) {
            $this->sendError('Insufficient permissions', 403);
        }

        $shifts = $this->workShift->getAllShifts();
        $this->sendSuccess($shifts);
    }

    private function getShift($id) {
        if (!$this->permissions['can_view_reports']) {
            $this->sendError('Insufficient permissions', 403);
        }

        $shift = $this->workShift->getShiftById($id);
        if ($shift) {
            $this->sendSuccess($shift);
        } else {
            $this->sendError('Shift not found', 404);
        }
    }

    private function createShift() {
        if (!$this->permissions['can_view_reports']) {
            $this->sendError('Insufficient permissions', 403);
        }

        $data = $this->getJsonInput();
        
        // Validate required fields
        $required = ['shift_name', 'start_time', 'end_time', 'work_days'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->sendError("Field {$field} is required", 400);
            }
        }

        // Validate shift data
        $validation = ValidationHelper::validateShiftData($data);
        if (!$validation['valid']) {
            $this->sendError($validation['message'], 400);
        }

        // Process work_days if it's an array
        if (is_array($data['work_days'])) {
            $data['work_days'] = implode(',', $data['work_days']);
        }

        if ($this->workShift->createShift($data)) {
            $this->sendSuccess(['message' => 'Shift created successfully']);
        } else {
            $this->sendError('Failed to create shift', 500);
        }
    }

    private function updateShift($id) {
        if (!$this->permissions['can_view_reports']) {
            $this->sendError('Insufficient permissions', 403);
        }

        $data = $this->getJsonInput();
        
        // Validate shift exists
        $existingShift = $this->workShift->getShiftById($id);
        if (!$existingShift) {
            $this->sendError('Shift not found', 404);
        }

        // Validate shift data
        $validation = ValidationHelper::validateShiftData($data);
        if (!$validation['valid']) {
            $this->sendError($validation['message'], 400);
        }

        // Process work_days if it's an array
        if (is_array($data['work_days'])) {
            $data['work_days'] = implode(',', $data['work_days']);
        }

        if ($this->workShift->updateShift($id, $data)) {
            $this->sendSuccess(['message' => 'Shift updated successfully']);
        } else {
            $this->sendError('Failed to update shift', 500);
        }
    }

    private function deleteShift($id) {
        if (!$this->permissions['can_manage_employees']) {
            $this->sendError('Insufficient permissions', 403);
        }

        // Check if shift exists
        $shift = $this->workShift->getShiftById($id);
        if (!$shift) {
            $this->sendError('Shift not found', 404);
        }

        // Check if shift has assigned employees
        $employees = $this->workShift->getEmployeesInShift($id);
        if (!empty($employees)) {
            $this->sendError('Cannot delete shift with assigned employees', 400);
        }

        if ($this->workShift->deleteShift($id)) {
            $this->sendSuccess(['message' => 'Shift deleted successfully']);
        } else {
            $this->sendError('Failed to delete shift', 500);
        }
    }

    private function getShiftEmployees($shiftId) {
        if (!$this->permissions['can_view_reports']) {
            $this->sendError('Insufficient permissions', 403);
        }

        if (!$shiftId) {
            $this->sendError('Shift ID is required', 400);
        }

        $employees = $this->workShift->getEmployeesInShift($shiftId);
        $this->sendSuccess(['employees' => $employees]);
    }

    private function assignShift() {
        if (!$this->permissions['can_view_reports']) {
            $this->sendError('Insufficient permissions', 403);
        }

        $data = $this->getJsonInput();
        
        $required = ['employee_ids', 'shift_id', 'start_date'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                $this->sendError("Field {$field} is required", 400);
            }
        }

        $successCount = 0;
        $errors = [];

        foreach ($data['employee_ids'] as $employeeId) {
            $result = $this->workShift->assignEmployeeToShift(
                $employeeId,
                $data['shift_id'],
                $data['start_date'],
                $data['end_date'] ?? null
            );

            if ($result) {
                $successCount++;
            } else {
                $errors[] = "Failed to assign employee ID: {$employeeId}";
            }
        }

        if ($successCount > 0) {
            $message = "Successfully assigned {$successCount} employee(s)";
            if (!empty($errors)) {
                $message .= ". Errors: " . implode(', ', $errors);
            }
            $this->sendSuccess(['message' => $message, 'success_count' => $successCount]);
        } else {
            $this->sendError('Failed to assign any employees', 500);
        }
    }

    // Theme and Preview Methods
    private function previewTheme() {
        $data = $this->getJsonInput();
        
        $colors = [
            'primary_color' => $data['primary_color'] ?? '#4f46e5',
            'secondary_color' => $data['secondary_color'] ?? '#7c3aed',
            'accent_color' => $data['accent_color'] ?? '#06b6d4',
            'sidebar_bg_color' => $data['sidebar_bg_color'] ?? '#1f2937',
            'header_bg_color' => $data['header_bg_color'] ?? '#ffffff'
        ];

        // Validate colors
        foreach ($colors as $key => $color) {
            if (!ValidationHelper::isValidColor($color)) {
                $this->sendError("Invalid color format for {$key}", 400);
            }
        }

        $css = $this->generateThemeCSS($colors);

        $this->sendSuccess([
            'success' => true,
            'colors' => $colors,
            'css' => $css
        ]);
    }

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
        .tab-button.active {
            border-color: {$colors['primary_color']};
            color: {$colors['primary_color']};
            background-color: " . $this->hexToRgba($colors['primary_color'], 0.1) . ";
        }
        ";
    }

    // Backup and Restore Methods
    private function createBackup() {
        if (!$this->permissions['can_export_data']) {
            $this->sendError('Insufficient permissions', 403);
        }

        $data = $this->getJsonInput();
        $backupName = $data['name'] ?? 'Settings Backup ' . date('Y-m-d H:i:s');
        $backupType = $data['type'] ?? 'manual';

        try {
            $settings = $this->setting->getAllSettings();
            $backupData = [
                'version' => '1.0',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $_SESSION['user_id'],
                'settings' => $settings
            ];

            // Save to database
            $query = "INSERT INTO settings_backup (backup_name, backup_data, backup_type, created_by, file_size, settings_count) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            
            $jsonData = json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $fileSize = strlen($jsonData);
            $settingsCount = count($settings);
            
            $stmt->execute([
                $backupName,
                $jsonData,
                $backupType,
                $_SESSION['user_id'],
                $fileSize,
                $settingsCount
            ]);

            $backupId = $this->db->lastInsertId();

            $this->sendSuccess([
                'message' => 'Backup created successfully',
                'backup_id' => $backupId,
                'backup_name' => $backupName
            ]);

        } catch (Exception $e) {
            $this->sendError('Failed to create backup: ' . $e->getMessage(), 500);
        }
    }

    private function restoreBackup() {
        if (!$this->permissions['can_manage_settings']) {
            $this->sendError('Insufficient permissions', 403);
        }

        $data = $this->getJsonInput();
        
        if (!isset($data['backup_id'])) {
            $this->sendError('Backup ID is required', 400);
        }

        try {
            // Get backup data
            $query = "SELECT backup_data FROM settings_backup WHERE id = ? AND created_by = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$data['backup_id'], $_SESSION['user_id']]);
            
            $backup = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$backup) {
                $this->sendError('Backup not found', 404);
            }

            $backupData = json_decode($backup['backup_data'], true);
            if (!$backupData || !isset($backupData['settings'])) {
                $this->sendError('Invalid backup data', 400);
            }

            // Restore settings
            $settingsToRestore = [];
            foreach ($backupData['settings'] as $key => $setting) {
                if ($this->canUpdateSetting($key)) {
                    $settingsToRestore[$key] = is_array($setting) ? $setting['value'] : $setting;
                }
            }

            if ($this->setting->saveSettings($settingsToRestore)) {
                $this->sendSuccess([
                    'message' => 'Settings restored successfully',
                    'restored_count' => count($settingsToRestore)
                ]);
            } else {
                $this->sendError('Failed to restore settings', 500);
            }

        } catch (Exception $e) {
            $this->sendError('Failed to restore backup: ' . $e->getMessage(), 500);
        }
    }

    // Permission and Security Methods
    private function getUserPermissions() {
        $this->sendSuccess($this->permissions);
    }

    private function filterSettingsByPermissions($settings) {
        $allowed = [];
        foreach ($settings as $key => $value) {
            if ($this->canUpdateSetting($key)) {
                $allowed[$key] = $value;
            }
        }
        return $allowed;
    }

    private function canUpdateSetting($settingKey) {
        // Admin can update everything
        if ($this->permissions['can_manage_settings']) {
            return true;
        }

        // HR can update most settings except system and security
        if ($this->permissions['can_manage_employees']) {
            $restrictedForHR = [
                'system_timezone', 'backup_frequency', 'auto_logout_time',
                'session_timeout', 'max_login_attempts', 'password_min_length',
                'password_require_special', 'two_factor_auth', 'ip_whitelist',
                'debug_mode', 'system_maintenance_mode'
            ];
            return !in_array($settingKey, $restrictedForHR);
        }

        // Manager can update work time and notification settings only
        if ($this->permissions['can_view_reports']) {
            $allowedForManager = [
                'work_start_time', 'work_end_time', 'grace_period_minutes',
                'ot_start_time', 'break_start_time', 'break_end_time',
                'lunch_break_duration', 'enable_email_notifications',
                'notification_sound', 'late_arrival_notification',
                'leave_request_notification', 'overtime_notification'
            ];
            return in_array($settingKey, $allowedForManager);
        }

        return false;
    }

    // Utility Methods
    private function getJsonInput() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendError('Invalid JSON input', 400);
        }
        
        return $data;
    }

    private function hexToRgba($hex, $alpha = 1) {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) === 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) .
                   str_repeat(substr($hex, 1, 1), 2) .
                   str_repeat(substr($hex, 2, 1), 2);
        }
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }

    private function sendSuccess($data) {
        echo json_encode([
            'success' => true,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit();
    }

    private function sendError($message, $code = 400) {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'error' => $message,
            'code' => $code,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit();
    }
}

// Initialize API
new SettingsAPI();
?>

<?php
// helpers/ValidationHelper.php - Validation Helper for Settings

class ValidationHelper {
    
    /**
     * Validate multiple settings at once
     */
    public static function validateSettings($settings) {
        $errors = [];
        
        foreach ($settings as $key => $value) {
            $validation = self::validateSettingValue($key, $value);
            if (!$validation['valid']) {
                $errors[$key] = $validation['message'];
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'message' => empty($errors) ? 'All settings are valid' : 'Some settings have validation errors'
        ];
    }
    
    /**
     * Validate individual setting value
     */
    public static function validateSettingValue($key, $value) {
        $rules = self::getValidationRules();
        
        if (!isset($rules[$key])) {
            return ['valid' => true, 'message' => 'No validation rules defined'];
        }
        
        $rule = $rules[$key];
        
        // Required validation
        if (isset($rule['required']) && $rule['required'] && empty($value)) {
            return ['valid' => false, 'message' => 'This field is required'];
        }
        
        // Skip other validations if value is empty and not required
        if (empty($value) && (!isset($rule['required']) || !$rule['required'])) {
            return ['valid' => true, 'message' => 'Valid'];
        }
        
        // Type-specific validation
        switch ($rule['type']) {
            case 'email':
                return self::validateEmail($value);
            case 'url':
                return self::validateUrl($value);
            case 'color':
                return self::validateColor($value);
            case 'time':
                return self::validateTime($value);
            case 'number':
                return self::validateNumber($value, $rule);
            case 'boolean':
                return self::validateBoolean($value);
            case 'select':
                return self::validateSelect($value, $rule);
            case 'phone':
                return self::validatePhone($value);
            default:
                return self::validateText($value, $rule);
        }
    }
    
    /**
     * Validate work shift data
     */
    public static function validateShiftData($data) {
        $errors = [];
        
        // Required fields
        $required = ['shift_name', 'start_time', 'end_time'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = "Field {$field} is required";
            }
        }
        
        if (!empty($errors)) {
            return ['valid' => false, 'message' => implode(', ', $errors)];
        }
        
        // Validate shift name
        if (strlen($data['shift_name']) > 100) {
            $errors[] = 'Shift name is too long (max 100 characters)';
        }
        
        // Validate times
        if (!self::validateTime($data['start_time'])['valid']) {
            $errors[] = 'Invalid start time format';
        }
        
        if (!self::validateTime($data['end_time'])['valid']) {
            $errors[] = 'Invalid end time format';
        }
        
        // Validate break times if provided
        if (!empty($data['break_start']) && !self::validateTime($data['break_start'])['valid']) {
            $errors[] = 'Invalid break start time format';
        }
        
        if (!empty($data['break_end']) && !self::validateTime($data['break_end'])['valid']) {
            $errors[] = 'Invalid break end time format';
        }
        
        // Validate work days
        if (isset($data['work_days'])) {
            $workDays = is_array($data['work_days']) ? $data['work_days'] : explode(',', $data['work_days']);
            foreach ($workDays as $day) {
                if (!in_array(trim($day), ['1', '2', '3', '4', '5', '6', '7'])) {
                    $errors[] = 'Invalid work day value';
                    break;
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'message' => empty($errors) ? 'Valid shift data' : implode(', ', $errors)
        ];
    }
    
    /**
     * Validation rule definitions
     */
    private static function getValidationRules() {
        return [
            // Organization
            'org_name' => ['type' => 'text', 'required' => true, 'max_length' => 255],
            'org_email' => ['type' => 'email'],
            'org_website' => ['type' => 'url'],
            'org_phone' => ['type' => 'phone'],
            
            // Work Time
            'work_start_time' => ['type' => 'time', 'required' => true],
            'work_end_time' => ['type' => 'time', 'required' => true],
            'grace_period_minutes' => ['type' => 'number', 'min' => 0, 'max' => 60],
            'ot_start_time' => ['type' => 'time'],
            'break_start_time' => ['type' => 'time'],
            'break_end_time' => ['type' => 'time'],
            'lunch_break_duration' => ['type' => 'number', 'min' => 0, 'max' => 240],
            'work_days_per_week' => ['type' => 'number', 'min' => 1, 'max' => 7],
            'weekend_ot_rate' => ['type' => 'number', 'min' => 1, 'max' => 5, 'decimal' => true],
            'holiday_ot_rate' => ['type' => 'number', 'min' => 1, 'max' => 5, 'decimal' => true],
            'night_shift_allowance' => ['type' => 'number', 'min' => 0],
            
            // Theme
            'primary_color' => ['type' => 'color', 'required' => true],
            'secondary_color' => ['type' => 'color', 'required' => true],
            'accent_color' => ['type' => 'color', 'required' => true],
            'sidebar_bg_color' => ['type' => 'color', 'required' => true],
            'header_bg_color' => ['type' => 'color', 'required' => true],
            'logo_position' => ['type' => 'select', 'options' => ['left', 'center', 'right']],
            'theme_mode' => ['type' => 'select', 'options' => ['light', 'dark', 'auto']],
            
            // System
            'system_timezone' => ['type' => 'select', 'required' => true],
            'date_format' => ['type' => 'select', 'required' => true],
            'time_format' => ['type' => 'select', 'required' => true],
            'language' => ['type' => 'select', 'required' => true],
            'currency' => ['type' => 'select', 'required' => true],
            'decimal_places' => ['type' => 'number', 'min' => 0, 'max' => 4],
            'backup_frequency' => ['type' => 'select', 'options' => ['daily', 'weekly', 'monthly']],
            'auto_logout_time' => ['type' => 'number', 'min' => 5, 'max' => 1440],
            
            // Notifications
            'enable_email_notifications' => ['type' => 'boolean'],
            'enable_sms_notifications' => ['type' => 'boolean'],
            'notification_sound' => ['type' => 'boolean'],
            'late_arrival_notification' => ['type' => 'boolean'],
            'leave_request_notification' => ['type' => 'boolean'],
            'overtime_notification' => ['type' => 'boolean'],
            'birthday_notification' => ['type' => 'boolean'],
            'contract_expiry_notification' => ['type' => 'boolean'],
            'email_server_host' => ['type' => 'text'],
            'email_server_port' => ['type' => 'number', 'min' => 1, 'max' => 65535],
            'email_server_username' => ['type' => 'email'],
            
            // Security
            'session_timeout' => ['type' => 'number', 'min' => 300, 'max' => 86400],
            'max_login_attempts' => ['type' => 'number', 'min' => 3, 'max' => 20],
            'password_min_length' => ['type' => 'number', 'min' => 6, 'max' => 50],
            'password_require_special' => ['type' => 'boolean'],
            'password_require_uppercase' => ['type' => 'boolean'],
            'password_require_numbers' => ['type' => 'boolean'],
            'two_factor_auth' => ['type' => 'boolean'],
            'failed_login_lockout_time' => ['type' => 'number', 'min' => 60, 'max' => 3600],
            'password_expiry_days' => ['type' => 'number', 'min' => 30, 'max' => 365],
            'force_password_change' => ['type' => 'boolean']
        ];
    }
    
    /**
     * Specific validation methods
     */
    private static function validateEmail($value) {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => true, 'message' => 'Valid email'];
        }
        return ['valid' => false, 'message' => 'Invalid email format'];
    }
    
    private static function validateUrl($value) {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return ['valid' => true, 'message' => 'Valid URL'];
        }
        return ['valid' => false, 'message' => 'Invalid URL format'];
    }
    
    public static function validateColor($value) {
        if (preg_match('/^#[0-9A-F]{6}$/i', $value)) {
            return ['valid' => true, 'message' => 'Valid color'];
        }
        return ['valid' => false, 'message' => 'Invalid color format (use #RRGGBB)'];
    }
    
    public static function isValidColor($value) {
        return preg_match('/^#[0-9A-F]{6}$/i', $value);
    }
    
    private static function validateTime($value) {
        if (preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $value)) {
            return ['valid' => true, 'message' => 'Valid time'];
        }
        return ['valid' => false, 'message' => 'Invalid time format (use HH:MM)'];
    }
    
    private static function validateNumber($value, $rule) {
        if (!is_numeric($value)) {
            return ['valid' => false, 'message' => 'Must be a number'];
        }
        
        $numValue = floatval($value);
        
        if (isset($rule['min']) && $numValue < $rule['min']) {
            return ['valid' => false, 'message' => "Must be at least {$rule['min']}"];
        }
        
        if (isset($rule['max']) && $numValue > $rule['max']) {
            return ['valid' => false, 'message' => "Must not exceed {$rule['max']}"];
        }
        
        // Check for decimal validation
        if (!isset($rule['decimal']) || !$rule['decimal']) {
            if ($numValue != intval($numValue)) {
                return ['valid' => false, 'message' => 'Must be a whole number'];
            }
        }
        
        return ['valid' => true, 'message' => 'Valid number'];
    }
    
    private static function validateBoolean($value) {
        if (in_array($value, [0, 1, '0', '1', true, false, 'true', 'false'], true)) {
            return ['valid' => true, 'message' => 'Valid boolean'];
        }
        return ['valid' => false, 'message' => 'Must be true or false'];
    }
    
    private static function validateSelect($value, $rule) {
        if (isset($rule['options']) && in_array($value, $rule['options'])) {
            return ['valid' => true, 'message' => 'Valid selection'];
        }
        return ['valid' => false, 'message' => 'Invalid selection'];
    }
    
    private static function validatePhone($value) {
        // Simple phone validation - can be enhanced based on requirements
        if (preg_match('/^[0-9\-\+\(\)\s]+$/', $value)) {
            return ['valid' => true, 'message' => 'Valid phone'];
        }
        return ['valid' => false, 'message' => 'Invalid phone format'];
    }
    
    private static function validateText($value, $rule) {
        if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
            return ['valid' => false, 'message' => "Must not exceed {$rule['max_length']} characters"];
        }
        
        if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
            return ['valid' => false, 'message' => "Must be at least {$rule['min_length']} characters"];
        }
        
        return ['valid' => true, 'message' => 'Valid text'];
    }
}
?>