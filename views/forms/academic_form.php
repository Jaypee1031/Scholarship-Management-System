<?php ob_start(); ?>
<div class="container-fluid px-0" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold text-qsu-green mb-0">APPLICATION FORM (ACADEMIC AND SERVICE SCHOLARS)</h3>
            <p class="text-muted small mb-0">Document No.: <strong>QSU-SCH-F005</strong> | Rev. 00 (March 27, 2025)</p>
        </div>
        <div>
            <span class="badge bg-secondary">Application # <?= htmlspecialchars($application['application_number']) ?></span>
        </div>
    </div>

    <form action="<?= BASE_URL ?>/student/apply/academic" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <!-- Step 1: Type of Scholarship -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-success text-white py-2">
                <h6 class="fw-bold mb-0"><i class="fas fa-award me-2"></i>TYPE OF SCHOLARSHIP</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-check p-3 border rounded">
                            <input class="form-check-input ms-0 me-2" type="radio" name="scholarship_type" value="Academic" id="typeAcademic" <?= ($acaApp['scholarship_type'] ?? 'Academic') === 'Academic' ? 'checked' : '' ?> onchange="toggleServiceGroup(false)">
                            <label class="form-check-label fw-bold" for="typeAcademic">
                                Academic Scholarship
                            </label>
                            <div class="small text-muted mt-1">For outstanding academic performance and honor students.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check p-3 border rounded">
                            <input class="form-check-input ms-0 me-2" type="radio" name="scholarship_type" value="Service" id="typeService" <?= ($acaApp['scholarship_type'] ?? '') === 'Service' ? 'checked' : '' ?> onchange="toggleServiceGroup(true)">
                            <label class="form-check-label fw-bold" for="typeService">
                                Service Scholarship
                            </label>
                            <div class="small text-muted mt-1">For active student organization leaders, cultural artists, band, and staff.</div>
                        </div>
                    </div>
                </div>

                <div id="serviceCategoryBox" class="mt-3 p-3 bg-light rounded" style="display: <?= ($acaApp['scholarship_type'] ?? '') === 'Service' ? 'block' : 'none' ?>;">
                    <label class="form-label small fw-bold">Select Service Category:</label>
                    <div class="row g-2">
                        <?php
                        $cats = ['Student Government', 'Student Publication', 'QSU Drum and Lyre Corps/ Rondalla', 'QSU Band', 'Socio-Cultural Group', 'Others'];
                        foreach ($cats as $cat):
                        ?>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="service_category" value="<?= $cat ?>" id="cat_<?= md5($cat) ?>" <?= ($acaApp['service_category'] ?? '') === $cat ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="cat_<?= md5($cat) ?>"><?= $cat ?></label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-2">
                        <input type="text" name="service_category_other" class="form-control form-control-sm" placeholder="If 'Others', please specify here" value="<?= htmlspecialchars($acaApp['service_category_other'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Personal Info -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-success text-white py-2">
                <h6 class="fw-bold mb-0"><i class="fas fa-user me-2"></i>STUDENT'S PERSONAL INFORMATION</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($student['last_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($student['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" value="<?= htmlspecialchars($student['middle_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small fw-semibold">Ext.</label>
                        <input type="text" name="extension_name" class="form-control" value="<?= htmlspecialchars($student['extension_name'] ?? '') ?>">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Degree Program <span class="text-danger">*</span></label>
                        <input type="text" name="degree_program" class="form-control" value="<?= htmlspecialchars($student['degree_program'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Semester</label>
                        <select name="semester" class="form-select">
                            <option value="First" <?= ($acaApp['semester'] ?? 'First') === 'First' ? 'selected' : '' ?>>First</option>
                            <option value="Second" <?= ($acaApp['semester'] ?? '') === 'Second' ? 'selected' : '' ?>>Second</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Academic Year</label>
                        <input type="text" name="academic_year" class="form-control" value="<?= htmlspecialchars($acaApp['academic_year'] ?? '2025-2026') ?>">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-12">
                        <label class="form-label small fw-semibold">Year Level</label>
                        <div>
                            <?php for ($i=1; $i<=5; $i++): ?>
                            <div class="form-check form-check-inline me-3">
                                <input class="form-check-input" type="radio" name="year_level" value="<?= $i ?>" id="yr_<?= $i ?>" <?= ($student['year_level'] ?? 1) == $i ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="yr_<?= $i ?>"><?= yearLevelLabel($i) ?></label>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Sex</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="sex" value="Male" <?= ($student['sex'] ?? '') === 'Male' ? 'checked' : '' ?>>
                                <label class="form-check-label small">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="sex" value="Female" <?= ($student['sex'] ?? '') === 'Female' ? 'checked' : '' ?>>
                                <label class="form-check-label small">Female</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Civil Status</label>
                        <select name="civil_status" class="form-select">
                            <option value="Single" <?= ($student['civil_status'] ?? '') === 'Single' ? 'selected' : '' ?>>Single</option>
                            <option value="Married" <?= ($student['civil_status'] ?? '') === 'Married' ? 'selected' : '' ?>>Married</option>
                            <option value="Separated" <?= ($student['civil_status'] ?? '') === 'Separated' ? 'selected' : '' ?>>Separated</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Are you a solo parent?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_solo_parent" value="1" <?= !empty($student['is_solo_parent']) ? 'checked' : '' ?>>
                                <label class="form-check-label small">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_solo_parent" value="0" <?= empty($student['is_solo_parent']) ? 'checked' : '' ?>>
                                <label class="form-check-label small">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Disability (Please specify)</label>
                        <input type="text" name="physical_disability" class="form-control" placeholder="None" value="<?= htmlspecialchars($student['physical_disability'] ?? '') ?>">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Ethnic Affiliation / Tribe</label>
                        <input type="text" name="ethnic_affiliation" class="form-control" value="<?= htmlspecialchars($student['ethnic_affiliation'] ?? '') ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold">Barangay (Permanent Address)</label>
                        <input type="text" name="street_barangay" class="form-control" value="<?= htmlspecialchars($student['street_barangay'] ?? '') ?>">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Municipality</label>
                        <input type="text" name="municipality" class="form-control" value="<?= htmlspecialchars($student['municipality'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Province</label>
                        <input type="text" name="province" class="form-control" value="<?= htmlspecialchars($student['province'] ?? '') ?>">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" value="<?= htmlspecialchars($student['contact_number'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email Address</label>
                        <input type="email" name="email_address" class="form-control" value="<?= htmlspecialchars($student['email_address'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Documents -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-success text-white py-2">
                <h6 class="fw-bold mb-0"><i class="fas fa-file-upload me-2"></i>SUPPORTING DOCUMENTS</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <?php foreach ($requirements as $req): ?>
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-light">
                            <label class="form-label fw-bold small mb-1"><?= htmlspecialchars($req['name']) ?></label>
                            <div class="small text-muted mb-2"><?= htmlspecialchars($req['description']) ?></div>
                            <input type="file" name="doc_<?= $req['id'] ?>" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Step 4: Certification -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4 text-center">
                <p class="text-muted fst-italic mb-3">
                    "I hereby certify that the foregoing statements are true and correct. The information is needed to select students who will qualify for the available scholarship/financial assistance. Rest assured that the information gathered will be treated with utmost respect and confidentiality."
                </p>
                <div class="form-check d-inline-block text-start mb-4">
                    <input class="form-check-input" type="checkbox" name="certification_confirmed" value="1" id="certConfirm" required <?= !empty($acaApp['certification_confirmed']) ? 'checked' : '' ?>>
                    <label class="form-check-label fw-bold" for="certConfirm">
                        I hereby certify the statements above are true and correct.
                    </label>
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <button type="submit" name="submit_action" value="draft" class="btn btn-outline-secondary px-4 py-2">Save as Draft</button>
                    <button type="submit" name="submit_action" value="submit" class="btn btn-success px-5 py-2 fw-bold">Submit Application</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function toggleServiceGroup(show) {
    document.getElementById('serviceCategoryBox').style.display = show ? 'block' : 'none';
}
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/student_layout.php'; ?>
