<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];
$name = $_SESSION['user_name'] ?? 'User';

$records = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM health_records WHERE user_id=$uid"));
$meds    = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM medications WHERE user_id=$uid"));
$diet    = mysqli_num_rows(mysqli_query($conn,"SELECT id FROM diet_plan WHERE user_id=$uid"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard | HealthTrack+</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background:#f5f7fb;
    font-family: "Segoe UI", sans-serif;
}
.sidebar {
    width:260px;
    height:100vh;
    position:fixed;
    background:linear-gradient(180deg,#2563eb,#1e40af);
    color:#fff;
    padding:25px;
}
.sidebar h4 {
    margin-bottom:30px;
}
.sidebar a {
    display:block;
    color:#e0e7ff;
    text-decoration:none;
    padding:12px 15px;
    border-radius:8px;
    margin-bottom:8px;
}
.sidebar a.active,
.sidebar a:hover {
    background:rgba(255,255,255,0.15);
    color:#fff;
}
.main {
    margin-left:260px;
    padding:30px;
}
.card-box {
    background:#fff;
    border-radius:14px;
    padding:20px;
    box-shadow:0 10px 25px rgba(0,0,0,.05);
}
.stat-icon {
    width:45px;
    height:45px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
    color:#fff;
}
.icon-blue { background:#3b82f6; }
.icon-green { background:#22c55e; }
.icon-purple { background:#a855f7; }
.icon-orange { background:#f97316; }

/* AI Assistant Icon */
.ai-assistant-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    z-index: 1000;
    animation: pulse 2s infinite;
}

.ai-assistant-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 30px rgba(102, 126, 234, 0.6);
}

.ai-assistant-btn::before {
    content: '🤖';
    font-size: 28px;
    animation: bounce 1s ease-in-out infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }
    50% {
        box-shadow: 0 8px 30px rgba(102, 126, 234, 0.7);
    }
    100% {
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}

/* Tooltip */
.ai-assistant-btn::after {
    content: 'AI Assistant';
    position: absolute;
    right: 70px;
    background: #2c3e50;
    color: #fff;
    padding: 8px 15px;
    border-radius: 8px;
    white-space: nowrap;
    font-size: 14px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.ai-assistant-btn:hover::after {
    opacity: 1;
}

/* Check Progress Button */
.check-progress-btn {
    display: inline-block;
    background: #3b82f6;
    color: #fff;
    padding: 12px 30px;
    border-radius: 10px;
    text-decoration: none;
    font-size: 16px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    margin-top: 20px;
}

.check-progress-btn:hover {
    background: #2563eb;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4>💙 Helixa HealthTracker+</h4>
    <a class="active" href="dashboard.php">Dashboard</a>
    <a href="health_records.php">Health Records</a>
    <a href="medications.php">Medications</a>
    <a href="diet_plan.php">Diet Plan</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<h3 class="fw-bold">Welcome back, <?= htmlspecialchars($name) ?></h3>
<p class="text-muted">Here's your health overview for today</p>

<!-- STATS -->
<div class="row g-4 mt-2">
    <div class="col-md-3">
        <div class="card-box d-flex justify-content-between">
            <div>
                <small class="text-muted">Health Records</small>
                <h3><?= $records ?></h3>
            </div>
            <div class="stat-icon icon-blue">📄</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-box d-flex justify-content-between">
            <div>
                <small class="text-muted">Active Medications</small>
                <h3><?= $meds ?></h3>
            </div>
            <div class="stat-icon icon-green">💊</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-box d-flex justify-content-between">
            <div>
                <small class="text-muted">Diet Plans</small>
                <h3><?= $diet ?></h3>
            </div>
            <div class="stat-icon icon-purple">🍎</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card-box d-flex justify-content-between">
            <div>
                <small class="text-muted">Upcoming Checkups</small>
                <h3>2</h3>
            </div>
            <div class="stat-icon icon-orange">📅</div>
        </div>
    </div>
</div>

<!-- CONTENT -->
<div class="row mt-4 g-4">
    <div class="col-md-6">
        <div class="card-box">
            <h5>Today's Medications</h5>
            <hr>
            <p>Metformin – 2:00 PM</p>
            <p>Lisinopril – 6:00 PM</p>
            <p>Vitamin D – 8:00 PM</p>
            
            <a href="https://helixa-2erpyikhakre5tpaxlspgg.streamlit.app/" class="check-progress-btn" target="_blank">
                📊 Check Your Progress
            </a>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-box">
            <h5>Recent Records</h5>
            <hr>
            <p>Diabetes Checkup</p>
            <p>Hypertension Monitor</p>
        </div>
    </div>
</div>

</div>

<!-- AI Assistant Button -->
<a href="https://helixa-chatbot-n6sgwr9a6kccnzvjs7k9aj.streamlit.app/" class="ai-assistant-btn" target="_blank" title="AI Assistant"></a>

</body>
</html>