<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 Not Found - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
    <div class="text-center p-5 bg-white rounded shadow-sm" style="max-width: 500px;">
        <i class="fas fa-compass fa-4x text-success mb-3"></i>
        <h1 class="display-4 fw-bold text-success">404</h1>
        <h4 class="mb-3">Page Not Found</h4>
        <p class="text-muted mb-4">The requested URL or resource was not found on this system.</p>
        <a href="<?= BASE_URL ?>/" class="btn btn-success px-4 py-2"><i class="fas fa-home me-2"></i>Return Home</a>
    </div>
</body>
</html>
