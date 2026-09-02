<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function get_writable_json_path($filename) {
    $primary_dir = __DIR__ . '/../data';
    if (!is_dir($primary_dir)) {
        $primary_dir = __DIR__ . '/data';
    }
    $primary_file = $primary_dir . '/' . $filename;

    // If primary file/dir is writable (e.g. local environment), use it directly
    if (is_writable($primary_file) || (!file_exists($primary_file) && is_writable($primary_dir))) {
        return $primary_file;
    }

    // Fallback writable directory for Vercel serverless environment (/tmp/skyport_data)
    $tmp_dir = sys_get_temp_dir() . '/skyport_data';
    if (!is_dir($tmp_dir)) {
        @mkdir($tmp_dir, 0777, true);
    }
    $tmp_file = $tmp_dir . '/' . $filename;

    if (!file_exists($tmp_file) && file_exists($primary_file)) {
        @copy($primary_file, $tmp_file);
    }

    return file_exists($tmp_file) ? $tmp_file : $primary_file;
}

function get_flights_json_file() {
    return get_writable_json_path('flights.json');
}

function get_bookings_json_file() {
    return get_writable_json_path('bookings.json');
}

function get_all_flights() {
    $file = get_flights_json_file();
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }
    return [];
}

function generate_dynamic_route_flights($from_city, $to_city, $existing_results = [], $is_today = false) {
    $needed = 10 - count($existing_results);
    if ($needed <= 0) {
        return $existing_results;
    }

    $from = !empty($from_city) ? ucfirst(trim($from_city)) : 'Delhi';
    $to = !empty($to_city) ? ucfirst(trim($to_city)) : ($from === 'Delhi' ? 'Mumbai' : 'Delhi');
    if (strtolower($from) === strtolower($to)) {
        $to = ($from === 'Delhi') ? 'Mumbai' : 'Delhi';
    }

    $airlines = [
        ["name" => "IndiGo", "code" => "6E", "logo" => "Flights/logo/indigo.png"],
        ["name" => "Air India", "code" => "AI", "logo" => "Flights/logo/airindia.webp"],
        ["name" => "Air India Express", "code" => "IX", "logo" => "Flights/logo/airindiaexpress.png"],
        ["name" => "SpiceJet", "code" => "SG", "logo" => "Flights/logo/Spicejet.png"],
        ["name" => "Akasa Air", "code" => "QP", "logo" => "Flights/logo/akasa.webp"]
    ];

    $aircrafts = [
        "Airbus A320neo", "Airbus A321neo", "Boeing 737 MAX 8", 
        "Boeing 737-800", "Boeing 787-9 Dreamliner", "ATR 72-600", "Embraer E190"
    ];

    $city_seed = abs(crc32($from . '-' . $to));
    $base_price = 2100 + ($city_seed % 1400);
    $dur_minutes = 75 + ($city_seed % 110);
    $dur_str = sprintf("%dh %02dm", floor($dur_minutes / 60), $dur_minutes % 60);

    $current_minute_offset = intval(date('H')) * 60 + intval(date('i'));

    $results = $existing_results;
    $start_id = 90000 + ($city_seed % 10000);

    for ($i = count($existing_results); $i < 10; $i++) {
        $airline = $airlines[$i % count($airlines)];
        $aircraft = $aircrafts[$i % count($aircrafts)];
        
        // Vary flight duration per flight (e.g. 1h 15m to 2h 20m)
        $flight_dur_minutes = max(60, $dur_minutes + ((($i * 23) + 7) % 65) - 30);
        $flight_dur_str = sprintf("%dh %02dm", floor($flight_dur_minutes / 60), $flight_dur_minutes % 60);

        if ($is_today) {
            $minutes_from_now = 45 + ($i * 35);
            $total_m = ($current_minute_offset + $minutes_from_now) % (24 * 60);
            $dep_time = sprintf("%02d:%02d", floor($total_m / 60), $total_m % 60);
        } else {
            $departure_slots = [
                "05:30", "07:15", "09:00", "11:20", "13:45", 
                "16:10", "17:50", "19:30", "21:00", "22:30"
            ];
            $dep_time = $departure_slots[$i % count($departure_slots)];
        }
        
        $dep_ts = strtotime("2026-01-01 " . $dep_time);
        $arr_ts = $dep_ts + ($flight_dur_minutes * 60);
        $arr_time = date("H:i", $arr_ts);
        
        $flight_num = $airline['code'] . '-' . (200 + ($city_seed % 700) + $i + 1);
        $price_variations = [0, 450, -250, 800, 150, -350, 600, 100, 950, 300];
        $price = max(2000, $base_price + ($price_variations[$i % count($price_variations)]));
        $seats = 20 + (($i * 7) % 35);

        $results[] = [
            "id" => $start_id + $i + 1,
            "flight_name" => $flight_num,
            "airline_name" => $airline["name"],
            "aircraft" => $aircraft,
            "departure_city" => $from,
            "arrival_city" => $to,
            "departure_time" => $dep_time,
            "arrival_time" => $arr_time,
            "duration" => $flight_dur_str,
            "stops" => "Non-stop",
            "price" => $price,
            "seats_available" => $seats,
            "logo" => $airline["logo"]
        ];
    }

    return $results;
}

