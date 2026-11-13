<?php ob_start(); ?>
<div class="container-fluid px-0" style="max-width: 800px;">
    <h3 class="fw-bold text-qsu-green mb-1">MY PROFILE</h3>
    <p class="text-muted mb-4">Manage your contact information and view your registered profile.</p>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="<?= BASE_URL ?>/student/profile" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <h6 class="fw-bold text-qsu-green border-bottom pb-2 mb-3">Academic Identity (Read Only)</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Student ID</label>
                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($student['student_id'] ?? '') ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">First Name</label>
                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($student['first_name'] ?? '') ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Last Name</label>
                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($student['last_name'] ?? '') ?>" readonly>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small text-muted">Degree Program</label>
                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($student['degree_program'] ?? '') ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Year Level</label>
                        <input type="text" class="form-control bg-light" value="<?= yearLevelLabel($student['year_level'] ?? 1) ?>" readonly>
                    </div>
                </div>

                <h6 class="fw-bold text-qsu-green border-bottom pb-2 mb-3">Contact Details</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Contact Mobile Number</label>
                        <input type="text" name="contact_number" class="form-control" value="<?= htmlspecialchars($student['contact_number'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email Address</label>
                        <input type="email" name="email_address" class="form-control" value="<?= htmlspecialchars($student['email_address'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Street / Barangay</label>
                        <input type="text" name="street_barangay" class="form-control" value="<?= htmlspecialchars($student['street_barangay'] ?? '') ?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold">Municipality</label>
                        <input type="text" name="municipality" class="form-control" value="<?= htmlspecialchars($student['municipality'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Province</label>
                        <input type="text" name="province" class="form-control" value="<?= htmlspecialchars($student['province'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">ZIP Code</label>
                        <input type="text" name="zip_code" class="form-control" value="<?= htmlspecialchars($student['zip_code'] ?? '') ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-success px-4 py-2 fw-semibold">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </form>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/student_layout.php'; ?>
