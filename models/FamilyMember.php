<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class FamilyMember extends BaseModel {
    protected $table = "family_members";
    public function findByApplicationId($appId) { return $this->findAll(["application_id" => $appId], "FIELD(relationship,\"Father\",\"Mother\",\"Sibling\") ASC"); }
    public function saveFamilyData($appId, $membersArray) {
        $this->db->prepare("DELETE FROM family_members WHERE application_id = ?")->execute([$appId]);
        foreach ($membersArray as $member) {
            if (empty($member["first_name"]) && empty($member["last_name"])) continue;
            $member["application_id"] = $appId;
            $this->insert($member);
        }
    }
    public function getFather($appId) { return $this->query("SELECT * FROM family_members WHERE application_id = ? AND relationship = ?", [$appId, "Father"])->fetch(); }
    public function getMother($appId) { return $this->query("SELECT * FROM family_members WHERE application_id = ? AND relationship = ?", [$appId, "Mother"])->fetch(); }
    public function getSiblings($appId) { return $this->query("SELECT * FROM family_members WHERE application_id = ? AND relationship = ?", [$appId, "Sibling"])->fetchAll(); }
}
