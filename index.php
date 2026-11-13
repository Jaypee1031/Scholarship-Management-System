<?php
// index.php - Main Front Controller / Router
// QSU OSAS Scholarship System

require_once __DIR__ . "/config/session.php";
require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/lib/BaseModel.php";
require_once __DIR__ . "/lib/BaseController.php";

// Autoload controllers and models
spl_autoload_register(function ($class) {
    $dirs = [__DIR__ . "/controllers/", __DIR__ . "/models/"];
    foreach ($dirs as $dir) {
        $file = $dir . $class . ".php";
        if (file_exists($file)) { require_once $file; return; }
    }
});

// Parse route from URL
$requestUri = $_SERVER["REQUEST_URI"];
$basePath = "/SCHOLARSHIP";
$path = str_replace($basePath, "", parse_url($requestUri, PHP_URL_PATH));
$path = rtrim($path, "/");
if (empty($path)) $path = "/";
$method = $_SERVER["REQUEST_METHOD"];

// Route definitions: [method, pattern, controller, action]
$routes = [
    ["GET",  "/",                              "AuthController",          "unifiedLogin"],
    ["GET",  "/login",                         "AuthController",          "unifiedLogin"],
    ["POST", "/login",                         "AuthController",          "unifiedLoginPost"],
    ["GET",  "/logout",                        "AuthController",          "logout"],
    ["GET",  "/student/login",                "AuthController",          "unifiedLogin"],
    ["POST", "/student/login",                "AuthController",          "unifiedLoginPost"],
    ["GET",  "/student/logout",               "AuthController",          "studentLogout"],
    ["GET",  "/student/register",             "AuthController",          "studentRegister"],
    ["POST", "/student/register",             "AuthController",          "studentRegisterPost"],
    ["GET",  "/student/forgot-password",      "AuthController",          "forgotPassword"],
    ["POST", "/student/forgot-password",      "AuthController",          "forgotPasswordPost"],
    ["GET",  "/student/dashboard",            "StudentController",       "dashboard"],
    ["GET",  "/student/select-type",          "ApplicationController",   "selectType"],
    ["POST", "/student/select-type",          "ApplicationController",   "confirmType"],
    ["GET",  "/student/apply/scholarship",    "ApplicationController",   "scholarshipForm"],
    ["POST", "/student/apply/scholarship",    "ApplicationController",   "scholarshipSubmit"],
    ["GET",  "/student/apply/academic",       "ApplicationController",   "academicForm"],
    ["POST", "/student/apply/academic",       "ApplicationController",   "academicSubmit"],
    ["GET",  "/student/apply/assistant",      "ApplicationController",   "assistantForm"],
    ["POST", "/student/apply/assistant",      "ApplicationController",   "assistantSubmit"],
    ["GET",  "/student/application",          "StudentController",       "myApplication"],
    ["GET",  "/student/profile",              "StudentController",       "profile"],
    ["POST", "/student/profile",              "StudentController",       "updateProfile"],
    ["GET",  "/student/notifications",        "NotificationController",  "studentNotifications"],
    ["POST", "/student/notifications/read",   "NotificationController",  "markRead"],
    ["GET",  "/admin/login",                  "AuthController",          "adminLogin"],
    ["POST", "/admin/login",                  "AuthController",          "adminLoginPost"],
    ["GET",  "/admin/logout",                 "AuthController",          "adminLogout"],
    ["GET",  "/admin/dashboard",              "AdminController",         "dashboard"],
    ["GET",  "/admin/applications",           "AdminController",         "applications"],
    ["GET",  "/admin/applications/view",      "AdminController",         "viewApplication"],
    ["GET",  "/admin/applications/edit",      "AdminController",         "editApplication"],
    ["POST", "/admin/applications/edit",      "AdminController",         "updateApplication"],
    ["GET",  "/admin/applications/evaluate",  "EvaluationController",    "evaluate"],
    ["POST", "/admin/applications/evaluate",  "EvaluationController",    "submitEvaluation"],
    ["POST", "/admin/applications/status",    "AdminController",         "updateStatus"],
    ["POST", "/admin/applications/assign",    "AdminController",         "assignApplication"],
    ["GET",  "/admin/application-types",      "AdminController",         "applicationTypes"],
    ["POST", "/admin/application-types/toggle","AdminController",        "toggleType"],
    ["POST", "/admin/application-types/update","AdminController",        "updateType"],
    ["POST", "/admin/requirements",           "AdminController",         "manageRequirements"],
    ["GET",  "/admin/users",                  "AdminController",         "users"],
    ["POST", "/admin/users/create",           "AdminController",         "createUser"],
    ["POST", "/admin/users/update",           "AdminController",         "updateUser"],
    ["POST", "/admin/users/toggle",           "AdminController",         "toggleUser"],
    ["POST", "/admin/users/reset-password",   "AdminController",         "resetPassword"],
    ["GET",  "/admin/reports",                "ReportController",        "index"],
    ["POST", "/admin/reports/generate",       "ReportController",        "generate"],
    ["GET",  "/admin/reports/export",         "ReportController",        "export"],
    ["GET",  "/admin/audit-logs",             "AdminController",         "auditLogs"],
    ["GET",  "/admin/notifications",          "NotificationController",  "adminNotifications"],
    ["POST", "/admin/notifications/send",     "NotificationController",  "sendAnnouncement"],
    ["GET",  "/admin/sa-positions",           "AdminController",         "saPositions"],
    ["POST", "/admin/sa-positions",           "AdminController",         "updateSaPositions"],
    ["POST", "/admin/periods",                "AdminController",         "managePeriods"],
    ["GET",  "/document/serve",               "DocumentController",      "serve"],
    ["POST", "/document/upload",              "DocumentController",      "upload"],
    ["GET",  "/document/download",            "DocumentController",      "download"],
    ["GET",  "/document/pdf",                 "DocumentController",      "generatePdf"],
];

$matched = false;
foreach ($routes as [$routeMethod, $routePath, $controllerName, $actionName]) {
    if ($method === $routeMethod && $path === $routePath) {
        $controller = new $controllerName();
        $controller->$actionName();
        $matched = true;
        break;
    }
}

if (!$matched) {
    http_response_code(404);
    include __DIR__ . "/views/errors/404.php";
}
