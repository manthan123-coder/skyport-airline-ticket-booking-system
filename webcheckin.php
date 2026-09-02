<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'include/db_json.php';
require_once 'auth.php';

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

$pnr = trim($_REQUEST['pnr'] ?? '');
$booking = null;

if (!empty($pnr)) {
    $booking = get_booking_by_pnr($pnr);
}

include 'include/header.php';
?>

<div class="container my-4">
    <!-- HERO SECTION -->
    <div class="checkin-hero p-4 p-md-5 mb-4 position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 mb-3 fw-bold">
                    <i class="bi bi-airplane-engines me-1"></i> Express Terminal Check-In
                </span>
                <h1 class="fw-extrabold display-6 mb-2">Web Check-In Portal</h1>
                <p class="lead opacity-90 mb-0">Select your seat, declare baggage, and generate your official digital Boarding Pass in seconds.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="d-inline-flex flex-column align-items-lg-end">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-semibold">
                        <i class="bi bi-clock-history me-1"></i> Open 48h to 60m before flight
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- WIZARD STEP NAVIGATION -->
    <div class="checkin-wizard-nav d-none d-md-flex my-4">
        <div class="wizard-step <?= empty($booking) ? 'active' : 'completed'; ?>">
            <div class="step-icon">
                <?= empty($booking) ? '1' : '<i class="bi bi-check-lg"></i>'; ?>
            </div>
            <span class="step-label">Lookup Booking</span>
        </div>
        <div class="wizard-step <?= !empty($booking) ? 'active' : ''; ?>">
            <div class="step-icon">2</div>
            <span class="step-label">Passenger & Baggage</span>
        </div>
        <div class="wizard-step <?= !empty($booking) ? 'active' : ''; ?>">
            <div class="step-icon">3</div>
            <span class="step-label">Aircraft Seat Map</span>
        </div>
        <div class="wizard-step">
            <div class="step-icon">4</div>
            <span class="step-label">Boarding Pass</span>
        </div>
    </div>

    <?php if (!$booking): ?>
        <!-- ============================================== -->
        <!-- STEP 1: SEARCH PNR FORM & DEMO PRESETS         -->
        <!-- ============================================== -->
        <div class="row justify-content-center my-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-white">
                    <div class="text-center mb-4">
                        <div class="icon-circle bg-primary-subtle text-primary mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.6rem;">
                            <i class="bi bi-qr-code-scan"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">Enter Booking Details</h3>
                        <p class="text-muted">Locate your flight using your 6-character PNR or Booking Reference</p>
                    </div>

                    <?php if (!empty($pnr)): ?>
                        <div class="alert alert-danger rounded-3 d-flex align-items-center gap-2 mb-4">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <div>
                                <strong>Booking Not Found:</strong> No active flight found matching PNR "<strong><?= htmlspecialchars($pnr); ?></strong>". Please verify your PNR or try one of the quick test buttons below.
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="webcheckin.php" class="mb-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">PNR / Booking Reference Number</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-ticket-perforated"></i></span>
                                <input type="text" name="pnr" id="pnr_input" class="form-control bg-light border-start-0 text-uppercase fw-bold text-primary py-3" placeholder="e.g. SKP7DBF1" required value="<?= htmlspecialchars($pnr); ?>" style="letter-spacing: 2px;">
                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                    Continue <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- QUICK DEMO PNR BUTTONS -->
                    <div class="p-3 bg-light rounded-4 border border-dashed mb-4">
                        <span class="text-muted small fw-bold d-block mb-2 text-uppercase">
                            <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Instant Demo Test PNRs:
                        </span>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="pnr-quick-btn" onclick="fillPNR('SKP7DBF1')">
                                <i class="bi bi-airplane me-1"></i> SKP7DBF1 (IndiGo 6E-121)
                            </button>
                            <button type="button" class="pnr-quick-btn" onclick="fillPNR('SKP8238F')">
                                <i class="bi bi-airplane me-1"></i> SKP8238F (IndiGo 6E-804)
                            </button>
                        </div>
                    </div>

                    <!-- CHECKIN FEATURES ACCORDION -->
                    <div class="accordion accordion-flush rounded-3 border" id="checkinFaq">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    <i class="bi bi-bag-check text-primary me-2"></i> What baggage allowances are included?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#checkinFaq">
                                <div class="accordion-body text-muted small">
                                    Standard web check-in includes 15 kg checked baggage allowance + 7 kg cabin handbag. Extra baggage can be declared during check-in.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    <i class="bi bi-shield-exclamation text-warning me-2"></i> Hazardous Materials Guidelines
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#checkinFaq">
                                <div class="accordion-body text-muted small">
                                    Power banks, spare lithium batteries, e-cigarettes, matches, and compressed gases are strictly prohibited in checked baggage. Power banks must be carried in hand baggage only.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- ============================================== -->
        <!-- STEP 2 & 3: DYNAMIC SEAT MAP & CHECKIN FORM    -->
        <!-- ============================================== -->
        <form action="boardingpass.php" method="POST" id="checkinForm">
            <input type="hidden" name="pnr" value="<?= htmlspecialchars($booking['pnr']); ?>">
            <input type="hidden" name="seat_number" id="seat_number_input" value="<?= htmlspecialchars($booking['seat_no'] ?? '12A'); ?>" required>

            <div class="row g-4">
                <!-- LEFT COLUMN: FLIGHT INFO & SEAT SELECTION MAP -->
                <div class="col-lg-7">
                    <!-- BOOKING DETAILS SUMMARY BANNER -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold me-2">
                                    <?= htmlspecialchars($booking['airline_name'] ?? 'SkyPort Airlines'); ?>
                                </span>
                                <span class="fw-bold text-dark fs-5"><?= htmlspecialchars($booking['flight_name']); ?></span>
                            </div>
                            <span class="badge bg-dark fs-6 px-3 py-2">PNR: <?= htmlspecialchars($booking['pnr']); ?></span>
                        </div>

                        <div class="row g-3 p-3 bg-light rounded-3 align-items-center text-center text-md-start">
                            <div class="col-md-5">
                                <div class="small text-muted text-uppercase fw-semibold">From</div>
                                <div class="fs-4 fw-bold text-primary mb-0"><?= htmlspecialchars($booking['from_city']); ?></div>
                                <div class="small text-muted"><i class="bi bi-clock me-1"></i><?= date('H:i', strtotime($booking['departure_time'] ?? '06:00')); ?></div>
                            </div>
                            <div class="col-md-2 text-center my-2 my-md-0">
                                <i class="bi bi-airplane-fill fs-3 text-primary"></i>
                                <div class="small text-muted opacity-75">Direct</div>
                            </div>
                            <div class="col-md-5 text-md-end">
                                <div class="small text-muted text-uppercase fw-semibold">To</div>
                                <div class="fs-4 fw-bold text-primary mb-0"><?= htmlspecialchars($booking['to_city']); ?></div>
                                <div class="small text-muted"><i class="bi bi-clock me-1"></i><?= date('H:i', strtotime($booking['arrival_time'] ?? '08:15')); ?></div>
                            </div>
                        </div>

                        <div class="row g-2 mt-3 small text-muted">
                            <div class="col-6 col-md-4">
                                <i class="bi bi-person-fill text-primary me-1"></i> <strong>Passenger:</strong><br>
                                <span class="text-dark fw-bold"><?= htmlspecialchars(($booking['firstname'] ?? '') . ' ' . ($booking['lastname'] ?? '')); ?></span>
                            </div>
                            <div class="col-6 col-md-4">
                                <i class="bi bi-calendar-event text-primary me-1"></i> <strong>Departure Date:</strong><br>
                                <span class="text-dark fw-bold"><?= date('d M Y', strtotime($booking['departure_date'])); ?></span>
                            </div>
                            <div class="col-6 col-md-4">
                                <i class="bi bi-person-workspace text-primary me-1"></i> <strong>Cabin Class:</strong><br>
                                <span class="text-dark fw-bold"><?= htmlspecialchars($booking['passenger_class'] ?? 'Economy'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- INTERACTIVE 2D CABIN SEAT MAP (CLEAN MODERN STYLING) -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">
                                    <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>Select Aircraft Seat
                                </h5>
                                <small class="text-muted">Interactive Airbus A320 Cabin Layout</small>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-bold">
                                <i class="bi bi-airplane-fill me-1"></i> Airbus A320 (3-3 Layout)
                            </span>
                        </div>

                        <!-- CLEAN SEAT MAP LEGEND -->
                        <div class="d-flex flex-wrap gap-4 justify-content-center bg-light p-3 rounded-4 mb-4 small fw-bold">
                            <div class="d-flex align-items-center gap-2">
                                <span class="seat-legend-dot bg-white border border-secondary"></span> Available
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="seat-legend-dot bg-primary"></span> Selected
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="seat-legend-dot bg-secondary opacity-50"></span> Selected
                            </div>
                        </div>

                        <!-- 2D AIRCRAFT FUSELAGE -->
                        <div class="aircraft-fuselage">
                            <!-- COCKPIT NOSE -->
                            <div class="aircraft-cockpit">
                                <div class="aircraft-cockpit-window"></div>
                                <span class="badge bg-dark text-white opacity-75 fw-semibold px-2 py-1 small">Front Cockpit</span>
                            </div>

                            <!-- COLUMN LABELS (A, B, C | AISLE | D, E, F) -->
                            <div class="d-flex justify-content-center align-items-center gap-1 mb-2 text-muted small fw-extrabold text-center">
                                <div style="width: 26px;"></div>
                                <div style="width: 38px;">A <span class="d-block text-muted opacity-75" style="font-size: 0.6rem;">(Win)</span></div>
                                <div style="width: 38px;">B <span class="d-block text-muted opacity-75" style="font-size: 0.6rem;">(Mid)</span></div>
                                <div style="width: 38px;">C <span class="d-block text-muted opacity-75" style="font-size: 0.6rem;">(Aisle)</span></div>
                                <div style="width: 24px;" class="small">|</div>
                                <div style="width: 38px;">D <span class="d-block text-muted opacity-75" style="font-size: 0.6rem;">(Aisle)</span></div>
                                <div style="width: 38px;">E <span class="d-block text-muted opacity-75" style="font-size: 0.6rem;">(Mid)</span></div>
                                <div style="width: 38px;">F <span class="d-block text-muted opacity-75" style="font-size: 0.6rem;">(Win)</span></div>
                            </div>

                            <!-- SEAT ROWS MAP -->
                            <div class="seat-map-grid my-1">
                                <?php
                                $rows = ['1', '2', '3', '6', '10', '12'];
                                $occupied_seats = ['1B', '2D', '6E', '10B'];
                                $current_selected = $booking['seat_no'] ?? '12A';

                                foreach ($rows as $rowNum):
                                ?>
                                    <?php if ($rowNum === '6'): ?>
                                        <!-- EMERGENCY EXIT ROW INDICATOR -->
                                        <div class="d-flex justify-content-between align-items-center px-1 my-2">
                                            <span class="exit-door-badge"><i class="bi bi-door-open-fill"></i> EXIT</span>
                                            <span class="small fw-bold text-muted" style="font-size: 0.7rem;">EMERGENCY EXIT ROW</span>
                                            <span class="exit-door-badge">EXIT <i class="bi bi-door-open-fill"></i></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="seat-row">
                                        <span class="seat-row-number"><?= $rowNum; ?></span>

                                        <!-- LEFT COLUMN: A, B, C -->
                                        <?php foreach (['A', 'B', 'C'] as $col): 
                                            $seatId = $rowNum . $col;
                                            $isOccupied = in_array($seatId, $occupied_seats);
                                            $isSelected = ($seatId === $current_selected);
                                            
                                            $classExtra = '';
                                            if ($isOccupied) $classExtra .= ' occupied';
                                            if ($isSelected) $classExtra .= ' selected';
                                            $seatType = ($col === 'A') ? 'Window' : (($col === 'C') ? 'Aisle' : 'Middle');
                                        ?>
                                            <div class="seat-item <?= $classExtra; ?>" 
                                                 data-seat="<?= $seatId; ?>" 
                                                 data-type="<?= $seatType; ?>" 
                                                 onclick="selectSeat(this, '<?= $seatId; ?>', '<?= $seatType; ?>', 'No')">
                                                <span><?= $col; ?></span>
                                            </div>
                                        <?php endforeach; ?>

                                        <!-- AISLE -->
                                        <div class="seat-aisle">|</div>

                                        <!-- RIGHT COLUMN: D, E, F -->
                                        <?php foreach (['D', 'E', 'F'] as $col): 
                                            $seatId = $rowNum . $col;
                                            $isOccupied = in_array($seatId, $occupied_seats);
                                            $isSelected = ($seatId === $current_selected);
                                            
                                            $classExtra = '';
                                            if ($isOccupied) $classExtra .= ' occupied';
                                            if ($isSelected) $classExtra .= ' selected';
                                            $seatType = ($col === 'F') ? 'Window' : (($col === 'D') ? 'Aisle' : 'Middle');
                                        ?>
                                            <div class="seat-item <?= $classExtra; ?>" 
                                                 data-seat="<?= $seatId; ?>" 
                                                 data-type="<?= $seatType; ?>" 
                                                 onclick="selectSeat(this, '<?= $seatId; ?>', '<?= $seatType; ?>', 'No')">
                                                <span><?= $col; ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: SEAT SUMMARY & BAGGAGE -->
                <div class="col-lg-5">

                    <!-- CURRENT SELECTION DISPLAY CARD -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 text-white mb-4" style="background: linear-gradient(135deg, #022567 0%, #1e40af 100%);">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-white-50 small fw-bold text-uppercase">SELECTED SEAT</span>
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold" id="preview_seat_type">
                                <?= strpos($current_selected, 'A') !== false || strpos($current_selected, 'F') !== false ? 'Window Seat' : 'Standard Seat'; ?>
                            </span>
                        </div>
                        <div class="d-flex align-items-baseline gap-3 mb-2">
                            <div class="display-4 fw-extrabold text-white" id="preview_seat_num"><?= htmlspecialchars($current_selected); ?></div>
                            <div class="small opacity-90" id="preview_seat_desc">Standard Assigned Seat</div>
                        </div>
                        <div class="border-top border-white-50 pt-2 text-white-50 small d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle-fill text-warning"></i>
                            <span>Seat selection confirmed & saved into Boarding Pass QR code.</span>
                        </div>
                    </div>

                    <!-- BAGGAGE & ADD-ONS SELECTION -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="bi bi-luggage-fill me-2" style="color: #022567;"></i>Baggage & In-Flight Extras
                        </h6>

                        <!-- BAGGAGE ALLOWANCE -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Checked Baggage Allowance</label>
                            <select name="baggage_count" class="form-select bg-light border-0 py-2">
                                <option value="15kg Included">1 Bag (15 kg) — Included (Free)</option>
                                <option value="20kg (+ $10)">1 Bag (20 kg) — Extended (+ $10)</option>
                                <option value="25kg (+ $20)">2 Bags (25 kg Total) — Heavy (+ $20)</option>
                            </select>
                        </div>

                        <!-- MEAL SELECTION -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">In-Flight Meal Preference</label>
                            <select name="meal_type" class="form-select bg-light border-0 py-2">
                                <option value="Veg Meal" <?= ($booking['meal_type'] ?? '') === 'Veg Meal' ? 'selected' : ''; ?>>Veg Combo & Beverage</option>
                                <option value="Non-Veg Meal" <?= ($booking['meal_type'] ?? '') === 'Non-Veg Meal' ? 'selected' : ''; ?>>Non-Veg Classic & Beverage</option>
                                <option value="Jain Special" <?= ($booking['meal_type'] ?? '') === 'Jain Meal' ? 'selected' : ''; ?>>Jain Special Meal</option>
                                <option value="Fruit Platter">Fresh Fruit Platter</option>
                                <option value="No Meal" <?= ($booking['meal_type'] ?? '') === 'None' ? 'selected' : ''; ?>>No Meal</option>
                            </select>
                        </div>

                        <!-- SPECIAL ASSISTANCE TOGGLE -->
                        <div class="form-check form-switch p-3 bg-light rounded-3 mb-0">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="wheelchair" value="Yes" id="wheelchairCheck" <?= ($booking['wheelchair'] ?? '') === 'Yes' ? 'checked' : ''; ?>>
                            <label class="form-check-input-label fw-semibold text-dark small" for="wheelchairCheck">
                                Request Wheelchair / Special Assistance
                            </label>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="d-grid gap-3">
                        <button type="submit" class="btn btn-success btn-lg rounded-pill py-3 fw-bold shadow-lg">
                            <i class="bi bi-check-circle-fill me-2"></i> Confirm Check-In & Get Boarding Pass
                        </button>
                        <a href="mybooking.php" class="btn btn-outline-secondary rounded-pill py-2.5 text-center fw-semibold w-100">
                            Cancel & Return to Bookings
                        </a>
                    </div>

                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
function fillPNR(pnrCode) {
    var pnrInput = document.getElementById('pnr_input');
    if (pnrInput) {
        pnrInput.value = pnrCode;
        var form = pnrInput.closest('form') || document.querySelector('form');
        if (form) form.submit();
    }
}

function selectSeat(element, seatId, seatType) {
    if (element.classList.contains('occupied')) {
        alert('Seat ' + seatId + ' is already occupied by another passenger. Please select another seat.');
        return;
    }

    // Unselect all other seats
    var allSeats = document.querySelectorAll('.seat-item');
    allSeats.forEach(function(s) {
        s.classList.remove('selected');
    });

    // Select clicked seat
    element.classList.add('selected');

    // Update hidden input
    document.getElementById('seat_number_input').value = seatId;

    // Update preview card
    document.getElementById('preview_seat_num').innerText = seatId;
    document.getElementById('preview_seat_type').innerText = seatType + ' Seat';
    
    var descText = 'Standard Assigned Seat';
    if (seatType === 'Window') {
        descText = 'Window View Seat';
    } else if (seatType === 'Aisle') {
        descText = 'Aisle Seat — Easy Access';
    } else if (seatType === 'Middle') {
        descText = 'Middle Seat';
    }
    document.getElementById('preview_seat_desc').innerText = descText;
}
</script>

<?php include 'include/footer.php'; ?>
