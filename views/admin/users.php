<?php ob_start(); ?>
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-0 text-navy">User Management</h3>
            <p class="text-muted small mb-0">Manage administrator roles, OSAS staff, and student access.</p>
        </div>
        <div>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#newAdminModal">
                <i class="fas fa-user-plus me-1"></i> Add Administrator
            </button>
        </div>
    </div>

    <!-- Nav tabs -->
    <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-semibold" data-bs-toggle="pill" data-bs-target="#pAdmins">
                <i class="fas fa-user-shield me-1"></i> Admin Accounts (<?= count($admins) ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-semibold" data-bs-toggle="pill" data-bs-target="#pStudents">
                <i class="fas fa-user-graduate me-1"></i> Student Accounts (<?= count($students) ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Admins Tab -->
        <div class="tab-pane fade show active" id="pAdmins">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Office</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($admins as $a): ?>
                                <tr>
                                    <td class="ps-3 fw-bold"><?= htmlspecialchars(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? '')) ?></td>
                                    <td><code><?= htmlspecialchars($a['username']) ?></code></td>
                                    <td><?= htmlspecialchars($a['email']) ?></td>
                                    <td><span class="badge bg-success"><?= htmlspecialchars($a['role_name']) ?></span></td>
                                    <td><?= htmlspecialchars($a['office'] ?? 'OSAS') ?></td>
                                    <td>
                                        <span class="badge <?= $a['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $a['is_active'] ? 'Active' : 'Disabled' ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <button class="btn btn-outline-secondary btn-sm" onclick="toggleUser(<?= $a['user_id'] ?>, <?= $a['is_active'] ? 0 : 1 ?>)">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Tab -->
        <div class="tab-pane fade" id="pStudents">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Student ID</th>
                                    <th>Name</th>
                                    <th>Degree Program</th>
                                    <th>Year</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Reset Password</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $s): ?>
                                <tr>
                                    <td class="ps-3 fw-bold text-success"><?= htmlspecialchars($s['student_id']) ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($s['last_name'] . ', ' . $s['first_name']) ?></td>
                                    <td><?= htmlspecialchars($s['degree_program'] ?? '') ?></td>
                                    <td><?= yearLevelLabel($s['year_level'] ?? 1) ?></td>
                                    <td><?= htmlspecialchars($s['email_address'] ?? '') ?></td>
                                    <td>
                                        <span class="badge <?= ($s['is_active'] ?? 1) ? 'bg-success' : 'bg-danger' ?>">
                                            <?= ($s['is_active'] ?? 1) ? 'Active' : 'Locked' ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <form action="<?= BASE_URL ?>/admin/users/reset-password" method="POST" class="d-inline" onsubmit="return confirm('Reset password for student <?= htmlspecialchars($s['student_id']) ?>?')">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                            <input type="hidden" name="user_id" value="<?= $s['user_id'] ?>">
                                            <button type="submit" class="btn btn-outline-warning btn-sm" title="Reset to temporary password">
                                                <i class="fas fa-key me-1"></i> Reset
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: New Admin -->
<div class="modal fade" id="newAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>/admin/users/create" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Create Administrator Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">First Name</label>
                            <input type="text" name="first_name" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Last Name</label>
                            <input type="text" name="last_name" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Username</label>
                        <input type="text" name="username" class="form-control form-control-sm" placeholder="e.g. staff02" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control form-control-sm" placeholder="staff@qsu.edu.ph" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Role</label>
                        <select name="role_id" class="form-select form-select-sm">
                            <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Office / Department</label>
                        <input type="text" name="office" class="form-control form-control-sm" value="Scholarship Office">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control form-control-sm" required minlength="8">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm fw-semibold">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleUser(userId, status) {
    if (!confirm('Change account status?')) return;
    fetch('<?= BASE_URL ?>/admin/users/toggle', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'csrf_token=<?= $csrf_token ?>&user_id=' + userId + '&is_active=' + status
    }).then(r => r.json()).then(d => {
        if (d.success) location.reload();
    });
}
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin_layout.php'; ?>
