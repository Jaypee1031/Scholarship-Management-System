<?php ob_start(); ?>
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1 text-qsu-green">MY APPLICATION</h3>
            <p class="text-muted mb-0">Application Number: <span class="fw-bold text-dark"><?= htmlspecialchars($application['application_number']) ?></span></p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge <?= getStatusBadgeClass($application['status']) ?> px-3 py-2 fs-6 align-self-center"><?= htmlspecialchars($application['status']) ?></span>
            <a href="<?= BASE_URL ?>/document/pdf?id=<?= $application['id'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-print me-1"></i> Printable PDF
            </a>
            <?php if ($application['status'] === 'Draft' || $application['status'] === 'For Revision'): ?>
                <?php
                $editRoutes = ['scholarship' => '/student/apply/scholarship', 'academic_achiever' => '/student/apply/academic', 'student_assistant' => '/student/apply/assistant'];
                $route = $editRoutes[$application['type_slug'] ?? ''] ?? '/student/dashboard';
                ?>
                <a href="<?= BASE_URL . $route ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-edit me-1"></i> Edit Form
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($application['status'] === 'For Revision'): ?>
    <div class="alert alert-warning border-warning shadow-sm mb-4">
        <h5 class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Action Required: Application For Revision</h5>
        <p class="mb-2">Your application needs additional details or corrected documents as requested by the evaluator.</p>
        <?php if (!empty($evaluations)): ?>
            <div class="bg-white p-3 rounded border text-dark">
                <strong>Required Corrections:</strong><br>
                <?= nl2br(htmlspecialchars($evaluations[0]['required_corrections'] ?? $evaluations[0]['remarks'])) ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs border-0 px-3" id="appDetailTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active py-3 fw-semibold text-dark" data-bs-toggle="tab" data-bs-target="#tabInfo" type="button">
                        <i class="fas fa-user me-2 text-success"></i>Application Details
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 fw-semibold text-dark" data-bs-toggle="tab" data-bs-target="#tabDocs" type="button">
                        <i class="fas fa-file-upload me-2 text-primary"></i>Uploaded Documents (<?= count($documents) ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 fw-semibold text-dark" data-bs-toggle="tab" data-bs-target="#tabHistory" type="button">
                        <i class="fas fa-history me-2 text-warning"></i>Status History
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 fw-semibold text-dark" data-bs-toggle="tab" data-bs-target="#tabEval" type="button">
                        <i class="fas fa-clipboard-check me-2 text-info"></i>Evaluator Remarks
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="appDetailTabContent">
                <div class="tab-pane fade show active" id="tabInfo">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold border-bottom pb-2 text-qsu-green">Personal Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr><th style="width: 40%;" class="text-muted">Full Name:</th><td class="fw-semibold"><?= htmlspecialchars($application['last_name'] . ', ' . $application['first_name'] . ' ' . $application['middle_name']) ?></td></tr>
                                <tr><th class="text-muted">Student ID:</th><td><?= htmlspecialchars($application['student_number']) ?></td></tr>
                                <tr><th class="text-muted">Degree Program:</th><td><?= htmlspecialchars($application['degree_program']) ?></td></tr>
                                <tr><th class="text-muted">Year Level:</th><td><?= yearLevelLabel($application['year_level']) ?></td></tr>
                                <tr><th class="text-muted">Sex / Civil Status:</th><td><?= htmlspecialchars($application['sex'] ?? '') ?> / <?= htmlspecialchars($application['civil_status'] ?? '') ?></td></tr>
                                <tr><th class="text-muted">Solo Parent:</th><td><?= $application['is_solo_parent'] ? 'Yes' : 'No' ?></td></tr>
                                <tr><th class="text-muted">Citizenship:</th><td><?= htmlspecialchars($application['citizenship'] ?? 'Filipino') ?></td></tr>
                                <tr><th class="text-muted">Birthdate:</th><td><?= formatDate($application['birthdate']) ?></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold border-bottom pb-2 text-qsu-green">Contact & Address</h6>
                            <table class="table table-sm table-borderless">
                                <tr><th style="width: 40%;" class="text-muted">Permanent Address:</th><td><?= htmlspecialchars(($application['street_barangay'] ?? '') . ', ' . ($application['municipality'] ?? '') . ', ' . ($application['province'] ?? '')) ?></td></tr>
                                <tr><th class="text-muted">Contact Number:</th><td><?= htmlspecialchars($application['contact_number'] ?? 'N/A') ?></td></tr>
                                <tr><th class="text-muted">Email Address:</th><td><?= htmlspecialchars($application['email_address'] ?? 'N/A') ?></td></tr>
                                <tr><th class="text-muted">QSU Dormitory:</th><td><?= $application['is_dormitory'] ? 'Yes' : 'No' ?></td></tr>
                                <tr><th class="text-muted">Application Type:</th><td><span class="badge bg-success"><?= htmlspecialchars($application['type_name']) ?></span></td></tr>
                                <tr><th class="text-muted">Period:</th><td><?= htmlspecialchars($application['period_name'] ?? 'Current') ?></td></tr>
                            </table>
                        </div>
                    </div>

                    <?php if (!empty($familyMembers)): ?>
                    <h6 class="fw-bold border-bottom pb-2 text-qsu-green mt-4">Family Background</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr><th>Relationship</th><th>Name</th><th>Age</th><th>Occupation</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($familyMembers as $fm): ?>
                                <tr>
                                    <td><?= htmlspecialchars($fm['relationship']) ?></td>
                                    <td><?= htmlspecialchars($fm['last_name'] . ', ' . $fm['first_name'] . ' ' . $fm['middle_name']) ?></td>
                                    <td><?= $fm['age'] ?? '-' ?></td>
                                    <td><?= htmlspecialchars($fm['occupation'] ?? '-') ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($fm['member_status']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($eduAssistance)): ?>
                    <h6 class="fw-bold border-bottom pb-2 text-qsu-green mt-4">Other Educational Assistance</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr><th>Type</th><th>Grantee</th><th>Institution / Agency</th><th>Amount</th></tr>
                            </thead>
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
                    </div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="tabDocs">
                    <?php if (empty($documents)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-file-upload fa-2x mb-2"></i>
                        <p>No documents uploaded yet.</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr><th>Requirement</th><th>Filename</th><th>Size</th><th>Uploaded</th><th>Status</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($doc['requirement_name'] ?? 'Document') ?></td>
                                    <td><?= htmlspecialchars($doc['original_filename']) ?></td>
                                    <td><?= round($doc['file_size'] / 1024, 1) ?> KB</td>
                                    <td><?= formatDate($doc['uploaded_at']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $doc['upload_status'] === 'Verified' ? 'success' : ($doc['upload_status'] === 'Rejected' ? 'danger' : 'warning text-dark') ?>">
                                            <?= htmlspecialchars($doc['upload_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/document/serve?file=<?= urlencode($doc['stored_filename']) ?>" target="_blank" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="tabHistory">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($statusHistory as $hist): ?>
                        <li class="list-group-item px-0 py-3">
                            <div class="d-flex justify-content-between">
                                <span class="badge <?= getStatusBadgeClass($hist['new_status']) ?> px-2 py-1"><?= htmlspecialchars($hist['new_status']) ?></span>
                                <small class="text-muted"><?= formatDate($hist['created_at'], 'M d, Y h:i A') ?></small>
                            </div>
                            <p class="mb-0 small text-muted mt-1"><?= htmlspecialchars($hist['remarks'] ?? 'Status updated') ?></p>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="tab-pane fade" id="tabEval">
                    <?php if (empty($evaluations)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                        <p>No evaluations recorded yet.</p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($evaluations as $ev): ?>
                    <div class="card bg-light border mb-3 p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="fw-bold mb-0">Evaluated by: <?= htmlspecialchars(($ev['eval_first'] ?? 'Staff') . ' ' . ($ev['eval_last'] ?? '')) ?></h6>
                            <small class="text-muted"><?= formatDate($ev['evaluated_at'], 'M d, Y h:i A') ?></small>
                        </div>
                        <p class="mb-1 small"><strong>Remarks:</strong> <?= htmlspecialchars($ev['remarks']) ?></p>
                        <?php if ($ev['disqualification_reason']): ?>
                        <p class="mb-1 small text-danger"><strong>Disqualification Reason:</strong> <?= htmlspecialchars($ev['disqualification_reason']) ?></p>
                        <?php endif; ?>
                        <?php if ($ev['required_corrections']): ?>
                        <p class="mb-0 small text-warning text-dark"><strong>Required Corrections:</strong> <?= htmlspecialchars($ev['required_corrections']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/student_layout.php'; ?>
