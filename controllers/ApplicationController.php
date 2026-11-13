<?php
require_once __DIR__ . "/../lib/BaseController.php";
require_once __DIR__ . "/../models/Application.php";
require_once __DIR__ . "/../models/ApplicationType.php";
require_once __DIR__ . "/../models/ApplicationPeriod.php";
require_once __DIR__ . "/../models/Student.php";
require_once __DIR__ . "/../models/ScholarshipApplication.php";
require_once __DIR__ . "/../models/AcademicScholarApplication.php";
require_once __DIR__ . "/../models/StudentAssistantApplication.php";
require_once __DIR__ . "/../models/FamilyMember.php";
require_once __DIR__ . "/../models/EducationalAssistance.php";
require_once __DIR__ . "/../models/Document.php";
require_once __DIR__ . "/../models/Requirement.php";
require_once __DIR__ . "/../models/SaPosition.php";
require_once __DIR__ . "/../models/Notification.php";

class ApplicationController extends BaseController {
    private $appModel;
    private $typeModel;
    private $periodModel;
    private $studentModel;

    public function __construct() {
        $this->appModel = new Application();
        $this->typeModel = new ApplicationType();
        $this->periodModel = new ApplicationPeriod();
        $this->studentModel = new Student();
    }

    public function selectType() {
        requireStudent();
        $studentId = $_SESSION["student_id"];
        $activePeriod = $this->periodModel->getActive();
        if (!$activePeriod) { flash("warning", "No active application period at this time. Please check back later.", "warning"); redirect(BASE_URL . "/student/dashboard"); return; }
        $existingApp = $this->appModel->findByStudentAndPeriod($studentId, $activePeriod["id"]);
        $applicationTypes = $this->typeModel->getAllTypes();
        $notifModel = new Notification();
        $student = $this->studentModel->findById($studentId);
        $this->view("student/select_type", ["applicationTypes" => $applicationTypes, "activePeriod" => $activePeriod, "student" => $student, "unreadCount" => $notifModel->countUnread($studentId), "pageTitle" => "Select Application Type - " . APP_NAME]);
    }

    public function confirmType() {
        requireStudent();
        if (!validateCSRF($_POST["csrf_token"] ?? "")) { redirect(BASE_URL . "/student/select-type"); return; }
        $studentId = $_SESSION["student_id"];
        $typeSlug = sanitize($_POST["type_slug"] ?? "");
        if (empty($typeSlug)) { flash("error", "Please select an application type.", "danger"); redirect(BASE_URL . "/student/select-type"); return; }
        $activePeriod = $this->periodModel->getActive();
        if (!$activePeriod) { flash("error", "No active application period.", "danger"); redirect(BASE_URL . "/student/dashboard"); return; }
        $existingApp = $this->appModel->findByStudentAndPeriod($studentId, $activePeriod["id"]);
        if ($existingApp) { flash("warning", "You already have an application for this period.", "warning"); redirect(BASE_URL . "/student/application"); return; }
        $appType = $this->typeModel->findBySlug($typeSlug);
        if (!$appType || !$appType["is_enabled"]) { flash("error", "This application type is currently not available.", "danger"); redirect(BASE_URL . "/student/select-type"); return; }
        if (!$this->typeModel->isCurrentlyOpen($appType["id"])) { flash("error", "This application type is currently closed.", "danger"); redirect(BASE_URL . "/student/select-type"); return; }
        if ($appType["max_applications"]) {
            $count = $this->typeModel->getApplicationCount($appType["id"]);
            if ($count >= $appType["max_applications"]) { flash("error", "The maximum number of applications for this type has been reached.", "danger"); redirect(BASE_URL . "/student/select-type"); return; }
        }
        $appNumber = $this->appModel->generateUniqueAppNumber($typeSlug);
        $appId = $this->appModel->insert(["application_number" => $appNumber, "student_id" => $studentId, "application_type_id" => $appType["id"], "application_period_id" => $activePeriod["id"], "status" => "Draft"]);
        
        $this->appModel->query("INSERT INTO application_status_history (application_id, old_status, new_status, changed_by_student_id, remarks) VALUES (?,?,?,?,?)", [$appId, null, "Draft", $studentId, "Application created"]);
        sendNotification($studentId, "Application Created", "Your application ({$appNumber}) for " . $appType["name"] . " has been created. Please complete and submit it.", "general", $appId);
        flash("success", "You have selected: " . $appType["name"] . ". Application number: $appNumber. Please complete your application.", "success");
        $routes = ["student_assistant" => "/student/apply/assistant", "academic_achiever" => "/student/apply/academic", "scholarship" => "/student/apply/scholarship"];
        redirect(BASE_URL . ($routes[$typeSlug] ?? "/student/dashboard"));
    }


