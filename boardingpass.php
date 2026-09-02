<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'include/db_json.php';

$pnr = $_REQUEST['pnr'] ?? $_SESSION['pnr'] ?? '';
$seat = $_REQUEST['seat_number'] ?? '12A';
$meal_type = $_REQUEST['meal_type'] ?? null;
$baggage_count = $_REQUEST['baggage_count'] ?? '15kg Included';

if (empty($pnr)) {
    die('<div class="container my-5 alert alert-danger text-center"><h5>Invalid Request</h5><p>No PNR specified for boarding pass generation.</p><a href="webcheckin.php" class="btn btn-primary btn-sm">Web Check-in</a></div>');
}

// Update check-in record in JSON database
if (!empty($seat)) {
    update_checkin($pnr, $seat, $meal_type, $baggage_count);
}

$booking = get_booking_by_pnr($pnr);
if (!$booking) {
    die('<div class="container my-5 alert alert-danger text-center"><h5>Booking Not Found</h5><p>No booking matches PNR: ' . htmlspecialchars($pnr) . '</p><a href="webcheckin.php" class="btn btn-primary btn-sm">Web Check-in</a></div>');
}

// Automatically dispatch Email E-Ticket Boarding Pass & SMS Alert
send_checkin_email_and_sms($booking);

$passengerName = trim(($booking['firstname'] ?? '') . ' ' . ($booking['lastname'] ?? ''));
$depTime = $booking['departure_time'] ?? '06:00';
$boardingTime = date('H:i', strtotime($depTime . ' -40 minutes'));
$flightDate = date('d M Y', strtotime($booking['departure_date'] ?? date('Y-m-d')));

$qrData = "SkyPort Airlines Official Digital Pass\n"
    . "Passenger: " . $passengerName . "\n"
    . "PNR: " . $booking['pnr'] . "\n"
    . "Flight: " . ($booking['flight_name'] ?? '') . "\n"
    . "Seat: " . ($booking['seat_no'] ?? '12A') . "\n"
    . "Gate: A12 | Zone: 2\n"
    . "Checkin Time: " . ($booking['checkin_time'] ?? date('Y-m-d H:i:s'));

