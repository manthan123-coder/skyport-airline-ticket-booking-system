<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'include/db_json.php';
include 'include/header.php';

// Capture parameters from GET or POST
$from_city = isset($_REQUEST['from_city']) ? trim($_REQUEST['from_city']) : '';
$to_city = isset($_REQUEST['to_city']) ? trim($_REQUEST['to_city']) : '';
$departure_date = !empty($_REQUEST['departure_date']) ? $_REQUEST['departure_date'] : date('Y-m-d');
$return_date = $_REQUEST['return_date'] ?? '';
$trip_type = $_REQUEST['trip_type'] ?? 'oneway';
$passenger_class = $_REQUEST['passenger_class'] ?? '1 Adult, Economy';
$filter_airline = $_REQUEST['airline'] ?? '';
$max_price = $_REQUEST['max_price'] ?? 0;

// Save to Session
if (!empty($from_city)) $_SESSION['from_city'] = $from_city;
if (!empty($to_city)) $_SESSION['to_city'] = $to_city;
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

// Weighted passenger multiplier (Adult = 1.0, Child = 0.75) * Class Multiplier
$total_fare_multiplier = ($adults_count + ($children_count * 0.75)) * $class_multiplier;

$_SESSION['adults_count'] = $adults_count;
$_SESSION['children_count'] = $children_count;
$_SESSION['total_passengers'] = $total_passenger_count;
$_SESSION['total_fare_multiplier'] = $total_fare_multiplier;

$is_search_active = (!empty($from_city) || !empty($to_city));

// Search Flights from JSON / Dynamic Guarantee
if ($is_search_active) {
    $flights = search_flights($from_city, $to_city, $departure_date, $return_date, $trip_type, $filter_airline, 0);
} else {
    $flights = get_all_flights();
}

// Filter out past departure time flights if departure date is Today or earlier
$dep_ts = !empty($departure_date) ? strtotime($departure_date) : time();
$dep_day = date('Y-m-d', $dep_ts);
$today_day = date('Y-m-d');
if ($dep_day <= $today_day) {
    $cur_h_m = date('H:i');
    $flights = array_values(array_filter($flights, function($fl) use ($cur_h_m) {
        if (empty($fl['departure_time'])) return true;
        $fl_dep = date('H:i', strtotime($fl['departure_time']));
        return $fl_dep >= $cur_h_m;
    }));
}

// For a round trip, find flights travelling back on the return date.
$return_flights = [];
if ($is_search_active && $trip_type === 'roundtrip' && !empty($return_date) && !empty($from_city) && !empty($to_city)) {
    $return_flights = search_flights(
        $to_city,
        $from_city,
        $return_date,
        '',
        'oneway',
        $filter_airline,
        0
    );
}

// Dynamically compute Slider Max and Min prices based on calculated flight fares
$all_calculated_prices = [];
foreach ($flights as $index => $flight) {
    $rFlight = null;
    foreach ($return_flights as $candidate) {
        if (strcasecmp($candidate['airline_name'], $flight['airline_name']) === 0) {
            $rFlight = $candidate;
            break;
        }
    }
    if ($rFlight === null && !empty($return_flights)) {
        $rFlight = $return_flights[$index % count($return_flights)];
    }
    $bPrice = $flight['price'] + ($rFlight['price'] ?? 0);
    $all_calculated_prices[] = round($bPrice * $total_fare_multiplier);
}

if (!empty($all_calculated_prices)) {
    $highest_flight_price = max($all_calculated_prices);
    $lowest_flight_price = min($all_calculated_prices);
    
    $slider_max = intval($highest_flight_price);
    $slider_min = intval($lowest_flight_price);
    if ($slider_min >= $slider_max) {
        $slider_min = max(1000, $slider_max - 1500);
    }
} else {
    $slider_min = 2000;
    $slider_max = 50000;
}
?>

