<?php
require 'db.php';
require 'config.php';

function redirectWithMessage($type, $message, $mode = 'register') {
    header("Location: index.php?" . $type . "=" . rawurlencode($message) . "&mode=" . rawurlencode($mode));
    exit();
}

function isStrongPassword($password) {
    return strlen($password) >= 12 &&
           preg_match('/[a-z]/', $password) &&
           preg_match('/[A-Z]/', $password) &&
           preg_match('/[0-9]/', $password) &&
           preg_match('/[^a-zA-Z0-9]/', $password);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($password) || empty($confirmPassword)) {
        redirectWithMessage('error', 'Please fill in all fields.');
    }

    if (strlen($username) < 3) {
        redirectWithMessage('error', 'Username must be at least 3 characters long.');
    }

    if ($password !== $confirmPassword) {
        redirectWithMessage('error', 'Password and confirm password do not match.');
    }

    if (!isStrongPassword($password)) {
        redirectWithMessage('error', 'Password must be at least 12 characters and include lowercase, uppercase, number, and special character.');
    }

    $checkSql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->execute([$username]);

    if ($stmt->fetch()) {
        redirectWithMessage('error', 'Username already exists.');
    }

    $salt = bin2hex(random_bytes(16));
    $combinedPassword = $password . $salt . PEPPER;
    $passwordHash = hash('sha256', $combinedPassword);

    $insertSql = "INSERT INTO users (id, username, password_hash, salt) VALUES (NULL, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->execute([$username, $passwordHash, $salt]);

    redirectWithMessage('success', 'Registered successfully! You can now log in.', 'login');
}

header("Location: index.php?mode=register");
exit();
?>