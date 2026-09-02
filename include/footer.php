    <!-- FOOTER -->
    <footer class="footer mt-5">
      <div class="container">
        <div class="footer-grid">

          <!-- Logo & Description -->
          <div class="footer-col">
            <div class="footer-logo d-flex align-items-center mb-3">
              <span class="logo-circle me-2">✈</span>
              <span class="fw-bold fs-4 text-white">SkyPort</span>
            </div>
            <p class="footer-text">
              Fast, reliable and easy flight booking system with real-time flight schedules, flexible ticketing, and instantly generated boarding passes.
            </p>

            <h6 class="footer-title mt-4">Subscribe to our special offers</h6>
            <form class="subscribe-box" onsubmit="event.preventDefault(); alert('Subscribed successfully!');">
              <input type="email" placeholder="Enter your email address" required>
              <button type="submit">Subscribe</button>
            </form>
          </div>

          <!-- Booking Links -->
          <div class="footer-col">
            <h5 class="footer-heading">Quick Booking</h5>
            <ul>
              <li><a href="index.php">Book Flights</a></li>
              <li><a href="flight.php">All Destinations</a></li>
              <li><a href="webcheckin.php">Web Check-in</a></li>
              <li><a href="mybooking.php">My Trips</a></li>
            </ul>
          </div>

          <!-- Useful Links -->
          <div class="footer-col">
            <h5 class="footer-heading">Useful Links</h5>
            <ul>
              <li><a href="index.php">Home</a></li>
              <li><a href="flight.php">Available Flights</a></li>
              <li><a href="mybooking.php">Manage Booking</a></li>
              <li><a href="login.php">Account Login</a></li>
            </ul>
          </div>

          <!-- Contact Details -->
          <div class="footer-col">
            <h5 class="footer-heading">Contact Us</h5>
            <ul class="contact-list">
              <li>📍 SkyPort International Airport, Terminal 3</li>
              <li>📞 <a href="tel:+18001234567">+1 800 123 4567</a></li>
              <li>✉️ <a href="mailto:support@skyport.com">support@skyport.com</a></li>
            </ul>

            <h6 class="footer-title mt-3">Follow Us</h6>
            <div class="social-icons">
              <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
              <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
              <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
              <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
            </div>
          </div>

        </div>

        <div class="footer-bottom">
          © <?= date('Y'); ?> SkyPort Airlines. All Rights Reserved.
        </div>
      </div>
    </footer>

    <!-- Bootstrap 5 & Flatpickr Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="index.js"></script>
</body>
</html>