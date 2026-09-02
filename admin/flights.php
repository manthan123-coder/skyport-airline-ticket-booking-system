<?php
require_once __DIR__ . '/../include/db_json.php';
require_once __DIR__ . '/../auth.php';

$message = '';
$message_type = 'success';

$file = get_flights_json_file();
$flights = get_all_flights();

// Handle POST actions (Add, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_flight') {
        $flight_name = trim($_POST['flight_name'] ?? '');
        $airline_name = trim($_POST['airline_name'] ?? '');
        $aircraft = trim($_POST['aircraft'] ?? 'Airbus A320neo');
        $departure_city = trim($_POST['departure_city'] ?? '');
        $arrival_city = trim($_POST['arrival_city'] ?? '');
        $departure_time = trim($_POST['departure_time'] ?? '06:00');
        $arrival_time = trim($_POST['arrival_time'] ?? '08:15');
        $duration = trim($_POST['duration'] ?? '2h 15m');
        $price = floatval($_POST['price'] ?? 4500);
        $seats_available = intval($_POST['seats_available'] ?? 30);

        if (!empty($flight_name) && !empty($airline_name) && !empty($departure_city) && !empty($arrival_city)) {
            $new_id = 1;
            foreach ($flights as $f) {
                if (isset($f['id']) && intval($f['id']) >= $new_id) {
                    $new_id = intval($f['id']) + 1;
                }
            }

            // Determine logo
            $logo_map = [
                'IndiGo' => 'Flights/logo/indigo.png',
                'Air India' => 'Flights/logo/airindia.webp',
                'SpiceJet' => 'Flights/logo/Spicejet.png',
                'Akasa Air' => 'Flights/logo/akasa.webp',
                'Air India Express' => 'Flights/logo/airindiaexpress.png'
            ];
            $logo = $logo_map[$airline_name] ?? 'Flights/logo/indigo.png';

            $new_flight = [
                "id" => $new_id,
                "flight_name" => $flight_name,
                "airline_name" => $airline_name,
                "aircraft" => $aircraft,
                "departure_city" => $departure_city,
                "arrival_city" => $arrival_city,
                "departure_time" => $departure_time,
                "arrival_time" => $arrival_time,
                "duration" => $duration,
                "stops" => "Non-stop",
                "price" => $price,
                "seats_available" => $seats_available,
                "logo" => $logo
            ];

            array_unshift($flights, $new_flight);
            save_flights($flights);
            $message = "Flight <strong>{$flight_name}</strong> added successfully!";
            $message_type = 'success';
        } else {
            $message = "Please fill in all required fields.";
            $message_type = 'danger';
        }
    }

    if ($action === 'edit_flight') {
        $flight_id = intval($_POST['flight_id'] ?? 0);
        foreach ($flights as $idx => $f) {
            if (isset($f['id']) && intval($f['id']) === $flight_id) {
                $flights[$idx]['flight_name'] = trim($_POST['flight_name'] ?? $f['flight_name']);
                $flights[$idx]['airline_name'] = trim($_POST['airline_name'] ?? $f['airline_name']);
                $flights[$idx]['aircraft'] = trim($_POST['aircraft'] ?? $f['aircraft']);
                $flights[$idx]['departure_city'] = trim($_POST['departure_city'] ?? $f['departure_city']);
                $flights[$idx]['arrival_city'] = trim($_POST['arrival_city'] ?? $f['arrival_city']);
                $flights[$idx]['departure_time'] = trim($_POST['departure_time'] ?? $f['departure_time']);
                $flights[$idx]['arrival_time'] = trim($_POST['arrival_time'] ?? $f['arrival_time']);
                $flights[$idx]['duration'] = trim($_POST['duration'] ?? $f['duration']);
                $flights[$idx]['price'] = floatval($_POST['price'] ?? $f['price']);
                $flights[$idx]['seats_available'] = intval($_POST['seats_available'] ?? $f['seats_available']);
                break;
            }
        }
        save_flights($flights);
        $message = "Flight updated successfully!";
        $message_type = 'success';
    }

    if ($action === 'delete_flight') {
        $flight_id = intval($_POST['flight_id'] ?? 0);
        $deleted_flight_name = '';
        foreach ($flights as $f) {
            if (isset($f['id']) && intval($f['id']) === $flight_id) {
                $deleted_flight_name = $f['flight_name'] ?? '';
                break;
            }
        }

        $updated_flights = [];
        foreach ($flights as $f) {
            if (isset($f['id']) && intval($f['id']) === $flight_id) {
                continue;
            }
            $updated_flights[] = $f;
        }
        $flights = $updated_flights;
        save_flights($flights);

        if (!empty($deleted_flight_name)) {
            $b_file = get_bookings_json_file();
            $all_b = get_all_bookings();
            $b_changed = false;
            foreach ($all_b as $b_idx => $b_item) {
                if (isset($b_item['flight_name']) && strcasecmp(trim($b_item['flight_name']), trim($deleted_flight_name)) === 0) {
                    $all_b[$b_idx]['checkin_status'] = 'Cancelled';
                    $all_b[$b_idx]['payment_status'] = 'Cancelled';
                    $all_b[$b_idx]['cancellation_time'] = date('Y-m-d H:i:s');
                    $b_changed = true;
                }
            }
            if ($b_changed) {
                file_put_contents($b_file, json_encode($all_b, JSON_PRETTY_PRINT));
                $_SESSION['saved_bookings'] = $all_b;
            }
        }

        $message = "Flight deleted and all associated passenger bookings marked as <strong>Cancelled</strong>.";
        $message_type = 'warning';
    }
}

