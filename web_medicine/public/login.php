<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once("../config/db.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please fill in all fields";
    } else {
        $stmt = $conn->prepare(
            "SELECT id, full_name, password FROM users WHERE email=?"
        );

        if (!$stmt) {
            die("DB Error: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id, $name, $hash);

        if ($stmt->fetch() && password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['name'] = $name;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | HealthTrack+</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
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

.login-wrapper {
    display: flex;
    height: 100vh;
}

/* LEFT IMAGE */
.login-image {
    flex: 1;
    background: url("../assets/css/login.jpg") center/cover no-repeat;
}

/* RIGHT FORM */
.login-form {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #ffffff;
}

.form-box {
    width: 320px;
}

.logo {
    text-align: center;
    color: #6c63ff;
    margin-bottom: 15px;
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

input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.options {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    margin-bottom: 15px;
}

button {
    width: 100%;
    padding: 10px;
    background: #4f46e5;
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 15px;
    cursor: pointer;
}

button:hover {
    background: #4338ca;
}

.links {
    text-align: center;
    margin-top: 15px;
    font-size: 13px;
}

.links a {
    color: #555;
    text-decoration: none;
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
    .login-wrapper {
        flex-direction: column;
    }
    .login-image {
        height: 40vh;
    }
}

    </style>
<body>

<div class="login-wrapper">

    <!-- LEFT IMAGE -->
    <div class="login-image"></div>

    <!-- RIGHT FORM -->
    <div class="login-form">
        <div class="form-box">

            <h2 class="logo">HealthTrack+</h2>
            <h3>Welcome Back</h3>

            <?php if ($error): ?>
                <div class="error-box">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <label>Email Address</label>
                <input type="email" name="email" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <div class="options">
                    <label><input type="checkbox"> Remember me</label>
                </div>

                <button type="submit">Log In</button>
                <a href="/web_medicine/public/login.php" class="btn">Already </a>
                <div class="links">
    <p style="margin-top:10px;">
        Don’t have an account?
        <a href="register.php" style="font-weight:600;color:#4f46e5;">
            Please register
        </a>
    </p>
</div>
            </form>

            

        </div>
    </div>

</div>

</body>
</html>
