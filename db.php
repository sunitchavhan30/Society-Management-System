<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = ''; 
$dbname = 'society_db';

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db($dbname);
if ($conn->connect_error) {
    die('Database selection failed: ' . $conn->connect_error);
}

function escape($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function require_role($role) {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
        redirect('login.php');
    }
}

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        redirect('login.php');
    }
}

function initialize_database($conn) {
    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('resident','staff','admin') NOT NULL DEFAULT 'resident',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->query("CREATE TABLE IF NOT EXISTS complaints (
        id INT AUTO_INCREMENT PRIMARY KEY,
        resident_id INT NOT NULL,
        subject VARCHAR(200) NOT NULL,
        description TEXT NOT NULL,
        category VARCHAR(100) NOT NULL,
        status ENUM('Open','In Progress','Resolved','Closed') NOT NULL DEFAULT 'Open',
        assigned_to INT DEFAULT NULL,
        staff_note TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        FOREIGN KEY (resident_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->query("CREATE TABLE IF NOT EXISTS feedbacks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        complaint_id INT NOT NULL,
        resident_id INT NOT NULL,
        rating TINYINT NOT NULL DEFAULT 5,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE,
        FOREIGN KEY (resident_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $adminEmail = 'admin@domain.com';
    $staffEmail = 'staff@domain.com';

    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $adminEmail);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmtAdd = $conn->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ? )');
        $role = 'admin';
        $name = 'Admin User';
        $stmtAdd->bind_param('ssss', $name, $adminEmail, $passwordHash, $role);
        $stmtAdd->execute();
        $stmtAdd->close();
    }
    $stmt->close();

    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $staffEmail);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $passwordHash = password_hash('staff123', PASSWORD_DEFAULT);
        $stmtAdd = $conn->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ? )');
        $role = 'staff';
        $name = 'Staff User';
        $stmtAdd->bind_param('ssss', $name, $staffEmail, $passwordHash, $role);
        $stmtAdd->execute();
        $stmtAdd->close();
    }
    $stmt->close();
}

initialize_database($conn);
