<?php ob_start(); ?>
<div class="container-fluid px-0" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-qsu-green mb-1">NOTIFICATIONS</h3>
            <p class="text-muted mb-0">Updates, announcements, and evaluations from OSAS.</p>
        </div>
        <div>
            <button class="btn btn-outline-secondary btn-sm" onclick="markAllRead()">
                <i class="fas fa-check-double me-1"></i>Mark all as read
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <?php if (empty($notifications)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-bell-slash fa-3x mb-3 text-secondary"></i>
                <h5>No notifications yet</h5>
                <p class="small">We'll alert you here when your application status changes.</p>
            </div>
            <?php else: ?>
            <ul class="list-group list-group-flush" id="notifList">
                <?php foreach ($notifications as $n): ?>
                <li class="list-group-item p-3 d-flex align-items-start gap-3 <?= $n['is_read'] ? '' : 'bg-light' ?>" id="notif-<?= $n['id'] ?>">
                    <div class="rounded-circle p-2 bg-success text-white mt-1">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($n['title']) ?></h6>
                            <small class="text-muted"><?= timeAgo($n['created_at']) ?></small>
                        </div>
                        <p class="mb-1 text-muted small"><?= nl2br(htmlspecialchars($n['message'])) ?></p>
                        <?php if ($n['related_application_id']): ?>
                        <a href="<?= BASE_URL ?>/student/application" class="small text-success text-decoration-none fw-semibold">View Application &rarr;</a>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function markAllRead() {
    fetch('<?= BASE_URL ?>/student/notifications/read', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'all=1'
    }).then(r => r.json()).then(data => {
        if (data.success) location.reload();
    });
}
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/student_layout.php'; ?>
