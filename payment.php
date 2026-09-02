<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'include/db_json.php';

// Save passenger details sent from detail.php into Session automatically
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['pay_now'])) {
    foreach ($_POST as $key => $value) {
        $_SESSION[$key] = $value;
    }
}

// Handle Payment Submission
if (isset($_POST['pay_now'])) {
    $pnr = 'SKP' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
    $booking_id = 'BK' . date('YmdHis');
    $payment_id = !empty($_POST['razorpay_payment_id']) ? $_POST['razorpay_payment_id'] : ('pay_' . substr(md5(uniqid()), 0, 14));
    
    $_SESSION['pnr'] = $pnr;
    $_SESSION['booking_id'] = $booking_id;
    $_SESSION['payment_status'] = 'Success';
    $_SESSION['payment_id'] = $payment_id;

    $booking_data = [
        'booking_id'     => $booking_id,
        'pnr'            => $pnr,
        'payment_id'     => $payment_id,
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
        'meal_type'      => $_SESSION['meal_type'] ?? 'Standard Meal',
        'wheelchair'     => $_SESSION['wheelchair'] ?? 'No',
        'amount'         => $_SESSION['price'] ?? '4500',
        'payment_method' => $_POST['payment_method'] ?? 'Credit/Debit Card',
        'payment_status' => 'Success / Debited',
        'checkin_status' => 'Confirmed',
        'seat_no'        => 'Unassigned',
        'departure_time' => $_SESSION['departure_time'] ?? '06:00',
        'arrival_time'   => $_SESSION['arrival_time'] ?? '08:15',
        'created_at'     => date('Y-m-d H:i:s')
    ];

    save_booking($booking_data);
    send_booking_email_and_sms($booking_data);

    header('Location: confirmation.php');
    exit();
}

// Pre-fill All Values Automatically from Session
$firstname = !empty($_SESSION['firstname']) ? $_SESSION['firstname'] : 'Guest';
$lastname = !empty($_SESSION['lastname']) ? $_SESSION['lastname'] : 'Traveler';
$fullname = trim($firstname . ' ' . $lastname);
$email = !empty($_SESSION['email']) ? $_SESSION['email'] : 'passenger@skyport.com';
$phone = !empty($_SESSION['number']) ? $_SESSION['number'] : '+91 7202097453';
$gender = !empty($_SESSION['gender']) ? $_SESSION['gender'] : 'Mr';

$flight_name = !empty($_SESSION['flight_name']) ? $_SESSION['flight_name'] : '6E-204';
$airline_name = !empty($_SESSION['airline_name']) ? $_SESSION['airline_name'] : 'IndiGo';
$from_city = !empty($_SESSION['from_city']) ? $_SESSION['from_city'] : 'Delhi';
$to_city = !empty($_SESSION['to_city']) ? $_SESSION['to_city'] : 'Mumbai';
$departure_date = !empty($_SESSION['departure_date']) ? $_SESSION['departure_date'] : date('Y-m-d');
$departure_time = !empty($_SESSION['departure_time']) ? $_SESSION['departure_time'] : '06:00';
$arrival_time = !empty($_SESSION['arrival_time']) ? $_SESSION['arrival_time'] : '08:15';

$total_price = floatval(!empty($_SESSION['price']) ? $_SESSION['price'] : 4500);
$base_fare = round($total_price * 0.85);
$taxes_fees = $total_price - $base_fare;

include 'include/header.php';
?>

