<?php
<<<<<<< HEAD
// models/Setting.php - Enhanced Settings Model with Role-Based Access
=======
// models/Setting.php
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335

class Setting {
    private $conn;
    private $table_name = "system_settings";

    public function __construct($db) {
        $this->conn = $db;
    }

<<<<<<< HEAD
    /**
     * Get all settings with full details
     * @return array
     */
    public function getAllSettings() {
        $settings = [];
        $query = "SELECT setting_key, setting_value, setting_description, setting_type, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  ORDER BY setting_key";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $settings[$row['setting_key']] = [
                    'value' => $row['setting_value'],
                    'description' => $row['setting_description'],
                    'type' => $row['setting_type'],
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at']
                ];
            }
        } catch (PDOException $e) {
            error_log("Error getting all settings: " . $e->getMessage());
        }
        
        return $settings;
    }

    /**
     * Get simple settings (key-value pairs only)
     * @return array
     */
    public function getSimpleSettings() {
        $settings = [];
        $query = "SELECT setting_key, setting_value FROM " . $this->table_name;
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (PDOException $e) {
            error_log("Error getting simple settings: " . $e->getMessage());
        }
        
        return $settings;
    }

    /**
     * Get single setting value
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSettingValue($key, $default = null) {
        $query = "SELECT setting_value FROM " . $this->table_name . " WHERE setting_key = ? LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $key);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['setting_value'];
            }
        } catch (PDOException $e) {
            error_log("Error getting setting value: " . $e->getMessage());
        }
        
        return $default;
    }

    /**
     * Get setting with full details
     * @param string $key
     * @return array|null
     */
    public function getSettingDetails($key) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE setting_key = ? LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $key);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log("Error getting setting details: " . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Get settings by type
     * @param string $type
     * @return array
     */
    public function getSettingsByType($type) {
        $settings = [];
        $query = "SELECT setting_key, setting_value, setting_description, setting_type 
                  FROM " . $this->table_name . " 
                  WHERE setting_type = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $type);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $settings[$row['setting_key']] = [
                    'value' => $row['setting_value'],
                    'description' => $row['setting_description'],
                    'type' => $row['setting_type']
                ];
            }
        } catch (PDOException $e) {
            error_log("Error getting settings by type: " . $e->getMessage());
        }
        
        return $settings;
    }

    /**
     * Get settings by category with enhanced categorization
     * @return array
     */
    public function getSettingsByCategory() {
        $categories = [
            'organization' => [
                'title' => 'ข้อมูลองค์กร',
                'icon' => 'fas fa-building',
                'keys' => [
                    'org_name', 'org_address', 'org_logo', 'org_phone', 
                    'org_email', 'org_website', 'org_tax_id', 'org_registration_number'
                ]
            ],
            'work_time' => [
                'title' => 'การตั้งค่าเวลาทำงาน',
                'icon' => 'fas fa-clock',
                'keys' => [
                    'work_start_time', 'work_end_time', 'grace_period_minutes', 
                    'ot_start_time', 'break_start_time', 'break_end_time',
                    'lunch_break_duration', 'work_days_per_week', 'weekend_ot_rate',
                    'holiday_ot_rate', 'night_shift_allowance'
                ]
            ],
            'theme' => [
                'title' => 'ธีมและสีสัน',
                'icon' => 'fas fa-palette',
                'keys' => [
                    'favicon', 'primary_color', 'secondary_color', 'accent_color', 
                    'sidebar_bg_color', 'header_bg_color', 'login_bg_image',
                    'logo_position', 'theme_mode'
                ]
            ],
            'system' => [
                'title' => 'การตั้งค่าระบบ',
                'icon' => 'fas fa-cogs',
                'keys' => [
                    'system_timezone', 'date_format', 'time_format', 'language', 
                    'currency', 'decimal_places', 'backup_frequency', 'auto_logout_time',
                    'system_maintenance_mode', 'debug_mode'
                ]
            ],
            'notifications' => [
                'title' => 'การแจ้งเตือน',
                'icon' => 'fas fa-bell',
                'keys' => [
                    'enable_email_notifications', 'enable_sms_notifications', 
                    'notification_sound', 'late_arrival_notification',
                    'leave_request_notification', 'overtime_notification',
                    'birthday_notification', 'contract_expiry_notification',
                    'email_server_host', 'email_server_port', 'email_server_username',
                    'sms_provider', 'sms_api_key'
                ]
            ],
            'security' => [
                'title' => 'ความปลอดภัย',
                'icon' => 'fas fa-shield-alt',
                'keys' => [
                    'session_timeout', 'max_login_attempts', 'password_min_length',
                    'password_require_special', 'password_require_uppercase',
                    'password_require_numbers', 'two_factor_auth', 'ip_whitelist',
                    'failed_login_lockout_time', 'password_expiry_days',
                    'force_password_change'
                ]
            ]
        ];

        $all_settings = $this->getAllSettings();
        $categorized_settings = [];

        foreach ($categories as $category_key => $category_info) {
            $categorized_settings[$category_key] = [
                'title' => $category_info['title'],
                'icon' => $category_info['icon'],
                'settings' => []
            ];

            foreach ($category_info['keys'] as $setting_key) {
                if (isset($all_settings[$setting_key])) {
                    $categorized_settings[$category_key]['settings'][$setting_key] = $all_settings[$setting_key];
                } else {
                    // Create default setting if not exists
                    $categorized_settings[$category_key]['settings'][$setting_key] = [
                        'value' => $this->getDefaultValue($setting_key),
                        'description' => $this->getSettingDescription($setting_key),
                        'type' => $this->getSettingType($setting_key)
                    ];
                }
            }
        }

        return $categorized_settings;
    }

    /**
     * Save multiple settings
     * @param array $settings_array
     * @return bool
     */
    public function saveSettings($settings_array) {
        if (empty($settings_array)) {
            return true;
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  (setting_key, setting_value, setting_description, setting_type, created_at, updated_at) 
                  VALUES (:key, :value, :description, :type, NOW(), NOW())
                  ON DUPLICATE KEY UPDATE 
                  setting_value = VALUES(setting_value), 
                  updated_at = NOW()";
        
        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare($query);
            
            foreach ($settings_array as $key => $value) {
                $description = $this->getSettingDescription($key);
                $type = $this->getSettingType($key);
                
                $stmt->bindParam(':key', $key);
                $stmt->bindParam(':value', $value);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':type', $type);
                $stmt->execute();
            }
            
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollback();
            error_log("Setting save error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Save single setting
     * @param string $key
     * @param mixed $value
     * @param string|null $description
     * @param string $type
     * @return bool
     */
    public function saveSetting($key, $value, $description = null, $type = 'text') {
        $query = "INSERT INTO " . $this->table_name . " 
                  (setting_key, setting_value, setting_description, setting_type, created_at, updated_at) 
                  VALUES (:key, :value, :description, :type, NOW(), NOW())
                  ON DUPLICATE KEY UPDATE 
                  setting_value = VALUES(setting_value),
                  setting_description = COALESCE(VALUES(setting_description), setting_description),
                  setting_type = COALESCE(VALUES(setting_type), setting_type),
                  updated_at = NOW()";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':key', $key);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':type', $type);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Single setting save error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete setting
     * @param string $key
     * @return bool
     */
    public function deleteSetting($key) {
        $query = "DELETE FROM " . $this->table_name . " WHERE setting_key = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $key);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Setting delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if setting exists
     * @param string $key
     * @return bool
     */
    public function settingExists($key) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE setting_key = ?";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $key);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error checking setting existence: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reset settings to defaults
     * @return bool
     */
    public function resetToDefaults() {
        $defaults = $this->getDefaultSettings();
        return $this->saveSettings($defaults);
    }

    /**
     * Get default settings array
     * @return array
     */
    private function getDefaultSettings() {
        return [
            // Organization
            'org_name' => 'อ.บ.ต.หนองปากโลง',
            'org_address' => '',
            'org_phone' => '',
            'org_email' => '',
            'org_website' => '',
            'org_tax_id' => '',
            'org_registration_number' => '',
            
            // Work Time
            'work_start_time' => '08:30',
            'work_end_time' => '17:30',
            'grace_period_minutes' => '15',
            'ot_start_time' => '18:00',
            'break_start_time' => '12:00',
            'break_end_time' => '13:00',
            'lunch_break_duration' => '60',
            'work_days_per_week' => '5',
            'weekend_ot_rate' => '2.0',
            'holiday_ot_rate' => '3.0',
            'night_shift_allowance' => '500',
            
            // Theme
            'primary_color' => '#4f46e5',
            'secondary_color' => '#7c3aed',
            'accent_color' => '#06b6d4',
            'sidebar_bg_color' => '#1f2937',
            'header_bg_color' => '#ffffff',
            'logo_position' => 'left',
            'theme_mode' => 'light',
            
            // System
            'system_timezone' => 'Asia/Bangkok',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'language' => 'th',
            'currency' => 'THB',
            'decimal_places' => '2',
            'backup_frequency' => 'daily',
            'auto_logout_time' => '30',
            'system_maintenance_mode' => '0',
            'debug_mode' => '0',
            
            // Notifications
            'enable_email_notifications' => '1',
            'enable_sms_notifications' => '0',
            'notification_sound' => '1',
            'late_arrival_notification' => '1',
            'leave_request_notification' => '1',
            'overtime_notification' => '1',
            'birthday_notification' => '1',
            'contract_expiry_notification' => '1',
            'email_server_host' => '',
            'email_server_port' => '587',
            'email_server_username' => '',
            'sms_provider' => '',
            'sms_api_key' => '',
            
            // Security
            'session_timeout' => '1800',
            'max_login_attempts' => '5',
            'password_min_length' => '8',
            'password_require_special' => '1',
            'password_require_uppercase' => '1',
            'password_require_numbers' => '1',
            'two_factor_auth' => '0',
            'ip_whitelist' => '',
            'failed_login_lockout_time' => '300',
            'password_expiry_days' => '90',
            'force_password_change' => '0'
        ];
    }

    /**
     * Get default value for a setting key
     * @param string $key
     * @return mixed
     */
    private function getDefaultValue($key) {
        $defaults = $this->getDefaultSettings();
        return $defaults[$key] ?? '';
    }

    /**
     * Get setting description
     * @param string $key
     * @return string
     */
    private function getSettingDescription($key) {
        $descriptions = [
            // Organization
            'org_name' => 'ชื่อองค์กร',
            'org_address' => 'ที่อยู่องค์กร',
            'org_phone' => 'เบอร์โทรศัพท์',
            'org_email' => 'อีเมลองค์กร',
            'org_website' => 'เว็บไซต์องค์กร',
            'org_tax_id' => 'เลขประจำตัวผู้เสียภาษี',
            'org_registration_number' => 'เลขที่จดทะเบียน',
            
            // Work Time
            'work_start_time' => 'เวลาเริ่มงาน',
            'work_end_time' => 'เวลาเลิกงาน',
            'grace_period_minutes' => 'เวลาผ่อนผัน (นาที)',
            'ot_start_time' => 'เวลาเริ่ม OT',
            'break_start_time' => 'เวลาเริ่มพัก',
            'break_end_time' => 'เวลาสิ้นสุดพัก',
            'lunch_break_duration' => 'ระยะเวลาพักกลางวัน (นาที)',
            'work_days_per_week' => 'วันทำงานต่อสัปดาห์',
            'weekend_ot_rate' => 'อัตรา OT วันหยุดสุดสัปดาห์',
            'holiday_ot_rate' => 'อัตรา OT วันหยุดนักขัตฤกษ์',
            'night_shift_allowance' => 'เบี้ยเลี้ยงกะกลางคืน',
            
            // Theme
            'primary_color' => 'สีหลัก',
            'secondary_color' => 'สีรอง',
            'accent_color' => 'สีเน้น',
            'sidebar_bg_color' => 'สีพื้นหลัง Sidebar',
            'header_bg_color' => 'สีพื้นหลัง Header',
            'login_bg_image' => 'รูปพื้นหลังหน้า Login',
            'logo_position' => 'ตำแหน่งโลโก้',
            'theme_mode' => 'โหมดธีม',
            
            // System
            'system_timezone' => 'เขตเวลา',
            'date_format' => 'รูปแบบวันที่',
            'time_format' => 'รูปแบบเวลา',
            'language' => 'ภาษา',
            'currency' => 'สกุลเงิน',
            'decimal_places' => 'จำนวนทศนิยม',
            'backup_frequency' => 'ความถี่การสำรองข้อมูล',
            'auto_logout_time' => 'เวลาออกจากระบบอัตโนมัติ (นาที)',
            'system_maintenance_mode' => 'โหมดปรับปรุงระบบ',
            'debug_mode' => 'โหมด Debug',
            
            // Notifications
            'enable_email_notifications' => 'เปิดการแจ้งเตือนทางอีเมล',
            'enable_sms_notifications' => 'เปิดการแจ้งเตือนทาง SMS',
            'notification_sound' => 'เสียงแจ้งเตือน',
            'late_arrival_notification' => 'แจ้งเตือนการมาสาย',
            'leave_request_notification' => 'แจ้งเตือนคำขอลา',
            'overtime_notification' => 'แจ้งเตือนการทำ OT',
            'birthday_notification' => 'แจ้งเตือนวันเกิด',
            'contract_expiry_notification' => 'แจ้งเตือนสัญญาหมดอายุ',
            'email_server_host' => 'เซิร์ฟเวอร์อีเมล',
            'email_server_port' => 'พอร์ตเซิร์ฟเวอร์อีเมล',
            'email_server_username' => 'ชื่อผู้ใช้เซิร์ฟเวอร์อีเมล',
            'sms_provider' => 'ผู้ให้บริการ SMS',
            'sms_api_key' => 'API Key สำหรับ SMS',
            
            // Security
            'session_timeout' => 'หมดเวลา Session (วินาที)',
            'max_login_attempts' => 'จำนวนครั้งที่พยายาม Login สูงสุด',
            'password_min_length' => 'ความยาวรหัสผ่านขั้นต่ำ',
            'password_require_special' => 'ต้องมีอักขระพิเศษในรหัสผ่าน',
            'password_require_uppercase' => 'ต้องมีตัวพิมพ์ใหญ่ในรหัสผ่าน',
            'password_require_numbers' => 'ต้องมีตัวเลขในรหัสผ่าน',
            'two_factor_auth' => 'การยืนยันตัวตนสองชั้น',
            'ip_whitelist' => 'รายการ IP ที่อนุญาต',
            'failed_login_lockout_time' => 'เวลาล็อคบัญชีเมื่อ Login ผิด (วินาที)',
            'password_expiry_days' => 'วันหมดอายุรหัสผ่าน',
            'force_password_change' => 'บังคับเปลี่ยนรหัสผ่าน'
        ];
        
        return $descriptions[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    /**
     * Get setting input type
     * @param string $key
     * @return string
     */
    private function getSettingType($key) {
        $types = [
            // Organization
            'org_name' => 'text',
            'org_address' => 'textarea',
            'org_phone' => 'tel',
            'org_email' => 'email',
            'org_website' => 'url',
            'org_tax_id' => 'text',
            'org_registration_number' => 'text',
            'org_logo' => 'file',
            
            // Work Time
            'work_start_time' => 'time',
            'work_end_time' => 'time',
            'grace_period_minutes' => 'number',
            'ot_start_time' => 'time',
            'break_start_time' => 'time',
            'break_end_time' => 'time',
            'lunch_break_duration' => 'number',
            'work_days_per_week' => 'number',
            'weekend_ot_rate' => 'number',
            'holiday_ot_rate' => 'number',
            'night_shift_allowance' => 'number',
            
            // Theme
            'primary_color' => 'color',
            'secondary_color' => 'color',
            'accent_color' => 'color',
            'sidebar_bg_color' => 'color',
            'header_bg_color' => 'color',
            'login_bg_image' => 'file',
            'favicon' => 'file',
            'logo_position' => 'select',
            'theme_mode' => 'select',
            
            // System
            'system_timezone' => 'select',
            'date_format' => 'select',
            'time_format' => 'select',
            'language' => 'select',
            'currency' => 'select',
            'decimal_places' => 'number',
            'backup_frequency' => 'select',
            'auto_logout_time' => 'number',
            'system_maintenance_mode' => 'boolean',
            'debug_mode' => 'boolean',
            
            // Notifications
            'enable_email_notifications' => 'boolean',
            'enable_sms_notifications' => 'boolean',
            'notification_sound' => 'boolean',
            'late_arrival_notification' => 'boolean',
            'leave_request_notification' => 'boolean',
            'overtime_notification' => 'boolean',
            'birthday_notification' => 'boolean',
            'contract_expiry_notification' => 'boolean',
            'email_server_host' => 'text',
            'email_server_port' => 'number',
            'email_server_username' => 'email',
            'sms_provider' => 'text',
            'sms_api_key' => 'password',
            
            // Security
            'session_timeout' => 'number',
            'max_login_attempts' => 'number',
            'password_min_length' => 'number',
            'password_require_special' => 'boolean',
            'password_require_uppercase' => 'boolean',
            'password_require_numbers' => 'boolean',
            'two_factor_auth' => 'boolean',
            'ip_whitelist' => 'textarea',
            'failed_login_lockout_time' => 'number',
            'password_expiry_days' => 'number',
            'force_password_change' => 'boolean'
        ];
        
        return $types[$key] ?? 'text';
    }

    /**
     * Helper functions for specific data types
     */
    
    /**
     * Get boolean value
     * @param string $key
     * @param bool $default
     * @return bool
     */
    public function getBooleanValue($key, $default = false) {
        $value = $this->getSettingValue($key, $default ? '1' : '0');
        return in_array($value, ['1', 'true', 'yes', 'on'], true);
    }

    /**
     * Get numeric value
     * @param string $key
     * @param float $default
     * @return float
     */
    public function getNumericValue($key, $default = 0) {
        $value = $this->getSettingValue($key, $default);
        return is_numeric($value) ? (float)$value : $default;
    }

    /**
     * Get integer value
     * @param string $key
     * @param int $default
     * @return int
     */
    public function getIntegerValue($key, $default = 0) {
        $value = $this->getSettingValue($key, $default);
        return is_numeric($value) ? (int)$value : $default;
    }

    /**
     * Get array value (for comma-separated values)
     * @param string $key
     * @param array $default
     * @return array
     */
    public function getArrayValue($key, $default = []) {
        $value = $this->getSettingValue($key, '');
        if (empty($value)) {
            return $default;
        }
        return array_map('trim', explode(',', $value));
    }

    /**
     * Validate setting value based on type
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function validateSettingValue($key, $value) {
        $type = $this->getSettingType($key);
        
        switch ($type) {
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            case 'url':
                return filter_var($value, FILTER_VALIDATE_URL) !== false;
            case 'number':
                return is_numeric($value);
            case 'boolean':
                return in_array($value, ['0', '1', 'true', 'false', 'yes', 'no']);
            case 'time':
                return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $value);
            case 'color':
                return preg_match('/^#[0-9A-F]{6}$/i', $value);
            default:
                return true; // Default validation passes
        }
    }

    /**
     * Get settings that require system restart
     * @return array
     */
    public function getSystemRestartRequiredSettings() {
        return [
            'system_timezone',
            'language',
            'debug_mode',
            'system_maintenance_mode',
            'session_timeout'
        ];
    }

    /**
     * Check if setting change requires system restart
     * @param string $key
     * @return bool
     */
    public function requiresSystemRestart($key) {
        return in_array($key, $this->getSystemRestartRequiredSettings());
    }

    /**
     * Get settings backup
     * @return array
     */
    public function createBackup() {
        $backup = [
            'backup_date' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'settings' => $this->getAllSettings()
        ];
        
        return $backup;
    }

    /**
     * Restore settings from backup
     * @param array $backup_data
     * @return bool
     */
    public function restoreFromBackup($backup_data) {
        if (!isset($backup_data['settings']) || !is_array($backup_data['settings'])) {
            return false;
        }
        
        $settings_to_restore = [];
        foreach ($backup_data['settings'] as $key => $data) {
            if (is_array($data) && isset($data['value'])) {
                $settings_to_restore[$key] = $data['value'];
            } else {
                $settings_to_restore[$key] = $data;
            }
        }
        
        return $this->saveSettings($settings_to_restore);
    }
}
?>
=======
    // ดึงข้อมูลการตั้งค่าทั้งหมด
    public function getAllSettings() {
        $settings = [];
        $query = "SELECT setting_key, setting_value FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    // ===== ฟังก์ชันใหม่: ดึงค่าการตั้งค่ารายการเดียว =====
    public function getSettingValue($key, $default = null) {
        $query = "SELECT setting_value FROM " . $this->table_name . " WHERE setting_key = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $key);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['setting_value'];
        }
        return $default; // คืนค่า default หากไม่พบ key
    }

    // บันทึกการตั้งค่า
    public function saveSettings($settings_array) {
        $query = "INSERT INTO " . $this->table_name . " (setting_key, setting_value) VALUES (:key, :value)
                  ON DUPLICATE KEY UPDATE setting_value = :value";
        
        $stmt = $this->conn->prepare($query);

        try {
            foreach ($settings_array as $key => $value) {
                $stmt->bindParam(':key', $key);
                $stmt->bindParam(':value', $value);
                $stmt->execute();
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
