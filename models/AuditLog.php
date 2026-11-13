<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class AuditLog extends BaseModel {
    protected $table = "audit_logs";
    public function log($adminId, $action, $table, $recordId, $field, $oldVal, $newVal, $desc, $studentId = null) {
        return $this->insert(["admin_id" => $adminId, "student_id" => $studentId, "action" => $action, "table_name" => $table, "record_id" => $recordId, "field_name" => $field, "old_value" => $oldVal, "new_value" => $newVal, "description" => $desc, "ip_address" => $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1"]);
    }
    public function getAll($filters = [], $page = 1, $perPage = 50) {
        $sql = "SELECT al.*, a.first_name as admin_first, a.last_name as admin_last FROM audit_logs al LEFT JOIN admins a ON al.admin_id = a.id WHERE 1=1";
        $params = [];
        if (!empty($filters["action"])) { $sql .= " AND al.action = ?"; $params[] = $filters["action"]; }
        if (!empty($filters["date_from"])) { $sql .= " AND DATE(al.created_at) >= ?"; $params[] = $filters["date_from"]; }
        $sql .= " ORDER BY al.created_at DESC LIMIT $perPage OFFSET " . (($page - 1) * $perPage);
        return $this->query($sql, $params)->fetchAll();
    }
    public function countAll($filters = []) {
        $sql = "SELECT COUNT(*) FROM audit_logs WHERE 1=1"; $params = [];
        if (!empty($filters["action"])) { $sql .= " AND action = ?"; $params[] = $filters["action"]; }
        return (int)$this->query($sql, $params)->fetchColumn();
    }
    public function getByApplication($appId) {
        $tables = "applications,scholarship_applications,academic_scholar_applications,student_assistant_applications";
        $sql = "SELECT al.*, a.first_name as admin_first, a.last_name as admin_last FROM audit_logs al LEFT JOIN admins a ON al.admin_id = a.id WHERE al.record_id = ? ORDER BY al.created_at DESC";
        return $this->query($sql, [$appId])->fetchAll();
    }
}