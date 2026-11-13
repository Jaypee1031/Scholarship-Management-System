<?php
ob_start();
$father = (new FamilyMember())->getFather($application['id']) ?: [];
$mother = (new FamilyMember())->getMother($application['id']) ?: [];
$siblings = (new FamilyMember())->getSiblings($application['id']) ?: [];
?>
<div class="container-fluid px-0" style="max-width: 960px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold text-qsu-green mb-0">SCHOLARSHIP & FINANCIAL ASSISTANCE</h3>
            <p class="text-muted small mb-0">Form: QSU-SCH-F003 Rev. 00 (Dec. 27, 2024)</p>
        </div>
        <div>
            <span class="badge bg-secondary">Application # <?= htmlspecialchars($application['application_number']) ?></span>
        </div>
    </div>

    <form action="<?= BASE_URL ?>/student/apply/scholarship" method="POST" enctype="multipart/form-data" id="scholarshipForm">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <!-- Step 1: Personal Information -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-success text-white py-2">
                <h6 class="fw-bold mb-0"><i class="fas fa-user me-2"></i>1. STUDENT PERSONAL INFORMATION</h6>
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
                        <input type="text" name="extension_name" class="form-control" placeholder="Jr" value="<?= htmlspecialchars($student['extension_name'] ?? '') ?>">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Student ID No. <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($student['student_id'] ?? '') ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Degree Program <span class="text-danger">*</span></label>
                        <input type="text" name="degree_program" class="form-control" value="<?= htmlspecialchars($student['degree_program'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Year Level <span class="text-danger">*</span></label>
                        <select name="year_level" class="form-select" required>
                            <?php for ($i=1; $i<=5; $i++): ?>
                            <option value="<?= $i ?>" <?= ($student['year_level'] ?? 1) == $i ? 'selected' : '' ?>><?= yearLevelLabel($i) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Registering As <span class="text-danger">*</span></label>
                        <select name="registering_as" class="form-select" required>
                            <?php
                            $regs = ['New Student', 'Old Student', 'Transfer In', 'Lateral Transfer', 'Cross Enrollee'];
                            foreach ($regs as $r):
                            ?>
                            <option value="<?= $r ?>" <?= ($student['registering_as'] ?? 'Old Student') === $r ? 'selected' : '' ?>><?= $r ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">LRN Number</label>
                        <input type="text" name="lrn_number" class="form-control" value="<?= htmlspecialchars($student['lrn_number'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Citizenship</label>
                        <input type="text" name="citizenship" class="form-control" value="<?= htmlspecialchars($student['citizenship'] ?? 'Filipino') ?>">
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
                        <label class="form-label small fw-semibold">Solo Parent?</label>
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
                        <label class="form-label small fw-semibold">Birthdate</label>
                        <input type="date" name="birthdate" class="form-control" value="<?= $student['birthdate'] ?? '' ?>">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Place of Birth</label>
                        <input type="text" name="place_of_birth" class="form-control" value="<?= htmlspecialchars($student['place_of_birth'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Physical Disability (if any)</label>
                        <input type="text" name="physical_disability" class="form-control" placeholder="None" value="<?= htmlspecialchars($student['physical_disability'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Ethnic Affiliation / Tribe</label>
                        <input type="text" name="ethnic_affiliation" class="form-control" value="<?= htmlspecialchars($student['ethnic_affiliation'] ?? '') ?>">
                    </div>
                </div>

                <h6 class="fw-bold text-secondary mt-4 border-top pt-3">Permanent Address & Contact</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Street / Barangay <span class="text-danger">*</span></label>
                        <input type="text" name="street_barangay" class="form-control" value="<?= htmlspecialchars($student['street_barangay'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Municipality <span class="text-danger">*</span></label>
                        <input type="text" name="municipality" class="form-control" value="<?= htmlspecialchars($student['municipality'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Province <span class="text-danger">*</span></label>
                        <input type="text" name="province" class="form-control" value="<?= htmlspecialchars($student['province'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">ZIP Code</label>
                        <input type="text" name="zip_code" class="form-control" value="<?= htmlspecialchars($student['zip_code'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Contact Number <span class="text-danger">*</span></label>
                        <input type="text" name="contact_number" class="form-control" value="<?= htmlspecialchars($student['contact_number'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email_address" class="form-control" value="<?= htmlspecialchars($student['email_address'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Staying at Dorm?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_dormitory" value="1" <?= !empty($student['is_dormitory']) ? 'checked' : '' ?>>
                                <label class="form-check-label small">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_dormitory" value="0" <?= empty($student['is_dormitory']) ? 'checked' : '' ?>>
                                <label class="form-check-label small">No</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Family Background -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-success text-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="fas fa-users me-2"></i>2. FAMILY BACKGROUND</h6>
                <button type="button" class="btn btn-light btn-sm fw-semibold" onclick="addSiblingRow()">
                    <i class="fas fa-plus me-1"></i> Add Sibling
                </button>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Rel</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Age</th>
                                <th>Ethnic/Tribe</th>
                                <th>Occupation</th>
                                <th>Status</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="siblingsTableBody">
                            <!-- Father Row -->
                            <tr>
                                <td class="fw-bold">Father</td>
                                <td><input type="text" name="father_last_name" class="form-control form-control-sm" value="<?= htmlspecialchars($father['last_name'] ?? '') ?>" required></td>
                                <td><input type="text" name="father_first_name" class="form-control form-control-sm" value="<?= htmlspecialchars($father['first_name'] ?? '') ?>" required></td>
                                <td><input type="text" name="father_middle_name" class="form-control form-control-sm" value="<?= htmlspecialchars($father['middle_name'] ?? '') ?>"></td>
                                <td><input type="number" name="father_age" class="form-control form-control-sm" style="max-width:70px;" value="<?= $father['age'] ?? '' ?>"></td>
                                <td><input type="text" name="father_ethnic" class="form-control form-control-sm" value="<?= htmlspecialchars($father['ethnic_affiliation'] ?? '') ?>"></td>
                                <td><input type="text" name="father_occupation" class="form-control form-control-sm" value="<?= htmlspecialchars($father['occupation'] ?? '') ?>"></td>
                                <td>
                                    <select name="father_status" class="form-select form-select-sm">
                                        <option value="Living" <?= ($father['member_status'] ?? 'Living') === 'Living' ? 'selected' : '' ?>>Living</option>
                                        <option value="Deceased" <?= ($father['member_status'] ?? '') === 'Deceased' ? 'selected' : '' ?>>Deceased</option>
                                    </select>
                                </td>
                                <td></td>
                            </tr>
                            <!-- Mother Row -->
                            <tr>
                                <td class="fw-bold">Mother</td>
                                <td><input type="text" name="mother_last_name" class="form-control form-control-sm" value="<?= htmlspecialchars($mother['last_name'] ?? '') ?>" required></td>
                                <td><input type="text" name="mother_first_name" class="form-control form-control-sm" value="<?= htmlspecialchars($mother['first_name'] ?? '') ?>" required></td>
                                <td><input type="text" name="mother_middle_name" class="form-control form-control-sm" value="<?= htmlspecialchars($mother['middle_name'] ?? '') ?>"></td>
                                <td><input type="number" name="mother_age" class="form-control form-control-sm" style="max-width:70px;" value="<?= $mother['age'] ?? '' ?>"></td>
                                <td><input type="text" name="mother_ethnic" class="form-control form-control-sm" value="<?= htmlspecialchars($mother['ethnic_affiliation'] ?? '') ?>"></td>
                                <td><input type="text" name="mother_occupation" class="form-control form-control-sm" value="<?= htmlspecialchars($mother['occupation'] ?? '') ?>"></td>
                                <td>
                                    <select name="mother_status" class="form-select form-select-sm">
                                        <option value="Living" <?= ($mother['member_status'] ?? 'Living') === 'Living' ? 'selected' : '' ?>>Living</option>
                                        <option value="Deceased" <?= ($mother['member_status'] ?? '') === 'Deceased' ? 'selected' : '' ?>>Deceased</option>
                                    </select>
                                </td>
                                <td></td>
                            </tr>
                            <!-- Siblings Rows -->
                            <?php foreach ($siblings as $sib): ?>
                            <tr>
                                <td>Sibling</td>
                                <td><input type="text" name="sibling_last_name[]" class="form-control form-control-sm" value="<?= htmlspecialchars($sib['last_name']) ?>" required></td>
                                <td><input type="text" name="sibling_first_name[]" class="form-control form-control-sm" value="<?= htmlspecialchars($sib['first_name']) ?>" required></td>
                                <td><input type="text" name="sibling_middle_name[]" class="form-control form-control-sm" value="<?= htmlspecialchars($sib['middle_name'] ?? '') ?>"></td>
                                <td><input type="number" name="sibling_age[]" class="form-control form-control-sm" style="max-width:70px;" value="<?= $sib['age'] ?? '' ?>"></td>
                                <td><input type="text" name="sibling_ethnic[]" class="form-control form-control-sm" value="<?= htmlspecialchars($sib['ethnic_affiliation'] ?? '') ?>"></td>
                                <td><input type="text" name="sibling_occupation[]" class="form-control form-control-sm" value="<?= htmlspecialchars($sib['occupation'] ?? '') ?>"></td>
                                <td>Living</td>
                                <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Household Per Capita Income (PHP)</label>
                        <input type="number" step="0.01" name="household_income" class="form-control" placeholder="e.g. 3000.00" value="<?= $schApp['household_per_capita_income'] ?? '' ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">DSWD Household Number <span class="text-muted">(For 4Ps Members Only)</span></label>
                        <input type="text" name="dswd_number" class="form-control" placeholder="e.g. 02-57-01-..." value="<?= htmlspecialchars($schApp['dswd_household_number'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Other Educational Assistance -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-success text-white py-2">
                <h6 class="fw-bold mb-0"><i class="fas fa-hand-holding-usd me-2"></i>3. OTHER EDUCATIONAL ASSISTANCE</h6>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-semibold d-block">Are you enjoying other educational assistance?</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="has_other_assistance" value="1" id="otherAssistanceYes" onchange="toggleAssistanceSection(true)" <?= !empty($schApp['has_other_assistance']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="otherAssistanceYes">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="has_other_assistance" value="0" id="otherAssistanceNo" onchange="toggleAssistanceSection(false)" <?= empty($schApp['has_other_assistance']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="otherAssistanceNo">No</label>
                    </div>
                </div>

                <div id="assistanceTableSection" style="display: <?= !empty($schApp['has_other_assistance']) ? 'block' : 'none' ?>;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted">Please specify current educational grants:</span>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="addAssistanceRow()">
                            <i class="fas fa-plus me-1"></i> Add Record
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Grantee</th>
                                    <th>Institution / Agency</th>
                                    <th>Amount (PHP)</th>
                                    <th style="width:40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="assistanceTableBody">
                                <?php if (!empty($eduAssistance)): ?>
                                    <?php foreach ($eduAssistance as $ea): ?>
                                    <tr>
                                        <td><input type="text" name="ea_type[]" class="form-control form-control-sm" value="<?= htmlspecialchars($ea['assistance_type']) ?>" required></td>
                                        <td><input type="text" name="ea_grantee[]" class="form-control form-control-sm" value="<?= htmlspecialchars($ea['grantee']) ?>"></td>
                                        <td><input type="text" name="ea_institution[]" class="form-control form-control-sm" value="<?= htmlspecialchars($ea['institution_agency']) ?>" required></td>
                                        <td><input type="number" step="0.01" name="ea_amount[]" class="form-control form-control-sm" value="<?= $ea['amount'] ?>"></td>
                                        <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4: Required Document Upload -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-success text-white py-2">
                <h6 class="fw-bold mb-0"><i class="fas fa-file-upload me-2"></i>4. REQUIRED DOCUMENTS</h6>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">Upload clear scanned copies or photos (JPG, PNG, PDF up to 5MB):</p>
                <div class="row g-3">
                    <?php foreach ($requirements as $req): ?>
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-light">
                            <label class="form-label fw-bold small mb-1">
                                <?= htmlspecialchars($req['name']) ?>
                                <?php if ($req['is_required']): ?><span class="text-danger">*</span><?php endif; ?>
                            </label>
                            <div class="small text-muted mb-2"><?= htmlspecialchars($req['description']) ?></div>
                            <input type="file" name="doc_<?= $req['id'] ?>" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Step 5: Certification & Submit -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4 text-center">
                <p class="text-muted fst-italic mb-3">
                    "I hereby certify that the foregoing statements are true and correct. The information is needed to select students who will qualify for the available scholarship/financial assistance. Rest assured that the information gathered will be treated with utmost respect and confidentiality."
                </p>
                <div class="form-check d-inline-block text-start mb-4">
                    <input class="form-check-input" type="checkbox" name="certification_confirmed" value="1" id="certConfirm" required <?= !empty($schApp['certification_confirmed']) ? 'checked' : '' ?>>
                    <label class="form-check-label fw-bold" for="certConfirm">
                        I confirm and certify that all details provided are accurate.
                    </label>
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <button type="submit" name="submit_action" value="draft" class="btn btn-outline-secondary px-4 py-2">
                        <i class="fas fa-save me-1"></i> Save as Draft
                    </button>
                    <button type="submit" name="submit_action" value="submit" class="btn btn-success px-5 py-2 fw-bold">
                        <i class="fas fa-paper-plane me-2"></i> Submit Application
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="<?= BASE_URL ?>/public/assets/js/forms.js"></script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/student_layout.php'; ?>
