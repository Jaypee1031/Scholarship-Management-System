<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <!-- Navigation (Matching Admission Portal) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>/">
                <img src="<?= BASE_URL ?>/public/assets/img/qsulogo.png" alt="QSU Logo" class="me-2">
                <?= APP_NAME ?>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= BASE_URL ?>/student/register"><i class="fas fa-user-plus me-1"></i> Register</a>
            </div>
        </div>
    </nav>

    <div class="container py-5 flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            OSAS Portal Login
                        </h4>
                        <small class="text-white-50" style="font-size: 0.8rem;">Student & Administrator Access</small>
                    </div>
                    <div class="card-body p-4">
                        <?php include __DIR__ . '/../partials/flash_messages.php'; ?>

                        <form method="POST" action="<?= BASE_URL ?>/login">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                            <div class="mb-3">
                                <label for="login_identifier" class="form-label fw-semibold">Gmail Address or Username</label>
                                <input type="text" class="form-control" id="login_identifier" name="login_identifier" 
                                       placeholder="e.g. juandelacruz@gmail.com or admin" required autofocus>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="password" class="form-label fw-semibold">Password</label>
                                    <a href="<?= BASE_URL ?>/student/forgot-password" class="small text-muted text-decoration-none">Forgot?</a>
                                </div>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="••••••••" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>

                            <div class="text-center">
                                <span class="small text-muted">Don't have an account?</span>
                                <a href="<?= BASE_URL ?>/student/register" class="small text-primary text-decoration-none fw-semibold ms-1">Register here</a>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer bg-light text-center py-2 border-0">
                        <small class="text-muted" style="font-size: 0.75rem;">Quirino State University &bull; OSAS Portal</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container text-center">
            <p class="mb-0 text-muted small">Quirino State University &bull; Office of Student Affairs and Services (OSAS)</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