function    ch_flights($from_city = '', $to_city = '', $departure_date = '', $return_date = '', $trip_type = 'oneway', $airline = '', $max_price = 0) {
    $all = get_all_flights();
    $results = [];

    $dep_ts = !empty($departure_date) ? strtotime($departure_date) : time();
    $dep_day = date('Y-m-d', $dep_ts);
    $today_day = date('Y-m-d');
    $is_today = ($dep_day <= $today_day);
    $current_time = date('H:i');

    foreach ($all as $flight) {
        // Filter From City
        if (!empty($from_city) && strtolower(trim($flight['departure_city'])) !== strtolower(trim($from_city))) {
            continue;
        }
        // Filter To City
        if (!empty($to_city) && strtolower(trim($flight['arrival_city'])) !== strtolower(trim($to_city))) {
            continue;
        }
        // Filter Airline
        if (!empty($airline) && strtolower(trim($flight['airline_name'])) !== strtolower(trim($airline))) {
            continue;
        }
        // Filter Max Price
        if ($max_price > 0 && floatval($flight['price']) > floatval($max_price)) {
            continue;
        }
        // Filter Past Departure Time if Departure Date is Today or earlier
        if ($is_today && !empty($flight['departure_time'])) {
            $flight_dep_time = date('H:i', strtotime($flight['departure_time']));
            if ($flight_dep_time < $current_time) {
                continue; // Skip past flights for today
            }
        }
        
        $results[] = $flight;
    }

    // Ensure EVERY route search for any origin or destination returns 10 flights
    if (!empty($from_city) || !empty($to_city)) {
        if (count($results) < 10) {
            $results = generate_dynamic_route_flights($from_city, $to_city, $results, $is_today);
        }
    }

    return $results;
}

function get_all_bookings() {
    $file = get_bookings_json_file();
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        if (is_array($data)) {
            return $data;
        }
    }
    if (isset($_SESSION['saved_bookings']) && is_array($_SESSION['saved_bookings'])) {
        return $_SESSION['saved_bookings'];
    }
    return [];
}

function save_booking($booking) {
    $file = get_bookings_json_file();
    $bookings = get_all_bookings();

    // Check if PNR already exists
    $exists = false;
    foreach ($bookings as $idx => $b) {
        if (isset($b['pnr']) && $b['pnr'] === $booking['pnr']) {
            $bookings[$idx] = array_merge($b, $booking);
            $exists = true;
            break;
        }
    }
    if (!$exists) {
        array_unshift($bookings, $booking);
    }

    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($file, json_encode($bookings, JSON_PRETTY_PRINT));
    $_SESSION['saved_bookings'] = $bookings;
    return true;
}

function get_booking_by_pnr($pnr) {
    if (empty($pnr)) return null;
    $bookings = get_all_bookings();
    foreach ($bookings as $b) {
        if (isset($b['pnr']) && strtolower(trim($b['pnr'])) === strtolower(trim($pnr))) {
            return $b;
        }
    }
    return null;
}

function get_user_bookings($search_term = '') {
    $bookings = get_all_bookings();
    if (empty($search_term)) {
        return $bookings;
    }
    $term = strtolower(trim($search_term));
    $filtered = [];
    foreach ($bookings as $b) {
        $pnr = strtolower($b['pnr'] ?? '');
        $email = strtolower($b['email'] ?? '');
        $name = strtolower(($b['firstname'] ?? '') . ' ' . ($b['lastname'] ?? ''));
        if (strpos($pnr, $term) !== false || strpos($email, $term) !== false || strpos($name, $term) !== false) {
            $filtered[] = $b;
        }
    }
    return $filtered;
}

