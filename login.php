<?php
require_once __DIR__ . '/auth.php';
$error = ''; $success = $_SESSION['flash_success'] ?? ''; unset($_SESSION['flash_success']);

// City to Airport Code Mapping
$city_codes = [
    'Delhi' => 'DEL', 'Mumbai' => 'BOM', 'Bangalore' => 'BLR', 'Hyderabad' => 'HYD',
    'Chennai' => 'MAA', 'Kolkata' => 'CCU', 'Ahmedabad' => 'AMD', 'Pune' => 'PNQ',
    'Goa' => 'GOX', 'Jaipur' => 'JAI', 'Lucknow' => 'LKO', 'Patna' => 'PAT',
    'Surat' => 'STV', 'Rajkot' => 'RAJ', 'Vadodara' => 'BDQ', 'Indore' => 'IDR',
    'Bhopal' => 'BHO', 'Nagpur' => 'NAG', 'Chandigarh' => 'IXC', 'Jammu' => 'IXJ',
    'Srinagar' => 'SXR', 'Amritsar' => 'ATQ', 'Varanasi' => 'VNS', 'Ranchi' => 'IXR',
    'Bhubaneswar' => 'BBI', 'Guwahati' => 'GAU', 'Kochi' => 'COK', 'Coimbatore' => 'CJB',
    'Visakhapatnam' => 'VTZ', 'Thiruvananthapuram' => 'TRV'
];

$selected_from_city = !empty($_SESSION['from_city']) ? $_SESSION['from_city'] : 'Ahmedabad';
$selected_to_city = !empty($_SESSION['to_city']) ? $_SESSION['to_city'] : 'Delhi';