    private function getOrCreateDraft($studentId, $typeSlug) {
        $activePeriod = $this->periodModel->getActive();
        if (!$activePeriod) return null;
        $appType = $this->typeModel->findBySlug($typeSlug);
        if (!$appType || !$appType["is_enabled"]) return null;
        
        $existing = $this->appModel->findByStudentAndPeriod($studentId, $activePeriod["id"]);
        if ($existing) {
            if ($existing["status"] !== "Draft" && $existing["status"] !== "For Revision") {
                return $existing;
            }
            if ($existing["application_type_id"] != $appType["id"]) {
                $newAppNumber = $this->appModel->generateUniqueAppNumber($typeSlug);
                $this->appModel->update($existing["id"], [
                    "application_type_id" => $appType["id"],
                    "application_number" => $newAppNumber,
                    "updated_at" => date("Y-m-d H:i:s")
                ]);
                $existing["application_type_id"] = $appType["id"];
                $existing["application_number"] = $newAppNumber;
            }
            return $existing;
        }
        
        $appNumber = $this->appModel->generateUniqueAppNumber($typeSlug);
        $appId = $this->appModel->insert([
            "application_number" => $appNumber,
            "student_id" => $studentId,
            "application_type_id" => $appType["id"],
            "application_period_id" => $activePeriod["id"],
            "status" => "Draft"
        ]);
        return $this->appModel->findById($appId);
    }

    public function scholarshipForm() {
        requireStudent();
        $studentId = $_SESSION["student_id"];
        $student = $this->studentModel->findById($studentId);
        $application = $this->getOrCreateDraft($studentId, "scholarship");
        if (!$application) { flash("error", "Scholarship application is currently unavailable or closed.", "danger"); redirect(BASE_URL . "/student/dashboard"); return; }
        if ($application["status"] === "Submitted" && $application["type_slug"] !== "scholarship") { redirect(BASE_URL . "/student/application"); return; }
        $schApp = (new ScholarshipApplication())->findByApplicationId($application["id"]);
        $familyMembers = (new FamilyMember())->findByApplicationId($application["id"]);
        $eduAssistance = (new EducationalAssistance())->findByApplicationId($application["id"]);
        $requirements = (new Requirement())->findByTypeId($application["application_type_id"]);
        $documents = (new Document())->findByApplicationId($application["id"]);
        $notifModel = new Notification();
        $this->view("forms/scholarship_form", ["student" => $student, "application" => $application, "schApp" => $schApp, "familyMembers" => $familyMembers, "eduAssistance" => $eduAssistance, "requirements" => $requirements, "documents" => $documents, "unreadCount" => $notifModel->countUnread($studentId), "pageTitle" => "Scholarship Application - " . APP_NAME]);
    }

