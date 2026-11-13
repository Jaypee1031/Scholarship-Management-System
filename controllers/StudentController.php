<?php
require_once __DIR__ . "/../lib/BaseController.php";
require_once __DIR__ . "/../models/Student.php";
require_once __DIR__ . "/../models/Application.php";
require_once __DIR__ . "/../models/ApplicationType.php";
require_once __DIR__ . "/../models/ApplicationPeriod.php";
require_once __DIR__ . "/../models/Notification.php";
require_once __DIR__ . "/../models/ScholarshipApplication.php";
require_once __DIR__ . "/../models/AcademicScholarApplication.php";
require_once __DIR__ . "/../models/StudentAssistantApplication.php";
require_once __DIR__ . "/../models/FamilyMember.php";
require_once __DIR__ . "/../models/EducationalAssistance.php";
require_once __DIR__ . "/../models/Document.php";
require_once __DIR__ . "/../models/Requirement.php";
require_once __DIR__ . "/../models/Evaluation.php";

class StudentController extends BaseController {
    private $studentModel;
    private $appModel;

    public function __construct() {
        $this->studentModel = new Student();
        $this->appModel = new Application();
    }

    public function dashboard() {
        requireStudent();
        $studentId = $_SESSION["student_id"];
        $student = $this->studentModel->findById($studentId);
        $periodModel = new ApplicationPeriod();
        $activePeriod = $periodModel->getActive();
        $application = null;
        if ($activePeriod) $application = $this->appModel->getStudentApplication($studentId, $activePeriod["id"]);
        if (!$application) $application = $this->appModel->getStudentApplication($studentId);
        $typeModel = new ApplicationType();
        $applicationTypes = $typeModel->getAllTypes();
        $notifModel = new Notification();
        $unreadCount = $notifModel->countUnread($studentId);
        $recentNotifications = $notifModel->findByStudentId($studentId, false, 3);
        $this->view("student/dashboard", ["student" => $student, "application" => $application, "applicationTypes" => $applicationTypes, "activePeriod" => $activePeriod, "unreadCount" => $unreadCount, "recentNotifications" => $recentNotifications, "pageTitle" => "Dashboard - " . APP_NAME]);
    }

    public function myApplication() {
        requireStudent();
        $studentId = $_SESSION["student_id"];
        $periodModel = new ApplicationPeriod();
        $activePeriod = $periodModel->getActive();
        $application = null;
        if ($activePeriod) $application = $this->appModel->getStudentApplication($studentId, $activePeriod["id"]);
        if (!$application) $application = $this->appModel->getStudentApplication($studentId);
        if (!$application) { flash("info", "You have no current application.", "info"); redirect(BASE_URL . "/student/dashboard"); return; }
        $appDetails = $this->appModel->getApplicationWithDetails($application["id"]);
        $typeSlug = $appDetails["type_slug"] ?? "";
        $formData = null; $familyMembers = []; $eduAssistance = [];
        if ($typeSlug === "scholarship") {
            $formData = (new ScholarshipApplication())->findByApplicationId($application["id"]);
            $familyMembers = (new FamilyMember())->findByApplicationId($application["id"]);
            $eduAssistance = (new EducationalAssistance())->findByApplicationId($application["id"]);
        } elseif ($typeSlug === "academic_achiever") {
            $formData = (new AcademicScholarApplication())->findByApplicationId($application["id"]);
        } elseif ($typeSlug === "student_assistant") {
            $formData = (new StudentAssistantApplication())->getWithPosition($application["id"]);
        }
        $documents = (new Document())->getDocumentsWithRequirements($application["id"]);
        $statusHistory = $this->appModel->getStatusHistory($application["id"]);
        $evaluations = (new Evaluation())->getEvaluationHistory($application["id"]);
        $notifModel = new Notification();
        $unreadCount = $notifModel->countUnread($studentId);
        $student = $this->studentModel->findById($studentId);
        $this->view("student/application", ["application" => $appDetails, "formData" => $formData, "familyMembers" => $familyMembers, "eduAssistance" => $eduAssistance, "documents" => $documents, "statusHistory" => $statusHistory, "evaluations" => $evaluations, "student" => $student, "unreadCount" => $unreadCount, "pageTitle" => "My Application - " . APP_NAME]);
    }

    public function profile() {
        requireStudent();
        $studentId = $_SESSION["student_id"];
        $student = $this->studentModel->findById($studentId);
        $notifModel = new Notification();
        $unreadCount = $notifModel->countUnread($studentId);
        $this->view("student/profile", ["student" => $student, "unreadCount" => $unreadCount, "pageTitle" => "My Profile - " . APP_NAME]);
    }

    public function updateProfile() {
        requireStudent();
        if (!validateCSRF($_POST["csrf_token"] ?? "")) { redirect(BASE_URL . "/student/profile"); return; }
        $studentId = $_SESSION["student_id"];
        $data = ["contact_number" => sanitize($_POST["contact_number"] ?? ""), "email_address" => sanitize($_POST["email_address"] ?? ""), "street_barangay" => sanitize($_POST["street_barangay"] ?? ""), "municipality" => sanitize($_POST["municipality"] ?? ""), "province" => sanitize($_POST["province"] ?? ""), "zip_code" => sanitize($_POST["zip_code"] ?? ""), "updated_at" => date("Y-m-d H:i:s")];
        $this->studentModel->update($studentId, $data);
        flash("success", "Profile updated successfully.", "success");
        redirect(BASE_URL . "/student/profile");
    }
}