<div class="container my-4">

    <!-- MODERN GLASS HERO HEADER -->
    <div class="flight-hero-modern p-4 p-md-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="flight-hero-badge mb-3">
                    <i class="bi bi-airplane-fill text-warning"></i>
                    <span>Real-Time Airline Schedule &bull; Verified Availability</span>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                    <?php if ($is_search_active): ?>
                        <div class="route-pill-display">
                            <span class="route-pill-city"><?= htmlspecialchars($from_city ?: 'Any City'); ?></span>
                            <i class="bi bi-arrow-right route-pill-arrow"></i>
                            <span class="route-pill-city"><?= htmlspecialchars($to_city ?: 'Any City'); ?></span>
                        </div>
                    <?php else: ?>
                        <h1 class="h2 fw-bold mb-0 text-white"><i class="bi bi-compass me-2 text-info"></i>All Available Flights</h1>
                    <?php endif; ?>
                </div>
                <p class="text-white-50 mb-0">
                    <i class="bi bi-calendar3 me-1 text-info"></i> Departure: <strong><?= date('D, d M Y', strtotime($departure_date)); ?></strong>
                    <?php if ($trip_type === 'roundtrip' && !empty($return_date)): ?>
                        | <i class="bi bi-arrow-left-right me-1 text-info"></i> Return: <strong><?= date('D, d M Y', strtotime($return_date)); ?></strong>
                    <?php endif; ?>
                    | <i class="bi bi-people me-1 text-info"></i> <strong><?= htmlspecialchars($passenger_class); ?></strong>
                </p>
            </div>
            <div class="col-lg-4 text-lg-end text-start">
                <a href="index.php#search" class="btn btn-light btn-lg rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-sliders me-2 text-primary"></i> Modify Search
                </a>
            </div>
        </div>
    </div>

    <!-- QUICK SORT & FILTER TABS -->
    <div class="quick-sort-bar">
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small fw-bold text-uppercase me-2"><i class="bi bi-sort-down me-1"></i> Sort By:</span>
            <button type="button" class="sort-pill-btn active-sort" onclick="sortFlightCards('cheapest', this)">
                <i class="bi bi-tag-fill"></i> Lowest Price
            </button>
            <button type="button" class="sort-pill-btn" onclick="sortFlightCards('fastest', this)">
                <i class="bi bi-speedometer2"></i> Fastest
            </button>
            <button type="button" class="sort-pill-btn" onclick="sortFlightCards('earliest', this)">
                <i class="bi bi-clock"></i> Departure Time
            </button>
        </div>
        <div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-bold" id="flightCounter">
                Showing <?= count($flights); ?> Flights Available
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- MODERN FILTER SIDEBAR -->
        <div class="col-lg-3">
            <div class="filter-card-v2">
                <div class="filter-card-title">
                    <span><i class="bi bi-funnel-fill text-primary me-2"></i>Filter Options</span>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none text-muted p-0" onclick="resetClientFilters()">Reset</button>
                </div>

                <!-- Instant Search Input -->
                <div class="mb-4">
                    <label class="filter-section-label">Search Airline / Flight</label>
                    <div class="search-input-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" id="airlineSearchInput" class="form-control" placeholder="e.g. IndiGo, 6E...">
                    </div>
                </div>

                <!-- Airline Filter Dropdown -->
                <div class="mb-4">
                    <label class="filter-section-label">Airline</label>
                    <select id="airlineFilterSelect" class="form-select form-select-sm">
                        <option value="">All Airlines</option>
                        <option value="IndiGo" <?= $filter_airline === 'IndiGo' ? 'selected' : ''; ?>>IndiGo</option>
                        <option value="Air India" <?= $filter_airline === 'Air India' ? 'selected' : ''; ?>>Air India</option>
                        <option value="SpiceJet" <?= $filter_airline === 'SpiceJet' ? 'selected' : ''; ?>>SpiceJet</option>
                        <option value="Akasa Air" <?= $filter_airline === 'Akasa Air' ? 'selected' : ''; ?>>Akasa Air</option>
                        <option value="Air India Express" <?= $filter_airline === 'Air India Express' ? 'selected' : ''; ?>>Air India Express</option>
                    </select>
                </div>

                <!-- Max Price Range Slider -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="filter-section-label mb-0">Max Ticket Price</label>
                        <span class="fw-bold text-primary small" id="priceDisplay">₹<?= number_format($slider_max); ?></span>
                    </div>
                    <input type="range" id="priceRangeInput" class="form-range custom-blue-slider" min="<?= $slider_min; ?>" max="<?= $slider_max; ?>" step="50" value="<?= $slider_max; ?>" oninput="updatePriceLabel(this.value);">
                    <div class="d-flex justify-content-between text-muted small mt-1" style="font-size: 0.72rem;">
                        <span>Min: ₹<?= number_format($slider_min); ?></span>
                        <span>Max: ₹<?= number_format($slider_max); ?></span>
                    </div>
                </div>

                <!-- Apply Filter Action Button (Mandatory Trigger) -->
                <div class="mb-4">
                    <button type="button" id="applyFilterBtn" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm py-2" onclick="handleApplyFilterClick(this)">
                        <i class="bi bi-funnel-fill me-1"></i> Apply Filter
                    </button>
                </div>

                <!-- Cabin Amenities Notice -->
                <div class="p-3 bg-light rounded-4 border text-muted small">
                    <div class="fw-bold text-dark mb-1"><i class="bi bi-shield-check text-success me-1"></i> Included Baggage</div>
                    <div>&bull; 7 KG Cabin Baggage</div>
                    <div>&bull; 15 KG Check-in Baggage</div>
                </div>
            </div>
        </div>

        <!-- FLIGHT LISTINGS CONTAINER -->
        <div class="col-lg-9" id="flightsListContainer">
            <?php if (empty($flights)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-3">
                    <div class="fs-1 text-muted mb-3"><i class="bi bi-airplane text-warning"></i></div>
                    <h5 class="fw-bold">No Flights Found</h5>
                    <p class="text-muted mb-4">Try adjusting your origin or destination cities to see available flights.</p>
                    <div>
                        <a href="index.php" class="btn btn-primary px-4 rounded-pill">Search Other Routes</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($flights as $index => $flight): 
                    $collapseId = "flightDetail_" . $flight['id'];
                    $logo = !empty($flight['logo']) ? $flight['logo'] : 'Flights/logo/indigo.png';
                    
                    // Match return flight for round trip
                    $returnFlight = null;
                    foreach ($return_flights as $candidate) {
                        if (strcasecmp($candidate['airline_name'], $flight['airline_name']) === 0) {
                            $returnFlight = $candidate;
                            break;
                        }
                    }
                    if ($returnFlight === null && !empty($return_flights)) {
                        $returnFlight = $return_flights[$index % count($return_flights)];
                    }
                    $baseSinglePrice = $flight['price'] + ($returnFlight['price'] ?? 0);
                    $totalPrice = round($baseSinglePrice * $total_fare_multiplier);
                    
                    // Extract duration minutes & departure time minutes for client-side sorting
                    $durStr = $flight['duration'] ?? '';
                    $durHours = 0; $durMins = 0;
                    if (preg_match('/(\d+)\s*h/i', $durStr, $mH)) { $durHours = intval($mH[1]); }
                    if (preg_match('/(\d+)\s*m/i', $durStr, $mM)) { $durMins = intval($mM[1]); }
                    $totalDurationMins = ($durHours * 60) + $durMins;
                    if (!empty($returnFlight)) {
                        $rDurStr = $returnFlight['duration'] ?? '';
                        $rHours = 0; $rMins = 0;
                        if (preg_match('/(\d+)\s*h/i', $rDurStr, $mRH)) { $rHours = intval($mRH[1]); }
                        if (preg_match('/(\d+)\s*m/i', $rDurStr, $mRM)) { $rMins = intval($mRM[1]); }
                        $totalDurationMins += ($rHours * 60) + $rMins;
                    }
                    if ($totalDurationMins <= 0) { $totalDurationMins = 120; }

                    $depTimeStr = trim($flight['departure_time'] ?? '00:00');
                    $depMinutes = 0;
                    if (preg_match('/(\d{1,2}):(\d{2})/', $depTimeStr, $tm)) {
                        $depMinutes = (intval($tm[1]) * 60) + intval($tm[2]);
                    }
                ?>
                    <div class="flight-card-v2 fade-in-up" 
                         data-price="<?= $totalPrice; ?>" 
                         data-duration="<?= $totalDurationMins; ?>" 
                         data-deptime="<?= htmlspecialchars($flight['departure_time']); ?>"
                         data-time="<?= $depMinutes; ?>" 
                         data-airline="<?= htmlspecialchars($flight['airline_name']); ?>" 
                         data-name="<?= htmlspecialchars($flight['flight_name']); ?>"
                         style="animation-delay: <?= $index * 0.04; ?>s;">

                        <!-- Flight Card Top Bar -->
                        <div class="flight-card-header">
                            <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1 rounded-pill small">
                                <i class="bi bi-airplane-engines me-1"></i> Outbound Flight &bull; Non-Stop
                            </span>
                            <span class="seats-badge">
                                <i class="bi bi-person-fill me-1"></i> <?= htmlspecialchars($flight['seats_available']); ?> Seats Left
                            </span>
                        </div>

                        <div class="row g-0 align-items-center">
                            <!-- Left Info Section -->
                            <div class="col-lg-8 p-4">
                                <div class="row align-items-center g-3">
                                    
                                    <!-- Airline Logo & Name -->
                                    <div class="col-md-4 col-12">
                                        <div class="airline-badge-wrapper">
                                            <div class="airline-logo-box">
                                                <img src="<?= htmlspecialchars($logo); ?>" alt="<?= htmlspecialchars($flight['airline_name']); ?>" onerror="this.src='Flights/logo/indigo.png'">
                                            </div>
                                            <div>
                                                <h3 class="airline-title"><?= htmlspecialchars($flight['airline_name']); ?></h3>
                                                <div class="airline-subtitle"><?= htmlspecialchars($flight['flight_name']); ?> &bull; <?= htmlspecialchars($flight['aircraft']); ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Departure City & Time -->
                                    <div class="col-md-2 col-4 text-center">
                                        <div class="time-display"><?= date('H:i', strtotime($flight['departure_time'])); ?></div>
                                        <div class="city-code-display"><?= htmlspecialchars($flight['departure_city']); ?></div>
                                    </div>

                                    <!-- Animated Route Path -->
                                    <div class="col-md-4 col-4">
                                        <div class="flight-path-container">
                                            <div class="flight-path-duration"><?= htmlspecialchars($flight['duration']); ?></div>
                                            <div class="flight-path-bar">
                                                <div class="flight-path-progress"></div>
                                                <div class="flight-plane-icon">
                                                    <i class="bi bi-airplane-fill"></i>
                                                </div>
                                            </div>
                                            <div class="small text-success fw-semibold" style="font-size:0.75rem;">Direct</div>
                                        </div>
                                    </div>

                                    <!-- Arrival City & Time -->
                                    <div class="col-md-2 col-4 text-center">
                                        <div class="time-display"><?= date('H:i', strtotime($flight['arrival_time'])); ?></div>
                                        <div class="city-code-display"><?= htmlspecialchars($flight['arrival_city']); ?></div>
                                    </div>

                                </div>

                                <?php if ($returnFlight): ?>
                                    <!-- Return Flight Row (Roundtrip) -->
                                    <div class="border-top mt-3 pt-3">
                                        <div class="small fw-bold text-primary text-uppercase mb-2">
                                            <i class="bi bi-arrow-left-right me-1"></i> Return Flight &bull; <?= date('d M', strtotime($return_date)); ?>
                                        </div>
                                        <div class="row align-items-center g-3">
                                            <div class="col-md-4 col-12 d-flex align-items-center gap-3">
                                                <div style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:#fff;border-radius:10px;border:1px solid #e2e8f0;padding:4px;">
                                                    <img src="<?= htmlspecialchars($returnFlight['logo'] ?? 'Flights/logo/indigo.png'); ?>" alt="<?= htmlspecialchars($returnFlight['airline_name']); ?>" style="max-width:100%;max-height:100%;object-fit:contain;" onerror="this.src='Flights/logo/indigo.png'">
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark small"><?= htmlspecialchars($returnFlight['airline_name']); ?></div>
                                                    <div class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($returnFlight['flight_name']); ?> &bull; <?= htmlspecialchars($returnFlight['aircraft']); ?></div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-4 text-center">
                                                <div class="fw-bold text-dark"><?= date('H:i', strtotime($returnFlight['departure_time'])); ?></div>
                                                <div class="small text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($returnFlight['departure_city']); ?></div>
                                            </div>
                                            <div class="col-md-4 col-4 text-center">
                                                <div class="small text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($returnFlight['duration']); ?></div>
                                                <div class="position-relative d-flex align-items-center justify-content-center">
                                                    <div class="w-100 bg-secondary opacity-25" style="height:2px;"></div>
                                                    <i class="bi bi-airplane-fill position-absolute text-primary fs-6 bg-white px-2"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-4 text-center">
                                                <div class="fw-bold text-dark"><?= date('H:i', strtotime($returnFlight['arrival_time'])); ?></div>
                                                <div class="small text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($returnFlight['arrival_city']); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Details Accordion Toggle -->
                                <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                                    <span class="text-muted small"><i class="bi bi-shield-check text-primary me-1"></i> Refundable Ticket</span>
                                    <a class="small text-decoration-none fw-bold text-primary" data-bs-toggle="collapse" href="#<?= $collapseId; ?>" role="button">
                                        Flight Details <i class="bi bi-chevron-down ms-1"></i>
                                    </a>
                                </div>

                                <!-- Accordion Content -->
                                <div class="collapse mt-3" id="<?= $collapseId; ?>">
                                    <div class="flight-specs-panel">
                                        <div class="row g-3 small">
                                            <div class="col-md-6">
                                                <div class="mb-1"><strong>Airline Carrier:</strong> <?= htmlspecialchars($flight['airline_name']); ?> (<?= htmlspecialchars($flight['flight_name']); ?>)</div>
                                                <div class="mb-1"><strong>Aircraft Model:</strong> <?= htmlspecialchars($flight['aircraft']); ?></div>
                                                <div class="mb-0"><strong>Seats Available:</strong> <?= htmlspecialchars($flight['seats_available']); ?> economy seats</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-1"><strong>Cabin Baggage:</strong> 7 KG Included</div>
                                                <div class="mb-1"><strong>Check-in Baggage:</strong> 15 KG Included</div>
                                                <div class="mb-0"><strong>Cancellation Policy:</strong> Standard refund policy</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Right Pricing & CTA Section -->
                            <div class="col-lg-4">
                                <div class="flight-price-box">
                                    <div class="text-muted small fw-semibold mb-1">Total Fare (<?= $total_passenger_count; ?> <?= $total_passenger_count > 1 ? 'Passengers' : 'Passenger'; ?>)</div>
                                    <div class="price-tag-amount mb-1">₹<?= number_format($totalPrice); ?></div>
                                    <div class="text-muted small mb-3">
                                        (₹<?= number_format($baseSinglePrice); ?> base × <?= $total_passenger_count; ?> pax<?= $class_multiplier > 1 ? ', ' . (stripos($passenger_class, 'Business') !== false ? 'Business Class' : 'First Class') : ''; ?>)
                                    </div>
                                    
                                    <form action="select_fligh.php" method="POST">
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
                                        <button type="submit" class="btn btn-book-v2 w-100">
                                            Select Flight <i class="bi bi-arrow-right ms-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<style>
#priceRangeInput.custom-blue-slider {
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    height: 6px;
    background: transparent !important;
    border: none !important;
    outline: none !important;
    padding: 0 !important;
    margin: 8px 0 !important;
    cursor: pointer;
}

