<?php
require_once __DIR__ . "/../lib/BaseController.php";
require_once __DIR__ . "/../models/Application.php";
require_once __DIR__ . "/../models/ApplicationType.php";
require_once __DIR__ . "/../models/ApplicationPeriod.php";
require_once __DIR__ . "/../models/Student.php";
require_once __DIR__ . "/../models/Admin.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Role.php";
require_once __DIR__ . "/../models/Requirement.php";
require_once __DIR__ . "/../models/SaPosition.php";
require_once __DIR__ . "/../models/AuditLog.php";
require_once __DIR__ . "/../models/Notification.php";
require_once __DIR__ . "/../models/ScholarshipApplication.php";
require_once __DIR__ . "/../models/AcademicScholarApplication.php";
require_once __DIR__ . "/../models/StudentAssistantApplication.php";
require_once __DIR__ . "/../models/FamilyMember.php";
require_once __DIR__ . "/../models/EducationalAssistance.php";
require_once __DIR__ . "/../models/Document.php";
require_once __DIR__ . "/../models/Evaluation.php";
require_once __DIR__ . "/../models/SystemSetting.php";

class AdminController extends BaseController {
    private $appModel;
    private $typeModel;

    public function __construct() {
        $this->appModel = new Application();
        $this->typeModel = new ApplicationType();
    }

    public function dashboard() {
        requireAdmin();
        $stats = [];
        $stats["total_students"] = (new Student())->count();
        $stats["total_applications"] = $this->appModel->count();
        $byType = $this->appModel->countByType();
        $stats["sa_count"] = $byType["student_assistant"]["count"] ?? 0;
        $stats["aca_count"] = $byType["academic_achiever"]["count"] ?? 0;
        $stats["sch_count"] = $byType["scholarship"]["count"] ?? 0;
        $byStatus = $this->appModel->countByStatus();
        $stats["pending"] = ($byStatus["Submitted"] ?? 0) + ($byStatus["Under Review"] ?? 0);
        $stats["qualified"] = $byStatus["Qualified"] ?? 0;
        $stats["disqualified"] = $byStatus["Disqualified"] ?? 0;
        $stats["approved"] = $byStatus["Approved"] ?? 0;
        $stats["rejected"] = $byStatus["Rejected"] ?? 0;
        $stats["by_status"] = $byStatus;
        $recentApps = $this->appModel->getRecentApplications(10);
        $activePeriod = (new ApplicationPeriod())->getActive();
        $applicationTypes = $this->typeModel->getAllTypes();
        foreach ($applicationTypes as &$type) $type["app_count"] = $this->typeModel->getApplicationCount($type["id"]);
        $this->view("admin/dashboard", ["stats" => $stats, "recentApps" => $recentApps, "activePeriod" => $activePeriod, "applicationTypes" => $applicationTypes, "pageTitle" => "Admin Dashboard - " . APP_NAME]);
    }

    public function applications() {
        requireAdmin();
        $filters = ["type_slug" => $this->getGet("type_slug"), "status" => $this->getGet("status"), "year_level" => $this->getGet("year_level"), "search" => $this->getGet("search"), "program" => $this->getGet("program"), "date_from" => $this->getGet("date_from"), "date_to" => $this->getGet("date_to")];
        $page = max(1, (int)$this->getGet("page", 1));
        $perPage = 20;
        $applications = $this->appModel->getAllApplications($filters, $page, $perPage);
        $total = $this->appModel->countApplications($filters);
        $totalPages = ceil($total / $perPage);
        $applicationTypes = $this->typeModel->getAllTypes();
        $admins = (new Admin())->getAllAdmins();
        $this->view("admin/applications", ["applications" => $applications, "total" => $total, "currentPage" => $page, "totalPages" => $totalPages, "filters" => $filters, "applicationTypes" => $applicationTypes, "admins" => $admins, "pageTitle" => "Application Management - " . APP_NAME]);
    }

