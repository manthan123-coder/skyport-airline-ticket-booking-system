<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
$is_user_logged_in = !empty($_SESSION['user_id']) || !empty($_SESSION['user_email']);
$user_display_name = $_SESSION['user_name'] ?? $_SESSION['user_email'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SkyPort — Premium Airline Ticket Booking</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Google Fonts (IndiGo Airline Style Typography) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="index.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold" href="index.php">
                <span class="logo-circle me-2">✈</span>
                <span style="color: #0d6efd; font-size: 1.4rem;">SkyPort</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item ms-lg-4"><a class="nav-link <?= ($current_page == 'index.php') ? 'active fw-bold text-primary' : ''; ?>" href="index.php">Home</a></li>
                    <!-- <li class="nav-item ms-lg-4"><a class="nav-link <?= ($current_page == 'flight.php') ? 'active fw-bold text-primary' : ''; ?>" href="flight.php">Flights</a></li> -->
                    <li class="nav-item ms-lg-4"><a class="nav-link <?= ($current_page == 'webcheckin.php' || $current_page == 'web check-in.php') ? 'active fw-bold text-primary' : ''; ?>" href="webcheckin.php">Web Check-in</a></li>
                    <li class="nav-item ms-lg-4"><a class="nav-link <?= ($current_page == 'mybooking.php') ? 'active fw-bold text-primary' : ''; ?>" href="mybooking.php">My Bookings</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a class="btn btn-outline-primary btn-sm" href="mybooking.php"><i class="bi bi-ticket-perforated"></i> Find Ticket</a>
                    <?php if ($is_user_logged_in): ?>
                        <a class="btn btn-danger btn-sm" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold d-inline-flex align-items-center">
                            <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($user_display_name); ?>
                        </span>
                    <?php else: ?>
                        <a class="btn btn-primary btn-sm" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <div style="padding-top: 75px;"></div>