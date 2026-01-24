<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once("../config/db.php");

/* LOGIN CHECK */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

/* FETCH USER */
$stmt = $conn->prepare("SELECT full_name, dob, email FROM users WHERE id=?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* FETCH PATIENT PROFILE */
$stmt = $conn->prepare("
    SELECT phone, gender, height, weight, blood_group, profile_photo
    FROM patient_profile
    WHERE user_id=?
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* IMPORTANT FIX */
if (!$profile) {
    $profile = [];
}

/* SAFE VALUES */
$height = $profile['height'] ?? '—';
$weight = $profile['weight'] ?? '—';
$gender = $profile['gender'] ?? '—';
$blood  = $profile['blood_group'] ?? '—';
$phone  = $profile['phone'] ?? '—';

/* AGE */
$age = '—';
if (!empty($user['dob'])) {
    $dob = new DateTime($user['dob']);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
}

/* BMI */
$bmi = '—';
$bmiStatus = '';
$bmiColor = '#6c757d';
if (is_numeric($height) && is_numeric($weight) && $height > 0) {
    $h = $height / 100;
    $bmi = round($weight / ($h * $h), 1);
    
    if ($bmi < 18.5) {
        $bmiStatus = 'Underweight';
        $bmiColor = '#ffc107';
    } elseif ($bmi < 25) {
        $bmiStatus = 'Normal';
        $bmiColor = '#28a745';
    } elseif ($bmi < 30) {
        $bmiStatus = 'Overweight';
        $bmiColor = '#fd7e14';
    } else {
        $bmiStatus = 'Obese';
        $bmiColor = '#dc3545';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile | HealthTrack+</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body { 
    background:#f5f7fb;
    font-family:'Poppins', sans-serif;
    margin:0;
    padding:0;
}

/* SIDEBAR */
.sidebar {
    width:260px;
    height:100vh;
    position:fixed;
    background:linear-gradient(180deg,#2563eb,#1e40af);
    color:#fff;
    padding:25px;
    z-index:1000;
}
.sidebar h4 {
    margin-bottom:30px;
    font-size:20px;
    font-weight:600;
}
.sidebar a {
    display:block;
    color:#e0e7ff;
    text-decoration:none;
    padding:12px 15px;
    border-radius:8px;
    margin-bottom:8px;
    transition: all 0.3s ease;
}
.sidebar a.active,
.sidebar a:hover {
    background:rgba(255,255,255,0.15);
    color:#fff;
}

/* MAIN CONTENT */
.main { 
    margin-left:280px;
    padding:40px;
    min-height:100vh;
}

/* PROFILE HEADER CARD */
.profile-header-card {
    background:linear-gradient(135deg,#667eea 0%, #764ba2 100%);
    color:#fff;
    border-radius:20px;
    padding:40px;
    box-shadow:0 10px 30px rgba(102, 126, 234, 0.3);
    margin-bottom:30px;
    position:relative;
    overflow:hidden;
}

.profile-header-card::before {
    content:'';
    position:absolute;
    top:-50%;
    right:-50%;
    width:200%;
    height:200%;
    background:radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
}

.profile-photo-container {
    position:relative;
    display:inline-block;
}

.profile-photo {
    width:140px;
    height:140px;
    border-radius:50%;
    object-fit:cover;
    border:5px solid rgba(255,255,255,0.3);
    box-shadow:0 8px 20px rgba(0,0,0,0.2);
}

.profile-icon {
    font-size:80px;
    width:140px;
    height:140px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(255,255,255,0.2);
    border-radius:50%;
    border:5px solid rgba(255,255,255,0.3);
}

/* INFO CARDS */
.info-card {
    background:#fff;
    border-radius:18px;
    padding:30px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    margin-bottom:20px;
    transition:all 0.3s ease;
}

.info-card:hover {
    transform:translateY(-5px);
    box-shadow:0 15px 40px rgba(0,0,0,0.12);
}

.info-card h5 {
    font-size:20px;
    font-weight:600;
    margin-bottom:25px;
    color:#2c3e50;
    border-bottom:2px solid #f0f0f0;
    padding-bottom:12px;
}

.stat-box {
    text-align:center;
    padding:20px;
    background:#f8f9fa;
    border-radius:12px;
    transition:all 0.3s ease;
}

.stat-box:hover {
    background:#e9ecef;
    transform:scale(1.05);
}

.stat-box .value {
    font-size:28px;
    font-weight:700;
    color:#2c3e50;
    display:block;
    margin-bottom:5px;
}

.stat-box .label {
    font-size:13px;
    color:#6c757d;
    font-weight:500;
    text-transform:uppercase;
    letter-spacing:0.5px;
}

/* EDIT BUTTON */
.edit-profile-btn {
    background:#fff;
    color:#667eea;
    border:none;
    padding:12px 30px;
    border-radius:10px;
    font-weight:600;
    font-size:16px;
    transition:all 0.3s ease;
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
}

.edit-profile-btn:hover {
    background:#f8f9fa;
    transform:translateY(-2px);
    box-shadow:0 6px 20px rgba(0,0,0,0.15);
    color:#5568d3;
}

.edit-profile-btn::before {
    content:'✏️';
    margin-right:8px;
}

/* BADGES */
.condition-badge {
    display:inline-block;
    padding:8px 16px;
    border-radius:20px;
    font-size:14px;
    font-weight:500;
    margin-right:10px;
    margin-bottom:10px;
}

.badge-diabetes {
    background:#fff3cd;
    color:#856404;
}

.badge-hypertension {
    background:#f8d7da;
    color:#721c24;
}

/* BMI INDICATOR */
.bmi-indicator {
    display:inline-block;
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
    margin-top:5px;
}

@media (max-width: 768px) {
    .sidebar {
        width:200px;
    }
    .main {
        margin-left:220px;
        padding:20px;
    }
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4>💙 Helixa HealthTracker+</h4>
    <a href="dashboard.php">Dashboard</a>
    <a href="health_records.php">Health Records</a>
    <a href="medications.php">Medications</a>
    <a href="diet_plan.php">Diet Plan</a>
    <a class="active" href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 style="font-weight:700;color:#2c3e50;">My Profile</h2>
        <p class="text-muted" style="margin:0;">Manage your personal information</p>
    </div>
    <a href="edit_profile.php" class="btn edit-profile-btn">Edit Profile</a>
</div>

<!-- PROFILE HEADER CARD -->
<div class="profile-header-card">
    <div class="row align-items-center">
        <div class="col-md-3 text-center">
            <div class="profile-photo-container">
                <?php if (!empty($profile['profile_photo'])): ?>
                    <img src="../<?=htmlspecialchars($profile['profile_photo'])?>" 
                         class="profile-photo" 
                         alt="Profile Photo">
                <?php else: ?>
                    <div class="profile-icon">👤</div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-md-9">
            <h3 style="font-weight:700;font-size:32px;margin-bottom:10px;">
                <?= htmlspecialchars($user['full_name'] ?? 'User') ?>
            </h3>
            <p style="font-size:18px;opacity:0.9;margin-bottom:5px;">
                <?= $age ?> years old • <?= htmlspecialchars($gender) ?>
            </p>
            <p style="font-size:16px;opacity:0.8;margin-bottom:15px;">
                📧 <?= htmlspecialchars($user['email'] ?? 'No email') ?>
            </p>
            <?php if ($phone !== '—'): ?>
            <p style="font-size:16px;opacity:0.8;margin:0;">
                📱 <?= htmlspecialchars($phone) ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- INFORMATION CARDS -->
<div class="row">

    <!-- PHYSICAL INFORMATION -->
    <div class="col-md-6">
        <div class="info-card">
            <h5>📊 Physical Information</h5>
            <div class="row g-3">
                <div class="col-6">
                    <div class="stat-box" style="background:#e3f2fd;">
                        <span class="value" style="color:#1976d2;"><?= $height ?></span>
                        <span class="label">Height (cm)</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box" style="background:#f3e5f5;">
                        <span class="value" style="color:#7b1fa2;"><?= $weight ?></span>
                        <span class="label">Weight (kg)</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box" style="background:#ffebee;">
                        <span class="value" style="color:#c62828;"><?= $blood ?></span>
                        <span class="label">Blood Group</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box" style="background:#e8f5e9;">
                        <span class="value" style="color:#2e7d32;"><?= $bmi ?></span>
                        <span class="label">BMI</span>
                        <?php if ($bmiStatus): ?>
                        <span class="bmi-indicator" style="background:<?= $bmiColor ?>;color:#fff;">
                            <?= $bmiStatus ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MEDICAL INFORMATION -->
    <div class="col-md-6">
        <div class="info-card">
            <h5>🏥 Medical Information</h5>
            <div style="margin-bottom:20px;">
                <p style="color:#6c757d;margin-bottom:12px;font-weight:500;">Chronic Conditions:</p>
                <span class="condition-badge badge-diabetes">🩺 Type 2 Diabetes</span>
                <span class="condition-badge badge-hypertension">❤️ Hypertension</span>
            </div>
            <div style="background:#f8f9fa;padding:15px;border-radius:10px;">
                <p style="color:#6c757d;margin:0;font-weight:500;">💊 Allergies:</p>
                <p style="color:#2c3e50;margin:5px 0 0 0;">No known allergies</p>
            </div>
        </div>
    </div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>