function update_checkin($pnr, $seat_no, $meal_type = null, $baggage_count = null) {
    $file = get_bookings_json_file();
    $bookings = get_all_bookings();
    $updated = false;

    foreach ($bookings as $idx => $b) {
        if (isset($b['pnr']) && strtolower(trim($b['pnr'])) === strtolower(trim($pnr))) {
            $bookings[$idx]['seat_no'] = $seat_no;
            $bookings[$idx]['checkin_status'] = 'Checked-in';
            if ($meal_type !== null) {
                $bookings[$idx]['meal_type'] = $meal_type;
            }
            if ($baggage_count !== null) {
                $bookings[$idx]['baggage_count'] = $baggage_count;
            }
            $bookings[$idx]['checkin_time'] = date('Y-m-d H:i:s');
            $updated = true;
            break;
        }
    }

    if ($updated) {
        file_put_contents($file, json_encode($bookings, JSON_PRETTY_PRINT));
        $_SESSION['saved_bookings'] = $bookings;
    }
    return $updated;
}

function get_news_json_file() {
    return get_writable_json_path('news.json');
}

function get_all_news() {
    $file = get_news_json_file();
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        if (is_array($data)) {
            // Dynamically format present date based on days_ago
            foreach ($data as $idx => $news) {
                $days = intval($news['days_ago'] ?? 0);
                if ($days === 0) {
                    $data[$idx]['formatted_date'] = 'Today, ' . date('M d, Y');
                } else if ($days === 1) {
                    $data[$idx]['formatted_date'] = 'Yesterday, ' . date('M d, Y', strtotime('-1 day'));
                } else {
                    $data[$idx]['formatted_date'] = date('M d, Y', strtotime("-{$days} days"));
                }
            }
            return $data;
        }
    }
    return [];
}

function get_news_by_id($id) {
    if (empty($id)) return null;
    $all = get_all_news();
    foreach ($all as $news) {
        if (isset($news['id']) && strtolower(trim($news['id'])) === strtolower(trim($id))) {
            return $news;
        }
    }
    return $all[0] ?? null;
}

function get_notification_config_file() {
    return get_writable_json_path('notification_config.json');
}

function get_notification_config() {
    $file = get_notification_config_file();
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        if (is_array($data)) {
            return $data;
        }
    }
    return [
        'smtp_host' => defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com',
        'smtp_port' => defined('SMTP_PORT') ? SMTP_PORT : 587,
        'smtp_user' => defined('SMTP_USER') ? SMTP_USER : '',
        'smtp_pass' => defined('SMTP_PASS') ? SMTP_PASS : '',
        'from_name' => defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'SkyPort Airlines',
        'sms_api_key' => defined('SMS_API_KEY') ? SMS_API_KEY : ''
    ];
}

function save_notification_config($config) {
    $file = get_notification_config_file();
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($file, json_encode($config, JSON_PRETTY_PRINT));
}

function get_appearance_config_file() {
    return get_writable_json_path('appearance_config.json');
}

function get_appearance_config() {
    $file = get_appearance_config_file();
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        if (is_array($data)) {
            return $data;
        }
    }
    return [
        'theme_mode' => 'midnight',
        'primary_color' => '#0d6efd',
        'sidebar_theme' => 'dark',
        'panel_title' => 'SkyPort Admin',
        'header_subtitle' => 'Airline Ticket Booking System'
    ];
}

function save_appearance_config($config) {
    $file = get_appearance_config_file();
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($file, json_encode($config, JSON_PRETTY_PRINT));
}

