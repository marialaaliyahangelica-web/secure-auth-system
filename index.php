<?php
session_start();

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'login';
$isRegister = $mode === 'register';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Auth System</title>

    <link rel="stylesheet" href="assets/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #1d1f24;
            font-size: 16px;
            cursor: pointer;
            padding: 4px;
            z-index: 5;
        }

        .password-toggle i {
            position: static !important;
            transform: none !important;
            color: inherit;
        }

        .password-toggle:hover {
            color: #5e44f3;
        }
    </style>
</head>
<body>

<div class="page-shell">
    <div class="auth-container <?php echo $isRegister ? 'active' : ''; ?>" id="authContainer">

        <?php if (isset($_GET['success'])): ?>
            <div class="notif success-notif">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="notif error-notif">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <!-- REGISTER FORM -->
        <div class="form-panel register-panel">
            <form action="register.php" method="POST" class="form-content" id="registerForm">
                <h1>Registration</h1>

                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" minlength="3" required>
                    <i class="fa-solid fa-user"></i>
                </div>

                <div class="input-box">
                    <input type="password" name="password" id="registerPassword" placeholder="Password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('registerPassword', this)">
                        <i class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>

                <!-- Password Strength Meter -->
                <div class="password-meter">
                    <div class="meter-header">
                        <span>Password Strength</span>
                        <strong id="strengthText">Start typing</strong>
                    </div>

                    <div class="meter-bar">
                        <div class="meter-fill" id="strengthFill"></div>
                    </div>

                    <ul class="requirement-list">
                        <li data-rule="lowercase"><i class="fa-regular fa-circle"></i> Lowercase letter</li>
                        <li data-rule="uppercase"><i class="fa-regular fa-circle"></i> Uppercase letter</li>
                        <li data-rule="digit"><i class="fa-regular fa-circle"></i> Number</li>
                        <li data-rule="symbol"><i class="fa-regular fa-circle"></i> Special character</li>
                        <li data-rule="length"><i class="fa-regular fa-circle"></i> At least 12 characters</li>
                    </ul>
                </div>

                <div class="input-box confirm-box">
                    <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm Password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword', this)">
                        <i class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>

                <p class="match-message" id="matchMessage"></p>

                <button type="submit" class="main-btn">Register</button>
            </form>
        </div>

        <!-- LOGIN FORM -->
        <div class="form-panel login-panel">
            <form action="login.php" method="POST" class="form-content">
                <h1>Login</h1>

                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required>
                    <i class="fa-solid fa-user"></i>
                </div>

                <div class="input-box">
                    <input type="password" name="password" id="loginPassword" placeholder="Password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('loginPassword', this)">
                        <i class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>

                <div class="forgot-link">
                    <a href="#">Forgot Password?</a>
                </div>

                <button type="submit" class="main-btn">Login</button>
            </form>
        </div>

        <!-- BLUE PANEL -->
        <div class="blue-panel">
            <div class="panel-message left-message">
                <h2>Hello, Welcome!</h2>
                <p>Don't have an account?</p>
                <button type="button" class="switch-btn" onclick="goRegister()">Register</button>
            </div>

            <div class="panel-message right-message">
                <h2>Welcome Back!</h2>
                <p>Already have an account?</p>
                <button type="button" class="switch-btn" onclick="goLogin()">Login</button>
            </div>
        </div>

    </div>
</div>

<script>
function goRegister() {
    document.getElementById('authContainer').classList.add('active');
}

function goLogin() {
    document.getElementById('authContainer').classList.remove('active');
}

function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-solid fa-eye';
    } else {
        input.type = 'password';
        icon.className = 'fa-solid fa-eye-slash';
    }
}

const passwordInput = document.getElementById('registerPassword');
const confirmInput = document.getElementById('confirmPassword');
const strengthText = document.getElementById('strengthText');
const strengthFill = document.getElementById('strengthFill');
const matchMessage = document.getElementById('matchMessage');
const registerForm = document.getElementById('registerForm');

function getPasswordChecks(password) {
    return {
        lowercase: /[a-z]/.test(password),
        uppercase: /[A-Z]/.test(password),
        digit: /[0-9]/.test(password),
        symbol: /[^a-zA-Z0-9]/.test(password),
        length: password.length >= 12
    };
}

function updatePasswordMeter() {
    const password = passwordInput.value;
    const checks = getPasswordChecks(password);
    const score = Object.values(checks).filter(Boolean).length;

    document.querySelectorAll('.requirement-list li').forEach((item) => {
        const rule = item.getAttribute('data-rule');
        const icon = item.querySelector('i');

        if (checks[rule]) {
            item.classList.add('valid');
            icon.className = 'fa-solid fa-circle-check';
        } else {
            item.classList.remove('valid');
            icon.className = 'fa-regular fa-circle';
        }
    });

    strengthFill.className = 'meter-fill';

    if (password.length === 0) {
        strengthText.textContent = 'Start typing';
        strengthFill.style.width = '0%';
    } else if (score <= 2) {
        strengthText.textContent = 'Weak';
        strengthFill.style.width = '35%';
        strengthFill.classList.add('weak');
    } else if (score <= 4) {
        strengthText.textContent = 'Medium';
        strengthFill.style.width = '70%';
        strengthFill.classList.add('medium');
    } else {
        strengthText.textContent = 'Strong';
        strengthFill.style.width = '100%';
        strengthFill.classList.add('strong');
    }

    updateMatchMessage();
}

function updateMatchMessage() {
    const password = passwordInput.value;
    const confirmPassword = confirmInput.value;

    matchMessage.className = 'match-message';

    if (confirmPassword.length === 0) {
        matchMessage.textContent = '';
        return;
    }

    if (password === confirmPassword) {
        matchMessage.textContent = 'Passwords match.';
        matchMessage.classList.add('match');
    } else {
        matchMessage.textContent = 'Passwords do not match.';
        matchMessage.classList.add('no-match');
    }
}

passwordInput.addEventListener('input', updatePasswordMeter);
confirmInput.addEventListener('input', updateMatchMessage);

registerForm.addEventListener('submit', function(event) {
    const password = passwordInput.value;
    const confirmPassword = confirmInput.value;
    const checks = getPasswordChecks(password);
    const isStrong = Object.values(checks).every(Boolean);

    if (!isStrong) {
        event.preventDefault();
        alert('Password must be strong: 12 characters with lowercase, uppercase, number, and special character.');
        return;
    }

    if (password !== confirmPassword) {
        event.preventDefault();
        alert('Password and confirm password must match.');
    }
});
</script>

</body>
</html>