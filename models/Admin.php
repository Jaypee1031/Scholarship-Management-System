<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class Admin extends BaseModel {
    protected $table = "admins";
    public function findByUserId($userId) { return $this->findOne(["user_id" => $userId]); }
    public function getAdminWithRole($adminId) {
        $stmt = $this->query("SELECT a.*, u.username, u.email, u.is_active, u.last_login, r.name as role_name, r.slug as role_slug, r.permissions FROM admins a JOIN users u ON a.user_id = u.id JOIN roles r ON a.role_id = r.id WHERE a.id = ?", [$adminId]);
        return $stmt->fetch();
    }
    public function getAllAdmins() {
        $stmt = $this->query("SELECT a.*, u.username, u.email, u.is_active, u.last_login, r.name as role_name, r.slug as role_slug FROM admins a JOIN users u ON a.user_id = u.id JOIN roles r ON a.role_id = r.id ORDER BY r.id ASC, a.last_name ASC");
        return $stmt->fetchAll();
    }
    public function getFullName($a) {
        if (empty($a)) return "Admin";
        return ($a["first_name"] ?? "") . " " . ($a["last_name"] ?? "");
    }
}
