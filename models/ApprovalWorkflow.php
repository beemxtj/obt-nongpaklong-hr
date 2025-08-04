<?php
// models/ApprovalWorkflow.php

class ApprovalWorkflow {
    private $conn;
    private $workflows_table = "approval_workflows";
    private $steps_table = "approval_workflow_steps";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * สร้าง Workflow หลัก และคืนค่า ID ที่สร้างใหม่
     * @param string $name ชื่อของ Workflow
     * @return int|false ID ของ Workflow ที่สร้าง หรือ false ถ้าล้มเหลว
     */
    public function createWorkflow($name) {
        $query = "INSERT INTO " . $this->workflows_table . " (name) VALUES (:name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * เพิ่มขั้นตอน (Step) เข้าไปใน Workflow
     * @param int $workflow_id ID ของ Workflow
     * @param int $step_number ลำดับของขั้นตอน
     * @param string $role บทบาทของผู้อนุมัติ (SUPERVISOR, HR, MANAGER)
     * @return bool
     */
    public function addStep($workflow_id, $step_number, $role) {
        $query = "INSERT INTO " . $this->steps_table . " (workflow_id, step_number, approver_role) VALUES (:workflow_id, :step_number, :role)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':workflow_id', $workflow_id);
        $stmt->bindParam(':step_number', $step_number);
        $stmt->bindParam(':role', $role);
        return $stmt->execute();
    }

    /**
     * อ่านข้อมูล Workflow ทั้งหมดพร้อมขั้นตอน
     * @return array
     */
    public function readAll() {
        $query = "SELECT 
                    w.id, 
                    w.name,
                    GROUP_CONCAT(s.approver_role ORDER BY s.step_number ASC SEPARATOR ' -> ') as steps_summary
                  FROM 
                    " . $this->workflows_table . " w
                  LEFT JOIN 
                    " . $this->steps_table . " s ON w.id = s.workflow_id
                  GROUP BY
                    w.id, w.name
                  ORDER BY
                    w.name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * อ่านข้อมูล Workflow เดียว
     * @param int $id ID ของ Workflow
     * @return array|null
     */
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->workflows_table . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * ดึงขั้นตอนทั้งหมดของ Workflow ที่ระบุ
     * @param int $workflow_id ID ของ Workflow
     * @return array
     */
    public function getSteps($workflow_id) {
        $query = "SELECT * FROM " . $this->steps_table . " WHERE workflow_id = ? ORDER BY step_number ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $workflow_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * อัปเดตชื่อของ Workflow
     * @param int $id ID ของ Workflow
     * @param string $name ชื่อใหม่
     * @return bool
     */
    public function updateWorkflow($id, $name) {
        $query = "UPDATE " . $this->workflows_table . " SET name = :name WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * ลบขั้นตอนทั้งหมดของ Workflow ที่ระบุ
     * @param int $workflow_id ID ของ Workflow
     * @return bool
     */
    public function clearSteps($workflow_id) {
        $query = "DELETE FROM " . $this->steps_table . " WHERE workflow_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $workflow_id);
        return $stmt->execute();
    }

    /**
     * ลบ Workflow (ขั้นตอนจะถูกลบไปด้วย ON DELETE CASCADE)
     * @param int $id ID ของ Workflow
     * @return bool
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->workflows_table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
}
