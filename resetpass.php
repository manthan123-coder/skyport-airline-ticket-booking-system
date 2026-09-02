<?php
require_once 'auth.php';
if (empty($_SESSION['reset_email'])) { header('Location: forgot_password.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? ''; $confirm = $_POST['confirm_password'] ?? '';
    if (strlen($password) < 8) $error = 'Password must be at least 8 characters.';
    elseif ($password !== $confirm) $error = 'Passwords do not match.';
    else {
        $users = all_users();
        foreach ($users as &$user) if (strcasecmp($user['email'], $_SESSION['reset_email']) === 0) $user['password'] = password_hash($password, PASSWORD_DEFAULT);
        unset($user); save_users($users); unset($_SESSION['reset_email']);
        $_SESSION['flash_success'] = 'Password updated. Please log in.'; header('Location: login.php'); exit;
    }
}
include 'include/header.php';
?>
<div class="container py-5"><div class="row justify-content-center"><div class="col-md-6 col-lg-5"><div class="card border-0 shadow-lg rounded-4 p-4 p-md-5"><div class="text-center mb-4"><div class="d-inline-flex rounded-circle bg-primary-subtle text-primary p-3"><i class="bi bi-shield-lock fs-3"></i></div><h3 class="fw-bold mt-3">Choose a new password</h3></div><?php if ($error): ?><div class="alert alert-danger small"><?= htmlspecialchars($error); ?></div><?php endif; ?><form method="post"><div class="mb-3"><label class="form-label fw-semibold">New password</label><input type="password" class="form-control form-control-lg" name="password" minlength="8" required></div><div class="mb-4"><label class="form-label fw-semibold">Confirm password</label><input type="password" class="form-control form-control-lg" name="confirm_password" minlength="8" required></div><button class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">Update password</button></form></div></div></div></div>
<?php include 'include/footer.php'; ?>