#priceRangeInput.custom-blue-slider::-webkit-slider-runnable-track {
    width: 100%;
    height: 6px;
    border-radius: 6px;
    background: var(--track-bg, linear-gradient(to right, #0d6efd 0%, #0d6efd 100%, #cbd5e1 100%, #cbd5e1 100%)) !important;
    border: none !important;
}

#priceRangeInput.custom-blue-slider::-moz-range-track {
    width: 100%;
    height: 6px;
    border-radius: 6px;
    background: var(--track-bg, linear-gradient(to right, #0d6efd 0%, #0d6efd 100%, #cbd5e1 100%, #cbd5e1 100%)) !important;
    border: none !important;
}

#priceRangeInput.custom-blue-slider::-moz-range-progress {
    height: 6px;
    border-radius: 6px;
    background: #0d6efd !important;
}

#priceRangeInput.custom-blue-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    margin-top: -6px;
    border-radius: 50%;
    background: #0d6efd !important;
    cursor: pointer;
    border: 2px solid #ffffff !important;
    box-shadow: 0 2px 6px rgba(13, 110, 253, 0.5) !important;
    transition: transform 0.15s ease;
}

#priceRangeInput.custom-blue-slider::-webkit-slider-thumb:hover {
    transform: scale(1.2);
}

#priceRangeInput.custom-blue-slider::-moz-range-thumb {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #0d6efd !important;
    cursor: pointer;
    border: 2px solid #ffffff !important;
    box-shadow: 0 2px 6px rgba(13, 110, 253, 0.5) !important;
}
</style>

