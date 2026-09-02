<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class SkyPortDocsPDF {
    private $buffer = '';
    private $offsets = [];

    public function __construct() {
        $this->buffer = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
    }

    private function addObject($content) {
        $id = count($this->offsets) + 1;
        $this->offsets[$id] = strlen($this->buffer);
        $this->buffer .= "{$id} 0 obj\n{$content}\nendobj\n";
        return $id;
    }

    private function addJpegImage($filepath) {
        if (!file_exists($filepath)) return null;
        $data = file_get_contents($filepath);
        if (!$data) return null;

        $width = 1280;
        $height = 850;
        $pos = 2;
        $len = strlen($data);
        while ($pos < $len) {
            if ($data[$pos] !== "\xFF") break;
            $marker = ord($data[$pos + 1]);
            if ($marker >= 0xC0 && $marker <= 0xC3) {
                $height = unpack('n', substr($data, $pos + 5, 2))[1];
                $width = unpack('n', substr($data, $pos + 7, 2))[1];
                break;
            }
            $sectionLen = unpack('n', substr($data, $pos + 2, 2))[1];
            $pos += 2 + $sectionLen;
        }

        $id = count($this->offsets) + 1;
        $this->offsets[$id] = strlen($this->buffer);
        $this->buffer .= "{$id} 0 obj\n<< /Type /XObject /Subtype /Image /Width {$width} /Height {$height} /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length " . strlen($data) . " >>\nstream\n{$data}\nendstream\nendobj\n";

        return [
            'id' => $id,
            'name' => 'Img' . $id,
            'width' => $width,
            'height' => $height
        ];
    }

    private function pdfEscape($str) {
        $str = str_replace('\\', '\\\\', (string)$str);
        $str = str_replace('(', '\\(', $str);
        $str = str_replace(')', '\\)', $str);
        return preg_replace('/[^\x20-\x7E]/', ' ', $str);
    }

    public function generate() {
        $fontHelvetica = $this->addObject("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>");
        $fontHelveticaBold = $this->addObject("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>");

        $pagesData = [];

        // Load Real JPEG Screenshots
        $imgLogin = $this->addJpegImage(__DIR__ . '/screenshots/1_login_real.jpg');
        $imgSearch = $this->addJpegImage(__DIR__ . '/screenshots/2_home_search_real.jpg');
        $imgFleet = $this->addJpegImage(__DIR__ . '/screenshots/3_flight_results_real.jpg');
        $imgCheckout = $this->addJpegImage(__DIR__ . '/screenshots/4_detail_checkout_real.jpg');
        $imgPayment = $this->addJpegImage(__DIR__ . '/screenshots/5_payment_otp_real.jpg');
        $imgConfirm = $this->addJpegImage(__DIR__ . '/screenshots/6_confirmation_real.jpg');
        $imgCheckin = $this->addJpegImage(__DIR__ . '/screenshots/7_webcheckin_real.jpg');
        $imgTrips = $this->addJpegImage(__DIR__ . '/screenshots/8_mybooking_real.jpg');
        $imgAdmin = $this->addJpegImage(__DIR__ . '/screenshots/9_admin_real.jpg');

        // =============================================================
        // PAGE 1: EASY STUDENT COVER CERTIFICATE
        // =============================================================
        $p1 = "";
        $p1 .= "0.04 0.10 0.22 rg 0 510 595 332 re f\n";
        $p1 .= "1 1 1 rg BT /F2 26 Tf 40 785 Td (" . $this->pdfEscape("SkyPort Airlines Booking System") . ") Tj ET\n";
        $p1 .= "0.85 0.92 1.0 rg BT /F2 16 Tf 40 755 Td (" . $this->pdfEscape("Student Project Report & Technical Documentation") . ") Tj ET\n";
        $p1 .= "0.65 0.78 0.95 rg BT /F1 11 Tf 40 733 Td (" . $this->pdfEscape("Easy-To-Understand Step-by-Step Flow: Login to Confirmation & Beyond") . ") Tj ET\n";

        $p1 .= "0.95 0.96 0.98 rg 40 540 515 165 re f\n";
        $p1 .= "0.80 0.85 0.92 RG 1 w 40 540 515 165 re s\n";
        $p1 .= "0.04 0.10 0.22 rg BT /F2 12 Tf 55 682 Td (" . $this->pdfEscape("STUDENT PERSONAL DETAILS & PROJECT CERTIFICATE") . ") Tj ET\n";

        $cert = [
            ["Student Name:", "Manthan"],
            ["Academic Semester:", "Semester 5 (Sem-5)"],
            ["Department / Branch:", "Department of Computer Science & Engineering"],
            ["College / Institute:", "Institute of Engineering & Technology"],
            ["Project Name:", "SkyPort Airline Ticket Booking & Management System"],
            ["Core Stack Used:", "PHP 8+, HTML5, Custom CSS3, ES6 JavaScript, JSON Database"],
            ["Submission Date:", date('F d, Y')]
        ];

        $y = 660;
        foreach ($cert as $c) {
            $p1 .= "0.05 0.35 0.85 rg BT /F2 9.5 Tf 55 {$y} Td (" . $this->pdfEscape($c[0]) . ") Tj ET\n";
            $p1 .= "0.15 0.15 0.15 rg BT /F1 9.5 Tf 185 {$y} Td (" . $this->pdfEscape($c[1]) . ") Tj ET\n";
            $y -= 17;
        }

        $p1 .= "0.04 0.10 0.22 rg BT /F2 12 Tf 40 495 Td (" . $this->pdfEscape("EASY PROJECT FLOW INDEX (PAGES 1 TO 10)") . ") Tj ET\n";

        $toc = [
            ["Page 1", "Student Cover Certificate & Easy Project Flow Index"],
            ["Page 2", "Step 1: User Account Login & Password Security (login.php)"],
            ["Page 3", "Step 2: Home Page & Dynamic Flight Search Box (index.php)"],
            ["Page 4", "Step 3: Flight Fleet Selection & Ticket Fares (flight.php)"],
            ["Page 5", "Step 4: Passenger Details & Checkout Form (detail.php)"],
            ["Page 6", "Step 5: Payment Gateway & Simulated OTP Verification (payment.php)"],
            ["Page 7", "Step 6: Booking Confirmation & Digital Ticket Stub (confirmation.php)"],
            ["Page 8", "Step 7: Instant Web Check-In & Gate QR Boarding Pass (webcheckin.php)"],
            ["Page 9", "Step 8: Traveler Dashboard (My Trips) & Cancellation Sync (mybooking.php)"],
            ["Page 10", "Step 9: Super-Admin Control Panel & Easy Viva Speaking Script"]
        ];

        $y = 472;
        foreach ($toc as $t) {
            $p1 .= "0.96 0.97 0.99 rg 40 " . ($y - 4) . " 515 19 re f\n";
            $p1 .= "0.05 0.35 0.85 rg BT /F2 9.5 Tf 50 {$y} Td (" . $this->pdfEscape($t[0]) . ") Tj ET\n";
            $p1 .= "0.15 0.15 0.15 rg BT /F1 9.5 Tf 115 {$y} Td (" . $this->pdfEscape($t[1]) . ") Tj ET\n";
            $y -= 21;
        }

        $p1 .= "0.04 0.10 0.22 rg 40 45 515 2 re f\n";
        $p1 .= "0.4 0.4 0.4 rg BT /F1 9 Tf 210 30 Td (" . $this->pdfEscape("SkyPort Project Documentation -- Page 1 of 10") . ") Tj ET\n";

        $pagesData[] = ['stream' => $p1, 'img' => null];

        // Helper function for page content with Real Embedded Image & Easy Bullets
        $buildPage = function($pageNum, $title, $url, $imgInfo, $bullets) {
            $s = "";
            $s .= "0.04 0.10 0.22 rg 0 800 595 42 re f\n";
            $s .= "1 1 1 rg BT /F2 14 Tf 40 815 Td (" . $this->pdfEscape($title) . ") Tj ET\n";

            // Browser Header Line
            $s .= "0.92 0.93 0.96 rg 40 765 515 22 re f\n";
            $s .= "0.80 0.85 0.90 RG 0.5 w 40 765 515 22 re s\n";
            $s .= "0.25 0.25 0.30 rg BT /F1 9 Tf 50 772 Td (" . $this->pdfEscape("REAL WEBSITE SCREENSHOT  |  URL: " . $url) . ") Tj ET\n";

            // Embed Real JPEG Image on PDF Page
            if ($imgInfo) {
                $s .= "0.85 0.88 0.92 RG 1 w 40 450 515 310 re s\n";
                $s .= "q 515 0 0 310 40 450 cm /{$imgInfo['name']} Do Q\n";
            }

            // Easy Explanation Section
            $s .= "0.04 0.10 0.22 rg BT /F2 11 Tf 40 425 Td (" . $this->pdfEscape("EASY UNDERSTANDING -- HOW THIS FEATURE WORKS:") . ") Tj ET\n";

            $y = 402;
            foreach ($bullets as $b) {
                $s .= "0.96 0.97 0.99 rg 40 " . ($y - 26) . " 515 32 re f\n";
                $s .= "0.80 0.85 0.92 RG 1 w 40 " . ($y - 26) . " 515 32 re s\n";
                $s .= "0.05 0.35 0.85 rg BT /F2 9.5 Tf 50 {$y} Td (" . $this->pdfEscape($b[0]) . ") Tj ET\n";
                $s .= "0.20 0.20 0.20 rg BT /F1 9 Tf 50 " . ($y - 14) . " Td (" . $this->pdfEscape(substr($b[1], 0, 115)) . ") Tj ET\n";
                $s .= "0.20 0.20 0.20 rg BT /F1 9 Tf 50 " . ($y - 24) . " Td (" . $this->pdfEscape(substr($b[1], 115)) . ") Tj ET\n";
                $y -= 40;
            }

            // Footer
            $s .= "0.04 0.10 0.22 rg 40 45 515 2 re f\n";
            $s .= "0.4 0.4 0.4 rg BT /F1 9 Tf 210 30 Td (" . $this->pdfEscape("SkyPort Project Documentation -- Page {$pageNum} of 10") . ") Tj ET\n";

            return ['stream' => $s, 'img' => $imgInfo];
        };

        // PAGE 2: LOGIN
        $pagesData[] = $buildPage(
            2,
            "Step 1 -- User Account Login & Security (login.php)",
            "http://localhost/airport/project/login.php",
            $imgLogin,
            [
                ["User Login Process:", "User enters email & password. Password is checked safely using PHP password_hash()."],
                ["Session Initialization:", "User email is saved in \$_SESSION['user_email'] so you stay logged in securely."],
                ["Clean Page Redirect:", "After login, user redirects straight to index.php without exposing passwords in URL."]
            ]
        );

        // PAGE 3: SEARCH
        $pagesData[] = $buildPage(
            3,
            "Step 2 -- Home Page & Dynamic Flight Search Box (index.php)",
            "http://localhost/airport/project/index.php",
            $imgSearch,
            [
                ["Flight Search Box:", "User picks Origin city, Destination city, Departure date, and Passengers counter."],
                ["Dynamic City Hiding Feature:", "Selecting 'Delhi' in From city automatically hides 'Delhi' from To city dropdown in real-time."],
                ["Date & Passenger Selection:", "Flatpickr calendar picks travel dates easily while counters set Adult/Child travelers."]
            ]
        );

        // PAGE 4: FLEET
        $pagesData[] = $buildPage(
            4,
            "Step 3 -- Flight Fleet Selection & Ticket Fares (flight.php)",
            "http://localhost/airport/project/flight.php",
            $imgFleet,
            [
                ["Real Fleet Search Results:", "Queries flights.json and displays matching flight schedules across Indian metro hubs."],
                ["Airline Logos & Timings:", "Displays IndiGo, Air India, SpiceJet logos with departure time, arrival time, and duration."],
                ["Live Ticket Pricing:", "Shows clear ticket prices per passenger and dynamic route schedule generator."]
            ]
        );

        // PAGE 5: CHECKOUT
        $pagesData[] = $buildPage(
            5,
            "Step 4 -- Passenger Details & Checkout Form (detail.php)",
            "http://localhost/airport/project/detail.php",
            $imgCheckout,
            [
                ["Passenger Contact Form:", "Traveler fills First Name, Last Name, Email, and Mobile Phone number."],
                ["Total Fare Calculation:", "Calculates base ticket price plus taxes based on Economy, Business, or First Class."],
                ["Data Verification:", "Ensures no empty inputs and saves temporary booking state before payment."]
            ]
        );

        // PAGE 6: PAYMENT
        $pagesData[] = $buildPage(
            6,
            "Step 5 -- Payment Gateway & Simulated OTP Verification (payment.php)",
            "http://localhost/airport/project/payment.php",
            $imgPayment,
            [
                ["Payment Method Selection:", "Choose Credit/Debit Card, UPI Instant, or NetBanking payment option."],
                ["Live OTP Pop-up Modal:", "Interactive simulated OTP verification pop-up modal appears for payment approval."],
                ["6-Character PNR Generation:", "Entering OTP generates a unique 6-character PNR code (e.g. SKP982) and saves to bookings.json."]
            ]
        );

        // PAGE 7: CONFIRMATION
        $pagesData[] = $buildPage(
            7,
            "Step 6 -- Booking Confirmation & Digital Ticket Stub (confirmation.php)",
            "http://localhost/airport/project/confirmation.php",
            $imgConfirm,
            [
                ["Success Banner & Copy PNR:", "Green pulse icon shows booking is confirmed. 1-Click Copy PNR button copies code."],
                ["Pure PHP Vector PDF Ticket:", "Clicking 'Download PDF Ticket' streams a clean A4 PDF ticket created directly in PHP."],
                ["Automated Notifications:", "Sends formatted E-ticket HTML email and SMS confirmation message."]
            ]
        );

        // PAGE 8: WEB CHECK-IN
        $pagesData[] = $buildPage(
            8,
            "Step 7 -- Instant Web Check-In & Gate QR Pass (webcheckin.php)",
            "http://localhost/airport/project/webcheckin.php",
            $imgCheckin,
            [
                ["30-Second Web Check-In:", "Enter 6-character PNR code to retrieve ticket itinerary instantly."],
                ["Interactive Seat Selection:", "Passenger picks aircraft seat (e.g. Seat 12F) and meal preferences."],
                ["Digital Boarding Pass & QR Code:", "Generates live Boarding Pass with Gate A12 / Zone 2 QR Code for terminal scanners."]
            ]
        );

        // PAGE 9: MY TRIPS
        $pagesData[] = $buildPage(
            9,
            "Step 8 -- Traveler Dashboard & Admin Cancellation Sync (mybooking.php)",
            "http://localhost/airport/project/mybooking.php",
            $imgTrips,
            [
                ["Traveler Trips Dashboard:", "Automatically retrieves all user tickets via logged-in email or PNR search."],
                ["Admin Cancellation Sync:", "If Admin cancels a flight schedule in Admin Panel, ticket shows a bold red CANCELLED status badge."],
                ["Quick Action Pills:", "Enables downloading PDF tickets and viewing boarding passes directly from dashboard."]
            ]
        );

        // PAGE 10: ADMIN & EASY VIVA SCRIPT
        $pagesData[] = $buildPage(
            10,
            "Step 9 -- Super-Admin Control Panel & Easy Viva Speaking Guide (admin/index.php)",
            "http://localhost/airport/project/admin/index.php",
            $imgAdmin,
            [
                ["Q1: Database Used?", "Lightweight JSON Flat-File Database (flights.json, bookings.json) with file locks."],
                ["Q2: Special Feature?", "Dynamic City Hiding in search box so users cannot pick same origin & destination."],
                ["Q3: Admin Cancellation?", "Deleting a flight in Admin panel marks passenger booking as Cancelled in My Bookings."]
            ]
        );

        // =============================================================
        // ASSEMBLE 10-PAGE PDF CATALOG & XREF
        // =============================================================
        $pageTreeIds = [];
        foreach ($pagesData as $pData) {
            $cObj = $this->addObject("<< /Length " . strlen($pData['stream']) . " >>\nstream\n{$pData['stream']}\nendstream");
            
            $resXObj = "";
            if ($pData['img']) {
                $resXObj = " /XObject << /{$pData['img']['name']} {$pData['img']['id']} 0 R >>";
            }

            $pObj = $this->addObject("<< /Type /Page /Parent 0 0 R /MediaBox [0 0 595 842] /Contents {$cObj} 0 R /Resources << /Font << /F1 {$fontHelvetica} 0 R /F2 {$fontHelveticaBold} 0 R >>{$resXObj} >> >>");
            $pageTreeIds[] = $pObj;
        }

        $kidsStr = implode(" 0 R ", $pageTreeIds) . " 0 R";
        $pagesObj = $this->addObject("<< /Type /Pages /Kids [{$kidsStr}] /Count " . count($pageTreeIds) . " >>");
        $catalogObj = $this->addObject("<< /Type /Catalog /Pages {$pagesObj} 0 R >>");

        $xrefOffset = strlen($this->buffer);
        $count = count($this->offsets) + 1;
        $this->buffer .= "xref\n0 {$count}\n0000000000 65535 f \n";
        foreach ($this->offsets as $off) {
            $this->buffer .= sprintf("%010d 00000 n \n", $off);
        }

        $this->buffer .= "trailer\n<< /Size {$count} /Root {$catalogObj} 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";
        return $this->buffer;
    }
}

function output_documentation_pdf() {
    $pdf = new SkyPortDocsPDF();
    $pdfData = $pdf->generate();
    
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="SkyPort_Project_Documentation_10Pages.pdf"');
    header('Content-Length: ' . strlen($pdfData));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    echo $pdfData;
    exit();
}

if (basename($_SERVER['PHP_SELF']) === 'generate_docs_pdf.php') {
    output_documentation_pdf();
}
?>