    public function viewApplication() {
        requireAdmin();
        $id = (int)$this->getGet("id");
        if (!$id) { redirect(BASE_URL . "/admin/applications"); return; }
        $appDetails = $this->appModel->getApplicationWithDetails($id);
        if (!$appDetails) { flash("error", "Application not found.", "danger"); redirect(BASE_URL . "/admin/applications"); return; }
        $typeSlug = $appDetails["type_slug"];
        $formData = null; $familyMembers = []; $eduAssistance = [];
        if ($typeSlug === "scholarship") {
            $formData = (new ScholarshipApplication())->findByApplicationId($id);
            $familyMembers = (new FamilyMember())->findByApplicationId($id);
            $eduAssistance = (new EducationalAssistance())->findByApplicationId($id);
        } elseif ($typeSlug === "academic_achiever") {
            $formData = (new AcademicScholarApplication())->findByApplicationId($id);
        } elseif ($typeSlug === "student_assistant") {
            $formData = (new StudentAssistantApplication())->getWithPosition($id);
        }
        $documents = (new Document())->getDocumentsWithRequirements($id);
        $requirements = (new Requirement())->findByTypeId($appDetails["application_type_id"]);
        $evaluations = (new Evaluation())->getEvaluationHistory($id);
        $statusHistory = $this->appModel->getStatusHistory($id);
        $auditLogs = (new AuditLog())->getByApplication($id);
        $admins = (new Admin())->getAllAdmins();
        $this->view("admin/application_view", ["application" => $appDetails, "formData" => $formData, "familyMembers" => $familyMembers, "eduAssistance" => $eduAssistance, "documents" => $documents, "requirements" => $requirements, "evaluations" => $evaluations, "statusHistory" => $statusHistory, "auditLogs" => $auditLogs, "admins" => $admins, "pageTitle" => "View Application - " . APP_NAME]);
    }

    public function editApplication() {
        requireAdmin();
        if (!hasPermission("edit_applications")) { flash("error", "Access denied.", "danger"); redirect(BASE_URL . "/admin/applications"); return; }
        $id = (int)$this->getGet("id");
        $appDetails = $this->appModel->getApplicationWithDetails($id);
        if (!$appDetails) { redirect(BASE_URL . "/admin/applications"); return; }
        $typeSlug = $appDetails["type_slug"];
        $formData = null; $familyMembers = []; $eduAssistance = [];
        if ($typeSlug === "scholarship") { $formData = (new ScholarshipApplication())->findByApplicationId($id); $familyMembers = (new FamilyMember())->findByApplicationId($id); $eduAssistance = (new EducationalAssistance())->findByApplicationId($id); }
        elseif ($typeSlug === "academic_achiever") { $formData = (new AcademicScholarApplication())->findByApplicationId($id); }
        elseif ($typeSlug === "student_assistant") { $formData = (new StudentAssistantApplication())->getWithPosition($id); }
        $saPositions = (new SaPosition())->getAll();
        $this->view("admin/application_edit", ["application" => $appDetails, "formData" => $formData, "familyMembers" => $familyMembers, "eduAssistance" => $eduAssistance, "saPositions" => $saPositions, "pageTitle" => "Edit Application - " . APP_NAME]);
    }

    public function updateApplication() {
        requireAdmin();
        if (!validateCSRF($_POST["csrf_token"] ?? "") || !hasPermission("edit_applications")) { redirect(BASE_URL . "/admin/applications"); return; }
        $id = (int)($_POST["application_id"] ?? 0);
        $appDetails = $this->appModel->getApplicationWithDetails($id);
        if (!$appDetails) { redirect(BASE_URL . "/admin/applications"); return; }
        $adminId = $_SESSION["admin_id"];
        $studentId = $appDetails["student_id"] ?? null;
        $studentModel = new Student();
        $oldStudent = $studentModel->findById($studentId);
        $studentFields = ["last_name", "first_name", "middle_name", "extension_name", "degree_program", "year_level", "sex", "civil_status", "is_solo_parent", "citizenship", "birthdate", "place_of_birth", "physical_disability", "ethnic_affiliation", "street_barangay", "municipality", "province", "zip_code", "contact_number", "email_address", "is_dormitory", "registering_as", "lrn_number"];
        $updatedStudent = [];
        foreach ($studentFields as $field) {
            if (isset($_POST[$field])) {
                $newVal = sanitize($_POST[$field]);
                if ((string)($oldStudent[$field] ?? "") !== (string)$newVal) {
                    logAudit("EDIT", "students", $studentId, $field, $oldStudent[$field], $newVal, "Admin edited student " . $field . " for application #" . $appDetails["application_number"]);
                }
                $updatedStudent[$field] = $newVal;
            }
        }
        if (!empty($updatedStudent)) { $updatedStudent["updated_at"] = date("Y-m-d H:i:s"); $studentModel->update($studentId, $updatedStudent); }
        if (!empty($_POST["override_status"]) && hasPermission("override_applications")) {
            $oldStatus = $appDetails["status"];
            $newStatus = sanitize($_POST["override_status"]);
            $overrideReason = sanitize($_POST["override_reason"] ?? "");
            if ($oldStatus !== $newStatus) {
                $this->appModel->updateStatus($id, $newStatus, $adminId, null, "Override: " . $overrideReason);
                logAudit("STATUS_OVERRIDE", "applications", $id, "status", $oldStatus, $newStatus, "Admin overrode status. Reason: $overrideReason");
            }
        }
        flash("success", "Application updated successfully.", "success");
        redirect(BASE_URL . "/admin/applications/view?id=$id");
    }