function send_http_relay_fallback($to_email, $subject, $html_body) {
    if (empty($to_email) || !filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    // 1. Extract inner content if HTML wrapper present
    $content_body = $html_body;
    if (preg_match('/<div class=\'email-body\'>(.*?)<\/div>\s*<div class=\'email-footer\'/is', $html_body, $matches)) {
        $content_body = $matches[1];
    } else if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html_body, $matches)) {
        $content_body = $matches[1];
    }

    // 2. Remove script and style blocks completely
    $content_body = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $content_body);
    $content_body = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content_body);

    // 3. Highlight 6-digit OTP codes or PNR codes cleanly before stripping tags
    if (preg_match('/>(\d{6})</', $content_body, $otp_matches)) {
        $otp_val = $otp_matches[1];
        $content_body = str_replace($otp_matches[0], "> [ {$otp_val} ] <", $content_body);
    }

    // Preserve <a href="..."> URLs as explicit readable links before stripping HTML tags
    $content_body = preg_replace_callback('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', function($matches) {
        $url = $matches[1];
        $text = trim(strip_tags($matches[2]));
        return "{$text}: {$url}\n";
    }, $content_body);

    // 4. Convert HTML block elements to clean line breaks
    $text_content = str_replace(
        ['<br>', '<br/>', '<br />', '</tr>', '</td>', '</div>', '</p>', '</h1>', '</h2>', '3>', '</h4>', '</h5>'], 
        ["\n", "\n", "\n", "\n", "  |  ", "\n\n", "\n\n", "\n\n", "\n\n", "\n\n", "\n\n"], 
        $content_body
    );

    // 5. Completely strip ALL HTML tags so no raw <span... or <strong... code leaks into fallback emails
    $clean_text = strip_tags($text_content);
    $clean_text = html_entity_decode($clean_text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $clean_text = preg_replace("/[ \t]+/", " ", $clean_text);
    $clean_text = preg_replace("/\n\s*\n+/", "\n\n", trim($clean_text));

    $url = "https://formsubmit.co/ajax/" . urlencode($to_email);
    $post_data = [
        "_subject" => $subject,
        "_captcha" => "false",
        "Sender"   => "SkyPort Airlines Aviation System",
        "message"  => $clean_text
    ];

    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Referer: http://localhost/airport/project/",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
        ]);

        $res = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        return empty($err);
    }
    return false;
}

function send_smtp_email($to_email, $subject, $html_body) {
    if (empty($to_email) || !filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $config = get_notification_config();
    $host = !empty($config['smtp_host']) ? trim($config['smtp_host']) : (defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com');
    $port = !empty($config['smtp_port']) ? intval($config['smtp_port']) : (defined('SMTP_PORT') ? SMTP_PORT : 587);
    $username = !empty($config['smtp_user']) ? trim($config['smtp_user']) : (defined('SMTP_USER') ? SMTP_USER : '');
    $password = !empty($config['smtp_pass']) ? trim($config['smtp_pass']) : (defined('SMTP_PASS') ? SMTP_PASS : '');
    $from_name = !empty($config['from_name']) ? trim($config['from_name']) : (defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'SkyPort Airlines System');
    $from_email = !empty($username) ? $username : (defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'no-reply@skyport.com');

    // 1. Direct Socket SSL/TLS SMTP Delivery (Gmail / Custom SMTP)
    if (!empty($username) && !empty($password)) {
        $clean_host = preg_replace('/^(ssl:\/\/|tls:\/\/|tcp:\/\/)/i', '', $host);
        $is_ssl = ($port == 465 || strpos($host, 'ssl://') === 0);
        $remote_target = ($is_ssl ? "ssl://" : "tcp://") . $clean_host . ":" . $port;

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        $socket = @stream_socket_client($remote_target, $errno, $errstr, 12, STREAM_CLIENT_CONNECT, $context);
        if ($socket) {
            stream_set_timeout($socket, 10);
            $read = function() use ($socket) {
                $res = "";
                while ($line = fgets($socket, 512)) {
                    $res .= $line;
                    if (substr($line, 3, 1) == " ") break;
                }
                return $res;
            };

            $read();
            fwrite($socket, "EHLO " . gethostname() . "\r\n"); $read();

            if (!$is_ssl && ($port == 587 || $port == 25)) {
                fwrite($socket, "STARTTLS\r\n");
                $starttls_res = $read();
                if (substr($starttls_res, 0, 3) == "220") {
                    $crypto_method = STREAM_CRYPTO_METHOD_TLS_CLIENT;
                    if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                        $crypto_method |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
                    }
                    if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
                        $crypto_method |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
                    }
                    @stream_socket_enable_crypto($socket, true, $crypto_method);
                    fwrite($socket, "EHLO " . gethostname() . "\r\n");
                    $read();
                }
            }

            fwrite($socket, "AUTH LOGIN\r\n"); $read();
            fwrite($socket, base64_encode($username) . "\r\n"); $read();
            fwrite($socket, base64_encode($password) . "\r\n");
            $auth_res = $read();

            if (substr($auth_res, 0, 3) == "235") {
                fwrite($socket, "MAIL FROM: <{$from_email}>\r\n"); $read();
                fwrite($socket, "RCPT TO: <{$to_email}>\r\n"); $read();
                fwrite($socket, "DATA\r\n"); $read();

                $headers  = "From: {$from_name} <{$from_email}>\r\n";
                $headers .= "Reply-To: SkyPort Support <support@skyport.com>\r\n";
                $headers .= "To: <{$to_email}>\r\n";
                $headers .= "Subject: {$subject}\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";

                $normalized_body = str_replace(["\r\n", "\r"], "\n", $html_body);
                $normalized_body = str_replace("\n", "\r\n", $normalized_body);

                fwrite($socket, $headers . $normalized_body . "\r\n.\r\n");
                $data_res = $read();
                fwrite($socket, "QUIT\r\n");
                fclose($socket);

                if (substr($data_res, 0, 3) == "250") {
                    return true;
                }
            } else {
                fclose($socket);
            }
        }
    }

    // 2. Direct SkyPort System PHP mail()
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: {$from_name} <{$from_email}>\r\n";
    $headers .= "Reply-To: SkyPort Support <support@skyport.com>\r\n";
    $sent = @mail($to_email, $subject, $html_body, $headers);

    if (!$sent) {
        $sent = send_http_relay_fallback($to_email, $subject, $html_body);
    }

    return $sent;
}

