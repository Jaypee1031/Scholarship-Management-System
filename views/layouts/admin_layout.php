<?php
$adminName = $_SESSION['admin_name'] ?? 'Administrator';
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="<?= BASE_URL ?>/public/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <!-- Top Navigation (Matching Admission Portal Admin Navbar) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>/admin/dashboard">
                <img src="<?= BASE_URL ?>/public/assets/img/qsulogo.png" alt="QSU Logo" class="me-2">
                <span><?= APP_NAME ?></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/dashboard">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/applications') !== false && strpos($_SERVER['REQUEST_URI'], '/application-types') === false) ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/applications">
                            <i class="fas fa-folder-open me-1"></i> Applications
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-sliders-h me-1"></i> Management
                        </a>
                        <ul class="dropdown-menu shadow">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/application-types"><i class="fas fa-toggle-on me-2"></i>Application Types Control</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/sa-positions"><i class="fas fa-briefcase me-2"></i>SA Positions</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/notifications"><i class="fas fa-bullhorn me-2"></i>Announcements</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/reports') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/reports">
                            <i class="fas fa-file-excel me-1"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/users">
                            <i class="fas fa-users-cog me-1"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/audit-logs') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/audit-logs">
                            <i class="fas fa-history me-1"></i> Audit Trail
                        </a>
                    </li>
                </ul>
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white d-flex align-items-center navbar-user-btn" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield me-2"></i>
                            <span class="me-1"><?= htmlspecialchars($adminName) ?></span>
                            <span class="badge bg-light text-success fw-bold ms-1" style="font-size: 0.72rem;">ADMIN</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/dashboard"><i class="fas fa-tachometer-alt me-2 text-success"></i>Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/admin/logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
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
            <p class="mb-0 text-muted small">Administrator Control Portal &bull; Form Code: QSU-SCH-F003 Rev. 00</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/js/main.js"></script>
    <script src="<?= BASE_URL ?>/public/assets/js/admin.js"></script>
</body>
</html>
