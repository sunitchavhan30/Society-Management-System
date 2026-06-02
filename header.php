<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Society CMS';
}
if (!isset($pageStyles)) {
    $pageStyles = '';
}
$homeLink = 'index.php';
$navButtons = '';
if (isset($_SESSION['user_role'])) {
    $role = $_SESSION['user_role'];
    $dashboardLink = $role === 'admin' ? 'admin_dashboard.php' : ($role === 'staff' ? 'staff_dashboard.php' : 'resident_dashboard.php');
    $navButtons = "<a class='btn btn-outline-light me-2' href='{$dashboardLink}'>Dashboard</a><a class='btn btn-light' href='logout.php'>Logout</a>";
} else {
    $navButtons = "<a class='btn btn-outline-light me-2' href='login.php'>Login</a><a class='btn btn-primary' href='register.php'>Register</a>";
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {font-family: 'Inter', sans-serif; background: #f4f7fb; color: #1f2937;}
        .navbar {box-shadow: 0 14px 45px rgba(15, 23, 42, .08);}
        .brand-logo {width: 34px; height: 34px;}
        .brand-text {margin-left: .75rem; font-size: 1rem; letter-spacing: .01em;}
        .card {border-radius: 1rem;}
        .badge-soft {background: rgba(13, 110, 253, .1); color: #0d6efd;}
        .btn-rounded {border-radius: 999px;}
        .footer-bar {background: #111827; color: rgba(255,255,255,.72);}
        .section-hero {min-height: 78vh;}
        <?= $pageStyles ?>
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-semibold" href="<?= $homeLink ?>">
            <img src="logo.svg" alt="Society CMS logo" class="brand-logo" />
            <span class="brand-text">Society CMS</span>
        </a>
        <div class="d-flex align-items-center">
            <?= $navButtons ?>
        </div>
    </div>
</nav>
