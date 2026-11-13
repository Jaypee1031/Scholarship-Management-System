<?php
require_once __DIR__ . "/../lib/BaseController.php";
require_once __DIR__ . "/../models/Application.php";
require_once __DIR__ . "/../models/Evaluation.php";
require_once __DIR__ . "/../models/Document.php";
require_once __DIR__ . "/../models/Requirement.php";
require_once __DIR__ . "/../models/ScholarshipApplication.php";
require_once __DIR__ . "/../models/AcademicScholarApplication.php";
require_once __DIR__ . "/../models/StudentAssistantApplication.php";
require_once __DIR__ . "/../models/FamilyMember.php";
require_once __DIR__ . "/../models/EducationalAssistance.php";

class EvaluationController extends BaseController {
    public function evaluate() {
        requireAdmin();
        if (!hasPermission("evaluate_applications")) { flash("error", "Access denied.", "danger"); redirect(BASE_URL . "/admin/applications"); return; }
        $id = (int)$this->getGet("id");
        if (!$id) { redirect(BASE_URL . "/admin/applications"); return; }
        $appModel = new Application();
        $appDetails = $appModel->getApplicationWithDetails($id);
        if (!$appDetails) { flash("error", "Application not found.", "danger"); redirect(BASE_URL . "/admin/applications"); return; }
        $typeSlug = $appDetails["type_slug"];
        $formData = null; $familyMembers = []; $eduAssistance = [];
        if ($typeSlug === "scholarship") { $formData = (new ScholarshipApplication())->findByApplicationId($id); $familyMembers = (new FamilyMember())->findByApplicationId($id); $eduAssistance = (new EducationalAssistance())->findByApplicationId($id); }
        elseif ($typeSlug === "academic_achiever") { $formData = (new AcademicScholarApplication())->findByApplicationId($id); }
        elseif ($typeSlug === "student_assistant") { $formData = (new StudentAssistantApplication())->getWithPosition($id); }
        $documents = (new Document())->getDocumentsWithRequirements($id);
        $requirements = (new Requirement())->findByTypeId($appDetails["application_type_id"]);
        $previousEvaluations = (new Evaluation())->getEvaluationHistory($id);
        $this->view("admin/evaluation", ["application" => $appDetails, "formData" => $formData, "familyMembers" => $familyMembers, "eduAssistance" => $eduAssistance, "documents" => $documents, "requirements" => $requirements, "previousEvaluations" => $previousEvaluations, "pageTitle" => "Evaluate Application - " . APP_NAME]);
    }

    public function submitEvaluation() {
        requireAdmin();
        if (!validateCSRF($_POST["csrf_token"] ?? "") || !hasPermission("evaluate_applications")) { redirect(BASE_URL . "/admin/applications"); return; }
        $id = (int)($_POST["application_id"] ?? 0);
        if (!$id) { redirect(BASE_URL . "/admin/applications"); return; }
        $appModel = new Application();
        $app = $appModel->findById($id);
        if (!$app) { redirect(BASE_URL . "/admin/applications"); return; }
        $docsStatus = sanitize($_POST["documents_status"] ?? "");
        $decision = sanitize($_POST["decision"] ?? "");
        $remarks = sanitize($_POST["remarks"] ?? "");
        $disqualReason = sanitize($_POST["disqualification_reason"] ?? "");
        $requiredCorrections = sanitize($_POST["required_corrections"] ?? "");
        if (empty($remarks)) { flash("error", "Remarks are required.", "danger"); redirect(BASE_URL . "/admin/applications/evaluate?id=$id"); return; }
        if ($decision === "Disqualified" && empty($disqualReason)) { flash("error", "Disqualification reason is required.", "danger"); redirect(BASE_URL . "/admin/applications/evaluate?id=$id"); return; }
        if ($decision === "For Revision" && empty($requiredCorrections)) { flash("error", "Required corrections must be specified.", "danger"); redirect(BASE_URL . "/admin/applications/evaluate?id=$id"); return; }
        $evalModel = new Evaluation();
        $evalModel->saveEvaluation(["application_id" => $id, "evaluator_admin_id" => $_SESSION["admin_id"], "documents_status" => $docsStatus ?: null, "decision" => $decision ?: null, "remarks" => $remarks, "disqualification_reason" => $disqualReason, "required_corrections" => $requiredCorrections]);
        if ($decision) {
            $statusMap = ["Qualified" => "Qualified", "Disqualified" => "Disqualified", "For Revision" => "For Revision", "Approved" => "Approved", "Rejected" => "Rejected"];
            if (isset($statusMap[$decision])) {
                $appModel->updateStatus($id, $statusMap[$decision], $_SESSION["admin_id"], null, $remarks);
            }
            if ($docsStatus && !$decision) $appModel->update($id, ["status" => "Under Review", "updated_at" => date("Y-m-d H:i:s")]);
        } else {
            $appModel->update($id, ["status" => "Under Review", "updated_at" => date("Y-m-d H:i:s")]);
        }
        logAudit("EVALUATE", "evaluations", $id, "decision", $app["status"], $decision, "Evaluation submitted. Decision: $decision. Remarks: $remarks");
        flash("success", "Evaluation submitted successfully.", "success");
        redirect(BASE_URL . "/admin/applications/view?id=$id");
    }
}