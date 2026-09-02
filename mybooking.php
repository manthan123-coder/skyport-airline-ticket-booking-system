<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'include/db_json.php';
require_once 'auth.php';

// MANDATORY LOGIN CHECK FOR MY BOOKINGS
if (empty($_SESSION['user_id'])) {
    $redirect_url = 'mybooking.php';
    if (!empty($_GET['pnr'])) {
        $redirect_url .= '?pnr=' . urlencode(trim($_GET['pnr']));
    }
    $_SESSION['post_login_redirect'] = $redirect_url;
    $_SESSION['flash_success'] = 'Please log in to your SkyPort account to view your flight bookings.';
    header('Location: login.php');
    exit;
}

$search_term = $_GET['pnr'] ?? $_GET['email'] ?? $_SESSION['user_email'] ?? '';
$bookings = get_user_bookings($search_term);

include 'include/header.php';
?>

<div class="container my-4">
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <h4 class="fw-bold mb-3"><i class="bi bi-ticket-perforated me-2 text-primary"></i>My Flight Bookings</h4>
        
        <!-- SEARCH BOX -->
        <form method="GET" action="mybooking.php" class="row g-2 align-items-center">
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="pnr" class="form-control border-start-0 py-2" placeholder="Search by PNR Number or Passenger Name" value="<?= htmlspecialchars($search_term); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3">
                    Search Booking
                </button>
            </div>
        </form>
    </div>

    <!-- BOOKINGS LIST -->
    <?php if (empty($bookings)): ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4">
            <div class="fs-1 text-muted mb-3"><i class="bi bi-journal-x"></i></div>
            <h5 class="fw-bold">No Bookings Found</h5>
            <p class="text-muted mb-4">We couldn't find any flight bookings matching your search term.</p>
            <div>
                <a href="index.php" class="btn btn-primary px-4 rounded-pill">Book a Flight Now</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($bookings as $b): 
                $is_cancelled = (strcasecmp($b['checkin_status'] ?? '', 'Cancelled') === 0 || strcasecmp($b['payment_status'] ?? '', 'Cancelled') === 0);
                $status_display = $is_cancelled ? 'Cancelled' : ($b['payment_status'] ?? 'Success');
                $status_badge_class = $is_cancelled ? 'bg-danger' : 'bg-success';
            ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 <?= $is_cancelled ? 'border-danger border-opacity-25 bg-light-subtle' : ''; ?>">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark fs-5">✈ <?= htmlspecialchars($b['airline_name'] ?? 'IndiGo'); ?> (<?= htmlspecialchars($b['flight_name']); ?>)</span>
                            <span class="badge <?= $status_badge_class; ?> small"><?= htmlspecialchars($status_display); ?></span>
                        </div>

                        <div class="text-primary fw-bold mb-3 fs-6">
                            <?= htmlspecialchars($b['from_city']); ?> ➔ <?= htmlspecialchars($b['to_city']); ?>
                        </div>

                        <?php if ($is_cancelled): ?>
                            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3 border-danger-subtle">
                                <i class="bi bi-x-circle-fill me-1"></i> <strong>Booking Cancelled:</strong> This flight booking was cancelled by airline administration.
                            </div>
                        <?php endif; ?>

                        <div class="row g-2 small text-muted mb-3">
                            <div class="col-6"><strong>PNR:</strong> <span class="text-dark fw-bold"><?= htmlspecialchars($b['pnr']); ?></span></div>
                            <div class="col-6"><strong>Name:</strong> <?= htmlspecialchars(($b['firstname'] ?? '') . ' ' . ($b['lastname'] ?? '')); ?></div>
                            <div class="col-6"><strong>Date:</strong> <?= date('d M Y', strtotime($b['departure_date'] ?? date('Y-m-d'))); ?></div>
                            <div class="col-6"><strong>Amount:</strong> ₹<?= number_format(floatval($b['amount'] ?? 0)); ?></div>
                            <div class="col-12"><strong>Check-in Status:</strong> 
                                <?php if ($is_cancelled): ?>
                                    <span class="badge bg-danger">Cancelled</span>
                                <?php elseif (($b['checkin_status'] ?? '') === 'Checked-in'): ?>
                                    <span class="badge bg-info text-dark">Checked-in (Seat: <?= htmlspecialchars($b['seat_no'] ?? 'N/A'); ?>)</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Not Checked-in</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-auto pt-3 border-top">
                            <a href="download_pdf.php?pnr=<?= htmlspecialchars($b['pnr']); ?>" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold">
                                <i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF Ticket
                            </a>

                            <form action="viewticket.php" method="POST" class="d-inline">
                                <input type="hidden" name="pnr" value="<?= htmlspecialchars($b['pnr']); ?>">
                                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="bi bi-printer me-1"></i> View Ticket
                                </button>
                            </form>

                            <?php if ($is_cancelled): ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-bold align-self-center ms-auto">
                                    <i class="bi bi-slash-circle me-1"></i> Flight Cancelled
                                </span>
                            <?php elseif (($b['checkin_status'] ?? '') === 'Checked-in'): ?>
                                <form action="boardingpass.php" method="POST" class="d-inline">
                                    <input type="hidden" name="pnr" value="<?= htmlspecialchars($b['pnr']); ?>">
                                    <button type="submit" class="btn btn-info btn-sm text-dark fw-bold rounded-pill px-3">
                                        <i class="bi bi-card-heading me-1"></i> Boarding Pass
                                    </button>
                                </form>
                            <?php else: ?>
                                <form action="webcheckin.php" method="POST" class="d-inline">
                                    <input type="hidden" name="pnr" value="<?= htmlspecialchars($b['pnr']); ?>">
                                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">
                                        <i class="bi bi-qr-code me-1"></i> Web Check-In
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'include/footer.php'; ?>
