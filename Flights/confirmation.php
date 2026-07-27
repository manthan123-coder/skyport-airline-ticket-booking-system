<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <title>Confirmed section</title>
</head>
<body>
<?php
session_start();

if (!isset($_SESSION['booking_id'])) {
  $_SESSION['booking_id'] = 'BK' . date('YmdHis');
}

if (!isset($_SESSION['pnr'])) {
  $_SESSION['pnr'] = strtoupper(substr(uniqid(), -6));
}

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

<div class="container">

<div class="card ticket-card">

<div class="ticket-header">

<h2>🎉 Booking Confirmed</h2>

<p>Your flight has been booked successfully.</p>

</div>

<div class="card-body p-4">

<!-- Booking Details -->

<div class="info-box">

<h5 class="section-title">Booking Information</h5>

<div class="row">

<div class="col-md-6">
<p><strong>Booking ID:</strong>
<?= $_SESSION['booking_id'] ?? 'Not Generated'; ?>
</p>

<p><strong>PNR:</strong>
<?= $_SESSION['pnr'] ?? 'Not Generated'; ?>
</p>
</div>

</div>

</div>

<!-- Flight Details -->

<div class="info-box">

<h5 class="section-title">Flight Details</h5>

<p>
<strong>Flight:</strong>
<?= $_SESSION['flight_name'] ?? 'IndiGo'; ?>
</p>

<div class="route">
<?= $_SESSION['from_city'] ?? ''; ?>
✈
<?= $_SESSION['to_city'] ?? ''; ?>
</div>

<hr>

<div class="row">

<div class="col-md-3">
<strong>Departure</strong><br>
<?= $_SESSION['departure_date'] ?? ''; ?>
</div>

<div class="col-md-3">
<strong>Return</strong><br>
<?= $_SESSION['return_date'] ?? 'N/A'; ?>
</div>

<div class="col-md-3">
<strong>Departure Time</strong><br>
<?= $_SESSION['departure_time'] ?? ''; ?>
</div>

<div class="col-md-3">
<strong>Arrival Time</strong><br>
<?= $_SESSION['arrival_time'] ?? ''; ?>
</div>

</div>

</div>

<!-- Passenger Details -->

<div class="info-box">

<h5 class="section-title">Passenger Details</h5>

<div class="row">

<div class="col-md-6">
<p><strong>Name:</strong>
<?= ($_SESSION['firstname'] ?? '') . ' ' . ($_SESSION['lastname'] ?? ''); ?>
</p>

<p><strong>Gender:</strong>
<?= $_SESSION['gender'] ?? ''; ?>
</p>

<p><strong>Nationality:</strong>
<?= $_SESSION['nationality'] ?? ''; ?>
</p>
</div>

<div class="col-md-6">

<p><strong>Email:</strong>
<?= $_SESSION['email'] ?? ''; ?>
</p>

<p><strong>Phone:</strong>
<?= $_SESSION['number'] ?? ''; ?>
</p>

<p><strong>Date of Birth:</strong>
<?= $_SESSION['dob'] ?? ''; ?>
</p>

</div>

</div>

</div>

<!-- Payment -->

<div class="info-box">

<h5 class="section-title">Payment Information</h5>

<p>
<strong>Payment Status:</strong>
<span class="status">
<?= $_SESSION['payment_status'] ?? 'Success'; ?>
</span>
</p>

<p>
<strong>Amount Paid:</strong>
₹<?= $_SESSION['price'] ?? '0'; ?>
</p>

</div>

<div class="ticket-footer">

<!-- <button onclick="window.print()"
class="btn btn-primary me-2">
🖨 View Ticket
</button> -->
<a href="mybooking.php"
  class="btn btn-primary">
  🖨 My Bookings
</a>

<a href="index.php"
class="btn btn-success">
🏠 Back To Home
</a>

</div>

</div>

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
</body>
</html>