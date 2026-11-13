<?php
require_once __DIR__ . "/../lib/BaseController.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Student.php";
require_once __DIR__ . "/../models/Admin.php";
require_once __DIR__ . "/../models/Role.php";

class AuthController extends BaseController {
    private $userModel;
    private $studentModel;
    private $adminModel;

    public function __construct() {
        $this->userModel = new User();
        $this->studentModel = new Student();
        $this->adminModel = new Admin();
    }

    public function unifiedLogin() {
        if (isAdmin()) redirect(BASE_URL . "/admin/dashboard");
        if (isStudent()) redirect(BASE_URL . "/student/dashboard");
        $this->view("auth/student_login", ["pageTitle" => "Portal Login - " . APP_NAME]);
    }

    public function studentLogin() {
        $this->unifiedLogin();
    }

    public function adminLogin() {
        $this->unifiedLogin();
    }

    public function unifiedLoginPost() {
        if (!validateCSRF($_POST["csrf_token"] ?? "")) {
            flash("error", "Invalid request. Please try again.", "danger");
            redirect(BASE_URL . "/student/login"); return;
        }
        $loginInput = trim($_POST["login_identifier"] ?? $_POST["email"] ?? $_POST["username"] ?? $_POST["student_id"] ?? "");
        $password = $_POST["password"] ?? "";
        if (empty($loginInput) || empty($password)) {
            flash("error", "Gmail address or Username and password are required.", "danger");
            redirect(BASE_URL . "/student/login"); return;
        }

        // 1. Search in users table by email
        $user = $this->userModel->findOne(["email" => $loginInput]);

        // 2. Search in users table by username
        if (!$user) {
            $user = $this->userModel->findByUsername($loginInput);
        }

        // 3. Search in students table by email_address
        if (!$user) {
            $student = $this->studentModel->findOne(["email_address" => $loginInput]);
            if ($student && !empty($student["user_id"])) {
                $user = $this->userModel->findById($student["user_id"]);
            }
        }

        // 4. Search in students table by student_id
        if (!$user) {
            $student = $this->studentModel->findOne(["student_id" => $loginInput]);
            if ($student && !empty($student["user_id"])) {
                $user = $this->userModel->findById($student["user_id"]);
            }
        }

        if (!$user) {
            flash("error", "Invalid credentials. Please check your Gmail address / username and password.", "danger");
            redirect(BASE_URL . "/student/login"); return;
        }
        if (!$user["is_active"]) {
            flash("error", "Your account has been deactivated. Please contact OSAS.", "danger");
            redirect(BASE_URL . "/student/login"); return;
        }
        if ($this->userModel->isLocked($user["id"])) {
            flash("error", "Your account is temporarily locked due to too many failed login attempts. Please try again later.", "danger");
            redirect(BASE_URL . "/student/login"); return;
        }
        if (!password_verify($password, $user["password"])) {
            $attempts = $this->userModel->incrementLoginAttempts($user["id"]);
            $remaining = MAX_LOGIN_ATTEMPTS - $attempts;
            if ($remaining > 0) flash("error", "Invalid password. $remaining attempt(s) remaining before lockout.", "danger");
            else flash("error", "Account locked for 15 minutes due to too many failed attempts.", "danger");
            redirect(BASE_URL . "/student/login"); return;
        }

        $this->userModel->resetLoginAttempts($user["id"]);
        $this->userModel->updateLastLogin($user["id"]);
        session_regenerate_id(true);
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_type"] = $user["user_type"];
        $_SESSION["last_regenerated"] = time();

        if ($user["user_type"] === "admin") {
            $admin = $this->adminModel->getAdminWithRole($this->adminModel->findByUserId($user["id"])["id"] ?? 0);
            if (!$admin) {
                flash("error", "Admin profile not found. Please contact OSAS.", "danger");
                redirect(BASE_URL . "/student/login"); return;
            }
            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_name"] = $admin["first_name"] . " " . $admin["last_name"];
            $_SESSION["role_slug"] = $admin["role_slug"];
            $_SESSION["role_name"] = $admin["role_name"];
            $_SESSION["role_permissions"] = $admin["permissions"];
            flash("success", "Welcome, " . $admin["first_name"] . "!", "success");
            redirect(BASE_URL . "/admin/dashboard");
            return;
        }

        if ($user["user_type"] === "student") {
            $student = $this->studentModel->findByUserId($user["id"]);
            if (!$student) {
                flash("error", "Student profile not found. Please contact OSAS.", "danger");
                redirect(BASE_URL . "/student/login"); return;
            }
            $_SESSION["student_id"] = $student["id"];
            $_SESSION["student_number"] = $student["student_id"];
            $_SESSION["student_name"] = $student["first_name"] . " " . $student["last_name"];
            flash("success", "Welcome back, " . $student["first_name"] . "!", "success");
            redirect(BASE_URL . "/student/dashboard");
            return;
        }
    }