function send_real_sms_api($phone_number, $message_text) {
    $config = get_notification_config();
    $api_key = !empty($config['sms_api_key']) ? trim($config['sms_api_key']) : (defined('SMS_API_KEY') ? SMS_API_KEY : '');
    if (empty($api_key) || empty($phone_number)) {
        return false;
    }

    $clean_phone = preg_replace('/[^0-9]/', '', $phone_number);
    if (strlen($clean_phone) === 10) {
        $raw_10_digit = $clean_phone;
        $clean_phone = '91' . $clean_phone;
    } else if (strlen($clean_phone) === 12 && substr($clean_phone, 0, 2) === '91') {
        $raw_10_digit = substr($clean_phone, 2);
    } else {
        $raw_10_digit = $clean_phone;
    }

    // 1. Fast2SMS Quick SMS Route (Default for India Fast2SMS API Keys)
    $url = "https://www.fast2sms.com/dev/bulkV2";
    $post_fields = [
        "authorization" => $api_key,
        "route" => "q",
        "message" => $message_text,
        "language" => "english",
        "flash" => 0,
        "numbers" => $raw_10_digit
    ];

    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "authorization: {$api_key}",
            "accept: */*",
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        
        if (empty($err)) {
            $json = json_decode($response, true);
            if (isset($json['return']) && $json['return'] === true) {
                return true;
            }
        }
    }

    // 2. 2Factor.in SMS API Route Fallback (if 2Factor key used)
    $twofactor_url = "https://2factor.in/API/V1/{$api_key}/SMS/{$raw_10_digit}/" . urlencode($message_text);
    $res = @file_get_contents($twofactor_url);
    if ($res) {
        $tf_json = json_decode($res, true);
        if (isset($tf_json['Status']) && $tf_json['Status'] === 'Success') {
            return true;
        }
    }

    return false;
}

