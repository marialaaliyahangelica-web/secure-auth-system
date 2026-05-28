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
    $stmt->execute([$username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $combinedPassword = $password . $user['salt'] . PEPPER;
        $loginHash = hash('sha256', $combinedPassword);

        if (hash_equals($user['password_hash'], $loginHash)) {
            $_SESSION['username'] = $username;
            $_SESSION['login_success'] = 'You have logged in successfully.';

            header("Location: dashboard.php?success=" . rawurlencode("You have logged in successfully."));
            exit();
        }
    }

    redirectWithMessage('error', 'Invalid username or password.');
}

header("Location: index.php?mode=login");
exit();
?>