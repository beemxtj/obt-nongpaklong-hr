/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 100432
 Source Host           : localhost:3306
 Source Schema         : hrm_nongpaklong

 Target Server Type    : MySQL
 Target Server Version : 100432
 File Encoding         : 65001

 Date: 01/08/2025 16:35:23
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for approval_workflow_steps
-- ----------------------------
DROP TABLE IF EXISTS `approval_workflow_steps`;
CREATE TABLE `approval_workflow_steps`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `workflow_id` int NOT NULL,
  `step_number` int NOT NULL,
  `approver_role` enum('SUPERVISOR','HR','MANAGER') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `workflow_id`(`workflow_id`) USING BTREE,
  CONSTRAINT `fk_workflow_step` FOREIGN KEY (`workflow_id`) REFERENCES `approval_workflows` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of approval_workflow_steps
-- ----------------------------
INSERT INTO `approval_workflow_steps` VALUES (1, 1, 1, 'SUPERVISOR', '2025-08-01 11:03:38');
INSERT INTO `approval_workflow_steps` VALUES (2, 1, 2, 'MANAGER', '2025-08-01 11:03:38');
INSERT INTO `approval_workflow_steps` VALUES (3, 1, 3, 'HR', '2025-08-01 11:03:38');

-- ----------------------------
-- Table structure for approval_workflows
-- ----------------------------
DROP TABLE IF EXISTS `approval_workflows`;
CREATE TABLE `approval_workflows`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of approval_workflows
-- ----------------------------
INSERT INTO `approval_workflows` VALUES (1, 'อนุมัติลาป่วย', '2025-08-01 11:03:38', '2025-08-01 11:03:38');

-- ----------------------------
-- Table structure for attendance_logs
-- ----------------------------
DROP TABLE IF EXISTS `attendance_logs`;
CREATE TABLE `attendance_logs`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `clock_in_time` datetime NULL DEFAULT NULL,
  `clock_out_time` datetime NULL DEFAULT NULL,
  `clock_in_latitude` decimal(10, 8) NULL DEFAULT NULL,
  `clock_in_longitude` decimal(11, 8) NULL DEFAULT NULL,
  `clock_out_latitude` decimal(10, 8) NULL DEFAULT NULL,
  `clock_out_longitude` decimal(11, 8) NULL DEFAULT NULL,
  `clock_in_image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `clock_out_image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `status` enum('ปกติ','สาย','ขาดงาน') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ปกติ',
  `work_hours` decimal(5, 2) NULL DEFAULT 0.00,
  `ot_hours` decimal(5, 2) NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `employee_id`(`employee_id`) USING BTREE,
  INDEX `clock_in_date`(`clock_in_time`) USING BTREE,
  CONSTRAINT `fk_attendance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of attendance_logs
-- ----------------------------

-- ----------------------------
-- Table structure for audit_logs
-- ----------------------------
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NULL DEFAULT NULL,
  `action` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `action`(`action`) USING BTREE,
  INDEX `timestamp`(`timestamp`) USING BTREE,
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 36 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of audit_logs
-- ----------------------------
INSERT INTO `audit_logs` VALUES (1, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-07-31 09:00:41');
INSERT INTO `audit_logs` VALUES (2, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-07-31 12:31:17');
INSERT INTO `audit_logs` VALUES (3, 1, 'logout', 'User logged out successfully.', '::1', '2025-07-31 13:21:49');
INSERT INTO `audit_logs` VALUES (4, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-07-31 13:21:51');
INSERT INTO `audit_logs` VALUES (5, 1, 'logout', 'User logged out successfully.', '::1', '2025-07-31 14:16:20');
INSERT INTO `audit_logs` VALUES (6, 4, 'login_success', 'User logged in successfully via email.', '::1', '2025-07-31 14:17:51');
INSERT INTO `audit_logs` VALUES (7, 4, 'logout', 'User logged out successfully.', '::1', '2025-07-31 14:19:02');
INSERT INTO `audit_logs` VALUES (8, 4, 'login_success', 'User logged in successfully via email.', '::1', '2025-07-31 14:22:06');
INSERT INTO `audit_logs` VALUES (9, 4, 'logout', 'User logged out successfully.', '::1', '2025-07-31 14:49:55');
INSERT INTO `audit_logs` VALUES (10, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-07-31 14:49:59');
INSERT INTO `audit_logs` VALUES (11, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-07-31 23:38:51');
INSERT INTO `audit_logs` VALUES (12, 1, 'logout', 'User logged out successfully.', '::1', '2025-08-01 00:12:09');
INSERT INTO `audit_logs` VALUES (13, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-08-01 00:12:13');
INSERT INTO `audit_logs` VALUES (14, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-08-01 09:40:30');
INSERT INTO `audit_logs` VALUES (15, 1, 'logout', 'User logged out successfully.', '::1', '2025-08-01 09:43:32');
INSERT INTO `audit_logs` VALUES (16, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-08-01 09:43:36');
INSERT INTO `audit_logs` VALUES (17, 1, 'logout', 'User logged out successfully.', '::1', '2025-08-01 09:47:39');
INSERT INTO `audit_logs` VALUES (18, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-08-01 09:47:58');
INSERT INTO `audit_logs` VALUES (19, 1, 'logout', 'User logged out successfully.', '::1', '2025-08-01 10:57:56');
INSERT INTO `audit_logs` VALUES (20, 4, 'login_success', 'User logged in successfully via email.', '::1', '2025-08-01 10:58:00');
INSERT INTO `audit_logs` VALUES (21, 4, 'logout', 'User logged out successfully.', '::1', '2025-08-01 10:58:49');
INSERT INTO `audit_logs` VALUES (22, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-08-01 10:59:17');
INSERT INTO `audit_logs` VALUES (23, 1, 'logout', 'User logged out successfully.', '::1', '2025-08-01 11:17:36');
INSERT INTO `audit_logs` VALUES (24, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-08-01 11:46:34');
INSERT INTO `audit_logs` VALUES (25, 1, 'logout', 'User logged out successfully.', '::1', '2025-08-01 11:48:34');
INSERT INTO `audit_logs` VALUES (26, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-08-01 11:54:51');
INSERT INTO `audit_logs` VALUES (27, 1, 'logout', 'User logged out successfully.', '::1', '2025-08-01 13:55:26');
INSERT INTO `audit_logs` VALUES (28, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-08-01 13:56:21');
INSERT INTO `audit_logs` VALUES (29, 1, 'logout', 'User logged out successfully.', '::1', '2025-08-01 13:58:44');
INSERT INTO `audit_logs` VALUES (30, 1, 'logout', 'User logged out successfully.', '::1', '2025-08-01 14:02:35');
INSERT INTO `audit_logs` VALUES (31, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-08-01 14:03:56');
INSERT INTO `audit_logs` VALUES (32, 1, 'logout', 'User logged out successfully.', '::1', '2025-08-01 14:03:59');
INSERT INTO `audit_logs` VALUES (33, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-08-01 14:04:00');
INSERT INTO `audit_logs` VALUES (34, 1, 'logout', 'User logged out successfully.', '::1', '2025-08-01 14:08:01');
INSERT INTO `audit_logs` VALUES (35, 1, 'login_success', 'User logged in successfully via email.', '::1', '2025-08-01 14:08:13');

-- ----------------------------
-- Table structure for departments
-- ----------------------------
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name_th` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of departments
-- ----------------------------
INSERT INTO `departments` VALUES (1, 'ฝ่ายบริหาร', 'Administration', NULL, '2025-07-30 22:07:51', '2025-07-30 22:07:51');
INSERT INTO `departments` VALUES (3, 'ฝ่ายบุคคล', 'Human Resources', NULL, '2025-07-30 22:07:51', '2025-07-30 22:07:51');
INSERT INTO `departments` VALUES (4, 'ฝ่ายเทคโนโลยีสารสนเทศ', 'Information Technology', NULL, '2025-07-30 22:07:51', '2025-07-30 22:07:51');
INSERT INTO `departments` VALUES (5, 'ฝ่ายการเงิน', 'Finance', 'ทดสอบระบบ', '2025-07-31 09:14:08', '2025-07-31 09:14:08');