$from_code = $city_codes[$selected_from_city] ?? strtoupper(substr($selected_from_city, 0, 3));
$to_code = $city_codes[$selected_to_city] ?? strtoupper(substr($selected_to_city, 0, 3));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? '')); $password = $_POST['password'] ?? '';
    $user = find_user($email);
    if (!$user || !password_verify($password, $user['password'])) $error = 'Incorrect email or password.';
    else {
        sign_in_user($user);
        $redirect = $_SESSION['post_login_redirect'] ?? 'index.php';
        unset($_SESSION['post_login_redirect']);
        header('Location: ' . $redirect);
        exit;
    }
}
include 'include/header.php';
?>
<style>
    .sky-auth { min-height: calc(100vh - 150px); padding: 54px 0; background: #f3f7fd; background-image: radial-gradient(circle at 5% 5%, #dcecff 0, transparent 29%), radial-gradient(circle at 92% 95%, #d9e8ff 0, transparent 25%); }
    .sky-auth-card { border-radius: 24px; overflow: hidden; box-shadow: 0 24px 70px rgba(12, 48, 95, .16); }
    .sky-auth-visual { min-height: 630px; padding: 48px; color: #fff; background: linear-gradient(150deg, #061f47 0%, #073d89 55%, #1673e6 100%); position: relative; overflow: hidden; }
    .sky-auth-visual:after { content: ''; width: 420px; height: 420px; border: 1px solid rgba(255,255,255,.24); border-radius: 50%; position: absolute; right: -165px; bottom: -175px; box-shadow: 0 0 0 55px rgba(255,255,255,.05), 0 0 0 110px rgba(255,255,255,.04); }
    .sky-auth-form { padding: 52px 54px; background: #fff; }
    .sky-kicker { font-size: .73rem; letter-spacing: .15em; text-transform: uppercase; font-weight: 700; color: #b9d6ff; }
    .route-board { position: relative; z-index: 1; margin-top: 76px; padding: 24px; background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.22); border-radius: 18px; backdrop-filter: blur(6px); }
    .route-line { height: 2px; flex: 1; background: rgba(255,255,255,.6); position: relative; }
    .route-line i { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background:#196fda; padding:0 7px; }
    .benefit { display:flex; align-items:center; gap:11px; color:#dcecff; font-size:.9rem; margin-top:17px; }
    .benefit i { color:#8cc3ff; font-size:1.1rem; }
    .sky-auth-form .form-control, .sky-auth-form .input-group-text { border-color:#dbe3ef; min-height:52px; }
    .sky-auth-form .form-control:focus { border-color:#1769d3; box-shadow:0 0 0 .2rem rgba(23,105,211,.12); }
    .sky-signin { min-height:54px; background:#0864d9; border:0; box-shadow:0 10px 20px rgba(8,100,217,.24); }
    @media(max-width:991px) { .sky-auth-visual { min-height:auto; padding:34px; } .route-board { margin-top:30px; } .sky-auth-form { padding:38px 30px; } }
</style>
<section class="sky-auth d-flex align-items-center">
    <div class="container"><div class="card sky-auth-card border-0 mx-auto" style="max-width:1060px;"><div class="row g-0">
        <div class="col-lg-6"><div class="sky-auth-visual d-flex flex-column">
            <div><div class="d-flex align-items-center gap-2 fw-bold fs-4"><span class="bg-white text-primary rounded-3 p-2 lh-1"><i class="bi bi-airplane-engines"></i></span> SkyPort</div><div class="sky-kicker mt-4">Your journey, your account</div><h1 class="fw-bold display-6 mt-2">Travel feels better when everything is in one place.</h1><p class="text-white-50 mb-0">Manage bookings, check in faster, and keep your travel details ready for take-off.</p></div>
            <div class="route-board">
                <div class="small text-white-50 mb-3">YOUR SELECTED JOURNEY</div>
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <div class="fw-bold fs-4"><?= htmlspecialchars($from_code); ?></div>
                        <small class="text-white-50"><?= htmlspecialchars($selected_from_city); ?></small>
                    </div>
                    <div class="route-line"><i class="bi bi-airplane-fill"></i></div>
                    <div class="text-end">
                        <div class="fw-bold fs-4"><?= htmlspecialchars($to_code); ?></div>
                        <small class="text-white-50"><?= htmlspecialchars($selected_to_city); ?></small>
                    </div>
                </div>
                <div class="border-top border-light border-opacity-25 mt-3 pt-3 small d-flex justify-content-between">
                    <span><i class="bi bi-calendar3 me-1"></i> Ready when you are</span>
                    <span>SkyPort Club</span>
                </div>
            </div>
            <div class="mt-auto pt-4"><div class="benefit"><i class="bi bi-check2-circle"></i><span>Manage bookings and travel details</span></div><div class="benefit"><i class="bi bi-check2-circle"></i><span>Quick access to web check-in</span></div><div class="benefit"><i class="bi bi-check2-circle"></i><span>Secure, personalised account</span></div></div>
        </div></div>
        <div class="col-lg-6"><div class="sky-auth-form h-100 d-flex flex-column justify-content-center"><div class="mb-4"><div class="sky-kicker text-primary">SkyPort account</div><h2 class="fw-bold mt-2 mb-2">Welcome back</h2><p class="text-muted mb-0">Sign in to continue your journey.</p></div>
            <?php if ($success): ?><div class="alert alert-primary border-0 small py-3"><i class="bi bi-info-circle me-1"></i><?= htmlspecialchars($success); ?></div><?php endif; ?><?php if ($error): ?><div class="alert alert-danger border-0 small py-3"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error); ?></div><?php endif; ?>
            <form method="post"><div class="mb-3"><label class="form-label fw-semibold small">EMAIL ADDRESS</label><div class="input-group"><span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span><input type="email" name="email" class="form-control" placeholder="name@example.com" required value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"></div></div><div class="mb-2"><div class="d-flex justify-content-between"><label class="form-label fw-semibold small">PASSWORD</label><a href="forgot_password.php" class="small fw-semibold text-decoration-none">Forgot password?</a></div><div class="input-group"><span class="input-group-text bg-white"><i class="bi bi-lock"></i></span><input id="password" type="password" name="password" class="form-control" required><button class="btn btn-outline-secondary" type="button" aria-label="Show password" onclick="password.type=password.type==='password'?'text':'password'"><i class="bi bi-eye"></i></button></div></div><div class="form-check my-4"><input class="form-check-input" type="checkbox" id="remember" checked><label class="form-check-label small text-muted" for="remember">Keep me signed in on this device</label></div><button class="btn btn-primary sky-signin w-100 rounded-3 fw-bold">Sign in securely <i class="bi bi-arrow-right ms-1"></i></button></form><div class="text-center border-top mt-4 pt-4"><span class="text-muted small">New to SkyPort?</span> <a href="register.php" class="fw-bold text-decoration-none">Create an account</a></div><p class="text-center text-muted small mt-4 mb-0"><i class="bi bi-shield-check me-1"></i>Your information is protected with secure encryption.</p></div></div>
    </div></div></div>
</section>
<?php include 'include/footer.php'; ?>
