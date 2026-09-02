<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'include/header.php';
?>

<style>
    .docs-hero {
        background: linear-gradient(135deg, #0b192c 0%, #0f2b5c 50%, #1e3a8a 100%);
        color: #ffffff;
        padding: 50px 0;
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.15);
        margin-bottom: 40px;
    }
    .cert-box {
        background: #ffffff;
        border-radius: 20px;
        border: 2px solid #0d6efd;
        padding: 28px;
        box-shadow: 0 10px 30px rgba(13, 110, 253, 0.08);
        margin-bottom: 35px;
    }
    .step-card {
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        overflow: hidden;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        transition: all 0.25s ease;
    }
    .step-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 40px rgba(13, 110, 253, 0.12);
        border-color: #0d6efd;
    }
    .step-number-badge {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #0d6efd;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1rem;
        margin-right: 12px;
    }
</style>

<div class="container my-5">
    
    <!-- DOCS HERO -->
    <div class="docs-hero text-center position-relative overflow-hidden">
        <div class="container px-4">
            <span class="badge bg-white bg-opacity-15 text-white border border-white border-opacity-25 px-3 py-2 rounded-pill fw-semibold mb-3">
                <i class="bi bi-person-badge-fill me-1 text-warning"></i> Student Academic Major Project Documentation
            </span>
            <h1 class="fw-bold display-5 mb-2">SkyPort Airline Booking System</h1>
            <p class="text-white-50 max-w-lg mx-auto mb-4" style="max-width: 650px;">
                Complete step-by-step chronological user flow (Login ➔ Flight Search ➔ Checkout ➔ Payment ➔ Confirmation) with real website action screenshots.
            </p>

            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="generate_docs_pdf.php" target="_blank" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold shadow">
                    <i class="bi bi-file-earmark-pdf-fill me-2"></i> Download Official 10-Page Documentation PDF
                </a>
                <a href="SkyPort_Project_Documentation.pdf" download class="btn btn-outline-light btn-lg rounded-pill px-4 fw-semibold">
                    <i class="bi bi-download me-2"></i> Direct PDF Download
                </a>
            </div>
        </div>
    </div>

    <!-- STUDENT INTRO & ACADEMIC CERTIFICATE -->
    <div class="cert-box">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
            <div>
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill mb-1">Academic Certificate</span>
                <h4 class="fw-bold text-dark m-0">Student Project Submission Metadata</h4>
            </div>
            <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Verified Authentic Project</span>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3">
                    <small class="text-muted d-block">Student Developer Name</small>
                    <strong class="text-dark fs-5">Manthan</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3">
                    <small class="text-muted d-block">Academic Semester</small>
                    <strong class="text-dark fs-5">Semester 5 (Sem-5)</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3">
                    <small class="text-muted d-block">Department & Course</small>
                    <strong class="text-dark fs-5">Computer Science & Engineering</strong>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 bg-light rounded-3">
                    <small class="text-muted d-block">Project Name & Scope</small>
                    <strong class="text-dark">SkyPort — Airline Ticket Booking & Management System</strong>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 bg-light rounded-3">
                    <small class="text-muted d-block">Submission Date</small>
                    <strong class="text-dark"><?= date('F d, Y'); ?></strong>
                </div>
            </div>
        </div>
    </div>

    <!-- CHRONOLOGICAL STEP-BY-STEP FLOW WITH REAL SCREENSHOTS -->
    <h3 class="fw-bold text-dark mb-4"><i class="bi bi-diagram-3-fill me-2 text-primary"></i>Chronological Project Flow (Login to Confirmation & Beyond)</h3>

    <!-- STEP 1: LOGIN -->
    <div class="step-card">
        <div class="row g-0 align-items-center">
            <div class="col-lg-6">
                <img src="screenshots/1_login_real.png" class="img-fluid w-100 border-end" alt="User Login Portal" style="max-height: 350px; object-fit: cover; object-position: top;">
            </div>
            <div class="col-lg-6 p-4">
                <div class="d-flex align-items-center mb-2">
                    <span class="step-number-badge">1</span>
                    <h5 class="fw-bold text-dark m-0">User Account Login & Security (login.php)</h5>
                </div>
                <p class="text-muted small mb-3">
                    Travelers enter their registered email and password. Passwords are verified using PHP <code>password_hash()</code>, and session state is initialized via <code>$_SESSION['user_email']</code>.
                </p>
                <div class="p-3 bg-light rounded-3 border">
                    <small class="fw-bold text-primary d-block mb-1"><i class="bi bi-shield-check me-1"></i> How It Works In Action:</small>
                    <small class="text-muted">Authenticates credentials securely against <code>users.json</code> and redirects directly to <code>index.php</code> without exposing parameters in the URL.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 2: SEARCH ENGINE -->
    <div class="step-card">
        <div class="row g-0 align-items-center">
            <div class="col-lg-6 order-lg-2">
                <img src="screenshots/2_home_search_real.png" class="img-fluid w-100 border-start" alt="Flight Search Box" style="max-height: 350px; object-fit: cover; object-position: top;">
            </div>
            <div class="col-lg-6 p-4 order-lg-1">
                <div class="d-flex align-items-center mb-2">
                    <span class="step-number-badge">2</span>
                    <h5 class="fw-bold text-dark m-0">Home Page & Dynamic Search Box (index.php)</h5>
                </div>
                <p class="text-muted small mb-3">
                    Features origin/destination city selection, one-way/round-trip date pickers, passenger counter modal, and **Dynamic Combobox Option Hiding**.
                </p>
                <div class="p-3 bg-light rounded-3 border">
                    <small class="fw-bold text-primary d-block mb-1"><i class="bi bi-funnel-fill me-1"></i> How It Works In Action:</small>
                    <small class="text-muted">Selecting "Delhi" in From City triggers JavaScript <code>syncCityOptions()</code>, which dynamically removes "Delhi" from the To City dropdown list to prevent invalid same-city route bookings.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 3: FLEET RESULTS -->
    <div class="step-card">
        <div class="row g-0 align-items-center">
            <div class="col-lg-6">
                <img src="screenshots/3_flight_results_real.png" class="img-fluid w-100 border-end" alt="Flight Fleet Selection" style="max-height: 350px; object-fit: cover; object-position: top;">
            </div>
            <div class="col-lg-6 p-4">
                <div class="d-flex align-items-center mb-2">
                    <span class="step-number-badge">3</span>
                    <h5 class="fw-bold text-dark m-0">Flight Fleet Selection & Fares (flight.php)</h5>
                </div>
                <p class="text-muted small mb-3">
                    Queries <code>flights.json</code> and renders live fleet schedules with carrier logos (IndiGo, Air India, SpiceJet), departure/arrival times, and fare prices.
                </p>
                <div class="p-3 bg-light rounded-3 border">
                    <small class="fw-bold text-primary d-block mb-1"><i class="bi bi-airplane-engines me-1"></i> How It Works In Action:</small>
                    <small class="text-muted">If a specific route has fewer static flights, <code>generate_dynamic_route_flights()</code> synthesizes full schedules with accurate pricing and timings in real-time.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 4: PASSENGER CHECKOUT -->
    <div class="step-card">
        <div class="row g-0 align-items-center">
            <div class="col-lg-6 order-lg-2">
                <img src="screenshots/4_detail_checkout_real.png" class="img-fluid w-100 border-start" alt="Passenger Checkout Form" style="max-height: 350px; object-fit: cover; object-position: top;">
            </div>
            <div class="col-lg-6 p-4 order-lg-1">
                <div class="d-flex align-items-center mb-2">
                    <span class="step-number-badge">4</span>
                    <h5 class="fw-bold text-dark m-0">Passenger Details & Checkout Form (detail.php)</h5>
                </div>
                <p class="text-muted small mb-3">
                    Collects traveler contact information (First Name, Last Name, Email, Mobile Phone) and displays selected seat class specs.
                </p>
                <div class="p-3 bg-light rounded-3 border">
                    <small class="fw-bold text-primary d-block mb-1"><i class="bi bi-person-bounding-box me-1"></i> How It Works In Action:</small>
                    <small class="text-muted">Validates all fields, calculates total fare including taxes, and preserves reservation state before proceeding to payment authentication.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 5: PAYMENT OTP -->
    <div class="step-card">
        <div class="row g-0 align-items-center">
            <div class="col-lg-6">
                <img src="screenshots/5_payment_otp_real.png" class="img-fluid w-100 border-end" alt="Payment Gateway OTP" style="max-height: 350px; object-fit: cover; object-position: top;">
            </div>
            <div class="col-lg-6 p-4">
                <div class="d-flex align-items-center mb-2">
                    <span class="step-number-badge">5</span>
                    <h5 class="fw-bold text-dark m-0">Payment Gateway & Simulated OTP (payment.php)</h5>
                </div>
                <p class="text-muted small mb-3">
                    Offers Credit/Debit Card, UPI, and NetBanking payment choices with an interactive simulated OTP verification popup modal.
                </p>
                <div class="p-3 bg-light rounded-3 border">
                    <small class="fw-bold text-primary d-block mb-1"><i class="bi bi-credit-card-2-front me-1"></i> How It Works In Action:</small>
                    <small class="text-muted">Verifying OTP generates a unique 6-character PNR reference code (e.g. <code>SKP982</code>) and serializes the complete booking into <code>bookings.json</code>.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 6: CONFIRMATION & PDF TICKET -->
    <div class="step-card">
        <div class="row g-0 align-items-center">
            <div class="col-lg-6 order-lg-2">
                <img src="screenshots/6_confirmation_real.png" class="img-fluid w-100 border-start" alt="Booking Confirmation" style="max-height: 350px; object-fit: cover; object-position: top;">
            </div>
            <div class="col-lg-6 p-4 order-lg-1">
                <div class="d-flex align-items-center mb-2">
                    <span class="step-number-badge">6</span>
                    <h5 class="fw-bold text-dark m-0">Booking Confirmation & Digital Stub (confirmation.php)</h5>
                </div>
                <p class="text-muted small mb-3">
                    Presents a dark navy hero card with green pulse icon, one-click **Copy PNR** button, digital ticket stub, live airport QR code, and PDF download button.
                </p>
                <div class="p-3 bg-light rounded-3 border">
                    <small class="fw-bold text-primary d-block mb-1"><i class="bi bi-file-earmark-pdf me-1"></i> How It Works In Action:</small>
                    <small class="text-muted">Directly triggers `download_pdf.php` to stream pure vector A4 PDF tickets generated in-memory via `SkyPortPDF` engine.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 7: WEB CHECK-IN -->
    <div class="step-card">
        <div class="row g-0 align-items-center">
            <div class="col-lg-6">
                <img src="screenshots/7_webcheckin_real.png" class="img-fluid w-100 border-end" alt="Web Check-In Portal" style="max-height: 350px; object-fit: cover; object-position: top;">
            </div>
            <div class="col-lg-6 p-4">
                <div class="d-flex align-items-center mb-2">
                    <span class="step-number-badge">7</span>
                    <h5 class="fw-bold text-dark m-0">Instant Web Check-In & Gate QR Pass (webcheckin.php)</h5>
                </div>
                <p class="text-muted small mb-3">
                    Enables passengers to enter PNR number, pick aircraft seats (e.g. Seat 12F), select meal & baggage preferences under 30 seconds.
                </p>
                <div class="p-3 bg-light rounded-3 border">
                    <small class="fw-bold text-primary d-block mb-1"><i class="bi bi-qr-code me-1"></i> How It Works In Action:</small>
                    <small class="text-muted">Generates a digital boarding pass complete with Terminal 2 / Gate A12 zone details and live airport gate QR scanner code.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 8: MY BOOKINGS -->
    <div class="step-card">
        <div class="row g-0 align-items-center">
            <div class="col-lg-6 order-lg-2">
                <img src="screenshots/8_mybooking_real.png" class="img-fluid w-100 border-start" alt="My Trips Dashboard" style="max-height: 350px; object-fit: cover; object-position: top;">
            </div>
            <div class="col-lg-6 p-4 order-lg-1">
                <div class="d-flex align-items-center mb-2">
                    <span class="step-number-badge">8</span>
                    <h5 class="fw-bold text-dark m-0">Traveler Dashboard & Cancellation Sync (mybooking.php)</h5>
                </div>
                <p class="text-muted small mb-3">
                    Displays all reservations tied to user accounts or PNR searches with quick action pills for viewing tickets and boarding passes.
                </p>
                <div class="p-3 bg-light rounded-3 border">
                    <small class="fw-bold text-danger d-block mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> How Cancellation Sync Works:</small>
                    <small class="text-muted">If an admin cancels a flight schedule or booking, the trip immediately renders a red <code>CANCELLED</code> badge and warning notice.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 9: ADMIN PANEL -->
    <div class="step-card">
        <div class="row g-0 align-items-center">
            <div class="col-lg-6">
                <img src="screenshots/9_admin_real.png" class="img-fluid w-100 border-end" alt="Admin Control Panel" style="max-height: 350px; object-fit: cover; object-position: top;">
            </div>
            <div class="col-lg-6 p-4">
                <div class="d-flex align-items-center mb-2">
                    <span class="step-number-badge">9</span>
                    <h5 class="fw-bold text-dark m-0">Super-Admin Control Panel (admin/index.php)</h5>
                </div>
                <p class="text-muted small mb-3">
                    Super-admin control dashboard displaying Active Flights count, Total Bookings, and Total Revenue analytics counters.
                </p>
                <div class="p-3 bg-light rounded-3 border">
                    <small class="fw-bold text-primary d-block mb-1"><i class="bi bi-sliders me-1"></i> How It Works In Action:</small>
                    <small class="text-muted">Allows adding new flight routes, updating ticket prices, and deleting flight schedules (which auto-cancels passenger bookings).</small>
                </div>
            </div>
        </div>
    </div>

    <!-- EASY COLLEGE VIVA SPEAKING GUIDE & SCRIPT -->
    <div class="card border-0 shadow-sm rounded-4 p-4 my-5 bg-white border-start border-4 border-warning">
        <div class="d-flex align-items-center gap-2 mb-3">
            <div class="step-number-badge bg-warning text-dark"><i class="bi bi-mic-fill"></i></div>
            <h4 class="fw-bold text-dark m-0">🎙️ Super Easy Viva Presentation Script & Speaking Guide</h4>
        </div>
        <p class="text-muted small mb-4">
            College Viva me bolne ke liye super easy Hindi/English lines. In points ko padhein aur examiner ke saamne aasaani se bolen:
        </p>

        <div class="row g-3">
            <div class="col-12">
                <div class="p-3 bg-light rounded-3 border">
                    <h6 class="fw-bold text-primary mb-1"><i class="bi bi-chat-quote-fill me-2"></i>1. Project Introduction (Sabse pehle kya bolna hai):</h6>
                    <p class="text-dark small m-0">
                        "Good morning Sir/Ma'am. Mera naam <strong>Manthan</strong> hai, Semester 5 CSE ka student. Mera project hai <strong>SkyPort — Airline Ticket Booking & Management System</strong>. Yeh ek full-stack web application hai jisme real-time flight search, seat reservation, OTP payment verification, 6-digit PNR code generation, PDF ticket download, Web Check-In (Seat 12F), aur Admin control panel features hain."
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border h-100">
                    <h6 class="fw-bold text-success mb-1"><i class="bi bi-check-circle-fill me-2"></i>Q1: Database kya use kiya hai?</h6>
                    <p class="text-muted small m-0">
                        "Sir, humne lightweight <strong>JSON Flat-File Database</strong> (<code>flights.json</code>, <code>bookings.json</code>, <code>users.json</code>) with <code>LOCK_EX</code> file locking use kiya hai. Isse bina kisi heavy SQL database setup ke fast read/write hota hai."
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border h-100">
                    <h6 class="fw-bold text-success mb-1"><i class="bi bi-check-circle-fill me-2"></i>Q2: Search box me special kya hai?</h6>
                    <p class="text-muted small m-0">
                        "Sir, search box me <strong>Dynamic Combobox Option Hiding</strong> feature hai. Jab user Origin (e.g. Delhi) chunata hai, to JavaScript <code>syncCityOptions()</code> function instantly Destination dropdown list se Delhi ko hide kar deta hai taaki wrong route booking na ho."
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border h-100">
                    <h6 class="fw-bold text-success mb-1"><i class="bi bi-check-circle-fill me-2"></i>Q3: PDF Ticket kaise banta hai?</h6>
                    <p class="text-muted small m-0">
                        "Sir, humne pure-PHP me custom <code>SkyPortPDF</code> class likhi hai jo in-memory A4 vector PDF streams bina kisi third-party library ke create karti hai."
                    </p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border h-100">
                    <h6 class="fw-bold text-danger mb-1"><i class="bi bi-check-circle-fill me-2"></i>Q4: Admin Flight cancel kare to kya hoga?</h6>
                    <p class="text-muted small m-0">
                        "Sir, Admin Panel me flight delete hone par <code>bookings.json</code> me status <code>Cancelled</code> update hota hai aur user ke 'My Bookings' dashboard me red badge dikhta hai."
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- DOWNLOAD FOOTER ACTION -->
    <div class="text-center my-5 p-5 bg-white rounded-4 shadow-sm border">
        <h4 class="fw-bold text-dark mb-2">Need a Printable Copy for College Submission?</h4>
        <p class="text-muted small mb-4">Click below to generate and download the official 10-page printable PDF technical documentation report.</p>
        <a href="generate_docs_pdf.php" target="_blank" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow">
            <i class="bi bi-file-earmark-pdf-fill me-2"></i> Open / Download 10-Page Documentation PDF
        </a>
    </div>

</div>

<?php include 'include/footer.php'; ?>
