<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class ApplicationPeriod extends BaseModel {
    protected $table = "application_periods";
    public function getActive() { return $this->findOne(["is_active" => 1]); }
    public function setActive($id) {
        $this->db->prepare("UPDATE application_periods SET is_active = 0")->execute();
        return $this->update($id, ["is_active" => 1]);
    }
    public function getAllPeriods() { return $this->findAll([], "created_at DESC"); }
    public function createPeriod($data) {
        $this->db->prepare("UPDATE application_periods SET is_active = 0")->execute();
        $data["is_active"] = 1;
        return $this->insert($data);
    }
}
