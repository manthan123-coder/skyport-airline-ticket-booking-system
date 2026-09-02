<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'include/db_json.php';

// Save passenger details to Session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['pay_now'])) {
    foreach ($_POST as $key => $value) {
        $_SESSION[$key] = $value;
    }
}

// Handle Payment Submission
if (isset($_POST['pay_now'])) {
    $pnr = 'SKP' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
    $booking_id = 'BK' . date('YmdHis');
    
    $_SESSION['pnr'] = $pnr;
    $_SESSION['booking_id'] = $booking_id;
    $_SESSION['payment_status'] = 'Success';

    $booking_data = [
        'booking_id'     => $booking_id,
        'pnr'            => $pnr,
        'flight_name'    => $_SESSION['flight_name'] ?? '6E-204',
        'airline_name'   => $_SESSION['airline_name'] ?? 'IndiGo',
        'from_city'      => $_SESSION['from_city'] ?? 'Delhi',
        'to_city'        => $_SESSION['to_city'] ?? 'Mumbai',
        'departure_date' => $_SESSION['departure_date'] ?? date('Y-m-d'),
        'return_date'    => $_SESSION['return_date'] ?? '',
        'trip_type'      => $_SESSION['trip_type'] ?? 'oneway',
        'passenger_class'=> $_SESSION['passenger_class'] ?? '1 Adult, Economy',
        'gender'         => $_SESSION['gender'] ?? 'Mr',
        'firstname'      => $_SESSION['firstname'] ?? 'Guest',
        'lastname'       => $_SESSION['lastname'] ?? 'Passenger',
        'email'          => $_SESSION['email'] ?? 'guest@example.com',
        'phone'          => $_SESSION['number'] ?? '+91 9876543210',
        'nationality'    => $_SESSION['nationality'] ?? 'Indian',
        'dob'            => $_SESSION['dob'] ?? '1995-01-01',
        'meal_type'      => $_SESSION['meal_type'] ?? 'None',
        'wheelchair'     => $_SESSION['wheelchair'] ?? 'No',
        'amount'         => $_SESSION['price'] ?? '4500',
        'payment_method' => $_POST['payment_method'] ?? 'Card',
        'payment_status' => 'Success',
        'checkin_status' => 'Not Checked-in',
        'seat_no'        => 'Unassigned',
        'departure_time' => $_SESSION['departure_time'] ?? '06:00',
        'arrival_time'   => $_SESSION['arrival_time'] ?? '08:15',
        'created_at'     => date('Y-m-d H:i:s')
    ];

    save_booking($booking_data);

    header('Location: confirmation.php');
    exit();
}

