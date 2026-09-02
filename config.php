<?php
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "airport";

$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// REAL SMTP EMAIL SETTINGS (Set your Gmail / SMTP credentials here for real inbox delivery)
if (!defined('SMTP_HOST')) define('SMTP_HOST', 'smtp.gmail.com');
if (!defined('SMTP_PORT')) define('SMTP_PORT', 587);
if (!defined('SMTP_USER')) define('SMTP_USER', ''); // e.g. your_email@gmail.com
if (!defined('SMTP_PASS')) define('SMTP_PASS', ''); // e.g. Gmail App Password
if (!defined('SMTP_FROM_EMAIL')) define('SMTP_FROM_EMAIL', 'no-reply@skyport.com');
if (!defined('SMTP_FROM_NAME')) define('SMTP_FROM_NAME', 'SkyPort Airlines');

// REAL SMS API SETTINGS (Set your Fast2SMS / Twilio API key here for real mobile text delivery)
if (!defined('SMS_API_KEY')) define('SMS_API_KEY', ''); // e.g. Fast2SMS API key
?>