<?php
// lib/BaseController.php
abstract class BaseController {
    protected function view($viewPath, $data = []) {
        extract($data);
        $csrf_token = generateCSRF();
        if (!isset($pageTitle)) $pageTitle = APP_NAME;
        $fullPath = __DIR__ . "/../views/" . $viewPath . ".php";
        if (file_exists($fullPath)) { include $fullPath; }
        else { http_response_code(404); echo "<h1>View not found: $viewPath</h1>"; }
    }
    protected function jsonResponse($data, $code = 200) {
        http_response_code($code); header("Content-Type: application/json");
        echo json_encode($data); exit;
    }
    protected function redirect($url) { header("Location: " . $url); exit; }
    protected function requireRole($permissions) {
        if (!is_array($permissions)) $permissions = [$permissions];
        foreach ($permissions as $perm) { if (hasPermission($perm)) return true; }
        flash("error", "You do not have permission to perform this action.", "danger");
        $this->redirect(BASE_URL . "/admin/dashboard"); return false;
    }
    protected function getPost($key, $default = "") { return isset($_POST[$key]) ? trim($_POST[$key]) : $default; }
    protected function getGet($key, $default = "") { return isset($_GET[$key]) ? trim($_GET[$key]) : $default; }
}
