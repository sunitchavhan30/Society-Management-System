<?php
require 'db.php';
if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'admin') {
        redirect('admin_dashboard.php');
    }
    if ($_SESSION['user_role'] === 'staff') {
        redirect('staff_dashboard.php');
    }
    if ($_SESSION['user_role'] === 'resident') {
        redirect('resident_dashboard.php');
    }
}
?>
<?php
$pageTitle = 'Society Complaint Management';
$pageStyles = ".section-hero {background: radial-gradient(circle at top left, rgba(13,110,253,.18), transparent 28%), linear-gradient(135deg, #0d6efd 0%, #198754 100%); color: #fff; min-height: 78vh;} .hero-title {letter-spacing: -.03em;} .feature-card {border: 1px solid rgba(255,255,255,.16);} .btn-hero {box-shadow: 0 14px 32px rgba(255,255,255,.16);}";
require 'header.php';
?>
<section class="hero hero-bg d-flex align-items-center py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="text-white mb-4">
                    <span class="badge bg-white text-primary mb-3">Professional Society Management</span>
                    <h1 class="display-5 fw-bold hero-title">Modern society complaint tracking, built in PHP.</h1>
                    <p class="lead">Manage resident requests, staff assignments, and service quality with a polished dashboard experience.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a class="btn btn-light btn-lg" href="login.php">Login</a>
                    <a class="btn btn-outline-light btn-lg" href="register.php">Register</a>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h5 class="mb-1">System Preview</h5>
                                <p class="text-muted mb-0">Complaints, assignments, feedback, and reports in one workflow.</p>
                            </div>
                            <span class="badge badge-soft rounded-pill py-2 px-3">Live-ready</span>
                        </div>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item px-0 py-2">Role-based dashboards for admin, staff, and residents.</li>
                            <li class="list-group-item px-0 py-2">Complaint tracking with assignment and status updates.</li>
                            <li class="list-group-item px-0 py-2">Feedback and basic reports for operations insight.</li>
                        </ul>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="card feature-card p-3 shadow-sm">
                                    <strong>Secure login</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card feature-card p-3 shadow-sm">
                                    <strong>Responsive UI</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card feature-card p-3 shadow-sm">
                                    <strong>Admin reports</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card feature-card p-3 shadow-sm">
                                    <strong>Resident feedback</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-5">
    <div class="container">
        <div class="row text-center gy-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <h5 class="mb-2">Smart ticketing</h5>
                    <p class="text-muted">Track all complaints in one system with real-time status labels.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <h5 class="mb-2">Staff coordination</h5>
                    <p class="text-muted">Assign tasks to staff and monitor workload from the dashboard.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <h5 class="mb-2">Impact reporting</h5>
                    <p class="text-muted">View complaint summaries and feedback metrics for service improvement.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require 'footer.php'; ?>