include 'header.php';
include 'sidebar.php';
?>

<!-- Main Content Area -->
<main class="app-main">

    <!-- Header Title -->
    <div class="app-content-header py-3 bg-body-secondary border-bottom mb-4">
        <div class="container-fluid px-4">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-airplane text-warning me-2"></i>Manage Flights</h3>
                    <small class="text-muted">Create, Edit, and Manage Flight Schedules</small>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-end">
                        <button class="btn btn-primary rounded-pill fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addFlightModal">
                            <i class="bi bi-plus-circle me-1"></i> Add New Flight
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="app-content">
        <div class="container-fluid px-4">

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $message_type; ?> alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i><?= $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- FLIGHTS TABLE CARD -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="bi bi-list-task text-primary me-2"></i>Flight Fleet Listing (<?= count($flights); ?> Total)
                    </h5>
                    <div class="w-25">
                        <input type="text" id="flightSearchInput" class="form-control form-control-sm" placeholder="Search airline or city..." onkeyup="filterFlightsTable()">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="flightsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Airline</th>
                                    <th>Flight No.</th>
                                    <th>Route (From &rarr; To)</th>
                                    <th>Timing</th>
                                    <th>Price</th>
                                    <th>Seats</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($flights as $f): 
                                    $fid = $f['id'] ?? 0;
                                ?>
                                    <tr>
                                        <td class="small text-muted font-monospace">#<?= $fid; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width: 28px; height: 28px; display:flex; align-items:center; justify-content:center; background:#f8fafc; border-radius:6px; border:1px solid #e2e8f0;">
                                                    <img src="../<?= htmlspecialchars($f['logo'] ?? 'Flights/logo/indigo.png'); ?>" alt="logo" style="max-width:100%; max-height:100%; object-fit:contain;" onerror="this.src='../Flights/logo/indigo.png'">
                                                </div>
                                                <span class="fw-bold text-dark"><?= htmlspecialchars($f['airline_name']); ?></span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1"><?= htmlspecialchars($f['flight_name']); ?></span></td>
                                        <td>
                                            <span class="fw-semibold text-dark"><?= htmlspecialchars($f['departure_city']); ?></span>
                                            <i class="bi bi-arrow-right text-muted mx-1"></i>
                                            <span class="fw-semibold text-dark"><?= htmlspecialchars($f['arrival_city']); ?></span>
                                        </td>
                                        <td class="small">
                                            <i class="bi bi-clock me-1 text-primary"></i><?= htmlspecialchars($f['departure_time']); ?> - <?= htmlspecialchars($f['arrival_time']); ?>
                                            <div class="text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($f['duration']); ?></div>
                                        </td>
                                        <td class="fw-bold text-success">₹<?= number_format(floatval($f['price'])); ?></td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                <?= htmlspecialchars($f['seats_available']); ?> Left
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-primary me-1" 
                                                    onclick='openEditModal(<?= json_encode($f); ?>)' title="Edit Flight">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete flight <?= htmlspecialchars($f['flight_name']); ?>?');">
                                                <input type="hidden" name="action" value="delete_flight">
                                                <input type="hidden" name="flight_id" value="<?= $fid; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Flight">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</main>

