<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'include/db_json.php';

$type = $_GET['type'] ?? 'booking';
$pnr  = $_GET['pnr'] ?? 'SKP' . rand(10000, 99999);

$booking = get_booking_by_pnr($pnr);
if (!$booking) {
    $booking = [
        'pnr'            => $pnr,
        'flight_name'    => '6E-509',
        'airline_name'   => 'IndiGo',
        'from_city'      => 'Vadodara',
        'to_city'        => 'Mumbai',
        'departure_date' => date('Y-m-d', strtotime('+3 days')),
        'departure_time' => '21:00',
        'arrival_time'   => '23:33',
        'firstname'      => 'Manthan',
        'lastname'       => 'Gondaliya',
        'email'          => 'manthangondaliya56@gmail.com',
        'phone'          => '+91 9876543210',
        'amount'         => 7554,
        'seat_no'        => '3F',
        'meal_type'      => 'Standard Meal',
        'baggage_count'  => '15kg Included'
    ];
}

$firstname = trim($booking['firstname'] ?? 'Traveler');
$lastname = trim($booking['lastname'] ?? '');
$name = trim($firstname . ' ' . $lastname);
$to_email = $booking['email'] ?? 'passenger@example.com';
$to_phone = $booking['phone'] ?? '+91 9876543210';
$from_city = $booking['from_city'] ?? 'Vadodara';
$to_city = $booking['to_city'] ?? 'Mumbai';
$airline_name = $booking['airline_name'] ?? 'IndiGo';
$flight_name = $booking['flight_name'] ?? '6E-509';
$departure_date = date('d M Y', strtotime($booking['departure_date'] ?? date('Y-m-d')));
$departure_time = $booking['departure_time'] ?? '21:00';
$arrival_time = $booking['arrival_time'] ?? '23:33';
$amount = number_format(floatval($booking['amount'] ?? 7554));

