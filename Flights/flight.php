<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Flights</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    
</head>
<body>
<?php

session_start();
include 'db.php';

// Search Data
$from_city = $_POST['from_city'] ?? '';
$to_city = $_POST['to_city'] ?? '';
$departure_date = $_POST['departure_date'] ?? '';

// Session me save
$_SESSION['from_city'] = $from_city;
$_SESSION['to_city'] = $to_city;
$_SESSION['departure_date'] = $departure_date;

// Flight Search
$sql = "SELECT * FROM flights
WHERE departure_city='$from_city'
AND arrival_city='$to_city'
ORDER BY departure_time ASC";

$result = mysqli_query($conn, $sql);

// ============dynamic


?>


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
                      <li class="nav-item ms-5"><a class="nav-link active" href="index.php">Home</a></li>
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

  <!-- Flight Card 1 - Indi Go -->

  <!-- <h2 align="center">Flights</h2> -->
<?php
if(mysqli_num_rows($result) > 0)
{
    while($flight = mysqli_fetch_assoc($result))
    {
        $collapseId = "flightDetail".$flight['id'];
?>
  <div class="container">
    <div class="flight-card">
      <div class="row align-items-center">
      <div class="col-12 col-md-3 d-flex align-items-center gap-3">

<?php
$logos = [
    "IndiGo"    => "logo\indigo.png",
    "Air India" => "logo\airindia.webp",
    "SpiceJet"  => "logo\Spicejet.png",
    "Akasa Air" => "logo\akasa.webp",
    "AirAsia"   => "airasia.png",
    "Air India Express" => "logo\airindiaexpress.png"
];

$logo = $logos[$flight['airline_name']] ?? "default.png";
?>

<div class="airline-logo-1">
    <img src="<?= $logo ?>" alt="<?= $flight['airline_name']; ?>">
</div>
<div>

                <div class="airline-name"><?= $flight['airline_name']; ?></div>
                <div class="sub-text"><?= $flight['aircraft']; ?></div>
            </div>
          </div>
          
          <div class="col-6 col-6 col-md-2 text-center">
           <div class="time">
<?= date('H:i', strtotime($flight['departure_time'])); ?>
</div>
      <div class="airport"><?= $flight['departure_city']; ?></div>
          </div>
          <div class="col-12 col-md-3 text-center">
      <div class="sub-text mb-1"><?= $flight['duration']; ?></div>
            <div class="route-line"></div>
      <div class="sub-text mt-1"><?= $flight['stops']; ?></div>
          </div>
          <div class="col-6 col-md-2 text-center">
         <div class="time">
<?= date('H:i', strtotime($flight['arrival_time'])); ?>
</div>
            <div class="airport"><?= $flight['arrival_city']; ?></div>
          </div>
          <div class="col-6 col-md-2 text-end">
          <div class="price">
₹<?= number_format($flight['price']); ?>
</div>
            <div class="sub-text mb-2">Price</div>
<form action="detail.php" method="POST">
    <input type="hidden" name="flight_name" value="<?= $flight['flight_name']; ?>">
    <input type="hidden" name="price" value="<?= $flight['price']; ?>">
    <input type="hidden" name="departure_time" value="<?= $flight['departure_time']; ?>">
    <input type="hidden" name="arrival_time" value="<?= $flight['arrival_time']; ?>">

    <button type="submit" class="btn btn-primary btn-book">
        Book Now
    </button>
</form>
          </div>
      </div>
      <div class="divider"></div>
      <div class="d-flex justify-content-between align-items-center">
     <?= date("d F, Y", strtotime($_SESSION['departure_date'])); ?>

<a class="flight-detail-toggle"
   data-bs-toggle="collapse"
   data-bs-target="#<?= $collapseId ?>"
   role="button">
   Flight Detail ▼
</a>
</div>
</div>

<!-- THIS IS REQUIRED -->
<div id="<?= $collapseId ?>" class="collapse mt-3">

    <div class="flight-ticket-card">

        <!-- YOUR EXISTING CONTENT -->

        <div class="left-box">
           <?= date("d F, Y", strtotime($flight['flight_date'])); ?>

            <div class="timeline">

<p>
<span><?= date("l, d F", strtotime($_SESSION['departure_date'])); ?></span>
-
<?= date("H:i", strtotime($flight['departure_time'])); ?>
</p>

<small><?= $flight['duration']; ?></small>

<p>
<?= date("l, M d", strtotime($flight['flight_date'])); ?>
-
<?= date("H:i", strtotime($flight['arrival_time'])); ?>
</p>

</div>
        </div>

        <div class="right-box">
            <div class="airline-header">

                <div class="logo-1">  <h6 align="center" style="margin: 0; font-size: 10px; font-weight: bold; color: white; line-height: 1;"></h6></div>

                <div class="info">
                    <h3>Operated by <?= $flight['airline_name']; ?></h3>
                    Economy |
                    Flight <?= $flight['flight_name']; ?> |
                    Aircraft <?= $flight['aircraft']; ?>    
                    <p>Adult(s): 25KG luggage free</p>
                </div>

            </div>
        </div>

    </div>

</div>



</div>
    </div>

<?php
    }
}
else
{
?>
<div class="alert alert-warning text-center mt-5">
    <h5>No Flights Available</h5>
</div>
<?php
}
?>

  <!-- ============ -->

  
  

 

</div>
    </div>


  
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