    public function scholarshipSubmit() {
        requireStudent();
        if (!validateCSRF($_POST["csrf_token"] ?? "")) { redirect(BASE_URL . "/student/apply/scholarship"); return; }
        $studentId = $_SESSION["student_id"];
        $activePeriod = $this->periodModel->getActive();
        $application = $activePeriod ? $this->appModel->findByStudentAndPeriod($studentId, $activePeriod["id"]) : $this->appModel->getStudentApplication($studentId);
        if (!$application) { redirect(BASE_URL . "/student/dashboard"); return; }
        $appId = $application["id"];
        // Update student personal info
        $studentData = ["last_name" => sanitize($_POST["last_name"] ?? ""), "first_name" => sanitize($_POST["first_name"] ?? ""), "middle_name" => sanitize($_POST["middle_name"] ?? ""), "extension_name" => sanitize($_POST["extension_name"] ?? ""), "degree_program" => sanitize($_POST["degree_program"] ?? ""), "year_level" => (int)($_POST["year_level"] ?? 1), "registering_as" => sanitize($_POST["registering_as"] ?? ""), "lrn_number" => sanitize($_POST["lrn_number"] ?? ""), "sex" => sanitize($_POST["sex"] ?? ""), "civil_status" => sanitize($_POST["civil_status"] ?? ""), "is_solo_parent" => isset($_POST["is_solo_parent"]) && $_POST["is_solo_parent"] === "1" ? 1 : 0, "citizenship" => sanitize($_POST["citizenship"] ?? "Filipino"), "birthdate" => $_POST["birthdate"] ?? null, "place_of_birth" => sanitize($_POST["place_of_birth"] ?? ""), "physical_disability" => sanitize($_POST["physical_disability"] ?? ""), "ethnic_affiliation" => sanitize($_POST["ethnic_affiliation"] ?? ""), "street_barangay" => sanitize($_POST["street_barangay"] ?? ""), "municipality" => sanitize($_POST["municipality"] ?? ""), "province" => sanitize($_POST["province"] ?? ""), "zip_code" => sanitize($_POST["zip_code"] ?? ""), "contact_number" => sanitize($_POST["contact_number"] ?? ""), "email_address" => sanitize($_POST["email_address"] ?? ""), "is_dormitory" => isset($_POST["is_dormitory"]) && $_POST["is_dormitory"] === "1" ? 1 : 0, "updated_at" => date("Y-m-d H:i:s")];
        $this->studentModel->update($studentId, $studentData);
        // Save scholarship data
        $schData = ["household_per_capita_income" => !empty($_POST["household_income"]) ? (float)$_POST["household_income"] : null, "dswd_household_number" => sanitize($_POST["dswd_number"] ?? ""), "has_other_assistance" => isset($_POST["has_other_assistance"]) && $_POST["has_other_assistance"] === "1" ? 1 : 0, "certification_confirmed" => isset($_POST["certification_confirmed"]) ? 1 : 0, "certification_date" => date("Y-m-d")];
        (new ScholarshipApplication())->saveOrUpdate($appId, $schData);
        // Save family members
        $familyData = [];
        $relationships = ["Father", "Mother"];
        foreach ($relationships as $rel) {
            $key = strtolower($rel);
            $familyData[] = ["relationship" => $rel, "last_name" => sanitize($_POST["{$key}_last_name"] ?? ""), "first_name" => sanitize($_POST["{$key}_first_name"] ?? ""), "middle_name" => sanitize($_POST["{$key}_middle_name"] ?? ""), "age" => !empty($_POST["{$key}_age"]) ? (int)$_POST["{$key}_age"] : null, "ethnic_affiliation" => sanitize($_POST["{$key}_ethnic"] ?? ""), "occupation" => sanitize($_POST["{$key}_occupation"] ?? ""), "member_status" => sanitize($_POST["{$key}_status"] ?? "Living")];
        }
        if (!empty($_POST["sibling_first_name"])) {
            foreach ($_POST["sibling_first_name"] as $i => $fn) {
                if (empty($fn)) continue;
                $familyData[] = ["relationship" => "Sibling", "last_name" => sanitize($_POST["sibling_last_name"][$i] ?? ""), "first_name" => sanitize($fn), "middle_name" => sanitize($_POST["sibling_middle_name"][$i] ?? ""), "age" => !empty($_POST["sibling_age"][$i]) ? (int)$_POST["sibling_age"][$i] : null, "ethnic_affiliation" => sanitize($_POST["sibling_ethnic"][$i] ?? ""), "occupation" => sanitize($_POST["sibling_occupation"][$i] ?? ""), "member_status" => "Living"];
            }
        }
        (new FamilyMember())->saveFamilyData($appId, $familyData);
        // Save educational assistance
        $eduData = [];
        if (!empty($_POST["ea_type"])) {
            foreach ($_POST["ea_type"] as $i => $type) {
                if (empty($type)) continue;
                $eduData[] = ["assistance_type" => sanitize($type), "grantee" => sanitize($_POST["ea_grantee"][$i] ?? ""), "institution_agency" => sanitize($_POST["ea_institution"][$i] ?? ""), "amount" => !empty($_POST["ea_amount"][$i]) ? (float)$_POST["ea_amount"][$i] : null];
            }
        }
        (new EducationalAssistance())->saveAssistanceData($appId, $eduData);
        // Handle file uploads
        $this->handleFileUploads($appId, $application["application_type_id"]);
        // Submit or save draft
        $action = $_POST["submit_action"] ?? "draft";
        if ($action === "submit") {
            if (!isset($_POST["certification_confirmed"]) || !$_POST["certification_confirmed"]) { flash("error", "You must confirm the certification statement before submitting.", "danger"); redirect(BASE_URL . "/student/apply/scholarship"); return; }
            $this->appModel->update($appId, ["status" => "Submitted", "submitted_at" => date("Y-m-d H:i:s"), "updated_at" => date("Y-m-d H:i:s")]);
            $this->appModel->query("INSERT INTO application_status_history (application_id, old_status, new_status, changed_by_student_id, remarks) VALUES (?,?,?,?,?)", [$appId, "Draft", "Submitted", $studentId, "Application submitted by student"]);
            sendNotification($studentId, "Application Submitted", "Your Scholarship/Financial Assistance application (" . $application["application_number"] . ") has been successfully submitted.", "success", $appId);
            flash("success", "Application submitted successfully! Application Number: " . $application["application_number"], "success");
            redirect(BASE_URL . "/student/application");
        } else {
            flash("success", "Application saved as draft.", "success");
            redirect(BASE_URL . "/student/apply/scholarship");
        }
    }