if ($type === 'checkin' || $type === 'boardingpass') {
    $seat_no = $booking['seat_no'] ?? '3F';
    $boarding_time = date('H:i', strtotime($departure_time . ' -40 minutes'));
    $qr_data = urlencode("SkyPort Pass: {$name} | PNR: {$pnr} | Flight: {$flight_name} | Seat: {$seat_no} | Gate: A12");
    
    $body_content = "
        <p style='margin-top: 0;'>Dear <strong>{$name}</strong>,</p>
        <p style='color: #475569;'>Your Web Check-In has been completed successfully! Your digital boarding pass has been issued and seat <strong>{$seat_no}</strong> is reserved.</p>
        
        <div class='route-card'>
            <div style='font-size: 13px; font-weight: 700; color: #2563eb; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px;'>{$airline_name} &bull; Flight {$flight_name}</div>
            <div style='font-size: 26px; font-weight: 800; color: #0f172a; margin: 8px 0;'>
                {$from_city} <span style='color: #2563eb; font-size: 22px; margin: 0 8px;'>✈</span> {$to_city}
            </div>
            <div style='margin-top: 12px; font-size: 13px; color: #475569; background: #ffffff; padding: 10px 16px; border-radius: 30px; display: inline-block; border: 1px solid #e2e8f0;'>
                📅 <strong>{$departure_date}</strong> &bull; 🛫 Dep: <strong>{$departure_time}</strong> &bull; 🕒 Boarding: <strong style='color: #d97706;'>{$boarding_time}</strong>
            </div>
        </div>

        <table class='info-table'>
            <tr><td class='label'>Passenger Name</td><td class='value'>{$name}</td></tr>
            <tr><td class='label'>PNR Reference</td><td class='value' style='color: #2563eb; font-weight: 800;'>{$pnr}</td></tr>
            <tr><td class='label'>Assigned Seat Number</td><td class='value'><span style='background: #fef3c7; color: #d97706; font-weight: 800; padding: 4px 14px; border-radius: 20px; font-size: 14px;'>Seat {$seat_no}</span></td></tr>
            <tr><td class='label'>Terminal & Gate</td><td class='value'>Terminal 2 &bull; Gate A12 (Zone 2)</td></tr>
            <tr><td class='label'>Check-In Status</td><td class='value'><span style='color: #16a34a; background: #dcfce7; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 700;'>✓ Checked-In</span></td></tr>
            <tr><td class='label'>Total Amount Paid</td><td class='value' style='color: #2563eb;'>₹{$amount}</td></tr>
        </table>

        <div style='text-align: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; margin-top: 24px;'>
            <img src='https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={$qr_data}' alt='Boarding Pass QR Code' width='150' height='150' style='border-radius: 8px; border: 4px solid #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.1);'>
            <div style='font-size: 13px; font-weight: 700; color: #475569; margin-top: 10px;'>Digital Boarding Pass QR Code</div>
            <div style='font-size: 12px; color: #94a3b8;'>Scan at Airport Gate A12 Security Checkpoints</div>
        </div>

        <div style='text-align: center; margin-top: 26px; display: flex; flex-wrap: wrap; justify-content: center; gap: 12px;'>
            <a href='http://localhost/airport/project/download_pdf.php?pnr={$pnr}' class='btn-primary' style='background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); box-shadow: 0 8px 20px rgba(220,38,38,0.3); text-decoration: none; margin: 4px;'>📄 Download Official PDF Ticket & Pass</a>
            <a href='http://localhost/airport/project/boardingpass.php?pnr={$pnr}' class='btn-primary' style='background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); box-shadow: 0 8px 20px rgba(13,110,253,0.3); text-decoration: none; margin: 4px;'>🎫 View Boarding Pass Online</a>
        </div>
    ";

    $html_email = build_modern_email_html([
        'title'        => "SkyPort Boarding Pass - Seat {$seat_no}",
        'subtitle'     => "Digital Boarding Pass & Express Gate Clearance",
        'badge_text'   => "Assigned Seat: {$seat_no}",
        'badge_style'  => "background: #f59e0b; color: #0f172a;",
        'body_content' => $body_content,
        'footer_text'  => "SkyPort Airlines System &bull; Express Airport Boarding Terminal"
    ]);

} else if ($type === 'otp') {
    $otp = rand(100000, 999999);
    $otp_body_content = "
        <p style='margin-top: 0;'>Dear Passenger,</p>
        <p style='color: #475569;'>Your 6-digit Bank Security OTP for authorizing your flight ticket booking payment is:</p>
        
        <div style='text-align: center; margin: 28px 0; background: linear-gradient(145deg, #f8fafc 0%, #eff6ff 100%); border: 1px solid #bfdbfe; border-radius: 16px; padding: 24px;'>
            <div style='font-size: 12px; font-weight: 700; color: #2563eb; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;'>Payment Verification Code</div>
            <span style='display: inline-block; background: #1d4ed8; color: #ffffff; padding: 14px 28px; font-size: 32px; font-weight: 800; border-radius: 14px; letter-spacing: 8px; box-shadow: 0 8px 20px rgba(29,78,216,0.35); font-family: monospace;'>{$otp}</span>
            <div style='margin-top: 12px; font-size: 13px; color: #dc2626; font-weight: 600;'>⏳ Valid for 5 Minutes Only</div>
        </div>

        <div style='background: #fffbeb; border-left: 4px solid #f59e0b; padding: 14px 18px; border-radius: 8px; font-size: 13px; color: #92400e; margin-top: 20px;'>
            🛡️ <strong>Security Warning:</strong> SkyPort Airlines will never call or request your OTP. Do not share this code with anyone.
        </div>
    ";

    $html_email = build_modern_email_html([
        'title'        => "SkyPort 2FA Payment Verification - OTP: {$otp}",
        'subtitle'     => "2FA Payment Authorization & Security Gateway",
        'badge_text'   => "SECURITY OTP: {$otp}",
        'badge_style'  => "background: #10b981; color: #ffffff;",
        'body_content' => $otp_body_content,
        'footer_text'  => "SkyPort Payment Security Gateway &bull; 256-Bit SSL Encrypted"
    ]);

} else {
    $body_content = "
        <p style='margin-top: 0;'>Dear <strong>{$name}</strong>,</p>
        <p style='color: #475569;'>Thank you for choosing <strong>SkyPort Airlines</strong>! Your flight reservation is confirmed and your official E-Ticket has been issued. Below are your itinerary and ticket details:</p>
        
        <div class='route-card'>
            <div style='font-size: 13px; font-weight: 700; color: #2563eb; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px;'>{$airline_name} &bull; Flight {$flight_name}</div>
            <div style='font-size: 26px; font-weight: 800; color: #0f172a; margin: 8px 0;'>
                {$from_city} <span style='color: #2563eb; font-size: 22px; margin: 0 8px;'>✈</span> {$to_city}
            </div>
            <div style='margin-top: 12px; font-size: 13px; color: #64748b; background: #ffffff; padding: 10px 16px; border-radius: 30px; display: inline-block; border: 1px solid #e2e8f0;'>
                📅 <strong>{$departure_date}</strong> &nbsp;|&nbsp; 🛫 Dep: <strong>{$departure_time}</strong> &nbsp;|&nbsp; 🛬 Arr: <strong>{$arrival_time}</strong>
            </div>
        </div>

        <table class='info-table'>
            <tr><td class='label'>Passenger Name</td><td class='value'>{$name}</td></tr>
            <tr><td class='label'>Registered Email</td><td class='value'>{$to_email}</td></tr>
            <tr><td class='label'>Mobile Number</td><td class='value'>{$to_phone}</td></tr>
            <tr><td class='label'>Booking Reference (PNR)</td><td class='value' style='color: #2563eb; font-weight: 800;'>{$pnr}</td></tr>
            <tr><td class='label'>Booking Status</td><td class='value'><span style='color: #16a34a; background: #dcfce7; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 700;'>✓ Confirmed / Paid</span></td></tr>
            <tr><td class='label'>Total Fare Paid</td><td class='value' style='font-size: 18px; color: #2563eb;'>₹{$amount}</td></tr>
        </table>

        <div style='text-align: center; margin-top: 30px; display: flex; flex-wrap: wrap; justify-content: center; gap: 12px;'>
            <a href='http://localhost/airport/project/download_pdf.php?pnr={$pnr}' class='btn-primary' style='background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); box-shadow: 0 8px 20px rgba(220,38,38,0.3); text-decoration: none; margin: 4px;'>📄 Download PDF Ticket & Pass</a>
            <a href='http://localhost/airport/project/confirmation.php?pnr={$pnr}' class='btn-primary' style='background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); box-shadow: 0 8px 20px rgba(13,110,253,0.3); text-decoration: none; margin: 4px;'>📑 View E-Ticket & Receipt</a>
        </div>

        <div style='margin-top: 26px; background: #eff6ff; border-left: 4px solid #2563eb; padding: 14px 18px; border-radius: 8px; font-size: 13px; color: #1e40af;'>
            💡 <strong>Airport Reminders:</strong> Please present a valid government photo ID along with this E-Ticket at check-in. Web Check-in opens 48 hours prior to scheduled departure.
        </div>
    ";

    $html_email = build_modern_email_html([
        'title'        => "SkyPort E-Ticket Confirmation - PNR: {$pnr}",
        'subtitle'     => "Official Flight E-Ticket & Itinerary Receipt",
        'badge_text'   => "PNR: {$pnr}",
        'badge_style'  => "background: #2563eb; color: #ffffff;",
        'body_content' => $body_content,
        'footer_text'  => "SkyPort Airlines System &bull; 24/7 Customer Support Desk"
    ]);
}

echo $html_email;
