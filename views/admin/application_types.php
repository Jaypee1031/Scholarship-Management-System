<?php ob_start(); ?>
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-0 text-navy">Application Type Management</h3>
            <p class="text-muted small mb-0">Control availability, deadlines, instructions, and requirements for each type.</p>
        </div>
        <div>
            <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#newPeriodModal">
                <i class="fas fa-calendar-plus me-1"></i> New Application Period
            </button>
        </div>
    </div>

    <!-- Active Period Summary -->
    <?php if ($activePeriod): ?>
    <div class="alert alert-success d-flex justify-content-between align-items-center mb-4">
        <div>
            <h6 class="fw-bold mb-0"><i class="fas fa-calendar-check me-2"></i>Active Period: <?= htmlspecialchars($activePeriod['name']) ?></h6>
            <small>AY <?= htmlspecialchars($activePeriod['academic_year']) ?> &bull; <?= htmlspecialchars($activePeriod['semester']) ?> Semester (<?= formatDate($activePeriod['start_date']) ?> to <?= formatDate($activePeriod['end_date']) ?>)</small>
        </div>
        <span class="badge bg-success px-3 py-2">OPEN</span>
    </div>
    <?php endif; ?>

    <!-- Application Types List -->
    <div class="row g-4 mb-4">
        <?php foreach ($applicationTypes as $type): ?>
        <?php if ($type['slug'] === 'scholarship') continue; ?>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($type['name']) ?></h6>
                    <button class="btn btn-sm <?= $type['is_enabled'] ? 'btn-success' : 'btn-danger' ?>" onclick="toggleType(<?= $type['id'] ?>, <?= $type['is_enabled'] ? 0 : 1 ?>)">
                        <?= $type['is_enabled'] ? 'ENABLED' : 'DISABLED' ?>
                    </button>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3"><?= htmlspecialchars($type['description']) ?></p>

                    <form action="<?= BASE_URL ?>/admin/application-types/update" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="type_id" value="<?= $type['id'] ?>">

                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Start Date</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $type['start_date'] ?? '' ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">End Date</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $type['end_date'] ?? '' ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Max Applications</label>
                            <input type="number" name="max_applications" class="form-control form-control-sm" placeholder="Leave empty for unlimited" value="<?= $type['max_applications'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Instructions / Notes</label>
                            <textarea name="instructions" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($type['instructions'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-success btn-sm w-100">Save Configuration</button>
                    </form>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold text-muted">Requirements (<?= count($type['requirements_list'] ?? []) ?>)</span>
                        <button class="btn btn-link btn-sm p-0 text-success text-decoration-none" onclick="openReqModal(<?= $type['id'] ?>)">+ Add</button>
                    </div>
                    <ul class="list-group list-group-flush small">
                        <?php foreach (($type['requirements_list'] ?? []) as $req): ?>
                        <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                            <span><?= htmlspecialchars($req['name']) ?></span>
                            <?php if ($req['is_required']): ?><span class="badge bg-danger" style="font-size:0.65rem;">Req</span><?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal: New Period -->
<div class="modal fade" id="newPeriodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>/admin/periods" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="period_action" value="create">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Create New Application Period</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Period Name</label>
                        <input type="text" name="period_name" class="form-control" placeholder="e.g. Second Semester AY 2025-2026" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Semester</label>
                            <select name="semester" class="form-select">
                                <option value="First">First</option>
                                <option value="Second">Second</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Academic Year</label>
                            <input type="text" name="academic_year" class="form-control" placeholder="2025-2026" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Start Date</label>
                            <input type="date" name="period_start" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">End Date</label>
                            <input type="date" name="period_end" class="form-control">
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="allow_reapplication" value="1" id="allowReapp">
                        <label class="form-check-label small" for="allowReapp">Allow students with existing application to re-apply in this new period</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm fw-semibold">Create & Set Active</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Requirement -->
<div class="modal fade" id="addReqModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>/admin/requirements" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="req_action" value="add">
                <input type="hidden" name="type_id" id="reqTypeId" value="">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Add Requirement Document</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Document Name</label>
                        <input type="text" name="req_name" class="form-control" placeholder="e.g. Certificate of Indigency" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description / Instruction</label>
                        <input type="text" name="req_desc" class="form-control" placeholder="Issued by Barangay Captain">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="req_required" value="1" id="reqReq" checked>
                        <label class="form-check-label small" for="reqReq">Mandatory Requirement</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm fw-semibold">Add Requirement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleType(id, enabled) {
    if (!confirm('Are you sure you want to change the availability status of this application type?')) return;
    fetch('<?= BASE_URL ?>/admin/application-types/toggle', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'csrf_token=<?= $csrf_token ?>&type_id=' + id + '&enabled=' + enabled
    }).then(r => r.json()).then(d => {
        if (d.success) location.reload();
        else alert(d.message || 'Error occurred');
    });
}
function openReqModal(typeId) {
    document.getElementById('reqTypeId').value = typeId;
    new bootstrap.Modal(document.getElementById('addReqModal')).show();
}
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin_layout.php'; ?>
