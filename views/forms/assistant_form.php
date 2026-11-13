<?php ob_start(); ?>
<div class="container-fluid px-0" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold text-qsu-green mb-0">STUDENT ASSISTANT APPLICATION</h3>
            <p class="text-muted small mb-0">Office of Student Affairs and Services (OSAS)</p>
        </div>
        <div>
            <span class="badge bg-secondary">Application # <?= htmlspecialchars($application['application_number']) ?></span>
        </div>
    </div>

    <form action="<?= BASE_URL ?>/student/apply/assistant" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <!-- Personal Info -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-success text-white py-2">
                <h6 class="fw-bold mb-0"><i class="fas fa-user me-2"></i>STUDENT INFORMATION</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($student['last_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($student['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" value="<?= htmlspecialchars($student['middle_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold">Degree Program</label>
                        <input type="text" name="degree_program" class="form-control" value="<?= htmlspecialchars($student['degree_program'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Year Level</label>
                        <select name="year_level" class="form-select">
                            <?php for ($i=1; $i<=5; $i++): ?>
                            <option value="<?= $i ?>" <?= ($student['year_level'] ?? 1) == $i ? 'selected' : '' ?>><?= yearLevelLabel($i) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Contact Mobile</label>
                        <input type="text" name="contact_number" class="form-control" value="<?= htmlspecialchars($student['contact_number'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email Address</label>
                        <input type="email" name="email_address" class="form-control" value="<?= htmlspecialchars($student['email_address'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Position Preference -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-success text-white py-2">
                <h6 class="fw-bold mb-0"><i class="fas fa-briefcase me-2"></i>POSITION & OFFICE PREFERENCE</h6>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Available Positions Under OSAS</label>
                    <select name="preferred_position_id" class="form-select">
                        <option value="">-- Select Preferred Opening --</option>
                        <?php foreach ($saPositions as $pos): ?>
                        <option value="<?= $pos['id'] ?>" <?= ($saApp['preferred_position_id'] ?? '') == $pos['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pos['title']) ?> &bull; <?= htmlspecialchars($pos['office']) ?> (<?= $pos['available_slots'] ?> slots)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Alternative Office Preference</label>
                        <input type="text" name="preferred_office" class="form-control" placeholder="e.g. Library, IT Dept, OSAS" value="<?= htmlspecialchars($saApp['preferred_office'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Preferred Schedule / Shift</label>
                        <input type="text" name="preferred_schedule" class="form-control" placeholder="e.g. Morning 8am-12pm, MWF" value="<?= htmlspecialchars($saApp['preferred_schedule'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Qualifications -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-success text-white py-2">
                <h6 class="fw-bold mb-0"><i class="fas fa-tools me-2"></i>SKILLS & QUALIFICATIONS</h6>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Key Skills (Computer, clerical, customer service, etc.)</label>
                    <textarea name="skills" class="form-control" rows="2" placeholder="List your technical, clerical, or interpersonal skills..."><?= htmlspecialchars($saApp['skills'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Previous Student Assistant Experience</label>
                    <textarea name="previous_experience" class="form-control" rows="2" placeholder="Detail any past assistant work or volunteer tasks..."><?= htmlspecialchars($saApp['previous_experience'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Reason for Applying</label>
                    <textarea name="reason_for_applying" class="form-control" rows="2" placeholder="Why do you wish to become a QSU Student Assistant?"><?= htmlspecialchars($saApp['reason_for_applying'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Documents -->
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

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4 text-center">
                <div class="d-flex justify-content-center gap-3">
                    <button type="submit" name="submit_action" value="draft" class="btn btn-outline-secondary px-4 py-2">Save as Draft</button>
                    <button type="submit" name="submit_action" value="submit" class="btn btn-success px-5 py-2 fw-bold">Submit Application</button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/student_layout.php'; ?>