include 'include/header.php';
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- SUCCESS STATUS BANNER -->
            <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-circle bg-success text-white" style="width: 50px; height: 50px; font-size: 1.5rem;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Check-In Successful & Boarding Pass Issued!</h5>
                        <p class="mb-0 text-muted small">Your seat <strong><?= htmlspecialchars($booking['seat_no'] ?? '12A'); ?></strong> is confirmed. Show this digital pass or print before security check.</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="download_pdf.php?pnr=<?= htmlspecialchars($booking['pnr']); ?>" class="btn btn-danger rounded-pill btn-sm px-3 fw-bold shadow-sm">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download PDF Pass
                    </a>
                    <button class="btn btn-outline-dark rounded-pill btn-sm px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#mobilePassModal">
                        <i class="bi bi-phone me-1"></i> Mobile Pass Preview
                    </button>
                    <button onclick="window.print()" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold shadow-sm">
                        <i class="bi bi-printer me-1"></i> Print Pass
                    </button>
                </div>
            </div>

            <!-- BOARDING PASS MAIN CARD -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4" id="printableBoardingPass">
                <!-- TOP HEADER BAR -->
                <div class="bg-dark text-white p-4 d-flex flex-wrap justify-content-between align-items-center position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="logo-circle" style="width: 44px; height: 44px; font-size: 1.3rem;">✈</div>
                        <div>
                            <h3 class="fw-bold mb-0 text-white" style="letter-spacing: 1px;">SkyPort Airlines</h3>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0 small fw-bold">OFFICIAL BOARDING PASS</span>
                        </div>
                    </div>
                    <div class="text-end mt-2 mt-sm-0">
                        <div class="fs-4 fw-bold text-warning" style="letter-spacing: 1px;"><?= htmlspecialchars($booking['flight_name']); ?></div>
                        <div class="small opacity-75">PNR REFERENCE: <strong class="text-white"><?= htmlspecialchars($booking['pnr']); ?></strong></div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="row g-4 align-items-center">
                        <!-- LEFT MAIN DETAILS -->
                        <div class="col-lg-8 border-end-lg pe-lg-4">
                            <!-- PASSENGER & CLASS -->
                            <div class="row g-3 mb-4 p-3 bg-light rounded-3">
                                <div class="col-md-6">
                                    <span class="text-muted small fw-bold text-uppercase d-block">PASSENGER NAME</span>
                                    <span class="fs-5 fw-extrabold text-dark"><?= htmlspecialchars($passengerName); ?></span>
                                </div>
                                <div class="col-md-3 col-6">
                                    <span class="text-muted small fw-bold text-uppercase d-block">CLASS</span>
                                    <span class="fw-bold text-dark"><?= htmlspecialchars($booking['passenger_class'] ?? 'Economy'); ?></span>
                                </div>
                                <div class="col-md-3 col-6">
                                    <span class="text-muted small fw-bold text-uppercase d-block">MEAL OPTION</span>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 small"><?= htmlspecialchars($booking['meal_type'] ?? 'Standard'); ?></span>
                                </div>
                            </div>

                            <!-- ROUTE & FLIGHT TIMINGS -->
                            <div class="p-4 bg-light rounded-4 mb-4 border">
                                <div class="row align-items-center text-center text-md-start">
                                    <div class="col-md-5">
                                        <span class="text-muted small fw-bold text-uppercase">DEPARTURE</span>
                                        <div class="fs-3 fw-bold text-primary"><?= htmlspecialchars($booking['from_city']); ?></div>
                                        <div class="fs-5 fw-bold text-dark"><?= $depTime; ?></div>
                                        <div class="small text-muted"><?= $flightDate; ?></div>
                                    </div>
                                    <div class="col-md-2 text-center my-3 my-md-0">
                                        <i class="bi bi-airplane-fill fs-2 text-primary"></i>
                                        <div class="small fw-semibold text-muted">Direct Flight</div>
                                    </div>
                                    <div class="col-md-5 text-md-end">
                                        <span class="text-muted small fw-bold text-uppercase">DESTINATION</span>
                                        <div class="fs-3 fw-bold text-primary"><?= htmlspecialchars($booking['to_city']); ?></div>
                                        <div class="fs-5 fw-bold text-dark"><?= htmlspecialchars($booking['arrival_time'] ?? '08:15'); ?></div>
                                        <div class="small text-muted"><?= $flightDate; ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEAT, GATE, BOARDING, ZONE -->
                            <div class="row g-3 text-center">
                                <div class="col-3">
                                    <div class="p-3 border rounded-4 bg-primary-subtle border-primary text-primary">
                                        <span class="small fw-bold text-uppercase d-block">SEAT</span>
                                        <div class="fs-3 fw-extrabold"><?= htmlspecialchars($booking['seat_no'] ?? '12A'); ?></div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-3 border rounded-4 bg-light">
                                        <span class="small fw-bold text-uppercase text-muted d-block">GATE</span>
                                        <div class="fs-3 fw-bold text-dark">A12</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-3 border rounded-4 bg-light">
                                        <span class="small fw-bold text-uppercase text-muted d-block">BOARDING</span>
                                        <div class="fs-4 fw-bold text-dark"><?= $boardingTime; ?></div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="p-3 border rounded-4 bg-light">
                                        <span class="small fw-bold text-uppercase text-muted d-block">ZONE</span>
                                        <div class="fs-3 fw-bold text-dark">2</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT QR CODE & VERIFICATION STUB -->
                        <div class="col-lg-4 text-center ps-lg-4">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-bold mb-3 d-inline-block">
                                <i class="bi bi-shield-check me-1"></i> VERIFIED & CHECKED-IN
                            </span>

                            <div class="p-3 border rounded-4 bg-white d-inline-block shadow-sm mb-3 position-relative">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?= urlencode($qrData); ?>" alt="Boarding Pass QR Code" class="img-fluid rounded-3" width="180" height="180">
                                <div class="small text-muted mt-2 fw-semibold">Scan at Security Gate & Gate A12</div>
                            </div>

                            <div class="p-3 bg-light rounded-3 text-start small text-muted">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Baggage Tag:</span>
                                    <strong class="text-dark"><?= htmlspecialchars($booking['baggage_count'] ?? '15kg Included'); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Check-in Timestamp:</span>
                                    <strong class="text-dark"><?= date('d M, H:i'); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER ACTIONS & DISCLAIMER -->
                    <div class="pt-4 mt-4 border-top d-flex flex-wrap gap-3 justify-content-between align-items-center">
                        <a href="mybooking.php" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                            <i class="bi bi-arrow-left fs-6"></i> Return to My Bookings
                        </a>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="webcheckin.php" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                                <i class="bi bi-arrow-repeat fs-6"></i> Change Seat / Check-in
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- BAGGAGE CLAIM TAG STUB -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-light mb-4 border-start border-4 border-primary">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1 fw-bold mb-1">OFFICIAL BAGGAGE CLAIM TAG</span>
                        <h6 class="fw-bold mb-0 text-dark">Tag Reference: BAG-<?= htmlspecialchars($booking['pnr']); ?>-01</h6>
                        <small class="text-muted">Attach to checked baggage at airport drop counter.</small>
                    </div>
                    <div class="text-end mt-2 mt-sm-0">
                        <span class="badge bg-dark fs-6 px-3 py-2">Allowed: <?= htmlspecialchars($booking['baggage_count'] ?? '15kg Included'); ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MOBILE BOARDING PASS MODAL PREVIEW -->
<div class="modal fade" id="mobilePassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="bg-dark text-white p-4 text-center" style="background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%) !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-white text-dark rounded-pill px-3 py-1 fw-bold">Apple Wallet / Pass Preview</span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <h4 class="fw-bold text-white mb-0"><?= htmlspecialchars($booking['from_city']); ?> ✈ <?= htmlspecialchars($booking['to_city']); ?></h4>
                <small class="text-white-50">Flight <?= htmlspecialchars($booking['flight_name']); ?> • PNR <?= htmlspecialchars($booking['pnr']); ?></small>
            </div>
            <div class="modal-body p-4 bg-light text-center">
                <div class="bg-white p-3 rounded-4 shadow-sm mb-3">
                    <div class="row g-2 mb-3 text-start border-bottom pb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">PASSENGER</small>
                            <strong class="text-dark"><?= htmlspecialchars($passengerName); ?></strong>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block">SEAT</small>
                            <strong class="text-primary fs-4"><?= htmlspecialchars($booking['seat_no'] ?? '12A'); ?></strong>
                        </div>
                    </div>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=<?= urlencode($qrData); ?>" alt="QR Code" width="160" height="160" class="rounded-3">
                    <div class="small text-muted mt-2">Scan at Boarding Gate</div>
                </div>
                <button type="button" class="btn btn-dark w-100 rounded-pill fw-bold" onclick="alert('Digital pass link generated and ready!')">
                    <i class="bi bi-download me-1"></i> Save Pass to Device
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    nav, footer, .btn, .alert, .badge, .border-top, #mobilePassModal {
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
