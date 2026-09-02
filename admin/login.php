<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';

// Check if logout parameter passed
if (isset($_GET['logout'])) {
    unset($_SESSION['is_admin_logged_in']);
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);
}

$already_logged_in = !empty($_SESSION['is_admin_logged_in']);
$admin_display_name = $_SESSION['admin_name'] ?? 'Administrator';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_input = trim($_POST['email'] ?? '');
    $password_input = $_POST['password'] ?? '';

    if (empty($email_input) || empty($password_input)) {
        $error = "Please enter both Admin Username/Email and Password.";
    } else {
        $login_success = false;
        $admin_id = null;
        $admin_name = 'Administrator';

        // 1. Check MySQL 'admin' table safely
        try {
            if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
                $stmt = @$conn->prepare("SELECT * FROM admin WHERE email = ?");
                if ($stmt) {
                    $stmt->bind_param("s", $email_input);
                    if ($stmt->execute()) {
                        $res = $stmt->get_result();
                        if ($res && $res->num_rows > 0) {
                            while ($row = $res->fetch_assoc()) {
                                $dbPass = $row['password'] ?? '';
                                $pass_ok = false;
                                if (preg_match('/^[a-f0-9]{32}$/i', $dbPass)) {
                                    if (md5($password_input) === $dbPass) $pass_ok = true;
                                } else {
                                    if (password_verify($password_input, $dbPass) || $password_input === $dbPass) $pass_ok = true;
                                }

                                if ($pass_ok) {
                                    $login_success = true;
                                    $admin_id = $row['id'] ?? 1;
                                    $admin_name = $row['name'] ?? $row['username'] ?? $row['email'] ?? 'Administrator';
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            // MySQL error caught safely without crashing
        }

        // 2. Check JSON users with role 'Admin' or 'Staff'
        if (!$login_success) {
            $users = all_users();
            foreach ($users as $u) {
                $u_email = strtolower($u['email'] ?? '');
                $u_role = strtolower($u['role'] ?? 'user');
                if (($u_role === 'admin' || $u_role === 'staff') && (strcasecmp($u_email, $email_input) === 0 || strcasecmp($u['name'] ?? '', $email_input) === 0)) {
                    if (password_verify($password_input, $u['password'] ?? '') || $password_input === ($u['password'] ?? '')) {
                        $login_success = true;
                        $admin_id = $u['id'] ?? 1;
                        $admin_name = $u['name'] ?? 'Admin';
                        break;
                    }
                }
            }
        }

        // 3. Fallback Credentials (admin@skyport.com / admin123 or admin / admin123 or admin / admin)
        if (!$login_success) {
            $valid_defaults = [
                'admin@skyport.com' => ['pass' => 'admin123', 'name' => 'System Admin'],
                'admin'             => ['pass' => 'admin123', 'name' => 'Administrator'],
                'skyport'           => ['pass' => 'admin123', 'name' => 'SkyPort Manager']
            ];

            $lowered = strtolower($email_input);
            if (isset($valid_defaults[$lowered])) {
                if ($password_input === $valid_defaults[$lowered]['pass'] || $password_input === 'admin') {
                    $login_success = true;
                    $admin_id = 999;
                    $admin_name = $valid_defaults[$lowered]['name'];
                }
            } else if ($lowered === 'admin' && ($password_input === 'admin' || $password_input === 'admin123')) {
                $login_success = true;
                $admin_id = 999;
                $admin_name = 'Administrator';
            }
        }

        if ($login_success) {
            $_SESSION['is_admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin_id;
            $_SESSION['admin_name'] = $admin_name;
            $_SESSION['admin_email'] = $email_input;
            header("Location: index.php");
            exit;
        } else {
            $error = "Incorrect Admin Username or Password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SkyPort Admin — Control Tower Portal</title>

    <!-- Google Fonts & Bootstrap Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --sky-primary: #0d6efd;
            --sky-accent: #00f2fe;
            --sky-bg-dark: #0067f8ff;
        }

        body.admin-login-page {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--sky-bg-dark);
            background-image: 
                linear-gradient(135deg, rgba(4, 13, 26, 0.88), rgba(9, 30, 62, 0.92)), 
                url('../images/admin_bg.png'),
                url('../aeroplane.jpg');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            color: #ffffff;
        }

        /* Animated flight path radar lines in background */
        .flight-grid-overlay {
            position: absolute;
            inset: 0;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(0, 0, 0, 0.15) 0, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(0, 242, 254, 0.12) 0, transparent 40%),
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 60px 60px, 60px 60px;
            pointer-events: none;
        }

        /* Telemetry HUD Badges */
        .hud-badge {
            position: absolute;
            padding: 8px 16px;
            background: rgba(10, 25, 50, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            backdrop-filter: blur(12px);
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            color: #a3ccff;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }

        .hud-top-left { top: 28px; left: 32px; }
        .hud-top-right { top: 28px; right: 32px; }
        .hud-bottom-left { bottom: 28px; left: 32px; }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #00ff88;
            border-radius: 50%;
            box-shadow: 0 0 10px #00ff88;
            animation: pulse-ring 1.8s infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 255, 136, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(0, 255, 136, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 255, 136, 0); }
        }

        /* Glassmorphism Auth Card */
        .card-admin-glass {
            background: rgba(8, 23, 46, 0.76);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 28px;
            box-shadow: 0 30px 90px rgba(0, 0, 0, 0.65), 0 0 40px rgba(13, 110, 253, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 460px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-admin-header {
            padding: 38px 36px 20px;
            text-align: center;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.05) 0%, transparent 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .logo-icon-badge {
            width: 68px;
            height: 68px;
            border-radius: 20px;
            background: linear-gradient(135deg, #0d6efd 0%, #00d2ff 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 30px rgba(13, 110, 253, 0.45);
            margin-bottom: 16px;
            font-size: 2rem;
            color: #ffffff;
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
        }

        .card-admin-glass:hover .logo-icon-badge {
            transform: rotate(0deg) scale(1.05);
        }

        .admin-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.65rem;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .card-admin-body {
            padding: 36px;
        }

        .form-label {
            font-size: 0.76rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 700;
            color: #a3ccff;
            margin-bottom: 8px;
        }

        .input-group-text {
            background-color: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-end: none;
            color: #79b5ff;
            min-height: 52px;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-start: none;
            min-height: 52px;
            color: #ffffff;
            font-size: 0.95rem;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            color: #ffffff;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.35);
        }

        .btn-toggle-pass {
            background-color: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-start: none;
            color: #a3ccff;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .btn-toggle-pass:hover {
            background-color: rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }

        .btn-admin-submit {
            min-height: 54px;
            background: linear-gradient(135deg, #0d6efd 0%, #00b4d8 100%);
            border: 0;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.04em;
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.35);
            transition: all 0.3s ease;
        }

        .btn-admin-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 36px rgba(13, 110, 253, 0.48);
            background: linear-gradient(135deg, #0b5ed7 0%, #0077b6 100%);
        }

        /* Demo helper pills */
        .demo-pill {
            background: rgba(255, 255, 255, 0.08);
            border: 1px dashed rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.78rem;
            color: #b9d6ff;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .demo-pill:hover {
            background: rgba(13, 110, 253, 0.25);
            border-color: #0d6efd;
            color: #ffffff;
        }

        @media (max-width: 768px) {
            .hud-badge { display: none; }
            .card-admin-header { padding: 28px 24px 16px; }
            .card-admin-body { padding: 24px; }
        }
    </style>
</head>
<body class="admin-login-page">

<div class="flight-grid-overlay"></div>

<!-- HUD Telemetry Badges (Desktop) -->
<div class="hud-badge hud-top-left">
    <span class="pulse-dot"></span>
    <span>SKYPORT RADAR SYSTEM — ONLINE</span>
</div>

<div class="hud-badge hud-top-right">
    <i class="bi bi-airplane-fill text-info"></i>
    <span>CONTROL TOWER V4.2</span>
</div>

<div class="hud-badge hud-bottom-left">
    <i class="bi bi-shield-lock-fill text-warning"></i>
    <span>SSL 256-BIT ENCRYPTED PORTAL</span>
</div>

<!-- Main Admin Login Glass Card -->
<div class="card card-admin-glass m-3">
    <div class="card-admin-header">
        <div class="logo-icon-badge">
            <i class="bi bi-airplane-engines"></i>
        </div>
        <h2 class="admin-title text-white">SkyPort Admin</h2>
        <p class="text-white-50 small mb-0">Airline Management & Control Panel</p>
    </div>

    <!-- <div class="card-admin-body">
        <?php if ($already_logged_in): ?>
            <div class="alert alert-info border-0 small py-3 mb-4 rounded-3 d-flex align-items-center justify-content-between bg-primary bg-opacity-25 text-white" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-person-check-fill fs-5 text-info"></i>
                    <div>Active session as <strong><?= htmlspecialchars($admin_display_name); ?></strong></div>
                </div>
                <a href="index.php" class="btn btn-sm btn-primary rounded-pill px-3 py-1 text-nowrap ms-2 fw-bold">Dashboard <i class="bi bi-arrow-right"></i></a>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger border-0 small py-3 mb-4 rounded-3 d-flex align-items-center bg-danger bg-opacity-25 text-white" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-warning"></i>
                <div><?= htmlspecialchars($error); ?></div>
            </div>
        <?php endif; ?> -->

        <form method="POST">
            <!-- Email / Username Input -->
            <div class="mb-3">
                <label class="form-label">ADMIN USERNAME / EMAIL</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                    <input type="text" 
                           id="adminEmail"
                           name="email" 
                           class="form-control" 
                           placeholder="admin@skyport.com or admin" 
                           required 
                           value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
            </div>

            <!-- Password Input -->
            <div class="mb-4">
                <label class="form-label">PASSWORD</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                    <input type="password" 
                           name="password" 
                           id="adminPass" 
                           class="form-control" 
                           placeholder="••••••••" 
                           required>
                    <button class="btn btn-toggle-pass" 
                            type="button" 
                            aria-label="Toggle Password Visibility"
                            onclick="toggleAdminPass()">
                        <i class="bi bi-eye" id="adminPassIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Quick Credential Helper Pills -->
            <!-- <div class="mb-4">
                <div class="small text-white-50 mb-2" style="font-size: 0.73rem;">QUICK DEMO CREDENTIALS:</div>
                <div class="d-flex gap-2">
                    <div class="demo-pill flex-fill text-center" onclick="fillCreds('admin@skyport.com', 'admin123')">
                        <i class="bi bi-key me-1"></i> admin@skyport.com
                    </div>
                    <div class="demo-pill flex-fill text-center" onclick="fillCreds('admin', 'admin123')">
                        <i class="bi bi-person me-1"></i> admin / admin123
                    </div>
                </div>
            </div> -->

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-admin-submit w-100 text-uppercase">
                Login To Deshboard <i class="bi bi-arrow-right-short fs-4 ms-1 align-middle"></i>
            </button>
        </form>

        <div class="mt-4 pt-3 border-top border-white border-opacity-10 text-center">
            <a href="../index.php" class="small text-decoration-none text-white-50 hover-white">
                <i class="bi bi-arrow-left me-1"></i> Back to SkyPort Main Website
            </a>
        </div>
    </div>
</div>

<!-- JavaScript Controls -->
<script>
function toggleAdminPass() {
    const input = document.getElementById('adminPass');
    const icon = document.getElementById('adminPassIcon');
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

function fillCreds(email, pass) {
    const emailField = document.getElementById('adminEmail');
    const passField = document.getElementById('adminPass');
    if (emailField) emailField.value = email;
    if (passField) passField.value = pass;
}
</script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
