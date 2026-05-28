<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="dashboard-wrapper">
    <div class="dashboard-card">

        <?php if (isset($_GET['success'])): ?>
            <div class="dashboard-alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-badge">Secure Access</div>

        <h1>Login Successful</h1>

        <p>Welcome, <strong><?php echo htmlspecialchars($username); ?></strong></p>

        <a href="logout.php" class="dashboard-btn">Logout</a>
    </div>
</div>

</body>
</html>