<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <title>Document</title>
</head>
<body>
    <?php
    session_start();
    include ('db.php');

    $pnr = $_POST['pnr'] ?? $_GET['pnr'] ?? '';

    if (empty($pnr)) {
      die('Invalid Request');
    }

    $sql = "SELECT * FROM bookings WHERE pnr='$pnr'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 0) {
      die('Booking Not Found');
    }

    $row = mysqli_fetch_assoc($result);

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

<div class="container py-5 mt-5">

    <!-- Header -->

    <div class="text-center mb-5">
        <h1 class="fw-bold text-primary">
            ✈ Web Check-in
        </h1>

        <p class="text-muted">
            Complete your check-in and select your preferred seat.
        </p>
    </div>

    <!-- Progress -->

    <div class="checkin-progress mb-5">

        <div class="step active">
            <div class="circle">1</div>
            <span>Passenger</span>
        </div>

        <div class="line active"></div>

        <div class="step">
            <div class="circle">2</div>
            <span>Seat</span>
        </div>

        <div class="line"></div>

        <div class="step">
            <div class="circle">3</div>
            <span>Confirm</span>
        </div>

    </div>

<div class="row">

<!-- Passenger Card -->

<div class="col-lg-7">

<div class="card shadow-lg border-0 rounded-4 mb-4">

<div class="card-header bg-primary text-white rounded-top-4">

<h4 class="mb-0">

👤 Passenger Information

</h4>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">

<label class="text-muted">

Passenger Name

</label>

<h5>

<?= $row['firstname']; ?>

<?= $row['lastname']; ?>

</h5>

</div>

<div class="col-md-6 mb-3">

<label class="text-muted">

Email

</label>

<h6>

<?= $row['email']; ?>

</h6>

</div>

<div class="col-md-6 mb-3">

<label class="text-muted">

Phone Number

</label>

<h6>

<?= $row['phone']; ?>

</h6>

</div>

<div class="col-md-6 mb-3">

<label class="text-muted">

PNR Number

</label>

<h5 class="text-primary">

<?= $row['pnr']; ?>

</h5>

</div>

<div class="col-md-6">

<label class="text-muted">

Booking ID

</label>

<h6>

<?= $row['booking_id']; ?>

</h6>

</div>



</div>

</div>

</div>

</div>

<!-- Flight Summary -->

<div class="col-lg-5">

<div class="card shadow-lg border-0 rounded-4">

<div class="card-header bg-dark text-white rounded-top-4">

<h4 class="mb-0">

🛫 Flight Summary

</h4>

</div>

<div class="card-body">

<h3 class="fw-bold text-primary">

<?= $row['flight_name']; ?>

</h3>

<hr>

<div class="flight-route">

<div>

<h6>

<?= $row['from_city']; ?>

</h6>

<small class="text-muted">

Departure

</small>

</div>

<div class="plane">

✈

</div>

<div>

<h6>

<?= $row['to_city']; ?>

</h6>

<small class="text-muted">

Arrival

</small>

</div>

</div>

<hr>

<div class="row text-center">

<div class="col-6">

<h6>

Journey Date

</h6>

<p>

<?= $row['departure_date']; ?>

</p>

</div>

<div class="col-6">
<h6>Journey Status</h6>

<span class="badge bg-warning text-dark px-3 py-2">
🟡 Ready for Web Check-in
</span>

</div>

</div>

</div>

</div>

</div>

</div>

</div>

<!-- Seat Selection -->
<!-- ================== PREMIUM SEAT SELECTION ================== -->

<div class="card shadow-lg border-0 rounded-4 mt-4 seat-card">

<div class="card-header bg-white border-0 pt-4">

<h2 class="text-center fw-bold text-primary">

✈ Select Your Seat

</h2>

<p class="text-center text-muted mb-0">

Choose your preferred seat before boarding

</p>

</div>

<div class="card-body">

<!-- Aircraft Information -->

<div class="d-flex justify-content-between align-items-center flex-wrap mb-4 aircraft-info">

<div>

<h5 class="fw-bold mb-1">

<?= $row['flight_name']; ?>

</h5>

<span class="text-muted">

Airbus A320 • Economy Class

</span>

</div>

<div class="text-end">

<h6 class="mb-1">

<?= $row['from_city']; ?>

→

<?= $row['to_city']; ?>

</h6>

<small class="text-muted">

<?= $row['departure_date']; ?>

</small>

</div>

</div>

<hr>

<!-- Legend -->

<div class="seat-legend">

<div>

<span class="legend available"></span>

Available

</div>

<div>

<span class="legend occupied"></span>

Booked

</div>

<div>

<span class="legend selected"></span>

Selected

</div>

</div>

<hr class="mt-4 mb-4">

<!-- Aircraft -->

<div class="aircraft-wrapper">

<div class="aircraft-title">

✈ AIRBUS A320

</div>

<div class="cockpit">

COCKPIT

</div>

<!-- Seat Grid yaha Part-2 me aayega -->

<div id="seatGrid">
<?php

$occupied = ['1C', '2D', '3A', '4E', '6B', '7D', '8F', '10B'];

$letters = ['A', 'B', 'C', 'D', 'E', 'F'];

?>

<div class="seat-header">

<div class="row-number"></div>

<div class="seat-letter">A</div>
<div class="seat-letter">B</div>
<div class="seat-letter">C</div>

<div class="aisle-space"></div>

<div class="seat-letter">D</div>
<div class="seat-letter">E</div>
<div class="seat-letter">F</div>

</div>

<?php

for ($r = 1; $r <= 7; $r++) {
  echo "<div class='seat-row'>";
  echo "<div class='row-number'>$r</div>";

  for ($i = 0; $i < 6; $i++) {
    $seat = $r . $letters[$i];

    if ($i == 3) {
      echo "<div class='aisle-space'></div>";
    }

    if (in_array($seat, $occupied)) {
      echo "<button
class='seat occupied'
disabled>$seat</button>";
    } else {
      echo "<button
type='button'
class='seat available'
onclick=\"selectSeat('$seat',this)\">
$seat
</button>";
    }
  }

  echo '</div>';
}

?>  
</div>

</div>

<!-- Selected Seat -->

<div class="selected-seat-box mt-4">

<div>

<strong>

Selected Seat

</strong>

<h4 id="selectedSeat">

None

</h4>

</div>

<div>

<strong>

Seat Price

</strong>

<h5 class="text-success">

Included

</h5>

</div>

</div>
<form action="boardingpass.php" method="POST" id="seatForm">

    <input type="hidden" name="pnr" value="<?= $row['pnr']; ?>">

    <input type="hidden" name="seat_no" id="seatNumber">

    <div class="text-center mt-4">

        <button type="submit" class="btn btn-primary btn-lg px-5" id="continueBtn" disabled>

            Continue to Boarding Pass →

        </button>

    </div>

</form>
</div>

</div>
<script>
  let currentSeat = null;

function selectSeat(seat, btn){

    if(currentSeat != null){

        currentSeat.classList.remove("selected");
        currentSeat.classList.add("available");

    }

    currentSeat = btn;

    btn.classList.remove("available");
    btn.classList.add("selected");

    document.getElementById("selectedSeat").innerHTML = seat;

    document.getElementById("seatNumber").value = seat;

    document.getElementById("continueBtn").disabled = false;

}
</script>
<!-- ================== END ================== -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



</body>
</html>