<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class Application extends BaseModel {
    protected $table = "applications";
    public function findByApplicationNumber($num) { return $this->findOne(["application_number" => $num]); }
    public function findByStudentId($studentId) {
        $stmt = $this->query("SELECT a.*, at.name as type_name, at.slug as type_slug FROM applications a JOIN application_types at ON a.application_type_id = at.id WHERE a.student_id = ? ORDER BY a.created_at DESC", [$studentId]);
        return $stmt->fetchAll();
    }
    public function findByStudentAndPeriod($studentId, $periodId) {
        return $this->findOne(["student_id" => $studentId, "application_period_id" => $periodId]);
    }
    public function getApplicationWithDetails($id) {
        $stmt = $this->query("SELECT a.*, s.student_id as student_number, s.last_name, s.first_name, s.middle_name, s.extension_name, s.degree_program, s.year_level, s.sex, s.civil_status, s.is_solo_parent, s.citizenship, s.birthdate, s.place_of_birth, s.physical_disability, s.ethnic_affiliation, s.street_barangay, s.municipality, s.province, s.zip_code, s.contact_number, s.email_address, s.is_dormitory, s.registering_as, s.lrn_number, at.name as type_name, at.slug as type_slug, at.description as type_description, ap.name as period_name, ap.semester, ap.academic_year, adm.first_name as assigned_first, adm.last_name as assigned_last FROM applications a JOIN students s ON a.student_id = s.id JOIN application_types at ON a.application_type_id = at.id LEFT JOIN application_periods ap ON a.application_period_id = ap.id LEFT JOIN admins adm ON a.assigned_admin_id = adm.id WHERE a.id = ?", [$id]);
        return $stmt->fetch();
    }
    public function getAllApplications($filters = [], $page = 1, $perPage = 20) {
        $sql = "SELECT a.*, 
                       s.student_id as student_number, 
                       s.last_name, s.first_name, s.middle_name, s.extension_name, 
                       s.degree_program, s.year_level, s.sex, s.civil_status, 
                       s.contact_number, s.email_address, s.street_barangay, s.municipality, s.province,
                       at.name as type_name, at.slug as type_slug, 
                       adm.first_name as assigned_first, adm.last_name as assigned_last,
                       sa.preferred_office, sa.preferred_schedule, sa.skills, sa.sa_status,
                       asa.scholarship_type, asa.service_category, asa.service_category_other,
                       ev.decision as evaluation_decision, ev.remarks as evaluation_remarks
                FROM applications a 
                JOIN students s ON a.student_id = s.id 
                JOIN application_types at ON a.application_type_id = at.id 
                LEFT JOIN admins adm ON a.assigned_admin_id = adm.id
                LEFT JOIN student_assistant_applications sa ON sa.application_id = a.id
                LEFT JOIN academic_scholar_applications asa ON asa.application_id = a.id
                LEFT JOIN (
                    SELECT e1.* FROM evaluations e1
                    JOIN (SELECT application_id, MAX(id) as max_id FROM evaluations GROUP BY application_id) e2
                    ON e1.id = e2.max_id
                ) ev ON ev.application_id = a.id
                WHERE 1=1";
        $params = [];
        if (!empty($filters["type_slug"])) { 
            $sql .= " AND at.slug = ?"; 
            $params[] = $filters["type_slug"]; 
        } elseif (!empty($filters["active_types_only"])) {
            $sql .= " AND at.slug IN ('student_assistant', 'academic_achiever')";
        }
        if (!empty($filters["status"])) { $sql .= " AND a.status = ?"; $params[] = $filters["status"]; }
        if (!empty($filters["year_level"])) { $sql .= " AND s.year_level = ?"; $params[] = $filters["year_level"]; }
        if (!empty($filters["search"])) { $sql .= " AND (s.student_id LIKE ? OR s.last_name LIKE ? OR s.first_name LIKE ? OR a.application_number LIKE ?)"; $like = "%" . $filters["search"] . "%"; $params = array_merge($params, [$like, $like, $like, $like]); }
        if (!empty($filters["program"])) { $sql .= " AND s.degree_program LIKE ?"; $params[] = "%" . $filters["program"] . "%"; }
        if (!empty($filters["date_from"])) { $sql .= " AND DATE(a.submitted_at) >= ?"; $params[] = $filters["date_from"]; }
        if (!empty($filters["date_to"])) { $sql .= " AND DATE(a.submitted_at) <= ?"; $params[] = $filters["date_to"]; }
        $sql .= " ORDER BY a.created_at DESC";
        $offset = ($page - 1) * $perPage;
        $sql .= " LIMIT $perPage OFFSET $offset";
        return $this->query($sql, $params)->fetchAll();
    }
    public function countApplications($filters = []) {
        $sql = "SELECT COUNT(*) FROM applications a JOIN students s ON a.student_id = s.id JOIN application_types at ON a.application_type_id = at.id WHERE 1=1";
        $params = [];
        if (!empty($filters["type_slug"])) { $sql .= " AND at.slug = ?"; $params[] = $filters["type_slug"]; }
        if (!empty($filters["status"])) { $sql .= " AND a.status = ?"; $params[] = $filters["status"]; }
        if (!empty($filters["search"])) { $sql .= " AND (s.student_id LIKE ? OR s.last_name LIKE ? OR s.first_name LIKE ?)"; $like = "%" . $filters["search"] . "%"; $params = array_merge($params, [$like, $like, $like]); }
        return (int)$this->query($sql, $params)->fetchColumn();
    }
    public function countByStatus() {
        $stmt = $this->query("SELECT status, COUNT(*) as cnt FROM applications GROUP BY status");
        $result = [];
        foreach ($stmt->fetchAll() as $row) $result[$row["status"]] = (int)$row["cnt"];
        return $result;
    }
    public function countByType() {
        $stmt = $this->query("SELECT at.slug, at.name, COUNT(a.id) as cnt FROM application_types at LEFT JOIN applications a ON at.id = a.application_type_id GROUP BY at.id");
        $result = [];
        foreach ($stmt->fetchAll() as $row) $result[$row["slug"]] = ["name" => $row["name"], "count" => (int)$row["cnt"]];
        return $result;
    }
    public function updateStatus($id, $newStatus, $adminId = null, $studentId = null, $remarks = "") {
        $app = $this->findById($id);
        if (!$app) return false;
        $oldStatus = $app["status"];
        $this->update($id, ["status" => $newStatus, "updated_at" => date("Y-m-d H:i:s")]);
        $this->db->prepare("INSERT INTO application_status_history (application_id, old_status, new_status, changed_by_admin_id, changed_by_student_id, remarks) VALUES (?,?,?,?,?,?)")->execute([$id, $oldStatus, $newStatus, $adminId, $studentId, $remarks]);
        // Notify student
        $msgs = ["Submitted" => "Your application has been successfully submitted.", "Under Review" => "Your application is now under review.", "For Revision" => "Your application requires additional information or corrections.", "Qualified" => "Congratulations! Your application has been marked QUALIFIED.", "Disqualified" => "Your application has been marked DISQUALIFIED.", "Approved" => "Congratulations! Your application has been APPROVED.", "Rejected" => "Your application has been rejected."];
        if (isset($msgs[$newStatus])) {
            $student = $this->query("SELECT student_id FROM applications WHERE id = ?", [$id])->fetchColumn();
            if ($student) sendNotification($student, "Application Status Update: $newStatus", $msgs[$newStatus], strtolower($newStatus), $id);
        }
        return true;
    }
    public function getStatusHistory($appId) {
        return $this->query("SELECT h.*, a.first_name as admin_first, a.last_name as admin_last FROM application_status_history h LEFT JOIN admins a ON h.changed_by_admin_id = a.id WHERE h.application_id = ? ORDER BY h.created_at ASC", [$appId])->fetchAll();
    }
    public function getRecentApplications($limit = 10) {
        return $this->query("SELECT a.*, s.student_id as student_number, s.last_name, s.first_name, at.name as type_name, at.slug as type_slug FROM applications a JOIN students s ON a.student_id = s.id JOIN application_types at ON a.application_type_id = at.id ORDER BY a.created_at DESC LIMIT $limit")->fetchAll();
    }
    public function generateUniqueAppNumber($typeSlug) {
        $attempts = 0;
        do {
            $num = generateApplicationNumber($typeSlug);
            $exists = $this->findByApplicationNumber($num);
            $attempts++;
        } while ($exists && $attempts < 10);
        return $num;
    }
    public function getStudentApplication($studentId, $periodId = null) {
        if ($periodId) return $this->findOne(["student_id" => $studentId, "application_period_id" => $periodId]);
        $results = $this->query("SELECT * FROM applications WHERE student_id = ? ORDER BY created_at DESC LIMIT 1", [$studentId])->fetchAll();
        return $results ? $results[0] : null;
    }
}
