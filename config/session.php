<?php
// config/session.php - Secure Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    session_name("QSU_OSAS_SESSION");
    ini_set("session.cookie_httponly", 1);
    ini_set("session.use_strict_mode", 1);
    ini_set("session.cookie_samesite", "Lax");
    ini_set("session.use_only_cookies", 1);
    session_start();
}
if (isset($_SESSION["last_activity"])) {
    $timeout = defined("SESSION_TIMEOUT") ? SESSION_TIMEOUT : 3600;
    if (time() - $_SESSION["last_activity"] > $timeout) {
        $user_type = $_SESSION["user_type"] ?? "student";
        session_unset(); session_destroy(); session_start();
        $_SESSION["flash"]["error"] = ["message" => "Your session has expired. Please login again.", "type" => "warning"];
        $redir = ($user_type === "admin") ? "/SCHOLARSHIP/admin/login" : "/SCHOLARSHIP/student/login";
        header("Location: " . $redir); exit;
    }
}
$_SESSION["last_activity"] = time();
if (!isset($_SESSION["last_regenerated"])) {
    $_SESSION["last_regenerated"] = time();
} elseif (time() - $_SESSION["last_regenerated"] > 1800) {
    session_regenerate_id(true);
    $_SESSION["last_regenerated"] = time();
}
