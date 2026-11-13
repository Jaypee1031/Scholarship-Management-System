<?php ob_start(); ?>
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-navy">OSAS Administrative Dashboard</h3>
            <p class="text-muted small mb-0">Quirino State University Scholarship Management</p>
        </div>
        <div>
            <span class="badge bg-success px-3 py-2">
                <i class="fas fa-calendar-alt me-1"></i> <?= htmlspecialchars($activePeriod['name'] ?? 'No active period') ?>
            </span>
        </div>
    </div>

    <!-- Metric Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card shadow-sm admin-card-metric metric-students p-3">
                <small class="text-muted fw-bold text-uppercase">Total Students</small>
                <h3 class="fw-bold text-dark my-1"><?= (int)$stats['total_students'] ?></h3>
                <small class="text-primary"><i class="fas fa-users me-1"></i> Registered</small>
            </div>
        </div>
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card shadow-sm admin-card-metric metric-applications p-3">
                <small class="text-muted fw-bold text-uppercase">Applications</small>
                <h3 class="fw-bold text-dark my-1"><?= (int)$stats['total_applications'] ?></h3>
                <small class="text-success"><i class="fas fa-file-signature me-1"></i> All Time</small>
            </div>
        </div>
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card shadow-sm admin-card-metric metric-pending p-3">
                <small class="text-muted fw-bold text-uppercase">Pending / Review</small>
                <h3 class="fw-bold text-dark my-1"><?= (int)$stats['pending'] ?></h3>
                <small class="text-warning text-dark"><i class="fas fa-clock me-1"></i> Needs Action</small>
            </div>
        </div>
        <div class="col-xl col-md-6 col-sm-6">
            <div class="card shadow-sm admin-card-metric metric-sa p-3">
                <small class="text-muted fw-bold text-uppercase">Student Assistant</small>
                <h3 class="fw-bold text-dark my-1"><?= (int)$stats['sa_count'] ?></h3>
                <small class="text-info"><i class="fas fa-user-tie me-1"></i> Applicants</small>
            </div>
        </div>
        <div class="col-xl col-md-6 col-sm-6">
            <div class="card shadow-sm admin-card-metric metric-aca p-3">
                <small class="text-muted fw-bold text-uppercase">Academic Scholar</small>
                <h3 class="fw-bold text-dark my-1"><?= (int)$stats['aca_count'] ?></h3>
                <small class="text-purple"><i class="fas fa-graduation-cap me-1"></i> Applicants</small>
            </div>
        </div>
    </div>

    <!-- Application Type Controls Quick Panel -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-toggle-on text-success me-2"></i>Application Type Availability Control</h5>
            <a href="<?= BASE_URL ?>/admin/application-types" class="btn btn-sm btn-outline-secondary">Manage Configuration</a>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <?php foreach ($applicationTypes as $type): ?>
                <?php if ($type['slug'] === 'scholarship') continue; ?>
                <div class="col-md-6">
                    <div class="p-3 border rounded d-flex justify-content-between align-items-center bg-light">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($type['name']) ?></h6>
                            <small class="text-muted"><?= $type['app_count'] ?> applications submitted</small>
                        </div>
                        <div>
                            <?php if ($type['is_enabled']): ?>
                            <span class="badge bg-success px-3 py-2">ENABLED</span>
                            <?php else: ?>
                            <span class="badge bg-danger px-3 py-2">DISABLED</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Recent Applications Table -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-history text-primary me-2"></i>Recent Applications</h5>
            <a href="<?= BASE_URL ?>/admin/applications" class="btn btn-sm btn-outline-success">View All Applications</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Application #</th>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Application Type</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentApps)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No applications found.</td></tr>
                        <?php else: ?>
                        <?php foreach ($recentApps as $app): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-success"><?= htmlspecialchars($app['application_number']) ?></td>
                            <td><?= htmlspecialchars($app['student_number']) ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($app['last_name'] . ', ' . $app['first_name']) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($app['type_name']) ?></span></td>
                            <td><span class="badge <?= getStatusBadgeClass($app['status']) ?>"><?= htmlspecialchars($app['status']) ?></span></td>
                            <td class="small text-muted"><?= formatDate($app['submitted_at'] ?? $app['created_at']) ?></td>
                            <td class="text-end pe-3">
                                <a href="<?= BASE_URL ?>/admin/applications/view?id=<?= $app['id'] ?>" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin_layout.php'; ?>
