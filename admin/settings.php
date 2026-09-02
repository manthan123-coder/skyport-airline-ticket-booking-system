<?php
require_once __DIR__ . '/../include/db_json.php';
require_once __DIR__ . '/../auth.php';

$message = '';
$message_type = 'success';

$config = get_notification_config();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_settings') {
        $new_config = [
            'smtp_host'   => trim($_POST['smtp_host'] ?? 'smtp.gmail.com'),
            'smtp_port'   => intval($_POST['smtp_port'] ?? 587),
            'smtp_user'   => trim($_POST['smtp_user'] ?? ''),
            'smtp_pass'   => trim($_POST['smtp_pass'] ?? ''),
            'from_name'   => trim($_POST['from_name'] ?? 'SkyPort Airlines'),
            'sms_api_key' => trim($_POST['sms_api_key'] ?? '')
        ];

        save_notification_config($new_config);
        $config = $new_config;
        $message = "System notification and SMTP settings saved successfully!";
        $message_type = 'success';
    }

    if ($action === 'test_email') {
        $test_email = trim($_POST['test_email'] ?? '');
        if (!empty($test_email)) {
            $subject = "✈️ SkyPort System Test: SMTP Gateway Operational";
            
            $test_body_content = "
                <p style='margin-top: 0;'>Hello Administrator,</p>
                <p style='color: #475569;'>This is an automated system verification email sent from your <strong>SkyPort Airlines Admin Panel</strong> to test SMTP connectivity and HTML layout formatting.</p>
                
                <div class='route-card'>
                    <div style='font-size: 13px; font-weight: 700; color: #16a34a; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px;'>✓ Gateway Connection Active</div>
                    <div style='font-size: 22px; font-weight: 800; color: #0f172a; margin: 6px 0;'>
                        SMTP Gateway Test Successful
                    </div>
                    <div style='margin-top: 10px; font-size: 13px; color: #64748b; background: #ffffff; padding: 8px 16px; border-radius: 30px; display: inline-block; border: 1px solid #e2e8f0;'>
                        Server Host: <strong>" . htmlspecialchars($config['smtp_host'] ?? 'smtp.gmail.com') . "</strong> &bull; Port: <strong>" . intval($config['smtp_port'] ?? 587) . "</strong>
                    </div>
                </div>

                <table class='info-table'>
                    <tr><td class='label'>Recipient Address</td><td class='value'>" . htmlspecialchars($test_email) . "</td></tr>
                    <tr><td class='label'>Sender Profile Name</td><td class='value'>" . htmlspecialchars($config['from_name'] ?? 'SkyPort Airlines') . "</td></tr>
                    <tr><td class='label'>Dispatch Timestamp</td><td class='value'>" . date('d M Y, h:i:s A') . "</td></tr>
                    <tr><td class='label'>Gateway Test Result</td><td class='value'><span style='color: #16a34a; background: #dcfce7; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 700;'>Operational / Active</span></td></tr>
                </table>

                <div style='text-align: center; margin-top: 26px;'>
                    <a href='http://localhost/airport/project/admin/settings.php' class='btn-primary'>Return to Admin Panel Settings</a>
                </div>
            ";

            $body = build_modern_email_html([
                'title'        => "SkyPort Admin System Test Email",
                'subtitle'     => "SMTP Gateway Verification & Health Check",
                'badge_text'   => "SMTP STATUS: ONLINE",
                'badge_style'  => "background: #10b981; color: #ffffff;",
                'body_content' => $test_body_content,
                'footer_text'  => "SkyPort System Administration &bull; Network Health Diagnostics"
            ]);

            $sent = send_smtp_email($test_email, $subject, $body);
            if ($sent) {
                $message = "Test email sent successfully to <strong>" . htmlspecialchars($test_email) . "</strong>!";
                $message_type = 'success';
            } else {
                $message = "Test email attempted. Check your SMTP login credentials or server connectivity.";
                $message_type = 'warning';
            }
        }
    }
}

include 'header.php';
include 'sidebar.php';
?>

