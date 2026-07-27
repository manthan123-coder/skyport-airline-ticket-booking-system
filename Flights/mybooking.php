<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
     <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <?php

    session_start();
    include ('db.php');

    $email = $_SESSION['email'];

    $sql = "SELECT * FROM bookings
        WHERE email='$email'
        ORDER BY booking_date DESC";

    $result = mysqli_query($conn, $sql);

    $pnr = $_GET['pnr'] ?? '';

    if ($pnr != '') {
      $sql = "SELECT * FROM bookings
            WHERE pnr LIKE '%$pnr%'
            ORDER BY booking_date DESC";
    } else {
      $sql = 'SELECT * FROM bookings
            ORDER BY booking_date DESC';
    }

    $result = mysqli_query($conn, $sql);
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
    
<div class="container my-5">

<!-- SEARCH BOX -->
<div class="search-box mb-4">
<form method="GET" class="row g-2 mb-4">
    <div class="col-md-10">
        <input type="text"
               class="form-control"
               name="pnr"
               placeholder="Enter PNR Number"
               value="<?= $_GET['pnr'] ?? '' ?>">
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary w-100">
            Search
        </button>
    </div>
</form>
</div>

<!-- FILTER BUTTONS -->
<div class="d-flex gap-2 mb-4">
    <button class="btn btn-primary">All</button>
    <button class="btn btn-outline-primary">Upcoming</button>
    <button class="btn btn-outline-primary">Completed</button>
</div>

<!-- BOOKINGS LIST -->
<div class="row">

<?php while ($row = mysqli_fetch_assoc($result)) { ?>

<div class="col-md-6 mb-4">

    <div class="booking-card shadow-sm">

        <div class="top">
            <h5>✈ <?= $row['flight_name']; ?></h5>
<span class="status <?= strtolower($row['payment_status']); ?>">
    <?= $row['payment_status']; ?>
</span>
        </div>

        <hr>

        <div class="route">
            <?= $row['from_city']; ?> ➝ <?= $row['to_city']; ?>
        </div>

        <div class="details">
            <p><b>PNR:</b> <?= $row['pnr']; ?></p>
            <p><b>Name:</b> <?= $row['firstname']; ?> <?= $row['lastname']; ?></p>
            <p><b>Date:</b> <?= $row['departure_date']; ?></p>
            <p><b>Amount:</b> ₹<?= $row['amount']; ?></p>
        </div>


    <!-- View Ticket -->
<form action="viewticket.php" method="POST" class="d-inline">
    <input type="hidden" name="pnr" value="<?= $row['pnr']; ?>">
    <button type="submit" class="btn btn-primary">
        View Ticket
    </button>
</form>
<?php if ($row['checkin_status'] == 'Checked-in') { ?>

    <form action="boardingpass.php" method="POST" class="d-inline">
        <input type="hidden" name="pnr" value="<?= $row['pnr']; ?>">
        <button type="submit" class="btn btn-info">
            <i class="fa-solid fa-id-card"></i> Boarding Pass
        </button>
    </form>

<?php } else { ?>

    <!-- Web Check-in -->
     
<form action="webcheckin.php" method="POST" class="d-inline">
    <input type="hidden" name="pnr" value="<?= $row['pnr']; ?>">

    <button type="submit" class="btn btn-success">
      <i class="fa-solid fa-plane-departure"></i> Web Check-in
    </button>
</form>
<?php } ?>

</div>

</div>

<?php } ?>



</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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

</body>
</html>