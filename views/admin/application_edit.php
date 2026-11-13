<?php ob_start(); ?>
<div class="container-fluid px-0" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-0 text-navy">Admin Edit Application</h3>
            <p class="text-muted small mb-0">App # <?= htmlspecialchars($application['application_number']) ?> &bull; Student ID: <?= htmlspecialchars($application['student_number']) ?></p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/admin/applications/view?id=<?= $application['id'] ?>" class="btn btn-outline-secondary btn-sm">
                Cancel
            </a>
        </div>
    </div>

    <div class="alert alert-info py-2 small">
        <i class="fas fa-info-circle me-1"></i> All modifications made here are recorded in the audit trail with before/after values and administrator identity.
    </div>

    <form action="<?= BASE_URL ?>/admin/applications/edit" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="hidden" name="application_id" value="<?= $application['id'] ?>">

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-user-edit text-success me-2"></i>Student Details</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Last Name</label>
                        <input type="text" name="last_name" class="form-control form-control-sm" value="<?= htmlspecialchars($application['last_name']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">First Name</label>
                        <input type="text" name="first_name" class="form-control form-control-sm" value="<?= htmlspecialchars($application['first_name']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control form-control-sm" value="<?= htmlspecialchars($application['middle_name'] ?? '') ?>">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold">Degree Program</label>
                        <input type="text" name="degree_program" class="form-control form-control-sm" value="<?= htmlspecialchars($application['degree_program']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Year Level</label>
                        <select name="year_level" class="form-select form-select-sm">
                            <?php for ($y=1; $y<=5; $y++): ?>
                            <option value="<?= $y ?>" <?= $application['year_level'] == $y ? 'selected' : '' ?>><?= yearLevelLabel($y) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control form-control-sm" value="<?= htmlspecialchars($application['contact_number'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email Address</label>
                        <input type="email" name="email_address" class="form-control form-control-sm" value="<?= htmlspecialchars($application['email_address'] ?? '') ?>">
                    </div>
                </div>

                <?php if (hasPermission('override_applications')): ?>
                <hr>
                <h6 class="fw-bold text-danger"><i class="fas fa-shield-alt me-1"></i>Super Admin Override</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Force Status Override</label>
                        <select name="override_status" class="form-select form-select-sm">
                            <option value="">-- Keep Current Status (<?= htmlspecialchars($application['status']) ?>) --</option>
                            <?php
                            $statuses = ['Draft', 'Submitted', 'Under Review', 'For Revision', 'Qualified', 'Disqualified', 'Approved', 'Rejected'];
                            foreach ($statuses as $st):
                            ?>
                            <option value="<?= $st ?>"><?= $st ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Override Reason (Required if changing)</label>
                        <input type="text" name="override_reason" class="form-control form-control-sm" placeholder="e.g., Authorized by Dean">
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-light text-end py-3">
                <button type="submit" class="btn btn-success px-4 fw-semibold">Save Corrections</button>
            </div>
        </div>
    </form>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin_layout.php'; ?>
