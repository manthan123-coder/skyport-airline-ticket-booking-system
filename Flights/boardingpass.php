<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'include/db_json.php';

$pnr = $_REQUEST['pnr'] ?? $_SESSION['pnr'] ?? '';
$seat = $_REQUEST['seat_number'] ?? '12A';

if (empty($pnr)) {
    die('<div class="container my-5 alert alert-danger text-center"><h5>Invalid Request</h5><p>No PNR specified.</p><a href="mybooking.php" class="btn btn-primary btn-sm">My Bookings</a></div>');
}

// Perform Check-in update
if (!empty($seat)) {
    update_checkin($pnr, $seat);
}

$booking = get_booking_by_pnr($pnr);
if (!$booking) {
    die('<div class="container my-5 alert alert-danger text-center"><h5>Booking Not Found</h5><p>No booking matches PNR: ' . htmlspecialchars($pnr) . '</p><a href="mybooking.php" class="btn btn-primary btn-sm">My Bookings</a></div>');
}

$qrData = "SkyPort Airlines Boarding Pass\n"
    . "Passenger: " . ($booking['firstname'] ?? '') . " " . ($booking['lastname'] ?? '') . "\n"
    . "PNR: " . $booking['pnr'] . "\n"
    . "Flight: " . ($booking['flight_name'] ?? '') . "\n"
    . "Seat: " . ($booking['seat_no'] ?? '12A') . "\n"
    . "Gate: A12\n"
    . "Status: Checked-in";

include 'include/header.php';
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <div class="alert alert-success text-center mb-4 rounded-3 shadow-sm">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <strong>Check-In Successful!</strong> Your boarding pass has been issued.
            </div>

            <!-- BOARDING PASS CARD -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" id="printableBoardingPass">
                <!-- TOP HEADER -->
                <div class="bg-dark text-white p-4 d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-2 text-primary">✈</span>
                            <h3 class="fw-bold mb-0 text-white">SkyPort Airlines</h3>
                        </div>
                        <small class="text-white-50">BOARDING PASS</small>
                    </div>
                    <div class="text-end">
                        <div class="fs-4 fw-bold text-warning"><?= htmlspecialchars($booking['flight_name']); ?></div>
                        <div class="small opacity-75">PNR: <strong><?= htmlspecialchars($booking['pnr']); ?></strong></div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-8">
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <span class="text-muted small">PASSENGER NAME</span>
                                    <div class="fs-5 fw-bold text-dark"><?= htmlspecialchars(($booking['firstname'] ?? '') . ' ' . ($booking['lastname'] ?? '')); ?></div>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted small">CLASS / CATEGORY</span>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($booking['passenger_class'] ?? 'Economy'); ?></div>
                                </div>
                            </div>

                            <div class="p-3 bg-light rounded-3 mb-3">
                                <div class="row align-items-center text-center">
                                    <div class="col-5 text-start">
                                        <span class="text-muted small">FROM</span>
                                        <div class="fs-4 fw-bold text-primary"><?= htmlspecialchars($booking['from_city']); ?></div>
                                        <div class="small text-muted"><?= date('H:i', strtotime($booking['departure_time'] ?? '06:00')); ?></div>
                                    </div>
                                    <div class="col-2 text-center fs-3 text-muted">✈</div>
                                    <div class="col-5 text-end">
                                        <span class="text-muted small">TO</span>
                                        <div class="fs-4 fw-bold text-primary"><?= htmlspecialchars($booking['to_city']); ?></div>
                                        <div class="small text-muted"><?= date('H:i', strtotime($booking['arrival_time'] ?? '08:15')); ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 text-center">
                                <div class="col-3">
                                    <div class="p-2 border rounded-3 bg-light">
                                        <span class="text-muted small">SEAT</span>
                                        <div class="fs-4 fw-bold text-primary"><?= htmlspecialchars($booking['seat_no'] ?? '12A'); ?></div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-2 border rounded-3 bg-light">
                                        <span class="text-muted small">GATE</span>
                                        <div class="fs-4 fw-bold text-dark">A12</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-2 border rounded-3 bg-light">
                                        <span class="text-muted small">BOARDING</span>
                                        <div class="fs-6 fw-bold text-dark"><?= date('H:i', strtotime(($booking['departure_time'] ?? '06:00') . ' -40 minutes')); ?></div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-2 border rounded-3 bg-light">
                                        <span class="text-muted small">DATE</span>
                                        <div class="small fw-bold text-dark mt-1"><?= date('d M', strtotime($booking['departure_date'])); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- QR SCANNER SECTION -->
                        <div class="col-md-4 text-center border-start ps-md-4">
                            <h6 class="fw-bold mb-2">SCAN TO BOARD</h6>
                            <div class="p-2 border rounded-3 d-inline-block bg-white mb-2 shadow-sm">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode($qrData); ?>" alt="Boarding Pass QR Code" width="150" height="150">
                            </div>
                            <div class="small text-muted mb-1">Verify at Security Gate</div>
                            <span class="badge bg-success"><i class="bi bi-shield-check me-1"></i> VERIFIED & CHECKED-IN</span>
                        </div>
                    </div>

                    <!-- FOOTER ACTIONS -->
                    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center pt-4 mt-4 border-top">
                        <a href="mybooking.php" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                            <i class="bi bi-arrow-left fs-6"></i> Return to My Bookings
                        </a>
                        <div class="d-flex gap-2">
                            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                <i class="bi bi-printer me-1"></i> Print Boarding Pass
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<style>
@media print {
    nav, footer, .btn, .alert, .d-flex.flex-wrap.gap-2.justify-content-between {
        display: none !important;
    }
    body {
        background: #fff !important;
    }
    #printableBoardingPass {
        box-shadow: none !important;
        border: 2px solid #000 !important;
    }
}
</style>

<?php include 'include/footer.php'; ?>