<!-- INSTANT CLIENT-SIDE DYNAMIC FILTERING & SORTING JAVASCRIPT -->
<script>
function updatePriceLabel(val) {
    document.getElementById('priceDisplay').innerText = '₹' + parseInt(val).toLocaleString();
    const input = document.getElementById('priceRangeInput');
    if (input) {
        const min = parseFloat(input.min) || 2000;
        const max = parseFloat(input.max) || 10000;
        const pct = Math.min(100, Math.max(0, ((val - min) / (max - min)) * 100));
        input.style.background = `linear-gradient(to right, #0d6efd 0%, #0d6efd ${pct}%, #e2e8f0 ${pct}%, #e2e8f0 100%)`;
    }
}

function applyTodayTimeFilter() {
    const depDateStr = "<?= $departure_date; ?>";
    if (!depDateStr) return;

    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const todayYMD = `${yyyy}-${mm}-${dd}`;

    if (depDateStr <= todayYMD || depDateStr.includes(todayYMD)) {
        const currentHHMM = String(today.getHours()).padStart(2, '0') + ':' + String(today.getMinutes()).padStart(2, '0');
        const cards = document.querySelectorAll('.flight-card-v2');

        cards.forEach(card => {
            const depTime = card.getAttribute('data-deptime') || '';
            if (depTime && depTime < currentHHMM) {
                card.style.display = 'none';
                card.classList.add('past-time-hidden');
            }
        });

        const visibleCards = document.querySelectorAll('.flight-card-v2:not(.past-time-hidden)');
        const counter = document.getElementById('flightCounter');
        if (counter) {
            counter.innerText = `Showing ${visibleCards.length} Flights Available`;
        }
    }
}