$firstname = $_SESSION['firstname'] ?? 'Passenger';
$lastname = $_SESSION['lastname'] ?? '';
$flight_name = $_SESSION['flight_name'] ?? '6E-204';
$airline_name = $_SESSION['airline_name'] ?? 'IndiGo';
$from_city = $_SESSION['from_city'] ?? 'Delhi';
$to_city = $_SESSION['to_city'] ?? 'Mumbai';
$departure_date = $_SESSION['departure_date'] ?? date('Y-m-d');
$price = $_SESSION['price'] ?? '4500';

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
                <span class="badge bg-success rounded-circle p-2 me-1"><i class="bi bi-check-lg"></i></span>
                <span class="fw-bold text-success small">2. Details</span>
            </div>
            <div class="col-4">
                <span class="badge bg-primary rounded-circle p-2 me-1">3</span>
                <span class="fw-bold text-primary small">3. Payment</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- BOOKING SUMMARY SIDEBAR -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-receipt me-2 text-primary"></i>Booking Summary</h6>
                
                <div class="p-3 bg-light rounded-3 mb-3">
                    <div class="small text-muted mb-1">Passenger</div>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($firstname . ' ' . $lastname); ?></div>
                    <div class="small text-muted mt-2">Flight</div>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($airline_name); ?> (<?= htmlspecialchars($flight_name); ?>)</div>
                    
                    <hr class="my-2 opacity-25">
                    
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Route:</span>
                        <span class="fw-semibold"><?= htmlspecialchars($from_city); ?> ✈ <?= htmlspecialchars($to_city); ?></span>
                    </div>
                    <div class="d-flex justify-content-between small mt-1">
                        <span class="text-muted">Date:</span>
                        <span class="fw-semibold"><?= date('d M Y', strtotime($departure_date)); ?></span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Total Fare:</span>
                    <span class="fs-4 fw-bold text-primary">₹<?= number_format($price); ?></span>
                </div>
                <div class="alert alert-success py-2 small mb-0"><i class="bi bi-shield-check me-1"></i> SSL Encrypted & Secure Payment</div>
            </div>
        </div>

        <!-- PAYMENT OPTIONS -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-credit-card-2-front me-2 text-primary"></i>Select Payment Method</h5>

                <ul class="nav nav-pills mb-4 nav-justified bg-light p-1 rounded-3" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold py-2" data-bs-toggle="pill" data-bs-target="#cardTab">
                            <i class="bi bi-credit-card me-1"></i> Card
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold py-2" data-bs-toggle="pill" data-bs-target="#upiTab">
                            <i class="bi bi-qr-code-scan me-1"></i> UPI
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold py-2" data-bs-toggle="pill" data-bs-target="#netTab">
                            <i class="bi bi-bank me-1"></i> Net Banking
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- CARD TAB -->
                    <div class="tab-pane fade show active" id="cardTab">
                        <form action="payment.php" method="POST">
                            <input type="hidden" name="payment_method" value="Credit/Debit Card">
                            
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Cardholder Name</label>
                                <input type="text" class="form-control" placeholder="Name on card" required value="<?= htmlspecialchars($firstname . ' ' . $lastname); ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Card Number</label>
                                <input type="text" maxlength="19" class="form-control" placeholder="4532 •••• •••• 8901" required value="4532 8912 3456 8901">
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-muted">Expiry Date</label>
                                    <input type="text" class="form-control" placeholder="MM/YY" required value="12/28">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-semibold text-muted">CVV Code</label>
                                    <input type="password" maxlength="3" class="form-control" placeholder="123" required value="892">
                                </div>
                            </div>

                            <button type="submit" name="pay_now" class="btn btn-success btn-lg w-100 mt-4 rounded-pill fw-bold shadow-sm py-3">
                                Pay ₹<?= number_format($price); ?> & Confirm Booking <i class="bi bi-check-circle-fill ms-2"></i>
                            </button>
                        </form>
                    </div>

                    <!-- UPI TAB -->
                    <div class="tab-pane fade" id="upiTab">
                        <form action="payment.php" method="POST">
                            <input type="hidden" name="payment_method" value="UPI">
                            
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Enter Virtual Payment Address (VPA)</label>
                                <input type="text" class="form-control" placeholder="mobileNumber@upi / username@okaxis" required>
                            </div>
                            <small class="text-muted d-block mb-3">Compatible with Google Pay, PhonePe, Paytm, and BHIM UPI.</small>

                            <button type="submit" name="pay_now" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow-sm py-3">
                                Pay via UPI ₹<?= number_format($price); ?> <i class="bi bi-check-circle-fill ms-2"></i>
                            </button>
                        </form>
                    </div>

                    <!-- NET BANKING TAB -->
                    <div class="tab-pane fade" id="netTab">
                        <form action="payment.php" method="POST">
                            <input type="hidden" name="payment_method" value="Net Banking">
                            
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Select Your Bank</label>
                                <select class="form-select" required>
                                    <option value="">Choose Bank</option>
                                    <option value="SBI">State Bank of India (SBI)</option>
                                    <option value="HDFC">HDFC Bank</option>
                                    <option value="ICICI">ICICI Bank</option>
                                    <option value="Axis">Axis Bank</option>
                                    <option value="Kotak">Kotak Mahindra Bank</option>
                                </select>
                            </div>

                            <button type="submit" name="pay_now" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow-sm py-3">
                                Proceed to Bank Portal <i class="bi bi-box-arrow-up-right ms-2"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>