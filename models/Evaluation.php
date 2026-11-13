<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class Evaluation extends BaseModel {
    protected $table = "evaluations";
    public function findByApplicationId($appId) {
        return $this->query("SELECT e.*, a.first_name as eval_first, a.last_name as eval_last FROM evaluations e LEFT JOIN admins a ON e.evaluator_admin_id = a.id WHERE e.application_id = ? ORDER BY e.evaluated_at DESC LIMIT 1", [$appId])->fetch();
    }
    public function getEvaluationHistory($appId) {
        return $this->query("SELECT e.*, a.first_name as eval_first, a.last_name as eval_last FROM evaluations e LEFT JOIN admins a ON e.evaluator_admin_id = a.id WHERE e.application_id = ? ORDER BY e.evaluated_at DESC", [$appId])->fetchAll();
    }
    public function saveEvaluation($data) { return $this->insert($data); }
}
