<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Passenger Details</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="index.css">
</head>
<body>
<?php

session_start();
$from_city = $_SESSION['from_city'] ?? '';
$to_city = $_SESSION['to_city'] ?? '';
$departure_date = $_SESSION['departure_date'] ?? '';
$return_date = $_SESSION['return_date'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $_SESSION['flight_name'] = $_POST['flight_name'];
  $_SESSION['price'] = $_POST['price'];
  $_SESSION['departure_time'] = $_POST['departure_time'];
  $_SESSION['arrival_time'] = $_POST['arrival_time'];
}

$flight_name = $_SESSION['flight_name'] ?? '';
$price = $_SESSION['price'] ?? '';
$departure_time = $_SESSION['departure_time'] ?? '';
$arrival_time = $_SESSION['arrival_time'] ?? '';

?>

<div class="airline-booking-page">
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


   <div class="airline-step-container">
    <div class="airline-steps">

        <div class="airline-step">
            <div class="airline-circle airline-active">✓</div>
            <span>Your Selection</span>
        </div>

        <div class="airline-step">
            <div class="airline-circle airline-active">2</div>
            <span>Your Details</span>
        </div>

        <div class="airline-step">
            <div class="airline-circle airline-inactive">3</div>
            <span>Final Step</span>
        </div>

    </div>
</div>
<div class="container">

  <div class="airline-detail-card">

        <h2>Enter Your Details</h2>

        <?php

        ?>

        <form action="payment.php" method="POST" novalidate>

            <div class="row g-3">

                <div class="col-md-3">
                    <select class="form-select" name="gender" required>
                        <option value="">Gender</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>

                <div class="col-md-5">
                    <input type="text" class="form-control"
                    name="firstname" placeholder="First Name" required>
                </div>

                <div class="col-md-4">
                    <input type="text" class="form-control"
                    name="lastname" placeholder="Last Name" required>
                </div>

                <div class="col-md-6">
                    <input type="email" class="form-control"
                    name="email" placeholder="Email Address" required>
                </div>

                <div class="col-md-6">
                    <select class="form-select" name="nationality" required>
                        <option value="">Nationality</option>
                   <option>Afghan</option>
