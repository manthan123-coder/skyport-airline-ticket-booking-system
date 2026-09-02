<?php
require_once __DIR__ . '/../include/db_json.php';
require_once __DIR__ . '/../auth.php';

include 'header.php';
include 'sidebar.php';

$flights = get_all_flights();
$bookings = get_all_bookings();
$users = all_users();

$total_flights = count($flights);
$total_bookings = count($bookings);
$total_users = count($users);

$total_revenue = 0;
$checked_in_count = 0;

foreach ($bookings as $b) {
    $total_revenue += floatval($b['amount'] ?? 0);
    if (isset($b['checkin_status']) && strtolower($b['checkin_status']) === 'checked-in') {
        $checked_in_count++;
    }
}
?>

<!-- Main Content Area -->
<main class="app-main">
    
    <!-- Header Title -->
    <div class="app-content-header py-3 bg-body-secondary border-bottom mb-4">
        <div class="container-fluid px-4">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-speedometer2 text-primary me-2"></i>Admin Dashboard</h3>
                    <small class="text-muted">Welcome to SkyPort Airlines Control Panel</small>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end mb-0">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Body Content -->
    <div class="app-content">
        <div class="container-fluid px-4">

            <!-- STATS INFO BOXES -->
            <div class="row g-3 mb-4">
                
                <!-- Card 1: Flights -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-sm rounded-3 border-0 bg-primary text-white p-3">
                        <span class="info-box-icon fs-1 opacity-75"><i class="bi bi-airplane-engines-fill"></i></span>
                        <div class="info-box-content ms-3">
                            <span class="info-box-text text-uppercase fw-semibold" style="font-size: 0.8rem; letter-spacing: 0.5px;">Active Flights</span>
                            <span class="info-box-number display-6 fw-bold"><?= $total_flights; ?></span>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-white" style="width: 85%"></div>
                            </div>
                            <span class="progress-description small opacity-75 mt-1">Available across all routes</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Bookings -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-sm rounded-3 border-0 bg-success text-white p-3">
                        <span class="info-box-icon fs-1 opacity-75"><i class="bi bi-ticket-perforated-fill"></i></span>
                        <div class="info-box-content ms-3">
                            <span class="info-box-text text-uppercase fw-semibold" style="font-size: 0.8rem; letter-spacing: 0.5px;">Total Bookings</span>
                            <span class="info-box-number display-6 fw-bold"><?= $total_bookings; ?></span>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-white" style="width: 70%"></div>
                            </div>
                            <span class="progress-description small opacity-75 mt-1"><?= $checked_in_count; ?> Web Checked-in</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Revenue -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-sm rounded-3 border-0 bg-warning text-dark p-3">
                        <span class="info-box-icon fs-1 opacity-75"><i class="bi bi-currency-rupee"></i></span>
                        <div class="info-box-content ms-3">
                            <span class="info-box-text text-uppercase fw-semibold" style="font-size: 0.8rem; letter-spacing: 0.5px;">Total Revenue</span>
                            <span class="info-box-number display-6 fw-bold">₹<?= number_format($total_revenue); ?></span>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-dark" style="width: 90%"></div>
                            </div>
                            <span class="progress-description small opacity-75 mt-1">Total tickets sold</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Registered Users -->
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-sm rounded-3 border-0 bg-danger text-white p-3">
                        <span class="info-box-icon fs-1 opacity-75"><i class="bi bi-people-fill"></i></span>
                        <div class="info-box-content ms-3">
                            <span class="info-box-text text-uppercase fw-semibold" style="font-size: 0.8rem; letter-spacing: 0.5px;">Users / Accounts</span>
                            <span class="info-box-number display-6 fw-bold"><?= $total_users; ?></span>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar bg-white" style="width: 60%"></div>
                            </div>
                            <span class="progress-description small opacity-75 mt-1">Registered SkyPort members</span>
                        </div>
                    </div>
                </div>

            </div>
            <!-- END STATS INFO BOXES -->

            <div class="row g-4">

                <!-- RECENT BOOKINGS TABLE -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="bi bi-clock-history text-primary me-2"></i>Recent Ticket Bookings
                            </h5>
                            <a href="bookings.php" class="btn btn-sm btn-outline-primary rounded-pill">View All Bookings <i class="bi bi-arrow-right"></i></a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>PNR</th>
                                            <th>Passenger</th>
                                            <th>Flight</th>
                                            <th>Route</th>
                                            <th>Amount</th>
                                            <th>Check-In Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($bookings)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">No bookings found in the system yet.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach (array_slice($bookings, 0, 7) as $b): 
                                                $pnr = htmlspecialchars($b['pnr'] ?? 'N/A');
                                                $passenger = htmlspecialchars(($b['firstname'] ?? '') . ' ' . ($b['lastname'] ?? 'Passenger'));
                                                $flight_num = htmlspecialchars($b['flight_name'] ?? '6E-101');
                                                $route = htmlspecialchars(($b['from_city'] ?? 'DEL') . ' → ' . ($b['to_city'] ?? 'BOM'));
                                                $amount = number_format(floatval($b['amount'] ?? 0));
                                                $status = $b['checkin_status'] ?? 'Confirmed';
                                                $badge_class = ($status === 'Checked-in') ? 'bg-success' : 'bg-primary';
                                            ?>
                                                <tr>
                                                    <td><span class="badge bg-dark-subtle text-dark font-monospace px-2 py-1"><?= $pnr; ?></span></td>
                                                    <td class="fw-semibold text-dark"><?= $passenger; ?></td>
                                                    <td><span class="badge bg-secondary-subtle text-secondary fw-bold"><?= $flight_num; ?></span></td>
                                                    <td class="small text-muted"><?= $route; ?></td>
                                                    <td class="fw-bold text-success">₹<?= $amount; ?></td>
                                                    <td><span class="badge <?= $badge_class; ?> rounded-pill px-3 py-1"><?= htmlspecialchars($status); ?></span></td>
                                                    <td>
                                                        <a href="../boardingpass.php?pnr=<?= urlencode($pnr); ?>" target="_blank" class="btn btn-sm btn-light border text-primary" title="View Boarding Pass">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FLIGHTS OVERVIEW CARD -->
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="bi bi-airplane text-warning me-2"></i>Flight Fleet Overview
                            </h5>
                            <a href="flights.php" class="btn btn-sm btn-outline-warning text-dark rounded-pill">Manage</a>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php foreach (array_slice($flights, 0, 5) as $f): ?>
                                    <li class="list-group-item d-flex align-items-center justify-content-between py-3">
                                        <div>
                                            <div class="fw-bold text-dark mb-1"><?= htmlspecialchars($f['airline_name']); ?> (<?= htmlspecialchars($f['flight_name']); ?>)</div>
                                            <div class="small text-muted">
                                                <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($f['departure_city']); ?> → <?= htmlspecialchars($f['arrival_city']); ?>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-primary">₹<?= number_format(floatval($f['price'])); ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($f['seats_available']); ?> seats</small>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="card-footer bg-light text-center py-2">
                            <a href="flights.php" class="small fw-bold text-decoration-none">View All <?= $total_flights; ?> Active Flights &rarr;</a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

</main>
<!-- End Main Content Area -->

<?php include 'footer.php'; ?>
