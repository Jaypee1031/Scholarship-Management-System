<?php
// models/User.php
require_once __DIR__ . "/../lib/BaseModel.php";
class User extends BaseModel {
    protected $table = "users";
    public function findByUsername($username) {
        return $this->findOne(["username" => $username]);
    }
    public function findByEmail($email) { return $this->findOne(["email" => $email]); }
    public function isLocked($userId) {
        $user = $this->findById($userId);
        if (!$user || !$user["locked_until"]) return false;
        return strtotime($user["locked_until"]) > time();
    }
    public function lockAccount($userId, $minutes = 15) {
        $until = date("Y-m-d H:i:s", time() + ($minutes * 60));
        $this->update($userId, ["locked_until" => $until]);
    }
    public function incrementLoginAttempts($userId) {
        $this->db->prepare("UPDATE users SET login_attempts = login_attempts + 1 WHERE id = ?")->execute([$userId]);
        $user = $this->findById($userId);
        if ($user["login_attempts"] >= MAX_LOGIN_ATTEMPTS) $this->lockAccount($userId);
        return $user["login_attempts"] + 1;
    }
    public function resetLoginAttempts($userId) {
        $this->update($userId, ["login_attempts" => 0, "locked_until" => null]);
    }
    public function updateLastLogin($userId) {
        $this->update($userId, ["last_login" => date("Y-m-d H:i:s")]);
    }
    public function createUser($data) {
        $data["password"] = password_hash($data["password"], PASSWORD_BCRYPT, ["cost" => BCRYPT_COST]);
        return $this->insert($data);
    }
    public function changePassword($userId, $newPassword) {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ["cost" => BCRYPT_COST]);
        return $this->update($userId, ["password" => $hash]);
    }
    public function verifyPassword($username, $password) {
        $user = $this->findByUsername($username);
        if (!$user) return false;
        if (!password_verify($password, $user["password"])) return false;
        return $user;
    }
    public function getAllByType($type) {
        return $this->findAll(["user_type" => $type], "created_at DESC");
    }
}
