
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SkyPort— Airport Booking</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="0fc46d2a-4479-4573-a6b4-9c21ef7e10e1.css"> -->
    <link rel="stylesheet" href="index.css">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- External CSS
    <link rel="stylesheet" href="index.css"> -->

    <!-- Optional: Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">


</head>
<body>

     <!-- NAVBAR -->
     <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold" href="#">
                <span class="logo-circle me-2">✈</span>
                <span>SkyPort</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item ms-5"><a class="nav-link active" href="#">Home</a></li>
                    <li class="nav-item ms-5"><a class="nav-link" href="flight.php">Flights</a></li>
                    <li class="nav-item ms-5"><a class="nav-link" href="#">Web Check-in</a></li>
                    <li class="nav-item ms-5"><a class="nav-link" href="#">My Bookings</a></li>
                    <li class="nav-item ms-5"><a class="nav-link" href="#">Offers</a></li>
                    <li class="nav-item ms-5"><a class="nav-link" href="#">Contact Us</a></li>
                </ul>

                <div class="d-flex align-items-center">
                    <a class="btn btn-outline-primary me-2" href="#"><i class="bi bi-person"></i> Sign up</a>
                    <a class="btn btn-primary" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- /NAVBAR -->


    <!-- HERO -->
    <header class="hero-section">
        <div class="hero-bg"></div>
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-6 text-white">
                    <h1 class="display-5 fw-bold">Book Your Dream Flights Now!</h1>
                    <p class="lead mb-4">Fast, reliable and easy flight booking with real-time schedules and great
                        deals.</p>
                    <a href="#search" class="btn btn-outline-light btn-lg me-2">Book Now</a>
                    <a href="#deals" class="btn btn-light btn-lg">See Deals</a>
                    <!-- <img src="https://images.unsplash.com/photo-1504196606672-aef5c9cefc92?auto=format&fit=crop&w=1500&q=80" alt=""> -->
                </div>


            </div>
        </div>
<br>
        <!-- SEARCH CARD (Tabbed) -->
        <div id="search" class="search-card card shadow-lg " >
            <div class="card-body p-3 p-md-4">
                <ul class="nav nav-tabs mb-1" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="flights-tab" data-bs-toggle="tab" data-bs-target="#flights"
                            type="button" role="">Search Flights</button>
                    </li>
             
                </ul>

                <div class="tab-content overflow: visible !important;">
                    <!-- FLIGHTS TAB -->
                    <div class="tab-pane fade show active" id="flights" role="tabpanel">
                       <form action="flight.php" method="POST" class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label small">From</label>
                               
                                <select name="from_city" class="form-select"  required>
                                            <option value="">Select City</option>

                                            <option value="Agra">Agra</option>
                                            <option value="Ahmedabad">Ahmedabad</option>
                                            <option value="Amritsar">Amritsar</option>
                                            <option value="Aurangabad">Aurangabad</option>
                                            <option value="Bangalore">Bangalore</option>
                                            <option value="Bhavnagar">Bhavnagar</option>
                                            <option value="Bhopal">Bhopal</option>
                                            <option value="Chandigarh">Chandigarh</option>
                                            <option value="Chennai">Chennai</option>
                                            <option value="Coimbatore">Coimbatore</option>
                                            <option value="Dehradun">Dehradun</option>
                                            <option value="Delhi">Delhi</option>
                                            <option value="Goa">Goa</option>
                                            <option value="Guwahati">Guwahati</option>
                                            <option value="Hyderabad">Hyderabad</option>
                                            <option value="Indore">Indore</option>
                                            <option value="Jaipur">Jaipur</option>
                                            <option value="Kochi">Kochi</option>
                                            <option value="Kolkata">Kolkata</option>
                                            <option value="Lucknow">Lucknow</option>
                                            <option value="Mumbai">Mumbai</option>
                                            <option value="Nagpur">Nagpur</option>
                                            <option value="Patna">Patna</option>
                                            <option value="Pune">Pune</option>
                                            <option value="Rajkot">Rajkot</option>
                                            <option value="Surat">Surat</option>
                                            <option value="Vadodara">Vadodara</option>
                                            <option value="Varanasi">Varanasi</option>
                                            <option value="Visakhapatnam">Visakhapatnam</option>
                                           </div>
                                          </div>
                                          </select>                                      
                                          </div>

                            <div class="col-md-2">
                               <label class="form-label small">To</label>
                                <select name="to_city" class="form-select" required>
                                <option value="">Select City</option>

        <option value="Agra">Agra</option>
        <option value="Ahmedabad">Ahmedabad</option>
        <option value="Amritsar">Amritsar</option>
        <option value="Aurangabad">Aurangabad</option>
        <option value="Bangalore">Bangalore</option>
        <option value="Bhavnagar">Bhavnagar</option>
        <option value="Bhopal">Bhopal</option>
        <option value="Chandigarh">Chandigarh</option>
        <option value="Chennai">Chennai</option>
        <option value="Coimbatore">Coimbatore</option>
        <option value="Dehradun">Dehradun</option>
        <option value="Delhi">Delhi</option>
        <option value="Goa">Goa</option>
        <option value="Guwahati">Guwahati</option>
        <option value="Hyderabad">Hyderabad</option>
        <option value="Indore">Indore</option>
        <option value="Jaipur">Jaipur</option>
        <option value="Kochi">Kochi</option>
        <option value="Kolkata">Kolkata</option>
        <option value="Lucknow">Lucknow</option>
        <option value="Mumbai">Mumbai</option>
        <option value="Nagpur">Nagpur</option>
        <option value="Patna">Patna</option>
        <option value="Pune">Pune</option>
        <option value="Rajkot">Rajkot</option>
        <option value="Surat">Surat</option>
        <option value="Vadodara">Vadodara</option>
        <option value="Varanasi">Varanasi</option>
        <option value="Visakhapatnam">Visakhapatnam</option>

                                    </div>
                                </div>
    </select>
                            </div>