    public function updateStatus() {
        requireAdmin();
        if (!validateCSRF($_POST["csrf_token"] ?? "")) { $this->jsonResponse(["success" => false, "message" => "Invalid request."], 400); return; }
        $id = (int)($_POST["application_id"] ?? 0);
        $newStatus = sanitize($_POST["status"] ?? "");
        $remarks = sanitize($_POST["remarks"] ?? "");
        if (!$id || !$newStatus) { $this->jsonResponse(["success" => false, "message" => "Missing data."], 400); return; }
        $this->appModel->updateStatus($id, $newStatus, $_SESSION["admin_id"], null, $remarks);
        logAudit("STATUS_UPDATE", "applications", $id, "status", "", $newStatus, "Status updated to $newStatus. $remarks");
        $this->jsonResponse(["success" => true, "message" => "Status updated."]);
    }

    public function assignApplication() {
        requireAdmin();
        if (!validateCSRF($_POST["csrf_token"] ?? "")) { $this->jsonResponse(["success" => false], 400); return; }
        $id = (int)($_POST["application_id"] ?? 0);
        $adminId = (int)($_POST["admin_id"] ?? 0);
        $this->appModel->update($id, ["assigned_admin_id" => $adminId ?: null, "updated_at" => date("Y-m-d H:i:s")]);
        logAudit("ASSIGN", "applications", $id, "assigned_admin_id", "", $adminId, "Application assigned to admin $adminId");
        $this->jsonResponse(["success" => true]);
    }

    public function applicationTypes() {
        requireAdmin();
        $applicationTypes = $this->typeModel->getAllTypes();
        foreach ($applicationTypes as &$type) {
            $type["requirements_list"] = (new Requirement())->findByTypeId($type["id"]);
            $type["app_count"] = $this->typeModel->getApplicationCount($type["id"]);
        }
        $saPositions = (new SaPosition())->getAll();
        $activePeriod = (new ApplicationPeriod())->getActive();
        $allPeriods = (new ApplicationPeriod())->getAllPeriods();
        $this->view("admin/application_types", ["applicationTypes" => $applicationTypes, "saPositions" => $saPositions, "activePeriod" => $activePeriod, "allPeriods" => $allPeriods, "pageTitle" => "Application Type Management - " . APP_NAME]);
    }

    public function toggleType() {
        requireAdmin();
        if (!validateCSRF($_POST["csrf_token"] ?? "") || !hasPermission("manage_application_types")) { $this->jsonResponse(["success" => false, "message" => "Access denied."], 403); return; }
        $id = (int)($_POST["type_id"] ?? 0);
        $enabled = (int)($_POST["enabled"] ?? 0);
        $type = $this->typeModel->findById($id);
        if (!$type) { $this->jsonResponse(["success" => false, "message" => "Type not found."], 404); return; }
        $this->typeModel->toggleEnabled($id, $enabled);
        logAudit("TOGGLE_TYPE", "application_types", $id, "is_enabled", $type["is_enabled"], $enabled, "Application type " . $type["name"] . " " . ($enabled ? "enabled" : "disabled"));
        $this->jsonResponse(["success" => true, "message" => "Application type " . ($enabled ? "enabled" : "disabled") . "."]);
    }

    public function updateType() {
        requireAdmin();
        if (!validateCSRF($_POST["csrf_token"] ?? "") || !hasPermission("manage_application_types")) { redirect(BASE_URL . "/admin/application-types"); return; }
        $id = (int)($_POST["type_id"] ?? 0);
        $data = ["start_date" => $_POST["start_date"] ?: null, "end_date" => $_POST["end_date"] ?: null, "max_applications" => !empty($_POST["max_applications"]) ? (int)$_POST["max_applications"] : null, "instructions" => sanitize($_POST["instructions"] ?? ""), "description" => sanitize($_POST["description"] ?? "")];
        $this->typeModel->updateTypeSettings($id, $data);
        logAudit("UPDATE_TYPE", "application_types", $id, "", "", "", "Application type settings updated");
        flash("success", "Application type settings updated.", "success");
        redirect(BASE_URL . "/admin/application-types");
    }

