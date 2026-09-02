<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'include/db_json.php';

$pnr = $_SESSION['pnr'] ?? $_GET['pnr'] ?? '';
$booking = get_booking_by_pnr($pnr);

if (!$booking) {
    $booking = [
        'booking_id'     => $_SESSION['booking_id'] ?? ('BK' . date('YmdHis')),
        'pnr'            => $pnr ?: 'SKP982',
        'flight_name'    => $_SESSION['flight_name'] ?? '6E-204',
        'airline_name'   => $_SESSION['airline_name'] ?? 'IndiGo',
        'from_city'      => $_SESSION['from_city'] ?? 'Delhi',
        'to_city'        => $_SESSION['to_city'] ?? 'Mumbai',
        'departure_date' => $_SESSION['departure_date'] ?? date('Y-m-d'),
        'return_date'    => $_SESSION['return_date'] ?? '',
        'firstname'      => $_SESSION['firstname'] ?? 'Guest',
        'lastname'       => $_SESSION['lastname'] ?? 'Passenger',
        'email'          => $_SESSION['email'] ?? 'guest@example.com',
        'phone'          => $_SESSION['number'] ?? '+91 9876543210',
        'amount'         => $_SESSION['price'] ?? '4500',
        'payment_status' => 'Success',
        'departure_time' => $_SESSION['departure_time'] ?? '06:00',
        'arrival_time'   => $_SESSION['arrival_time'] ?? '08:15',
        'checkin_status' => 'Not Checked-in'
    ];
}

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

$from_city = $booking['from_city'] ?? 'Delhi';
$to_city = $booking['to_city'] ?? 'Mumbai';
$from_code = $city_codes[$from_city] ?? strtoupper(substr($from_city, 0, 3));
$to_code = $city_codes[$to_city] ?? strtoupper(substr($to_city, 0, 3));

$airline_logos = [
    'IndiGo' => 'logo/indigo.png',
    'Air India' => 'logo/airindia.webp',
    'Air India Express' => 'logo/airindiaexpress.png',
    'SpiceJet' => 'logo/Spicejet.png',
    'Akasa Air' => 'logo/akasa.webp'
];

$airline = $booking['airline_name'] ?? 'IndiGo';
$logo_path = $airline_logos[$airline] ?? 'logo/indigo.png';
$passenger_name = trim(($booking['firstname'] ?? '') . ' ' . ($booking['lastname'] ?? ''));
if (empty($passenger_name)) $passenger_name = 'Traveler';

include 'include/header.php';
?>

<style>
    .confirm-wrapper {
        min-height: calc(100vh - 120px);
        background: #f4f7fc;
        background-image: 
            radial-gradient(circle at 10% 10%, rgba(13, 110, 253, 0.05) 0%, transparent 35%),
            radial-gradient(circle at 90% 90%, rgba(16, 185, 129, 0.05) 0%, transparent 35%);
        padding: 40px 0 60px 0;
    }
    .confirm-card {
        border-radius: 28px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 25px 70px rgba(15, 23, 42, 0.12);
        background: #ffffff;
        overflow: hidden;
        position: relative;
    }
    .confirm-hero {
        background: linear-gradient(135deg, #0b192c 0%, #0f2b5c 50%, #1e3a8a 100%);
        color: #ffffff;
        padding: 48px 36px 42px 36px;
        position: relative;
        overflow: hidden;
    }
    .confirm-hero::after {
        content: '';
        position: absolute;
        right: -80px;
        bottom: -90px;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 0 0 45px rgba(255, 255, 255, 0.04);
        pointer-events: none;
    }
    .success-icon-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.18);
        border: 2px solid rgba(52, 211, 153, 0.4);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #34d399;
        font-size: 2.5rem;
        box-shadow: 0 0 30px rgba(16, 185, 129, 0.3);
        animation: pulse-ring 2.5s infinite;
    }
    @keyframes pulse-ring {
        0% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.4); }
        70% { box-shadow: 0 0 0 20px rgba(52, 211, 153, 0); }
        100% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
    }
    .pnr-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        padding: 20px 24px;
    }
    .pnr-code {
        font-family: 'Outfit', sans-serif;
        font-size: 1.8rem;
        font-weight: 800;
        letter-spacing: 2px;
        color: #0d6efd;
    }
    .copy-btn {
        background: #e0edff;
        color: #0d6efd;
        border: 0;
        border-radius: 12px;
        padding: 6px 14px;
        font-size: 0.82rem;
        font-weight: 700;
        transition: all 0.2s ease;
    }
    .copy-btn:hover {
        background: #0d6efd;
        color: #ffffff;
    }
    .ticket-body {
        position: relative;
        background: #ffffff;
        padding: 36px 40px;
    }
    .ticket-stub {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 24px 28px;
        position: relative;
    }
    .airline-logo-img {
        max-height: 38px;
        max-width: 130px;
        object-fit: contain;
    }
    .route-visual {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin: 20px 0;
    }
    .city-block h3 {
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0;
        color: #0f172a;
    }
    .route-line-container {
        flex: 1;
        position: relative;
        text-align: center;
    }
    .route-line {
        height: 2px;
        background: linear-gradient(90deg, #cbd5e1 0%, #0d6efd 50%, #cbd5e1 100%);
        width: 100%;
        position: relative;
        top: 12px;
    }
    .plane-icon-badge {
        position: relative;
        z-index: 2;
        background: #0d6efd;
        color: #ffffff;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }
    .info-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 24px;
    }
    .info-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 20px;
    }
    .info-label {
        font-size: 0.73rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 4px;
    }
    .info-value {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
    }
    .action-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: center;
        margin-top: 36px;
    }
    .btn-action-main {
        padding: 14px 28px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 0;
    }
    .btn-action-main:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #dcfce7;
        color: #15803d;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 5px 14px;
        border-radius: 30px;
    }
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #16a34a;
        display: inline-block;
    }
