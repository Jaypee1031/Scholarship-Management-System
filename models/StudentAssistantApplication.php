<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class StudentAssistantApplication extends BaseModel {
    protected $table = "student_assistant_applications";
    public function findByApplicationId($appId) { return $this->findOne(["application_id" => $appId]); }
    public function saveOrUpdate($appId, $data) {
        $existing = $this->findByApplicationId($appId);
        $data["application_id"] = $appId;
        if ($existing) { return $this->update($existing["id"], $data); }
        else { return $this->insert($data); }
    }
    public function getWithPosition($appId) {
        return $this->query("SELECT sa.*, p.title as position_title, p.office as position_office, p.schedule as position_schedule FROM student_assistant_applications sa LEFT JOIN sa_positions p ON sa.preferred_position_id = p.id WHERE sa.application_id = ?", [$appId])->fetch();
    }
}
