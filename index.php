<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'include/db_json.php';
$latest_news = get_all_news();
include 'include/header.php';
?>

<!-- HERO & SEARCH SECTION -->
<header class="hero-section py-5 position-relative text-white" style="background-image: url('images/aeroplane.png'); background-size: cover; background-position: center; margin-top: -85px;">
    <div class="container hero-content py-5">
        
        <!-- Hero Title Banner -->
        <div class="row align-items-center mb-4">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Book Your Dream Flight Fast & Easy!</h1>
                <p class="lead mb-5 fw-bold text-white-50">Real-time schedules, flexible ticketing, and instant boarding pass generation.</p>
            </div>
        </div>

        <!-- Flight Search Card Form -->
        <div id="search" class="search-card card shadow-lg text-dark border-0 rounded-4">
            <div class="card-body p-4">
                
                <form action="flight.php" method="POst" id="flightSearchForm">
                    
                    <!-- Trip Type Selection -->
                    <fieldset class="mb-3">
                        <legend class="visually-hidden">Trip Type</legend>
                        <div class="d-flex align-items-center">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="trip_type" value="oneway" id="oneway">
                                <label class="form-check-label fw-semibold" for="oneway">One Way</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="trip_type" value="roundtrip" id="roundtrip" checked>
                                <label class="form-check-label fw-semibold" for="roundtrip">Round Trip</label>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Search Form Inputs -->
                    <div class="row g-3 align-items-end">
                        
                        <!-- From City Selection -->
                        <div class="col-md-3">
                            <label for="fromCitySelect" class="form-label fw-semibold small text-muted">From City</label>
                            <select id="fromCitySelect" name="from_city" class="form-select form-select-sm" required>
                                <option value="">Select Origin City</option>
                                <option value="Delhi">Delhi (DEL)</option>
                                <option value="Mumbai">Mumbai (BOM)</option>
                                <option value="Bangalore">Bangalore (BLR)</option>
                                <option value="Hyderabad">Hyderabad (HYD)</option>
                                <option value="Chennai">Chennai (MAA)</option>
                                <option value="Kolkata">Kolkata (CCU)</option>                              
                                <option value="Ahmedabad">Ahmedabad (AMD)</option>
                                <option value="Pune">Pune (PNQ)</option>
                                <option value="Goa">Goa (GOX)</option>
                                <option value="Jaipur">Jaipur (JAI)</option>
                                <option value="Lucknow">Lucknow (LKO)</option>
                                <option value="Patna">Patna (PAT)</option>                              
                                <option value="Surat">Surat (STV)</option>
                                <option value="Rajkot">Rajkot (RAJ)</option>
                                <option value="Vadodara">Vadodara (BDQ)</option>
                                <option value="Indore">Indore (IDR)</option>
                                <option value="Bhopal">Bhopal (BHO)</option>
                                <option value="Nagpur">Nagpur (NAG)</option>                              
                                <option value="Chandigarh">Chandigarh (IXC)</option>
                                <option value="Jammu">Jammu (IXJ)</option>
                                <option value="Srinagar">Srinagar (SXR)</option>
                                <option value="Amritsar">Amritsar (ATQ)</option>
                                <option value="Varanasi">Varanasi (VNS)</option>
                                <option value="Ranchi">Ranchi (IXR)</option>                              
                                <option value="Bhubaneswar">Bhubaneswar (BBI)</option>
                                <option value="Guwahati">Guwahati (GAU)</option>
                                <option value="Kochi">Kochi (COK)</option>
                                <option value="Coimbatore">Coimbatore (CJB)</option>
                                <option value="Visakhapatnam">Visakhapatnam (VTZ)</option>
                                <option value="Thiruvananthapuram">Thiruvananthapuram (TRV)</option>
                            </select>
                        </div>

                        <!-- To City Selection -->
                        <div class="col-md-3">
                            <label for="toCitySelect" class="form-label fw-semibold small text-muted">To City</label>
                            <select id="toCitySelect" name="to_city" class="form-select form-select-sm" required>
                                <option value="">Select Destination City</option>
                                <option value="Delhi">Delhi (DEL)</option>
                                <option value="Mumbai">Mumbai (BOM)</option>
                                <option value="Bangalore">Bangalore (BLR)</option>
                                <option value="Hyderabad">Hyderabad (HYD)</option>
                                <option value="Chennai">Chennai (MAA)</option>
                                <option value="Kolkata">Kolkata (CCU)</option>                              
                                <option value="Ahmedabad">Ahmedabad (AMD)</option>
                                <option value="Pune">Pune (PNQ)</option>
                                <option value="Goa">Goa (GOX)</option>
                                <option value="Jaipur">Jaipur (JAI)</option>
                                <option value="Lucknow">Lucknow (LKO)</option>
                                <option value="Patna">Patna (PAT)</option>                              
                                <option value="Surat">Surat (STV)</option>
                                <option value="Rajkot">Rajkot (RAJ)</option>
                                <option value="Vadodara">Vadodara (BDQ)</option>
                                <option value="Indore">Indore (IDR)</option>
                                <option value="Bhopal">Bhopal (BHO)</option>
                                <option value="Nagpur">Nagpur (NAG)</option>                              
                                <option value="Chandigarh">Chandigarh (IXC)</option>
                                <option value="Jammu">Jammu (IXJ)</option>
                                <option value="Srinagar">Srinagar (SXR)</option>
                                <option value="Amritsar">Amritsar (ATQ)</option>
                                <option value="Varanasi">Varanasi (VNS)</option>
                                <option value="Ranchi">Ranchi (IXR)</option>                              
                                <option value="Bhubaneswar">Bhubaneswar (BBI)</option>
                                <option value="Guwahati">Guwahati (GAU)</option>
                                <option value="Kochi">Kochi (COK)</option>
                                <option value="Coimbatore">Coimbatore (CJB)</option>
                                <option value="Visakhapatnam">Visakhapatnam (VTZ)</option>
                                <option value="Thiruvananthapuram">Thiruvananthapuram (TRV)</option>
                            </select>
                        </div>

                        <!-- Departure Date Input -->
                        <div class="col-md-2">
                            <label for="departureDate" class="form-label fw-semibold small text-muted">Departure Date</label>
                            <input type="text" id="departureDate" name="departure_date" class="form-control py-2 bg-white" placeholder="Select Date" readonly required>
                        </div>

                        <!-- Return Date Input -->
                        <div class="col-md-2" id="returnBox">
                            <label for="returnDate" class="form-label fw-semibold small text-muted">Return Date</label>
                            <input type="text" id="returnDate" name="return_date" class="form-control py-2 bg-white" placeholder="Select Date" readonly>
                        </div>

                        <!-- Passengers / Travel Class Dropdown -->
                        <div class="col-md-2 position-relative" id="passengerBox">
                            <label for="passengerDropdownBtn" class="form-label fw-semibold small text-muted">Passengers / Class</label>
                            <button type="button" id="passengerDropdownBtn" class="form-control py-2 text-start bg-white text-truncate">
                                <span id="summary">1 Adult, Economy</span>
                            </button>

                            <!-- Passenger Selector Popup Menu -->
                            <div id="passengerMenu"
     class="passenger-menu shadow-lg p-3 bg-white rounded border position-absolute w-100"
     style="display: none; z-index: 1000; bottom: 100%; margin-bottom: 8px;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-semibold">Adults</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="passenger-count-btn" onclick="changeCount('adult', -1)" aria-label="Decrease Adults">−</button>
                                        <span id="adult" class="fw-bold px-2">1</span>
                                        <button type="button" class="passenger-count-btn" onclick="changeCount('adult', 1)" aria-label="Increase Adults">+</button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-semibold">Children</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="passenger-count-btn" onclick="changeCount('child', -1)" aria-label="Decrease Children">−</button>
                                        <span id="child" class="fw-bold px-2">0</span>
                                        <button type="button" class="passenger-count-btn" onclick="changeCount('child', 1)" aria-label="Increase Children">+</button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="travelClass" class="small text-muted mb-1">Travel Class</label>
                                    <select id="travelClass" class="form-select form-select-sm" onchange="updateSummary()">
                                        <option value="Economy">Economy</option>
                                        <option value="Business">Business</option>
                                        <option value="First Class">First Class</option>
                                    </select>
                                </div>
                                <button type="button" id="donePassenger" class="btn btn-primary btn-sm w-100">Done</button>
                            </div>

                            <input type="hidden" name="passenger_class" id="passenger_class" value="1 Adult, Economy">
                        </div>

                        <!-- Form Submit Button -->
                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-primary btn-lg px-3 fw-bold text-white shadow-sm">
                                <i class="bi bi-search me-2"></i> Search Flights
                            </button>
                        </div>

                    </div>

                </form>
            </div>
        </div>

    </div>
