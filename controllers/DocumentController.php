<?php
require_once __DIR__ . "/../lib/BaseController.php";
require_once __DIR__ . "/../models/Document.php";
require_once __DIR__ . "/../models/Application.php";
require_once __DIR__ . "/../models/Requirement.php";
require_once __DIR__ . "/../models/Student.php";
require_once __DIR__ . "/../models/ScholarshipApplication.php";
require_once __DIR__ . "/../models/AcademicScholarApplication.php";
require_once __DIR__ . "/../models/StudentAssistantApplication.php";
require_once __DIR__ . "/../models/FamilyMember.php";
require_once __DIR__ . "/../models/Evaluation.php";
require_once __DIR__ . "/../models/EducationalAssistance.php";
require_once __DIR__ . "/../lib/PdfGenerator.php";

class DocumentController extends BaseController {
    public function serve() {
        requireLogin();
        $filename = sanitize($this->getGet("file", ""));
        if (empty($filename) || !preg_match("/^[a-zA-Z0-9_\-.]+$/", $filename)) { http_response_code(404); exit("File not found."); }
        $filepath = UPLOAD_PATH . $filename;
        if (!file_exists($filepath)) { http_response_code(404); exit("File not found."); }
        $docModel = new Document();
        $doc = $docModel->findOne(["stored_filename" => $filename]);
        if (!$doc) { http_response_code(404); exit("Document not found."); }
        if (isStudent()) {
            $appModel = new Application();
            $app = $appModel->findById($doc["application_id"]);
            if (!$app || $app["student_id"] != $_SESSION["student_id"]) { http_response_code(403); exit("Access denied."); }
        }
        $mimeTypes = ["pdf" => "application/pdf", "jpg" => "image/jpeg", "jpeg" => "image/jpeg", "png" => "image/png"];
        $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
        $mime = $mimeTypes[$ext] ?? "application/octet-stream";
        header("Content-Type: $mime");
        header("Content-Length: " . filesize($filepath));
        header("Cache-Control: private, max-age=3600");
        readfile($filepath); exit;
    }

    public function upload() {
        requireStudent();
        if (!isset($_FILES["document"])) { $this->jsonResponse(["success" => false, "message" => "No file uploaded."], 400); return; }
        $file = $_FILES["document"];
        $requirementId = (int)($_POST["requirement_id"] ?? 0);
        $applicationId = (int)($_POST["application_id"] ?? 0);
        $appModel = new Application();
        $app = $appModel->findById($applicationId);
        if (!$app || $app["student_id"] != $_SESSION["student_id"]) { $this->jsonResponse(["success" => false, "message" => "Access denied."], 403); return; }
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXTENSIONS)) { $this->jsonResponse(["success" => false, "message" => "Invalid file type. Allowed: " . implode(", ", ALLOWED_EXTENSIONS)], 400); return; }
        if ($file["size"] > MAX_FILE_SIZE) { $this->jsonResponse(["success" => false, "message" => "File too large. Maximum: 5MB."], 400); return; }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file["tmp_name"]);
        finfo_close($finfo);
        $allowedMimes = ["application/pdf", "image/jpeg", "image/png"];
        if (!in_array($mime, $allowedMimes)) { $this->jsonResponse(["success" => false, "message" => "Invalid file content."], 400); return; }
        $storedName = uniqid("doc_", true) . "_" . time() . "." . $ext;
        $destPath = UPLOAD_PATH . $storedName;
        if (!move_uploaded_file($file["tmp_name"], $destPath)) { $this->jsonResponse(["success" => false, "message" => "Upload failed."], 500); return; }
        $docModel = new Document();
        $existing = $docModel->findByReqAndApp($applicationId, $requirementId);
        if ($existing) { if (file_exists($existing["file_path"])) unlink($existing["file_path"]); $docModel->delete($existing["id"]); }
        $docId = $docModel->saveDocument(["application_id" => $applicationId, "requirement_id" => $requirementId, "original_filename" => $file["name"], "stored_filename" => $storedName, "file_path" => $destPath, "file_type" => $mime, "file_size" => $file["size"], "upload_status" => "Pending"]);
        $this->jsonResponse(["success" => true, "document_id" => $docId, "filename" => $file["name"], "size" => $file["size"], "serve_url" => BASE_URL . "/document/serve?file=" . urlencode($storedName)]);
    }

    public function download() {
        requireAdmin();
        $id = (int)$this->getGet("id");
        $docModel = new Document();
        $doc = $docModel->findById($id);
        if (!$doc || !file_exists($doc["file_path"])) { http_response_code(404); exit("File not found."); }
        $ext = strtolower(pathinfo($doc["file_path"], PATHINFO_EXTENSION));
        $mimes = ["pdf" => "application/pdf", "jpg" => "image/jpeg", "jpeg" => "image/jpeg", "png" => "image/png"];
        $mime = $mimes[$ext] ?? "application/octet-stream";
        header("Content-Type: $mime");
        header("Content-Disposition: attachment; filename=" . $doc["original_filename"]);
        header("Content-Length: " . filesize($doc["file_path"]));
        readfile($doc["file_path"]); exit;
    }

    public function generatePdf() {
        requireLogin();
        $id = (int)$this->getGet("id");
        if (!$id) { http_response_code(404); exit("Invalid application ID."); }
        $appModel = new Application();
        $app = $appModel->getApplicationWithDetails($id);
        if (!$app) { http_response_code(404); exit("Application not found."); }
        if (isStudent() && $app["student_id"] != $_SESSION["student_id"]) { http_response_code(403); exit("Access denied."); }

        $evaluations = (new Evaluation())->getEvaluationHistory($id);
        $lastEval = !empty($evaluations) ? $evaluations[0] : null;

        // Clean any active output buffers to avoid TCPDF premature output errors
        while (ob_get_level()) {
            ob_end_clean();
        }

        if ($app["type_slug"] === "scholarship") {
            $schApp = (new ScholarshipApplication())->findByApplicationId($id) ?: [];
            $familyMembers = (new FamilyMember())->findByApplicationId($id) ?: [];
            $eduAssistance = (new EducationalAssistance())->findByApplicationId($id) ?: [];
            PdfGenerator::generateScholarshipForm($app, $schApp, $familyMembers, $eduAssistance, $lastEval, 'I');
            exit;
        } elseif ($app["type_slug"] === "academic_achiever") {
            $acaApp = (new AcademicScholarApplication())->findByApplicationId($id) ?: [];
            PdfGenerator::generateAcademicForm($app, $acaApp, $lastEval, 'I');
            exit;
        } elseif ($app["type_slug"] === "student_assistant") {
            $saApp = (new StudentAssistantApplication())->getWithPosition($id) ?: [];
            PdfGenerator::generateAssistantForm($app, $saApp, $lastEval, 'I');
            exit;
        } else {
            // Fallback for any other type
            $schApp = (new ScholarshipApplication())->findByApplicationId($id) ?: [];
            $familyMembers = (new FamilyMember())->findByApplicationId($id) ?: [];
            $eduAssistance = (new EducationalAssistance())->findByApplicationId($id) ?: [];
            PdfGenerator::generateScholarshipForm($app, $schApp, $familyMembers, $eduAssistance, $lastEval, 'I');
            exit;
        }
    }
}