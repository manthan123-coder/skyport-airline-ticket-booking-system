<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if (!empty($email)) {
        $_SESSION['user_email'] = $email;
        header('Location: mybooking.php');
        exit();
    } else {
        $message = 'Please enter a valid email address.';
    }
}

include 'include/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 p-4">
                <div class="text-center mb-4">
                    <span class="fs-1 text-primary">✈</span>
                    <h3 class="fw-bold mt-2">Welcome to SkyPort</h3>
                    <p class="text-muted small">Log in to manage your flight bookings and check-in</p>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-danger py-2 small"><?= htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control py-2" placeholder="name@example.com" required value="<?= htmlspecialchars($_SESSION['user_email'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control py-2" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" checked>
                            <label class="form-check-label small" for="remember">Remember me</label>
                        </div>
                        <a href="#" class="small text-decoration-none">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold py-2 shadow-sm">
                        Log In <i class="bi bi-box-arrow-in-right ms-1"></i>
                    </button>
                </form>

                <hr class="my-4 opacity-25">
                <div class="text-center">
                    <small class="text-muted">Don't have an account? <a href="#" class="fw-bold text-decoration-none">Sign up</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>
