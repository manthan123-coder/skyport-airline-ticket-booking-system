<?php
require_once __DIR__ . '/auth.php';

$already_logged_in = !empty($_SESSION['user_id']) || !empty($_SESSION['user_email']);
$user_display_name = $_SESSION['user_name'] ?? $_SESSION['user_email'] ?? 'User';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '') {
        $error = 'Please enter your full name.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (find_user($email)) {
        $error = 'An account already exists with this email address.';
    } else {
        $users = all_users();
        $user = [
            'id' => bin2hex(random_bytes(8)),
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'created_at' => date('c')
        ];
        $users[] = $user;
        save_users($users);
        sign_in_user($user);

        $redirect = $_SESSION['post_login_redirect'] ?? 'mybooking.php';
        unset($_SESSION['post_login_redirect']);
        header('Location: ' . $redirect);
        exit;
    }
}

include 'include/header.php';
?>

<style>
    .sky-auth {
        min-height: calc(100vh - 120px);
        padding: 50px 0;
        background: #f4f8fd;
        background-image: 
            radial-gradient(circle at 10% 10%, #dcecff 0, transparent 35%),
            radial-gradient(circle at 90% 90%, #d4e5ff 0, transparent 35%);
    }

    .sky-auth-card {
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 24px 70px rgba(12, 48, 95, 0.14);
        background: #ffffff;
    }

    .sky-auth-visual {
        min-height: 680px;
        padding: 48px;
        color: #ffffff;
        background: linear-gradient(145deg, #051a3d 0%, #073b82 55%, #1369db 100%);
        position: relative;
        overflow: hidden;
    }

    .sky-auth-visual::after {
        content: '';
        width: 440px;
        height: 440px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 50%;
        position: absolute;
        right: -170px;
        bottom: -180px;
        box-shadow: 
            0 0 0 60px rgba(255, 255, 255, 0.04),
            0 0 0 120px rgba(255, 255, 255, 0.03);
        pointer-events: none;
    }

    .sky-kicker {
        font-size: 0.75rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        font-weight: 700;
        color: #a3ccff;
    }

    .benefit-card {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 16px;
        backdrop-filter: blur(10px);
        padding: 20px;
        margin-top: 30px;
    }

    .benefit-item {
        display: flex;
        align-items: center;
        gap: 14px;
        color: #e2eeff;
        font-size: 0.93rem;
        margin-bottom: 14px;
    }

    .benefit-item:last-child {
        margin-bottom: 0;
    }

    .benefit-icon-wrapper {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #79b5ff;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .sky-auth-form {
        padding: 48px 52px;
        background: #ffffff;
    }

    .sky-auth-form .form-label {
        font-size: 0.78rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #495057;
    }

    .sky-auth-form .form-control, 
    .sky-auth-form .input-group-text {
        border-color: #dbe3ef;
        min-height: 50px;
        font-size: 0.95rem;
    }

    .sky-auth-form .form-control:focus {
        border-color: #1369db;
        box-shadow: 0 0 0 0.25rem rgba(19, 105, 219, 0.14);
    }

    .sky-auth-form .input-group-text {
        color: #6c757d;
        background-color: #f8fafc;
    }

    .sky-register-btn {
        min-height: 52px;
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        border: 0;
        box-shadow: 0 10px 24px rgba(13, 110, 253, 0.28);
        font-size: 1rem;
        transition: all 0.25s ease;
    }

    .sky-register-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(13, 110, 253, 0.35);
        background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
    }

    /* Password strength meter */
    .strength-meter-bar {
        height: 6px;
        border-radius: 4px;
        background-color: #e9ecef;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .strength-meter-fill {
        height: 100%;
        width: 0%;
        border-radius: 4px;
        transition: width 0.3s ease, background-color 0.3s ease;
    }

    @media (max-width: 991.98px) {
        .sky-auth-visual {
            min-height: auto;
            padding: 36px 30px;
        }
        .sky-auth-form {
            padding: 36px 28px;
        }
    }
</style>

<section class="sky-auth d-flex align-items-center">
    <div class="container py-3">
        <div class="card sky-auth-card border-0 mx-auto" style="max-width: 1060px;">
            <div class="row g-0">
                <!-- Left Visual Banner -->
                <div class="col-lg-5 col-xl-6">
                    <div class="sky-auth-visual d-flex flex-column h-100">
                        <div>
                            <div class="d-flex align-items-center gap-2 fw-bold fs-4">
                                <span class="bg-white text-primary rounded-3 p-2 lh-1 shadow-sm">
                                    <i class="bi bi-airplane-engines"></i>
                                </span> 
                                SkyPort
                            </div>
                            <div class="sky-kicker mt-4">JOIN SKYPORT CLUB</div>
                            <h1 class="fw-bold display-6 mt-2">Unlock effortless flight bookings & perks.</h1>
                            <p class="text-white-50 mb-0">Create your free account today and experience stress-free travel management at your fingertips.</p>
                        </div>

                        <div class="benefit-card">
                            <div class="small fw-bold text-white-50 text-uppercase mb-3">MEMBER ADVANTAGES</div>
                            
                            <div class="benefit-item">
                                <div class="benefit-icon-wrapper">
                                    <i class="bi bi-lightning-charge-fill"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-white">Instant Flight Check-in</strong>
                                    <span class="small text-white-50">Generate digital boarding passes in seconds</span>
                                </div>
                            </div>

                            <div class="benefit-item">
                                <div class="benefit-icon-wrapper">
                                    <i class="bi bi-journal-bookmark-fill"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-white">Centralized Travel Hub</strong>
                                    <span class="small text-white-50">Keep all ticket itineraries saved & organized</span>
                                </div>
                            </div>

                            <div class="benefit-item">
                                <div class="benefit-icon-wrapper">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-white">Secure Encrypted Account</strong>
                                    <span class="small text-white-50">Industry standard safety for all your bookings</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-4 border-top border-light border-opacity-25 d-flex align-items-center justify-content-between">
                            <span class="small text-white-50"><i class="bi bi-people me-1"></i> Over 50,000+ Happy Travelers</span>
                            <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 px-3 py-2 rounded-pill small">SkyPort Frequent Flyer</span>
                        </div>
                    </div>
                </div>

                <!-- Right Registration Form -->
                <div class="col-lg-7 col-xl-6">
                    <div class="sky-auth-form h-100 d-flex flex-column justify-content-center">
                        <div class="mb-4">
                            <div class="sky-kicker text-primary">NEW ACCOUNT</div>
                            <h2 class="fw-bold mt-2 mb-1">Create your SkyPort Account</h2>
                            <p class="text-muted mb-0">Fill in the information below to get started.</p>
                        </div>

                        <?php if ($already_logged_in): ?>
                            <div class="alert alert-info border-0 shadow-sm small py-3 d-flex align-items-center justify-content-between mb-4" role="alert">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-check-fill fs-5 text-primary"></i>
                                    <div>Active session as <strong><?= htmlspecialchars($user_display_name); ?></strong></div>
                                </div>
                                <a href="mybooking.php" class="btn btn-sm btn-primary rounded-pill px-3 text-nowrap ms-2 fw-bold">My Trips <i class="bi bi-arrow-right"></i></a>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger border-0 shadow-sm small py-3 d-flex align-items-center gap-2 mb-4" role="alert">
                                <i class="bi bi-exclamation-circle-fill fs-5 text-danger flex-shrink-0"></i>
                                <div><?= htmlspecialchars($error); ?></div>
                            </div>
                        <?php endif; ?>

                        <form method="post" id="registerForm" novalidate>
                            <!-- Full Name -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" 
                                           name="name" 
                                           class="form-control" 
                                           placeholder="e.g. Rahul Sharma" 
                                           required 
                                           value="<?= htmlspecialchars($_POST['name'] ?? ''); ?>">
                                </div>
                            </div>

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" 
                                           name="email" 
                                           class="form-control" 
                                           placeholder="name@example.com" 
                                           required 
                                           value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>">
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" 
                                           id="reg_password" 
                                           name="password" 
                                           class="form-control" 
                                           placeholder="At least 8 characters" 
                                           minlength="8" 
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            aria-label="Toggle Password Visibility" 
                                            onclick="toggleVisibility('reg_password', 'togglePassIcon1')">
                                        <i class="bi bi-eye" id="togglePassIcon1"></i>
                                    </button>
                                </div>
                                <!-- Strength Meter -->
                                <div class="mt-2" id="strengthMeterContainer" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted" style="font-size: 0.75rem;">Password Strength:</small>
                                        <small id="strengthText" class="fw-bold" style="font-size: 0.75rem; color: #6c757d;">Weak</small>
                                    </div>
                                    <div class="strength-meter-bar">
                                        <div id="strengthMeterFill" class="strength-meter-fill"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                    <input type="password" 
                                           id="reg_confirm_password" 
                                           name="confirm_password" 
                                           class="form-control" 
                                           placeholder="Re-enter your password" 
                                           minlength="8" 
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            aria-label="Toggle Password Visibility" 
                                            onclick="toggleVisibility('reg_confirm_password', 'togglePassIcon2')">
                                        <i class="bi bi-eye" id="togglePassIcon2"></i>
                                    </button>
                                </div>
                                <div id="matchFeedback" class="mt-1 small" style="display: none; font-size: 0.78rem;"></div>
                            </div>

                            <!-- Terms & Privacy -->
                            <div class="form-check my-4">
                                <input class="form-check-input" type="checkbox" id="termsCheck" required checked>
                                <label class="form-check-label text-muted small" for="termsCheck">
                                    I agree to SkyPort's <a href="#" class="text-decoration-none text-primary">Terms of Service</a> and <a href="#" class="text-decoration-none text-primary">Privacy Policy</a>.
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary sky-register-btn w-100 rounded-3 fw-bold">
                                Create Account <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </form>

                        <div class="text-center border-top mt-4 pt-4">
                            <span class="text-muted small">Already have an account?</span> 
                            <a href="login.php" class="fw-bold text-decoration-none ms-1">Log in here</a>
                        </div>

                        <p class="text-center text-muted small mt-3 mb-0">
                            <i class="bi bi-shield-check text-success me-1"></i>Your information is encrypted with bank-grade security.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function toggleVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input || !icon) return;
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const passInput = document.getElementById('reg_password');
    const confirmInput = document.getElementById('reg_confirm_password');
    const strengthMeterContainer = document.getElementById('strengthMeterContainer');
    const strengthFill = document.getElementById('strengthMeterFill');
    const strengthText = document.getElementById('strengthText');
    const matchFeedback = document.getElementById('matchFeedback');

    if (passInput) {
        passInput.addEventListener('input', function() {
            const val = passInput.value;
            if (val.length > 0) {
                strengthMeterContainer.style.display = 'block';
            } else {
                strengthMeterContainer.style.display = 'none';
            }

            let score = 0;
            if (val.length >= 8) score += 1;
            if (/[A-Z]/.test(val)) score += 1;
            if (/[0-9]/.test(val)) score += 1;
            if (/[^A-Za-z0-9]/.test(val)) score += 1;

            let percentage = (val.length === 0) ? 0 : (score / 4) * 100;
            if (val.length > 0 && val.length < 8) percentage = Math.min(percentage, 25);

            strengthFill.style.width = percentage + '%';

            if (val.length < 8) {
                strengthFill.style.backgroundColor = '#dc3545'; // Red
                strengthText.textContent = 'Too Short (Min 8 chars)';
                strengthText.style.color = '#dc3545';
            } else if (score <= 1) {
                strengthFill.style.backgroundColor = '#ffc107'; // Yellow
                strengthText.textContent = 'Weak';
                strengthText.style.color = '#ffc107';
            } else if (score === 2 || score === 3) {
                strengthFill.style.backgroundColor = '#0dcaf0'; // Cyan/Blue
                strengthText.textContent = 'Medium';
                strengthText.style.color = '#0dcaf0';
            } else {
                strengthFill.style.backgroundColor = '#198754'; // Green
                strengthText.textContent = 'Strong';
                strengthText.style.color = '#198754';
            }

            checkMatch();
        });
    }

    if (confirmInput) {
        confirmInput.addEventListener('input', checkMatch);
    }

    function checkMatch() {
        if (!confirmInput || !matchFeedback) return;
        const pass = passInput ? passInput.value : '';
        const confirm = confirmInput.value;

        if (confirm.length === 0) {
            matchFeedback.style.display = 'none';
            return;
        }

        matchFeedback.style.display = 'block';
        if (pass === confirm) {
            matchFeedback.textContent = '✓ Passwords match';
            matchFeedback.className = 'mt-1 small text-success fw-semibold';
        } else {
            matchFeedback.textContent = '✗ Passwords do not match';
            matchFeedback.className = 'mt-1 small text-danger fw-semibold';
        }
    }
});
</script>

<?php include 'include/footer.php'; ?>