<style>
    .payment-hero-bg {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: calc(100vh - 80px);
        padding-bottom: 60px;
    }
    .payment-card-glass {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .stepper-nav {
        background: #ffffff;
        border-radius: 50px;
        padding: 10px 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
        border: 1px solid #e2e8f0;
    }
    .stepper-step {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 10px 18px;
        border-radius: 30px;
        transition: all 0.3s ease;
    }
    .stepper-step.completed { background: #d1e7dd; color: #0f5132; }
    .stepper-step.active { background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: #ffffff; box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3); }

    .pay-tab-btn {
        border-radius: 14px !important;
        font-weight: 700 !important;
        padding: 14px 20px !important;
        color: #475569 !important;
        transition: all 0.3s ease !important;
    }
    .pay-tab-btn.active {
        background: linear-gradient(135deg, #0d6efd, #0a58ca) !important;
        color: #ffffff !important;
        box-shadow: 0 6px 20px rgba(13, 110, 253, 0.3) !important;
    }

    /* Live Visual Credit Card Mockup */
    .visual-credit-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 20px;
        padding: 24px;
        color: #ffffff;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.3);
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .visual-credit-card::before {
        content: '';
        position: absolute;
        top: -50%; right: -50%;
        width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 60%);
        pointer-events: none;
    }
    .card-chip {
        width: 48px;
        height: 34px;
        background: linear-gradient(135deg, #fcd34d, #f59e0b);
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
    }
    .card-number-display {
        font-size: 1.35rem;
        letter-spacing: 3px;
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        margin-bottom: 20px;
        word-spacing: 4px;
    }



    /* 3D-Secure Bank OTP Modal Overlay */
    .bank-otp-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(8px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .bank-otp-card {
        background: #ffffff;
        border-radius: 24px;
        max-width: 460px;
        width: 100%;
        box-shadow: 0 30px 70px rgba(0, 0, 0, 0.35);
        overflow: hidden;
        animation: popIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    @keyframes popIn {
        from { transform: scale(0.8); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .otp-input-box {
        letter-spacing: 8px;
        font-size: 2rem !important;
        font-weight: 800 !important;
        color: #0f172a;
        border: 2px solid #cbd5e1;
        border-radius: 14px;
        transition: all 0.2s ease;
    }
    .otp-input-box:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.15);
    }
</style>



<div class="payment-hero-bg py-4">
    <div class="container">

        <!-- STEPPER NAV -->
        <div class="stepper-nav mb-4">
            <div class="row text-center g-2">
                <div class="col-4">
                    <div class="stepper-step completed">
                        <i class="bi bi-check-circle-fill fs-6"></i>
                        <span class="d-none d-md-inline">1. Flight Selection</span>
                        <span class="d-md-none">1. Select</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stepper-step completed">
                        <i class="bi bi-check-circle-fill fs-6"></i>
                        <span class="d-none d-md-inline">2. Passenger Details</span>
                        <span class="d-md-none">2. Details</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stepper-step active">
                        <i class="bi bi-credit-card-fill fs-6"></i>
                        <span class="d-none d-md-inline">3. Payment & Dynamic OTP</span>
                        <span class="d-md-none">3. Payment</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            <!-- LEFT COLUMN: ULTRA MODERN FLIGHT TICKET SUMMARY CARD -->
            <div class="col-lg-4">
                <div class="payment-card-glass overflow-hidden position-relative shadow-lg rounded-4">
                    
                    <!-- Ticket Header Banner -->
                    <div class="p-4 text-white position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill fw-bold" style="font-size:0.75rem;">
                                <i class="bi bi-airplane-engines me-1"></i> E-TICKET SUMMARY
                            </span>
                            <span class="badge bg-success text-white px-3 py-1 rounded-pill fw-bold" style="font-size:0.75rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Ready for Pay
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white p-2 rounded-3 shadow-sm" style="width:48px; height:48px; display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-airplane-fill fs-3 text-primary"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-white"><?= htmlspecialchars($airline_name); ?></h5>
                                <small class="text-white-50 font-monospace">Flight: <?= htmlspecialchars($flight_name); ?></small>
                            </div>
                        </div>
                    </div>

                    <!-- Flight Route Trajectory Card -->
                    <div class="p-4 border-bottom bg-white">
                        <div class="d-flex align-items-center justify-content-between text-center position-relative mb-3">
                            <!-- Departure -->
                            <div class="text-start">
                                <div class="fs-3 fw-black text-dark font-monospace tracking-wider"><?= strtoupper(substr($from_city, 0, 3)); ?></div>
                                <div class="small fw-semibold text-primary text-truncate" style="max-width:90px;"><?= htmlspecialchars($from_city); ?></div>
                                <div class="small text-muted font-monospace"><i class="bi bi-clock me-1"></i><?= htmlspecialchars($departure_time); ?></div>
                            </div>

                            <!-- Trajectory Line & Plane -->
                            <div class="flex-grow-1 mx-3 position-relative text-center">
                                <div class="border-top border-2 border-primary border-dashed w-100 position-absolute top-50 start-0 translate-middle-y" style="z-index:1;"></div>
                                <span class="badge rounded-circle bg-primary text-white p-2 shadow-sm position-relative" style="z-index:2;">
                                    ✈
                                </span>
                            </div>

                            <!-- Arrival -->
                            <div class="text-end">
                                <div class="fs-3 fw-black text-dark font-monospace tracking-wider"><?= strtoupper(substr($to_city, 0, 3)); ?></div>
                                <div class="small fw-semibold text-primary text-truncate" style="max-width:90px;"><?= htmlspecialchars($to_city); ?></div>
                                <div class="small text-muted font-monospace"><i class="bi bi-clock me-1"></i><?= htmlspecialchars($arrival_time); ?></div>
                            </div>
                        </div>

                        <!-- Date & Baggage Badge -->
                        <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded-3 small">
                            <span class="text-muted"><i class="bi bi-calendar3 text-primary me-1"></i><?= date('D, d M Y', strtotime($departure_date)); ?></span>
                            <span class="badge bg-white text-dark border fw-bold"><i class="bi bi-briefcase-fill text-warning me-1"></i> 15kg Bag Free</span>
                        </div>
                    </div>

                    <!-- Passenger Details Card -->
                    <div class="p-4 border-bottom bg-light-subtle">
                        <div class="small text-uppercase fw-bold text-muted mb-2 tracking-wider"><i class="bi bi-person-circle text-primary me-1"></i> Passenger Details</div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-circle bg-primary text-white rounded-circle fw-bold fs-5 d-flex align-items-center justify-content-center shadow-sm" style="width:44px; height:44px; flex-shrink: 0;">
                                <?= strtoupper(substr($firstname, 0, 1)); ?>
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-dark fs-6 text-truncate"><?= htmlspecialchars($gender . ' ' . $fullname); ?></div>
                                <div class="small text-muted text-truncate"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($email); ?></div>
                                <div class="small text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($phone); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Fare Breakdown Card -->
                    <div class="p-4 bg-white">
                        <div class="small text-uppercase fw-bold text-muted mb-3 tracking-wider"><i class="bi bi-receipt-cutoff text-primary me-1"></i> Fare Breakdown</div>
                        
                        <div class="d-flex justify-content-between small mb-2 text-muted">
                            <span>Base Flight Ticket Fare:</span>
                            <span class="fw-bold text-dark">₹<?= number_format($base_fare); ?></span>
                        </div>
                        <div class="d-flex justify-content-between small mb-2 text-muted">
                            <span>Airport Taxes & Surcharges:</span>
                            <span class="fw-bold text-dark">₹<?= number_format($taxes_fees); ?></span>
                        </div>
                        <div class="d-flex justify-content-between small mb-3 text-muted">
                            <span>Convenience & Booking Fee:</span>
                            <span class="text-success fw-bold">FREE</span>
                        </div>

                        <div class="p-3 bg-primary-subtle border border-primary-subtle rounded-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="small text-uppercase fw-bold text-primary d-block">Total Payable Amount</span>
                                <small class="text-muted" style="font-size:0.75rem;">Includes all taxes & fees</small>
                            </div>
                            <div class="fs-3 fw-bold text-primary">₹<?= number_format($total_price); ?></div>
                        </div>
                    </div>

                    <!-- Security Verification Footer -->
                    <div class="p-3 bg-light text-center border-top small text-muted">
                        <i class="bi bi-shield-check text-success me-1"></i> <strong>256-Bit SSL Encrypted</strong> &bull; Bank 2FA Protected
                    </div>

                </div>
            </div>

            <!-- RIGHT COLUMN: MODERN PAYMENT GATEWAY & TABS -->
            <div class="col-lg-8">
                <div class="payment-card-glass p-4 p-md-5">

                    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                        <div>
                            <h4 class="fw-bold mb-1 text-dark"><i class="bi bi-credit-card-fill text-primary me-2"></i>Select Payment Method</h4>
                            <small class="text-muted">Enter details to generate your dynamic 6-digit Bank OTP</small>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-3 py-2 rounded-pill">
                            <i class="bi bi-shield-check me-1"></i> Secure Payment Gateway
                        </span>
                    </div>

                    <!-- PAYMENT TABS NAV -->
                    <ul class="nav nav-pills mb-4 nav-justified bg-light p-2 rounded-3 gap-2" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link pay-tab-btn active" data-bs-toggle="pill" data-bs-target="#cardDebitTab">
                                <i class="bi bi-credit-card me-2"></i>Credit / Debit Card
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link pay-tab-btn" data-bs-toggle="pill" data-bs-target="#upiDebitTab">
                                <i class="bi bi-qr-code-scan me-2"></i>UPI / GPay / PhonePe
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link pay-tab-btn" data-bs-toggle="pill" data-bs-target="#netDebitTab">
                                <i class="bi bi-bank me-2"></i>Net Banking
                            </button>
                        </li>
                    </ul>

                    <!-- TAB PANELS -->
                    <div class="tab-content">

                        <!-- TAB 1: CREDIT / DEBIT CARD WITH LIVE CARD MOCKUP -->
                        <div class="tab-pane fade show active" id="cardDebitTab">
                            
                            <!-- Live Animated Card Display -->
                            <div class="visual-credit-card">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-uppercase tracking-wider small opacity-75">SkyPort Bank Card</span>
                                    <span id="visualCardBrand" class="fw-bold fs-5 text-warning"><i class="bi bi-credit-card"></i> VISA</span>
                                </div>
                                <div class="card-chip"></div>
                                <div class="card-number-display" id="visualCardNumber">•••• •••• •••• ••••</div>
                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <div class="small opacity-50 text-uppercase" style="font-size:0.7rem;">Card Holder</div>
                                        <div class="fw-bold text-uppercase text-truncate" id="visualCardHolder" style="max-width:200px;">CARDHOLDER NAME</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="small opacity-50 text-uppercase" style="font-size:0.7rem;">Expires</div>
                                        <div class="fw-bold font-monospace" id="visualCardExpiry">MM / YY</div>
                                    </div>
                                </div>
                            </div>

                            <form id="cardForm" onsubmit="event.preventDefault(); initiateDynamicOtp('Credit/Debit Card');">
                                
                                <div class="row g-3">

                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold text-uppercase text-muted">Cardholder Name</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text bg-light"><i class="bi bi-person text-secondary"></i></span>
                                            <input type="text" id="cardholder_name" class="form-control fs-6" required placeholder="Enter name as printed on Card" value="" oninput="updateVisualCard()">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label small fw-bold text-uppercase text-muted mb-0">Card Number</label>
                                            <span class="small fw-bold text-primary" id="card_type_label">Visa / MasterCard / RuPay</span>
                                        </div>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text bg-light"><i class="bi bi-credit-card-2-front text-secondary"></i></span>
                                            <input type="text" id="card_number" maxlength="19" class="form-control fs-6 font-monospace" required placeholder="Enter 16-Digit Card Number" value="" oninput="formatCardNumber(this)">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-uppercase text-muted">Expiry Date</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text bg-light"><i class="bi bi-calendar-event text-secondary"></i></span>
                                            <input type="text" id="card_expiry" maxlength="5" class="form-control fs-6 font-monospace" required placeholder="MM / YY" value="" oninput="updateVisualCard()">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-uppercase text-muted">CVV / CVC Code</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text bg-light"><i class="bi bi-shield-lock text-secondary"></i></span>
                                            <input type="password" id="card_cvv" maxlength="4" class="form-control fs-6 font-monospace" required placeholder="CVC" value="">
                                        </div>
                                    </div>

                                </div>

                                <div class="p-3 bg-light rounded-3 mt-4 border d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="small fw-bold text-dark"><i class="bi bi-lock-fill text-success me-1"></i> Amount to Pay</div>
                                        <small class="text-muted">Instant Bank 2FA Authorization</small>
                                    </div>
                                    <div class="fs-4 fw-bold text-primary">₹<?= number_format($total_price); ?></div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold py-3 shadow">
                                        <i class="bi bi-send-fill me-2"></i> Generate Dynamic OTP & Pay ₹<?= number_format($total_price); ?>
                                    </button>
                                </div>

                            </form>
                        </div>

                        <!-- TAB 2: INSTANT UPI DEBIT -->
                        <div class="tab-pane fade" id="upiDebitTab">
                            <form onsubmit="event.preventDefault(); initiateDynamicOtp('UPI Payment');">
                                <div class="p-4 bg-light rounded-4 text-center mb-4 border">
                                    <div class="mb-3">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=upi://pay?pa=skyport@bank&pn=SkyPortAirlines&am=<?= $total_price; ?>" alt="UPI QR Code" class="img-fluid rounded-3 shadow-sm border p-2 bg-white" style="max-width:160px;">
                                    </div>
                                    <div class="fw-bold text-dark mb-1">Scan QR Code using any UPI App</div>
                                    <div class="small text-muted">Supports Google Pay, PhonePe, Paytm & BHIM UPI</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-uppercase text-muted">Or Enter Registered VPA / UPI ID</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light"><i class="bi bi-phone-vibrate text-secondary"></i></span>
                                        <input type="text" id="upi_id" class="form-control fs-6" required placeholder="e.g. 9876543210@upi / name@okaxis" value="">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold py-3 shadow">
                                    <i class="bi bi-qr-code me-2"></i> Request Dynamic OTP for UPI Payment
                                </button>
                            </form>
                        </div>

                        <!-- TAB 3: NET BANKING -->
                        <div class="tab-pane fade" id="netDebitTab">
                            <form onsubmit="event.preventDefault(); initiateDynamicOtp('Net Banking');">
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-uppercase text-muted">Select Your Bank</label>
                                    <select id="bank_select" class="form-select form-select-lg fs-6" required>
                                        <option value="SBI Bank">State Bank of India (SBI)</option>
                                        <option value="HDFC Bank">HDFC Bank</option>
                                        <option value="ICICI Bank">ICICI Bank</option>
                                        <option value="Axis Bank">Axis Bank</option>
                                        <option value="Kotak Bank">Kotak Mahindra Bank</option>
                                    </select>
                                </div>

                                <div class="p-3 bg-light rounded-3 mb-4 border d-flex align-items-center gap-3">
                                    <i class="bi bi-bank2 fs-2 text-primary"></i>
                                    <div>
                                        <div class="fw-bold text-dark small">Official Bank 3D-Secure Authentication</div>
                                        <div class="small text-muted">You will receive a 6-digit OTP on your mobile for approval</div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold py-3 shadow">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Login & Send Dynamic OTP
                                </button>
                            </form>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

<!-- HIDDEN FORM FOR FINAL BOOKING SUBMISSION -->
<form id="finalPayForm" action="payment.php" method="POST" style="display:none;">
    <input type="hidden" name="pay_now" value="1">
    <input type="hidden" name="payment_method" id="final_payment_method" value="Credit/Debit Card">
    <input type="hidden" name="razorpay_payment_id" id="final_razorpay_id" value="">
</form>

<!-- REALISTIC BANK 3D-SECURE OTP VERIFICATION MODAL -->
<div class="bank-otp-overlay" id="bankOtpModal">
    <div class="bank-otp-card">
        <div class="bg-primary text-white p-4 text-center position-relative">
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" onclick="closeBankModal()"></button>
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-bold fs-5"><i class="bi bi-bank2 me-1"></i> Bank 3D-Secure 2FA</span>
                <span class="badge bg-warning text-dark fw-bold">Verified By Gateway</span>
            </div>
            <div class="fs-3 fw-bold">₹<?= number_format($total_price); ?></div>
            <small class="opacity-75">Merchant: SkyPort Airlines Aviation</small>
        </div>
        <div class="p-4">
            
            <div id="otpStatusAlert" class="alert alert-info border-0 shadow-sm small text-center mb-3">
                <i class="bi bi-info-circle-fill me-1"></i> A 6-digit Security OTP has been sent via SMS to <strong><?= htmlspecialchars(substr($phone, 0, 4) . '****' . substr($phone, -2)); ?></strong>
            </div>

            <div class="text-center mb-3">
                <span class="badge bg-light text-dark border px-3 py-2 fs-6 fw-normal">
                    <i class="bi bi-clock me-1 text-primary"></i> OTP Resend in <strong id="otpTimerText" class="text-danger">00:59</strong>
                </span>
            </div>

            <!-- MANUAL OTP FILL TEXT BOX -->
            <div class="mb-3">
                <label class="form-label small fw-bold text-uppercase text-muted d-block text-center mb-2">Enter 6-Digit Bank OTP</label>
                <input type="text" maxlength="6" id="bankOtpInput" class="form-control form-control-lg text-center font-monospace otp-input-box" placeholder="------" autocomplete="off">
                <div class="invalid-feedback text-center fw-bold mt-2" id="otpErrorMessage" style="display:none;">
                    ❌ Invalid OTP! Please enter the correct 6-digit OTP sent to your phone.
                </div>
            </div>

            <button type="button" id="submitOtpBtn" class="btn btn-success btn-lg w-100 rounded-pill fw-bold py-3 shadow mb-2" onclick="verifyAndSubmitOtp()">
                <i class="bi bi-check-circle-fill me-1"></i> Verify OTP & Authorize Payment
            </button>

            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                <button type="button" class="btn btn-link text-decoration-none p-0 small text-primary fw-bold" onclick="resendDynamicOtp()">
                    <i class="bi bi-arrow-repeat me-1"></i> Resend New OTP
                </button>
                <button type="button" class="btn btn-link text-decoration-none p-0 small text-muted" onclick="closeBankModal()">
                    Cancel Transaction
                </button>
            </div>

        </div>
    </div>
</div>

<script>
let currentGeneratedOtp = "";
let selectedPaymentMethod = "Credit/Debit Card";
let otpTimerInterval = null;

// Generate Unique Random 6-Digit OTP
function generateUniqueOtp() {
    currentGeneratedOtp = Math.floor(100000 + Math.random() * 900000).toString();
    return currentGeneratedOtp;
}

// Visual Card Sync
function updateVisualCard() {
    const nameVal = document.getElementById('cardholder_name').value.trim();
    const expiryVal = document.getElementById('card_expiry').value.trim();
    
    document.getElementById('visualCardHolder').innerText = nameVal ? nameVal.toUpperCase() : 'PASSENGER NAME';
    document.getElementById('visualCardExpiry').innerText = expiryVal ? expiryVal : '12 / 28';
}

function formatCardNumber(input) {
    let val = input.value.replace(/\D/g, '');
    let formatted = '';
    for (let i = 0; i < val.length; i++) {
        if (i > 0 && i % 4 === 0) formatted += ' ';
        formatted += val[i];
    }
    input.value = formatted;

    const visualNum = document.getElementById('visualCardNumber');
    if (val.length > 0) {
        visualNum.innerText = formatted;
    } else {
        visualNum.innerText = '•••• •••• •••• ••••';
    }

    const label = document.getElementById('card_type_label');
    const brand = document.getElementById('visualCardBrand');
    if (val.startsWith('4')) {
        label.innerText = '💳 VISA Card';
        brand.innerHTML = '<i class="bi bi-credit-card"></i> VISA';
    } else if (val.startsWith('5')) {
        label.innerText = '💳 MasterCard';
        brand.innerHTML = '<i class="bi bi-credit-card-2-front"></i> MasterCard';
    } else if (val.startsWith('6')) {
        label.innerText = '💳 RuPay Card';
        brand.innerHTML = '<i class="bi bi-credit-card-2-back"></i> RuPay';
    } else {
        label.innerText = 'Visa / MasterCard / RuPay';
        brand.innerHTML = '<i class="bi bi-credit-card"></i> VISA';
    }
}

// Trigger Dynamic OTP Flow
function initiateDynamicOtp(methodName) {
    selectedPaymentMethod = methodName;
    const otp = generateUniqueOtp();

    // Clear input box
    const otpInput = document.getElementById('bankOtpInput');
    otpInput.value = '';
    document.getElementById('otpErrorMessage').style.display = 'none';

    // Send Real OTP Dispatch Request to Backend Server (SMS + Email)
    fetch('send_otp_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'phone=' + encodeURIComponent('<?= htmlspecialchars($phone); ?>') + '&email=' + encodeURIComponent('<?= htmlspecialchars($email); ?>') + '&otp=' + encodeURIComponent(otp)
    }).then(r => r.json()).then(data => {
        console.log('Real OTP Dispatch Result:', data);
        if (data.email_sent) {
            const alertBox = document.getElementById('otpStatusAlert');
            alertBox.className = "alert alert-success border-0 shadow-sm small text-center mb-3";
            alertBox.innerHTML = "<i class='bi bi-envelope-check-fill me-1'></i> <strong>Real OTP Sent to Email & Mobile!</strong> Check your Email Inbox or SMS alert.";
        }
    }).catch(e => console.error('OTP Send Error:', e));

    // Open Modal
    document.getElementById('bankOtpModal').style.display = 'flex';
    setTimeout(() => otpInput.focus(), 300);

    // Start 60-Second Countdown Timer
    startOtpTimer(59);
}

function resendDynamicOtp() {
    const newOtp = generateUniqueOtp();
    
    const alertBox = document.getElementById('otpStatusAlert');
    alertBox.className = "alert alert-success border-0 shadow-sm small text-center mb-3";
    alertBox.innerHTML = "<i class='bi bi-check-circle-fill me-1'></i> <strong>New OTP Generated & Sent!</strong> Check your Email Inbox or Mobile Phone.";

    fetch('send_otp_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'phone=' + encodeURIComponent('<?= htmlspecialchars($phone); ?>') + '&email=' + encodeURIComponent('<?= htmlspecialchars($email); ?>') + '&otp=' + encodeURIComponent(newOtp)
    });
    
    startOtpTimer(59);
}

function startOtpTimer(seconds) {
    if (otpTimerInterval) clearInterval(otpTimerInterval);
    let sec = seconds;
    const timerDisplay = document.getElementById('otpTimerText');
    
    otpTimerInterval = setInterval(() => {
        if (sec <= 0) {
            clearInterval(otpTimerInterval);
            timerDisplay.innerText = "00:00 (Expired)";
        } else {
            let m = Math.floor(sec / 60);
            let s = sec % 60;
            timerDisplay.innerText = (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
            sec--;
        }
    }, 1000);
}

function closeBankModal() {
    document.getElementById('bankOtpModal').style.display = 'none';
    if (otpTimerInterval) clearInterval(otpTimerInterval);
}

// Verify User Input OTP
function verifyAndSubmitOtp() {
    const userEnteredOtp = document.getElementById('bankOtpInput').value.trim();
    const errorMsg = document.getElementById('otpErrorMessage');
    const submitBtn = document.getElementById('submitOtpBtn');

    if (!userEnteredOtp) {
        errorMsg.innerText = "⚠️ Please enter the 6-digit OTP.";
        errorMsg.style.display = 'block';
        return;
    }

    if (userEnteredOtp !== currentGeneratedOtp) {
        errorMsg.innerText = "❌ Invalid OTP! The OTP you entered (" + userEnteredOtp + ") does not match the generated OTP.";
        errorMsg.style.display = 'block';
        document.getElementById('bankOtpInput').classList.add('is-invalid');
        return;
    }

    // Success State
    errorMsg.style.display = 'none';
    document.getElementById('bankOtpInput').classList.remove('is-invalid');
    document.getElementById('bankOtpInput').classList.add('is-valid');
    
    submitBtn.className = "btn btn-success btn-lg w-100 rounded-pill fw-bold py-3 shadow mb-2 disabled";
    submitBtn.innerHTML = "<span class='spinner-border spinner-border-sm me-2'></span> Payment Authorized! Redirecting...";

    setTimeout(() => {
        document.getElementById('final_payment_method').value = selectedPaymentMethod;
        document.getElementById('final_razorpay_id').value = 'pay_' + Math.random().toString(36).substring(2, 15);
        document.getElementById('finalPayForm').submit();
    }, 1200);
}
</script>

<?php include 'include/footer.php'; ?>
