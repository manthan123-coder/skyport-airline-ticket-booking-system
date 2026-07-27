<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand bg-dark navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Airport Admin</a>
        <div class="ms-auto">
            <span class="text-white me-3">Hello, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <a class="btn btn-sm btn-outline-light" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h3>Dashboard</h3>
    <p>Yahan se aage aap flights / staff / bookings manage kar sakte ho.</p>

    <!-- Example quick stats (optional) -->
    <?php
    require_once _DIR_ . '/../config.php';
    $counts = [];
    $tables = ['flights','users','bookings','staff'];
    foreach ($tables as $t) {
        $r = $conn->query("SELECT COUNT(*) as c FROM $t");
        $counts[$t] = ($r) ? $r->fetch_assoc()['c'] : 0;
    }
    ?>
    <div class="row">
        <div class="col-md-3"><div class="card p-3">Flights: <?php echo $counts['flights']; ?></div></div>
        <div class="col-md-3"><div class="card p-3">Users: <?php echo $counts['users']; ?></div></div>
        <div class="col-md-3"><div class="card p-3">Bookings: <?php echo $counts['bookings']; ?></div></div>
        <div class="col-md-3"><div class="card p-3">Staff: <?php echo $counts['staff']; ?></div></div>
    </div>
</div>
</body>
</html>