    public function academicForm() {
        requireStudent();
        $studentId = $_SESSION["student_id"];
        $student = $this->studentModel->findById($studentId);
        $application = $this->getOrCreateDraft($studentId, "academic_achiever");
        if (!$application) { flash("error", "Academic Achiever application is currently unavailable or closed.", "danger"); redirect(BASE_URL . "/student/dashboard"); return; }
        if ($application["status"] === "Submitted" && $application["type_slug"] !== "academic_achiever") { redirect(BASE_URL . "/student/application"); return; }
        $acaApp = (new AcademicScholarApplication())->findByApplicationId($application["id"]);
        $requirements = (new Requirement())->findByTypeId($application["application_type_id"]);
        $documents = (new Document())->findByApplicationId($application["id"]);
        $notifModel = new Notification();
        $this->view("forms/academic_form", ["student" => $student, "application" => $application, "acaApp" => $acaApp, "requirements" => $requirements, "documents" => $documents, "unreadCount" => $notifModel->countUnread($studentId), "pageTitle" => "Academic Achiever Application - " . APP_NAME]);
    }

    public function academicSubmit() {
        requireStudent();
        if (!validateCSRF($_POST["csrf_token"] ?? "")) { redirect(BASE_URL . "/student/apply/academic"); return; }
        $studentId = $_SESSION["student_id"];
        $activePeriod = $this->periodModel->getActive();
        $application = $activePeriod ? $this->appModel->findByStudentAndPeriod($studentId, $activePeriod["id"]) : $this->appModel->getStudentApplication($studentId);
        if (!$application) { redirect(BASE_URL . "/student/dashboard"); return; }
        $appId = $application["id"];
        $studentData = ["last_name" => sanitize($_POST["last_name"] ?? ""), "first_name" => sanitize($_POST["first_name"] ?? ""), "middle_name" => sanitize($_POST["middle_name"] ?? ""), "extension_name" => sanitize($_POST["extension_name"] ?? ""), "degree_program" => sanitize($_POST["degree_program"] ?? ""), "year_level" => (int)($_POST["year_level"] ?? 1), "sex" => sanitize($_POST["sex"] ?? ""), "civil_status" => sanitize($_POST["civil_status"] ?? ""), "is_solo_parent" => ($_POST["is_solo_parent"] ?? "0") === "1" ? 1 : 0, "physical_disability" => sanitize($_POST["physical_disability"] ?? ""), "ethnic_affiliation" => sanitize($_POST["ethnic_affiliation"] ?? ""), "street_barangay" => sanitize($_POST["street_barangay"] ?? ""), "municipality" => sanitize($_POST["municipality"] ?? ""), "province" => sanitize($_POST["province"] ?? ""), "contact_number" => sanitize($_POST["contact_number"] ?? ""), "email_address" => sanitize($_POST["email_address"] ?? ""), "updated_at" => date("Y-m-d H:i:s")];
        $this->studentModel->update($studentId, $studentData);
        $acaData = ["scholarship_type" => sanitize($_POST["scholarship_type"] ?? "Academic"), "service_category" => sanitize($_POST["service_category"] ?? ""), "service_category_other" => sanitize($_POST["service_category_other"] ?? ""), "semester" => sanitize($_POST["semester"] ?? "First"), "academic_year" => sanitize($_POST["academic_year"] ?? ""), "certification_confirmed" => isset($_POST["certification_confirmed"]) ? 1 : 0, "certification_date" => date("Y-m-d")];
        (new AcademicScholarApplication())->saveOrUpdate($appId, $acaData);
        $this->handleFileUploads($appId, $application["application_type_id"]);
        $action = $_POST["submit_action"] ?? "draft";
        if ($action === "submit") {
            $this->appModel->update($appId, ["status" => "Submitted", "submitted_at" => date("Y-m-d H:i:s"), "updated_at" => date("Y-m-d H:i:s")]);
            $this->appModel->query("INSERT INTO application_status_history (application_id, old_status, new_status, changed_by_student_id, remarks) VALUES (?,?,?,?,?)", [$appId, "Draft", "Submitted", $studentId, "Application submitted by student"]);
            sendNotification($studentId, "Application Submitted", "Your Academic Achiever application (" . $application["application_number"] . ") has been submitted.", "success", $appId);
            flash("success", "Application submitted successfully! Number: " . $application["application_number"], "success");
            redirect(BASE_URL . "/student/application");
        } else { flash("success", "Draft saved.", "success"); redirect(BASE_URL . "/student/apply/academic"); }
    }

