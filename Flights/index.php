<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'include/header.php';
?>

<!-- HERO SECTION -->
<header class="hero-section py-5 position-relative text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); margin-top: -75px;">
    <div class="container hero-content py-5">
        <div class="row align-items-center mb-4">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Book Your Dream Flight Fast & Easy!</h1>
                <p class="lead mb-0 text-white-50">Real-time schedules, flexible ticketing, and instant boarding pass generation.</p>
            </div>
        </div>

        <!-- SEARCH CARD -->
        <div id="search" class="search-card card shadow-lg text-dark border-0 rounded-4">
            <div class="card-body p-4">
                
                <form action="flight.php" method="GET">
                    
                    <!-- Trip Type Radio Buttons -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="form-check me-4">
                            <input class="form-check-input" type="radio" name="trip_type" value="oneway" id="oneway">
                            <label class="form-check-label fw-semibold" for="oneway">One Way</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="trip_type" value="roundtrip" id="roundtrip" checked>
                            <label class="form-check-label fw-semibold" for="roundtrip">Round Trip</label>
                        </div>
                    </div>

                    <div class="row g-3 align-items-end">
                        <!-- From City -->
                        <div class="col-md-3 position-relative">
                            <label class="form-label fw-semibold small text-muted">From City</label>
                            <select name="from_city" class="form-select py-2" required>
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
                              <option value="Kanpur">Kanpur (KNU)</option>
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

                        <!-- To City -->
                        <div class="col-md-3 position-relative">
                            <label class="form-label fw-semibold small text-muted">To City</label>
                            <select name="to_city" class="form-select py-2" required>
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
                              <option value="Kanpur">Kanpur (KNU)</option>
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

                        <!-- Departure Date -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small text-muted">Departure Date</label>
                            <input type="text" id="departureDate" name="departure_date" class="form-control py-2 bg-white" placeholder="Select Date" readonly required>
                        </div>

                        <!-- Return Date -->
                        <div class="col-md-2" id="returnBox">
                            <label class="form-label fw-semibold small text-muted">Return Date</label>
                            <input type="text" id="returnDate" name="return_date" class="form-control py-2 bg-white" placeholder="Select Date" readonly>
                        </div>

                        <!-- Passengers / Class Dropdown -->
                        <div class="col-md-2 position-relative" id="passengerBox">
                            <label class="form-label fw-semibold small text-muted">Passengers / Class</label>
                            <button type="button" id="passengerDropdownBtn" class="form-control py-2 text-start bg-white text-truncate">
                                <span id="summary">1 Adult, Economy</span>
                            </button>

                            <!-- Modern Airline Passenger & Cabin Class Selector Card -->
                            <div id="passengerMenu" class="passenger-card shadow-lg p-3 bg-white rounded-4 border position-absolute" style="display: none; z-index: 1050; top: 100%; right: 0; min-width: 310px; margin-top: 8px;">
                                <!-- Header -->
                                <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                                    <div>
                                        <h6 class="fw-bold m-0 text-dark">Passengers & Class</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">Select travelers & cabin class</small>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1" style="font-size: 0.7rem;">SkyPort</span>
                                </div>

                                <!-- Adults -->
                                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;">Adults</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">12+ years</div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="passenger-count-btn" onclick="changeCount('adult', -1)" aria-label="Decrease Adults">−</button>
                                        <span id="adult" class="fw-bold px-2">1</span>
                                        <button type="button" class="passenger-count-btn" onclick="changeCount('adult', 1)" aria-label="Increase Adults">+</button>
                                    </div>
                                </div>

                                <!-- Children -->
                                <div class="d-flex align-items-center justify-content-between py-2 border-bottom mb-3">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;">Children</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">2 – 12 years</div>
                                    </div>
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

                        <!-- Search Button -->
                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-warning btn-lg px-5 fw-bold text-dark shadow-sm">
                                <i class="bi bi-search me-2"></i> Search Flights
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
</header>

