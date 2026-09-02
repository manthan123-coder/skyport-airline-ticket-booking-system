<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once 'config.php';
require_once 'include/db_json.php';

$phone = $_POST['phone'] ?? $_SESSION['number'] ?? $_SESSION['phone'] ?? '';
$email = $_POST['email'] ?? $_SESSION['email'] ?? '';
$otp   = $_POST['otp'] ?? '';

if (empty($otp)) {
    $otp = rand(100000, 999999);
}

if (empty($phone)) {
    $phone = '+91 7202097453';
}

$sms_message = "SkyPort Airlines 2FA: Your OTP for flight ticket payment verification is {$otp}. Valid for 5 minutes. Do not share with anyone.";
$email_subject = "🔒 SkyPort 2FA Payment Verification OTP: {$otp}";

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

$email_html = build_modern_email_html([
    'title'        => "SkyPort 2FA Payment Verification - OTP: {$otp}",
    'subtitle'     => "2FA Payment Authorization & Security Gateway",
    'badge_text'   => "SECURITY OTP: {$otp}",
    'badge_style'  => "background: #10b981; color: #ffffff;",
    'body_content' => $otp_body_content,
    'footer_text'  => "SkyPort Payment Security Gateway &bull; 256-Bit SSL Encrypted"
]);

// 1. Send Email OTP (using configured SMTP manthangondaliya123@gmail.com)
$email_sent = false;
if (!empty($email)) {
    $email_sent = send_smtp_email($email, $email_subject, $email_html);
}

// 2. Send Real SMS via SMS Gateway
$sms_sent = false;
if (!empty($phone)) {
    $sms_sent = send_real_sms_api($phone, $sms_message);
}

$config = get_notification_config();
$has_sms_key = !empty($config['sms_api_key']) || (defined('SMS_API_KEY') && !empty(SMS_API_KEY));

echo json_encode([
    'success'      => true,
    'otp'          => $otp,
    'phone'        => $phone,
    'email'        => $email,
    'email_sent'   => $email_sent,
    'sms_sent'     => $sms_sent,
    'has_sms_key'  => $has_sms_key,
    'message'      => $sms_sent 
                        ? "OTP sent via Real SMS to {$phone}" 
                        : ($email_sent 
                            ? "OTP sent via Real Email to {$email} & Simulated SMS Alert" 
                            : "OTP generated successfully")
]);
exit();
