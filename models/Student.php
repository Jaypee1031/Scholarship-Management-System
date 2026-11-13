<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class Student extends BaseModel {
    protected $table = "students";
    public function findByStudentId($studentId) { return $this->findOne(["student_id" => $studentId]); }
    public function findByUserId($userId) { return $this->findOne(["user_id" => $userId]); }
    public function getFullName($s) {
        $name = $s["last_name"] . ", " . $s["first_name"];
        if (!empty($s["middle_name"])) $name .= " " . $s["middle_name"][0] . ".";
        if (!empty($s["extension_name"])) $name .= " " . $s["extension_name"];
        return $name;
    }
    public function getStudentWithUser($studentId) {
        $stmt = $this->query("SELECT s.*, u.username, u.email, u.is_active, u.last_login FROM students s JOIN users u ON s.user_id = u.id WHERE s.student_id = ?", [$studentId]);
        return $stmt->fetch();
    }
    public function getAllStudents($filters = [], $page = 1, $perPage = 20) {
        $sql = "SELECT s.*, u.is_active, u.last_login FROM students s JOIN users u ON s.user_id = u.id WHERE 1=1";
        $params = [];
        if (!empty($filters["search"])) { $sql .= " AND (s.student_id LIKE ? OR s.last_name LIKE ? OR s.first_name LIKE ?)"; $like = "%" . $filters["search"] . "%"; $params = array_merge($params, [$like, $like, $like]); }
        if (!empty($filters["program"])) { $sql .= " AND s.degree_program LIKE ?"; $params[] = "%" . $filters["program"] . "%"; }
        $sql .= " ORDER BY s.last_name ASC";
        $offset = ($page - 1) * $perPage;
        $sql .= " LIMIT $perPage OFFSET $offset";
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    public function countStudents($filters = []) {
        $sql = "SELECT COUNT(*) FROM students s JOIN users u ON s.user_id = u.id WHERE 1=1";
        $params = [];
        if (!empty($filters["search"])) { $sql .= " AND (s.student_id LIKE ? OR s.last_name LIKE ? OR s.first_name LIKE ?)"; $like = "%" . $filters["search"] . "%"; $params = array_merge($params, [$like, $like, $like]); }
        $stmt = $this->query($sql, $params);
        return (int)$stmt->fetchColumn();
    }
}
