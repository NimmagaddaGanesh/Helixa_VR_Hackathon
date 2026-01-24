<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once("../config/db.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name'] ?? '');
    $dob  = $_POST['dob'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || $dob === '' || $email === '' || $password === '' || $confirm === '') {
        $error = "Please fill in all fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {

        // Check email exists
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already exists";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users (full_name, dob, email, password) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("ssss", $name, $dob, $email, $hash);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit;
                
            } else {
                $error = "Registration failed";
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register | HealthTrack+</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", Arial, sans-serif;
}

body {
    height: 100vh;
    background: #f5f6fa;
}

.register-wrapper {
    display: flex;
    height: 100vh;
}

/* LEFT IMAGE */
.register-image {
    flex: 1;
    background: url("../assets/css/login1.jpg") center/cover no-repeat;
}

/* RIGHT FORM */
.register-form {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #ffffff;
}

.form-box {
    width: 340px;
    animation: slideIn 0.6s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.logo {
    text-align: center;
    color: #6c63ff;
    margin-bottom: 10px;
}

h3 {
    text-align: center;
    margin-bottom: 20px;
    font-weight: 500;
}

label {
    font-size: 14px;
    margin-bottom: 5px;
    display: block;
}

input {
    width: 100%;
    padding: 10px;
    margin-bottom: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button {
    width: 100%;
    padding: 11px;
    background: linear-gradient(135deg, #6c63ff, #4f46e5);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 15px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    opacity: 0.9;
}

.links {
    text-align: center;
    margin-top: 15px;
    font-size: 13px;
}

.links a {
    color: #4f46e5;
    text-decoration: none;
    font-weight: 600;
}

.links a:hover {
    text-decoration: underline;
}

.error-box {
    background: #ffe5e5;
    color: #b00020;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 15px;
    font-size: 14px;
    text-align: center;
}

/* MOBILE */
@media (max-width: 768px) {
    .register-wrapper {
        flex-direction: column;
    }
    .register-image {
        height: 40vh;
    }
}
</style>
</head>

<body>

<div class="register-wrapper">

    <!-- LEFT IMAGE -->
    <div class="register-image"></div>

    <!-- RIGHT FORM -->
    <div class="register-form">
        <div class="form-box">

            <h2 class="logo">HealthTrack+</h2>
            <h3>Create Account</h3>

            <?php if ($error): ?>
                <div class="error-box">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <label>Full Name</label>
                <input type="text" name="name" required>

                <label>Date of Birth</label>
                <input type="date" name="dob" required>

                <label>Email Address</label>
                <input type="email" name="email" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>

                <button type="submit">Register</button>
            </form>

            <div class="links">
                Already have an account?
                <a href="login.php">Login</a><br><br>
                <a href="index.php">← Back to Home</a>
            </div>

        </div>
    </div>

</div>

</body>
</html>
