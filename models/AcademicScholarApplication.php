<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class AcademicScholarApplication extends BaseModel {
    protected $table = "academic_scholar_applications";
    public function findByApplicationId($appId) { return $this->findOne(["application_id" => $appId]); }
    public function saveOrUpdate($appId, $data) {
        $existing = $this->findByApplicationId($appId);
        $data["application_id"] = $appId;
        if ($existing) { return $this->update($existing["id"], $data); }
        else { return $this->insert($data); }
    }
}