-- ----------------------------
-- Table structure for employee_shift_assignments
-- ----------------------------
DROP TABLE IF EXISTS `employee_shift_assignments`;
CREATE TABLE `employee_shift_assignments`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL COMMENT 'รหัสพนักงาน',
  `shift_id` int NOT NULL COMMENT 'รหัสกะ',
  `start_date` date NOT NULL COMMENT 'วันที่เริ่มมอบหมาย',
  `end_date` date NULL DEFAULT NULL COMMENT 'วันที่สิ้นสุดมอบหมาย (NULL = ไม่จำกัด)',
  `assigned_by` int NULL DEFAULT NULL COMMENT 'ผู้มอบหมาย',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'หมายเหตุ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_employee`(`employee_id`) USING BTREE,
  INDEX `idx_shift`(`shift_id`) USING BTREE,
  INDEX `idx_dates`(`start_date`, `end_date`) USING BTREE,
  INDEX `idx_active_assignment`(`employee_id`, `start_date`, `end_date`) USING BTREE,
  INDEX `fk_shift_assignment_assignor`(`assigned_by`) USING BTREE,
  INDEX `idx_employee_shift_current`(`employee_id`, `start_date`, `end_date`) USING BTREE,
  INDEX `idx_shift_employees_active`(`shift_id`, `start_date`, `end_date`) USING BTREE,
  CONSTRAINT `fk_shift_assignment_assignor` FOREIGN KEY (`assigned_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `fk_shift_assignment_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_shift_assignment_shift` FOREIGN KEY (`shift_id`) REFERENCES `work_shifts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'ตารางการมอบหมายกะงานให้พนักงาน' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of employee_shift_assignments
-- ----------------------------

-- ----------------------------
-- Table structure for employees
-- ----------------------------
DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `prefix` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `first_name_th` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `first_name_en` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `last_name_th` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `last_name_en` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `gender` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `birth_date` date NULL DEFAULT NULL,
  `nationality` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `work_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `national_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `address_line1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `district` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `province` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `postal_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `start_date` date NOT NULL,
  `probation_days` int NULL DEFAULT NULL,
  `status` enum('ทำงาน','ทดลองงาน','ลาออก','พักงาน') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ทดลองงาน',
  `position_id` int NULL DEFAULT NULL,
  `department_id` int NULL DEFAULT NULL,
  `supervisor_id` int NULL DEFAULT NULL,
  `role_id` int NOT NULL,
  `line_user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `salary` decimal(10, 2) NULL DEFAULT 0.00,
  `tax_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `bank_account_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `provident_fund_rate_employee` decimal(5, 2) NULL DEFAULT NULL,
  `provident_fund_rate_company` decimal(5, 2) NULL DEFAULT NULL,
  `profile_image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `employee_code`(`employee_code`) USING BTREE,
  UNIQUE INDEX `email`(`email`) USING BTREE,
  UNIQUE INDEX `line_user_id`(`line_user_id`) USING BTREE,
  INDEX `position_id`(`position_id`) USING BTREE,
  INDEX `department_id`(`department_id`) USING BTREE,
  INDEX `role_id`(`role_id`) USING BTREE,
  INDEX `supervisor_id`(`supervisor_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of employees
-- ----------------------------
INSERT INTO `employees` VALUES (1, 'ADMIN01', '$2y$10$JADJqcCBvDCoP7rusNYWA.jm7kFqieU5/.36VXhEQ0/UfqbUVL.8.', 'นาย', 'ผู้ดูแล', '', 'ระบบ', '', 'ชาย', '0000-00-00', '', 'admin@nongpaklong.go.th', '0810000001', '', '', '', '', '', '', '2023-01-01', 0, 'ทำงาน', 1, 4, 0, 1, NULL, 75000.00, '', '', '', 0.00, 0.00, 'uploads/profiles/profile_688a2c324832a.png');
INSERT INTO `employees` VALUES (2, 'HR001', '$2y$10$m/kPB0D6tW8b7s/rN32qfeigYKpUvSopEhEZStsvb6wFB0grigetq', 'นางสาว', 'สมหญิง', NULL, 'รักงาน', NULL, NULL, NULL, NULL, 'hr@nongpaklong.go.th', '0810000002', NULL, NULL, NULL, NULL, NULL, NULL, '2023-03-01', NULL, 'ทำงาน', 2, 1, 1, 2, NULL, 55000.00, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `employees` VALUES (3, 'EMP001', '$2y$10$m/kPB0D6tW8b7s/rN32qfeigYKpUvSopEhEZStsvb6wFB0grigetq', 'นาย', 'สมชาย', '', 'ใจดี', '', '', '0000-00-00', '', 'somchai.j@nongpaklong.go.th', '0810000003', '', '', '', '', '', '', '2024-05-20', 0, 'ทำงาน', 4, 2, 2, 3, NULL, 35000.00, '', '', '', 0.00, 0.00, 'uploads/profiles/profile_688a3a2828e83.png');
INSERT INTO `employees` VALUES (4, 'EMP002', '$2y$10$JADJqcCBvDCoP7rusNYWA.jm7kFqieU5/.36VXhEQ0/UfqbUVL.8.', 'นาง', 'มานี', '', 'ขยันยิ่ง', '', '', '0000-00-00', '', 'manee.k@nongpaklong.go.th', '0810000004', '', '', '', '', '', '', '2024-06-15', 0, 'ทำงาน', 5, 5, 3, 4, NULL, 22000.00, '', '', '', 0.00, 0.00, 'uploads/profiles/profile_20250731_050854_688ade4679ebf.png');

-- ----------------------------
-- Table structure for holidays
-- ----------------------------
DROP TABLE IF EXISTS `holidays`;
CREATE TABLE `holidays`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `holiday_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ชื่อวันหยุด',
  `holiday_date` date NOT NULL COMMENT 'วันที่หยุด',
  `holiday_type` enum('national','religious','company','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'national' COMMENT 'ประเภทวันหยุด',
  `is_paid` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'หยุดได้เงิน',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'คำอธิบาย',
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'วันหยุดประจำปี',
  `created_by` int NULL DEFAULT NULL COMMENT 'ผู้สร้าง',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `unique_holiday_date`(`holiday_date`) USING BTREE,
  INDEX `idx_holiday_type`(`holiday_type`) USING BTREE,
  INDEX `idx_holiday_date`(`holiday_date`) USING BTREE,
  INDEX `idx_is_recurring`(`is_recurring`) USING BTREE,
  INDEX `fk_holidays_creator`(`created_by`) USING BTREE,
  CONSTRAINT `fk_holidays_creator` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'ตารางวันหยุดนักขัตฤกษ์' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of holidays
-- ----------------------------
INSERT INTO `holidays` VALUES (1, 'วันขึ้นปีใหม่', '2025-01-01', 'national', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (2, 'วันมาฆบูชา', '2025-02-12', 'religious', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (3, 'วันจักรี', '2025-04-06', 'national', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (4, 'วันสงกรานต์', '2025-04-13', 'national', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (5, 'วันสงกรานต์', '2025-04-14', 'national', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (6, 'วันสงกรานต์', '2025-04-15', 'national', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (7, 'วันแรงงานแห่งชาติ', '2025-05-01', 'national', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (8, 'วันฉัตรมงคล', '2025-05-05', 'national', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (9, 'วันวิสาขบูชา', '2025-05-11', 'religious', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (10, 'วันเฉลิมพระชนมพรรษาสมเด็จพระนางเจ้าฯ พระบรมราชินี', '2025-06-03', 'national', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (11, 'วันอาสาฬหบูชา', '2025-07-09', 'religious', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (12, 'วันเข้าพรรษา', '2025-07-10', 'religious', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (13, 'วันเฉลิมพระชนมพรรษาพระบาทสมเด็จพระเจ้าอยู่หัว', '2025-07-28', 'national', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (14, 'วันแม่แห่งชาติ', '2025-08-12', 'national', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (15, 'วันคล้ายวันสวรรคตพระบาทสมเด็จพระบรมชนกาธิเบศร มหาภูมิพลอดุลยเดชมหาราช วันที่ 13 ตุลาคม', '2025-10-13', 'national', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (16, 'วันปิยมหาราช', '2025-10-23', 'national', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (17, 'วันพ่อแห่งชาติ', '2025-12-05', 'national', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (18, 'วันรัฐธรรมนูญ', '2025-12-10', 'national', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `holidays` VALUES (19, 'วันสิ้นปี', '2025-12-31', 'national', 1, 'วันหยุดราชการ', 0, NULL, '2025-07-31 14:49:10', '2025-07-31 14:49:10');

-- ----------------------------
-- Table structure for leave_policies
-- ----------------------------
DROP TABLE IF EXISTS `leave_policies`;
CREATE TABLE `leave_policies`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `leave_type_id` int NOT NULL,
  `days_allowed_per_year` int NOT NULL DEFAULT 0 COMMENT 'จำนวนวันลาที่อนุญาตต่อปี (0 = ไม่จำกัด)',
  `is_unlimited` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ลาได้ไม่จำกัดวัน (1 = ไม่จำกัด, 0 = จำกัด)',
  `can_be_carried_over` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'สามารถโอนวันลาไปปีถัดไปได้ (1 = ได้, 0 = ไม่ได้)',
  `max_carry_over_days` int NOT NULL DEFAULT 0 COMMENT 'จำนวนวันลาสูงสุดที่โอนได้',
  `min_notice_days` int NOT NULL DEFAULT 1 COMMENT 'จำนวนวันขั้นต่ำที่ต้องแจ้งล่วงหน้า',
  `max_consecutive_days` int NOT NULL DEFAULT 30 COMMENT 'จำนวนวันสูงสุดที่ลาติดต่อกันได้',
  `requires_approval` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'ต้องการการอนุมัติ (1 = ต้อง, 0 = ไม่ต้อง)',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'รายละเอียดและข้อกำหนดพิเศษ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_leave_policies_leave_type`(`leave_type_id`) USING BTREE,
  INDEX `idx_leave_policies_unlimited`(`is_unlimited`) USING BTREE,
  INDEX `idx_leave_policies_carry_over`(`can_be_carried_over`) USING BTREE,
  INDEX `idx_leave_policies_approval`(`requires_approval`) USING BTREE,
  INDEX `idx_leave_policies_created_at`(`created_at`) USING BTREE,
  INDEX `idx_leave_policies_updated_at`(`updated_at`) USING BTREE,
  CONSTRAINT `fk_leave_policies_leave_type` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'ตารางนโยบายการลา' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of leave_policies
-- ----------------------------
INSERT INTO `leave_policies` VALUES (1, 1, 30, 0, 1, 5, 1, 30, 1, 'ลาป่วยต้องมีใบรับรองแพทย์หากลาเกิน 3 วันติดต่อกัน', '2025-08-01 10:54:55', '2025-08-01 10:54:55');
INSERT INTO `leave_policies` VALUES (2, 2, 6, 0, 0, 0, 3, 6, 1, 'ลากิจต้องแจ้งล่วงหน้าอย่างน้อย 3 วัน พร้อมระบุเหตุผล', '2025-08-01 10:54:55', '2025-08-01 10:54:55');
INSERT INTO `leave_policies` VALUES (3, 3, 10, 0, 1, 3, 7, 10, 1, 'ลาพักร้อนต้องแจ้งล่วงหน้าอย่างน้อย 7 วัน และได้รับอนุมัติจากหัวหน้างาน', '2025-08-01 10:54:55', '2025-08-01 10:54:55');
INSERT INTO `leave_policies` VALUES (4, 4, 0, 1, 0, 0, 0, 365, 0, 'ลาคลอดตามกฎหมายแรงงาน สามารถลาได้ตามที่กฎหมายกำหนด', '2025-08-01 10:54:55', '2025-08-01 10:54:55');
INSERT INTO `leave_policies` VALUES (5, 5, 3, 0, 0, 0, 0, 3, 0, 'ลาเพื่อทำหน้าที่พลเมือง เช่น เลือกตั้ง หรือเป็นพยานในศาล', '2025-08-01 10:54:55', '2025-08-01 10:54:55');

-- ----------------------------
-- Table structure for leave_requests
-- ----------------------------
DROP TABLE IF EXISTS `leave_requests`;
CREATE TABLE `leave_requests`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `leave_type_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `status` enum('รออนุมัติ','อนุมัติ','ไม่อนุมัติ','ยกเลิก') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'รออนุมัติ',
  `attachment_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `employee_id`(`employee_id`) USING BTREE,
  INDEX `leave_type_id`(`leave_type_id`) USING BTREE,
  CONSTRAINT `fk_leave_request_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_leave_request_type` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of leave_requests
-- ----------------------------
INSERT INTO `leave_requests` VALUES (1, 1, 2, '2025-08-04', '2025-08-04', 'ทดสอบระบบ', 'รออนุมัติ', '', '2025-08-01 10:00:15', '2025-08-01 10:00:15');

-- ----------------------------
-- Table structure for leave_types
-- ----------------------------
DROP TABLE IF EXISTS `leave_types`;
CREATE TABLE `leave_types`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_days_per_year` int NOT NULL DEFAULT 0,
  `is_paid` tinyint(1) NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of leave_types
-- ----------------------------
INSERT INTO `leave_types` VALUES (1, 'ลาป่วย', 30, 1, '2025-07-30 22:07:51', '2025-07-30 22:07:51');
INSERT INTO `leave_types` VALUES (2, 'ลากิจ', 45, 1, '2025-07-30 22:07:51', '2025-07-30 22:07:51');
INSERT INTO `leave_types` VALUES (3, 'ลาพักผ่อน', 10, 1, '2025-07-30 22:07:51', '2025-07-30 22:07:51');
INSERT INTO `leave_types` VALUES (4, 'ลาคลอดบุตร', 98, 1, '2025-07-30 22:07:51', '2025-07-30 22:07:51');
INSERT INTO `leave_types` VALUES (5, 'ลาไม่รับเงินเดือน', 365, 0, '2025-07-30 22:07:51', '2025-07-30 22:07:51');
INSERT INTO `leave_types` VALUES (6, 'ลาไปราชการ', 30, 1, '2025-08-01 10:25:55', '2025-08-01 10:25:55');

-- ----------------------------
-- Table structure for notifications
-- ----------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_read` tinyint(1) NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `fk_notification_user` FOREIGN KEY (`user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of notifications
-- ----------------------------

-- ----------------------------
-- Table structure for payrolls
-- ----------------------------
DROP TABLE IF EXISTS `payrolls`;
CREATE TABLE `payrolls`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `pay_period_start` date NOT NULL,
  `pay_period_end` date NOT NULL,
  `base_salary` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `overtime_pay` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `allowances` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `late_deductions` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `absence_deductions` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `social_security` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `tax` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `employee_id`(`employee_id`) USING BTREE,
  INDEX `pay_period_start`(`pay_period_start`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of payrolls
-- ----------------------------
INSERT INTO `payrolls` VALUES (1, 1, '2025-07-01', '2025-07-31', 75000.00, 0.00, 0.00, 0.00, 0.00, 750.00, 3125.00, 71125.00, '2025-07-31 13:16:37');
INSERT INTO `payrolls` VALUES (2, 3, '2025-07-01', '2025-07-31', 35000.00, 0.00, 0.00, 0.00, 0.00, 750.00, 1125.00, 33125.00, '2025-07-31 13:16:37');
INSERT INTO `payrolls` VALUES (3, 4, '2025-07-01', '2025-07-31', 22000.00, 0.00, 0.00, 0.00, 0.00, 750.00, 475.00, 20775.00, '2025-07-31 13:16:37');
INSERT INTO `payrolls` VALUES (4, 2, '2025-07-01', '2025-07-31', 55000.00, 0.00, 0.00, 0.00, 0.00, 750.00, 2125.00, 52125.00, '2025-07-31 13:16:37');

-- ----------------------------
-- Table structure for payslips
-- ----------------------------
DROP TABLE IF EXISTS `payslips`;
CREATE TABLE `payslips`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `pay_period_month` int NOT NULL,
  `pay_period_year` int NOT NULL,
  `base_salary` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `ot_pay` decimal(15, 2) NULL DEFAULT 0.00,
  `total_earnings` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `tax_deduction` decimal(15, 2) NULL DEFAULT 0.00,
  `social_security_deduction` decimal(15, 2) NULL DEFAULT 0.00,
  `provident_fund_deduction` decimal(15, 2) NULL DEFAULT 0.00,
  `total_deductions` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `unique_employee_period`(`employee_id`, `pay_period_month`, `pay_period_year`) USING BTREE,
  INDEX `employee_id`(`employee_id`) USING BTREE,
  CONSTRAINT `fk_payslip_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of payslips
-- ----------------------------

-- ----------------------------
-- Table structure for positions
-- ----------------------------
DROP TABLE IF EXISTS `positions`;
CREATE TABLE `positions`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name_th` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of positions
-- ----------------------------
INSERT INTO `positions` VALUES (1, 'นายก อบต.', 'Mayor', '', '2025-07-30 22:07:51', '2025-07-31 12:51:32');
INSERT INTO `positions` VALUES (2, 'รองนายก อบต.', 'Deputy Mayor', NULL, '2025-07-30 22:07:51', '2025-07-30 22:07:51');
INSERT INTO `positions` VALUES (3, 'เลขานุการ', 'Secretary', NULL, '2025-07-30 22:07:51', '2025-07-30 22:07:51');
INSERT INTO `positions` VALUES (4, 'ผู้อำนวยการ', 'Director', NULL, '2025-07-30 22:07:51', '2025-07-30 22:07:51');
INSERT INTO `positions` VALUES (5, 'หัวหน้าฝ่าย', 'Department Head', NULL, '2025-07-30 22:07:51', '2025-07-30 22:07:51');
INSERT INTO `positions` VALUES (6, 'เจ้าหน้าที่', 'Officer', '', '2025-07-30 22:07:51', '2025-07-31 09:22:48');
INSERT INTO `positions` VALUES (7, 'พนักงานทั่วไป', 'General Staff', NULL, '2025-07-30 22:07:51', '2025-07-30 22:07:51');

-- ----------------------------
-- Table structure for previous_incomes
-- ----------------------------
DROP TABLE IF EXISTS `previous_incomes`;
CREATE TABLE `previous_incomes`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `tax_year` year NOT NULL,
  `total_income` decimal(15, 2) NULL DEFAULT 0.00 COMMENT 'รายได้รวมสะสม',
  `total_tax` decimal(15, 2) NULL DEFAULT 0.00 COMMENT 'ภาษีหัก ณ ที่จ่ายสะสม',
  `social_security` decimal(15, 2) NULL DEFAULT 0.00 COMMENT 'ประกันสังคมสะสม',
  `provident_fund` decimal(15, 2) NULL DEFAULT 0.00 COMMENT 'กองทุนสำรองเลี้ยงชีพสะสม',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `unique_employee_year`(`employee_id`, `tax_year`) USING BTREE,
  INDEX `idx_employee_id`(`employee_id`) USING BTREE,
  INDEX `idx_tax_year`(`tax_year`) USING BTREE,
  CONSTRAINT `fk_previous_income_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of previous_incomes
-- ----------------------------
INSERT INTO `previous_incomes` VALUES (1, 1, 2025, 0.00, 0.00, 0.00, 0.00, '2025-07-30 22:26:11', '2025-07-31 09:54:06');

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `role_name`(`role_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (1, 'Admin', '[\"manage_employees\",\"manage_attendance\",\"approve_leave\",\"manage_settings\",\"view_reports\"]', '2025-07-30 22:07:51', '2025-07-30 22:07:51');
INSERT INTO `roles` VALUES (2, 'HR', '[\"manage_employees\",\"manage_attendance\",\"approve_leave\",\"view_reports\"]', '2025-07-30 22:07:51', '2025-07-30 22:07:51');
INSERT INTO `roles` VALUES (3, 'Supervisor', '[\"approve_leave\",\"view_reports\"]', '2025-07-30 22:07:51', '2025-07-30 22:07:51');
INSERT INTO `roles` VALUES (4, 'Employee', '[]', '2025-07-30 22:07:51', '2025-07-30 22:07:51');

-- ----------------------------
-- Table structure for setting_permissions
-- ----------------------------
DROP TABLE IF EXISTS `setting_permissions`;
CREATE TABLE `setting_permissions`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL COMMENT 'รหัสบทบาท',
  `setting_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'คีย์การตั้งค่า',
  `can_view` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'สามารถดูได้',
  `can_edit` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'สามารถแก้ไขได้',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `unique_role_setting`(`role_id`, `setting_key`) USING BTREE,
  INDEX `idx_role`(`role_id`) USING BTREE,
  INDEX `idx_setting`(`setting_key`) USING BTREE,
  CONSTRAINT `fk_setting_permissions_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 31 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'ตารางสิทธิ์การเข้าถึงการตั้งค่า' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of setting_permissions
-- ----------------------------
INSERT INTO `setting_permissions` VALUES (1, 1, 'org_name', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (2, 1, 'org_address', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (3, 1, 'org_phone', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (4, 1, 'org_email', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (5, 1, 'work_start_time', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (6, 1, 'work_end_time', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (7, 1, 'grace_period_minutes', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (8, 1, 'primary_color', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (9, 1, 'secondary_color', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (10, 1, 'system_timezone', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (11, 1, 'language', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (12, 1, 'session_timeout', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (13, 1, 'max_login_attempts', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (14, 2, 'org_name', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (15, 2, 'org_address', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (16, 2, 'org_phone', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (17, 2, 'org_email', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (18, 2, 'work_start_time', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (19, 2, 'work_end_time', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (20, 2, 'grace_period_minutes', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (21, 2, 'primary_color', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (22, 2, 'secondary_color', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (23, 2, 'system_timezone', 1, 0, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (24, 2, 'language', 1, 0, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (25, 2, 'session_timeout', 1, 0, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (26, 3, 'work_start_time', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (27, 3, 'work_end_time', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (28, 3, 'grace_period_minutes', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (29, 3, 'late_arrival_notification', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `setting_permissions` VALUES (30, 3, 'overtime_notification', 1, 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');

-- ----------------------------
-- Table structure for settings_backup
-- ----------------------------
DROP TABLE IF EXISTS `settings_backup`;
CREATE TABLE `settings_backup`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `backup_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ชื่อการสำรอง',
  `backup_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ข้อมูลการตั้งค่า (JSON)',
  `backup_type` enum('manual','automatic','scheduled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual' COMMENT 'ประเภทการสำรอง',
  `created_by` int NULL DEFAULT NULL COMMENT 'ผู้สำรองข้อมูล',
  `file_size` bigint NULL DEFAULT NULL COMMENT 'ขนาดไฟล์ (bytes)',
  `settings_count` int NULL DEFAULT NULL COMMENT 'จำนวนการตั้งค่า',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'วันหมดอายุ',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_backup_type`(`backup_type`) USING BTREE,
  INDEX `idx_created_by`(`created_by`) USING BTREE,
  INDEX `idx_created_at`(`created_at`) USING BTREE,
  INDEX `idx_expires_at`(`expires_at`) USING BTREE,
  CONSTRAINT `fk_settings_backup_user` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'ตารางสำรองข้อมูลการตั้งค่า' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of settings_backup
-- ----------------------------

-- ----------------------------
-- Table structure for system_settings
-- ----------------------------
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `setting_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `setting_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'text',
  `setting_category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'หมวดหมู่การตั้งค่า',
  `is_sensitive` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ข้อมูลที่ต้องการความปลอดภัย',
  `requires_restart` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ต้องการรีสตาร์ทระบบ',
  `validation_rule` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'กฎการตรวจสอบข้อมูล',
  `default_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'ค่าเริ่มต้น',
  `display_order` int NULL DEFAULT 0 COMMENT 'ลำดับการแสดงผล',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `setting_key`(`setting_key`) USING BTREE,
  INDEX `idx_category`(`setting_category`) USING BTREE,
  INDEX `idx_display_order`(`display_order`) USING BTREE,
  INDEX `idx_system_settings_category_order`(`setting_category`, `display_order`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 406 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_settings
-- ----------------------------
INSERT INTO `system_settings` VALUES (1, 'org_name', 'อ.บ.ต.หนองปากโลง1', 'ชื่อองค์กร', 'text', 'organization', 0, 0, NULL, 'อ.บ.ต.หนองปากโลง', 1, '2025-07-30 22:07:51', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (2, 'work_start_time', '08:30', 'เวลาเริ่มงาน', 'time', 'work_time', 0, 0, NULL, '08:30', 10, '2025-07-30 22:07:51', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (3, 'work_end_time', '17:30', 'เวลาเลิกงาน', 'time', 'work_time', 0, 0, NULL, '17:30', 11, '2025-07-30 22:07:51', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (4, 'grace_period_minutes', '15', 'เวลาผ่อนผัน (นาที)', 'number', 'work_time', 0, 0, NULL, '15', 12, '2025-07-30 22:07:51', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (5, 'ot_start_time', '18:00', 'เวลาเริ่ม OT', 'time', 'work_time', 0, 0, NULL, '18:00', 13, '2025-07-30 22:07:51', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (6, 'org_address', '', 'ที่อยู่องค์กร', 'textarea', 'organization', 0, 0, NULL, '', 2, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (7, 'org_phone', '', 'เบอร์โทรศัพท์', 'tel', 'organization', 0, 0, NULL, '', 3, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (8, 'org_email', '', 'อีเมลองค์กร', 'email', 'organization', 0, 0, NULL, '', 4, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (9, 'org_website', '', 'เว็บไซต์องค์กร', 'url', 'organization', 0, 0, NULL, '', 5, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (10, 'org_tax_id', '', 'เลขประจำตัวผู้เสียภาษี', 'text', 'organization', 0, 0, NULL, '', 6, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (11, 'org_registration_number', '', 'เลขที่จดทะเบียน', 'text', 'organization', 0, 0, NULL, '', 7, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (12, 'break_start_time', '12:00', 'เวลาเริ่มพัก', 'time', 'work_time', 0, 0, NULL, '12:00', 14, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (13, 'break_end_time', '13:00', 'เวลาสิ้นสุดพัก', 'time', 'work_time', 0, 0, NULL, '13:00', 15, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (14, 'lunch_break_duration', '60', 'ระยะเวลาพักกลางวัน (นาที)', 'number', 'work_time', 0, 0, NULL, '60', 16, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (15, 'work_days_per_week', '6', 'วันทำงานต่อสัปดาห์', 'number', 'work_time', 0, 0, NULL, '5', 17, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (16, 'weekend_ot_rate', '2.0', 'อัตรา OT วันหยุดสุดสัปดาห์', 'number', 'work_time', 0, 0, NULL, '2.0', 18, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (17, 'holiday_ot_rate', '3.0', 'อัตรา OT วันหยุดนักขัตฤกษ์', 'number', 'work_time', 0, 0, NULL, '3.0', 19, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (18, 'night_shift_allowance', '500', 'เบี้ยเลี้ยงกะกลางคืน', 'number', 'work_time', 0, 0, NULL, '500', 20, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (19, 'primary_color', '#4f46e5', 'สีหลัก', 'color', 'theme', 0, 0, NULL, '#4f46e5', 30, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (20, 'secondary_color', '#7c3aed', 'สีรอง', 'color', 'theme', 0, 0, NULL, '#7c3aed', 31, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (21, 'accent_color', '#06b6d4', 'สีเน้น', 'color', 'theme', 0, 0, NULL, '#06b6d4', 32, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (22, 'sidebar_bg_color', '#1f2937', 'สีพื้นหลัง Sidebar', 'color', 'theme', 0, 0, NULL, '#1f2937', 33, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (23, 'header_bg_color', '#ffffff', 'สีพื้นหลัง Header', 'color', 'theme', 0, 0, NULL, '#ffffff', 34, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (24, 'logo_position', 'left', 'ตำแหน่งโลโก้', 'select', 'theme', 0, 0, NULL, 'left', 35, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (25, 'theme_mode', 'light', 'โหมดธีม', 'select', 'theme', 0, 0, NULL, 'light', 36, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (26, 'system_timezone', 'Asia/Bangkok', 'เขตเวลา', 'select', 'system', 0, 1, NULL, 'Asia/Bangkok', 40, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (27, 'date_format', 'd/m/Y', 'รูปแบบวันที่', 'select', 'system', 0, 0, NULL, 'd/m/Y', 41, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (28, 'time_format', 'H:i', 'รูปแบบเวลา', 'select', 'system', 0, 0, NULL, 'H:i', 42, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (29, 'language', 'th', 'ภาษา', 'select', 'system', 0, 1, NULL, 'th', 43, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (30, 'currency', 'THB', 'สกุลเงิน', 'select', 'system', 0, 0, NULL, 'THB', 44, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (31, 'decimal_places', '2', 'จำนวนทศนิยม', 'number', 'system', 0, 0, NULL, '2', 45, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (32, 'backup_frequency', 'weekly', 'ความถี่การสำรองข้อมูล', 'select', 'system', 0, 0, NULL, 'daily', 46, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (33, 'auto_logout_time', '30', 'เวลาออกจากระบบอัตโนมัติ (นาที)', 'number', 'system', 0, 0, NULL, '30', 47, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (34, 'system_maintenance_mode', '0', 'โหมดปรับปรุงระบบ', 'boolean', 'system', 0, 1, NULL, '0', 48, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (35, 'debug_mode', '0', 'โหมด Debug', 'boolean', 'system', 0, 1, NULL, '0', 49, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (36, 'enable_email_notifications', '1', 'เปิดการแจ้งเตือนทางอีเมล', 'boolean', 'notifications', 0, 0, NULL, '1', 50, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (37, 'enable_sms_notifications', '0', 'เปิดการแจ้งเตือนทาง SMS', 'boolean', 'notifications', 0, 0, NULL, '0', 51, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (38, 'notification_sound', '1', 'เสียงแจ้งเตือน', 'boolean', 'notifications', 0, 0, NULL, '1', 52, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (39, 'late_arrival_notification', '1', 'แจ้งเตือนการมาสาย', 'boolean', 'notifications', 0, 0, NULL, '1', 53, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (40, 'leave_request_notification', '1', 'แจ้งเตือนคำขอลา', 'boolean', 'notifications', 0, 0, NULL, '1', 54, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (41, 'overtime_notification', '1', 'แจ้งเตือนการทำ OT', 'boolean', 'notifications', 0, 0, NULL, '1', 55, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (42, 'birthday_notification', '1', 'แจ้งเตือนวันเกิด', 'boolean', 'notifications', 0, 0, NULL, '1', 56, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (43, 'contract_expiry_notification', '1', 'แจ้งเตือนสัญญาหมดอายุ', 'boolean', 'notifications', 0, 0, NULL, '1', 57, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (44, 'email_server_host', '', 'เซิร์ฟเวอร์อีเมล', 'text', 'notifications', 0, 0, NULL, '', 58, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (45, 'email_server_port', '587', 'พอร์ตเซิร์ฟเวอร์อีเมล', 'number', 'notifications', 0, 0, NULL, '587', 59, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (46, 'email_server_username', '', 'ชื่อผู้ใช้เซิร์ฟเวอร์อีเมล', 'email', 'notifications', 0, 1, NULL, '', 60, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (47, 'sms_provider', '', 'ผู้ให้บริการ SMS', 'text', 'notifications', 0, 0, NULL, '', 61, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (48, 'sms_api_key', '', 'API Key สำหรับ SMS', 'password', 'notifications', 0, 1, NULL, '', 62, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (49, 'session_timeout', '1800', 'หมดเวลา Session (วินาที)', 'number', 'security', 0, 1, NULL, '1800', 70, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (50, 'max_login_attempts', '6', 'จำนวนครั้งที่พยายาม Login สูงสุด', 'number', 'security', 0, 0, NULL, '5', 71, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (51, 'password_min_length', '8', 'ความยาวรหัสผ่านขั้นต่ำ', 'number', 'security', 0, 0, NULL, '8', 72, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (52, 'password_require_special', '1', 'ต้องมีอักขระพิเศษในรหัสผ่าน', 'boolean', 'security', 0, 0, NULL, '1', 73, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (53, 'password_require_uppercase', '1', 'ต้องมีตัวพิมพ์ใหญ่ในรหัสผ่าน', 'boolean', 'security', 0, 0, NULL, '1', 74, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (54, 'password_require_numbers', '1', 'ต้องมีตัวเลขในรหัสผ่าน', 'boolean', 'security', 0, 0, NULL, '1', 75, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (55, 'two_factor_auth', '0', 'การยืนยันตัวตนสองชั้น', 'boolean', 'security', 0, 1, NULL, '0', 76, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (56, 'ip_whitelist', '', 'รายการ IP ที่อนุญาต', 'textarea', 'security', 0, 1, NULL, '', 77, '2025-07-31 14:49:10', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (57, 'failed_login_lockout_time', '300', 'เวลาล็อคบัญชีเมื่อ Login ผิด (วินาที)', 'number', 'security', 0, 0, NULL, '300', 78, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (58, 'password_expiry_days', '90', 'วันหมดอายุรหัสผ่าน', 'number', 'security', 0, 0, NULL, '90', 79, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (59, 'force_password_change', '0', 'บังคับเปลี่ยนรหัสผ่าน', 'boolean', 'security', 0, 0, NULL, '0', 80, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `system_settings` VALUES (174, 'favicon', 'uploads/favicon/favicon_1753981923.png', 'Favicon', 'file', NULL, 0, 0, NULL, NULL, 0, '2025-08-01 00:12:03', '2025-08-01 00:12:03');
INSERT INTO `system_settings` VALUES (175, 'login_bg_image', 'uploads/login_bg_image/login_bg_image_1754032069.jpg', 'รูปพื้นหลังหน้า Login', 'file', NULL, 0, 0, NULL, NULL, 0, '2025-08-01 00:12:03', '2025-08-01 14:07:49');
INSERT INTO `system_settings` VALUES (328, 'org_logo', 'uploads/org_logo/org_logo_1754016310.png', 'Org logo', 'file', NULL, 0, 0, NULL, NULL, 0, '2025-08-01 09:45:10', '2025-08-01 09:45:10');

-- ----------------------------
-- Table structure for work_shifts
-- ----------------------------
DROP TABLE IF EXISTS `work_shifts`;
CREATE TABLE `work_shifts`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `shift_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ชื่อกะ',
  `start_time` time NOT NULL COMMENT 'เวลาเริ่มงาน',
  `end_time` time NOT NULL COMMENT 'เวลาเลิกงาน',
  `break_start` time NULL DEFAULT NULL COMMENT 'เวลาเริ่มพัก',
  `break_end` time NULL DEFAULT NULL COMMENT 'เวลาสิ้นสุดพัก',
  `work_days` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1,2,3,4,5' COMMENT 'วันทำงาน (1=จันทร์, 7=อาทิตย์)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'สถานะใช้งาน',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `shift_name`(`shift_name`) USING BTREE,
  INDEX `idx_active`(`is_active`) USING BTREE,
  INDEX `idx_work_days`(`work_days`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'ตารางกะการทำงาน' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of work_shifts
-- ----------------------------
INSERT INTO `work_shifts` VALUES (1, 'กะปกติ (08:30-17:30)', '08:30:00', '17:30:00', '12:00:00', '13:00:00', '1,2,3,4,5', 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `work_shifts` VALUES (2, 'กะเช้า (06:00-14:00)', '06:00:00', '14:00:00', '10:00:00', '10:30:00', '1,2,3,4,5', 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `work_shifts` VALUES (3, 'กะบ่าย (14:00-22:00)', '14:00:00', '22:00:00', '18:00:00', '19:00:00', '1,2,3,4,5', 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `work_shifts` VALUES (4, 'กะดึก (22:00-06:00)', '22:00:00', '06:00:00', '02:00:00', '02:30:00', '1,2,3,4,5', 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');
INSERT INTO `work_shifts` VALUES (5, 'กะสุดสัปดาห์', '09:00:00', '18:00:00', '12:30:00', '13:30:00', '6,7', 1, '2025-07-31 14:49:10', '2025-07-31 14:49:10');

-- ----------------------------
-- View structure for v_leave_policies_detail
-- ----------------------------
DROP VIEW IF EXISTS `v_leave_policies_detail`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `v_leave_policies_detail` AS SELECT 
    lp.id,
    lp.leave_type_id,
    lt.name as leave_type_name,
    lt.is_paid as leave_type_is_paid,
    lp.days_allowed_per_year,
    lp.is_unlimited,
    lp.can_be_carried_over,
    lp.max_carry_over_days,
    lp.min_notice_days,
    lp.max_consecutive_days,
    lp.requires_approval,
    lp.description,
    lp.created_at,
    lp.updated_at,
    CASE 
        WHEN lp.is_unlimited = 1 THEN 'ไม่จำกัด'
        ELSE CONCAT(lp.days_allowed_per_year, ' วัน')
    END as days_display,
    CASE 
        WHEN lp.can_be_carried_over = 1 THEN CONCAT('ได้ (สูงสุด ', lp.max_carry_over_days, ' วัน)')
        ELSE 'ไม่ได้'
    END as carry_over_display,
    CASE 
        WHEN lp.requires_approval = 1 THEN 'ต้องอนุมัติ'
        ELSE 'ไม่ต้องอนุมัติ'
    END as approval_display
FROM leave_policies lp
LEFT JOIN leave_types lt ON lp.leave_type_id = lt.id
ORDER BY lt.name ASC ;

-- ----------------------------
-- Function structure for fn_check_leave_policy_compliance
-- ----------------------------
DROP FUNCTION IF EXISTS `fn_check_leave_policy_compliance`;
delimiter ;;
CREATE FUNCTION `fn_check_leave_policy_compliance`(p_leave_type_id INT,
    p_requested_days INT,
    p_notice_days INT,
    p_consecutive_days INT)
 RETURNS longtext CHARSET utf8mb4 COLLATE utf8mb4_bin
  READS SQL DATA 
  DETERMINISTIC
BEGIN
    DECLARE v_days_allowed INT DEFAULT 0;
    DECLARE v_is_unlimited TINYINT DEFAULT 0;
    DECLARE v_min_notice INT DEFAULT 0;
    DECLARE v_max_consecutive INT DEFAULT 0;
    DECLARE v_requires_approval TINYINT DEFAULT 0;
    DECLARE v_result JSON;
    
    -- Get policy details
    SELECT 
        days_allowed_per_year,
        is_unlimited,
        min_notice_days,
        max_consecutive_days,
        requires_approval
    INTO 
        v_days_allowed,
        v_is_unlimited,
        v_min_notice,
        v_max_consecutive,
        v_requires_approval
    FROM leave_policies 
    WHERE leave_type_id = p_leave_type_id;
    
    -- Check compliance
    SET v_result = JSON_OBJECT(
        'is_compliant', (
            (v_is_unlimited = 1 OR p_requested_days <= v_days_allowed) AND
            (p_notice_days >= v_min_notice) AND
            (p_consecutive_days <= v_max_consecutive)
        ),
        'days_allowed', v_days_allowed,
        'is_unlimited', v_is_unlimited,
        'min_notice_required', v_min_notice,
        'max_consecutive_allowed', v_max_consecutive,
        'requires_approval', v_requires_approval,
        'violations', JSON_ARRAY(
            CASE WHEN v_is_unlimited = 0 AND p_requested_days > v_days_allowed 
                 THEN CONCAT('วันลาเกินกำหนด (สูงสุด ', v_days_allowed, ' วัน)')
                 ELSE NULL END,
            CASE WHEN p_notice_days < v_min_notice 
                 THEN CONCAT('แจ้งล่วงหน้าไม่เพียงพอ (ต้อง ', v_min_notice, ' วัน)')
                 ELSE NULL END,
            CASE WHEN p_consecutive_days > v_max_consecutive 
                 THEN CONCAT('ลาติดต่อกันเกินกำหนด (สูงสุด ', v_max_consecutive, ' วัน)')
                 ELSE NULL END
        )
    );
    
    RETURN v_result;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for sp_get_leave_policies_stats
-- ----------------------------
DROP PROCEDURE IF EXISTS `sp_get_leave_policies_stats`;
delimiter ;;
CREATE PROCEDURE `sp_get_leave_policies_stats`()
BEGIN
    SELECT 
        COUNT(*) as total_policies,
        COUNT(CASE WHEN is_unlimited = 1 THEN 1 END) as unlimited_policies,
        COUNT(CASE WHEN can_be_carried_over = 1 THEN 1 END) as carry_over_policies,
        COUNT(CASE WHEN requires_approval = 1 THEN 1 END) as approval_policies,
        AVG(days_allowed_per_year) as avg_days_per_year,
        AVG(min_notice_days) as avg_notice_days,
        AVG(max_consecutive_days) as avg_consecutive_days
    FROM leave_policies;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for sp_get_leave_policy_by_type
-- ----------------------------
DROP PROCEDURE IF EXISTS `sp_get_leave_policy_by_type`;
delimiter ;;
CREATE PROCEDURE `sp_get_leave_policy_by_type`(IN p_leave_type_id INT)
BEGIN
    SELECT * FROM v_leave_policies_detail 
    WHERE leave_type_id = p_leave_type_id;
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table leave_policies
-- ----------------------------
DROP TRIGGER IF EXISTS `tr_leave_policies_insert`;
delimiter ;;
CREATE TRIGGER `tr_leave_policies_insert` AFTER INSERT ON `leave_policies` FOR EACH ROW BEGIN
    INSERT INTO audit_logs (
        table_name, 
        record_id, 
        action, 
        old_values, 
        new_values, 
        user_id, 
        created_at
    ) VALUES (
        'leave_policies',
        NEW.id,
        'INSERT',
        NULL,
        JSON_OBJECT(
            'leave_type_id', NEW.leave_type_id,
            'days_allowed_per_year', NEW.days_allowed_per_year,
            'is_unlimited', NEW.is_unlimited,
            'can_be_carried_over', NEW.can_be_carried_over,
            'max_carry_over_days', NEW.max_carry_over_days,
            'min_notice_days', NEW.min_notice_days,
            'max_consecutive_days', NEW.max_consecutive_days,
            'requires_approval', NEW.requires_approval,
            'description', NEW.description
        ),
        @current_user_id,
        NOW()
    );
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table leave_policies
-- ----------------------------
DROP TRIGGER IF EXISTS `tr_leave_policies_update`;
delimiter ;;
CREATE TRIGGER `tr_leave_policies_update` AFTER UPDATE ON `leave_policies` FOR EACH ROW BEGIN
    INSERT INTO audit_logs (
        table_name, 
        record_id, 
        action, 
        old_values, 
        new_values, 
        user_id, 
        created_at
    ) VALUES (
        'leave_policies',
        NEW.id,
        'UPDATE',
        JSON_OBJECT(
            'leave_type_id', OLD.leave_type_id,
            'days_allowed_per_year', OLD.days_allowed_per_year,
            'is_unlimited', OLD.is_unlimited,
            'can_be_carried_over', OLD.can_be_carried_over,
            'max_carry_over_days', OLD.max_carry_over_days,
            'min_notice_days', OLD.min_notice_days,
            'max_consecutive_days', OLD.max_consecutive_days,
            'requires_approval', OLD.requires_approval,
            'description', OLD.description
        ),
        JSON_OBJECT(
            'leave_type_id', NEW.leave_type_id,
            'days_allowed_per_year', NEW.days_allowed_per_year,
            'is_unlimited', NEW.is_unlimited,
            'can_be_carried_over', NEW.can_be_carried_over,
            'max_carry_over_days', NEW.max_carry_over_days,
            'min_notice_days', NEW.min_notice_days,
            'max_consecutive_days', NEW.max_consecutive_days,
            'requires_approval', NEW.requires_approval,
            'description', NEW.description
        ),
        @current_user_id,
        NOW()
    );
END
;;
delimiter ;

-- ----------------------------
-- Triggers structure for table leave_policies
-- ----------------------------
DROP TRIGGER IF EXISTS `tr_leave_policies_delete`;
delimiter ;;
CREATE TRIGGER `tr_leave_policies_delete` AFTER DELETE ON `leave_policies` FOR EACH ROW BEGIN
    INSERT INTO audit_logs (
        table_name, 
        record_id, 
        action, 
        old_values, 
        new_values, 
        user_id, 
        created_at
    ) VALUES (
        'leave_policies',
        OLD.id,
        'DELETE',
        JSON_OBJECT(
            'leave_type_id', OLD.leave_type_id,
            'days_allowed_per_year', OLD.days_allowed_per_year,
            'is_unlimited', OLD.is_unlimited,
            'can_be_carried_over', OLD.can_be_carried_over,
            'max_carry_over_days', OLD.max_carry_over_days,
            'min_notice_days', OLD.min_notice_days,
            'max_consecutive_days', OLD.max_consecutive_days,
            'requires_approval', OLD.requires_approval,
            'description', OLD.description
        ),
        NULL,
        @current_user_id,
        NOW()
    );
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