    public function manageRequirements() {
        requireAdmin();
        if (!validateCSRF($_POST["csrf_token"] ?? "") || !hasPermission("manage_requirements")) { redirect(BASE_URL . "/admin/application-types"); return; }
        $action = $_POST["req_action"] ?? "add";
        $reqModel = new Requirement();
        if ($action === "add") {
            $reqModel->insert(["application_type_id" => (int)$_POST["type_id"], "name" => sanitize($_POST["req_name"]), "description" => sanitize($_POST["req_desc"] ?? ""), "is_required" => isset($_POST["req_required"]) ? 1 : 0, "accepted_formats" => sanitize($_POST["req_formats"] ?? "pdf,jpg,jpeg,png"), "max_size_mb" => (int)($_POST["req_max_size"] ?? 5), "display_order" => (int)($_POST["req_order"] ?? 99)]);
            flash("success", "Requirement added.", "success");
        } elseif ($action === "toggle") {
            $reqModel->toggleActive((int)$_POST["req_id"]);
            flash("success", "Requirement updated.", "success");
        } elseif ($action === "delete" && hasPermission("delete_applications")) {
            $reqModel->delete((int)$_POST["req_id"]);
            flash("success", "Requirement deleted.", "success");
        }
        redirect(BASE_URL . "/admin/application-types");
    }

    public function managePeriods() {
        requireAdmin();
        if (!validateCSRF($_POST["csrf_token"] ?? "") || !hasPermission("manage_periods")) { redirect(BASE_URL . "/admin/application-types"); return; }
        $action = $_POST["period_action"] ?? "create";
        $periodModel = new ApplicationPeriod();
        if ($action === "create") {
            $periodModel->createPeriod(["name" => sanitize($_POST["period_name"]), "semester" => sanitize($_POST["semester"]), "academic_year" => sanitize($_POST["academic_year"]), "start_date" => $_POST["period_start"] ?: null, "end_date" => $_POST["period_end"] ?: null, "allow_reapplication" => isset($_POST["allow_reapplication"]) ? 1 : 0]);
            flash("success", "New application period created and set as active.", "success");
        } elseif ($action === "activate") {
            $periodModel->setActive((int)$_POST["period_id"]);
            flash("success", "Application period activated.", "success");
        }
        redirect(BASE_URL . "/admin/application-types");
    }

    public function users() {
        requireAdmin();
        if (!hasPermission("manage_users")) { flash("error", "Access denied.", "danger"); redirect(BASE_URL . "/admin/dashboard"); return; }
        $admins = (new Admin())->getAllAdmins();
        $roles = (new Role())->getAllRoles();
        $students = (new Student())->getAllStudents([], 1, 100);
        $this->view("admin/users", ["admins" => $admins, "roles" => $roles, "students" => $students, "pageTitle" => "User Management - " . APP_NAME]);
    }

