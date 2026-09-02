<?php
require_once __DIR__ . '/auth.php';

// Keep only the selection values that detail.php needs after login.
$fields = ['flight_id', 'flight_name', 'airline_name', 'price', 'departure_time', 'arrival_time', 'from_city', 'to_city', 'duration', 'return_flight_id'];
foreach ($fields as $field) {
    if (isset($_POST[$field])) $_SESSION[$field] = $_POST[$field];
}

if (empty($_SESSION['user_id'])) {
    $_SESSION['post_login_redirect'] = 'detail.php';
    $_SESSION['flash_success'] = 'Please log in or create an account to continue with your booking.';
    header('Location: login.php');
    exit;
}

header('Location: detail.php');
exit;
?>
