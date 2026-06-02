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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $conn->prepare('SELECT id, name, password, role FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($userId, $name, $hash, $role);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $role;
                if ($role === 'admin') {
                    redirect('admin_dashboard.php');
                }
                if ($role === 'staff') {
                    redirect('staff_dashboard.php');
                }
                redirect('resident_dashboard.php');
            }
        }
        $error = 'Invalid email or password.';
        $stmt->close();
    }
}
?>
<?php
$pageTitle = 'Login - Society CMS';
$pageStyles = ".auth-card {max-width: 900px; margin: 4rem auto;} .bg-accent {background: linear-gradient(135deg, #0d6efd, #198754); color: #fff;} .form-control, .form-select {border-radius: .75rem;} .shadow-soft {box-shadow: 0 20px 50px rgba(15, 23, 42, .08);} .auth-panel {min-height: 520px;}";
require 'header.php';
?>
<div class="container">
    <div class="card auth-card border-0 shadow-soft">
        <div class="row g-0">
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-accent text-white rounded-start p-5 auth-panel">
                <div>
                    <h2 class="fw-bold">Welcome Back</h2>
                    <p class="lead">Secure access for residents, staff, and admins. Manage complaints, assignments and service updates from one dashboard.</p>
                    
                </div>
            </div>
            <div class="col-lg-6 p-5">
                <div class="mb-4">
                    <h3 class="fw-bold">Login to your account</h3>
                    <p class="text-muted mb-0">Enter your credentials to continue.</p>
                </div>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= escape($error) ?></div>
                <?php endif; ?>
                <form method="post" action="login.php">
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control form-control-lg" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Sign in</button>
                </form>
                <div class="mt-4 text-center text-muted">
                    <p class="mb-1">New to Society CMS?</p>
                    <a href="register.php">Create an account</a>
                </div>
                <div class="mt-4 p-3 bg-light rounded-3">
                    <p class="mb-1"><strong>Quick login</strong></p>
                    <small class="text-muted">Admin: admin@domain.com / admin123<br>Staff: staff@domain.com / staff123</small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require 'footer.php'; ?>
