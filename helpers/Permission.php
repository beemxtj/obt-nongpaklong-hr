<?php
// helpers/Permission.php

class Permission {
    /**
     * Checks if the currently logged-in user has a specific permission.
     * @param string $permission_key The key of the permission to check (e.g., 'manage_employees').
     * @return bool True if the user has the permission, false otherwise.
     */
    public static function has($permission_key) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Admin (role_id = 1) always has all permissions
        if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
            return true;
        }

        // Check permissions from the session
        if (isset($_SESSION['permissions'])) {
            $permissions = json_decode($_SESSION['permissions'], true);
            if (is_array($permissions) && in_array($permission_key, $permissions)) {
                return true;
            }
        }
        
        return false;
    }
}
?>
