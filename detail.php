<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'include/db_json.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['flight_name'])) $_SESSION['flight_name'] = $_POST['flight_name'];
    if (isset($_POST['airline_name'])) $_SESSION['airline_name'] = $_POST['airline_name'];
    if (isset($_POST['price'])) $_SESSION['price'] = $_POST['price'];
    if (isset($_POST['departure_time'])) $_SESSION['departure_time'] = $_POST['departure_time'];
    if (isset($_POST['arrival_time'])) $_SESSION['arrival_time'] = $_POST['arrival_time'];
    if (isset($_POST['from_city'])) $_SESSION['from_city'] = $_POST['from_city'];
    if (isset($_POST['to_city'])) $_SESSION['to_city'] = $_POST['to_city'];
    if (isset($_POST['duration'])) $_SESSION['duration'] = $_POST['duration'];
}

$flight_name = $_SESSION['flight_name'] ?? '6E-204';
$airline_name = $_SESSION['airline_name'] ?? 'IndiGo';
$base_price = intval($_SESSION['price'] ?? 4500);
$departure_time = $_SESSION['departure_time'] ?? '06:00';
$arrival_time = $_SESSION['arrival_time'] ?? '08:15';
$from_city = $_SESSION['from_city'] ?? 'Delhi';
$to_city = $_SESSION['to_city'] ?? 'Mumbai';
$departure_date = $_SESSION['departure_date'] ?? date('Y-m-d');
$return_date = $_SESSION['return_date'] ?? '';

// City code mapping
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

$from_code = $city_codes[$from_city] ?? strtoupper(substr($from_city, 0, 3));
$to_code = $city_codes[$to_city] ?? strtoupper(substr($to_city, 0, 3));

include 'include/header.php';
?>