    public function studentLoginPost() {
        $this->unifiedLoginPost();
    }

    public function studentLogout() {
        session_unset(); session_destroy();
        redirect(BASE_URL . "/student/login");
    }

    public function studentRegister() {
        if (isStudent()) redirect(BASE_URL . "/student/dashboard");
        $this->view("auth/student_register", ["pageTitle" => "Create Account - " . APP_NAME]);
    }

    public function studentRegisterPost() {
        if (!validateCSRF($_POST["csrf_token"] ?? "")) { redirect(BASE_URL . "/student/register"); return; }
        $studentId = trim($_POST["student_id"] ?? "");
        $firstName = trim($_POST["first_name"] ?? "");
        $lastName = trim($_POST["last_name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";
        $confirm = $_POST["confirm_password"] ?? "";
        if (empty($studentId) || empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            flash("error", "All required fields must be filled.", "danger");
            redirect(BASE_URL . "/student/register"); return;
        }
        if ($password !== $confirm) { flash("error", "Passwords do not match.", "danger"); redirect(BASE_URL . "/student/register"); return; }
        if (strlen($password) < 8) { flash("error", "Password must be at least 8 characters.", "danger"); redirect(BASE_URL . "/student/register"); return; }
        if ($this->userModel->findByUsername($studentId)) { flash("error", "Student ID is already registered.", "danger"); redirect(BASE_URL . "/student/register"); return; }
        if ($this->userModel->findByEmail($email)) { flash("error", "Email is already registered.", "danger"); redirect(BASE_URL . "/student/register"); return; }
        try {
            $userId = $this->userModel->createUser(["username" => $studentId, "password" => $password, "email" => $email, "user_type" => "student"]);
            $this->studentModel->insert(["user_id" => $userId, "student_id" => $studentId, "first_name" => sanitize($firstName), "last_name" => sanitize($lastName), "middle_name" => sanitize($_POST["middle_name"] ?? ""), "degree_program" => sanitize($_POST["degree_program"] ?? ""), "year_level" => (int)($_POST["year_level"] ?? 1), "email_address" => $email]);
            flash("success", "Account created successfully. Please login.", "success");
            redirect(BASE_URL . "/student/login");
        } catch (Exception $e) {
            flash("error", "Registration failed. Please try again.", "danger");
            redirect(BASE_URL . "/student/register");
        }
    }

    public function forgotPassword() {
        $this->view("auth/forgot_password", ["pageTitle" => "Forgot Password - " . APP_NAME]);
    }

    public function forgotPasswordPost() {
        flash("info", "Please contact the OSAS office to reset your password. Present your valid student ID.", "info");
        redirect(BASE_URL . "/student/login");
    }


    public function adminLogout() {
        session_unset(); session_destroy();
        redirect(BASE_URL . "/student/login");
    }

    public function logout() {
        session_unset(); session_destroy();
        redirect(BASE_URL . "/student/login");
    }
}