<div class="col-md-2">

<label class="form-label small">Departure</label>
<input 
type="text"
id="departureDate"
name="departure_date"
class="form-control py-2"
placeholder="Departure"
readonly> 

</div>


<div class="col-md-2" id="returnBox">
<label class="form-label small">Return</label>
    <input
    type="text"
    id="returnDate"
    name="return_date"
    class="form-control py-2"
    placeholder="Return"
    readonly>

</div>

              <div class="col-md-2 position-relative" id="passengerBox">

    <label class="form-label small">Passengers / Class</label>

    <button type="button"
        id="passengerDropdownBtn"
        class="form-control text-start">

        <span id="summary">1 Adult, Economy</span>

    </button>

    <div id="passengerMenu" class="passenger-menu">

        <!-- Adult -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <span>Adults</span>

            <div class="d-flex align-items-center gap-2">

                <button type="button" onclick="changeCount('adult',-1)">−</button>

                <span id="adult">1</span>

                <button type="button" onclick="changeCount('adult',1)">+</button>

            </div>

        </div>

        <!-- Child -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <span>Children</span>

            <div class="d-flex align-items-center gap-2">

                <button type="button" onclick="changeCount('child',-1)">−</button>

                <span id="child">0</span>

                <button type="button" onclick="changeCount('child',1)">+</button>

            </div>

        </div>

        <div class="mb-3">

            <label class="small mb-1">Travel Class</label>

            <select id="travelClass" class="form-select">

                <option>Economy</option>
                <option>Business</option>
                <option>First Class</option>

            </select>

        </div>

        <button id="donePassenger"
            class="btn btn-primary w-100">

            Done

        </button>

    </div>

    <input type="hidden" name="passenger_class" id="passenger_class" value="1 Adult, Economy">

    <input type="hidden" name="passenger_class" id="passenger_class">
