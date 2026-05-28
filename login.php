<?php
session_start();

require 'db.php';
require 'config.php';

function redirectWithMessage($type, $message, $mode = 'login') {
    header("Location: index.php?" . $type . "=" . rawurlencode($message) . "&mode=" . rawurlencode($mode));
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        redirectWithMessage('error', 'Please enter your username and password.');
    }

    $sql = "SELECT password_hash, salt FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        redirectWithMessage('error', 'Database error. Please try again.');
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($storedHash, $storedSalt);
        $stmt->fetch();

        $combinedPassword = $password . $storedSalt . PEPPER;
        $loginHash = hash('sha256', $combinedPassword);

        if (hash_equals($storedHash, $loginHash)) {
            $_SESSION['username'] = $username;
            $_SESSION['login_success'] = 'You have logged in successfully.';

            $stmt->close();
            header("Location: dashboard.php?success=" . rawurlencode("You have logged in successfully."));
            exit();
        }
    }

    $stmt->close();
    redirectWithMessage('error', 'Invalid username or password.');
}

header("Location: index.php?mode=login");
exit();
?>