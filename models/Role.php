<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class Role extends BaseModel {
    protected $table = "roles";
    public function findBySlug($slug) { return $this->findOne(["slug" => $slug]); }
    public function getPermissions($roleId) {
        $role = $this->findById($roleId);
        return $role ? json_decode($role["permissions"], true) : [];
    }
    public function hasPermission($roleId, $permission) {
        $perms = $this->getPermissions($roleId);
        return in_array($permission, $perms);
    }
    public function getAllRoles() { return $this->findAll([], "id ASC"); }
}