<!-- Main Content Area -->
<main class="app-main">

    <!-- Header Title -->
    <div class="app-content-header py-3 bg-body-secondary border-bottom mb-4">
        <div class="container-fluid px-4">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-gear text-secondary me-2"></i>Notification & System Settings</h3>
                    <small class="text-muted">Configure SMTP Email Gateway and Real-Time SMS API Credentials</small>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end mb-0">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="app-content">
        <div class="container-fluid px-4">

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $message_type; ?> alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i><?= $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                
                <!-- SMTP & SMS CONFIG FORM -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="bi bi-envelope-at text-primary me-2"></i>SMTP Server Email & SMS Settings
                            </h5>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="save_settings">
                            <div class="card-body p-4">
                                
                                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-server me-1"></i> SMTP Server Configuration</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold small">SMTP Server Host</label>
                                        <input type="text" name="smtp_host" class="form-control" value="<?= htmlspecialchars($config['smtp_host'] ?? 'smtp.gmail.com'); ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small">SMTP Port</label>
                                        <input type="number" name="smtp_port" class="form-control" value="<?= htmlspecialchars($config['smtp_port'] ?? 587); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">SMTP Username / Email</label>
                                        <input type="email" name="smtp_user" class="form-control" placeholder="your_email@gmail.com" value="<?= htmlspecialchars($config['smtp_user'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">SMTP App Password</label>
                                        <input type="password" name="smtp_pass" class="form-control" placeholder="••••••••••••••••" value="<?= htmlspecialchars($config['smtp_pass'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold small">Sender Display Name</label>
                                        <input type="text" name="from_name" class="form-control" value="<?= htmlspecialchars($config['from_name'] ?? 'SkyPort Airlines'); ?>">
                                    </div>
                                </div>

                                <hr>

                                <h6 class="fw-bold text-success mb-3"><i class="bi bi-chat-text me-1"></i> SMS Gateway Configuration</h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold small">Fast2SMS / Bulk SMS API Key</label>
                                        <input type="text" name="sms_api_key" class="form-control font-monospace" placeholder="Paste your Fast2SMS API Key here..." value="<?= htmlspecialchars($config['sms_api_key'] ?? ''); ?>">
                                        <div class="form-text small">Used for sending real mobile text SMS for flight confirmations and Web Check-in passes.</div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer bg-light p-3 text-end">
                                <button type="submit" class="btn btn-primary rounded-pill fw-bold px-4">
                                    <i class="bi bi-save me-1"></i> Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TEST EMAIL DISPATCH & EASY GUIDE -->
                <div class="col-lg-4">
                    
                    <!-- EXPLANATION CARD -->
                    <div class="card shadow-sm border-0 rounded-3 mb-4 bg-primary-subtle text-primary-emphasis">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-2"><i class="bi bi-question-circle-fill me-1"></i> SMTP & SMS Key Kya Hota Hai?</h6>
                            <p class="small mb-2">
                                <strong>1. SMTP (Email Gateway):</strong> Yeh ek tarika hai jisse aapka website passengers ke email address par automatic **Flight E-Tickets** aur **Boarding Pass** emails bhejta hai (e.g. Gmail / Outlook).
                            </p>
                            <p class="small mb-2">
                                <strong>2. SMS API Key:</strong> Yeh Fast2SMS / Twilio ki key hoti hai jisse passengers ke mobile phone par **SMS Messages** (PNR & Flight Timing) jajate hain.
                            </p>
                            <div class="p-2 bg-white rounded border small text-dark mt-3">
                                💡 <strong>Tension Lene Ki Zaroorat Nahi!</strong> Agar aapke paas SMTP ya SMS key nahi hai, tab bhi aapka system auto-fallback se sabhi tickets, Boarding Passes, aur PDF E-Tickets screen par instant generate karta hai!
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="bi bi-send text-success me-2"></i>Test Email Gateway
                            </h5>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="test_email">
                            <div class="card-body p-4">
                                <p class="text-muted small">Send a test ticket notification email to verify your SMTP settings.</p>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Recipient Email</label>
                                    <input type="email" name="test_email" class="form-control" placeholder="name@example.com" required>
                                </div>
                                <button type="submit" class="btn btn-outline-success w-100 rounded-pill fw-bold mb-3">
                                    <i class="bi bi-send-fill me-1"></i> Send Test Email
                                </button>
                                <hr>
                                <a href="../preview_email.php?type=booking" target="_blank" class="btn btn-sm btn-light border w-100 rounded-pill fw-bold text-primary mb-2">
                                    <i class="bi bi-eye-fill me-1"></i> Preview E-Ticket Email HTML
                                </a>
                                <a href="../preview_email.php?type=checkin" target="_blank" class="btn btn-sm btn-light border w-100 rounded-pill fw-bold text-warning">
                                    <i class="bi bi-eye-fill me-1"></i> Preview Boarding Pass Email HTML
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>

</main>

<?php include 'footer.php'; ?>
