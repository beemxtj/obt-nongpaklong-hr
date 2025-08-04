<?php
// controllers/ApprovalWorkflowController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/ApprovalWorkflow.php'; // **ต้องสร้าง Model นี้**
require_once __DIR__ . '/../config/database.php';

class ApprovalWorkflowController {

    private $db;
    private $workflow_model;
    private $available_roles = ['SUPERVISOR', 'HR', 'MANAGER']; // บทบาทที่สามารถเป็นผู้อนุมัติได้

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        // จำกัดสิทธิ์ให้เฉพาะ Admin หรือ HR เท่านั้น
        if (!in_array($_SESSION['role_id'], [1, 2])) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        
        $database = new Database();
        $this->db = $database->getConnection();
        $this->workflow_model = new ApprovalWorkflow($this->db);
    }

    /**
     * แสดงรายการสายการอนุมัติทั้งหมด
     */
    public function index() {
        $page_title = "จัดการสายการอนุมัติ";
        $workflows = $this->workflow_model->readAll();
        require_once 'views/settings/workflows/index.php'; // **ต้องสร้าง View นี้**
    }

    /**
     * แสดงฟอร์มสำหรับสร้างสายการอนุมัติใหม่
     */
    public function create() {
        $page_title = "สร้างสายการอนุมัติใหม่";
        $workflow = null; // ส่งค่า null สำหรับฟอร์มสร้างใหม่
        $steps = [];
        $available_roles = $this->available_roles;
        require_once 'views/settings/workflows/form.php'; // **ต้องสร้าง View นี้**
    }

    /**
     * บันทึกสายการอนุมัติใหม่ลงฐานข้อมูล
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['workflow_name'];
            $steps = $_POST['steps'] ?? [];

            // ใช้ Transaction เพื่อให้แน่ใจว่าข้อมูลจะถูกบันทึกครบถ้วนหรือไม่ก็ไม่บันทึกเลย
            $this->db->beginTransaction();
            try {
                // 1. สร้าง Workflow หลักก่อน
                $workflow_id = $this->workflow_model->createWorkflow($name);
                if (!$workflow_id) {
                    throw new Exception("ไม่สามารถสร้าง Workflow หลักได้");
                }

                // 2. เพิ่มขั้นตอน (Steps) ทีละขั้น
                foreach ($steps as $step_number => $role) {
                    $this->workflow_model->addStep($workflow_id, $step_number, $role);
                }

                $this->db->commit();
                $_SESSION['success_message'] = "สร้างสายการอนุมัติสำเร็จ";
                header('Location: ' . BASE_URL . '/workflow');
                exit();

            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['error_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
                header('Location: ' . BASE_URL . '/workflow/create');
                exit();
            }
        }
    }

    /**
     * แสดงฟอร์มสำหรับแก้ไขสายการอนุมัติ
     */
    public function edit($id) {
        $page_title = "แก้ไขสายการอนุมัติ";
        $workflow = $this->workflow_model->readOne($id);
        if (!$workflow) {
            // Handle not found
            header('Location: ' . BASE_URL . '/workflow');
            exit();
        }
        $steps = $this->workflow_model->getSteps($id);
        $available_roles = $this->available_roles;
        require_once 'views/settings/workflows/form.php';
    }

    /**
     * อัปเดตข้อมูลสายการอนุมัติ
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['workflow_name'];
            $steps = $_POST['steps'] ?? [];

            $this->db->beginTransaction();
            try {
                // 1. อัปเดตชื่อ Workflow
                $this->workflow_model->updateWorkflow($id, $name);

                // 2. ลบขั้นตอนเดิมทั้งหมด
                $this->workflow_model->clearSteps($id);

                // 3. เพิ่มขั้นตอนใหม่ทั้งหมด
                foreach ($steps as $step_number => $role) {
                    $this->workflow_model->addStep($id, $step_number, $role);
                }

                $this->db->commit();
                $_SESSION['success_message'] = "อัปเดตสายการอนุมัติสำเร็จ";
                header('Location: ' . BASE_URL . '/workflow');
                exit();

            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการอัปเดต: " . $e->getMessage();
                header('Location: ' . BASE_URL . '/workflow/edit/' . $id);
                exit();
            }
        }
    }

    /**
     * ลบสายการอนุมัติ
     */
    public function destroy($id) {
        if ($this->workflow_model->delete($id)) {
            $_SESSION['success_message'] = "ลบสายการอนุมัติสำเร็จ";
        } else {
            $_SESSION['error_message'] = "ไม่สามารถลบสายการอนุมัติได้";
        }
        header('Location: ' . BASE_URL . '/workflow');
        exit();
    }
}