<!-- ADD FLIGHT MODAL -->
<div class="modal fade" id="addFlightModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Add New Flight Schedule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_flight">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Airline Carrier</label>
                            <select name="airline_name" class="form-select" required>
                                <option value="IndiGo">IndiGo</option>
                                <option value="Air India">Air India</option>
                                <option value="Air India Express">Air India Express</option>
                                <option value="SpiceJet">SpiceJet</option>
                                <option value="Akasa Air">Akasa Air</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Flight Code / Number</label>
                            <input type="text" name="flight_name" class="form-control" placeholder="e.g. 6E-502" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Departure City (Origin)</label>
                            <input type="text" name="departure_city" class="form-control" placeholder="e.g. Delhi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Arrival City (Destination)</label>
                            <input type="text" name="arrival_city" class="form-control" placeholder="e.g. Mumbai" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Departure Time</label>
                            <input type="time" name="departure_time" class="form-control" value="08:00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Arrival Time</label>
                            <input type="time" name="arrival_time" class="form-control" value="10:15" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Duration</label>
                            <input type="text" name="duration" class="form-control" value="2h 15m" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Ticket Price (₹)</label>
                            <input type="number" name="price" class="form-control" value="4500" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Seats Available</label>
                            <input type="number" name="seats_available" class="form-control" value="40" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Aircraft Model</label>
                            <input type="text" name="aircraft" class="form-control" value="Airbus A320neo">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill fw-bold px-4"><i class="bi bi-check-circle me-1"></i> Save Flight</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT FLIGHT MODAL -->
<div class="modal fade" id="editFlightModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Flight Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit_flight">
                <input type="hidden" name="flight_id" id="edit_flight_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Airline Carrier</label>
                            <input type="text" name="airline_name" id="edit_airline_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Flight Code / Number</label>
                            <input type="text" name="flight_name" id="edit_flight_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Departure City</label>
                            <input type="text" name="departure_city" id="edit_departure_city" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Arrival City</label>
                            <input type="text" name="arrival_city" id="edit_arrival_city" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Departure Time</label>
                            <input type="text" name="departure_time" id="edit_departure_time" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Arrival Time</label>
                            <input type="text" name="arrival_time" id="edit_arrival_time" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Duration</label>
                            <input type="text" name="duration" id="edit_duration" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Price (₹)</label>
                            <input type="number" name="price" id="edit_price" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Seats Available</label>
                            <input type="number" name="seats_available" id="edit_seats_available" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Aircraft Model</label>
                            <input type="text" name="aircraft" id="edit_aircraft" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-pill fw-bold px-4"><i class="bi bi-save me-1"></i> Update Flight</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function filterFlightsTable() {
    const query = (document.getElementById('flightSearchInput').value || '').toLowerCase();
    const rows = document.querySelectorAll('#flightsTable tbody tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
    });
}

function openEditModal(flight) {
    document.getElementById('edit_flight_id').value = flight.id || 0;
    document.getElementById('edit_airline_name').value = flight.airline_name || '';
    document.getElementById('edit_flight_name').value = flight.flight_name || '';
    document.getElementById('edit_departure_city').value = flight.departure_city || '';
    document.getElementById('edit_arrival_city').value = flight.arrival_city || '';
    document.getElementById('edit_departure_time').value = flight.departure_time || '';
    document.getElementById('edit_arrival_time').value = flight.arrival_time || '';
    document.getElementById('edit_duration').value = flight.duration || '';
    document.getElementById('edit_price').value = flight.price || 0;
    document.getElementById('edit_seats_available').value = flight.seats_available || 0;
    document.getElementById('edit_aircraft').value = flight.aircraft || '';

    const editModal = new bootstrap.Modal(document.getElementById('editFlightModal'));
    editModal.show();
}
</script>

<?php include 'footer.php'; ?>
