<?php
// Pure PHP Vector PDF Generator for SkyPort E-Tickets & Boarding Passes

class SkyPortPDF {
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

    private function pdfEscape($str) {
        $str = str_replace('\\', '\\\\', (string)$str);
        $str = str_replace('(', '\\(', $str);
        $str = str_replace(')', '\\)', $str);
        return preg_replace('/[^\x20-\x7E]/', ' ', $str);
    }

    public function generate($booking) {
        $pnr = $booking['pnr'] ?? 'SKP123';
        $booking_id = $booking['booking_id'] ?? ('BK' . date('YmdHis'));
        $name = trim(($booking['firstname'] ?? 'Passenger') . ' ' . ($booking['lastname'] ?? ''));
        if (empty($name)) $name = 'Traveler';
        $email = $booking['email'] ?? 'N/A';
        $phone = $booking['phone'] ?? 'N/A';
        $airline = $booking['airline_name'] ?? 'IndiGo';
        $flight_num = $booking['flight_name'] ?? '6E-201';
        $flight = "{$airline} ({$flight_num})";
        $from = $booking['from_city'] ?? 'Delhi';
        $to = $booking['to_city'] ?? 'Mumbai';
        $date = !empty($booking['departure_date']) ? date('d M Y', strtotime($booking['departure_date'])) : date('d M Y');
        $depTime = $booking['departure_time'] ?? '06:00';
        $arrTime = $booking['arrival_time'] ?? '08:15';
        $seat = $booking['seat_no'] ?? 'Unassigned';
        $meal = $booking['meal_type'] ?? 'Standard Meal';
        $baggage = $booking['baggage_count'] ?? '15kg Included';
        $amount = 'INR ' . number_format(floatval($booking['amount'] ?? 0));
        
        $is_cancelled = (strcasecmp($booking['checkin_status'] ?? '', 'Cancelled') === 0 || strcasecmp($booking['payment_status'] ?? '', 'Cancelled') === 0);
        $status = $is_cancelled ? 'CANCELLED' : (($booking['checkin_status'] ?? 'Not Checked-in') === 'Checked-in' ? 'Checked-In & Boarding Pass Issued' : 'Confirmed / Paid');

        // Font Objects
        $fontHelvetica = $this->addObject("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>");
        $fontHelveticaBold = $this->addObject("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>");

        $stream = "";

        // TOP HEADER BANNER (Navy Blue #0B192C)
        $stream .= "0.04 0.10 0.22 rg 0 770 595 72 re f\n"; // Background fill
        // Header Text - WHITE (1 1 1 rg)
        $stream .= "1 1 1 rg BT /F2 20 Tf 35 815 Td (" . $this->pdfEscape("SkyPort Airlines -- Official E-Ticket") . ") Tj ET\n";
        $stream .= "0.8 0.9 1 rg BT /F1 10 Tf 35 792 Td (" . $this->pdfEscape("Electronic Flight Reservation & Passenger Itinerary Pass") . ") Tj ET\n";

        // PNR & REFERENCE BOX (Y: 690 to 750)
        // Background tint: Light Slate Tint (0.94 0.96 0.98 rg)
        $stream .= "0.94 0.96 0.98 rg 35 690 525 60 re f\n";
        // Border: Border Gray (0.80 0.85 0.92 RG)
        $stream .= "0.80 0.85 0.92 RG 1 w 35 690 525 60 re s\n";

        // CRITICAL FIX: ALWAYS SET TEXT FILL COLOR TO DARK COLOR BEFORE RENDERING TEXT!
        // PNR Code - Dark Royal Blue (0.05 0.35 0.85 rg)
        $stream .= "0.05 0.35 0.85 rg BT /F2 15 Tf 50 730 Td (" . $this->pdfEscape("PNR REFERENCE: {$pnr}") . ") Tj ET\n";
        // Ref ID - Slate Gray (0.25 0.25 0.30 rg)
        $stream .= "0.25 0.25 0.30 rg BT /F1 10 Tf 50 708 Td (" . $this->pdfEscape("Booking Ref: {$booking_id}") . ") Tj ET\n";

        // Seat & Status - Right Side
        $stream .= "0.85 0.40 0.00 rg BT /F2 13 Tf 380 730 Td (" . $this->pdfEscape("SEAT: {$seat}") . ") Tj ET\n";
        if ($is_cancelled) {
            $stream .= "0.85 0.10 0.10 rg BT /F2 11 Tf 380 708 Td (" . $this->pdfEscape("Status: {$status}") . ") Tj ET\n";
        } else {
            $stream .= "0.05 0.55 0.20 rg BT /F2 10 Tf 380 708 Td (" . $this->pdfEscape("Status: {$status}") . ") Tj ET\n";
        }

        // FLIGHT ITINERARY SECTION (Y: 570 to 670)
        $stream .= "0.04 0.10 0.22 rg BT /F2 13 Tf 35 660 Td (" . $this->pdfEscape("FLIGHT ITINERARY") . ") Tj ET\n";
        $stream .= "0.05 0.35 0.85 rg BT /F2 12 Tf 35 640 Td (" . $this->pdfEscape("{$flight}") . ") Tj ET\n";

        // Route: Origin -> Destination - Solid Black (0 0 0 rg)
        $stream .= "0 0 0 rg BT /F2 18 Tf 35 615 Td (" . $this->pdfEscape("{$from}   --->   {$to}") . ") Tj ET\n";
        // Date & Times - Dark Charcoal (0.20 0.20 0.20 rg)
        $stream .= "0.20 0.20 0.20 rg BT /F1 11 Tf 35 595 Td (" . $this->pdfEscape("Date: {$date}   |   Departure: {$depTime}   |   Arrival: {$arrTime}") . ") Tj ET\n";

        // Horizontal Divider Line
        $stream .= "0.80 0.85 0.92 RG 1 w 35 575 525 0.5 re s\n";

        // PASSENGER & TICKET DETAILS (Y: 330 to 550)
        $stream .= "0.04 0.10 0.22 rg BT /F2 13 Tf 35 548 Td (" . $this->pdfEscape("PASSENGER & BOOKING DETAILS") . ") Tj ET\n";

        $details = [
            ["Passenger Name:", $name],
            ["Registered Email:", $email],
            ["Mobile Phone:", $phone],
            ["Assigned Seat:", "Seat {$seat}"],
            ["Terminal & Gate:", "Terminal 2  |  Gate A12 (Zone 2)"],
            ["Baggage Allowance:", $baggage],
            ["In-Flight Meal:", $meal],
            ["Total Amount Paid:", $amount]
        ];

        $y = 520;
        foreach ($details as $idx => $row) {
            // Alternating light row tint for clean visual alignment
            if ($idx % 2 === 0) {
                $stream .= "0.96 0.97 0.99 rg 35 " . ($y - 5) . " 525 20 re f\n";
            }
            // Label: Dark Slate (0.30 0.35 0.40 rg)
            $stream .= "0.30 0.35 0.40 rg BT /F2 10 Tf 45 {$y} Td (" . $this->pdfEscape($row[0]) . ") Tj ET\n";
            // Value: Crisp Black (0.05 0.05 0.05 rg)
            $stream .= "0.05 0.05 0.05 rg BT /F1 10 Tf 220 {$y} Td (" . $this->pdfEscape($row[1]) . ") Tj ET\n";
            $y -= 22;
        }

        // IMPORTANT INSTRUCTIONS BOX (Y: 250 to 310)
        $stream .= "0.94 0.96 0.98 rg 35 250 525 55 re f\n";
        $stream .= "0.80 0.85 0.92 RG 1 w 35 250 525 55 re s\n";

        $stream .= "0.04 0.10 0.22 rg BT /F2 10 Tf 50 288 Td (" . $this->pdfEscape("IMPORTANT AIRPORT & SECURITY INSTRUCTIONS:") . ") Tj ET\n";
        $stream .= "0.20 0.20 0.20 rg BT /F1 9 Tf 50 272 Td (" . $this->pdfEscape("1. Please present this official PDF ticket along with a valid government photo ID at security.") . ") Tj ET\n";
        $stream .= "0.20 0.20 0.20 rg BT /F1 9 Tf 50 258 Td (" . $this->pdfEscape("2. Web check-in closes 60 minutes prior to scheduled departure. Gate closes 25 mins prior.") . ") Tj ET\n";

        // FOOTER (Y: 200)
        $stream .= "0.04 0.10 0.22 rg 35 230 525 2 re f\n";
        $stream .= "0.40 0.40 0.40 rg BT /F1 9 Tf 145 210 Td (" . $this->pdfEscape("SkyPort Aviation System -- Official Digital E-Ticket Document") . ") Tj ET\n";

        // PDF Structure Output
        $streamLen = strlen($stream);
        $contentsObj = $this->addObject("<< /Length {$streamLen} >>\nstream\n{$stream}\nendstream");
        $pageObj = $this->addObject("<< /Type /Page /Parent 4 0 R /MediaBox [0 0 595 842] /Contents {$contentsObj} 0 R /Resources << /Font << /F1 {$fontHelvetica} 0 R /F2 {$fontHelveticaBold} 0 R >> >> >>");
        $pagesObj = $this->addObject("<< /Type /Pages /Kids [{$pageObj} 0 R] /Count 1 >>");
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

function output_ticket_pdf($booking) {
    $pdf = new SkyPortPDF();
    $pdfData = $pdf->generate($booking);
    $pnr = $booking['pnr'] ?? 'SKP123';
    
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="SkyPort_Ticket_' . $pnr . '.pdf"');
    header('Content-Length: ' . strlen($pdfData));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    echo $pdfData;
    exit();
}
?>
