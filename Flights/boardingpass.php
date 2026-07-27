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

    <?php

    session_start();
    include ('db.php');

    $pnr = $_POST['pnr'] ?? '';
    $seat = $_POST['seat_number'] ?? '';

    if (empty($pnr)) {
        die('Invalid Request');
    }

    $sql = "SELECT * FROM bookings WHERE pnr='$pnr'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 0) {
        die('Booking Not Found');
    }

    $row = mysqli_fetch_assoc($result);

    // Seat save karo
    mysqli_query($conn, "
    UPDATE bookings
    SET seat_no='$seat',
        checkin_status='Checked-in'
    WHERE pnr='$pnr'
    ");

    // Fresh data
    $result = mysqli_query($conn, "SELECT * FROM bookings WHERE pnr='$pnr'");
    $row = mysqli_fetch_assoc($result);

    $qrData =
        "SkyPort Airlines\n"
        . 'Passenger: ' . $row['firstname'] . ' ' . $row['lastname'] . "\n"
        . 'Flight: ' . $row['flight_name'] . "\n"
        . 'PNR: ' . $row['pnr'] . "\n"
        . 'From: ' . $row['from_city'] . "\n"
        . 'To: ' . $row['to_city'] . "\n"
        . 'Seat: ' . $row['seat_no'] . "\n"
        . 'Status: Checked-in';
    ?>

<div class="boarding-pass">


<div class="boarding-header">

<div>
<h1 class="airline-name">
✈ SkyPort Airlines
</h1>

<p>BOARDING PASS</p>

</div>


<div>
<h2 class="flight-name">
<?= $row['flight_name']; ?>
</h2>

<p>
<?= $row['booking_id']; ?>
</p>

</div>

</div>



<div class="passenger-section">


<div class="left-details">


<div class="passenger-box">

<h6>Passenger</h6>

<h3>
<?= $row['firstname'] . ' ' . $row['lastname']; ?>
</h3>

</div>



<div class="route-box">


<div>

<div class="flight-route">


<div class="airport">

<span>FROM</span>

<h2>
<?= $row['from_city']; ?>
</h2>

<p>
Rajkot Airport
</p>

</div>



<div class="route-line">

<div class="line"></div>

<div class="plane">
✈
</div>

<div class="line"></div>

</div>



<div class="airport text-end">

<span>TO</span>

<h2>
<?= $row['to_city']; ?>
</h2>

<p>
Destination Airport
</p>

</div>



</div>

</div>


</div>




<div class="pnr-box">

<h6>PNR</h6>

<h3>
<?= $row['pnr']; ?>
</h3>


<p>
Booking ID : <?= $row['booking_id']; ?>
</p>


</div>


</div>


<div class="info-right">


<div class="detail-box">

<h6>Seat</h6>

<h3>
<?= $row['seat_no']; ?> 
</h3>

</div>



<div class="detail-box">

<h6>Gate</h6>

<h3>
<?= $gate ?? 'A12'; ?>
</h3>

</div>



<div class="detail-box">

<h6>Departure</h6>

<h3>
<?= $row['departure_date']; ?>
</h3>

</div>



<div class="detail-box">

<h6>Arrival</h6>

<h3>
<?= $row['return_date']; ?>
</h3>

</div>



</div>

</div>

<div class="ticket-divider">
    <span></span>
</div>


<div class="boarding-bottom">


<div class="qr-section">
<h5>Boarding Pass</h5>

<h2><?= $row['pnr']; ?></h2>
<h6>SCAN TO BOARD</h6>
<div class="qr-card">
<img 
src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode($qrData); ?>"
alt="QR Code">

<div class="qr-text">
    <small>Scan to verify your boarding pass</small>
</div>
</div>
</div>


<div class="boarding-info">


<div class="logo-box">

<img src="airlogo.jpg">

<h4>SkyPort Airlines</h4>

</div>


<p>
Please carry your valid ID proof at airport.
</p>


<span class="checked">
✓ Checked-in
</span>


</div>


</div>


<div class="bottom-section">


<!-- <div>

<h6>Check-in Status</h6>

<span class="status">
Checked-in
</span>


</div>
 -->
<div class="text-center mt-4 text-muted">

<strong>Thank you for choosing SkyPort Airlines.</strong><br>

We wish you a pleasant and safe journey.

</div>

<div class="alert alert-success text-center mt-4">

<i class="fa-solid fa-circle-check"></i>

<strong>Check-in Successful!</strong>

Have a pleasant journey with SkyPort Airlines.

</div>



</div>
<div class="boarding-buttons">

    <button class="btn btn-print" onclick="window.print()">
        <i class="fa-solid fa-print"></i> Print Boarding Pass
    </button>

    <button class="btn btn-download" onclick="window.print()">
        <i class="fa-solid fa-download"></i> Download PDF
    </button>

</div>

<div class="text-center mt-3">

    <a href="mybooking.php" class="btn btn-booking">
        <i class="fa-solid fa-ticket"></i> Return To My Bookings!
    </a>

</div>

</div>
</body>
</html>