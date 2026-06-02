<?php
require 'db.php';
require_role('admin');

$statusCounts = [];
$stmt = $conn->prepare('SELECT status, COUNT(*) AS total FROM complaints GROUP BY status');
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $statusCounts[$row['status']] = $row['total'];
}
$stmt->close();

$staffLoad = [];
$stmt = $conn->query('SELECT u.name, COUNT(c.id) AS total_assigned FROM users u LEFT JOIN complaints c ON u.id = c.assigned_to WHERE u.role = "staff" GROUP BY u.id ORDER BY total_assigned DESC');
while ($row = $stmt->fetch_assoc()) {
    $staffLoad[] = $row;
}

$feedbackCount = 0;
$averageRating = 0;
$stmt = $conn->query('SELECT COUNT(*) AS total, AVG(rating) AS average_rating FROM feedbacks');
if ($row = $stmt->fetch_assoc()) {
    $feedbackCount = $row['total'];
    $averageRating = $row['average_rating'];
}
?>
<?php
$pageTitle = 'Admin Reports - Society CMS';
$pageStyles = ".report-card {border:none; border-radius:1rem; box-shadow:0 22px 55px rgba(15,23,42,.08);} .page-title {letter-spacing:.02em;}";
require 'header.php';
?>
<div class="container py-5">
    <div class="row align-items-center justify-content-between mb-4">
        <div>
            <p class="text-uppercase text-muted mb-2">Operations report</p>
            <h1 class="h3 page-title mb-1">Performance metrics</h1>
            <p class="text-muted mb-0">Quick insight into complaint status, staff workload, and resident feedback.</p>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card report-card p-4 text-center">
                <small class="text-muted text-uppercase">Total feedback</small>
                <h2 class="mt-3 mb-0"><?= escape($feedbackCount) ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card report-card p-4 text-center">
                <small class="text-muted text-uppercase">Average rating</small>
                <h2 class="mt-3 mb-0"><?= $averageRating ? number_format($averageRating, 1) : '0.0' ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card report-card p-4 text-center">
                <small class="text-muted text-uppercase">Open complaints</small>
                <h2 class="mt-3 mb-0"><?= escape($statusCounts['Open'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card report-card p-4 h-100">
                <h5 class="mb-3">Complaint status summary</h5>
                <ul class="list-group list-group-flush">
                    <?php foreach (['Open', 'In Progress', 'Resolved', 'Closed'] as $status): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-3">
                            <?= escape($status) ?>
                            <span class="badge bg-primary rounded-pill"><?= escape($statusCounts[$status] ?? 0) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card report-card p-4 h-100">
                <h5 class="mb-3">Staff assignment load</h5>
                <?php if (empty($staffLoad)): ?>
                    <p class="text-muted">No staff records to display.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($staffLoad as $staff): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-3">
                                <?= escape($staff['name']) ?>
                                <span class="badge bg-secondary rounded-pill"><?= escape($staff['total_assigned']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require 'footer.php'; ?>
