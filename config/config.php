<?php
// config/config.php - Application Configuration and Helpers
// QSU OSAS Scholarship System

define("BASE_URL", "/SCHOLARSHIP");
define("APP_NAME", "QSU OSAS Scholarship System");
define("APP_VERSION", "1.0.0");
define("UPLOAD_PATH", dirname(__DIR__) . "/uploads/documents/");
define("MAX_FILE_SIZE", 5242880);
define("ALLOWED_EXTENSIONS", ["pdf", "jpg", "jpeg", "png"]);
define("SESSION_TIMEOUT", 3600);
define("MAX_LOGIN_ATTEMPTS", 5);
define("LOCKOUT_DURATION", 900);
define("BCRYPT_COST", 12);

require_once __DIR__ . "/database.php";

function redirect($url) { header("Location: " . $url); exit; }

function flash($key, $msg, $type = "success") {
    $_SESSION["flash"][$key] = ["message" => $msg, "type" => $type];
}

function getFlash($key) {
    if (isset($_SESSION["flash"][$key])) {
        $flash = $_SESSION["flash"][$key];
        unset($_SESSION["flash"][$key]);
        return $flash;
    }
    return null;
}

function sanitize($input) {
    if (is_array($input)) return array_map("sanitize", $input);
    return htmlspecialchars(trim($input), ENT_QUOTES, "UTF-8");
}

function generateCSRF() {
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

function validateCSRF($token) {
    if (!isset($_SESSION["csrf_token"]) || !hash_equals($_SESSION["csrf_token"], $token)) {
        return false;
    }
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    return true;
}

function isLoggedIn() {
    return isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"]);
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "admin";
}

function isStudent() {
    return isLoggedIn() && isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "student";
}

function requireLogin() {
    if (!isLoggedIn()) {
        flash("error", "Please login to continue.", "warning");
        redirect(BASE_URL . "/student/login");
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        flash("error", "Access denied. Admin login required.", "danger");
        redirect(BASE_URL . "/admin/login");
    }
}

function requireStudent() {
    if (!isStudent()) {
        flash("error", "Please login as a student to continue.", "warning");
        redirect(BASE_URL . "/student/login");
    }
}

function generateApplicationNumber($typeSlug) {
    $year = date("Y");
    $prefixes = [
        "student_assistant" => "SA",
        "academic_achiever" => "ACA",
        "scholarship"       => "SCH",
    ];
    $prefix = $prefixes[$typeSlug] ?? "APP";
    $random = str_pad(rand(1, 99999), 5, "0", STR_PAD_LEFT);
    return "QSU-{$year}-{$prefix}-{$random}";
}

function formatDate($date, $format = "F d, Y") {
    if (empty($date) || $date === "0000-00-00") return "N/A";
    return date($format, strtotime($date));
}

function hasPermission($permission) {
    if (isAdmin()) return true;
    if (!isset($_SESSION["role_permissions"])) return false;
    $permissions = is_string($_SESSION["role_permissions"])
        ? json_decode($_SESSION["role_permissions"], true)
        : $_SESSION["role_permissions"];
    return in_array($permission, $permissions ?? []);
}

function logAudit($action, $table, $recordId, $field, $oldVal, $newVal, $desc, $studentId = null) {
    try {
        $db = Database::getInstance()->getConnection();
        $adminId = $_SESSION["admin_id"] ?? null;
        $ip = $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1";
        $stmt = $db->prepare("INSERT INTO audit_logs (admin_id, student_id, action, table_name, record_id, field_name, old_value, new_value, description, ip_address) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$adminId, $studentId, $action, $table, $recordId, $field, $oldVal, $newVal, $desc, $ip]);
    } catch (Exception $e) {
        error_log("Audit log error: " . $e->getMessage());
    }
}

function sendNotification($studentId, $title, $message, $type = "general", $appId = null) {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO notifications (student_id, title, message, notification_type, related_application_id) VALUES (?,?,?,?,?)");
        $stmt->execute([$studentId, $title, $message, $type, $appId]);
    } catch (Exception $e) {
        error_log("Notification error: " . $e->getMessage());
    }
}

function formatMoney($amount) { return "PHP " . number_format((float)$amount, 2); }

function getSetting($key, $default = "") {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row["setting_value"] : $default;
    } catch (Exception $e) { return $default; }
}

function getStatusBadgeClass($status) {
    $classes = [
        "Draft" => "bg-secondary", "Submitted" => "bg-primary",
        "Under Review" => "bg-warning text-dark", "For Revision" => "bg-warning text-dark",
        "Qualified" => "bg-success", "Disqualified" => "bg-danger",
        "Approved" => "bg-success", "Rejected" => "bg-danger",
    ];
    return $classes[$status] ?? "bg-secondary";
}

function yearLevelLabel($level) {
    $labels = ["1st Year", "2nd Year", "3rd Year", "4th Year", "5th Year"];
    return $labels[($level - 1)] ?? $level . "th Year";
}

function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->d == 0) {
        if ($diff->h == 0) return $diff->i . " min ago";
        return $diff->h . " hr ago";
    }
    if ($diff->d < 7) return $diff->d . " day(s) ago";
    return date("M d, Y", strtotime($datetime));
}

function getTypeColor($slug) {
    $colors = [
        "student_assistant" => "info",
        "academic_achiever" => "purple",
        "scholarship"       => "warning",
    ];
    return $colors[$slug] ?? "secondary";
}

function getTypeIcon($slug) {
    $icons = [
        "student_assistant" => "fa-user-tie",
        "academic_achiever" => "fa-graduation-cap",
        "scholarship"       => "fa-hand-holding-dollar",
    ];
    return $icons[$slug] ?? "fa-file-alt";
}
