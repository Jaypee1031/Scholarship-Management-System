<?php
$unreadCount = $unreadCount ?? 0;
$student = $student ?? [];
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <!-- Top Navigation (Matching Admission Portal) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>/student/dashboard">
                <img src="<?= BASE_URL ?>/public/assets/img/qsulogo.png" alt="QSU Logo" class="me-2">
                <span><?= APP_NAME ?></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#studentNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="studentNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/student/dashboard') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/dashboard">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/student/application') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/application">
                            <i class="fas fa-file-alt me-1"></i> My Application
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= strpos($_SERVER['REQUEST_URI'], '/student/apply') !== false ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-file-pen me-1"></i> Fill Up Form
                        </a>
                        <ul class="dropdown-menu shadow">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/student/apply/assistant"><i class="fas fa-user-tie me-2 text-primary"></i>Student Assistant</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/student/apply/academic"><i class="fas fa-graduation-cap me-2 text-primary"></i>Academic Achiever</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item small text-muted" href="<?= BASE_URL ?>/student/select-type"><i class="fas fa-th-large me-2"></i>Select Form</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative <?= strpos($_SERVER['REQUEST_URI'], '/student/notifications') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/notifications">
                            <i class="fas fa-bell me-1"></i> Notifications
                            <?php if ($unreadCount > 0): ?>
                            <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.7rem;"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/student/profile') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/profile">
                            <i class="fas fa-user me-1"></i> Profile
                        </a>
                    </li>
                </ul>
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white d-flex align-items-center navbar-user-btn" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fa-lg me-2"></i>
                            <span class="me-1"><?= htmlspecialchars($student['first_name'] ?? 'Student') ?></span>
                            <span class="badge bg-light text-success fw-bold ms-1" style="font-size: 0.72rem;">STUDENT</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><span class="dropdown-item-text text-muted small"><i class="fas fa-id-card me-2"></i><?= htmlspecialchars($student['student_id'] ?? '') ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/student/profile"><i class="fas fa-user-edit me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/student/logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="container py-4 flex-grow-1">
        <?php include __DIR__ . '/../partials/flash_messages.php'; ?>
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container text-center">
            <p class="mb-1 fw-semibold">Quirino State University &bull; Office of Student Affairs and Services (OSAS)</p>
            <p class="mb-0 text-muted small">Scholarship and Financial Assistance Portal &bull; Form: QSU-SCH-F003 Rev. 00</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/js/main.js"></script>
</body>
</html>
