<?php
require 'db.php';
require_role('admin');

$complaintId = intval($_GET['id'] ?? 0);
$error = '';
$success = '';
$complaint = null;
$staffOptions = [];

if ($complaintId <= 0) {
    redirect('complaints.php');
}

$stmt = $conn->prepare('SELECT c.id, c.subject, c.category, c.description, c.status, r.name AS resident_name FROM complaints c JOIN users r ON c.resident_id = r.id WHERE c.id = ? LIMIT 1');
$stmt->bind_param('i', $complaintId);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();
$stmt->close();
if (!$complaint) {
    redirect('complaints.php');
}

$result = $conn->query('SELECT id, name FROM users WHERE role = "staff" ORDER BY name');
while ($row = $result->fetch_assoc()) {
    $staffOptions[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffId = intval($_POST['staff_id'] ?? 0);
    if ($staffId <= 0) {
        $error = 'Select a staff member to assign.';
    } else {
        $update = $conn->prepare('UPDATE complaints SET assigned_to = ?, status = ? WHERE id = ?');
        $status = 'In Progress';
        $update->bind_param('isi', $staffId, $status, $complaintId);
        if ($update->execute()) {
            $success = 'Complaint assigned successfully.';
            $complaint['status'] = $status;
        } else {
            $error = 'Unable to assign complaint. Try again.';
        }
        $update->close();
    }
}
?>
<?php
$pageTitle = 'Assign Complaint - Society CMS';
$pageStyles = ".card-hero {border: none; border-radius: 1rem; box-shadow: 0 24px 70px rgba(15, 23, 42, .07);}";
require 'header.php';
?>
<div class="container py-5">
    <div class="card card-hero">
        <div class="card-body p-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4 gap-3">
                <div>
                    <h1 class="h4 mb-1">Assign complaint</h1>
                    <p class="text-muted mb-0">Route this request to the right staff member and start work quickly.</p>
                </div>
                <div class="text-md-end">
                    <span class="badge bg-primary">Status: <?= escape($complaint['status']) ?></span>
                </div>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= escape($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= escape($success) ?></div>
            <?php endif; ?>
            <form method="post" action="assign_complaint.php?id=<?= escape($complaintId) ?>">
                <div class="mb-4">
                    <label class="form-label">Assign staff member</label>
                    <select name="staff_id" class="form-select form-select-lg" required>
                        <option value="">Choose staff</option>
                        <?php foreach ($staffOptions as $staff): ?>
                            <option value="<?= escape($staff['id']) ?>"><?= escape($staff['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Assign Now</button>
            </form>
            <div class="mt-5">
                <h5 class="mb-3">Complaint details</h5>
                <dl class="row">
                    <dt class="col-sm-3">Subject</dt>
                    <dd class="col-sm-9"><?= escape($complaint['subject']) ?></dd>
                    <dt class="col-sm-3">Category</dt>
                    <dd class="col-sm-9"><?= escape($complaint['category']) ?></dd>
                    <dt class="col-sm-3">Resident</dt>
                    <dd class="col-sm-9"><?= escape($complaint['resident_name']) ?></dd>
                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9"><?= nl2br(escape($complaint['description'])) ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>
<?php require 'footer.php'; ?>