function handleApplyFilterClick(btn) {
    if (!btn) btn = document.getElementById('applyFilterBtn');
    
    // 1. Tactile Loading Feedback on Button
    const originalHTML = '<i class="bi bi-funnel-fill me-1"></i> Apply Filter';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Applying...';
    }

    // 2. Smooth transition on flight list container
    const container = document.getElementById('flightsListContainer');
    if (container) {
        container.style.transition = 'opacity 0.15s ease, transform 0.15s ease';
        container.style.opacity = '0.4';
        container.style.transform = 'translateY(6px)';
    }

    setTimeout(function() {
        // 3. Apply client-side filters
        applyClientFilters();

        // 4. Restore container with fade-in
        if (container) {
            container.style.opacity = '1';
            container.style.transform = 'translateY(0)';
        }

        // 5. Highlight counter badge
        const counter = document.getElementById('flightCounter');
        if (counter) {
            counter.classList.add('bg-primary', 'text-white');
            counter.classList.remove('bg-primary-subtle', 'text-primary');
            setTimeout(() => {
                counter.classList.remove('bg-primary', 'text-white');
                counter.classList.add('bg-primary-subtle', 'text-primary');
            }, 600);
        }

        // 6. Success Feedback State
        if (btn) {
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Filter Applied!';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
            setTimeout(function() {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
            }, 1200);
        }
    }, 250);
}

