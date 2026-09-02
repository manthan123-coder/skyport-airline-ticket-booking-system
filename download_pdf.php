<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'include/db_json.php';
require_once 'include/pdf_generator.php';

$pnr = $_REQUEST['pnr'] ?? $_SESSION['pnr'] ?? '';
$booking = get_booking_by_pnr($pnr);

if (!$booking && !empty($_SESSION['pnr'])) {
    $booking = [
        'booking_id'     => $_SESSION['booking_id'] ?? 'BK' . date('YmdHis'),
        'pnr'            => $_SESSION['pnr'],
        'flight_name'    => $_SESSION['flight_name'] ?? '6E-201',
        'airline_name'   => $_SESSION['airline_name'] ?? 'IndiGo',
        'from_city'      => $_SESSION['from_city'] ?? 'Delhi',
        'to_city'        => $_SESSION['to_city'] ?? 'Ahmedabad',
        'departure_date' => $_SESSION['departure_date'] ?? date('Y-m-d'),
        'firstname'      => $_SESSION['firstname'] ?? 'Passenger',
        'lastname'       => $_SESSION['lastname'] ?? '',
        'email'          => $_SESSION['email'] ?? '',
        'phone'          => $_SESSION['number'] ?? $_SESSION['phone'] ?? '',
        'amount'         => $_SESSION['price'] ?? '4450',
        'seat_no'        => $_SESSION['seat_no'] ?? '1F',
        'meal_type'      => $_SESSION['meal_type'] ?? 'Veg Meal',
        'baggage_count'  => $_SESSION['baggage_count'] ?? '15kg Included',
        'checkin_status' => 'Checked-in'
    ];
}

if (!$booking) {
    die('Ticket not found for PNR: ' . htmlspecialchars($pnr));
}

output_ticket_pdf($booking);
?>
