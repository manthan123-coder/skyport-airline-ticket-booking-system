<?php
require_once __DIR__ . '/../include/db_json.php';
require_once __DIR__ . '/../auth.php';

$message = '';
$message_type = 'success';

$appearance = get_appearance_config();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_appearance') {
        $theme_mode = trim($_POST['theme_mode'] ?? 'midnight');
        $primary_color = trim($_POST['primary_color'] ?? '#0d6efd');
        $sidebar_theme = trim($_POST['sidebar_theme'] ?? 'dark');
        $panel_title = trim($_POST['panel_title'] ?? 'SkyPort Admin');
        $header_subtitle = trim($_POST['header_subtitle'] ?? 'Airline Ticket Booking System');

        $new_appearance = [
            'theme_mode' => $theme_mode,
            'primary_color' => $primary_color,
            'sidebar_theme' => $sidebar_theme,
            'panel_title' => $panel_title,
            'header_subtitle' => $header_subtitle
        ];

        save_appearance_config($new_appearance);
        $appearance = $new_appearance;
        $message = "Admin Panel Appearance and Theme settings updated successfully!";
        $message_type = 'success';
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
                    <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-palette-fill text-warning me-2"></i>Admin Appearance & Themes</h3>
                    <small class="text-muted">Customize AdminLTE colors, themes, dark mode, and branding</small>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end mb-0">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Appearance</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Body Content -->
    <div class="app-content">
        <div class="container-fluid px-4">

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $message_type; ?> alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">

                <!-- THEME CUSTOMIZER FORM -->
                <div class="col-lg-7">
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="bi bi-sliders text-primary me-2"></i>Theme & Visual Styling Settings
                            </h5>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="save_appearance">
                            <div class="card-body p-4">

                                <!-- Theme Mode Selection -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark">Select Theme Style Mode</label>
                                    <div class="row g-3">
                                        <div class="col-4">
                                            <label class="card h-100 p-3 text-center border cursor-pointer theme-select-card">
                                                <input type="radio" name="theme_mode" value="midnight" class="form-check-input mb-2 mx-auto" <?= ($appearance['theme_mode'] === 'midnight') ? 'checked' : ''; ?>>
                                                <div class="fw-bold text-primary mb-1"><i class="bi bi-moon-stars-fill me-1"></i> Midnight Blue</div>
                                                <div class="small text-muted">Signature SkyPort Dark Theme</div>
                                            </label>
                                        </div>
                                        <div class="col-4">
                                            <label class="card h-100 p-3 text-center border cursor-pointer theme-select-card">
                                                <input type="radio" name="theme_mode" value="dark" class="form-check-input mb-2 mx-auto" <?= ($appearance['theme_mode'] === 'dark') ? 'checked' : ''; ?>>
                                                <div class="fw-bold text-dark mb-1"><i class="bi bi-moon-fill me-1"></i> Dark Mode</div>
                                                <div class="small text-muted">Full Sleek Dark Interface</div>
                                            </label>
                                        </div>
                                        <div class="col-4">
                                            <label class="card h-100 p-3 text-center border cursor-pointer theme-select-card">
                                                <input type="radio" name="theme_mode" value="light" class="form-check-input mb-2 mx-auto" <?= ($appearance['theme_mode'] === 'light') ? 'checked' : ''; ?>>
                                                <div class="fw-bold text-secondary mb-1"><i class="bi bi-sun-fill me-1"></i> Light Mode</div>
                                                <div class="small text-muted">Clean Bright Minimalist</div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- Brand Primary Accent Color -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark">Primary Accent Brand Color</label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <?php
                                        $colors = [
                                            '#0d6efd' => 'Sky Blue',
                                            '#0b5ed7' => 'Navy Royal',
                                            '#6f42c1' => 'Purple',
                                            '#198754' => 'Emerald',
                                            '#ffc107' => 'Amber Gold',
                                            '#dc3545' => 'Ruby Red'
                                        ];
                                        foreach ($colors as $hex => $cname):
                                            $checked = (strtolower($appearance['primary_color']) === strtolower($hex)) ? 'checked' : '';
                                        ?>
                                            <label class="d-flex align-items-center gap-2 border px-3 py-2 rounded-pill cursor-pointer shadow-sm">
                                                <input type="radio" name="primary_color" value="<?= $hex; ?>" class="form-check-input" <?= $checked; ?>>
                                                <span class="rounded-circle d-inline-block" style="width:16px; height:16px; background:<?= $hex; ?>;"></span>
                                                <span class="small fw-semibold"><?= $cname; ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <hr>

                                <!-- Admin Branding & Titles -->
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-workspace me-1"></i> Admin Panel Title & Header Branding</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Panel Main Title</label>
                                        <input type="text" name="panel_title" class="form-control" value="<?= htmlspecialchars($appearance['panel_title'] ?? 'SkyPort Admin'); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Header Subtitle</label>
                                        <input type="text" name="header_subtitle" class="form-control" value="<?= htmlspecialchars($appearance['header_subtitle'] ?? 'Airline Ticket Booking System'); ?>" required>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer bg-light text-end py-3">
                                <button type="submit" class="btn btn-warning rounded-pill fw-bold px-4">
                                    <i class="bi bi-palette me-1"></i> Apply & Save Appearance
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- LIVE THEME PREVIEW CARD -->
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="bi bi-eye text-success me-2"></i>Live Theme Preview
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="p-3 rounded-3 mb-3 border text-white" style="background: <?= htmlspecialchars($appearance['primary_color']); ?>;">
                                <div class="d-flex align-items-center gap-2 fw-bold fs-5">
                                    <i class="bi bi-airplane-engines"></i> <?= htmlspecialchars($appearance['panel_title']); ?>
                                </div>
                                <small class="opacity-75"><?= htmlspecialchars($appearance['header_subtitle']); ?></small>
                            </div>

                            <div class="p-3 rounded-3 border bg-light text-dark mb-3">
                                <div class="fw-bold mb-1"><i class="bi bi-check-circle-fill text-success me-1"></i> Active Theme Mode</div>
                                <span class="badge bg-primary text-uppercase px-3 py-1"><?= htmlspecialchars($appearance['theme_mode']); ?></span>
                            </div>

                            <div class="alert alert-info border-0 small">
                                <i class="bi bi-info-circle me-1"></i> Changes will take effect immediately across all Admin LTE dashboard pages.
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

</main>

<?php include 'footer.php'; ?>
