<?php
require_once __DIR__ . "/../lib/BaseController.php";
require_once __DIR__ . "/../models/Application.php";
require_once __DIR__ . "/../models/ApplicationType.php";
require_once __DIR__ . "/../models/Student.php";

class ReportController extends BaseController {
    public function index() {
        requireAdmin();
        if (!hasPermission("view_reports")) { 
            flash("error", "Access denied.", "danger"); 
            redirect(BASE_URL . "/admin/dashboard"); 
            return; 
        }

        $allTypes = (new ApplicationType())->getAllTypes();
        // Restrict strictly to the 2 active types: student_assistant and academic_achiever
        $applicationTypes = [];
        foreach ($allTypes as $t) {
            if (in_array($t['slug'], ['student_assistant', 'academic_achiever'])) {
                $applicationTypes[] = $t;
            }
        }

        // Calculate counts for Student Assistant, Academic Achiever, and Total
        $appModel = new Application();
        $saCount = $appModel->countApplications(['type_slug' => 'student_assistant']);
        $aaCount = $appModel->countApplications(['type_slug' => 'academic_achiever']);
        $totalCount = $saCount + $aaCount;

        $this->view("admin/reports", [
            "applicationTypes" => $applicationTypes,
            "saCount" => $saCount,
            "aaCount" => $aaCount,
            "totalCount" => $totalCount,
            "pageTitle" => "Reports & Analytics - " . APP_NAME
        ]);
    }

    public function generate() {
        requireAdmin();
        if (!hasPermission("view_reports")) { 
            $this->jsonResponse(["success" => false, "message" => "Access denied."], 403); 
            return; 
        }

        $typeSlug = trim($_POST["type_slug"] ?? "");
        $filters = [
            "type_slug" => $typeSlug,
            "status" => $_POST["status"] ?? "",
            "year_level" => $_POST["year_level"] ?? "",
            "program" => $_POST["program"] ?? "",
            "date_from" => $_POST["date_from"] ?? "",
            "date_to" => $_POST["date_to"] ?? "",
            "active_types_only" => empty($typeSlug) ? true : false
        ];

        $appModel = new Application();
        $applications = $appModel->getAllApplications($filters, 1, 1000);

        // Format specific details for preview
        foreach ($applications as &$app) {
            $app['specific_details'] = $this->formatSpecificDetails($app);
        }

        $this->jsonResponse([
            "success" => true, 
            "data" => $applications, 
            "count" => count($applications)
        ]);
    }

    public function export() {
        requireAdmin();
        if (!hasPermission("export_reports")) { 
            flash("error", "Access denied.", "danger"); 
            redirect(BASE_URL . "/admin/reports"); 
            return; 
        }

        $format = strtolower($this->getGet("format", "excel"));
        $typeSlug = trim($this->getGet("type_slug", ""));
        $filters = [
            "type_slug" => $typeSlug,
            "status" => $this->getGet("status"),
            "year_level" => $this->getGet("year_level"),
            "program" => $this->getGet("program"),
            "date_from" => $this->getGet("date_from"),
            "date_to" => $this->getGet("date_to"),
            "active_types_only" => empty($typeSlug) ? true : false
        ];

        $appModel = new Application();
        $applications = $appModel->getAllApplications($filters, 1, 10000);

        foreach ($applications as &$app) {
            $app['specific_details'] = $this->formatSpecificDetails($app);
        }

        if ($format === "excel" || $format === "xls") {
            $this->exportExcel($applications, $filters);
        } elseif ($format === "csv") {
            $this->exportCsv($applications);
        } elseif ($format === "pdf" || $format === "print") {
            $this->generateReportPdf($applications);
        } else {
            flash("error", "Unsupported format requested.", "danger");
            redirect(BASE_URL . "/admin/reports");
        }
    }

