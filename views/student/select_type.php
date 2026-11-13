<?php ob_start(); ?>
<div class="container-fluid px-0">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-primary mb-1">SELECT APPLICATION TYPE TO FILL UP</h3>
        <p class="text-muted mb-0">Choose which application form you want to fill up for <?= htmlspecialchars($activePeriod['name'] ?? 'Current Period') ?></p>
        <div class="alert alert-info d-inline-block py-2 px-3 mt-2 small">
            <i class="fas fa-info-circle me-1"></i> Select one category below to start filling out your application.
        </div>
    </div>

    <!-- 2 Available Application Cards -->
    <div class="row g-4 justify-content-center">
        <!-- 1. Student Assistant -->
        <div class="col-lg-5 col-md-6">
            <div class="card shadow h-100 app-type-card p-4 text-center d-flex flex-column">
                <div class="card-header bg-primary text-white text-center py-2 rounded mb-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-user-tie me-2"></i>Student Assistant</h5>
                </div>
                <div class="my-2">
                    <div class="rounded-circle bg-light d-inline-flex p-3 text-primary mb-2">
                        <i class="fas fa-user-tie fa-3x"></i>
                    </div>
                    <span class="badge bg-success px-3 py-1 d-block mx-auto" style="max-width:120px;">AVAILABLE</span>
                </div>
                <p class="text-muted flex-grow-1 px-2 mt-2">
                    Apply for on-campus student assistant jobs, office duties, and library shifts.
                </p>
                <div class="mt-auto pt-3 border-top">
                    <a href="<?= BASE_URL ?>/student/apply/assistant" class="btn btn-primary w-100 fw-semibold py-2 fs-6">
                        <i class="fas fa-edit me-1"></i> Fill Up Student Assistant
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Academic Achiever -->
        <div class="col-lg-5 col-md-6">
            <div class="card shadow h-100 app-type-card p-4 text-center d-flex flex-column">
                <div class="card-header bg-primary text-white text-center py-2 rounded mb-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-graduation-cap me-2"></i>Academic Achiever</h5>
                </div>
                <div class="my-2">
                    <div class="rounded-circle bg-light d-inline-flex p-3 text-primary mb-2">
                        <i class="fas fa-graduation-cap fa-3x"></i>
                    </div>
                    <span class="badge bg-success px-3 py-1 d-block mx-auto" style="max-width:120px;">AVAILABLE</span>
                </div>
                <p class="text-muted flex-grow-1 px-2 mt-2">
                    For honor students, Dean's listers, and student leaders / socio-cultural artists.
                </p>
                <div class="mt-auto pt-3 border-top">
                    <a href="<?= BASE_URL ?>/student/apply/academic" class="btn btn-primary w-100 fw-semibold py-2 fs-6">
                        <i class="fas fa-edit me-1"></i> Fill Up Academic Achiever
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/student_layout.php'; ?>
