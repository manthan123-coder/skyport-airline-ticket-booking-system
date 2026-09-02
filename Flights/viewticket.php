<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'include/db_json.php';

$pnr = $_REQUEST['pnr'] ?? $_SESSION['pnr'] ?? '';
$booking = get_booking_by_pnr($pnr);

if (!$booking) {
    die('<div class="container my-5 alert alert-danger text-center"><h5>Ticket Not Found</h5><p>No booking matches PNR: ' . htmlspecialchars($pnr) . '</p><a href="mybooking.php" class="btn btn-primary btn-sm">Back to My Bookings</a></div>');
}

$qrData = "SkyPort Airlines E-Ticket\n"
    . "PNR: " . $booking['pnr'] . "\n"
    . "Passenger: " . ($booking['firstname'] ?? '') . " " . ($booking['lastname'] ?? '') . "\n"
    . "Flight: " . ($booking['flight_name'] ?? '') . "\n"
    . "Route: " . ($booking['from_city'] ?? '') . " to " . ($booking['to_city'] ?? '') . "\n"
    . "Status: Confirmed";

include 'include/header.php';
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <!-- PRINTABLE TICKET CARD -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" id="printableTicket">
                <!-- TICKET HEADER -->
                <div class="bg-primary text-white p-4 d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-2">✈</span>
                            <h3 class="fw-bold mb-0">SkyPort Airlines</h3>
                        </div>
                        <small class="text-white-50">Official Electronic Flight Ticket (E-Ticket)</small>
                    </div>
                    <div class="text-end mt-2 mt-md-0">
                        <div class="small opacity-75">PNR NUMBER</div>
                        <div class="fs-3 fw-bold tracking-wider"><?= htmlspecialchars($booking['pnr']); ?></div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- ROUTE & FLIGHT HEADER -->
                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="row align-items-center text-center text-md-start g-3">
                            <div class="col-md-4">
                                <span class="text-muted small">DEPARTURE</span>
                                <div class="fs-4 fw-bold text-dark"><?= htmlspecialchars($booking['from_city']); ?></div>
                                <div class="text-primary fw-semibold"><?= date('H:i', strtotime($booking['departure_time'] ?? '06:00')); ?></div>
                                <div class="small text-muted"><?= date('d M Y', strtotime($booking['departure_date'])); ?></div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="small text-muted mb-1"><?= htmlspecialchars($booking['airline_name'] ?? 'IndiGo'); ?></div>
                                <div class="fs-5 fw-bold text-primary"><?= htmlspecialchars($booking['flight_name']); ?></div>
                                <div class="position-relative d-flex align-items-center justify-content-center my-2">
                                    <div class="w-100 bg-secondary opacity-25" style="height: 2px;"></div>
                                    <i class="bi bi-airplane-fill position-absolute text-primary fs-5 bg-light px-2"></i>
                                </div>
                                <span class="badge bg-success small">CONFIRMED</span>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <span class="text-muted small">ARRIVAL</span>
                                <div class="fs-4 fw-bold text-dark"><?= htmlspecialchars($booking['to_city']); ?></div>
                                <div class="text-primary fw-semibold"><?= date('H:i', strtotime($booking['arrival_time'] ?? '08:15')); ?></div>
                                <div class="small text-muted"><?= date('d M Y', strtotime($booking['departure_date'])); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- PASSENGER & BOOKING INFO GRID -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-8">
                            <h6 class="fw-bold mb-3"><i class="bi bi-people me-2 text-primary"></i>Passenger Details</h6>
                            <table class="table table-bordered table-sm small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Passenger Name</th>
                                        <th>Class / Seat</th>
                                        <th>Baggage Allowance</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars(($booking['firstname'] ?? '') . ' ' . ($booking['lastname'] ?? '')); ?></td>
                                        <td><?= htmlspecialchars($booking['passenger_class'] ?? 'Economy'); ?> / <?= htmlspecialchars($booking['seat_no'] ?? 'Unassigned'); ?></td>
                                        <td>15 KG Check-in / 7 KG Cabin</td>
                                        <td><span class="badge bg-success"><?= htmlspecialchars($booking['checkin_status'] ?? 'Confirmed'); ?></span></td>
                                    </tr>
                                </tbody>
                            </table>

                            <h6 class="fw-bold mt-4 mb-2"><i class="bi bi-info-circle me-2 text-primary"></i>Important Travel Notes</h6>
                            <ul class="small text-muted ps-3 mb-0">
                                <li>Please bring a valid Government-issued Photo ID (Passport / Aadhaar / Driving License).</li>
                                <li>Check-in counters close 60 minutes prior to domestic flight departure.</li>
                                <li>Boarding gates close 25 minutes before departure time.</li>
                            </ul>
                        </div>

                        <!-- QR CODE & BARCODE -->
                        <div class="col-md-4 text-center border-start ps-md-4">
                            <h6 class="fw-bold mb-3">Scan Code</h6>
                            <div class="p-2 border rounded-3 d-inline-block bg-white mb-2 shadow-sm">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=<?= urlencode($qrData); ?>" alt="E-Ticket QR Code" width="140" height="140">
                            </div>
                            <div class="small text-muted">Booking Reference</div>
                            <div class="fw-bold text-dark font-monospace"><?= htmlspecialchars($booking['booking_id']); ?></div>
                        </div>
                    </div>

                    <!-- FOOTER ACTIONS -->
                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center pt-3 border-top">
                        <a href="mybooking.php" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Back to Bookings
                        </a>
                        <div class="d-flex gap-2">
                            <form action="webcheckin.php" method="POST" class="d-inline">
                                <input type="hidden" name="pnr" value="<?= htmlspecialchars($booking['pnr']); ?>">
                                <button type="submit" class="btn btn-success rounded-pill px-4">
                                    <i class="bi bi-qr-code me-1"></i> Web Check-In
                                </button>
                            </form>
                            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 fw-bold">
                                <i class="bi bi-printer me-1"></i> Print E-Ticket
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
    nav, footer, .btn, .d-flex.flex-wrap.gap-2.justify-content-between {
        display: none !important;
    }
    body {
        background: #fff !important;
    }
    #printableTicket {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>

<?php include 'include/footer.php'; ?>