    public function createUser() {
        requireAdmin();
        if (!validateCSRF($_POST["csrf_token"] ?? "") || !hasPermission("manage_users")) { redirect(BASE_URL . "/admin/users"); return; }
        $userModel = new User();
        $adminModel = new Admin();
        $username = sanitize($_POST["username"] ?? "");
        $email = sanitize($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";
        $roleId = (int)($_POST["role_id"] ?? 1);
        if (empty($username) || empty($email) || empty($password)) { flash("error", "All fields required.", "danger"); redirect(BASE_URL . "/admin/users"); return; }
        if ($userModel->findByUsername($username)) { flash("error", "Username already exists.", "danger"); redirect(BASE_URL . "/admin/users"); return; }
        $userId = $userModel->createUser(["username" => $username, "password" => $password, "email" => $email, "user_type" => "admin"]);
        $adminModel->insert(["user_id" => $userId, "role_id" => $roleId, "last_name" => sanitize($_POST["last_name"] ?? ""), "first_name" => sanitize($_POST["first_name"] ?? ""), "office" => sanitize($_POST["office"] ?? "")]);
        logAudit("CREATE_USER", "users", $userId, "", "", $username, "New admin user created: $username");
        flash("success", "Admin account created.", "success");
        redirect(BASE_URL . "/admin/users");
    }

    public function updateUser() {
        requireAdmin();
        if (!validateCSRF($_POST["csrf_token"] ?? "") || !hasPermission("manage_users")) { redirect(BASE_URL . "/admin/users"); return; }
        $userId = (int)($_POST["user_id"] ?? 0);
        $userModel = new User();
        $adminModel = new Admin();
        $admin = $adminModel->findByUserId($userId);
        if ($admin) {
            $adminModel->update($admin["id"], ["role_id" => (int)$_POST["role_id"], "last_name" => sanitize($_POST["last_name"]), "first_name" => sanitize($_POST["first_name"]), "office" => sanitize($_POST["office"] ?? "")]);
        }
        if (!empty($_POST["new_password"])) $userModel->changePassword($userId, $_POST["new_password"]);
        logAudit("UPDATE_USER", "users", $userId, "", "", "", "User account updated");
        flash("success", "Account updated.", "success");
        redirect(BASE_URL . "/admin/users");
    }

    public function toggleUser() {
        requireAdmin();
        if (!validateCSRF($_POST["csrf_token"] ?? "") || !hasPermission("manage_users")) { $this->jsonResponse(["success" => false], 403); return; }
        $userId = (int)($_POST["user_id"] ?? 0);
        $active = (int)($_POST["is_active"] ?? 0);
        (new User())->update($userId, ["is_active" => $active]);
        $this->jsonResponse(["success" => true]);
    }

    public function resetPassword() {
        requireAdmin();
        if (!validateCSRF($_POST["csrf_token"] ?? "") || !hasPermission("reset_passwords")) { redirect(BASE_URL . "/admin/users"); return; }
        $userId = (int)($_POST["user_id"] ?? 0);
        $newPwd = "Student@" . rand(1000, 9999);
        (new User())->changePassword($userId, $newPwd);
        logAudit("RESET_PASSWORD", "users", $userId, "password", "", "***", "Password reset by admin");
        flash("success", "Password reset to: $newPwd � Please inform the user.", "info");
        redirect(BASE_URL . "/admin/users");
    }

    public function auditLogs() {
        requireAdmin();
        if (!hasPermission("view_audit_logs")) { flash("error", "Access denied.", "danger"); redirect(BASE_URL . "/admin/dashboard"); return; }
        $filters = ["action" => $this->getGet("action"), "date_from" => $this->getGet("date_from"), "date_to" => $this->getGet("date_to")];
        $page = max(1, (int)$this->getGet("page", 1));
        $auditModel = new AuditLog();
        $logs = $auditModel->getAll($filters, $page, 50);
        $total = $auditModel->countAll($filters);
        $this->view("admin/audit_logs", ["logs" => $logs, "filters" => $filters, "currentPage" => $page, "totalPages" => ceil($total / 50), "total" => $total, "pageTitle" => "Audit Logs - " . APP_NAME]);
    }

    public function saPositions() {
        requireAdmin();
        $positions = (new SaPosition())->getAll();
        $this->view("admin/sa_positions", ["positions" => $positions, "pageTitle" => "SA Positions - " . APP_NAME]);
    }

    public function updateSaPositions() {
        requireAdmin();
        if (!validateCSRF($_POST["csrf_token"] ?? "")) { redirect(BASE_URL . "/admin/sa-positions"); return; }
        $posModel = new SaPosition();
        $action = $_POST["pos_action"] ?? "add";
        if ($action === "add") {
            $posModel->insert(["title" => sanitize($_POST["title"]), "office" => sanitize($_POST["office"]), "available_slots" => (int)($_POST["slots"] ?? 1), "schedule" => sanitize($_POST["schedule"] ?? ""), "qualifications" => sanitize($_POST["qualifications"] ?? ""), "is_active" => 1]);
            flash("success", "Position added.", "success");
        } elseif ($action === "toggle") {
            $pos = $posModel->findById((int)$_POST["pos_id"]);
            if ($pos) $posModel->update($pos["id"], ["is_active" => $pos["is_active"] ? 0 : 1]);
            flash("success", "Position updated.", "success");
        } elseif ($action === "delete") {
            $posModel->delete((int)$_POST["pos_id"]);
            flash("success", "Position deleted.", "success");
        }
        redirect(BASE_URL . "/admin/sa-positions");
    }
}