</div>
<div class="col-md-2 my-2" id="searchBox">
    <button type="submit" class="btn btn-primary btn-lg- me-2">
        Search Flights
    </button>
     <!-- <a href="#search" class="btn btn-outline-light btn-lg me-2">Book Now</a> -->
</div>
</div>




<div class="col-md-3 d-flex align-items-center mt-4">

    <div class="form-check me-3">
        <input 
        class="form-check-input"
        type="radio"
        name="trip_type"
        value="oneway"
        id="oneway">

        <label class="form-check-label" for="oneway">
            One Way
        </label>
    </div>


    <div class="form-check">
        <input 
        class="form-check-input"
        type="radio"
        name="trip_type"
        value="roundtrip"
        id="roundtrip"
        checked>

        <label class="form-check-label" for="roundtrip">
            Round Trip
        </label>
    </div>

</div>


                          
                     
                        </form>
                    </div>
                    <!-- /FLIGHTS TAB -->

                    <!-- HOTEL TAB (simple placeholder) -->
                  
            <!-- Rooms -->
          
    </header>
    <!-- /HERO -->

    <main class="mt-5 pt-5">
        <div class="container">

            <!-- INFO CARDS -->
            <div class="row my-5 g-3">
                <div class="col-md-4">
                    <div class="info-card p-3 shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle me-3"><i class="bi bi-headset"></i></div>
                            <div>
                                <h6 class="mb-0">We are Now Available</h6>
                                <small class="text-muted">Call +1 555 666 888</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-card p-3 shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle me-3"><i class="bi bi-globe2"></i></div>
                            <div>
                                <h6 class="mb-0">International Flight</h6>
                                <small class="text-muted">Worldwide routes</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-card p-3 shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle me-3"><i class="bi bi-arrow-counterclockwise"></i></div>
                            <div>
                                <h6 class="mb-0">Check Refund</h6>
                                <small class="text-muted">Easy refund policy</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /INFO CARDS -->

            <!-- LATEST FLIGHT DEALS (Carousel + Grid fallback) -->
                    <br><br>



                    <!-- =================achivment section -->


                    <div class="achievement-card">

<div class="card-header">
  <h1>Achivments</h1>
</div>
<hr>
<div class="stats">
  <div class="stat-box">
    <h2>250,000+</h2>
    <span>Flights Managed</span>
  </div>
  <div class="stat-box">
    <h2>32M+</h2>
    <span>Passengers Served</span>
  </div>
  <div class="stat-box">
    <h2>94%</h2>
    <span>On-Time Performance</span>
  </div>
  <div class="stat-box">
    <h2>98.5%</h2>
    <span>Baggage Accuracy</span>
  </div>
  <div class="stat-box">
    <h2>25+</h2>
    <span>Airline Partners</span>
  </div>
  <div class="stat-box">
    <h2>0</h2>
    <span>Major Safety Incidents</span>
  </div>
</div>

               
            <!-- /DEALS -->

            <!-- FOOTER CTA -->
            <section class="pt-4 pb-5">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="fw-bold">Need help? Our support team is available 24/7</h5>
                        <p class="text-muted mb-0">Call +1 555 666 888 or email support@flynow.com</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a class="btn btn-outline-primary btn-lg" href="#">Contact us</a>
                    </div>
                </div>
            </section>

        </div>
    </main>
    <!-- ==============================news -->

<br>

