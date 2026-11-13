<?php
// lib/PdfGenerator.php
require_once __DIR__ . '/tcpdf/tcpdf.php';

class PdfGenerator {

    /**
     * Generate QSU-SCH-F003: Scholarship and Financial Assistance Form
     */
    public static function generateScholarshipForm($app, $schApp, $familyMembers, $eduAssistance, $evaluation = null, $dest = 'I') {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(12, 10, 12);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        $logoPath = dirname(__DIR__) . '/public/assets/img/qsulogo.png';

        $chk = function($cond) {
            return $cond ? '<b>[ X ]</b>' : '[ &nbsp; ]';
        };

        $bdate = !empty($app['birthdate']) ? date('d / F / Y', strtotime($app['birthdate'])) : ' &nbsp; / &nbsp; / &nbsp; ';
        $certDate = !empty($app['submitted_at']) ? date('M d, Y', strtotime($app['submitted_at'])) : date('M d, Y');

        $father = null;
        $mother = null;
        $siblings = [];
        if (!empty($familyMembers)) {
            foreach ($familyMembers as $m) {
                if ($m['relationship'] === 'Father') $father = $m;
                elseif ($m['relationship'] === 'Mother') $mother = $m;
                elseif ($m['relationship'] === 'Sibling') $siblings[] = $m;
            }
        }

        $fLiving = ($father && ($father['member_status'] ?? 'Living') === 'Living');
        $fDeceased = ($father && ($father['member_status'] ?? '') === 'Deceased');
        $mLiving = ($mother && ($mother['member_status'] ?? 'Living') === 'Living');
        $mDeceased = ($mother && ($mother['member_status'] ?? '') === 'Deceased');

        $fRow = '<tr>
            <td width="16%"><b>Father</b><br>' . $chk($fLiving) . ' Living &nbsp; ' . $chk($fDeceased) . ' Deceased</td>
            <td width="15%">' . htmlspecialchars($father['last_name'] ?? '') . '</td>
            <td width="15%">' . htmlspecialchars($father['first_name'] ?? '') . '</td>
            <td width="14%">' . htmlspecialchars($father['middle_name'] ?? '') . '</td>
            <td width="7%" align="center">' . htmlspecialchars($father['age'] ?? '') . '</td>
            <td width="17%">' . htmlspecialchars($father['ethnic_affiliation'] ?? '') . '</td>
            <td width="16%">' . htmlspecialchars($father['occupation'] ?? '') . '</td>
        </tr>';

        $mRow = '<tr>
            <td width="16%"><b>Mother</b><br>' . $chk($mLiving) . ' Living &nbsp; ' . $chk($mDeceased) . ' Deceased</td>
            <td width="15%">' . htmlspecialchars($mother['last_name'] ?? '') . '</td>
            <td width="15%">' . htmlspecialchars($mother['first_name'] ?? '') . '</td>
            <td width="14%">' . htmlspecialchars($mother['middle_name'] ?? '') . '</td>
            <td width="7%" align="center">' . htmlspecialchars($mother['age'] ?? '') . '</td>
            <td width="17%">' . htmlspecialchars($mother['ethnic_affiliation'] ?? '') . '</td>
            <td width="16%">' . htmlspecialchars($mother['occupation'] ?? '') . '</td>
        </tr>';

        $sibRows = '';
        if (!empty($siblings)) {
            $first = true;
            foreach ($siblings as $sib) {
                $sibRows .= '<tr>
                    <td width="16%">' . ($first ? '<b>Sibling/s</b>' : '') . '</td>
                    <td width="15%">' . htmlspecialchars($sib['last_name'] ?? '') . '</td>
                    <td width="15%">' . htmlspecialchars($sib['first_name'] ?? '') . '</td>
                    <td width="14%">' . htmlspecialchars($sib['middle_name'] ?? '') . '</td>
                    <td width="7%" align="center">' . htmlspecialchars($sib['age'] ?? '') . '</td>
                    <td width="17%">' . htmlspecialchars($sib['ethnic_affiliation'] ?? '') . '</td>
                    <td width="16%">' . htmlspecialchars($sib['occupation'] ?? '') . '</td>
                </tr>';
                $first = false;
            }
        } else {
            $sibRows = '<tr>
                <td width="16%"><b>Sibling/s</b></td>
                <td width="15%"></td>
                <td width="15%"></td>
                <td width="14%"></td>
                <td width="7%"></td>
                <td width="17%"></td>
                <td width="16%"></td>
            </tr>';
        }

        $hasEA = !empty($schApp['has_other_assistance']);
        $ea1 = $eduAssistance[0] ?? ['assistance_type' => '', 'grantee' => '', 'institution_agency' => '', 'amount' => ''];
        $ea2 = $eduAssistance[1] ?? ['assistance_type' => '', 'grantee' => '', 'institution_agency' => '', 'amount' => ''];

        $evalRemarks = !empty($evaluation['remarks']) ? htmlspecialchars($evaluation['remarks']) : '';

        $html = '<table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="18%" align="left">';
        if (file_exists($logoPath)) {
            $html .= '<img src="' . $logoPath . '" width="62" height="62">';
        }
        $html .= '</td>
                <td width="64%" align="center" style="line-height: 1.3;">
                    <span style="font-size: 13pt; font-weight: bold; letter-spacing: 0.5px;">QUIRINO STATE UNIVERSITY</span><br>
                    <span style="font-size: 8.5pt;">Office of the Scholarship and Financial Assistance</span><br><br>
                    <span style="font-size: 11pt; font-weight: bold;">SCHOLARSHIP AND FINANCIAL ASSISTANCE</span><br>
                    <span style="font-size: 10.5pt; font-weight: bold;">APPLICATION FORM</span>
                </td>
                <td width="18%" align="right">
                    <table border="1" cellpadding="0" cellspacing="0" width="85" height="95">
                        <tr>
                            <td align="center" valign="middle" style="font-size: 7.5pt; color: #555; height: 75px;">
                                <br><br>Passport Size<br>ID Picture
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div style="font-size: 2pt;">&nbsp;</div>

        <table width="100%" cellpadding="2" cellspacing="0" border="1">
            <tr style="background-color: #f2f2f2;">
                <td colspan="5" align="center" style="font-size: 8.5pt; font-weight: bold;">STUDENT\'S PERSONAL INFORMATION</td>
            </tr>
            <tr style="font-size: 8pt;">
                <td width="20%"><b>Last Name:</b><br>' . htmlspecialchars($app['last_name'] ?? '') . '</td>
                <td width="20%"><b>First Name:</b><br>' . htmlspecialchars($app['first_name'] ?? '') . '</td>
                <td width="20%"><b>Middle Name:</b><br>' . htmlspecialchars($app['middle_name'] ?? '') . '</td>
                <td width="15%"><b>Extension:</b><br>' . htmlspecialchars($app['extension_name'] ?? '') . '</td>
                <td width="25%"><b>ID No.:</b><br>' . htmlspecialchars($app['student_number'] ?? '') . '</td>
            </tr>
            <tr style="font-size: 8pt;">
                <td colspan="3" width="55%"><b>Degree Program:</b> ' . htmlspecialchars($app['degree_program'] ?? '') . '</td>
                <td width="20%"><b>Sex:</b><br>' . $chk(($app['sex'] ?? '') === 'Male') . ' Male &nbsp; ' . $chk(($app['sex'] ?? '') === 'Female') . ' Female</td>
                <td width="25%"><b>Birthdate:</b><br>' . $bdate . '</td>
            </tr>
            <tr style="font-size: 7.5pt;">
                <td colspan="3" width="55%">
                    <b>Year Level:</b><br>
                    ' . $chk(($app['year_level'] ?? 1) == 1) . ' 1st year &nbsp; ' . $chk(($app['year_level'] ?? 1) == 2) . ' 2nd year &nbsp; ' . $chk(($app['year_level'] ?? 1) == 3) . ' 3rd year &nbsp; ' . $chk(($app['year_level'] ?? 1) == 4) . ' 4th year &nbsp; ' . $chk(($app['year_level'] ?? 1) == 5) . ' 5th year
                </td>
                <td rowspan="2" width="20%">
                    <b>Status:</b><br>
                    ' . $chk(($app['civil_status'] ?? '') === 'Single') . ' Single<br>
                    ' . $chk(($app['civil_status'] ?? '') === 'Married') . ' Married<br>
                    ' . $chk(($app['civil_status'] ?? '') === 'Separated') . ' Separated
                </td>
                <td rowspan="2" width="25%">
                    <b>Place of Birth:</b><br>' . htmlspecialchars($app['place_of_birth'] ?? '') . '
                </td>
            </tr>
            <tr style="font-size: 7.5pt;">
                <td colspan="3" width="55%">
                    <b>Registering as:</b><br>
                    ' . $chk(($app['registering_as'] ?? '') === 'New Student') . ' New Student &nbsp;&nbsp;&nbsp; ' . $chk(($app['registering_as'] ?? '') === 'Old Student') . ' Old Student<br>
                    ' . $chk(($app['registering_as'] ?? '') === 'Transfer In') . ' Transfer In &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ' . $chk(($app['registering_as'] ?? '') === 'Lateral Transfer') . ' Lateral Transfer<br>
                    ' . $chk(($app['registering_as'] ?? '') === 'Cross Enrollee') . ' Cross Enrollee
                </td>
            </tr>
            <tr style="font-size: 8pt;">
                <td colspan="3" width="55%"><b>LRN No.:</b> ' . htmlspecialchars($app['lrn_number'] ?? '') . '</td>
                <td width="20%"><b>Solo Parent?</b><br>' . $chk(!empty($app['is_solo_parent'])) . ' Yes &nbsp; ' . $chk(empty($app['is_solo_parent'])) . ' No</td>
                <td width="25%"><b>Physical Disability:</b><br>' . htmlspecialchars($app['physical_disability'] ?? 'None') . '</td>
            </tr>
            <tr style="font-size: 8pt;">
                <td colspan="3" width="55%"><b>Citizenship:</b> ' . htmlspecialchars($app['citizenship'] ?? 'Filipino') . '</td>
                <td colspan="2" width="45%"><b>Ethnic Affiliation/Tribe:</b> ' . htmlspecialchars($app['ethnic_affiliation'] ?? '') . '</td>
            </tr>
            <tr style="font-size: 8pt;">
                <td width="15%"><b>Permanent<br>Address:</b></td>
                <td width="30%"><b>Street/Barangay:</b><br>' . htmlspecialchars($app['street_barangay'] ?? '') . '</td>
                <td width="20%"><b>Municipality:</b><br>' . htmlspecialchars($app['municipality'] ?? '') . '</td>
                <td width="20%"><b>Province:</b><br>' . htmlspecialchars($app['province'] ?? '') . '</td>
                <td width="15%"><b>Zip Code:</b><br>' . htmlspecialchars($app['zip_code'] ?? '') . '</td>
            </tr>
            <tr style="font-size: 8pt;">
                <td colspan="2" width="45%"><b>Contact Number:</b> ' . htmlspecialchars($app['contact_number'] ?? '') . '</td>
                <td colspan="2" width="35%"><b>E-mail Address:</b> ' . htmlspecialchars($app['email_address'] ?? '') . '</td>
                <td width="20%"><b>Staying at Dorm?</b><br>' . $chk(!empty($app['is_dormitory'])) . ' Yes &nbsp; ' . $chk(empty($app['is_dormitory'])) . ' No</td>
            </tr>
        </table>

        <div style="font-size: 1pt;">&nbsp;</div>

        <table width="100%" cellpadding="2" cellspacing="0" border="1">
            <tr style="background-color: #f2f2f2;">
                <td colspan="7" align="center" style="font-size: 8.5pt; font-weight: bold;">FAMILY BACKGROUND</td>
            </tr>
            <tr style="font-size: 7.5pt; font-weight: bold; background-color: #fafafa;" align="center">
                <td width="16%">Relationship</td>
                <td width="15%">Last Name</td>
                <td width="15%">First Name</td>
                <td width="14%">Middle Name</td>
                <td width="7%">Age</td>
                <td width="17%">Ethnic Affiliation/Tribe</td>
                <td width="16%">Occupation</td>
            </tr>
            ' . $fRow . '
            ' . $mRow . '
            ' . $sibRows . '
        </table>

        <div style="font-size: 2pt;">&nbsp;</div>

        <table width="100%" cellpadding="3" cellspacing="0" border="1" style="font-size: 8pt;">
            <tr>
                <td width="40%"><b>Household Per Capita Income:</b> PHP ' . (!empty($schApp['household_per_capita_income']) ? number_format($schApp['household_per_capita_income'], 2) : '___________') . '</td>
                <td width="60%"><b>DSWD Household Number (For 4Ps Member\'s Only):</b> ' . htmlspecialchars($schApp['dswd_household_number'] ?? '___________') . '</td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>Are you enjoying other education assistance?</b> &nbsp;&nbsp; ' . $chk($hasEA) . ' Yes &nbsp;&nbsp; ' . $chk(!$hasEA) . ' No<br>
                    <table width="100%" cellpadding="1" cellspacing="0" border="0" style="margin-top: 2px;">
                        <tr style="font-size: 7.5pt; font-weight: bold;">
                            <td width="30%">Type</td>
                            <td width="45%">Grantee Institution/Agency</td>
                            <td width="25%" align="right">Amount</td>
                        </tr>
                        <tr style="font-size: 7.5pt;">
                            <td>1. ' . htmlspecialchars($ea1['assistance_type'] ?? '____________________') . '</td>
                            <td>' . htmlspecialchars($ea1['institution_agency'] ?? '______________________________') . '</td>
                            <td align="right">' . (!empty($ea1['amount']) ? 'PHP ' . number_format($ea1['amount'], 2) : '_______________') . '</td>
                        </tr>
                        <tr style="font-size: 7.5pt;">
                            <td>2. ' . htmlspecialchars($ea2['assistance_type'] ?? '____________________') . '</td>
                            <td>' . htmlspecialchars($ea2['institution_agency'] ?? '______________________________') . '</td>
                            <td align="right">' . (!empty($ea2['amount']) ? 'PHP ' . number_format($ea2['amount'], 2) : '_______________') . '</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 7.5pt; font-style: italic;">
                    I hereby certify that foregoing statements are true and correct.<br>
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="55%"></td>
                            <td width="45%" align="center">
                                <br>
                                <u>&nbsp; ' . strtoupper(htmlspecialchars(($app['first_name'] ?? '') . ' ' . ($app['middle_name'] ? substr($app['middle_name'],0,1).'. ' : '') . ($app['last_name'] ?? ''))) . ' &nbsp;</u><br>
                                <b>Signature over Printed Name</b><br>
                                Date: ' . $certDate . '
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div style="font-size: 1pt;">&nbsp;</div>

        <p style="font-size: 7pt; font-style: italic; margin: 0; padding: 0;">
            The information is needed to select students who will qualify for the available scholarship/financial assistance. Rest assured that the information gathered will be treated with utmost respect and confidentiality.
        </p>

        <table width="100%" cellpadding="2" cellspacing="0" border="1" style="font-size: 8pt; margin-top: 2px;">
            <tr>
                <td colspan="2">
                    <b>Remarks:</b> ' . (!empty($evalRemarks) ? $evalRemarks : '') . '<br>
                    ' . (empty($evalRemarks) ? '<br><br>' : '') . '
                </td>
            </tr>
            <tr>
                <td width="50%" align="center">
                    <br><br>
                    <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br>
                    <b>Processed by:</b> Scholarship Staff
                </td>
                <td width="50%" align="center">
                    <br><br>
                    <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br>
                    <b>Evaluated by:</b> Scholarship Coordinator
                </td>
            </tr>
        </table>

        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 4px; font-size: 7pt; color: #444;">
            <tr>
                <td width="50%" align="left">
                    <b>QSU-SCH-F003</b><br>
                    Rev. 00 (Dec. 27, 2024)
                </td>
                <td width="50%" align="right">
                    Application No: <b>' . htmlspecialchars($app['application_number']) . '</b>
                </td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $filename = 'QSU_Scholarship_' . $app['application_number'] . '.pdf';
        while (ob_get_level()) { ob_end_clean(); }
        return $pdf->Output($filename, $dest);
    }

    /**
     * Generate QSU-SCH-F005: Application Form (Academic and Service Scholars)
     */
    public static function generateAcademicForm($app, $acaApp, $evaluation = null, $dest = 'I') {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(12, 10, 12);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        $logoPath = dirname(__DIR__) . '/public/assets/img/qsulogo.png';

        $chk = function($cond) {
            return $cond ? '<b>[ X ]</b>' : '[ &nbsp; ]';
        };

        $schType = $acaApp['scholarship_type'] ?? 'Academic';
        $srvCat = $acaApp['service_category'] ?? '';
        $srvOther = $acaApp['service_category_other'] ?? '';

        $isAcademic = ($schType === 'Academic');
        $isService = ($schType === 'Service');

        $isGov = ($srvCat === 'Student Government');
        $isPub = ($srvCat === 'Student Publication');
        $isDrum = ($srvCat === 'QSU Drum and Lyre Corps/ Rondalla');
        $isBand = ($srvCat === 'QSU Band');
        $isSocio = ($srvCat === 'Socio-Cultural Group');
        $isOther = ($srvCat === 'Others' || !empty($srvOther));

        $certDate = !empty($app['submitted_at']) ? date('M d, Y', strtotime($app['submitted_at'])) : date('M d, Y');
        $isQualified = (!empty($evaluation['decision']) && in_array($evaluation['decision'], ['Qualified', 'Approved']));
        $isDisqualified = (!empty($evaluation['decision']) && in_array($evaluation['decision'], ['Disqualified', 'Rejected']));
        $disqualReason = $evaluation['disqualification_reason'] ?? '';

        $html = '<table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="15%" align="left">';
        if (file_exists($logoPath)) {
            $html .= '<img src="' . $logoPath . '" width="56" height="56">';
        }
        $html .= '</td>
                <td width="85%" align="left" style="line-height: 1.3; padding-left: 5px;">
                    <span style="font-size: 13pt; font-weight: bold; letter-spacing: 0.5px;">QUIRINO STATE UNIVERSITY</span><br>
                    <span style="font-size: 8.5pt;">Office of the Scholarship and Financial Assistance</span>
                </td>
            </tr>
        </table>

        <div style="text-align: center; margin-top: 5px; margin-bottom: 8px;">
            <span style="font-size: 11.5pt; font-weight: bold;">APPLICATION FORM</span><br>
            <span style="font-size: 9.5pt; font-style: italic;">(Academic and Service Scholars)</span>
        </div>

        <!-- TYPE OF SCHOLARSHIP SECTION -->
        <table width="100%" cellpadding="3" cellspacing="0" border="1" style="font-size: 8pt;">
            <tr style="background-color: #f2f2f2;">
                <td colspan="4" style="font-weight: bold;">TYPE OF SCHOLARSHIP:</td>
            </tr>
            <tr>
                <td colspan="4">
                    ' . $chk($isAcademic) . ' <b>Academic Scholarship</b><br>
                    ' . $chk($isService) . ' <b>Service Scholarship:</b> &nbsp;
                    ' . $chk($isGov) . ' Student Government &nbsp;&nbsp;
                    ' . $chk($isPub) . ' Student Publication &nbsp;&nbsp;
                    ' . $chk($isDrum) . ' QSU Drum and Lyre Corps/ Rondalla<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    ' . $chk($isBand) . ' QSU Band &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    ' . $chk($isSocio) . ' Socio-Cultural Group &nbsp;
                    ' . $chk($isOther) . ' Others: ' . htmlspecialchars($srvOther ? $srvOther : ($isOther ? $srvCat : '____________________')) . '
                </td>
            </tr>
        </table>

        <div style="font-size: 2pt;">&nbsp;</div>

        <!-- STUDENT PERSONAL INFORMATION SECTION -->
        <table width="100%" cellpadding="3" cellspacing="0" border="1" style="font-size: 8pt;">
            <tr style="background-color: #f2f2f2;">
                <td colspan="4" align="center" style="font-weight: bold;">STUDENT\'S PERSONAL INFORMATION</td>
            </tr>
            <tr>
                <td width="28%"><b>Last Name:</b><br>' . htmlspecialchars($app['last_name'] ?? '') . '</td>
                <td width="28%"><b>First Name:</b><br>' . htmlspecialchars($app['first_name'] ?? '') . '</td>
                <td width="26%"><b>Middle Name:</b><br>' . htmlspecialchars($app['middle_name'] ?? '') . '</td>
                <td width="18%"><b>Extension Name:</b><br>' . htmlspecialchars($app['extension_name'] ?? '') . '</td>
            </tr>
            <tr>
                <td colspan="2" width="56%">
                    <b>Program:</b> ' . htmlspecialchars($app['degree_program'] ?? '') . '<br>
                    <b>Semester:</b> ' . $chk(($acaApp['semester'] ?? 'First') === 'First') . ' First &nbsp;&nbsp; ' . $chk(($acaApp['semester'] ?? '') === 'Second') . ' Second &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <b>S.Y.:</b> ' . htmlspecialchars($acaApp['academic_year'] ?? '2025-2026') . '<br>
                    <b>Year Level:</b> &nbsp;
                    ' . $chk(($app['year_level'] ?? 1) == 1) . ' 1st &nbsp;&nbsp;
                    ' . $chk(($app['year_level'] ?? 1) == 2) . ' 2nd &nbsp;&nbsp;
                    ' . $chk(($app['year_level'] ?? 1) == 3) . ' 3rd &nbsp;&nbsp;
                    ' . $chk(($app['year_level'] ?? 1) == 4) . ' 4th
                </td>
                <td width="20%">
                    <b>Sex:</b><br>
                    ' . $chk(($app['sex'] ?? '') === 'Male') . ' Male<br>
                    ' . $chk(($app['sex'] ?? '') === 'Female') . ' Female<br><br>
                    <b>Status:</b><br>
                    ' . $chk(($app['civil_status'] ?? '') === 'Single') . ' Single<br>
                    ' . $chk(($app['civil_status'] ?? '') === 'Married') . ' Married<br>
                    ' . $chk(($app['civil_status'] ?? '') === 'Separated') . ' Separated
                </td>
                <td width="24%">
                    <b>Are you a solo parent?</b><br>
                    ' . $chk(!empty($app['is_solo_parent'])) . ' Yes &nbsp;&nbsp; ' . $chk(empty($app['is_solo_parent'])) . ' No<br><br>
                    <b>Disability (Please specify):</b><br>
                    ' . htmlspecialchars($app['physical_disability'] ?? 'None') . '<br><br>
                    <b>Ethnic Affiliation/Tribe:</b><br>
                    ' . htmlspecialchars($app['ethnic_affiliation'] ?? '') . '
                </td>
            </tr>
            <tr>
                <td width="16%"><b>Permanent<br>Address:</b></td>
                <td width="28%"><b>Barangay:</b><br>' . htmlspecialchars($app['street_barangay'] ?? '') . '</td>
                <td width="28%"><b>Municipality:</b><br>' . htmlspecialchars($app['municipality'] ?? '') . '</td>
                <td width="28%"><b>Province:</b><br>' . htmlspecialchars($app['province'] ?? '') . '</td>
            </tr>
            <tr>
                <td colspan="2"><b>Contact Number:</b> ' . htmlspecialchars($app['contact_number'] ?? '') . '</td>
                <td colspan="2"><b>E-mail Address:</b> ' . htmlspecialchars($app['email_address'] ?? '') . '</td>
            </tr>
            <tr>
                <td colspan="4" style="font-size: 7.5pt; font-style: italic;">
                    I hereby certify that foregoing statements are true and correct.<br>
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="55%"></td>
                            <td width="45%" align="center">
                                <br>
                                <u>&nbsp; ' . strtoupper(htmlspecialchars(($app['first_name'] ?? '') . ' ' . ($app['middle_name'] ? substr($app['middle_name'],0,1).'. ' : '') . ($app['last_name'] ?? ''))) . ' &nbsp;</u><br>
                                <b>Name and Signature of Student</b><br>
                                Date: ' . $certDate . '
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div style="font-size: 2pt;">&nbsp;</div>

        <p style="font-size: 7pt; font-style: italic; margin: 0; padding: 0;">
            The information is needed to select students who will qualify for the available scholarship/financial assistance. Rest assured that the information gathered will be treated with utmost respect and confidentiality.
        </p>

        <!-- Official Processing & Evaluation -->
        <table width="100%" cellpadding="3" cellspacing="0" border="0" style="font-size: 8pt; margin-top: 4px;">
            <tr>
                <td colspan="3">
                    <b>Document Submitted:</b> &nbsp; ' . $chk(true) . ' <b>Certification of Grades or Grades from SIAS</b>
                </td>
            </tr>
            <tr>
                <td width="15%"><b>Remarks:</b></td>
                <td width="25%">' . $chk($isQualified) . ' <b>Qualified</b></td>
                <td width="60%">' . $chk($isDisqualified) . ' <b>Disqualified</b> &nbsp;&nbsp;&nbsp;&nbsp; <b>Reasons:</b> ' . htmlspecialchars($disqualReason) . '</td>
            </tr>
        </table>

        <div style="font-size: 6pt;">&nbsp;</div>

        <table width="100%" cellpadding="2" cellspacing="0" border="0" style="font-size: 8pt;">
            <tr>
                <td width="50%" align="center">
                    <br><br>
                    <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br>
                    <b>Processed by:</b><br>
                    Scholarship Staff
                </td>
                <td width="50%" align="center">
                    <br><br>
                    <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br>
                    <b>Evaluated by:</b><br>
                    Scholarship Coordinator
                </td>
            </tr>
        </table>

        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 8px; font-size: 7pt; color: #444;">
            <tr>
                <td width="50%" align="left">
                    <b>QSU-SCH-F005</b><br>
                    Rev. 00 (March 27, 2025)
                </td>
                <td width="50%" align="right">
                    Application No: <b>' . htmlspecialchars($app['application_number']) . '</b>
                </td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $filename = 'QSU_Academic_Scholar_' . $app['application_number'] . '.pdf';
        while (ob_get_level()) { ob_end_clean(); }
        return $pdf->Output($filename, $dest);
    }

    /**
     * Generate Student Assistant Form (Standard OSAS format)
     */
    public static function generateAssistantForm($app, $saApp, $evaluation = null, $dest = 'I') {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(12, 10, 12);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        $logoPath = dirname(__DIR__) . '/public/assets/img/qsulogo.png';
        $certDate = !empty($app['submitted_at']) ? date('M d, Y', strtotime($app['submitted_at'])) : date('M d, Y');
        $evalRemarks = !empty($evaluation['remarks']) ? htmlspecialchars($evaluation['remarks']) : '';

        $html = '<table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="15%" align="left">';
        if (file_exists($logoPath)) {
            $html .= '<img src="' . $logoPath . '" width="56" height="56">';
        }
        $html .= '</td>
                <td width="85%" align="left" style="line-height: 1.3; padding-left: 5px;">
                    <span style="font-size: 13pt; font-weight: bold; letter-spacing: 0.5px;">QUIRINO STATE UNIVERSITY</span><br>
                    <span style="font-size: 8.5pt;">Office of Student Affairs and Services (OSAS)</span>
                </td>
            </tr>
        </table>

        <div style="text-align: center; margin-top: 5px; margin-bottom: 8px;">
            <span style="font-size: 12pt; font-weight: bold;">STUDENT ASSISTANT APPLICATION FORM</span><br>
            <span style="font-size: 9pt; color: #555;">Application No: ' . htmlspecialchars($app['application_number']) . '</span>
        </div>

        <table width="100%" cellpadding="3" cellspacing="0" border="1" style="font-size: 8.5pt;">
            <tr style="background-color: #f2f2f2;">
                <td colspan="4" align="center" style="font-weight: bold;">STUDENT APPLICANT INFORMATION</td>
            </tr>
            <tr>
                <td width="25%"><b>Last Name:</b><br>' . htmlspecialchars($app['last_name'] ?? '') . '</td>
                <td width="25%"><b>First Name:</b><br>' . htmlspecialchars($app['first_name'] ?? '') . '</td>
                <td width="25%"><b>Middle Name:</b><br>' . htmlspecialchars($app['middle_name'] ?? '') . '</td>
                <td width="25%"><b>Extension:</b><br>' . htmlspecialchars($app['extension_name'] ?? 'N/A') . '</td>
            </tr>
            <tr>
                <td colspan="2"><b>Student ID No.:</b> ' . htmlspecialchars($app['student_number'] ?? '') . '</td>
                <td colspan="2"><b>Degree Program:</b> ' . htmlspecialchars($app['degree_program'] ?? '') . '</td>
            </tr>
            <tr>
                <td><b>Year Level:</b> ' . ($app['year_level'] ?? 1) . '</td>
                <td><b>Sex:</b> ' . htmlspecialchars($app['sex'] ?? 'N/A') . '</td>
                <td><b>Contact:</b> ' . htmlspecialchars($app['contact_number'] ?? '') . '</td>
                <td><b>Email:</b> ' . htmlspecialchars($app['email_address'] ?? '') . '</td>
            </tr>
            <tr style="background-color: #f2f2f2;">
                <td colspan="4" align="center" style="font-weight: bold;">POSITION & ASSIGNMENT PREFERENCE</td>
            </tr>
            <tr>
                <td colspan="2"><b>Preferred Opening:</b><br>' . htmlspecialchars($saApp['position_title'] ?? 'Open for Any Office') . '</td>
                <td colspan="2"><b>Preferred Office:</b><br>' . htmlspecialchars($saApp['preferred_office'] ?? 'Any Available') . '</td>
            </tr>
            <tr>
                <td colspan="4"><b>Preferred Schedule / Availability:</b><br>' . htmlspecialchars($saApp['preferred_schedule'] ?? 'Flexible') . '</td>
            </tr>
            <tr style="background-color: #f2f2f2;">
                <td colspan="4" align="center" style="font-weight: bold;">SKILLS & QUALIFICATIONS</td>
            </tr>
            <tr>
                <td colspan="4"><b>Key Skills:</b><br>' . nl2br(htmlspecialchars($saApp['skills'] ?? 'None specified')) . '</td>
            </tr>
            <tr>
                <td colspan="4"><b>Previous Work / Assistant Experience:</b><br>' . nl2br(htmlspecialchars($saApp['previous_experience'] ?? 'None specified')) . '</td>
            </tr>
            <tr>
                <td colspan="4"><b>Reason for Applying:</b><br>' . nl2br(htmlspecialchars($saApp['reason_for_applying'] ?? 'N/A')) . '</td>
            </tr>
            <tr>
                <td colspan="4" style="font-size: 7.5pt; font-style: italic;">
                    I hereby certify that foregoing statements are true and correct to the best of my knowledge.<br>
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="55%"></td>
                            <td width="45%" align="center">
                                <br>
                                <u>&nbsp; ' . strtoupper(htmlspecialchars(($app['first_name'] ?? '') . ' ' . ($app['middle_name'] ? substr($app['middle_name'],0,1).'. ' : '') . ($app['last_name'] ?? ''))) . ' &nbsp;</u><br>
                                <b>Signature of Applicant</b><br>
                                Date: ' . $certDate . '
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div style="font-size: 3pt;">&nbsp;</div>

        <table width="100%" cellpadding="3" cellspacing="0" border="1" style="font-size: 8.5pt;">
            <tr style="background-color: #f2f2f2;">
                <td colspan="2" style="font-weight: bold;">OFFICIAL EVALUATION & REMARKS</td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>Remarks / Screening Notes:</b><br>' . (!empty($evalRemarks) ? $evalRemarks : 'Pending formal interview and office assignment.') . '<br>
                </td>
            </tr>
            <tr>
                <td width="50%" align="center">
                    <br><br>
                    <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br>
                    <b>Screened by:</b> OSAS Staff
                </td>
                <td width="50%" align="center">
                    <br><br>
                    <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br>
                    <b>Approved by:</b> OSAS Director / Coordinator
                </td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $filename = 'QSU_Student_Assistant_' . $app['application_number'] . '.pdf';
        while (ob_get_level()) { ob_end_clean(); }
        return $pdf->Output($filename, $dest);
    }
}
