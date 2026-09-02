<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'include/db_json.php';
require_once __DIR__ . '/../auth.php';

// MANDATORY LOGIN CHECK FOR WEB CHECK-IN
if (empty($_SESSION['user_id'])) {
    $redirect_url = 'webcheckin.php';
    if (!empty($_REQUEST['pnr'])) {
        $redirect_url .= '?pnr=' . urlencode(trim($_REQUEST['pnr']));
    }
    $_SESSION['post_login_redirect'] = $redirect_url;
    $_SESSION['flash_success'] = 'Please log in to your SkyPort account to access Web Check-In.';
    header('Location: login.php');
    exit;
}

$pnr = $_REQUEST['pnr'] ?? '';
$booking = null;

if (!empty($pnr)) {
    $booking = get_booking_by_pnr($pnr);
}

include 'include/header.php';
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 p-4">
                <h4 class="fw-bold mb-3 text-center"><i class="bi bi-qr-code-scan me-2 text-primary"></i>Web Check-In Portal</h4>
                <p class="text-muted text-center mb-4">Complete your check-in, select your preferred seat, and get your instant Boarding Pass.</p>

                <?php if (!$booking): ?>
                    <!-- SEARCH PNR FORM -->
                    <form method="POST" action="webcheckin.php" class="p-4 bg-light rounded-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Enter PNR Number or Booking Reference</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-ticket text-muted"></i></span>
                                <input type="text" name="pnr" class="form-control form-control-lg py-2" placeholder="e.g. SKP782" required value="<?= htmlspecialchars($pnr); ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 fw-bold">
                            Find Booking & Continue Check-In <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </form>
                <?php else: ?>
                    <!-- SEAT SELECTION FORM -->
                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark fs-5"><?= htmlspecialchars($booking['airline_name'] ?? 'IndiGo'); ?> (<?= htmlspecialchars($booking['flight_name']); ?>)</span>
                            <span class="badge bg-primary">PNR: <?= htmlspecialchars($booking['pnr']); ?></span>
                        </div>
                        <div class="row g-2 small text-muted">
                            <div class="col-md-6"><strong>Passenger:</strong> <?= htmlspecialchars(($booking['firstname'] ?? '') . ' ' . ($booking['lastname'] ?? '')); ?></div>
                            <div class="col-md-6"><strong>Route:</strong> <?= htmlspecialchars($booking['from_city']); ?> ✈ <?= htmlspecialchars($booking['to_city']); ?></div>
                            <div class="col-md-6"><strong>Date:</strong> <?= date('d M Y', strtotime($booking['departure_date'])); ?></div>
                            <div class="col-md-6"><strong>Time:</strong> <?= date('H:i', strtotime($booking['departure_time'] ?? '06:00')); ?></div>
                        </div>
                    </div>

                    <form action="boardingpass.php" method="POST">
                        <input type="hidden" name="pnr" value="<?= htmlspecialchars($booking['pnr']); ?>">
                        
                        <h6 class="fw-bold mb-3"><i class="bi bi-grid-3x3-gap me-2 text-primary"></i>Select Aircraft Seat</h6>
                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-muted">Available Seats</label>
                            <select name="seat_number" class="form-select form-select-lg" required>
                                <option value="">-- Choose Your Seat --</option>
                                <option value="12A" <?= ($booking['seat_no'] ?? '') === '12A' ? 'selected' : ''; ?>>12A (Window Seat - Left)</option>
                                <option value="12B" <?= ($booking['seat_no'] ?? '') === '12B' ? 'selected' : ''; ?>>12B (Middle Seat - Left)</option>
                                <option value="12C" <?= ($booking['seat_no'] ?? '') === '12C' ? 'selected' : ''; ?>>12C (Aisle Seat - Left)</option>
                                <option value="14D" <?= ($booking['seat_no'] ?? '') === '14D' ? 'selected' : ''; ?>>14D (Aisle Seat - Right)</option>
                                <option value="14E" <?= ($booking['seat_no'] ?? '') === '14E' ? 'selected' : ''; ?>>14E (Middle Seat - Right)</option>
                                <option value="14F" <?= ($booking['seat_no'] ?? '') === '14F' ? 'selected' : ''; ?>>14F (Window Seat - Right)</option>
                                <option value="18A" <?= ($booking['seat_no'] ?? '') === '18A' ? 'selected' : ''; ?>>18A (Window Seat - Extra Legroom)</option>
                                <option value="18F" <?= ($booking['seat_no'] ?? '') === '18F' ? 'selected' : ''; ?>>18F (Window Seat - Extra Legroom)</option>
                            </select>
                        </div>

                        <div class="alert alert-info py-2 small mb-4">
                            <i class="bi bi-info-circle me-1"></i> By completing web check-in, you declare that you are not carrying prohibited hazardous materials in cabin baggage.
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="mybooking.php" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                            <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                Confirm Check-In & Get Boarding Pass <i class="bi bi-check-circle-fill ms-2"></i>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>