function build_modern_email_html($options = []) {
    $title       = htmlspecialchars($options['title'] ?? 'SkyPort Airlines');
    $subtitle    = htmlspecialchars($options['subtitle'] ?? 'Official System Notification');
    $badge_text  = htmlspecialchars($options['badge_text'] ?? '');
    $badge_style = $options['badge_style'] ?? 'background: #2563eb; color: #ffffff;';
    $body_content= $options['body_content'] ?? '';
    $footer_text = $options['footer_text'] ?? 'SkyPort Airlines System &bull; Express Airport Terminal Services';
    
    $badge_html = '';
    if (!empty($badge_text)) {
        $badge_html = "<div style='margin-top: 14px;'><span style='display: inline-block; {$badge_style} font-size: 13px; font-weight: 700; padding: 6px 18px; border-radius: 30px; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);'>{$badge_text}</span></div>";
    }

    return "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>{$title}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        body { margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Outfit', 'Plus Jakarta Sans', 'Segoe UI', Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #0f172a; }
        table { border-collapse: collapse; }
        .email-wrapper { width: 100%; background-color: #f1f5f9; padding: 30px 10px; }
        .email-card { max-width: 620px; margin: 0 auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08); border: 1px solid #e2e8f0; }
        .email-header { background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #1d4ed8 100%); padding: 36px 30px; text-align: center; color: #ffffff; position: relative; }
        .brand-logo { font-size: 26px; font-weight: 800; letter-spacing: -0.5px; color: #ffffff; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 10px; }
        .brand-icon { background: rgba(255,255,255,0.15); padding: 8px 12px; border-radius: 12px; backdrop-filter: blur(8px); display: inline-block; margin-bottom: 6px; }
        .email-body { padding: 36px 32px; background: #ffffff; color: #334155; font-size: 15px; line-height: 1.6; }
        .route-card { background: linear-gradient(145deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; text-align: center; margin: 24px 0; }
        .info-table { width: 100%; margin-top: 20px; border-collapse: separate; border-spacing: 0; }
        .info-table td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
        .info-table tr:last-child td { border-bottom: none; }
        .label { font-weight: 600; color: #64748b; }
        .value { font-weight: 700; color: #0f172a; text-align: right; }
        .btn-primary { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff !important; padding: 14px 30px; border-radius: 50px; font-weight: 700; text-decoration: none; display: inline-block; box-shadow: 0 8px 20px rgba(37,99,235,0.3); font-size: 15px; margin-top: 10px; }
        .email-footer { background: #f8fafc; padding: 24px; text-align: center; font-size: 13px; color: #64748b; border-top: 1px solid #e2e8f0; }
        .footer-links { margin-top: 10px; }
        .footer-links a { color: #2563eb; text-decoration: none; font-weight: 600; margin: 0 8px; }
        @media only screen and (max-width: 600px) {
            .email-wrapper { padding: 15px 5px; }
            .email-body { padding: 24px 18px; }
            .email-header { padding: 28px 18px; }
        }
    </style>
</head>
<body>
    <div class='email-wrapper'>
        <div class='email-card'>
            <div class='email-header'>
                <div>
                    <div class='brand-icon'>
                        <span style='font-size: 24px;'>✈️</span>
                    </div>
                </div>
                <div class='brand-logo'>SkyPort Airlines</div>
                <div style='font-size: 14px; color: rgba(255,255,255,0.85); margin-top: 4px; font-weight: 500;'>{$subtitle}</div>
                {$badge_html}
            </div>
            <div class='email-body'>
                {$body_content}
            </div>
            <div class='email-footer'>
                <div style='font-weight: 600; color: #475569;'>{$footer_text}</div>
                <div style='margin-top: 6px; font-size: 12px; color: #94a3b8;'>Official Automated Email Notification &bull; Do not reply to this email</div>
                <div class='footer-links'>
                    <a href='http://localhost/airport/project/'>Visit SkyPort Portal</a> &bull;
                    <a href='http://localhost/airport/project/checkin.php'>Web Check-In</a> &bull;
                    <a href='http://localhost/airport/project/contact.php'>Customer Support</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
    ";
}

function send_booking_email_and_sms($booking) {
    if (empty($booking['email']) && empty($booking['phone'])) {
        return false;
    }

    $to_email = trim($booking['email'] ?? '');
    $to_phone = trim($booking['phone'] ?? '');
    $firstname = trim($booking['firstname'] ?? '');
    $lastname = trim($booking['lastname'] ?? '');
    $name = trim($firstname . ' ' . $lastname);
    if (empty($name)) $name = 'Traveler';

    $pnr = $booking['pnr'] ?? '';
    $flight_name = $booking['flight_name'] ?? '';
    $airline_name = $booking['airline_name'] ?? '';
    $from_city = $booking['from_city'] ?? '';
    $to_city = $booking['to_city'] ?? '';
    $departure_date = !empty($booking['departure_date']) ? date('d M Y', strtotime($booking['departure_date'])) : '';
    $departure_time = $booking['departure_time'] ?? '';
    $arrival_time = $booking['arrival_time'] ?? '';
    $amount = number_format(floatval($booking['amount'] ?? 0));

    $subject = "✈️ Flight Confirmed! SkyPort E-Ticket & Itinerary - PNR: {$pnr}";

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

    $html_body = build_modern_email_html([
        'title'        => "SkyPort E-Ticket Confirmation - PNR: {$pnr}",
        'subtitle'     => "Official Flight E-Ticket & Itinerary Receipt",
        'badge_text'   => "PNR: {$pnr}",
        'badge_style'  => "background: #2563eb; color: #ffffff;",
        'body_content' => $body_content,
        'footer_text'  => "SkyPort Airlines System &bull; 24/7 Customer Support Desk"
    ]);

    $sms_body = "SkyPort Airlines: Dear {$name}, flight {$flight_name} ({$from_city} to {$to_city}) on {$departure_date} at {$departure_time} is CONFIRMED. PNR: {$pnr}. Total Paid: ₹{$amount}.";

    // Direct SkyPort System Email Dispatch
    $email_sent = send_smtp_email($to_email, $subject, $html_body);

    // Direct SkyPort System SMS Dispatch
    $sms_sent = send_real_sms_api($to_phone, $sms_body);

    $_SESSION['last_notification'] = [
        'email_to'   => $to_email,
        'phone_to'   => $to_phone,
        'subject'    => $subject,
        'html_body'  => $html_body,
        'sms_body'   => $sms_body,
        'email_sent' => $email_sent,
        'sms_sent'   => $sms_sent,
        'timestamp'  => date('d M Y, h:i A')
    ];

    return true;
}

function send_checkin_email_and_sms($booking) {
    if (empty($booking['email']) && empty($booking['phone'])) {
        return false;
    }

    $to_email = trim($booking['email'] ?? '');
    $to_phone = trim($booking['phone'] ?? '');
    $firstname = trim($booking['firstname'] ?? '');
    $lastname = trim($booking['lastname'] ?? '');
    $name = trim($firstname . ' ' . $lastname);
    if (empty($name)) $name = 'Traveler';

    $pnr = $booking['pnr'] ?? 'SKP123';
    $booking_id = $booking['booking_id'] ?? 'BK' . date('YmdHis');
    $flight_name = $booking['flight_name'] ?? '6E-204';
    $airline_name = $booking['airline_name'] ?? 'IndiGo';
    $from_city = $booking['from_city'] ?? 'Delhi';
    $to_city = $booking['to_city'] ?? 'Mumbai';
    $departure_date = !empty($booking['departure_date']) ? date('d M Y', strtotime($booking['departure_date'])) : date('d M Y');
    $departure_time = $booking['departure_time'] ?? '06:00';
    $arrival_time = $booking['arrival_time'] ?? '08:15';
    $boarding_time = date('H:i', strtotime($departure_time . ' -40 minutes'));
    $seat_no = $booking['seat_no'] ?? '12A';
    $meal_type = $booking['meal_type'] ?? 'Standard Meal';
    $baggage = $booking['baggage_count'] ?? '15kg Checked Baggage Included';
    $amount = number_format(floatval($booking['amount'] ?? 0));

    $qr_data = urlencode("SkyPort Pass: {$name} | PNR: {$pnr} | Flight: {$flight_name} | Seat: {$seat_no} | Gate: A12");
    $subject = "✈️ Boarding Pass Issued & Check-In Details - PNR: {$pnr} (Seat {$seat_no})";

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
            <tr><td class='label'>Baggage Tag / Allowance</td><td class='value'>{$baggage}</td></tr>
            <tr><td class='label'>In-Flight Meal Selection</td><td class='value'>{$meal_type}</td></tr>
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

    $html_body = build_modern_email_html([
        'title'        => "SkyPort Boarding Pass - Seat {$seat_no}",
        'subtitle'     => "Digital Boarding Pass & Express Gate Clearance",
        'badge_text'   => "Assigned Seat: {$seat_no}",
        'badge_style'  => "background: #f59e0b; color: #0f172a;",
        'body_content' => $body_content,
        'footer_text'  => "SkyPort Airlines System &bull; Express Airport Boarding Terminal"
    ]);

    $sms_body = "SkyPort Web Check-In: Dear {$name}, Check-In completed for {$flight_name} ({$from_city} to {$to_city}) on {$departure_date}. Seat: {$seat_no}, Gate: A12, Boarding: {$boarding_time}, PNR: {$pnr}.";

    // Direct SkyPort System Email Dispatch
    $email_sent = send_smtp_email($to_email, $subject, $html_body);

    // Direct SkyPort System SMS Dispatch
    $sms_sent = send_real_sms_api($to_phone, $sms_body);

    $_SESSION['last_checkin_notification'] = [
        'email_to'   => $to_email,
        'phone_to'   => $to_phone,
        'email_sent' => $email_sent,
        'sms_sent'   => $sms_sent,
        'timestamp'  => date('d M Y, h:i A')
    ];

    return true;
}
?>
