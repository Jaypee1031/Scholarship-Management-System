<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
    <div class="container" style="max-width: 440px;">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 text-center">
                <div class="qsu-logo-circle mx-auto mb-3 bg-success text-white">QSU</div>
                <h5 class="fw-bold mb-2">Forgot Your Password?</h5>
                <p class="text-muted small mb-4">For security reasons, password resets are processed directly by the QSU OSAS / Scholarship Office.</p>

                <div class="alert alert-info text-start small">
                    <i class="fas fa-info-circle me-1"></i> Please visit the OSAS office with your valid Student ID, or ask your Scholarship Coordinator to reset your password.
                </div>

                <form action="<?= BASE_URL ?>/student/forgot-password" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <button type="submit" class="btn btn-outline-success w-100 py-2 mb-2">Request Assistance Info</button>
                    <a href="<?= BASE_URL ?>/login" class="btn btn-link text-decoration-none small text-muted">Return to Login</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
