<?php
require 'db.php';
require_role('staff');

$userId = $_SESSION['user_id'];
$assigned = [];
$stmt = $conn->prepare('SELECT c.id, c.subject, c.category, c.status, c.created_at, r.name AS resident_name FROM complaints c JOIN users r ON c.resident_id = r.id WHERE c.assigned_to = ? ORDER BY c.created_at DESC');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $assigned[] = $row;
}
$stmt->close();

$statusCounts = [];
$stmt = $conn->prepare('SELECT status, COUNT(*) AS count FROM complaints WHERE assigned_to = ? GROUP BY status');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $statusCounts[$row['status']] = $row['count'];
}
$stmt->close();
?>
<?php
$pageTitle = 'Staff Dashboard - Society CMS';
$pageStyles = ".summary-card {border:none; border-radius:1rem; box-shadow:0 20px 45px rgba(15,23,42,.06);} .page-header {border-bottom:1px solid rgba(15,23,42,.08);}";
require 'header.php';
?>
<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 page-header pb-4 mb-4">
        <div>
            <p class="text-uppercase text-muted mb-2">Staff tools</p>
            <h1 class="h3 mb-1">Your work queue</h1>
            <p class="text-muted mb-0">Review your assigned complaints and update statuses from one place.</p>
        </div>
        <a class="btn btn-primary btn-sm align-self-start" href="complaints.php">Open assigned tasks</a>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card summary-card p-4 text-center">
                <h6 class="text-muted">Open</h6>
                <h2 class="mt-2"><?= escape($statusCounts['Open'] ?? 0) ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card p-4 text-center">
                <h6 class="text-muted">In Progress</h6>
                <h2 class="mt-2"><?= escape($statusCounts['In Progress'] ?? 0) ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card p-4 text-center">
                <h6 class="text-muted">Resolved</h6>
                <h2 class="mt-2"><?= escape($statusCounts['Resolved'] ?? 0) ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card p-4 text-center">
                <h6 class="text-muted">Closed</h6>
                <h2 class="mt-2"><?= escape($statusCounts['Closed'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="card summary-card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Assigned Complaints</h5>
            <?php if (empty($assigned)): ?>
                <p class="text-muted">No complaints currently assigned to you.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Resident</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assigned as $item): ?>
                                <tr>
                                    <td><?= escape($item['subject']) ?></td>
                                    <td><?= escape($item['resident_name']) ?></td>
                                    <td><span class="badge bg-secondary"><?= escape($item['status']) ?></span></td>
                                    <td><a class="btn btn-sm btn-primary" href="update_status.php?id=<?= escape($item['id']) ?>">Update</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require 'footer.php'; ?>
