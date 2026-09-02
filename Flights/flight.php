<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'include/db_json.php';
include 'include/header.php';

// Capture parameters from GET or POST
$from_city = $_REQUEST['from_city'] ?? $_SESSION['from_city'] ?? '';
$to_city = $_REQUEST['to_city'] ?? $_SESSION['to_city'] ?? '';
$departure_date = $_REQUEST['departure_date'] ?? $_SESSION['departure_date'] ?? date('Y-m-d');
$return_date = $_REQUEST['return_date'] ?? $_SESSION['return_date'] ?? '';
$trip_type = $_REQUEST['trip_type'] ?? $_SESSION['trip_type'] ?? 'oneway';
$passenger_class = $_REQUEST['passenger_class'] ?? $_SESSION['passenger_class'] ?? '1 Adult, Economy';
$filter_airline = $_REQUEST['airline'] ?? '';
$max_price = $_REQUEST['max_price'] ?? 0;

// Save to Session
$_SESSION['from_city'] = $from_city;
$_SESSION['to_city'] = $to_city;
$_SESSION['departure_date'] = $departure_date;
$_SESSION['return_date'] = $return_date;
$_SESSION['trip_type'] = $trip_type;
$_SESSION['passenger_class'] = $passenger_class;

// Calculate Passenger Count & Cabin Class Multipliers
preg_match('/(\d+)\s*Adult/i', $passenger_class, $mAdult);
$adults_count = isset($mAdult[1]) ? max(1, intval($mAdult[1])) : 1;

preg_match('/(\d+)\s*Child/i', $passenger_class, $mChild);
$children_count = isset($mChild[1]) ? intval($mChild[1]) : 0;

$total_passenger_count = $adults_count + $children_count;

$class_multiplier = 1.0;
if (stripos($passenger_class, 'Business') !== false) {
    $class_multiplier = 1.5;
} elseif (stripos($passenger_class, 'First Class') !== false) {
    $class_multiplier = 2.0;
}

$total_fare_multiplier = ($adults_count + ($children_count * 0.75)) * $class_multiplier;

$_SESSION['adults_count'] = $adults_count;
$_SESSION['children_count'] = $children_count;
$_SESSION['total_passengers'] = $total_passenger_count;
$_SESSION['total_fare_multiplier'] = $total_fare_multiplier;

// Search Flights from JSON


$flights = search_flights($from_city, $to_city, $departure_date, $return_date, $trip_type, $filter_airline, $max_price);
// Load reverse-route flights for the return leg of a round trip.
$return_flights = [];
if ($trip_type === 'roundtrip' && !empty($return_date) && !empty($from_city) && !empty($to_city)) {
    $return_flights = search_flights(
        $to_city,
        $from_city,
        $return_date,
        '',
        'oneway',
        $filter_airline,
        $max_price
    );
}
// If search returned empty because cities weren't selected, show all available flights as recommendations
$showing_all = false;
if (empty($flights) && empty($from_city) && empty($to_city)) {
    $flights = get_all_flights();
    $showing_all = true;
}

?>

