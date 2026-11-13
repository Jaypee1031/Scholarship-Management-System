<?php ob_start(); ?>
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-0 text-navy">Audit Trail & Activity Logs</h3>
            <p class="text-muted small mb-0">Complete historical record of administrative actions, data edits, and status overrides.</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <form action="<?= BASE_URL ?>/admin/audit-logs" method="GET" class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="action" class="form-control form-control-sm" placeholder="Action (e.g. EDIT, STATUS_OVERRIDE)" value="<?= htmlspecialchars($filters['action'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-sm w-100"><i class="fas fa-filter me-1"></i>Filter</button>
                    <a href="<?= BASE_URL ?>/admin/audit-logs" class="btn btn-outline-secondary btn-sm"><i class="fas fa-undo"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Timestamp</th>
                            <th>Admin / Staff</th>
                            <th>Action</th>
                            <th>Target Table</th>
                            <th>Field</th>
                            <th>Old Value</th>
                            <th>New Value</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                        <tr><td colspan="9" class="text-center py-5 text-muted">No audit logs recorded matching criteria.</td></tr>
                        <?php else: ?>
                        <?php foreach ($logs as $l): ?>
                        <tr>
                            <td class="ps-3 text-nowrap"><?= formatDate($l['created_at'], 'M d, Y h:i A') ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars(($l['admin_first'] ?? 'System') . ' ' . ($l['admin_last'] ?? '')) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($l['action']) ?></span></td>
                            <td><code><?= htmlspecialchars($l['table_name']) ?></code></td>
                            <td><?= htmlspecialchars($l['field_name'] ?? '-') ?></td>
                            <td class="text-danger small text-truncate" style="max-width: 120px;" title="<?= htmlspecialchars($l['old_value'] ?? '') ?>">
                                <?= htmlspecialchars(substr($l['old_value'] ?? '-', 0, 20)) ?>
                            </td>
                            <td class="text-success small text-truncate" style="max-width: 120px;" title="<?= htmlspecialchars($l['new_value'] ?? '') ?>">
                                <?= htmlspecialchars(substr($l['new_value'] ?? '-', 0, 20)) ?>
                            </td>
                            <td class="small text-secondary"><?= htmlspecialchars($l['description']) ?></td>
                            <td><small class="text-muted"><?= htmlspecialchars($l['ip_address'] ?? '127.0.0.1') ?></small></td>
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
                        <a class="page-link" href="<?= BASE_URL ?>/admin/audit-logs?page=<?= $p ?>"><?= $p ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin_layout.php'; ?>
