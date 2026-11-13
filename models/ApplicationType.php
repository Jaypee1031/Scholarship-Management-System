<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class ApplicationType extends BaseModel {
    protected $table = "application_types";
    public function findBySlug($slug) { return $this->findOne(["slug" => $slug]); }
    public function getEnabled() { return $this->findAll(["is_enabled" => 1], "id ASC"); }
    public function getAllTypes() { return $this->findAll([], "id ASC"); }
    public function toggleEnabled($id, $enabled) { return $this->update($id, ["is_enabled" => $enabled ? 1 : 0, "updated_at" => date("Y-m-d H:i:s")]); }
    public function updateTypeSettings($id, $data) {
        $data["updated_at"] = date("Y-m-d H:i:s");
        return $this->update($id, $data);
    }
    public function isCurrentlyOpen($typeId) {
        $type = $this->findById($typeId);
        if (!$type || !$type["is_enabled"]) return false;
        $now = date("Y-m-d");
        if ($type["start_date"] && $now < $type["start_date"]) return false;
        if ($type["end_date"] && $now > $type["end_date"]) return false;
        return true;
    }
    public function getApplicationCount($typeId) {
        $stmt = $this->query("SELECT COUNT(*) FROM applications WHERE application_type_id = ? AND status != ?", [$typeId, "Draft"]);
        return (int)$stmt->fetchColumn();
    }
}
