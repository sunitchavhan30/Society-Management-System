<?php
require 'db.php';
require_role('admin');

$stats = [];
$statuses = ['Open', 'In Progress', 'Resolved', 'Closed'];
$stmt = $conn->prepare('SELECT status, COUNT(*) AS total FROM complaints GROUP BY status');
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $stats[$row['status']] = $row['total'];
}
$stmt->close();

$totalComplaints = array_sum($stats);
$resolvedCount = $stats['Resolved'] ?? 0;
$closedCount = $stats['Closed'] ?? 0;

$staffList = [];
$stmt = $conn->query('SELECT u.name, COUNT(c.id) AS assigned_count FROM users u LEFT JOIN complaints c ON u.id = c.assigned_to WHERE u.role = "staff" GROUP BY u.id ORDER BY assigned_count DESC');
while ($row = $stmt->fetch_assoc()) {
    $staffList[] = $row;
}

$recent = [];
$stmt = $conn->query('SELECT c.id, c.subject, c.status, c.category, c.created_at, r.name AS resident_name, s.name AS staff_name FROM complaints c LEFT JOIN users r ON c.resident_id = r.id LEFT JOIN users s ON c.assigned_to = s.id ORDER BY c.created_at DESC LIMIT 6');
while ($row = $stmt->fetch_assoc()) {
    $recent[] = $row;
}
?>
<?php
$pageTitle = 'Admin Dashboard - Society CMS';
$pageStyles = ".summary-card {border:none; box-shadow:0 14px 45px rgba(15,23,42,.08);} .page-title {letter-spacing:.02em;}";
require 'header.php';
?>
<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4 gap-3">
        <div>
            <p class="text-uppercase text-muted mb-2">Admin workspace</p>
            <h1 class="h2 mb-1">Operational overview</h1>
            <p class="text-muted">Track complaint flow, assign staff, and measure resolution performance.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-primary" href="complaints.php">View Complaints</a>
            <a class="btn btn-primary" href="reports.php">View Reports</a>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card summary-card p-4">
                <small class="text-uppercase text-muted">Total complaints</small>
                <h2 class="mt-3 mb-0"><?= escape($totalComplaints) ?></h2>
            </div>
        </div>
        <?php foreach ($statuses as $status): ?>
            <div class="col-md-3">
                <div class="card summary-card p-4">
                    <small class="text-uppercase text-muted"><?= escape($status) ?></small>
                    <h2 class="mt-3 mb-0"><?= escape($stats[$status] ?? 0) ?></h2>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="row g-4 mt-4">
        <div class="col-lg-6">
            <div class="card summary-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Staff workload</h5>
                    <span class="badge bg-primary badge-pill">Updated</span>
                </div>
                <?php if (empty($staffList)): ?>
                    <p class="text-muted mb-0">No staff members available.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($staffList as $staff): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-3">
                                <?= escape($staff['name']) ?>
                                <span class="badge bg-secondary rounded-pill"><?= escape($staff['assigned_count']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card summary-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Recent complaints</h5>
                    <span class="badge bg-secondary badge-pill">Latest</span>
                </div>
                <?php if (empty($recent)): ?>
                    <p class="text-muted mb-0">No recent complaints yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Staff</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent as $item): ?>
                                    <tr>
                                        <td><?= escape($item['subject']) ?></td>
                                        <td><span class="badge bg-light text-dark"><?= escape($item['status']) ?></span></td>
                                        <td><?= escape($item['staff_name'] ?: 'Unassigned') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require 'footer.php'; ?>
