<?php
require_once __DIR__ . '/auth.php';
$message = ''; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $user = find_user($email);
    if (!$user) $error = 'We could not find an account with that email.';
    else { $_SESSION['reset_email'] = $user['email']; header('Location: resetpass.php'); exit; }
}
include 'include/header.php';
?>
<div class="container py-5"><div class="row justify-content-center"><div class="col-md-6 col-lg-5"><div class="card border-0 shadow-lg rounded-4 p-4 p-md-5"><div class="text-center mb-4"><div class="d-inline-flex rounded-circle bg-primary-subtle text-primary p-3"><i class="bi bi-key fs-3"></i></div><h3 class="fw-bold mt-3 mb-1">Reset your password</h3><p class="text-muted mb-0">Enter your account email to continue.</p></div><?php if ($error): ?><div class="alert alert-danger small"><?= htmlspecialchars($error); ?></div><?php endif; ?><form method="post"><label class="form-label fw-semibold">Email address</label><input type="email" class="form-control form-control-lg mb-4" name="email" required><button class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">Continue</button></form><p class="text-center mt-4 mb-0"><a class="text-decoration-none" href="login.php">Back to login</a></p></div></div></div></div>
<?php include 'include/footer.php'; ?>
