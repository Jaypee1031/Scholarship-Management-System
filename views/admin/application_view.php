<?php ob_start(); ?>
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/applications">Applications</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($application['application_number']) ?></li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 text-navy"><?= htmlspecialchars($application['last_name'] . ', ' . $application['first_name'] . ' ' . $application['middle_name']) ?></h3>
            <span class="badge bg-secondary"><?= htmlspecialchars($application['type_name']) ?></span>
            <span class="badge <?= getStatusBadgeClass($application['status']) ?> ms-1"><?= htmlspecialchars($application['status']) ?></span>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/document/pdf?id=<?= $application['id'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-print me-1"></i> Print PDF
            </a>
            <?php if (hasPermission('evaluate_applications')): ?>
            <a href="<?= BASE_URL ?>/admin/applications/evaluate?id=<?= $application['id'] ?>" class="btn btn-success btn-sm">
                <i class="fas fa-check-double me-1"></i> Evaluate Application
            </a>
            <?php endif; ?>
            <?php if (hasPermission('edit_applications')): ?>
            <a href="<?= BASE_URL ?>/admin/applications/edit?id=<?= $application['id'] ?>" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-edit me-1"></i> Edit Details
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Detail Tabs -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs border-0 px-3" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active py-3 fw-semibold text-dark" data-bs-toggle="tab" data-bs-target="#vInfo" type="button">
                        <i class="fas fa-id-badge text-success me-2"></i>Student Details
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 fw-semibold text-dark" data-bs-toggle="tab" data-bs-target="#vDocs" type="button">
                        <i class="fas fa-folder text-primary me-2"></i>Documents (<?= count($documents) ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 fw-semibold text-dark" data-bs-toggle="tab" data-bs-target="#vEval" type="button">
                        <i class="fas fa-clipboard-check text-warning me-2"></i>Evaluations & Remarks
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 fw-semibold text-dark" data-bs-toggle="tab" data-bs-target="#vHistory" type="button">
                        <i class="fas fa-history text-info me-2"></i>Status History
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 fw-semibold text-dark" data-bs-toggle="tab" data-bs-target="#vAudit" type="button">
                        <i class="fas fa-shield-alt text-danger me-2"></i>Audit Trail
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content">
                <!-- Info Tab -->
                <div class="tab-pane fade show active" id="vInfo">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-qsu-green border-bottom pb-2">Academic Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr><th class="text-muted" style="width:40%;">Student ID:</th><td class="fw-bold"><?= htmlspecialchars($application['student_number']) ?></td></tr>
                                <tr><th class="text-muted">Degree Program:</th><td><?= htmlspecialchars($application['degree_program']) ?></td></tr>
                                <tr><th class="text-muted">Year Level:</th><td><?= yearLevelLabel($application['year_level']) ?></td></tr>
                                <tr><th class="text-muted">Registering As:</th><td><?= htmlspecialchars($application['registering_as'] ?? 'N/A') ?></td></tr>
                                <tr><th class="text-muted">LRN Number:</th><td><?= htmlspecialchars($application['lrn_number'] ?? 'N/A') ?></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-qsu-green border-bottom pb-2">Personal & Contact Info</h6>
                            <table class="table table-sm table-borderless">
                                <tr><th class="text-muted" style="width:40%;">Sex / Civil Status:</th><td><?= htmlspecialchars($application['sex'] ?? '') ?> / <?= htmlspecialchars($application['civil_status'] ?? '') ?></td></tr>
                                <tr><th class="text-muted">Solo Parent:</th><td><?= $application['is_solo_parent'] ? 'Yes' : 'No' ?></td></tr>
                                <tr><th class="text-muted">Birthdate / Place:</th><td><?= formatDate($application['birthdate']) ?> (<?= htmlspecialchars($application['place_of_birth'] ?? '') ?>)</td></tr>
                                <tr><th class="text-muted">Disability / Tribe:</th><td><?= htmlspecialchars($application['physical_disability'] ?? 'None') ?> / <?= htmlspecialchars($application['ethnic_affiliation'] ?? 'None') ?></td></tr>
                                <tr><th class="text-muted">Address:</th><td><?= htmlspecialchars(($application['street_barangay'] ?? '') . ', ' . ($application['municipality'] ?? '') . ', ' . ($application['province'] ?? '')) ?></td></tr>
                                <tr><th class="text-muted">Contact:</th><td><?= htmlspecialchars($application['contact_number'] ?? '') ?> (<?= htmlspecialchars($application['email_address'] ?? '') ?>)</td></tr>
                                <tr><th class="text-muted">QSU Dormitory:</th><td><?= $application['is_dormitory'] ? 'Yes' : 'No' ?></td></tr>
                            </table>
                        </div>
                    </div>

                    <?php if (!empty($familyMembers)): ?>
                    <h6 class="fw-bold text-qsu-green border-bottom pb-2 mt-3">Family Members</h6>
                    <table class="table table-bordered table-sm mb-4">
                        <thead class="table-light"><tr><th>Rel</th><th>Name</th><th>Age</th><th>Occupation</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($familyMembers as $m): ?>
                            <tr>
                                <td><?= htmlspecialchars($m['relationship']) ?></td>
                                <td><?= htmlspecialchars($m['last_name'] . ', ' . $m['first_name'] . ' ' . $m['middle_name']) ?></td>
                                <td><?= $m['age'] ?? '-' ?></td>
                                <td><?= htmlspecialchars($m['occupation'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($m['member_status']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>

                    <?php if (!empty($eduAssistance)): ?>
                    <h6 class="fw-bold text-qsu-green border-bottom pb-2">Other Educational Assistance</h6>
                    <table class="table table-bordered table-sm">
                        <thead class="table-light"><tr><th>Type</th><th>Grantee</th><th>Institution / Agency</th><th>Amount</th></tr></thead>
                        <tbody>
                            <?php foreach ($eduAssistance as $ea): ?>
                            <tr>
                                <td><?= htmlspecialchars($ea['assistance_type']) ?></td>
                                <td><?= htmlspecialchars($ea['grantee']) ?></td>
                                <td><?= htmlspecialchars($ea['institution_agency']) ?></td>
                                <td><?= $ea['amount'] ? formatMoney($ea['amount']) : 'N/A' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

                <!-- Documents Tab -->
                <div class="tab-pane fade" id="vDocs">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light"><tr><th>Requirement</th><th>File</th><th>Size</th><th>Uploaded</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                            <tbody>
                                <?php if (empty($documents)): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">No documents uploaded.</td></tr>
                                <?php else: ?>
                                <?php foreach ($documents as $d): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($d['requirement_name'] ?? 'Document') ?></td>
                                    <td><?= htmlspecialchars($d['original_filename']) ?></td>
                                    <td><?= round($d['file_size']/1024, 1) ?> KB</td>
                                    <td><?= formatDate($d['uploaded_at']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($d['upload_status']) ?></span></td>
                                    <td class="text-end">
                                        <a href="<?= BASE_URL ?>/document/serve?file=<?= urlencode($d['stored_filename']) ?>" target="_blank" class="btn btn-outline-success btn-sm"><i class="fas fa-eye me-1"></i>View</a>
                                        <a href="<?= BASE_URL ?>/document/download?id=<?= $d['id'] ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-download"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Evaluations Tab -->
                <div class="tab-pane fade" id="vEval">
                    <?php if (empty($evaluations)): ?>
                    <p class="text-muted">No evaluation submitted yet.</p>
                    <?php else: ?>
                    <?php foreach ($evaluations as $e): ?>
                    <div class="card p-3 mb-3 bg-light border">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="fw-bold mb-0"><?= htmlspecialchars(($e['eval_first'] ?? 'Evaluator') . ' ' . ($e['eval_last'] ?? '')) ?></h6>
                            <small class="text-muted"><?= formatDate($e['evaluated_at'], 'M d, Y h:i A') ?></small>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-secondary">Documents: <?= htmlspecialchars($e['documents_status'] ?? 'N/A') ?></span>
                            <span class="badge <?= getStatusBadgeClass($e['decision']) ?> ms-1">Decision: <?= htmlspecialchars($e['decision']) ?></span>
                        </div>
                        <p class="small mb-1"><strong>Remarks:</strong> <?= htmlspecialchars($e['remarks']) ?></p>
                        <?php if ($e['disqualification_reason']): ?>
                        <p class="small text-danger mb-0"><strong>Disqualification Reason:</strong> <?= htmlspecialchars($e['disqualification_reason']) ?></p>
                        <?php endif; ?>
                        <?php if ($e['required_corrections']): ?>
                        <p class="small text-warning text-dark mb-0"><strong>Required Corrections:</strong> <?= htmlspecialchars($e['required_corrections']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- History Tab -->
                <div class="tab-pane fade" id="vHistory">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($statusHistory as $h): ?>
                        <li class="list-group-item px-0 py-2">
                            <span class="badge <?= getStatusBadgeClass($h['new_status']) ?>"><?= htmlspecialchars($h['new_status']) ?></span>
                            <span class="small text-muted ms-2"><?= formatDate($h['created_at'], 'M d, Y h:i A') ?></span>
                            <p class="mb-0 small text-secondary mt-1"><?= htmlspecialchars($h['remarks'] ?? 'Status updated') ?></p>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Audit Tab -->
                <div class="tab-pane fade" id="vAudit">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped small">
                            <thead><tr><th>Date</th><th>Admin</th><th>Action</th><th>Field</th><th>Old</th><th>New</th><th>Description</th></tr></thead>
                            <tbody>
                                <?php if (empty($auditLogs)): ?>
                                <tr><td colspan="7" class="text-center text-muted">No audit logs for this application.</td></tr>
                                <?php else: ?>
                                <?php foreach ($auditLogs as $al): ?>
                                <tr>
                                    <td><?= formatDate($al['created_at'], 'M d, Y h:i A') ?></td>
                                    <td><?= htmlspecialchars(($al['admin_first'] ?? 'Admin') . ' ' . ($al['admin_last'] ?? '')) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($al['action']) ?></span></td>
                                    <td><?= htmlspecialchars($al['field_name'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars(substr($al['old_value'] ?? '-', 0, 30)) ?></td>
                                    <td><?= htmlspecialchars(substr($al['new_value'] ?? '-', 0, 30)) ?></td>
                                    <td><?= htmlspecialchars($al['description'] ?? '') ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin_layout.php'; ?>
