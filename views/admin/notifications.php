<?php ob_start(); ?>
<div class="container-fluid px-0" style="max-width: 800px;">
    <h3 class="fw-bold mb-0 text-navy">Send Announcement / Notification</h3>
    <p class="text-muted small mb-4">Broadcast important dates, reminders, or messages to student groups.</p>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="<?= BASE_URL ?>/admin/notifications/send" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Target Audience</label>
                    <select name="target" class="form-select" id="targetSelect" onchange="checkSpecificTarget(this.value)">
                        <option value="all">All Registered Students</option>
                        <option value="scholarship">Scholarship & Financial Assistance Applicants</option>
                        <option value="academic_achiever">Academic Achiever Applicants</option>
                        <option value="student_assistant">Student Assistant Applicants</option>
                        <option value="specific">Specific Student ID...</option>
                    </select>
                </div>

                <div class="mb-3 d-none" id="specificStudentBox">
                    <label class="form-label small fw-semibold text-primary">Student ID Number</label>
                    <input type="text" name="specific_student_id" class="form-control" placeholder="e.g. QSU-2024-001">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Notification Type</label>
                    <select name="notif_type" class="form-select">
                        <option value="general">General Notice</option>
                        <option value="important">Important Alert</option>
                        <option value="reminder">Deadline Reminder</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Announcement Title <span class="text-danger">*</span></label>
                    <input type="text" name="notif_title" class="form-control" placeholder="e.g. Submission Deadline Extended" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold">Message Content <span class="text-danger">*</span></label>
                    <textarea name="notif_message" class="form-control" rows="4" placeholder="Enter message text..." required></textarea>
                </div>

                <button type="submit" class="btn btn-success px-4 py-2 fw-semibold">
                    <i class="fas fa-paper-plane me-2"></i> Broadcast Announcement
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function checkSpecificTarget(val) {
    var b = document.getElementById('specificStudentBox');
    if (val === 'specific') b.classList.remove('d-none');
    else b.classList.add('d-none');
}
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin_layout.php'; ?>