<h2 align="center"><span style="color: black" >Our Latest News</span></h2>
<br> <hr>
    <div class="row row-gap-4">
    <div class="col-xxl-6 col-xl-4 col-lg-12 col-md-6 col-sm-5">
      <div class="blog-box bg-skyblue light-shadow p-24 br-20">
        <div class="row align-items-center row-gap-2">
          <div class="col-xxl-6 col-xl-12 col-lg-6">
            <div class="image-box">
              <a href="blog-detail.html"><img src="images\img1.jpg"alt=""></a>
            </div>
          </div>
          <div class="col-xxl-6 col-xl-12 col-lg-6">
            <div class="content-box">
              <div class="d-flex gap-16 mb-24">
                <div class="d-flex align-items-center gap-8">
                  <img src="assets/media/icons/user-bk.png" alt="">
                  <p class=""<span style="color: blue">Malisa John</span></p>
                </div>
                <div class="vr-line"></div>
                <div class="d-flex align-items-center gap-8">
                  <img src="assets/media/icons/calender.png" alt="">
                  <p class=""><span style="color: Black">08 Aug, 2023</span></p>
                </div>
              </div>
              <h5 class="lightest-black mb-8"><a href="blog-detail.html"><span style="color: Black">Passenger Experience Enhancement.</span></a></h5>
              <p class="dark-gray mb-24"><span style="color:rgb(5, 109, 150);">Airports continue to enhance passenger experiences with faster check-in processes, upgraded security screening technologies, and improved terminal facilities.</span></p>
              <a href="blog-detail.html" class="cus-btn small-pad">Read More</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-6 col-xl-4 col-lg-12 col-md-6 col-sm-6">
      <div class="blog-box bg-skyblue light-shadow p-24 br-20">
        <div class="row align-items-center row-gap-3">
          <div class="col-xxl-6 col-xl-12 col-lg-6">
            <div class="image-box">
              <a href="blog-detail.html"><img src="images\maps.jpg" alt=""></a>
            </div>
          </div>
          <div class="col-xxl-6 col-xl-12 col-lg-6">
            <div class="content-box">
              <div class="d-flex gap-16 mb-24">
                <div class="d-flex align-items-center gap-8">
                  <img src="assets/media/icons/user-bk.png" alt="">
                  <p class=""><span style="color: blue">Malisa John</span></p>
                </div>
                <div class="vr-line"></div>
                <div class="d-flex align-items-center gap-8">
                  <img src="assets/media/icons/calender.png" alt="">
                  <p class=""><span style="color: Black">08 Aug, 2023</span></p>
                </div>
              </div>
              <h5 class="lightest-black mb-8"><a href="blog-detail.html"><span style="color: Black">Airline & Route Developments.</span></a></h5>
              <p class="dark-gray mb-24"><span style="color:rgb(5, 109, 150);">Airports are welcoming new airline partnerships and expanding flight routes, offering travelers greater connectivity and more destination choices.</span></p>
              <a href="blog-detail.html" class="cus-btn small-pad">Read More</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-6 col-xl-4 col-lg-12 col-md-6 col-sm-6">
      <div class="blog-box bg-skyblue light-shadow p-24 br-20">
        <div class="row align-items-center row-gap-3">
          <div class="col-xxl-6 col-xl-12 col-lg-6">
            <div class="image-box">
              <a href="blog-detail.html"><img src="images/security.jpg" alt=""></a>
            </div>
          </div>
          <div class="col-xxl-6 col-xl-12 col-lg-6">
            <div class="content-box">
              <div class="d-flex gap-16 mb-24">
                <div class="d-flex align-items-center gap-8">
                  <img src="assets/media/icons/user-bk.png" alt="">
                  <p class=""><span style="color: blue;">"Malisa John</span></p>
                </div>
                <div class="vr-line"></div>
                <div class="d-flex align-items-center gap-8">
                  <img src="assets/media/icons/calender.png" alt="">
                  <p class=><span style="color: black;">,08 Aug, 2023</span></p>
                </div>
              </div>
              <h5 class="lightest-black mb-8"><a href="blog-detail.html"><span style="color: Black">Advanced Security & Safety Measures.</span></a></h5>
              <p class="dark-gray mb-24"><span style="color:rgb(5, 109, 150);">airports are implementing advanced surveillance systems, AI-powered security checks, and enhanced emergency response protocols.</sapn></p>
              <a href="blog-detail.html" class="cus-btn small-pad">Read More</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xxl-6 col-xl-4 col-lg-12 col-md-6 col-sm-6 d-xxl-block d-xl-none">
      <div class="blog-box bg-skyblue light-shadow p-24 br-20">
        <div class="row align-items-center row-gap-3">
          <div class="col-xxl-6 col-xl-12 col-lg-6">
            <div class="image-box">
              <a href="blog-detail.html"><img src="images\adventure.jpg" alt=""></a>
            </div>
          </div>
          <div class="col-xxl-6 col-xl-12 col-lg-6">
            <div class="content-box">
              <div class="d-flex gap-16 mb-24">
                <div class="d-flex align-items-center gap-8">
                  <img src="assets/media/icons/user-bk.png" alt="">
                  <p class=><span style="color: blue;">Malisa John</span></p>
                </div>
                <div class="vr-line"></div>
                <div class="d-flex align-items-center gap-8">
                  <img src="assets/media/icons/calender.png" alt="">
                  <p class=><span style="color: black;">,08 Aug, 2023</span></p>
                  <p class="h6 dark-gray"></p>
                </div>
              </div>
              <h5 class="lightest-black mb-8"><a href="blog-detail.html"><span style="color: Black">Wings of Adventure: Exploring the World by
              Air.</span></a></h5>
              <p class="dark-gray mb-24"><span style="color:rgb(5, 109, 150);">Lorem ipsum dolor sit amet consectetur. Feugiat sit eleifend tortor.</p></span>
              <a href="blog-detail.html" class="cus-btn small-pad">Read More</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<br><br><br>