    private function formatSpecificDetails($app) {
        if ($app['type_slug'] === 'student_assistant') {
            $parts = [];
            if (!empty($app['preferred_office'])) $parts[] = "Office: " . $app['preferred_office'];
            if (!empty($app['preferred_schedule'])) $parts[] = "Schedule: " . $app['preferred_schedule'];
            if (!empty($app['skills'])) $parts[] = "Skills: " . $app['skills'];
            return !empty($parts) ? implode(" | ", $parts) : "General Assistant";
        } elseif ($app['type_slug'] === 'academic_achiever') {
            $parts = [];
            if (!empty($app['scholarship_type'])) $parts[] = $app['scholarship_type'] . " Scholar";
            if (!empty($app['service_category'])) $parts[] = $app['service_category'];
            if (!empty($app['service_category_other'])) $parts[] = "(" . $app['service_category_other'] . ")";
            return !empty($parts) ? implode(" - ", $parts) : "Academic Achiever";
        }
        return "-";
    }

    private function exportExcel($applications, $filters) {
        while (ob_get_level()) { ob_end_clean(); }

        $filename = "QSU_OSAS_Report_" . date("Y-m-d_His") . ".xls";
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Pragma: public");

        // Output UTF-8 BOM
        echo "\xEF\xBB\xBF";
        ?>
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <!--[if gte mso 9]>
            <xml>
                <x:ExcelWorkbook>
                    <x:ExcelWorksheets>
                        <x:ExcelWorksheet>
                            <x:Name>Applications Report</x:Name>
                            <x:WorksheetOptions>
                                <x:DisplayGridlines/>
                            </x:WorksheetOptions>
                        </x:ExcelWorksheet>
                    </x:ExcelWorksheets>
                </x:ExcelWorkbook>
            </xml>
            <![endif]-->
            <style>
                body { font-family: Arial, sans-serif; font-size: 10pt; }
                .univ-title { font-size: 15pt; font-weight: bold; color: #006633; text-align: center; }
                .univ-sub { font-size: 11pt; font-weight: bold; color: #333333; text-align: center; }
                .univ-meta { font-size: 9.5pt; color: #555555; text-align: center; }
                .header-row th {
                    background-color: #006633;
                    color: #ffffff;
                    font-weight: bold;
                    font-size: 10pt;
                    padding: 8px 6px;
                    border: 1px solid #004d26;
                    text-align: center;
                }
                .data-row td {
                    border: 1px solid #cccccc;
                    padding: 6px 6px;
                    vertical-align: middle;
                }
                .zebra { background-color: #f7fbf8; }
                .text-center { text-align: center; }
                .badge-status {
                    font-weight: bold;
                    padding: 3px 6px;
                    border-radius: 4px;
                    text-align: center;
                }
                .summary-table { margin-top: 15px; border-collapse: collapse; }
                .summary-table td { border: 1px solid #999999; padding: 5px 10px; font-size: 9.5pt; }
            </style>
        </head>
        <body>
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr><td colspan="19" class="univ-title">QUIRINO STATE UNIVERSITY</td></tr>
                <tr><td colspan="19" class="univ-sub">OFFICE OF STUDENT AFFAIRS AND SERVICES (OSAS)</td></tr>
                <tr><td colspan="19" class="univ-sub" style="color: #006633;">STUDENT ASSISTANT & ACADEMIC ACHIEVER APPLICATIONS REPORT</td></tr>
                <tr><td colspan="19" class="univ-meta">Generated on: <?= date("F d, Y \a\\t h:i A") ?> | Filter: <?= !empty($filters['type_slug']) ? ucwords(str_replace('_', ' ', $filters['type_slug'])) : 'All (Student Assistant & Academic Achiever)' ?> | Total Records: <?= count($applications) ?></td></tr>
                <tr><td colspan="19" style="height: 12px;"></td></tr>
            </table>

            <table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">
                <thead>
                    <tr class="header-row">
                        <th>#</th>
                        <th>Application Number</th>
                        <th>Student ID</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Ext</th>
                        <th>Degree Program</th>
                        <th>Year Level</th>
                        <th>Sex</th>
                        <th>Civil Status</th>
                        <th>Contact Number</th>
                        <th>Email Address</th>
                        <th>Address / Residence</th>
                        <th>Application Type</th>
                        <th>Specific Details / Category</th>
                        <th>Status</th>
                        <th>Submission Date</th>
                        <th>Evaluation Decision & Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($applications)): ?>
                    <tr>
                        <td colspan="19" class="text-center" style="padding: 20px; color: #777;">No application records found matching the specified criteria.</td>
                    </tr>
                    <?php else: ?>
                    <?php 
                    $idx = 1;
                    foreach ($applications as $app): 
                        $isZebra = ($idx % 2 === 0);
                        $fullAddress = trim(implode(", ", array_filter([$app['street_barangay'] ?? '', $app['municipality'] ?? '', $app['province'] ?? ''])));
                        if (empty($fullAddress)) $fullAddress = "Quirino";
                        
                        $submitted = !empty($app['submitted_at']) ? date("Y-m-d H:i", strtotime($app['submitted_at'])) : "Draft (Not Submitted)";
                        $evalInfo = "-";
                        if (!empty($app['evaluation_decision'])) {
                            $evalInfo = $app['evaluation_decision'] . (!empty($app['evaluation_remarks']) ? " - " . $app['evaluation_remarks'] : "");
                        }
                    ?>
                    <tr class="data-row <?= $isZebra ? 'zebra' : '' ?>">
                        <td class="text-center"><?= $idx++ ?></td>
                        <td style="font-weight: bold; mso-number-format:'\@';"><?= htmlspecialchars($app['application_number'] ?? '') ?></td>
                        <td class="text-center" style="mso-number-format:'\@';"><?= htmlspecialchars($app['student_number'] ?? '') ?></td>
                        <td><?= htmlspecialchars($app['last_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($app['first_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($app['middle_name'] ?? '') ?></td>
                        <td class="text-center"><?= htmlspecialchars($app['extension_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($app['degree_program'] ?? '') ?></td>
                        <td class="text-center"><?= yearLevelLabel($app['year_level'] ?? 1) ?></td>
                        <td class="text-center"><?= htmlspecialchars($app['sex'] ?? '') ?></td>
                        <td class="text-center"><?= htmlspecialchars($app['civil_status'] ?? 'Single') ?></td>
                        <td style="mso-number-format:'\@';"><?= htmlspecialchars($app['contact_number'] ?? '') ?></td>
                        <td><?= htmlspecialchars($app['email_address'] ?? '') ?></td>
                        <td><?= htmlspecialchars($fullAddress) ?></td>
                        <td style="font-weight: bold; color: #006633;"><?= htmlspecialchars($app['type_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($app['specific_details']) ?></td>
                        <td class="text-center" style="font-weight: bold;"><?= htmlspecialchars($app['status'] ?? '') ?></td>
                        <td class="text-center"><?= $submitted ?></td>
                        <td><?= htmlspecialchars($evalInfo) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <br/>
            <table class="summary-table">
                <tr><th colspan="2" style="background: #e2e8f0; font-weight: bold; text-align: left; padding: 5px 10px;">REPORT SUMMARY STATISTICS</th></tr>
                <tr><td>Total Applications</td><td style="font-weight: bold;"><?= count($applications) ?></td></tr>
                <tr><td>Report Generated By</td><td><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Administrator') ?></td></tr>
                <tr><td>Official Form Code</td><td>QSU-OSAS-RPT-002 Rev. 00</td></tr>
            </table>
        </body>
        </html>
        <?php
        exit;
    }

    private function exportCsv($applications) {
        while (ob_get_level()) { ob_end_clean(); }

        $filename = "QSU_OSAS_Report_" . date("Ymd_His") . ".csv";
        header("Content-Type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $out = fopen("php://output", "w");
        // UTF-8 BOM
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($out, [
            "App Number", 
            "Student ID", 
            "Last Name", 
            "First Name", 
            "Middle Name", 
            "Extension",
            "Degree Program", 
            "Year Level", 
            "Sex",
            "Civil Status",
            "Contact Number",
            "Email Address",
            "Address",
            "Application Type", 
            "Specific Details",
            "Status", 
            "Date Submitted",
            "Evaluation Decision",
            "Evaluation Remarks"
        ]);

        foreach ($applications as $app) {
            $fullAddress = trim(implode(", ", array_filter([$app['street_barangay'] ?? '', $app['municipality'] ?? '', $app['province'] ?? ''])));
            fputcsv($out, [
                $app["application_number"], 
                $app["student_number"], 
                $app["last_name"], 
                $app["first_name"], 
                $app["middle_name"] ?? '',
                $app["extension_name"] ?? '',
                $app["degree_program"], 
                yearLevelLabel($app["year_level"]), 
                $app["sex"] ?? '',
                $app["civil_status"] ?? '',
                $app["contact_number"] ?? '',
                $app["email_address"] ?? '',
                $fullAddress,
                $app["type_name"], 
                $app["specific_details"],
                $app["status"], 
                $app["submitted_at"] ?? "Draft",
                $app["evaluation_decision"] ?? '',
                $app["evaluation_remarks"] ?? ''
            ]);
        }
        fclose($out); 
        exit;
    }

    private function generateReportPdf($applications) {
        while (ob_get_level()) { ob_end_clean(); }
        $filename = "QSU_OSAS_Report_" . date("Ymd_His") . ".html";
        header("Content-Type: text/html; charset=utf-8");
        header("Content-Disposition: inline; filename=\"$filename\"");
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>QSU OSAS Application Summary Report</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; color: #222; }
                .header { text-align: center; border-bottom: 2px solid #006633; padding-bottom: 12px; margin-bottom: 15px; }
                .header h2 { margin: 0; color: #006633; font-size: 18px; }
                .header h4 { margin: 4px 0; font-size: 13px; color: #444; }
                .header p { margin: 2px 0; font-size: 11px; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th { background-color: #006633; color: white; padding: 7px 5px; border: 1px solid #004d26; text-align: left; font-size: 10.5px; }
                td { border: 1px solid #ddd; padding: 6px 5px; font-size: 10.5px; }
                tr:nth-child(even) { background-color: #f9fbf9; }
                .badge { font-weight: bold; }
                .footer { margin-top: 25px; display: flex; justify-content: space-between; font-size: 10px; color: #666; }
                @media print {
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="no-print" style="margin-bottom: 15px; text-align: right;">
                <button onclick="window.print()" style="padding: 8px 16px; background: #006633; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                    Print Report
                </button>
            </div>
            <div class="header">
                <h2>QUIRINO STATE UNIVERSITY</h2>
                <h4>OFFICE OF STUDENT AFFAIRS AND SERVICES (OSAS)</h4>
                <p>Student Assistant & Academic Achiever Applications Master Summary</p>
                <p>Generated: <?= date("F d, Y h:i A") ?> | Total Records: <?= count($applications) ?></p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>App Number</th>
                        <th>Student ID</th>
                        <th>Applicant Name</th>
                        <th>Program</th>
                        <th>Year</th>
                        <th>Type</th>
                        <th>Specific Details</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    foreach ($applications as $app): 
                    ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><strong><?= htmlspecialchars($app['application_number']) ?></strong></td>
                        <td><?= htmlspecialchars($app['student_number']) ?></td>
                        <td><?= htmlspecialchars($app['last_name'] . ', ' . $app['first_name'] . ' ' . ($app['middle_name'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($app['degree_program'] ?? '') ?></td>
                        <td><?= yearLevelLabel($app['year_level'] ?? 1) ?></td>
                        <td><?= htmlspecialchars($app['type_name']) ?></td>
                        <td><?= htmlspecialchars($app['specific_details']) ?></td>
                        <td><strong><?= htmlspecialchars($app['status']) ?></strong></td>
                        <td><?= !empty($app['submitted_at']) ? date("M d, Y", strtotime($app['submitted_at'])) : "Draft" ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="footer">
                <div>Form: QSU-OSAS-RPT-002 &bull; Quirino State University</div>
                <div>Printed by: <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Administrator') ?></div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}