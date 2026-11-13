<?php ob_start(); ?>
<div class="container-fluid px-0">
    <!-- Welcome Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1 text-primary">Welcome, <?= htmlspecialchars($student['first_name'] ?? 'Student') ?>!</h3>
            <p class="text-muted mb-0">Quirino State University &bull; Office of Student Affairs and Services (OSAS)</p>
        </div>
        <div>
            <?php if ($activePeriod): ?>
            <span class="badge bg-success py-2 px-3"><i class="fas fa-calendar-check me-1"></i> Active Period: <?= htmlspecialchars($activePeriod['name']) ?></span>
            <?php else: ?>
            <span class="badge bg-secondary py-2 px-3"><i class="fas fa-calendar-times me-1"></i> No Active Period</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Student Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm p-3 border-start border-4 border-success">
                <small class="text-muted text-uppercase fw-bold">Student ID</small>
                <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($student['student_id'] ?? 'N/A') ?></h5>
                <small class="text-secondary"><?= htmlspecialchars($student['registering_as'] ?? 'Student') ?></small>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card shadow-sm p-3 border-start border-4 border-primary">
                <small class="text-muted text-uppercase fw-bold">Degree Program</small>
                <h6 class="fw-bold mb-0 text-dark text-truncate"><?= htmlspecialchars($student['degree_program'] ?? 'Not set') ?></h6>
                <small class="text-secondary"><?= yearLevelLabel($student['year_level'] ?? 1) ?></small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card shadow-sm p-3 border-start border-4 border-warning">
                <small class="text-muted text-uppercase fw-bold">Application Status</small>
                <?php if ($application): ?>
                <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($application['application_number']) ?></h6>
                <span class="badge <?= getStatusBadgeClass($application['status']) ?> mt-1"><?= htmlspecialchars($application['status']) ?></span>
                <?php else: ?>
                <h6 class="fw-bold mb-0 text-muted">None Selected</h6>
                <small class="text-secondary">Ready to apply</small>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card shadow-sm p-3 border-start border-4 border-info">
                <small class="text-muted text-uppercase fw-bold">Notifications</small>
                <h5 class="fw-bold mb-0 text-dark"><?= (int)$unreadCount ?> Unread</h5>
                <a href="<?= BASE_URL ?>/student/notifications" class="small text-decoration-none text-primary">View Inbox &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Selection Section: Select what you want to fill up -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="fw-bold mb-0"><i class="fas fa-file-pen me-2"></i>Select What You Want To Fill Up</h5>
        </div>
        <div class="card-body p-4">
            <p class="text-muted mb-4">Choose the specific application form you wish to complete:</p>
            <div class="row g-4 justify-content-center">
                <!-- Option 1: Student Assistant -->
                <div class="col-md-6 col-lg-5">
                    <div class="card h-100 border p-4 text-center app-type-card bg-light shadow-sm">
                        <div class="rounded-circle bg-white shadow-sm d-inline-flex p-3 text-primary mx-auto my-2">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                        <h4 class="fw-bold text-dark mt-2">Student Assistant</h4>
                        <p class="text-muted flex-grow-1">Apply for on-campus student assistant jobs, office duties, and library shifts.</p>
                        <a href="<?= BASE_URL ?>/student/apply/assistant" class="btn btn-primary w-100 fw-semibold py-2 fs-6">
                            <i class="fas fa-edit me-1"></i> Fill Up Student Assistant
                        </a>
                    </div>
                </div>

                <!-- Option 2: Academic Achiever -->
                <div class="col-md-6 col-lg-5">
                    <div class="card h-100 border p-4 text-center app-type-card bg-light shadow-sm">
                        <div class="rounded-circle bg-white shadow-sm d-inline-flex p-3 text-primary mx-auto my-2">
                            <i class="fas fa-graduation-cap fa-2x"></i>
                        </div>
                        <h4 class="fw-bold text-dark mt-2">Academic Achiever</h4>
                        <p class="text-muted flex-grow-1">For honor students, Dean's listers, and student leaders / socio-cultural artists.</p>
                        <a href="<?= BASE_URL ?>/student/apply/academic" class="btn btn-primary w-100 fw-semibold py-2 fs-6">
                            <i class="fas fa-edit me-1"></i> Fill Up Academic Achiever
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- If application exists, show tracking & actions -->
    <?php if ($application): ?>
    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-clipboard-check text-primary me-2"></i>Current Selected Application: <?= htmlspecialchars($application['type_name'] ?? 'Application') ?></h5>
            <span class="badge <?= getStatusBadgeClass($application['status']) ?> px-3 py-2 fs-6"><?= htmlspecialchars($application['status']) ?></span>
        </div>
        <div class="card-body">
            <div class="p-3 bg-light rounded d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <strong>Application Number:</strong> <code><?= htmlspecialchars($application['application_number']) ?></code>
                    <div class="small text-muted mt-1">Submitted / Updated: <?= formatDate($application['submitted_at'] ?? $application['updated_at'] ?? $application['created_at']) ?></div>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= BASE_URL ?>/student/application" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye me-1"></i> View Details
                    </a>
                    <?php if ($application['status'] === 'Draft' || $application['status'] === 'For Revision'): ?>
                        <?php
                        $editRoutes = ['scholarship' => '/student/apply/scholarship', 'academic_achiever' => '/student/apply/academic', 'student_assistant' => '/student/apply/assistant'];
                        $route = $editRoutes[$application['type_slug'] ?? ''] ?? '/student/dashboard';
                        ?>
                        <a href="<?= BASE_URL . $route ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Continue Editing
                        </a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/document/pdf?id=<?= $application['id'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-print me-1"></i> Printable PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/student_layout.php'; ?>
