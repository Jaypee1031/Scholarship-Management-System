<?php ob_start(); ?>
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-0 text-navy">Application Management</h3>
            <p class="text-muted small mb-0">Filter, search, evaluate, and manage student applications.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/admin/reports/export?format=csv" class="btn btn-outline-success btn-sm">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </a>
            <a href="<?= BASE_URL ?>/admin/reports" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-chart-pie me-1"></i> Reports & Summary
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <form action="<?= BASE_URL ?>/admin/applications" method="GET" class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search ID, Name, App #" value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select name="type_slug" class="form-select form-select-sm">
                        <option value="">-- All Types --</option>
                        <?php foreach ($applicationTypes as $t): ?>
                        <option value="<?= $t['slug'] ?>" <?= ($filters['type_slug'] ?? '') === $t['slug'] ? 'selected' : '' ?>><?= htmlspecialchars($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- All Statuses --</option>
                        <?php
                        $statuses = ['Draft', 'Submitted', 'Under Review', 'For Revision', 'Qualified', 'Disqualified', 'Approved', 'Rejected'];
                        foreach ($statuses as $st):
                        ?>
                        <option value="<?= $st ?>" <?= ($filters['status'] ?? '') === $st ? 'selected' : '' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="year_level" class="form-select form-select-sm">
                        <option value="">-- Year Level --</option>
                        <?php for ($y=1; $y<=5; $y++): ?>
                        <option value="<?= $y ?>" <?= ($filters['year_level'] ?? '') == $y ? 'selected' : '' ?>><?= yearLevelLabel($y) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-sm w-100"><i class="fas fa-filter me-1"></i>Filter</button>
                    <a href="<?= BASE_URL ?>/admin/applications" class="btn btn-outline-secondary btn-sm"><i class="fas fa-undo"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <span class="fw-bold text-muted small text-uppercase">Showing <?= count($applications) ?> of <?= $total ?> Application(s)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">App Number</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Program</th>
                            <th>Year</th>
                            <th>Type</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Assigned Staff</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($applications)): ?>
                        <tr><td colspan="10" class="text-center py-5 text-muted">No applications match your criteria.</td></tr>
                        <?php else: ?>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-success">
                                <a href="<?= BASE_URL ?>/admin/applications/view?id=<?= $app['id'] ?>" class="text-decoration-none text-success">
                                    <?= htmlspecialchars($app['application_number']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($app['student_number']) ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($app['last_name'] . ', ' . $app['first_name']) ?></td>
                            <td class="small text-truncate" style="max-width: 140px;"><?= htmlspecialchars($app['degree_program']) ?></td>
                            <td><?= $app['year_level'] ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($app['type_name']) ?></span></td>
                            <td class="small text-muted"><?= formatDate($app['submitted_at'] ?? $app['created_at']) ?></td>
                            <td><span class="badge <?= getStatusBadgeClass($app['status']) ?>"><?= htmlspecialchars($app['status']) ?></span></td>
                            <td class="small text-muted"><?= htmlspecialchars(($app['assigned_first'] ?? '') ? $app['assigned_first'] . ' ' . $app['assigned_last'] : 'Unassigned') ?></td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= BASE_URL ?>/admin/applications/view?id=<?= $app['id'] ?>" class="btn btn-outline-success" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (hasPermission('evaluate_applications')): ?>
                                    <a href="<?= BASE_URL ?>/admin/applications/evaluate?id=<?= $app['id'] ?>" class="btn btn-outline-warning" title="Evaluate">
                                        <i class="fas fa-check-double"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (hasPermission('edit_applications')): ?>
                                    <a href="<?= BASE_URL ?>/admin/applications/edit?id=<?= $app['id'] ?>" class="btn btn-outline-secondary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white py-3 border-0">
            <nav>
                <ul class="pagination pagination-sm justify-content-center mb-0">
                    <?php for ($p=1; $p<=$totalPages; $p++): ?>
                    <li class="page-item <?= $p == $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="<?= BASE_URL ?>/admin/applications?page=<?= $p ?>&search=<?= urlencode($filters['search'] ?? '') ?>&status=<?= urlencode($filters['status'] ?? '') ?>&type_slug=<?= urlencode($filters['type_slug'] ?? '') ?>"><?= $p ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin_layout.php'; ?>
