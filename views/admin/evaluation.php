<?php ob_start(); ?>
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-0 text-navy">Application Evaluation</h3>
            <p class="text-muted small mb-0">App # <?= htmlspecialchars($application['application_number']) ?> &bull; <?= htmlspecialchars($application['last_name'] . ', ' . $application['first_name']) ?></p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/admin/applications/view?id=<?= $application['id'] ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to View
            </a>
        </div>
    </div>

    <form action="<?= BASE_URL ?>/admin/applications/evaluate" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="hidden" name="application_id" value="<?= $application['id'] ?>">

        <div class="row g-4">
            <!-- Left: Document Review -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-tasks text-success me-2"></i>1. Document Verification Checklist</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Requirement</th>
                                        <th>Uploaded File</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($requirements as $req): ?>
                                    <?php
                                    $uploaded = null;
                                    foreach ($documents as $d) {
                                        if (($d['requirement_id'] ?? null) == $req['id']) {
                                            $uploaded = $d; break;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold small"><?= htmlspecialchars($req['name']) ?></div>
                                            <small class="text-muted"><?= $req['is_required'] ? '<span class="text-danger">Required</span>' : 'Optional' ?></small>
                                        </td>
                                        <td>
                                            <?php if ($uploaded): ?>
                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i> <?= htmlspecialchars($uploaded['original_filename']) ?></span>
                                            <?php else: ?>
                                            <span class="badge bg-danger"><i class="fas fa-times me-1"></i> Missing</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-3">
                                            <?php if ($uploaded): ?>
                                            <a href="<?= BASE_URL ?>/document/serve?file=<?= urlencode($uploaded['stored_filename']) ?>" target="_blank" class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-user-graduate text-primary me-2"></i>Student Background Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 small">
                            <div class="col-sm-6"><strong>Program:</strong> <?= htmlspecialchars($application['degree_program']) ?></div>
                            <div class="col-sm-6"><strong>Year Level:</strong> <?= yearLevelLabel($application['year_level']) ?></div>
                            <div class="col-sm-6"><strong>Contact:</strong> <?= htmlspecialchars($application['contact_number'] ?? 'N/A') ?></div>
                            <div class="col-sm-6"><strong>Municipality:</strong> <?= htmlspecialchars($application['municipality'] ?? 'N/A') ?></div>
                            <div class="col-sm-6"><strong>Solo Parent:</strong> <?= $application['is_solo_parent'] ? 'Yes' : 'No' ?></div>
                            <div class="col-sm-6"><strong>Disability:</strong> <?= htmlspecialchars($application['physical_disability'] ?? 'None') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Evaluation Decision -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 eval-panel">
                    <h5 class="fw-bold text-navy mb-3"><i class="fas fa-gavel text-warning me-2"></i>Official Evaluation</h5>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Documents Overall Status</label>
                        <div class="d-flex gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="documents_status" value="Complete" id="docComp" checked>
                                <label class="form-check-label small" for="docComp">Complete</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="documents_status" value="Incomplete" id="docIncomp">
                                <label class="form-check-label small" for="docIncomp">Incomplete</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="documents_status" value="For Revision" id="docRev">
                                <label class="form-check-label small" for="docRev">For Revision</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Evaluation Decision <span class="text-danger">*</span></label>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="decision" value="Qualified" id="decQual" required>
                            <label class="form-check-label fw-bold text-success" for="decQual">
                                <i class="fas fa-check-circle me-1"></i> Qualified
                            </label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="decision" value="Disqualified" id="decDisqual">
                            <label class="form-check-label fw-bold text-danger" for="decDisqual">
                                <i class="fas fa-times-circle me-1"></i> Disqualified
                            </label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="decision" value="For Revision" id="decRevision">
                            <label class="form-check-label fw-bold text-warning text-dark" for="decRevision">
                                <i class="fas fa-edit me-1"></i> For Revision
                            </label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="decision" value="Approved" id="decAppr">
                            <label class="form-check-label fw-bold text-primary" for="decAppr">
                                <i class="fas fa-award me-1"></i> Approved (Awarded)
                            </label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="decision" value="Rejected" id="decRej">
                            <label class="form-check-label fw-bold text-secondary" for="decRej">
                                <i class="fas fa-ban me-1"></i> Rejected
                            </label>
                        </div>
                    </div>

                    <!-- Disqualification Reason Conditional Box -->
                    <div id="disqualifyReasonBox" class="mb-3 d-none">
                        <label class="form-label small fw-bold text-danger">Reason for Disqualification <span class="text-danger">*</span></label>
                        <textarea name="disqualification_reason" class="form-control form-control-sm" rows="2" placeholder="State specific disqualification grounds..."></textarea>
                    </div>

                    <!-- Required Correction Conditional Box -->
                    <div id="revisionBox" class="mb-3 d-none">
                        <label class="form-label small fw-bold text-warning text-dark">Required Corrections / Missing Items <span class="text-danger">*</span></label>
                        <textarea name="required_corrections" class="form-control form-control-sm" rows="2" placeholder="List instructions for the student..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">General Remarks / Notes <span class="text-danger">*</span></label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="Enter evaluation comments for record..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                        <i class="fas fa-save me-1"></i> Submit Evaluation & Notify Student
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin_layout.php'; ?>
