<?php
require_once __DIR__ . '/../include/db_json.php';
require_once __DIR__ . '/../auth.php';

$message = '';
$message_type = 'success';

$search_query = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

$file = get_bookings_json_file();
$bookings = get_all_bookings();

// Handle POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $pnr = trim($_POST['pnr'] ?? '');
        $new_status = trim($_POST['checkin_status'] ?? 'Confirmed');
        $seat_no = trim($_POST['seat_no'] ?? '');

        foreach ($bookings as $idx => $b) {
            if (isset($b['pnr']) && strcasecmp($b['pnr'], $pnr) === 0) {
                $bookings[$idx]['checkin_status'] = $new_status;
                if ($new_status === 'Cancelled') {
                    $bookings[$idx]['payment_status'] = 'Cancelled';
                }
                if (!empty($seat_no)) {
                    $bookings[$idx]['seat_no'] = $seat_no;
                }
                break;
            }
        }
        file_put_contents($file, json_encode($bookings, JSON_PRETTY_PRINT));
        $_SESSION['saved_bookings'] = $bookings;
        $message = "Booking PNR <strong>{$pnr}</strong> status updated to <strong>{$new_status}</strong>.";
        $message_type = 'success';
    }

    if ($action === 'cancel_booking') {
        $pnr = trim($_POST['pnr'] ?? '');
        foreach ($bookings as $idx => $b) {
            if (isset($b['pnr']) && strcasecmp($b['pnr'], $pnr) === 0) {
                $bookings[$idx]['checkin_status'] = 'Cancelled';
                $bookings[$idx]['payment_status'] = 'Cancelled';
                $bookings[$idx]['cancellation_time'] = date('Y-m-d H:i:s');
                break;
            }
        }
        file_put_contents($file, json_encode($bookings, JSON_PRETTY_PRINT));
        $_SESSION['saved_bookings'] = $bookings;
        $message = "Booking PNR <strong>{$pnr}</strong> has been marked as <strong>Cancelled</strong>.";
        $message_type = 'warning';
    }

    if ($action === 'resend_notification') {
        $pnr = trim($_POST['pnr'] ?? '');
        $booking = get_booking_by_pnr($pnr);
        if ($booking) {
            send_booking_email_and_sms($booking);
            $message = "E-Ticket confirmation notification re-sent to <strong>" . htmlspecialchars($booking['email'] ?? '') . "</strong>.";
            $message_type = 'info';
        }
    }
}

