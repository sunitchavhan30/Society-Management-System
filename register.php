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
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    if ($name === '' || $email === '' || $password === '' || $confirm === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'resident';
            $insert = $conn->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            $insert->bind_param('ssss', $name, $email, $hash, $role);
            if ($insert->execute()) {
                $success = 'Registration complete. You may now log in.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
            $insert->close();
        }
        $stmt->close();
    }
}
?>
<?php
$pageTitle = 'Register Resident - Society CMS';
$pageStyles = ".auth-card {max-width: 980px; margin: 3.5rem auto;} .bg-accent {background: linear-gradient(135deg, #198754, #0d6efd); color: #fff;} .form-control, .form-select {border-radius: .75rem;} .shadow-soft {box-shadow: 0 24px 70px rgba(15, 23, 42, .08);} .auth-panel {min-height: 520px;}";
require 'header.php';
?>
<div class="container">
    <div class="card auth-card border-0 shadow-soft overflow-hidden">
        <div class="row g-0">
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-accent text-white p-5 auth-panel">
                <div>
                    <h2 class="fw-bold">Join the community</h2>
                    <p class="lead">Create your resident account and start submitting service requests instantly.</p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">• Complaint tracking dashboard</li>
                        <li class="mb-2">• Feedback and follow-up history</li>
                        <li>• Secure role-based access</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6 p-5">
                <div class="mb-4">
                    <h3 class="fw-bold">Resident Registration</h3>
                    <p class="text-muted mb-0">Register to submit complaints and view status updates.</p>
                </div>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= escape($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= escape($success) ?></div>
                <?php endif; ?>
                <form method="post" action="register.php">
                    <div class="mb-3">
                        <label class="form-label">Full name</label>
                        <input type="text" name="name" class="form-control form-control-lg" value="<?= escape($_POST['name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control form-control-lg" value="<?= escape($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm password</label>
                            <input type="password" name="confirm_password" class="form-control form-control-lg" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg w-100 mt-4">Create account</button>
                </form>
                <div class="mt-4 text-center text-muted">
                    <p class="mb-1">Already have an account?</p>
                    <a href="login.php">Login here</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require 'footer.php'; ?>
