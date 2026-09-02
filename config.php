<?php
// Environment Variables for Database Connection
$DB_HOST = getenv('DB_HOST') ?: (getenv('MYSQL_HOST') ?: "localhost");
$DB_USER = getenv('DB_USER') ?: (getenv('MYSQL_USER') ?: "root");
$DB_PASS = getenv('DB_PASS') !== false ? getenv('DB_PASS') : (getenv('MYSQL_PASSWORD') !== false ? getenv('MYSQL_PASSWORD') : "");
$DB_NAME = getenv('DB_NAME') ?: (getenv('MYSQL_DATABASE') ?: "airport");

$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// REAL SMTP EMAIL SETTINGS (Read from Environment Variables or defaults)
if (!defined('SMTP_HOST')) define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
if (!defined('SMTP_PORT')) define('SMTP_PORT', getenv('SMTP_PORT') ? intval(getenv('SMTP_PORT')) : 587);
if (!defined('SMTP_USER')) define('SMTP_USER', getenv('SMTP_USER') ?: '');
if (!defined('SMTP_PASS')) define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
if (!defined('SMTP_FROM_EMAIL')) define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: 'no-reply@skyport.com');
if (!defined('SMTP_FROM_NAME')) define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'SkyPort Airlines');

// REAL SMS API SETTINGS
if (!defined('SMS_API_KEY')) define('SMS_API_KEY', getenv('SMS_API_KEY') ?: '');
?>