<main class="container my-5">
    <!-- INFO CARDS -->
    <div class="row my-4 g-3">
        <div class="col-md-4">
            <div class="card p-3 border-0 shadow-sm rounded-3">
                <div class="d-flex align-items-center">
                    <div class="fs-2 text-primary me-3"><i class="bi bi-headset"></i></div>
                    <div>
                        <h6 class="mb-0 fw-bold">24/7 Support Available</h6>
                        <small class="text-muted">Call +1 800 123 4567</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 border-0 shadow-sm rounded-3">
                <div class="d-flex align-items-center">
                    <div class="fs-2 text-primary me-3"><i class="bi bi-globe2"></i></div>
                    <div>
                        <h6 class="mb-0 fw-bold">Domestic & International</h6>
                        <small class="text-muted">Connecting 50+ Top Destinations</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 border-0 shadow-sm rounded-3">
                <div class="d-flex align-items-center">
                    <div class="fs-2 text-primary me-3"><i class="bi bi-shield-check"></i></div>
                    <div>
                        <h6 class="mb-0 fw-bold">Instant Web Check-in</h6>
                        <small class="text-muted">Fast boarding pass generation</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Statistics & Achievements (Why Choose SkyPort?) -->
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
    <section class="my-5" aria-label="Latest Airline News">
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
            <!-- News Card 1 -->
            <div class="col-lg-4 col-md-6">
                <article class="card news-card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="position-relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=600&q=80" class="card-img-top news-img" alt="Airline Route Expansion">
                        <span class="badge bg-primary position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill shadow-sm">Routes & Expansion</span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between text-muted small mb-2">
                            <span><i class="bi bi-calendar3 me-1"></i> Today, Aug 04, 2026</span>
                            <span><i class="bi bi-clock me-1"></i> 3 min read</span>
                        </div>
                        <h3 class="h6 card-title fw-bold text-dark mb-2">India-US Direct Flight Frequencies Boosted for Winter 2026</h3>
                        <p class="card-text text-muted small flex-grow-1">Major international carriers announce additional non-stop flights connecting Delhi & Mumbai with key global hubs using new eco-friendly fleets.</p>
                        <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                            <span class="text-primary fw-semibold small">Read Full Story <i class="bi bi-arrow-right ms-1"></i></span>
                            <span class="badge bg-light text-dark"><i class="bi bi-fire text-danger me-1"></i> Trending</span>
                        </div>
                    </div>
                </article>
            </div>

            <!-- News Card 2 -->
            <div class="col-lg-4 col-md-6">
                <article class="card news-card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="position-relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=600&q=80" class="card-img-top news-img" alt="Digital Airport Checkin">
                        <span class="badge bg-success position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill shadow-sm">Tech & Check-in</span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between text-muted small mb-2">
                            <span><i class="bi bi-calendar3 me-1"></i> Aug 03, 2026</span>
                            <span><i class="bi bi-clock me-1"></i> 2 min read</span>
                        </div>
                        <h3 class="h6 card-title fw-bold text-dark mb-2">SkyPort Upgrades 30+ Airports with Instant Mobile Web Check-in</h3>
                        <p class="card-text text-muted small flex-grow-1">Passengers can now generate digital boarding passes in under 30 seconds with real-time seat selection and live baggage tracking notifications.</p>
                        <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                            <span class="text-primary fw-semibold small">Read Full Story <i class="bi bi-arrow-right ms-1"></i></span>
                            <span class="badge bg-light text-dark"><i class="bi bi-lightning-fill text-warning me-1"></i> Feature</span>
                        </div>
                    </div>
                </article>
            </div>

            <!-- News Card 3 -->
            <div class="col-lg-4 col-md-6">
                <article class="card news-card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="position-relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1508873696983-2df5057d0256?auto=format&fit=crop&w=600&q=80" class="card-img-top news-img" alt="Eco Aviation Flight">
                        <span class="badge bg-info text-dark position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill shadow-sm">Eco-Aviation</span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between text-muted small mb-2">
                            <span><i class="bi bi-calendar3 me-1"></i> Aug 02, 2026</span>
                            <span><i class="bi bi-clock me-1"></i> 4 min read</span>
                        </div>
                        <h3 class="h6 card-title fw-bold text-dark mb-2">Sustainable Aviation Fuel (SAF) Milestones Reached Across Tier-1 Cities</h3>
                        <p class="card-text text-muted small flex-grow-1">Domestic airlines commit to 15% SAF blend on premier metro routes, reducing net carbon emissions and supporting green travel initiatives.</p>
                        <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                            <span class="text-primary fw-semibold small">Read Full Story <i class="bi bi-arrow-right ms-1"></i></span>
                            <span class="badge bg-light text-dark"><i class="bi bi-leaf-fill text-success me-1"></i> Green News</span>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>
</main>

<?php include 'include/footer.php'; ?>
