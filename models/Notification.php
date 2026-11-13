<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class Notification extends BaseModel {
    protected $table = "notifications";
    public function findByStudentId($studentId, $unreadOnly = false, $limit = 50) {
        $sql = "SELECT n.*, a.application_number FROM notifications n LEFT JOIN applications a ON n.related_application_id = a.id WHERE n.student_id = ?";
        $params = [$studentId];
        if ($unreadOnly) { $sql .= " AND n.is_read = 0"; }
        $sql .= " ORDER BY n.created_at DESC LIMIT $limit";
        return $this->query($sql, $params)->fetchAll();
    }
    public function countUnread($studentId) {
        return (int)$this->query("SELECT COUNT(*) FROM notifications WHERE student_id = ? AND is_read = 0", [$studentId])->fetchColumn();
    }
    public function markRead($id, $studentId) { return $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND student_id = ?")->execute([$id, $studentId]); }
    public function markAllRead($studentId) { return $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE student_id = ?")->execute([$studentId]); }
    public function sendToStudent($studentId, $title, $message, $type = "general", $appId = null) {
        return $this->insert(["student_id" => $studentId, "title" => $title, "message" => $message, "notification_type" => $type, "related_application_id" => $appId]);
    }
    public function sendToAllStudents($title, $message, $type = "general") {
        $students = $this->query("SELECT id FROM students")->fetchAll();
        foreach ($students as $s) $this->sendToStudent($s["id"], $title, $message, $type);
    }
    public function sendToTypeApplicants($typeSlug, $title, $message, $type = "general") {
        $stmt = $this->query("SELECT DISTINCT a.student_id FROM applications a JOIN application_types at ON a.application_type_id = at.id WHERE at.slug = ?", [$typeSlug]);
        foreach ($stmt->fetchAll() as $row) $this->sendToStudent($row["student_id"], $title, $message, $type);
    }
}