    public function assistantForm() {
        requireStudent();
        $studentId = $_SESSION["student_id"];
        $student = $this->studentModel->findById($studentId);
        $application = $this->getOrCreateDraft($studentId, "student_assistant");
        if (!$application) { flash("error", "Student Assistant application is currently unavailable or closed.", "danger"); redirect(BASE_URL . "/student/dashboard"); return; }
        if ($application["status"] === "Submitted" && $application["type_slug"] !== "student_assistant") { redirect(BASE_URL . "/student/application"); return; }
        $saApp = (new StudentAssistantApplication())->findByApplicationId($application["id"]);
        $requirements = (new Requirement())->findByTypeId($application["application_type_id"]);
        $documents = (new Document())->findByApplicationId($application["id"]);
        $saPositions = (new SaPosition())->getActive();
        $notifModel = new Notification();
        $this->view("forms/assistant_form", ["student" => $student, "application" => $application, "saApp" => $saApp, "requirements" => $requirements, "documents" => $documents, "saPositions" => $saPositions, "unreadCount" => $notifModel->countUnread($studentId), "pageTitle" => "Student Assistant Application - " . APP_NAME]);
    }

    public function assistantSubmit() {
        requireStudent();
        if (!validateCSRF($_POST["csrf_token"] ?? "")) { redirect(BASE_URL . "/student/apply/assistant"); return; }
        $studentId = $_SESSION["student_id"];
        $activePeriod = $this->periodModel->getActive();
        $application = $activePeriod ? $this->appModel->findByStudentAndPeriod($studentId, $activePeriod["id"]) : $this->appModel->getStudentApplication($studentId);
        if (!$application) { redirect(BASE_URL . "/student/dashboard"); return; }
        $appId = $application["id"];
        $studentData = ["last_name" => sanitize($_POST["last_name"] ?? ""), "first_name" => sanitize($_POST["first_name"] ?? ""), "middle_name" => sanitize($_POST["middle_name"] ?? ""), "degree_program" => sanitize($_POST["degree_program"] ?? ""), "year_level" => (int)($_POST["year_level"] ?? 1), "contact_number" => sanitize($_POST["contact_number"] ?? ""), "email_address" => sanitize($_POST["email_address"] ?? ""), "updated_at" => date("Y-m-d H:i:s")];
        $this->studentModel->update($studentId, $studentData);
        $saData = ["preferred_position_id" => !empty($_POST["preferred_position_id"]) ? (int)$_POST["preferred_position_id"] : null, "preferred_office" => sanitize($_POST["preferred_office"] ?? ""), "preferred_schedule" => sanitize($_POST["preferred_schedule"] ?? ""), "skills" => sanitize($_POST["skills"] ?? ""), "previous_experience" => sanitize($_POST["previous_experience"] ?? ""), "reason_for_applying" => sanitize($_POST["reason_for_applying"] ?? "")];
        (new StudentAssistantApplication())->saveOrUpdate($appId, $saData);
        $this->handleFileUploads($appId, $application["application_type_id"]);
        $action = $_POST["submit_action"] ?? "draft";
        if ($action === "submit") {
            $this->appModel->update($appId, ["status" => "Submitted", "submitted_at" => date("Y-m-d H:i:s"), "updated_at" => date("Y-m-d H:i:s")]);
            $this->appModel->query("INSERT INTO application_status_history (application_id, old_status, new_status, changed_by_student_id, remarks) VALUES (?,?,?,?,?)", [$appId, "Draft", "Submitted", $studentId, "Application submitted by student"]);
            sendNotification($studentId, "Application Submitted", "Your Student Assistant application (" . $application["application_number"] . ") has been submitted.", "success", $appId);
            flash("success", "Application submitted! Number: " . $application["application_number"], "success");
            redirect(BASE_URL . "/student/application");
        } else { flash("success", "Draft saved.", "success"); redirect(BASE_URL . "/student/apply/assistant"); }
    }

