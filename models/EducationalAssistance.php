<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class EducationalAssistance extends BaseModel {
    protected $table = "educational_assistance";
    public function findByApplicationId($appId) { return $this->findAll(["application_id" => $appId]); }
    public function saveAssistanceData($appId, $assistanceArray) {
        $this->db->prepare("DELETE FROM educational_assistance WHERE application_id = ?")->execute([$appId]);
        foreach ($assistanceArray as $item) {
            if (empty($item["assistance_type"]) && empty($item["institution_agency"])) continue;
            $item["application_id"] = $appId;
            $this->insert($item);
        }
    }
}
