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
$price = $_SESSION['price'] ?? '4500';
$departure_time = $_SESSION['departure_time'] ?? '06:00';
$arrival_time = $_SESSION['arrival_time'] ?? '08:15';
$from_city = $_SESSION['from_city'] ?? 'Delhi';
$to_city = $_SESSION['to_city'] ?? 'Mumbai';
$departure_date = $_SESSION['departure_date'] ?? date('Y-m-d');
$return_date = $_SESSION['return_date'] ?? '';

include 'include/header.php';
?>

<div class="container my-4">
    <!-- STEPPER BAR -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-light">
        <div class="row text-center align-items-center">
            <div class="col-4">
                <span class="badge bg-success rounded-circle p-2 me-1"><i class="bi bi-check-lg"></i></span>
                <span class="fw-bold text-success small">1. Selection</span>
            </div>
            <div class="col-4 border-start border-end">
                <span class="badge bg-primary rounded-circle p-2 me-1">2</span>
                <span class="fw-bold text-primary small">2. Passenger Details</span>
            </div>
            <div class="col-4">
                <span class="badge bg-secondary rounded-circle p-2 me-1">3</span>
                <span class="fw-bold text-muted small">3. Payment</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- PASSENGER FORM -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Enter Passenger Information</h5>

                <form action="payment.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-muted">Gender / Title</label>
                            <select class="form-select" name="gender" required>
                                <option value="">Select</option>
                                <option value="Mr">Mr (Male)</option>
                                <option value="Ms">Ms (Female)</option>
                                <option value="Mrs">Mrs (Female)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">First Name</label>
                            <input type="text" class="form-control" name="firstname" placeholder="First Name" required>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-muted">Last Name</label>
                            <input type="text" class="form-control" name="lastname" placeholder="Last Name" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Email Address</label>
                            <input type="email" class="form-control" name="email" placeholder="example@domain.com" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Mobile Number</label>
                            <input type="tel" class="form-control" name="number" placeholder="+91 9876543210" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Date of Birth</label>
                            <input type="date" class="form-control" name="dob" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Nationality</label>
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

                    <h6 class="fw-bold mt-4 mb-3"><i class="bi bi-star me-2 text-warning"></i>Extra Add-ons</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Meal Preference</label>
                            <select class="form-select" name="meal_type">
                                <option value="None" selected>No Meal</option>
                                <option value="Vegetarian">Vegetarian Meal (+₹250)</option>
                                <option value="Non-Vegetarian">Non-Vegetarian Meal (+₹350)</option>
                                <option value="Special Snack">Special Airline Snack (+₹200)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Wheelchair Assistance</label>
                            <select class="form-select" name="wheelchair">
                                <option value="No" selected>No Assistance Required</option>
                                <option value="Yes">Yes, Request Wheelchair</option>
                            </select>
                        </div>
                    </div>

                    <!-- Hidden fields -->
                    <input type="hidden" name="flight_name" value="<?= htmlspecialchars($flight_name); ?>">
                    <input type="hidden" name="airline_name" value="<?= htmlspecialchars($airline_name); ?>">
                    <input type="hidden" name="price" value="<?= htmlspecialchars($price); ?>">
                    <input type="hidden" name="departure_time" value="<?= htmlspecialchars($departure_time); ?>">
                    <input type="hidden" name="arrival_time" value="<?= htmlspecialchars($arrival_time); ?>">
                    <input type="hidden" name="from_city" value="<?= htmlspecialchars($from_city); ?>">
                    <input type="hidden" name="to_city" value="<?= htmlspecialchars($to_city); ?>">
                    <input type="hidden" name="departure_date" value="<?= htmlspecialchars($departure_date); ?>">
                    <input type="hidden" name="return_date" value="<?= htmlspecialchars($return_date); ?>">

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm fw-bold">
                            Proceed to Payment <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- FLIGHT SUMMARY CARD -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 90px;">
                <h6 class="fw-bold mb-3"><i class="bi bi-ticket-detailed me-2 text-primary"></i>Flight Summary</h6>
                
                <div class="p-3 bg-light rounded-3 mb-3">
                    <div class="fw-bold text-dark mb-1"><?= htmlspecialchars($airline_name); ?> (<?= htmlspecialchars($flight_name); ?>)</div>
                    <div class="small text-muted mb-2">
                        <?= htmlspecialchars($from_city); ?> ✈ <?= htmlspecialchars($to_city); ?>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Departure: <strong><?= date('H:i', strtotime($departure_time)); ?></strong></span>
                        <span>Arrival: <strong><?= date('H:i', strtotime($arrival_time)); ?></strong></span>
                    </div>
                    <div class="small text-muted mt-1">Date: <strong><?= date('d M Y', strtotime($departure_date)); ?></strong></div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Base Fare:</span>
                    <span class="fw-semibold">₹<?= number_format($price); ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Taxes & Fees:</span>
                    <span class="fw-semibold text-success">Included</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark">Total Amount:</span>
                    <span class="fs-4 fw-bold text-primary">₹<?= number_format($price); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>