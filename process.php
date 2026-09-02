<?php
session_start();
require_once _DIR_ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($email === '' || $password === '') {
    $_SESSION['error'] = "Please enter email and password.";
    header("Location: login.php");
    exit;
}

// Prepared statement to get admin by email
$stmt = $conn->prepare("SELECT id, name, password FROM admin WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows === 1) {
    $row = $res->fetch_assoc();
    $dbPass = $row['password'];

    $login_ok = false;

    // Detect if DB password looks like MD5 (32 hex chars)
    if (preg_match('/^[a-f0-9]{32}$/i', $dbPass)) {
        // MD5 stored (legacy)
        if (md5($password) === $dbPass) {
            $login_ok = true;
        }
    } else {
        // Assume password_hash used
        if (password_verify($password, $dbPass)) {
            $login_ok = true;
        }
    }

    if ($login_ok) {
        // Login success
        $_SESSION['admin_id'] = $row['id'];
        $_SESSION['admin_name'] = $row['name'];

        // OPTIONAL: If legacy MD5, encourage upgrade (you can re-hash here)
        if (preg_match('/^[a-f0-9]{32}$/i', $dbPass)) {
            // Upgrade to password_hash automatically
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $u = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
            $u->bind_param("si", $newHash, $row['id']);
            $u->execute();
            // ignore failures
        }

        header("Location: admin/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: login.php");
        exit;
    }

} else {
    $_SESSION['error'] = "Invalid email or password.";
    header("Location: login.php");
    exit;
}