function applyClientFilters() {
    const searchText = (document.getElementById('airlineSearchInput').value || '').toLowerCase().trim();
    const selectedAirline = (document.getElementById('airlineFilterSelect').value || '').toLowerCase().trim();
    const maxPriceInput = document.getElementById('priceRangeInput');
    const maxPrice = maxPriceInput ? parseFloat(maxPriceInput.value) : 1000000;

    const cards = document.querySelectorAll('.flight-card-v2');
    let visibleCount = 0;

    cards.forEach(card => {
        const cardPrice = parseFloat(card.getAttribute('data-price')) || 0;
        const cardAirline = (card.getAttribute('data-airline') || '').toLowerCase();
        const cardName = (card.getAttribute('data-name') || '').toLowerCase();
        const isPastHidden = card.classList.contains('past-time-hidden');

        const matchesPrice = (cardPrice <= (maxPrice + 10));
        const matchesAirline = !selectedAirline || cardAirline === selectedAirline || cardAirline.includes(selectedAirline);
        const matchesSearch = !searchText || cardAirline.includes(searchText) || cardName.includes(searchText);

        if (!isPastHidden && matchesPrice && matchesAirline && matchesSearch) {
            card.style.display = 'block';
            card.classList.add('is-visible');
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const counter = document.getElementById('flightCounter');
    if (counter) {
        counter.innerText = `Showing ${visibleCount} Flights Available`;
    }

    // Dynamic Empty Filter Results Notice
    let noMatchNotice = document.getElementById('noFilterMatchesNotice');
    if (visibleCount === 0) {
        if (!noMatchNotice) {
            noMatchNotice = document.createElement('div');
            noMatchNotice.id = 'noFilterMatchesNotice';
            noMatchNotice.className = 'card border-0 shadow-sm rounded-4 p-5 text-center my-3 w-100 bg-white';
            noMatchNotice.innerHTML = `
                <div class="fs-1 text-muted mb-3"><i class="bi bi-funnel text-warning"></i></div>
                <h5 class="fw-bold text-dark mb-2">No Flights Match Your Filter</h5>
                <p class="text-muted small mb-4">No flights available below <strong>₹${Math.round(maxPrice).toLocaleString()}</strong>. Try increasing the price slider or resetting the filter options.</p>
                <div>
                    <button type="button" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm" onclick="resetClientFilters()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset All Filters
                    </button>
                </div>
            `;
            const container = document.getElementById('flightsListContainer');
            if (container) container.appendChild(noMatchNotice);
        } else {
            noMatchNotice.querySelector('strong').innerText = `₹${Math.round(maxPrice).toLocaleString()}`;
            noMatchNotice.style.display = 'block';
        }
    } else {
        if (noMatchNotice) {
            noMatchNotice.style.display = 'none';
        }
    }
}

function resetClientFilters() {
    const input = document.getElementById('priceRangeInput');
    const searchInput = document.getElementById('airlineSearchInput');
    const selectAirline = document.getElementById('airlineFilterSelect');
    
    if (searchInput) searchInput.value = '';
    if (selectAirline) selectAirline.value = '';
    
    if (input) {
        const maxVal = parseFloat(input.max) || 50000;
        input.value = maxVal;
        updatePriceLabel(maxVal);
    }
    handleApplyFilterClick(document.getElementById('applyFilterBtn'));
}

function sortFlightCards(criteria, btnElement) {
    const container = document.getElementById('flightsListContainer');
    if (!container) return;
    const cards = Array.from(container.querySelectorAll('.flight-card-v2'));
    if (cards.length === 0) return;

    // Update active button style
    document.querySelectorAll('.sort-pill-btn').forEach(btn => btn.classList.remove('active-sort'));
    if (btnElement) {
        btnElement.classList.add('active-sort');
    }

    // Visual transition
    container.style.opacity = '0.5';

    cards.sort((a, b) => {
        const priceA = parseFloat(a.getAttribute('data-price')) || 0;
        const priceB = parseFloat(b.getAttribute('data-price')) || 0;
        const durA = parseInt(a.getAttribute('data-duration')) || 0;
        const durB = parseInt(b.getAttribute('data-duration')) || 0;
        const timeA = parseInt(a.getAttribute('data-time')) || 0;
        const timeB = parseInt(b.getAttribute('data-time')) || 0;

        if (criteria === 'cheapest') {
            return priceA - priceB;
        } else if (criteria === 'fastest') {
            return durA - durB;
        } else if (criteria === 'earliest') {
            return timeA - timeB;
        }
        return 0;
    });

    cards.forEach((card, idx) => {
        card.style.animationDelay = (idx * 0.03) + 's';
        container.appendChild(card);
    });

    setTimeout(() => {
        container.style.opacity = '1';
    }, 100);

    applyClientFilters();
}

// Auto-sort by Lowest Price and Apply Filters when page loads
document.addEventListener('DOMContentLoaded', function() {
    applyTodayTimeFilter();
    const rangeInput = document.getElementById('priceRangeInput');
    if (rangeInput) {
        updatePriceLabel(rangeInput.value);
    }
    const defaultBtn = document.querySelector('.sort-pill-btn.active-sort');
    sortFlightCards('cheapest', defaultBtn);
    applyClientFilters();
});
</script>

<?php include 'include/footer.php'; ?>