    private function handleFileUploads($appId, $typeId) {
        if (empty($_FILES)) return;
        $reqModel = new Requirement();
        $docModel = new Document();
        $requirements = $reqModel->findByTypeId($typeId);
        foreach ($requirements as $req) {
            $fieldName = "doc_" . $req["id"];
            if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]["error"] === UPLOAD_ERR_NO_FILE) continue;
            $file = $_FILES[$fieldName];
            if ($file["error"] !== UPLOAD_ERR_OK) continue;
            $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
            $allowedExts = explode(",", $req["accepted_formats"]);
            if (!in_array($ext, $allowedExts)) continue;
            if ($file["size"] > $req["max_size_mb"] * 1024 * 1024) continue;
            $allowedMimes = ["pdf" => "application/pdf", "jpg" => "image/jpeg", "jpeg" => "image/jpeg", "png" => "image/png"];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file["tmp_name"]);
            finfo_close($finfo);
            if (!in_array($mime, $allowedMimes)) continue;
            $storedName = uniqid("doc_", true) . "_" . time() . "." . $ext;
            $destPath = UPLOAD_PATH . $storedName;
            if (!move_uploaded_file($file["tmp_name"], $destPath)) continue;
            $existing = $docModel->findByReqAndApp($appId, $req["id"]);
            if ($existing) { if (file_exists($existing["file_path"])) unlink($existing["file_path"]); $docModel->delete($existing["id"]); }
            $docModel->saveDocument(["application_id" => $appId, "requirement_id" => $req["id"], "original_filename" => $file["name"], "stored_filename" => $storedName, "file_path" => $destPath, "file_type" => $mime, "file_size" => $file["size"], "upload_status" => "Pending"]);
        }
    }
}