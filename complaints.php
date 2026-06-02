<?php
require 'db.php';
require_login();

$role = $_SESSION['user_role'];
$userId = $_SESSION['user_id'];
$complaints = [];

if ($role === 'resident') {
    $stmt = $conn->prepare('SELECT c.id, c.subject, c.category, c.status, c.created_at, u.name AS staff_name FROM complaints c LEFT JOIN users u ON c.assigned_to = u.id WHERE c.resident_id = ? ORDER BY c.created_at DESC');
    $stmt->bind_param('i', $userId);
} elseif ($role === 'staff') {
    $stmt = $conn->prepare('SELECT c.id, c.subject, c.category, c.status, c.created_at, r.name AS resident_name FROM complaints c JOIN users r ON c.resident_id = r.id WHERE c.assigned_to = ? ORDER BY c.created_at DESC');
    $stmt->bind_param('i', $userId);
} else {
    $stmt = $conn->prepare('SELECT c.id, c.subject, c.category, c.status, c.created_at, r.name AS resident_name, u.name AS staff_name FROM complaints c LEFT JOIN users r ON c.resident_id = r.id LEFT JOIN users u ON c.assigned_to = u.id ORDER BY c.created_at DESC');
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}
$stmt->close();
?>
<?php
$pageTitle = 'Complaints - Society CMS';
$pageStyles = ".page-title {letter-spacing: .02em;} .summary-card {border: none; border-radius: 1rem; box-shadow: 0 20px 40px rgba(15,23,42,.07);} .badge-status {font-size: .82rem;}";
require 'header.php';
?>
<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
        <div>
            <p class="text-uppercase text-muted mb-2">Complaints</p>
            <h1 class="h3 page-title mb-1">View and manage tickets</h1>
            <p class="text-muted mb-0">All submitted complaint records are listed below with role-specific actions.</p>
        </div>
        <div class="text-md-end">
            <a class="btn btn-primary btn-sm" href="<?= $role === 'resident' ? 'resident_dashboard.php' : ($role === 'staff' ? 'staff_dashboard.php' : 'admin_dashboard.php') ?>">Back to dashboard</a>
        </div>
    </div>
    <div class="card summary-card p-4 mb-4">
        <div class="table-responsive">
            <?php if (empty($complaints)): ?>
                <p class="text-muted mb-0">There are no complaints to display.</p>
            <?php else: ?>
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Category</th>
                            <?php if ($role !== 'resident'): ?><th>Resident</th><?php endif; ?>
                            <?php if ($role === 'admin'): ?><th>Assigned To</th><?php endif; ?>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($complaints as $complaint): ?>
                            <tr>
                                <td><?= escape($complaint['subject']) ?></td>
                                <td><?= escape($complaint['category']) ?></td>
                                <?php if ($role !== 'resident'): ?><td><?= escape($complaint['resident_name']) ?></td><?php endif; ?>
                                <?php if ($role === 'admin'): ?><td><?= escape($complaint['staff_name'] ?: 'Unassigned') ?></td><?php endif; ?>
                                <td><span class="badge bg-secondary badge-status"><?= escape($complaint['status']) ?></span></td>
                                <td><?= escape($complaint['created_at']) ?></td>
                                <td>
                                    <?php if ($role === 'admin'): ?>
                                        <a class="btn btn-sm btn-outline-primary" href="assign_complaint.php?id=<?= escape($complaint['id']) ?>">Assign</a>
                                    <?php elseif ($role === 'staff'): ?>
                                        <a class="btn btn-sm btn-outline-primary" href="update_status.php?id=<?= escape($complaint['id']) ?>">Update</a>
                                    <?php else: ?>
                                        <?php if (in_array($complaint['status'], ['Resolved', 'Closed'], true)): ?>
                                            <a class="btn btn-sm btn-outline-primary" href="feedback.php?id=<?= escape($complaint['id']) ?>">Feedback</a>
                                        <?php else: ?>
                                            <span class="text-muted small">No action</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require 'footer.php'; ?>