<div class="container my-4">
    <!-- SEARCH SUMMARY BAR -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-light">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="fw-bold mb-1">
                    <?php if ($showing_all): ?>
                        <i class="bi bi-plane me-2 text-primary"></i>All Available Flights
                    <?php else: ?>
                        <i class="bi bi-geo-alt-fill me-1 text-primary"></i><?= htmlspecialchars($from_city ?: 'Origin'); ?> 
                        <i class="bi bi-arrow-right mx-2 text-muted"></i> 
                        <?= htmlspecialchars($to_city ?: 'Destination'); ?>
                    <?php endif; ?>
                </h5>
                <small class="text-muted">
                    Date: <strong><?= date('d M Y', strtotime($departure_date)); ?></strong> 
                    <?php if ($trip_type === 'roundtrip' && !empty($return_date)): ?>
                        | Return: <strong><?= date('d M Y', strtotime($return_date)); ?></strong>
                    <?php endif; ?>
                    | Passengers: <strong><?= htmlspecialchars($passenger_class); ?></strong>
                </small>
            </div>
            <a href="index.php#search" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-pencil-square me-1"></i> Modify Search
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- FILTER SIDEBAR -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2 text-primary"></i>Filter Flights</h6>
                <form method="GET" action="flight.php">
                    <input type="hidden" name="from_city" value="<?= htmlspecialchars($from_city); ?>">
                    <input type="hidden" name="to_city" value="<?= htmlspecialchars($to_city); ?>">
                    <input type="hidden" name="departure_date" value="<?= htmlspecialchars($departure_date); ?>">
                    <input type="hidden" name="return_date" value="<?= htmlspecialchars($return_date); ?>">
                    <input type="hidden" name="trip_type" value="<?= htmlspecialchars($trip_type); ?>">

                    <!-- Airline Filter -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Airline</label>
                        <select name="airline" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Airlines</option>
                            <option value="IndiGo" <?= $filter_airline === 'IndiGo' ? 'selected' : ''; ?>>IndiGo</option>
                            <option value="Air India" <?= $filter_airline === 'Air India' ? 'selected' : ''; ?>>Air India</option>
                            <option value="SpiceJet" <?= $filter_airline === 'SpiceJet' ? 'selected' : ''; ?>>SpiceJet</option>
                            <option value="Akasa Air" <?= $filter_airline === 'Akasa Air' ? 'selected' : ''; ?>>Akasa Air</option>
                            <option value="Air India Express" <?= $filter_airline === 'Air India Express' ? 'selected' : ''; ?>>Air India Express</option>
                        </select>
                    </div>

                    <!-- Max Price Filter -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Max Price: ₹<?= $max_price > 0 ? number_format($max_price) : '10,000+'; ?></label>
                        <input type="range" name="max_price" class="form-range" min="2000" max="10000" step="500" value="<?= $max_price > 0 ? $max_price : 10000; ?>" onchange="this.form.submit()">
                    </div>

                    <a href="flight.php" class="btn btn-sm btn-link text-decoration-none p-0 text-muted">Reset Filters</a>
                </form>
            </div>
        </div>

        <!-- FLIGHT LISTING -->
        <div class="col-lg-9">
            <?php if (empty($flights)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-3">
                    <div class="fs-1 text-muted mb-3"><i class="bi bi-airplane text-warning"></i></div>
                    <h5 class="fw-bold">No Direct Flights Found</h5>
                    <p class="text-muted mb-4">We couldn't find flights matching your exact cities. Try searching for different cities or dates.</p>
                    <div>
                        <a href="index.php" class="btn btn-primary px-4 rounded-pill">Search Other Routes</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Showing <strong><?= count($flights); ?></strong> flights</span>
                </div>

                <?php foreach ($flights as $index => $flight): 
                    $collapseId = "flightDetail_" . $flight['id'];
                    $logo = !empty($flight['logo']) ? $flight['logo'] : '../logo/indigo.png';
                    $returnFlight = !empty($return_flights) ? $return_flights[$index % count($return_flights)] : null;
                    $baseSinglePrice = $flight['price'] + ($returnFlight['price'] ?? 0);
                    $totalPrice = round($baseSinglePrice * $total_fare_multiplier);
                ?>
                    <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
                        <div class="card-body p-4">
                           ```php
<div class="row g-3 align-items-stretch">
    <div class="col-lg-9">
        <!-- Departure leg -->
        <div class="mb-3">
            <div class="small fw-bold text-primary text-uppercase mb-2">
                <i class="bi bi-airplane-engines me-1"></i>
                Departure &middot; <?= date('d M', strtotime($departure_date)); ?>
            </div>
            <div class="row align-items-center g-2">
                <div class="col-md-3 d-flex align-items-center gap-2">
                    <img src="<?= htmlspecialchars($logo); ?>" alt="<?= htmlspecialchars($flight['airline_name']); ?>" style="width:42px;height:42px;object-fit:contain" onerror="this.src='logo/indigo.png'">
                    <div><div class="fw-bold small"><?= htmlspecialchars($flight['airline_name']); ?></div><div class="small text-muted"><?= htmlspecialchars($flight['flight_name']); ?></div></div>
                </div>
                <div class="col-4 col-md-2 text-center"><div class="fs-5 fw-bold"><?= date('H:i', strtotime($flight['departure_time'])); ?></div><div class="small text-muted"><?= htmlspecialchars($flight['departure_city']); ?></div></div>
                <div class="col-4 col-md-3 text-center"><div class="small text-muted"><?= htmlspecialchars($flight['duration']); ?></div><div class="position-relative d-flex align-items-center justify-content-center"><div class="w-100 bg-secondary opacity-25" style="height:2px"></div><i class="bi bi-airplane-fill position-absolute text-primary bg-white px-2"></i></div><div class="small text-success fw-semibold"><?= htmlspecialchars($flight['stops']); ?></div></div>
                <div class="col-4 col-md-2 text-center"><div class="fs-5 fw-bold"><?= date('H:i', strtotime($flight['arrival_time'])); ?></div><div class="small text-muted"><?= htmlspecialchars($flight['arrival_city']); ?></div></div>
            </div>
        </div>

        <!-- Return leg -->
        <?php if ($returnFlight): ?>
            <div class="border-top pt-3">
                <div class="small fw-bold text-primary text-uppercase mb-2">
                    <i class="bi bi-airplane-engines-fill me-1"></i>
                    Return &middot; <?= date('d M', strtotime($return_date)); ?>
                </div>
                <div class="row align-items-center g-2">
                    <div class="col-md-3 d-flex align-items-center gap-2">
                        <img src="<?= htmlspecialchars($returnFlight['logo'] ?? $logo); ?>" alt="<?= htmlspecialchars($returnFlight['airline_name']); ?>" style="width:42px;height:42px;object-fit:contain" onerror="this.src='logo/indigo.png'">
                        <div><div class="fw-bold small"><?= htmlspecialchars($returnFlight['airline_name']); ?></div><div class="small text-muted"><?= htmlspecialchars($returnFlight['flight_name']); ?></div></div>
                    </div>
                    <div class="col-4 col-md-2 text-center"><div class="fs-5 fw-bold"><?= date('H:i', strtotime($returnFlight['departure_time'])); ?></div><div class="small text-muted"><?= htmlspecialchars($returnFlight['departure_city']); ?></div></div>
                    <div class="col-4 col-md-3 text-center"><div class="small text-muted"><?= htmlspecialchars($returnFlight['duration']); ?></div><div class="position-relative d-flex align-items-center justify-content-center"><div class="w-100 bg-secondary opacity-25" style="height:2px"></div><i class="bi bi-airplane-fill position-absolute text-primary bg-white px-2"></i></div><div class="small text-success fw-semibold"><?= htmlspecialchars($returnFlight['stops']); ?></div></div>
                    <div class="col-4 col-md-2 text-center"><div class="fs-5 fw-bold"><?= date('H:i', strtotime($returnFlight['arrival_time'])); ?></div><div class="small text-muted"><?= htmlspecialchars($returnFlight['arrival_city']); ?></div></div>
                </div>
            </div>
        <?php elseif ($trip_type === 'roundtrip'): ?>
            <div class="border-top pt-3 small text-warning"><i class="bi bi-exclamation-circle me-1"></i>Return flight is not available for this route.</div>
        <?php endif; ?>
    </div>

    <div class="col-lg-3 text-lg-end text-center border-start">
        <div class="fs-4 fw-bold text-primary">₹<?= number_format($totalPrice); ?></div>
        <div class="small text-muted mb-2"><?= $returnFlight ? 'round trip per passenger' : 'per passenger'; ?></div>
        <form action="detail.php" method="POST">
            <input type="hidden" name="flight_id" value="<?= $flight['id']; ?>">
            <input type="hidden" name="flight_name" value="<?= htmlspecialchars($flight['flight_name']); ?>">
            <input type="hidden" name="airline_name" value="<?= htmlspecialchars($flight['airline_name']); ?>">
            <input type="hidden" name="price" value="<?= $totalPrice; ?>">
            <input type="hidden" name="departure_time" value="<?= htmlspecialchars($flight['departure_time']); ?>">
            <input type="hidden" name="arrival_time" value="<?= htmlspecialchars($flight['arrival_time']); ?>">
            <input type="hidden" name="from_city" value="<?= htmlspecialchars($flight['departure_city']); ?>">
            <input type="hidden" name="to_city" value="<?= htmlspecialchars($flight['arrival_city']); ?>">
            <input type="hidden" name="duration" value="<?= htmlspecialchars($flight['duration']); ?>">
            <?php if ($returnFlight): ?><input type="hidden" name="return_flight_id" value="<?= $returnFlight['id']; ?>"><?php endif; ?>
            <button type="submit" class="btn btn-primary btn-sm px-3 rounded-pill fw-bold w-100">Book Now</button>
        </form>
    </div>
</div>

                            <hr class="my-3 opacity-25">

                            <!-- Accordion Toggle -->
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted"><i class="bi bi-calendar-event me-1"></i> <?= date('d M Y', strtotime($departure_date)); ?></small>
                                <a class="small text-decoration-none fw-semibold" data-bs-toggle="collapse" href="#<?= $collapseId; ?>" role="button">
                                    Flight Details <i class="bi bi-chevron-down ms-1"></i>
                                </a>
                            </div>

                            <!-- Collapse Details -->
                            <div class="collapse mt-3" id="<?= $collapseId; ?>">
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="row g-2 small">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Airline:</strong> <?= htmlspecialchars($flight['airline_name']); ?> (<?= htmlspecialchars($flight['flight_name']); ?>)</p>
                                            <p class="mb-1"><strong>Aircraft:</strong> <?= htmlspecialchars($flight['aircraft']); ?></p>
                                            <p class="mb-0"><strong>Seats Available:</strong> <?= htmlspecialchars($flight['seats_available']); ?> seats</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Cabin Baggage:</strong> 7 KG Included</p>
                                            <p class="mb-1"><strong>Check-in Baggage:</strong> 15 KG Included</p>
                                            <p class="mb-0"><strong>Cancellation:</strong> Refundable per airline policy</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>