</style>

<div class="confirm-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">

                <div class="confirm-card">
                    
                    <!-- HERO HEADER -->
                    <div class="confirm-hero text-center">
                        <div class="success-icon-wrapper mb-3">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-15 text-white border border-white border-opacity-25 px-3 py-2 rounded-pill fw-semibold mb-3">
                            <i class="bi bi-shield-check me-1 text-success"></i> Payment Successful & Booking Confirmed
                        </span>
                        <h1 class="fw-bold display-6 mb-2">You're Ready for Take-Off!</h1>
                        <p class="text-white-50 max-w-lg mx-auto mb-0" style="max-width: 580px; font-size: 0.98rem;">
                            Your ticket reservation has been booked. Have a pleasant flight!
                        </p>
                    </div>

                    <!-- TICKET BODY -->
                    <div class="ticket-body">
                        
                        <!-- PNR & REF ROW -->
                        <div class="pnr-box d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                            <div>
                                <div class="text-muted small fw-semibold text-uppercase">PNR Number (Booking Reference)</div>
                                <div class="d-flex align-items-center gap-3 mt-1">
                                    <span class="pnr-code" id="pnrText"><?= htmlspecialchars($booking['pnr']); ?></span>
                                    <button class="copy-btn" onclick="copyPNR()" title="Copy PNR to clipboard">
                                        <i class="bi bi-clipboard me-1" id="copyIcon"></i> <span id="copyText">Copy</span>
                                    </button>
                                </div>
                            </div>
                            <div class="text-start text-md-end">
                                <div class="status-pill mb-1">
                                    <span class="status-dot"></span> Confirmed & Issued
                                </div>
                                <div class="small text-muted">Ref ID: <strong class="text-dark"><?= htmlspecialchars($booking['booking_id']); ?></strong></div>
                            </div>
                        </div>

                        <!-- FLIGHT TICKET STUB -->
                        <div class="ticket-stub mb-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <?php if (file_exists(__DIR__ . '/' . $logo_path)): ?>
                                        <img src="<?= htmlspecialchars($logo_path); ?>" alt="<?= htmlspecialchars($airline); ?>" class="airline-logo-img">
                                    <?php else: ?>
                                        <span class="fs-4 fw-bold text-primary"><i class="bi bi-airplane-fill"></i> <?= htmlspecialchars($airline); ?></span>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($airline); ?> &bull; <?= htmlspecialchars($booking['flight_name']); ?></div>
                                        <div class="small text-muted">Economy Class &bull; Non-Stop</div>
                                    </div>
                                </div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-bold">
                                    Confirmed
                                </span>
                            </div>

                            <!-- ROUTE VISUAL -->
                            <div class="route-visual">
                                <div class="city-block text-start">
                                    <div class="text-primary fw-bold small text-uppercase">DEPARTURE</div>
                                    <h3><?= htmlspecialchars($from_code); ?></h3>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($from_city); ?></div>
                                    <div class="small text-muted"><i class="bi bi-clock me-1"></i><?= date('H:i', strtotime($booking['departure_time'] ?? '06:00')); ?></div>
                                </div>

                                <div class="route-line-container">
                                    <div class="small text-muted fw-semibold mb-1">Direct Flight</div>
                                    <div class="route-line"></div>
                                    <div class="plane-icon-badge">
                                        <i class="bi bi-airplane-fill"></i>
                                    </div>
                                    <div class="small text-primary fw-bold mt-1"><?= date('d M Y', strtotime($booking['departure_date'])); ?></div>
                                </div>

                                <div class="city-block text-end">
                                    <div class="text-primary fw-bold small text-uppercase">ARRIVAL</div>
                                    <h3><?= htmlspecialchars($to_code); ?></h3>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($to_city); ?></div>
                                    <div class="small text-muted"><i class="bi bi-clock me-1"></i><?= date('H:i', strtotime($booking['arrival_time'] ?? '08:15')); ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- PASSENGER & PAYMENT SUMMARY GRID -->
                        <div class="info-card-grid">
                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-person me-1 text-primary"></i> Passenger Name</div>
                                <div class="info-value text-truncate"><?= htmlspecialchars($passenger_name); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-envelope me-1 text-primary"></i> Email Address</div>
                                <div class="info-value text-truncate" style="font-size: 0.95rem;"><?= htmlspecialchars($booking['email']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-telephone me-1 text-primary"></i> Contact Phone</div>
                                <div class="info-value"><?= htmlspecialchars($booking['phone']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-currency-rupee me-1 text-primary"></i> Total Paid</div>
                                <div class="info-value text-success">₹<?= number_format(floatval($booking['amount'])); ?></div>
                            </div>
                        </div>

                        <!-- LIVE QR CODE & NOTICE -->
                        <div class="mt-4 p-3 bg-light rounded-4 border d-flex flex-wrap align-items-center gap-3">
                            <div class="bg-white p-2 rounded-3 border shadow-sm flex-shrink-0">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=<?= urlencode('SkyPort PNR:' . $booking['pnr'] . '|Passenger:' . $passenger_name); ?>" alt="E-Ticket QR" width="90" height="90" class="rounded">
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-qr-code-scan me-1 text-primary"></i> Quick Airport Gate Pass</h6>
                                <p class="text-muted small mb-0">Show this QR code at airport security or proceed to <strong>Web Check-In</strong> to select your seat and download your official boarding pass.</p>
                            </div>
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="action-bar d-flex flex-wrap align-items-center justify-content-center gap-3 mt-4">
                            <form action="viewticket.php" method="POST" class="d-inline m-0">
                                <input type="hidden" name="pnr" value="<?= htmlspecialchars($booking['pnr']); ?>">
                                <button type="submit" class="btn btn-primary btn-action-main shadow-sm">
                                    <i class="bi bi-printer-fill"></i> View & Print E-Ticket
                                </button>
                            </form>

                            <form action="webcheckin.php" method="POST" class="d-inline m-0">
                                <input type="hidden" name="pnr" value="<?= htmlspecialchars($booking['pnr']); ?>">
                                <button type="submit" class="btn btn-success btn-action-main shadow-sm" style="background: #10b981; color: #fff; border: none;">
                                    <i class="bi bi-qr-code"></i> Proceed to Web Check-In
                                </button>
                            </form>

                            <a href="mybooking.php" class="btn btn-outline-dark btn-action-main shadow-sm" style="border: 2px solid #334155; color: #334155; background: #ffffff;">
                                <i class="bi bi-card-checklist"></i> My Bookings
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function copyPNR() {
    const pnr = document.getElementById('pnrText').innerText;
    navigator.clipboard.writeText(pnr).then(() => {
        const copyText = document.getElementById('copyText');
        const copyIcon = document.getElementById('copyIcon');
        copyText.innerText = 'Copied!';
        copyIcon.className = 'bi bi-check2-all me-1';
        setTimeout(() => {
            copyText.innerText = 'Copy';
            copyIcon.className = 'bi bi-clipboard me-1';
        }, 2000);
    });
}
</script>

<?php include 'include/footer.php'; ?>