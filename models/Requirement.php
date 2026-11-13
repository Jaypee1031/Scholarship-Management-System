<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class Requirement extends BaseModel {
    protected $table = "requirements";
    public function findByTypeId($typeId) { return $this->findAll(["application_type_id" => $typeId, "is_active" => 1], "display_order ASC"); }
    public function toggleActive($id) {
        $req = $this->findById($id);
        return $this->update($id, ["is_active" => $req["is_active"] ? 0 : 1]);
    }
}
