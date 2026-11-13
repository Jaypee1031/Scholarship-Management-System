<?php ob_start(); ?>
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-0 text-navy">Student Assistant (SA) Positions</h3>
            <p class="text-muted small mb-0">Manage university office slots and qualifications for student assistants.</p>
        </div>
        <div>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addPosModal">
                <i class="fas fa-plus me-1"></i> Add New Position
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Position Title</th>
                            <th>Office / Department</th>
                            <th>Available Slots</th>
                            <th>Schedule</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($positions)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">No positions configured yet.</td></tr>
                        <?php else: ?>
                        <?php foreach ($positions as $p): ?>
                        <tr>
                            <td class="ps-3 fw-bold"><?= htmlspecialchars($p['title']) ?></td>
                            <td><?= htmlspecialchars($p['office']) ?></td>
                            <td><span class="badge bg-primary px-2 py-1"><?= $p['available_slots'] ?> Slots</span></td>
                            <td class="small text-muted"><?= htmlspecialchars($p['schedule'] ?? 'Flexible') ?></td>
                            <td>
                                <span class="badge <?= $p['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $p['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <form action="<?= BASE_URL ?>/admin/sa-positions" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <input type="hidden" name="pos_action" value="toggle">
                                    <input type="hidden" name="pos_id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm" title="Toggle Active">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </form>
                                <form action="<?= BASE_URL ?>/admin/sa-positions" method="POST" class="d-inline" onsubmit="return confirm('Delete this position?')">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <input type="hidden" name="pos_action" value="delete">
                                    <input type="hidden" name="pos_id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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

<!-- Modal: Add Position -->
<div class="modal fade" id="addPosModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>/admin/sa-positions" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="pos_action" value="add">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Add SA Position</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Position Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Library Assistant" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Office / Department</label>
                        <input type="text" name="office" class="form-control" placeholder="e.g. University Library" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Available Slots</label>
                            <input type="number" name="slots" class="form-control" value="2" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Schedule</label>
                            <input type="text" name="schedule" class="form-control" placeholder="e.g. 4 hrs/day, MWF">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Qualifications</label>
                        <textarea name="qualifications" class="form-control" rows="2" placeholder="Skills, course preference, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm fw-semibold">Add Position</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin_layout.php'; ?>
