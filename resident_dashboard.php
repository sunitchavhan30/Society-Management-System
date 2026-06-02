<?php
require 'db.php';
require_role('resident');

$userId = $_SESSION['user_id'];
$error = '';
$success = '';
$statusSummary = ['Open' => 0, 'In Progress' => 0, 'Resolved' => 0, 'Closed' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    if ($subject === '' || $category === '' || $description === '') {
        $error = 'Please complete all complaint fields.';
    } else {
        $stmt = $conn->prepare('INSERT INTO complaints (resident_id, subject, category, description) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('isss', $userId, $subject, $category, $description);
        if ($stmt->execute()) {
            $success = 'Your complaint has been submitted successfully.';
        } else {
            $error = 'Unable to submit complaint. Please try again.';
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare('SELECT status, COUNT(*) AS count FROM complaints WHERE resident_id = ? GROUP BY status');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $statusSummary[$row['status']] = $row['count'];
}
$stmt->close();

$complaints = [];
$stmt = $conn->prepare('SELECT c.id, c.subject, c.category, c.status, c.assigned_to, c.created_at, u.name AS staff_name FROM complaints c LEFT JOIN users u ON c.assigned_to = u.id WHERE c.resident_id = ? ORDER BY c.created_at DESC');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}
$stmt->close();
?>
<?php
$pageTitle = 'Resident Dashboard - Society CMS';
$pageStyles = ".summary-card {border: none; border-radius: 1rem; box-shadow: 0 20px 35px rgba(15,23,42,.08);} .page-title {letter-spacing: .02em;} .badge-status {font-size: .8rem; font-weight: 600;}";
require 'header.php';
?>
<div class="container py-5">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <p class="text-uppercase text-muted mb-2">Resident portal</p>
            <h1 class="h3 page-title mb-1">Track your society requests</h1>
            <p class="text-muted mb-0">Submit a complaint, see assignment status, and stay informed about progress.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a class="btn btn-outline-light btn-sm" href="complaints.php">View all complaints</a>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <?php foreach (['Open', 'In Progress', 'Resolved', 'Closed'] as $status): ?>
            <div class="col-sm-6 col-lg-3">
                <div class="card summary-card p-4 text-center">
                    <div class="text-uppercase text-muted small mb-2"><?= escape($status) ?></div>
                    <h2 class="mb-0"><?= escape($statusSummary[$status] ?? 0) ?></h2>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card summary-card p-4">
                <h4 class="mb-3">Submit a New Complaint</h4>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= escape($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= escape($success) ?></div>
                <?php endif; ?>
                <form method="post" action="resident_dashboard.php">
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control form-control-lg" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select form-select-lg" required>
                            <option value="">Choose category</option>
                            <option>Plumbing</option>
                            <option>Electrical</option>
                            <option>Security</option>
                            <option>Cleaning</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control form-control-lg" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">Submit Complaint</button>
                </form>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card summary-card p-4">
                <h4 class="mb-3">Recent Complaints</h4>
                <?php if (empty($complaints)): ?>
                    <p class="text-muted">No complaints submitted yet. Use the form to create one.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($complaints as $complaint): ?>
                            <div class="list-group-item mb-3 rounded-4 shadow-sm border-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?= escape($complaint['subject']) ?></h6>
                                        <small class="text-muted">Category: <?= escape($complaint['category']) ?> • Submitted: <?= escape($complaint['created_at']) ?></small>
                                    </div>
                                    <span class="badge bg-secondary badge-status"><?= escape($complaint['status']) ?></span>
                                </div>
                                <div class="mt-3 d-flex flex-column gap-2">
                                    <div><strong>Assigned to:</strong> <?= escape($complaint['staff_name'] ?: 'Pending assignment') ?></div>
                                    <a class="btn btn-sm btn-outline-primary" href="feedback.php?id=<?= escape($complaint['id']) ?>">Leave feedback</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require 'footer.php'; ?>
