<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class Document extends BaseModel {
    protected $table = "documents";
    public function findByApplicationId($appId) { return $this->findAll(["application_id" => $appId], "uploaded_at DESC"); }
    public function findByReqAndApp($appId, $reqId) { return $this->findOne(["application_id" => $appId, "requirement_id" => $reqId]); }
    public function updateStatus($docId, $status, $remarks = "") {
        return $this->update($docId, ["upload_status" => $status, "remarks" => $remarks]);
    }
    public function deleteDocument($docId) {
        $doc = $this->findById($docId);
        if ($doc && file_exists($doc["file_path"])) unlink($doc["file_path"]);
        return $this->delete($docId);
    }
    public function getDocumentsWithRequirements($appId) {
        return $this->query("SELECT d.*, r.name as requirement_name, r.is_required FROM documents d LEFT JOIN requirements r ON d.requirement_id = r.id WHERE d.application_id = ? ORDER BY r.display_order ASC", [$appId])->fetchAll();
    }
    public function saveDocument($data) { return $this->insert($data); }
}