<option>Albanian</option>
<option>Algerian</option>
<option>American</option>
<option>Andorran</option>
<option>Angolan</option>
<option>Argentine</option>
<option>Armenian</option>
<option>Australian</option>
<option>Austrian</option>
<option>Azerbaijani</option>
<option>Bahamian</option>
<option>Bahraini</option>
<option>Bangladeshi</option>
<option>Barbadian</option>
<option>Belarusian</option>
<option>Belgian</option>
<option>Belizean</option>
<option>Beninese</option>
<option>Bhutanese</option>
<option>Bolivian</option>
<option>Bosnian</option>
<option>Botswanan</option>
<option>Brazilian</option>
<option>British</option>
<option>Bruneian</option>
<option>Bulgarian</option>
<option>Burkinabe</option>
<option>Burmese</option>
<option>Burundian</option>
<option>Cambodian</option>
<option>Cameroonian</option>
<option>Canadian</option>
<option>Chadian</option>
<option>Chilean</option>
<option>Chinese</option>
<option>Colombian</option>
<option>Congolese</option>
<option>Costa Rican</option>
<option>Croatian</option>
<option>Cuban</option>
<option>Cypriot</option>
<option>Czech</option>
<option>Danish</option>
<option>Djiboutian</option>
<option>Dominican</option>
<option>Dutch</option>
<option>East Timorese</option>
<option>Ecuadorian</option>
<option>Egyptian</option>
<option>Emirati</option>
<option>English</option>
<option>Equatorial Guinean</option>
<option>Eritrean</option>
<option>Estonian</option>
<option>Ethiopian</option>
<option>Fijian</option>
<option>Filipino</option>
<option>Finnish</option>
<option>French</option>
<option>Gabonese</option>
<option>Gambian</option>
<option>Georgian</option>
<option>German</option>
<option>Ghanaian</option>
<option>Greek</option>
<option>Guatemalan</option>
<option>Guinean</option>
<option>Guyanese</option>
<option>Haitian</option>
<option>Honduran</option>
<option>Hungarian</option>
<option>Icelandic</option>
<option>Indian</option>
<option>Indonesian</option>
<option>Iranian</option>
<option>Iraqi</option>
<option>Irish</option>
<option>Israeli</option>
<option>Italian</option>
<option>Jamaican</option>
<option>Japanese</option>
<option>Jordanian</option>
<option>Kazakh</option>
<option>Kenyan</option>
<option>Kuwaiti</option>
<option>Kyrgyz</option>
<option>Laotian</option>
<option>Latvian</option>
<option>Lebanese</option>
<option>Liberian</option>
<option>Libyan</option>
<option>Lithuanian</option>
<option>Luxembourger</option>
<option>Malagasy</option>
<option>Malawian</option>
<option>Malaysian</option>
<option>Maldivian</option>
<option>Malian</option>
<option>Maltese</option>
<option>Mauritanian</option>
<option>Mauritian</option>
<option>Mexican</option>
<option>Moldovan</option>
<option>Monacan</option>
<option>Mongolian</option>
<option>Montenegrin</option>
<option>Moroccan</option>
<option>Mozambican</option>
<option>Namibian</option>
<option>Nepali</option>
<option>New Zealander</option>
<option>Nicaraguan</option>
<option>Nigerian</option>
<option>North Korean</option>
<option>Norwegian</option>
<option>Omani</option>
<option>Pakistani</option>
<option>Palestinian</option>
<option>Panamanian</option>
<option>Papua New Guinean</option>
<option>Paraguayan</option>
<option>Peruvian</option>
<option>Polish</option>
<option>Portuguese</option>
<option>Qatari</option>
<option>Romanian</option>
<option>Russian</option>
<option>Rwandan</option>
<option>Saudi Arabian</option>
<option>Scottish</option>
<option>Senegalese</option>
<option>Serbian</option>
<option>Singaporean</option>
<option>Slovak</option>
<option>Slovenian</option>
<option>Somali</option>
<option>South African</option>
<option>South Korean</option>
<option>Spanish</option>
<option>Sri Lankan</option>
<option>Sudanese</option>
<option>Swedish</option>
<option>Swiss</option>
<option>Syrian</option>
<option>Taiwanese</option>
<option>Tajik</option>
<option>Tanzanian</option>
<option>Thai</option>
<option>Togolese</option>
<option>Tunisian</option>
<option>Turkish</option>
<option>Turkmen</option>
<option>Ugandan</option>
<option>Ukrainian</option>
<option>Uruguayan</option>
<option>Uzbek</option>
<option>Venezuelan</option>
<option>Vietnamese</option>
<option>Welsh</option>
<option>Yemeni</option>
<option>Zambian</option>
<option>Zimbabwean</option>

                    </select>
                </div>

                <div class="col-md-6">
                    <input type="text" class="form-control"
                    name="number" placeholder="Your Number" required>
                </div>
                <div class="col-md-6">
                    <input type="text"
                    class="form-control"
                    name="dob"
                    id="dob"
                    placeholder="Date of Birth"
                    onfocus="this.type='date'; this.showPicker();"
                    onblur="if(!this.value) this.type='text';"
                    required>
</div>  

                <div class="col-md-6">
                    <input type="text" class="form-control"
                   name="postal_code" placeholder="Postal Code" required>
                </div>

                <div class="col-md-6">
                    <input type="text" class="form-control"
                    name="flight_no" placeholder="Flight No" required>
                </div>
            </div>

        
            
            <!-- <div class="btn-1">
          <a href="detail.php"><button class="btn btn-primary btn-next" >Next</button></a> </div> -->
    



    </div>

        <div class="extra">
        <div class="extras-card">
    <h2>Extras</h2>

    <div class="mb-3">
        <select class="form-select" name="meal_type">
            <option selected disabled>Select Meal Type</option>
            <option>Fast Food</option>
            <option>Vegetarian</option>
            <option>Non-Vegetarian Meal</option>
        </select>
    </div>

    <div class="mb-3">
        <select class="form-select" name="wheelchair">
            <option selected disabled>Request Wheelchair</option>
            <option>Yes</option>
            <option>No</option>
        </select>
    </div>
</div>    
        </div>
<input type="hidden" name="flight_name" value="<?php echo $flight_name; ?>">
<input type="hidden" name="price" value="<?php echo $price; ?>">
<input type="hidden" name="departure_time" value="<?php echo $departure_time; ?>">
<input type="hidden" name="arrival_time" value="<?php echo $arrival_time; ?>">

<input type="hidden" name="from_city" value="<?php echo $from_city; ?>">
<input type="hidden" name="to_city" value="<?php echo $to_city; ?>">
<input type="hidden" name="departure_date" value="<?php echo $departure_date; ?>">
<input type="hidden" name="return_date" value="<?php echo $return_date; ?>">

           <div class="btn-1">
        <button type="submit" class="btn btn-primary">
    Submit
</button>
         </div>
</div>
</div>
</form>

   


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