<?php
require_once __DIR__ . "/../lib/BaseController.php";
require_once __DIR__ . "/../models/Notification.php";
require_once __DIR__ . "/../models/ApplicationType.php";
require_once __DIR__ . "/../models/Student.php";

class NotificationController extends BaseController {
    public function studentNotifications() {
        requireStudent();
        $studentId = $_SESSION["student_id"];
        $notifModel = new Notification();
        $notifications = $notifModel->findByStudentId($studentId, false, 50);
        $unreadCount = $notifModel->countUnread($studentId);
        $student = (new Student())->findById($studentId);
        $this->view("student/notifications", ["notifications" => $notifications, "unreadCount" => $unreadCount, "student" => $student, "pageTitle" => "Notifications - " . APP_NAME]);
    }

    public function markRead() {
        requireStudent();
        $studentId = $_SESSION["student_id"];
        $id = (int)($_POST["id"] ?? 0);
        $all = $_POST["all"] ?? false;
        $notifModel = new Notification();
        if ($all) $notifModel->markAllRead($studentId);
        elseif ($id) $notifModel->markRead($id, $studentId);
        $this->jsonResponse(["success" => true, "unread" => $notifModel->countUnread($studentId)]);
    }

    public function adminNotifications() {
        requireAdmin();
        $applicationTypes = (new ApplicationType())->getAllTypes();
        $this->view("admin/notifications", ["applicationTypes" => $applicationTypes, "pageTitle" => "Notifications - " . APP_NAME]);
    }

    public function sendAnnouncement() {
        requireAdmin();
        if (!validateCSRF($_POST["csrf_token"] ?? "") || !hasPermission("send_notifications")) { redirect(BASE_URL . "/admin/notifications"); return; }
        $target = sanitize($_POST["target"] ?? "all");
        $title = sanitize($_POST["notif_title"] ?? "");
        $message = sanitize($_POST["notif_message"] ?? "");
        $type = sanitize($_POST["notif_type"] ?? "general");
        if (empty($title) || empty($message)) { flash("error", "Title and message are required.", "danger"); redirect(BASE_URL . "/admin/notifications"); return; }
        $notifModel = new Notification();
        if ($target === "all") $notifModel->sendToAllStudents($title, $message, $type);
        elseif (in_array($target, ["student_assistant", "academic_achiever", "scholarship"])) $notifModel->sendToTypeApplicants($target, $title, $message, $type);
        elseif ($target === "specific") {
            $studentNum = sanitize($_POST["specific_student_id"] ?? "");
            $student = (new Student())->findByStudentId($studentNum);
            if ($student) $notifModel->sendToStudent($student["id"], $title, $message, $type);
            else { flash("error", "Student not found.", "danger"); redirect(BASE_URL . "/admin/notifications"); return; }
        }
        logAudit("SEND_NOTIFICATION", "notifications", 0, "", "", $target, "Announcement sent to: $target. Title: $title");
        flash("success", "Announcement sent successfully.", "success");
        redirect(BASE_URL . "/admin/notifications");
    }
}