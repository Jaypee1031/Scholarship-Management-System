<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>/">
                <img src="<?= BASE_URL ?>/public/assets/img/qsulogo.png" alt="QSU Logo" class="me-2">
                <?= APP_NAME ?>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= BASE_URL ?>/login"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
            </div>
        </div>
    </nav>

    <div class="container py-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>
                            Student Registration
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <?php include __DIR__ . '/../partials/flash_messages.php'; ?>

                        <form method="POST" action="<?= BASE_URL ?>/student/register">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Student ID Number <span class="text-danger">*</span></label>
                                <input type="text" name="student_id" class="form-control" placeholder="e.g. QSU-2025-001" required autofocus>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Middle Name</label>
                                    <input type="text" name="middle_name" class="form-control">
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Degree Program <span class="text-danger">*</span></label>
                                    <input type="text" name="degree_program" class="form-control" placeholder="e.g. BS in Information Technology" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Year Level <span class="text-danger">*</span></label>
                                    <select name="year_level" class="form-select" required>
                                        <option value="1">1st Year</option>
                                        <option value="2">2nd Year</option>
                                        <option value="3">3rd Year</option>
                                        <option value="4">4th Year</option>
                                        <option value="5">5th Year</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="student@qsu.edu.ph" required>
                            </div>

                            <div class="row g-2 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" minlength="8" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" name="confirm_password" class="form-control" minlength="8" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3">
                                <i class="fas fa-user-plus me-2"></i>Create Student Account
                            </button>

                            <div class="text-center">
                                <span class="small text-muted">Already registered?</span>
                                <a href="<?= BASE_URL ?>/student/login" class="small text-primary text-decoration-none fw-semibold ms-1">Login here</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer mt-auto">
        <div class="container text-center">
            <p class="mb-0 text-muted small">Quirino State University &bull; Office of Student Affairs and Services (OSAS)</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
