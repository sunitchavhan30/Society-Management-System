<?php
require 'db.php';
require_login();

$role = $_SESSION['user_role'];
$userId = $_SESSION['user_id'];
$complaintId = intval($_GET['id'] ?? 0);
$error = '';
$success = '';
$complaint = null;

if ($complaintId <= 0) {
    redirect('complaints.php');
}

if ($role === 'staff') {
    $stmt = $conn->prepare('SELECT c.*, r.name AS resident_name FROM complaints c JOIN users r ON c.resident_id = r.id WHERE c.id = ? AND c.assigned_to = ? LIMIT 1');
    $stmt->bind_param('ii', $complaintId, $userId);
} else {
    $stmt = $conn->prepare('SELECT c.*, r.name AS resident_name FROM complaints c JOIN users r ON c.resident_id = r.id WHERE c.id = ? LIMIT 1');
    $stmt->bind_param('i', $complaintId);
}
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();
$stmt->close();

if (!$complaint) {
    redirect('complaints.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = trim($_POST['status'] ?? '');
    $note = trim($_POST['staff_note'] ?? '');
    if ($status === '') {
        $error = 'Select a status before updating.';
    } else {
        $update = $conn->prepare('UPDATE complaints SET status = ?, staff_note = ?, updated_at = NOW() WHERE id = ?');
        $update->bind_param('ssi', $status, $note, $complaintId);
        if ($update->execute()) {
            $success = 'Complaint status updated successfully.';
            $complaint['status'] = $status;
            $complaint['staff_note'] = $note;
        } else {
            $error = 'Unable to update the status. Try again.';
        }
        $update->close();
    }
}
?>
<?php
$pageTitle = 'Update Complaint - Society CMS';
$pageStyles = "body {background:#eef3f8; color:#1f2937;}";
require 'header.php';
?>
<div class="container py-5">
    <div class="card border-0 shadow-lg">
        <div class="card-body p-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h1 class="h4 mb-2">Update complaint status</h1>
                    <p class="text-muted mb-0">Keep residents informed by changing the ticket status and adding notes.</p>
                </div>
                <span class="badge bg-primary fs-6">Current: <?= escape($complaint['status']) ?></span>
            </div>
            <p class="mb-1"><strong>Subject:</strong> <?= escape($complaint['subject']) ?></p>
            <p class="mb-4"><strong>Resident:</strong> <?= escape($complaint['resident_name']) ?></p>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= escape($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= escape($success) ?></div>
            <?php endif; ?>
            <form method="post" action="update_status.php?id=<?= escape($complaintId) ?>">
                <div class="mb-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-lg" required>
                        <?php foreach (['In Progress', 'Resolved', 'Closed'] as $statusOption): ?>
                            <option value="<?= escape($statusOption) ?>" <?= $complaint['status'] === $statusOption ? 'selected' : '' ?>><?= escape($statusOption) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Staff Note</label>
                    <textarea name="staff_note" class="form-control form-control-lg" rows="5"><?= escape($complaint['staff_note'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Save update</button>
            </form>
            <div class="mt-5">
                <h5>Complaint description</h5>
                <p><?= nl2br(escape($complaint['description'])) ?></p>
            </div>
        </div>
    </div>
</div>
<?php require 'footer.php'; ?>
