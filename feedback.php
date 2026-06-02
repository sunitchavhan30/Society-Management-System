<?php
require 'db.php';
require_role('resident');

$userId = $_SESSION['user_id'];
$complaintId = intval($_GET['id'] ?? 0);
$error = '';
$success = '';
$complaint = null;
$existing = null;

if ($complaintId <= 0) {
    redirect('resident_dashboard.php');
}

$stmt = $conn->prepare('SELECT c.id, c.subject, c.status FROM complaints c WHERE c.id = ? AND c.resident_id = ? LIMIT 1');
$stmt->bind_param('ii', $complaintId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();
$stmt->close();

if (!$complaint || !in_array($complaint['status'], ['Resolved', 'Closed'], true)) {
    redirect('resident_dashboard.php');
}

$stmt = $conn->prepare('SELECT id, rating, comment, created_at FROM feedbacks WHERE complaint_id = ? LIMIT 1');
$stmt->bind_param('i', $complaintId);
$stmt->execute();
$result = $stmt->get_result();
$existing = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating'] ?? 5);
    $comment = trim($_POST['comment'] ?? '');
    if ($comment === '') {
        $error = 'Please add your feedback comment.';
    } elseif ($rating < 1 || $rating > 5) {
        $error = 'Rating must be between 1 and 5.';
    } else {
        if ($existing) {
            $update = $conn->prepare('UPDATE feedbacks SET rating = ?, comment = ?, created_at = NOW() WHERE id = ?');
            $update->bind_param('isi', $rating, $comment, $existing['id']);
            $update->execute();
            $update->close();
            $success = 'Feedback updated successfully.';
        } else {
            $insert = $conn->prepare('INSERT INTO feedbacks (complaint_id, resident_id, rating, comment) VALUES (?, ?, ?, ?)');
            $insert->bind_param('iiis', $complaintId, $userId, $rating, $comment);
            $insert->execute();
            $insert->close();
            $success = 'Thank you for your feedback.';
        }
        $stmt = $conn->prepare('SELECT id, rating, comment, created_at FROM feedbacks WHERE complaint_id = ? LIMIT 1');
        $stmt->bind_param('i', $complaintId);
        $stmt->execute();
        $result = $stmt->get_result();
        $existing = $result->fetch_assoc();
        $stmt->close();
    }
}
?>
<?php
$pageTitle = 'Complaint Feedback - Society CMS';
$pageStyles = "body {background:#eef6fa; color:#1f2937;}";
require 'header.php';
?>
<div class="container py-5">
        </div>
    </div>
</nav>
<div class="container py-5">
    <div class="card border-0 shadow-lg">
        <div class="card-body p-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h1 class="h4 mb-2">Provide feedback</h1>
                    <p class="text-muted mb-0">Share your experience once the complaint is resolved or closed.</p>
                </div>
                <div class="text-md-end">
                    <p class="mb-1"><strong>Complaint:</strong> <?= escape($complaint['subject']) ?></p>
                    <span class="badge bg-success">Status: <?= escape($complaint['status']) ?></span>
                </div>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= escape($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= escape($success) ?></div>
            <?php endif; ?>
            <form method="post" action="feedback.php?id=<?= escape($complaintId) ?>">
                <div class="mb-4">
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-select form-select-lg">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>" <?= $existing && intval($existing['rating']) === $i ? 'selected' : '' ?>><?= $i ?> star<?= $i === 1 ? '' : 's' ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Comment</label>
                    <textarea name="comment" class="form-control form-control-lg" rows="5" required><?= escape($existing['comment'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-success btn-lg">Submit Feedback</button>
            </form>
            <?php if ($existing): ?>
                <div class="mt-5 pt-4 border-top">
                    <h5 class="mb-3">Existing feedback</h5>
                    <p class="mb-1"><strong>Rating:</strong> <?= escape($existing['rating']) ?> / 5</p>
                    <p><?= nl2br(escape($existing['comment'])) ?></p>
                    <p class="text-muted">Submitted: <?= escape($existing['created_at']) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require 'footer.php'; ?>