</header>

<!-- MAIN CONTENT SECTION -->
<main class="container my-5">
    
    <!-- Feature Info Cards -->
    <section class="row my-4 g-3" aria-label="Service Features">
        <div class="col-md-4">
            <article class="card p-3 border-0 shadow-sm rounded-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="fs-2 text-primary me-3"><i class="bi bi-headset"></i></div>
                    <div>
                        <h2 class="h6 mb-0 fw-bold">24/7 Support Available</h2>
                        <small class="text-muted">Call +1 800 123 4567</small>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-md-4">
            <article class="card p-3 border-0 shadow-sm rounded-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="fs-2 text-primary me-3"><i class="bi bi-globe2"></i></div>
                    <div>
                        <h2 class="h6 mb-0 fw-bold">Domestic & International</h2>
                        <small class="text-muted">Connecting 50+ Top Destinations</small>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-md-4">
            <article class="card p-3 border-0 shadow-sm rounded-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="fs-2 text-primary me-3"><i class="bi bi-shield-check"></i></div>
                    <div>
                        <h2 class="h6 mb-0 fw-bold">Instant Web Check-in</h2>
                        <small class="text-muted">Fast boarding pass generation</small>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <!-- Key Statistics & Achievements -->
    <section class="card border-0 shadow-sm rounded-4 p-4 my-5 bg-white" aria-label="SkyPort Statistics">
        <h2 class="h3 fw-bold text-center mb-4">Why Choose SkyPort?</h2>
        <div class="row text-center g-3">
            <div class="col-md-4 col-6">
                <p class="h2 fw-bold text-primary mb-0">250,000+</p>
                <small class="text-muted">Flights Managed</small>
            </div>
            <div class="col-md-4 col-6">
                <p class="h2 fw-bold text-primary mb-0">32M+</p>
                <small class="text-muted">Passengers Served</small>
            </div>
            <div class="col-md-4 col-6">
                <p class="h2 fw-bold text-primary mb-0">98.5%</p>
                <small class="text-muted">On-Time Performance</small>
            </div>
        </div>
    </section>

    <!-- Our Latest News (Live Daily Airline & Aviation Updates) -->
    <section class="my-5" id="latest-news" aria-label="Latest Airline News">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill mb-2">
                    <i class="bi bi-newspaper me-1"></i> Aviation Insights
                </span>
                <h2 class="h3 fw-bold text-dark mb-1">Our Latest News</h2>
                <p class="text-muted small mb-0">Live daily updates on flights, route expansions, travel guidelines, and aviation technology</p>
            </div>
            <div class="mt-2 mt-md-0">
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2">
                    <i class="bi bi-broadcast me-1 animate-pulse"></i> Live Daily Updates
                </span>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach (array_slice($latest_news, 0, 3) as $news): ?>
                <div class="col-lg-4 col-md-6">
                    <article class="card news-card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        <a href="news.php?id=<?= urlencode($news['id']); ?>" class="text-decoration-none">
                            <div class="position-relative overflow-hidden">
                                <img src="<?= htmlspecialchars($news['image']); ?>" class="card-img-top news-img" alt="<?= htmlspecialchars($news['title']); ?>">
                                <span class="badge bg-<?= htmlspecialchars($news['category_color'] ?? 'primary'); ?> position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill shadow-sm">
                                    <?= htmlspecialchars($news['category']); ?>
                                </span>
                            </div>
                        </a>
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between text-muted small mb-2">
                                <span><i class="bi bi-calendar3 me-1"></i> <?= htmlspecialchars($news['formatted_date']); ?></span>
                                <span><i class="bi bi-clock me-1"></i> <?= htmlspecialchars($news['read_time']); ?></span>
                            </div>
                            <h3 class="h6 card-title fw-bold text-dark mb-2">
                                <a href="news.php?id=<?= urlencode($news['id']); ?>" class="text-dark text-decoration-none hover-primary">
                                    <?= htmlspecialchars($news['title']); ?>
                                </a>
                            </h3>
                            <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars($news['summary']); ?></p>
                            <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                                <a href="news.php?id=<?= urlencode($news['id']); ?>" class="text-primary fw-semibold small text-decoration-none">
                                    Read Full Story <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-eye-fill text-primary me-1"></i> <?= htmlspecialchars($news['views'] ?? 'Trending'); ?>
                                </span>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

</main>

<!-- SLEEK COMPACT SAME CITY ERROR POPUP -->
<div class="modal fade" id="sameCityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content border-0 shadow-lg rounded-4 text-center overflow-hidden p-3" style="background: #ffffffff;">
            <div class="modal-body p-3">
                <div class="mb-3">
                    <span class="bg-danger-subtle text-danger rounded-circle p-3 d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px;">
                        <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-1">Same City are not valid</h5>
                <p class="text-muted small mb-3">
                    Origin and destination cannot be the same city (<strong class="text-primary"><span id="sameCityNameDetail">Ahmedabad</span></strong>). Please choose a different destination.
                </p>
                <button type="button" class="btn btn-primary btn-sm w-100 rounded-pill fw-bold py-2 shadow-sm" data-bs-dismiss="modal">
                    Please! Change your Destination
                </button>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>


