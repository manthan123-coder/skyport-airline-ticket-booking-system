<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure strict admin login check
if (empty($_SESSION['is_admin_logged_in']) || $_SESSION['is_admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../include/db_json.php';

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$current_page = basename($_SERVER['PHP_SELF']);
$appearance = get_appearance_config();
$primary_color = $appearance['primary_color'] ?? '#0d6efd';
$theme_mode = $appearance['theme_mode'] ?? 'midnight';
$panel_title = $appearance['panel_title'] ?? 'SkyPort Admin';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="<?= ($theme_mode === 'dark') ? 'dark' : 'light'; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($panel_title); ?></title>

    <!-- Google Font: Source Sans Pro & Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+3:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- OverlayScrollbars -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css">
    <!-- AdminLTE v4 CSS -->
    <link rel="stylesheet" href="../AdminLte/dist/css/adminlte.min.css">
    
    <style>
        :root {
            --bs-primary: <?= $primary_color; ?>;
            --bs-primary-rgb: <?= implode(',', sscanf($primary_color, "#%02x%02x%02x")); ?>;
        }
        .bg-primary, .btn-primary { background-color: <?= $primary_color; ?> !important; border-color: <?= $primary_color; ?> !important; }
        .text-primary { color: <?= $primary_color; ?> !important; }
        .app-header .navbar-brand { font-weight: 700; color: <?= $primary_color; ?>; }
        .brand-link { background-color: #0b1e36 !important; }
        .sidebar-dark-primary { background-color: <?= ($theme_mode === 'midnight') ? '#0b1e36' : '#0f172a'; ?> !important; }
        .sidebar-dark-primary .nav-link.active { background-color: <?= $primary_color; ?> !important; color: #fff !important; }
        .card-skyport { border-top: 4px solid <?= $primary_color; ?>; }
        .badge-status-confirmed { background-color: #198754; color: white; }
        .badge-status-checkedin { background-color: <?= $primary_color; ?>; color: white; }
        .badge-status-cancelled { background-color: #dc3545; color: white; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg <?= ($theme_mode === 'dark') ? 'bg-dark text-white' : 'bg-body-tertiary'; ?>">
<div class="app-wrapper">

    <!-- Top Navigation Bar -->
    <nav class="app-header navbar navbar-expand bg-body shadow-sm">
        <div class="container-fluid">
            <!-- Left Header Links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list fs-4"></i></a>
                </li>
                <li class="nav-item d-none d-md-block">
                    <a href="../index.php" class="nav-link" target="_blank"><i class="bi bi-globe me-1"></i> View Website</a>
                </li>
            </ul>

            <!-- Right Header Links -->
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px;">
                            <?= strtoupper(substr($admin_name, 0, 1)); ?>
                        </div>
                        <span class="d-none d-md-inline fw-semibold"><?= htmlspecialchars($admin_name); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow border-0 mt-2">
                        <li class="user-header bg-primary text-white text-center p-3">
                            <i class="bi bi-person-badge display-4"></i>
                            <p class="mb-0 fw-bold mt-2"><?= htmlspecialchars($admin_name); ?></p>
                            <small>SkyPort System Administrator</small>
                        </li>
                        <li class="user-footer d-flex justify-content-between p-3 bg-light">
                            <a href="settings.php" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="bi bi-gear me-1"></i> Settings</a>
                            <a href="login.php?logout=1" class="btn btn-danger btn-sm rounded-pill"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!-- End Top Navigation Bar -->
