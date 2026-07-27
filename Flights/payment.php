<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="index.css">
</head>
<body>

<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['pay_now'])) {
  foreach ($_POST as $key => $value) {
    $_SESSION[$key] = $value;
  }
}

include 'db.php';

if (isset($_POST['pay_now'])) {
  $_SESSION['booking_id'] = 'BK' . date('YmdHis');
  $_SESSION['pnr'] = strtoupper(substr(uniqid(), -6));

  $sql = "INSERT INTO bookings
    (
        booking_id,
        pnr,
        flight_name,
        from_city,
        to_city,
        departure_date,
        return_date,
        firstname,
        lastname,
        email,
        phone,
        amount,
        payment_status,
        departure_time,
        arrival_time

    )
    VALUES
    (
        '{$_SESSION['booking_id']}',
        '{$_SESSION['pnr']}',
        '{$_SESSION['flight_name']}',
        '{$_SESSION['from_city']}',
        '{$_SESSION['to_city']}',
        '{$_SESSION['departure_date']}',
        '{$_SESSION['return_date']}',
        '{$_SESSION['firstname']}',
        '{$_SESSION['lastname']}',
        '{$_SESSION['email']}',
        '{$_SESSION['number']}',
        '{$_SESSION['price']}',
        'Success',
        '{$_SESSION['departure_time']}',
        '{$_SESSION['arrival_time']}'
    )";

  if (mysqli_query($conn, $sql)) {
    $_SESSION['payment_status'] = 'Success';

    header('Location: confirmation.php');
    exit();
  }
}
$from_city = $_SESSION['from_city'] ?? '';
$to_city = $_SESSION['to_city'] ?? '';
$departure_date = $_SESSION['departure_date'] ?? '';

$flight_name = $_POST['flight_name'];
$price = $_POST['price'];
$departure_time = $_POST['departure_time'];
$arrival_time = $_POST['arrival_time'];
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



<div class="container payment-wrapper">

<div class="row">

<!-- Booking Summary -->

<div class="col-lg-4 mb-4">

<div class="card summary-card p-4">

<h4>Booking Summary</h4>

<hr>
<p>
<strong>Passenger:</strong>
<?php echo $_SESSION['firstname'] . ' ' . $_SESSION['lastname']; ?>
</p>

<p>
<strong>Flight:</strong>
<?php echo $_SESSION['flight_name']; ?>
</p>

<p><strong>From:</strong>
<?php echo $_SESSION['from_city'] ?? 'Not Found'; ?>
</p>

<p><strong>To:</strong>
<?php echo $_SESSION['to_city'] ?? 'Not Found'; ?>
</p>

<p><strong>Departure:</strong>
<?php echo $_SESSION['departure_date'] ?? 'Not Found'; ?>
</p>

<p>
<strong>Amount:</strong>
₹<?php echo $_SESSION['price']; ?>
</p>

<hr>

</div>

</div>

<!-- Payment Section -->

<div class="col-lg-8">

<div class="card payment-card p-4">

<h3 class="mb-4">Complete Payment</h3>

<ul class="nav nav-pills mb-4">

<li class="nav-item">
<button class="nav-link active"
data-bs-toggle="pill"
data-bs-target="#cardtab">
Card
</button>
</li>

<li class="nav-item">
<button class="nav-link"
data-bs-toggle="pill"
data-bs-target="#upitab">
UPI
</button>
</li>

<li class="nav-item">
<button class="nav-link"
data-bs-toggle="pill"
data-bs-target="#nettab">
Net Banking
</button>
</li>

</ul>

<div class="tab-content">

<!-- Card -->

<div class="tab-pane fade show active" id="cardtab">

<form action="payment.php" method="POST">

<input type="hidden" name="payment_method" value="Card">

<div class="mb-3">
<label>Card Holder Name</label>
<input type="text" class="form-control" required>
</div>

<div class="mb-3">
<label>Card Number</label>
<input type="text" maxlength="19" class="form-control" required>
</div>

<div class="row">

<div class="col-md-6">
<label>Expiry Date</label>
<input type="text" class="form-control" placeholder="MM/YY" required>
</div>

<div class="col-md-6">
<label>CVV</label>
<input type="password" maxlength="3" class="form-control" required>
</div>

</div>

<button type="submit" name="pay_now"class="btn btn-success w-100 mt-4 pay-btn">
Pay Now
</button>

</form>

</div>

<!-- UPI -->

<div class="tab-pane fade" id="upitab">

<form action="payment.php" method="POST">

<input type="hidden" name="payment_method" value="UPI">

<div class="mb-3">
<label>UPI ID</label>
<input type="text"
class="form-control"
placeholder="example@upi"
required>
</div>

<button class="btn btn-success w-100 mt-4 pay-btn">
Pay via UPI
</button>

</form>

</div>

<!-- Net Banking -->

<div class="tab-pane fade" id="nettab">

<form action="payment.php" method="POST">

<input type="hidden" name="payment_method" value="Net Banking">

<div class="mb-3">
<label>Select Bank</label>

<select class="form-select" required>
<option value="">Choose Bank</option>
<option>SBI</option>
<option>HDFC Bank</option>
<option>ICICI Bank</option>
<option>Axis Bank</option>
<option>Kotak Mahindra</option>
<option>Bank of Baroda</option>
</select>

</div>

<button class="btn btn-success w-100 mt-4 pay-btn">
Proceed to Bank
</button>

</form>

</div>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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