// Filter Bookings
$filtered_bookings = [];
foreach ($bookings as $b) {
    $pnr = strtolower($b['pnr'] ?? '');
    $name = strtolower(($b['firstname'] ?? '') . ' ' . ($b['lastname'] ?? ''));
    $email = strtolower($b['email'] ?? '');
    $phone = strtolower($b['phone'] ?? '');
    $status = strtolower($b['checkin_status'] ?? 'confirmed');

    if (!empty($search_query)) {
        $q = strtolower($search_query);
        if (strpos($pnr, $q) === false && strpos($name, $q) === false && strpos($email, $q) === false && strpos($phone, $q) === false) {
            continue;
        }
    }

    if (!empty($status_filter)) {
        if ($status_filter === 'checked-in' && $status !== 'checked-in') continue;
        if ($status_filter === 'confirmed' && $status !== 'confirmed') continue;
        if ($status_filter === 'cancelled' && $status !== 'cancelled') continue;
    }

    $filtered_bookings[] = $b;
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
                    <h3 class="mb-0 fw-bold text-dark"><i class="bi bi-ticket-perforated text-success me-2"></i>Manage Bookings</h3>
                    <small class="text-muted">Search, View, and Update Passenger Tickets & Web Check-Ins</small>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end mb-0">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Bookings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="app-content">
        <div class="container-fluid px-4">

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $message_type; ?> alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- SEARCH & FILTER BAR -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-3 bg-white">
                    <form method="GET" class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Search by PNR, Passenger Name, Email, Mobile..." value="<?= htmlspecialchars($search_query); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All Check-in Statuses</option>
                                <option value="confirmed" <?= $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="checked-in" <?= $status_filter === 'checked-in' ? 'selected' : ''; ?>>Checked-In</option>
                                <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill"><i class="bi bi-funnel me-1"></i> Filter</button>
                            <a href="bookings.php" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-counterclockwise"></i></a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- BOOKINGS LIST TABLE CARD -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="bi bi-receipt text-primary me-2"></i>Passenger Bookings List (<?= count($filtered_bookings); ?> Records)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>PNR</th>
                                    <th>Passenger Details</th>
                                    <th>Flight & Route</th>
                                    <th>Date & Time</th>
                                    <th>Seat</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($filtered_bookings)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                            No passenger bookings found matching your search.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($filtered_bookings as $b): 
                                        $pnr = htmlspecialchars($b['pnr'] ?? 'N/A');
                                        $passenger = htmlspecialchars(($b['firstname'] ?? '') . ' ' . ($b['lastname'] ?? 'Passenger'));
                                        $email = htmlspecialchars($b['email'] ?? '');
                                        $phone = htmlspecialchars($b['phone'] ?? '');
                                        $flight_name = htmlspecialchars($b['flight_name'] ?? 'Flight');
                                        $airline_name = htmlspecialchars($b['airline_name'] ?? 'Airline');
                                        $route = htmlspecialchars(($b['from_city'] ?? 'Origin') . ' → ' . ($b['to_city'] ?? 'Destination'));
                                        $dep_date = !empty($b['departure_date']) ? date('d M Y', strtotime($b['departure_date'])) : 'N/A';
                                        $seat = htmlspecialchars($b['seat_no'] ?? 'Unassigned');
                                        $amount = number_format(floatval($b['amount'] ?? 0));
                                        $status = $b['checkin_status'] ?? 'Confirmed';
                                        $badge_class = ($status === 'Checked-in') ? 'bg-success' : (($status === 'Cancelled') ? 'bg-danger' : 'bg-primary');
                                    ?>
                                        <tr>
                                            <td><span class="badge bg-dark text-white font-monospace px-2 py-1 fs-6"><?= $pnr; ?></span></td>
                                            <td>
                                                <div class="fw-bold text-dark"><?= $passenger; ?></div>
                                                <div class="small text-muted"><i class="bi bi-envelope me-1"></i><?= $email; ?></div>
                                                <?php if ($phone): ?><div class="small text-muted"><i class="bi bi-phone me-1"></i><?= $phone; ?></div><?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-primary"><?= $airline_name; ?> &bull; <?= $flight_name; ?></div>
                                                <div class="small text-muted"><?= $route; ?></div>
                                            </td>
                                            <td class="small">
                                                <div><i class="bi bi-calendar3 me-1 text-secondary"></i><?= $dep_date; ?></div>
                                                <div class="text-muted"><i class="bi bi-clock me-1 text-secondary"></i><?= htmlspecialchars($b['departure_time'] ?? ''); ?></div>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning text-dark border border-warning px-2 py-1 fw-bold">
                                                    <i class="bi bi-border-inner me-1"></i><?= $seat; ?>
                                                </span>
                                            </td>
                                            <td class="fw-bold text-success">₹<?= $amount; ?></td>
                                            <td><span class="badge <?= $badge_class; ?> rounded-pill px-3 py-1"><?= htmlspecialchars($status); ?></span></td>
                                            <td class="text-end">
                                                <a href="../boardingpass.php?pnr=<?= urlencode($pnr); ?>" target="_blank" class="btn btn-sm btn-outline-info me-1" title="View Boarding Pass">
                                                    <i class="bi bi-eye"></i> Ticket
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick='openEditStatusModal("<?= $pnr; ?>", "<?= $status; ?>", "<?= $seat; ?>")' title="Update Status">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <?php if ($status !== 'Cancelled'): ?>
                                                    <form method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to cancel booking PNR <?= $pnr; ?>?');">
                                                        <input type="hidden" name="action" value="cancel_booking">
                                                        <input type="hidden" name="pnr" value="<?= $pnr; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger me-1" title="Cancel Booking">
                                                            <i class="bi bi-x-circle"></i> Cancel
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <form method="POST" style="display:inline-block;">
                                                    <input type="hidden" name="action" value="resend_notification">
                                                    <input type="hidden" name="pnr" value="<?= $pnr; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Resend E-Ticket Email">
                                                        <i class="bi bi-send"></i>
                                                    </button>
                                                </form>
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
    </div>

</main>

<!-- UPDATE STATUS MODAL -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-gear me-2"></i>Update Booking Status</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="pnr" id="modal_pnr">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">PNR NUMBER</label>
                        <input type="text" id="display_pnr" class="form-control bg-light font-monospace fw-bold" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Check-In / Ticket Status</label>
                        <select name="checkin_status" id="modal_status" class="form-select" required>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Checked-in">Checked-in</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Assigned Seat Number</label>
                        <input type="text" name="seat_no" id="modal_seat" class="form-control" placeholder="e.g. 14B">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill fw-bold px-4"><i class="bi bi-check-circle me-1"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditStatusModal(pnr, status, seat) {
    document.getElementById('modal_pnr').value = pnr;
    document.getElementById('display_pnr').value = pnr;
    document.getElementById('modal_status').value = status;
    document.getElementById('modal_seat').value = (seat === 'Unassigned') ? '' : seat;

    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}
</script>

<?php include 'footer.php'; ?>