<div class="container my-4">

    <!-- STEPPER WIZARD BAR -->
    <div class="detail-wizard-bar">
        <div class="row align-items-center g-3 text-center text-md-start">
            <div class="col-md-4">
                <div class="wizard-step-v2 completed">
                    <div class="wizard-step-circle"><i class="bi bi-check-lg"></i></div>
                    <div>
                        <div class="wizard-step-title">1. Flight Selection</div>
                        <small class="text-muted">Route & Schedule Chosen</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="wizard-step-v2 active">
                    <div class="wizard-step-circle">2</div>
                    <div>
                        <div class="wizard-step-title">2. Passenger Details</div>
                        <small class="text-primary fw-semibold">Enter Info & Add-ons</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="wizard-step-v2 disabled">
                    <div class="wizard-step-circle">3</div>
                    <div>
                        <div class="wizard-step-title">3. Secure Payment</div>
                        <small class="text-muted">Instant Booking Pass</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- PASSENGER FORM -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1"><i class="bi bi-person-badge-fill me-2 text-primary"></i>Passenger Details</h4>
                        <p class="text-muted small mb-0">Please enter passenger info exactly as shown on government ID.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">Primary Traveler</span>
                </div>

                <form action="payment.php" method="POST" id="passengerDetailsForm">
                    
                    <!-- Title Selection Pills -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Title / Gender</label>
                        <div class="title-pill-group">
                            <button type="button" class="title-pill-btn active-title" onclick="selectTitle('Mr', this)">Mr (Male)</button>
                            <button type="button" class="title-pill-btn" onclick="selectTitle('Ms', this)">Ms (Female)</button>
                            <button type="button" class="title-pill-btn" onclick="selectTitle('Mrs', this)">Mrs (Female)</button>
                        </div>
                        <input type="hidden" name="gender" id="genderInput" value="Mr" required>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">First Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" name="firstname" placeholder="First Name (e.g. John)" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Last Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" name="lastname" placeholder="Last Name (e.g. Doe)" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" name="email" placeholder="john.doe@example.com" required>
                            </div>
                            <div class="form-text small">Boarding pass & e-ticket will be sent here.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="bi bi-telephone"></i></span>
                                <input type="tel" class="form-control" name="number" placeholder="+91 9876543210" required>
                            </div>
                            <div class="form-text small">For SMS flight alerts & boarding notifications.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Date of Birth</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="bi bi-calendar"></i></span>
                                <input type="date" class="form-control" name="dob" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Nationality</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="bi bi-globe"></i></span>
                                <select class="form-select" name="nationality" required>
                                    <option value="Indian" selected>Indian</option>
                                    <option value="American">American</option>
                                    <option value="British">British</option>
                                    <option value="Canadian">Canadian</option>
                                    <option value="Emirati">Emirati</option>
                                    <option value="Australian">Australian</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- EXTRA ADD-ONS SECTION -->
                    <div class="pt-4 border-top">
                        <h5 class="fw-bold mb-3"><i class="bi bi-stars text-warning me-2"></i>Customize Your Flight Add-ons</h5>
                        <p class="text-muted small mb-4">Select optional in-flight meals or airport mobility assistance.</p>

                        <!-- Meal Preference Options -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted mb-2">In-Flight Meal Preference</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="addon-card-option selected-addon" onclick="selectMeal('None', 0, this)">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="addon-icon-circle"><i class="bi bi-x-circle"></i></div>
                                            <div>
                                                <div class="fw-bold text-dark">No Meal</div>
                                                <small class="text-muted">Standard flight</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-secondary-subtle text-secondary fw-bold">Free</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="addon-card-option" onclick="selectMeal('Vegetarian', 250, this)">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="addon-icon-circle"><i class="bi bi-cup-hot"></i></div>
                                            <div>
                                                <div class="fw-bold text-dark">Vegetarian Hot Meal</div>
                                                <small class="text-muted">Chef prepared veg menu</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-success-subtle text-success fw-bold">+₹250</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="addon-card-option" onclick="selectMeal('Non-Vegetarian', 350, this)">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="addon-icon-circle"><i class="bi bi-egg-fried"></i></div>
                                            <div>
                                                <div class="fw-bold text-dark">Non-Veg Gourmet Meal</div>
                                                <small class="text-muted">Premium non-veg option</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-danger-subtle text-danger fw-bold">+₹350</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="addon-card-option" onclick="selectMeal('Special Snack', 200, this)">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="addon-icon-circle"><i class="bi bi-box-seam"></i></div>
                                            <div>
                                                <div class="fw-bold text-dark">Airline Snack Box</div>
                                                <small class="text-muted">Beverage & snack combo</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-warning-subtle text-warning fw-bold">+₹200</span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="meal_type" id="mealTypeInput" value="None">
                        </div>

                        <!-- Wheelchair Assistance -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted mb-2">Special Assistance</label>
                            <div class="p-3 bg-light rounded-4 border d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="addon-icon-circle bg-white"><i class="bi bi-person-wheelchair text-primary"></i></div>
                                    <div>
                                        <div class="fw-bold text-dark">Wheelchair Assistance</div>
                                        <small class="text-muted">Airport ramps & boarding assistance</small>
                                    </div>
                                </div>
                                <select class="form-select form-select-sm w-auto" name="wheelchair" onchange="updateAddons()">
                                    <option value="No" selected>No Assistance Required</option>
                                    <option value="Yes">Yes, Request Wheelchair (Free)</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <!-- Hidden fields -->
                    <input type="hidden" name="flight_name" value="<?= htmlspecialchars($flight_name); ?>">
                    <input type="hidden" name="airline_name" value="<?= htmlspecialchars($airline_name); ?>">
                    <input type="hidden" name="price" id="finalPriceInput" value="<?= htmlspecialchars($base_price); ?>">
                    <input type="hidden" name="departure_time" value="<?= htmlspecialchars($departure_time); ?>">
                    <input type="hidden" name="arrival_time" value="<?= htmlspecialchars($arrival_time); ?>">
                    <input type="hidden" name="from_city" value="<?= htmlspecialchars($from_city); ?>">
                    <input type="hidden" name="to_city" value="<?= htmlspecialchars($to_city); ?>">
                    <input type="hidden" name="departure_date" value="<?= htmlspecialchars($departure_date); ?>">
                    <input type="hidden" name="return_date" value="<?= htmlspecialchars($return_date); ?>">

                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg fw-bold">
                            Proceed to Payment <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- DYNAMIC FLIGHT SUMMARY SIDEBAR -->
        <div class="col-lg-4">
            <div class="summary-card-v2">
                <h5 class="fw-bold mb-3"><i class="bi bi-ticket-perforated-fill text-primary me-2"></i>Booking Summary</h5>

                <!-- Route Banner Tile -->
                <div class="route-preview-badge">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-primary text-white fw-bold px-3 py-1 rounded-pill small"><?= htmlspecialchars($airline_name); ?></span>
                        <span class="small text-white-50"><?= htmlspecialchars($flight_name); ?></span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between my-3">
                        <div class="text-start">
                            <div class="fw-bold fs-3"><?= htmlspecialchars($from_code); ?></div>
                            <small class="text-white-50"><?= htmlspecialchars($from_city); ?></small>
                        </div>
                        <div class="text-center px-2">
                            <i class="bi bi-airplane-fill text-info fs-5"></i>
                            <div class="border-top border-secondary border-opacity-50 my-1" style="width: 50px;"></div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold fs-3"><?= htmlspecialchars($to_code); ?></div>
                            <small class="text-white-50"><?= htmlspecialchars($to_city); ?></small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between small text-white-50 border-top border-white border-opacity-10 pt-2">
                        <span>Dep: <strong><?= date('H:i', strtotime($departure_time)); ?></strong></span>
                        <span>Arr: <strong><?= date('H:i', strtotime($arrival_time)); ?></strong></span>
                    </div>
                </div>

                <?php $passenger_class_display = $_SESSION['passenger_class'] ?? '1 Adult, Economy'; ?>
                <div class="mb-3 p-3 bg-light rounded-4 border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i> Travel Date:</span>
                        <span class="badge bg-primary-subtle text-primary fw-bold small"><?= htmlspecialchars($passenger_class_display); ?></span>
                    </div>
                    <div class="fw-bold text-dark"><?= date('D, d M Y', strtotime($departure_date)); ?></div>
                </div>

                <!-- Price Breakdown -->
                <div class="price-row-item">
                    <span>Base Ticket Fare:</span>
                    <span class="fw-bold text-dark">₹<?= number_format($base_price); ?></span>
                </div>
                <div class="price-row-item">
                    <span>Selected Meal Add-on:</span>
                    <span class="fw-bold text-success" id="mealPriceDisplay">Free</span>
                </div>
                <div class="price-row-item">
                    <span>Airport Taxes & GST:</span>
                    <span class="fw-bold text-success">Included</span>
                </div>

                <hr class="my-3">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <div class="fw-bold text-dark">Total Amount Due:</div>
                        <small class="text-muted">All inclusive fare</small>
                    </div>
                    <div class="total-price-display text-primary" id="totalPriceDisplay">
                        ₹<?= number_format($base_price); ?>
                    </div>
                </div>

                <div class="p-3 bg-primary-subtle text-primary border border-primary-subtle rounded-4 small">
                    <i class="bi bi-shield-lock-fill me-1"></i> Guaranteed lowest fare & instant e-ticket issuance upon payment.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DYNAMIC FARE RECALCULATION JAVASCRIPT -->
<script>
const basePrice = <?= $base_price; ?>;
let selectedMealPrice = 0;

function selectTitle(title, btnElement) {
    document.querySelectorAll('.title-pill-btn').forEach(btn => btn.classList.remove('active-title'));
    btnElement.classList.add('active-title');
    document.getElementById('genderInput').value = title;
}

function selectMeal(mealType, price, cardElement) {
    document.querySelectorAll('.addon-card-option').forEach(card => card.classList.remove('selected-addon'));
    cardElement.classList.add('selected-addon');
    document.getElementById('mealTypeInput').value = mealType;
    selectedMealPrice = price;
    updateAddons();
}

function updateAddons() {
    const mealDisplay = document.getElementById('mealPriceDisplay');
    if (selectedMealPrice > 0) {
        mealDisplay.innerText = '+₹' + selectedMealPrice;
        mealDisplay.className = 'fw-bold text-primary';
    } else {
        mealDisplay.innerText = 'Free';
        mealDisplay.className = 'fw-bold text-success';
    }

    const total = basePrice + selectedMealPrice;
    document.getElementById('totalPriceDisplay').innerText = '₹' + total.toLocaleString();
    document.getElementById('finalPriceInput').value = total;
}
</script>

<?php include 'include/footer.php'; ?>