<!-- ==================footer -->

<footer class="footer">
  <div class="container">
    <div class="footer-grid">

      <!-- Logo & Subscribe -->
      <div class="footer-col">
        <div class="footer-logo">
        <span class="logo-circle me-2">✈</span>
          <img src="logo.svg" alt="">
          <span>Sky Port</span>
          
          
        </div>
        <p class="footer-text">
          Lorem ipsum dolor sit amet consectetur. Aliquet vulputate augue penatibus in libero et id aliquam.
          In ridiculus pretium est velit euismod.
        </p>

        <h6 class="footer-title">Subscribe to our special offers</h6>
        <form class="subscribe-box">
          <input type="email" placeholder="Email address">
          <button type="submit">Subscribe</button>
        </form>
      </div>

      <!-- Booking -->
      <div class="footer-col">
        <h5 class="footer-heading">Booking</h5>
        <ul>
          <li><a href="#">Book Flights</a></li>
          <li><a href="#">Travel Services</a></li>
          <li><a href="#">Transportation</a></li>
          <li><a href="#">Planning Your Trip</a></li>
        </ul>
      </div>

      <!-- Useful Links -->
      <div class="footer-col">
        <h5 class="footer-heading">Useful Links</h5>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="#">Blogs</a></li>
          <li><a href="#">About</a></li>
          <li><a href="#">Contact Us</a></li>
        </ul>
      </div>

      <!-- Manage -->
      <div class="footer-col">
        <h5 class="footer-heading">Manage</h5>
        <ul>
          <li><a href="#">Check-in</a></li>
          <li><a href="#">Manage Your Booking</a></li>
          <li><a href="#">Chauffeur Drive</a></li>
          <li><a href="#">Flight Status</a></li>
        </ul>
      </div>

      <!-- Contact -->
      <div class="footer-col">
        <h5 class="footer-heading">Contact Us</h5>
        <ul class="contact-list">
          <li>📍 123 Main Street, Anytown, USA.</li>
          <li>📞 <a href="tel:+1234567890">+1 234 567 890</a></li>
          <li>✉️ <a href="mailto:email@example.com">email@example.com</a></li>
        </ul>

        <h6 class="footer-title">Follow Us!</h6>
        <div class="social-icons">
          <a href="#">in</a>
          <a href="#">f</a>
          <a href="#">ig</a>
          <a href="#">x</a>
        </div>
      </div>

    </div>

    <div class="footer-bottom">
      ©2025 FlyNow All Rights Reserved.
    </div>
  </div>
</footer>



    <!-- Bootstrap JS -->

     
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Carousel controls linking to custom prev/next buttons -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="index.js"></script>
</body>
</html>
