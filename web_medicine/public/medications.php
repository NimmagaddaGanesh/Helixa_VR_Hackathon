<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once("../config/db.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$messageType = "";

if(isset($_POST['add'])){
    $uid = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $dosage = trim($_POST['dosage']);
    $timing = trim($_POST['timing']);
    
    // Validate required inputs
    if(empty($name) || empty($dosage) || empty($timing)){
        $message = "Please fill all required fields.";
        $messageType = "danger";
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = mysqli_prepare($conn, "INSERT INTO medications(user_id, medicine_name, dosage, timing) VALUES(?, ?, ?, ?)");
        
        mysqli_stmt_bind_param($stmt, "isss", $uid, $name, $dosage, $timing);
        
        if(mysqli_stmt_execute($stmt)){
            $message = "Medication added successfully!";
            $messageType = "success";
        } else {
            $message = "Error adding medication. Please try again.";
            $messageType = "danger";
        }
        
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Medications | HealthTrack+</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
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

/* MAIN CONTENT AREA */
.main-content{
    margin-left:260px;
    min-height:100vh;
    background:linear-gradient(rgba(0,0,0,.5),rgba(0,0,0,.5)),
    url("https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1600&q=80");
    background-size:cover;
    background-position:center;
    background-attachment:fixed;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:30px 20px;
}

/* Glass Card */
.med-card{
    width:60%;
    max-width:700px;
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(15px);
    -webkit-backdrop-filter:blur(15px);
    border-radius:24px;
    padding:50px 45px;
    color:#fff;
    animation:slideFade 0.9s ease;
    box-shadow: 0 12px 40px rgba(0,0,0,0.35);
    border: 1px solid rgba(255,255,255,0.2);
}

.med-card h3{
    text-align:center;
    margin-bottom:35px;
    font-size:34px;
    font-weight:700;
    letter-spacing:0.5px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

/* Inputs */
.med-card input,
.med-card select,
.med-card textarea{
    background:rgba(255,255,255,0.95);
    height:56px;
    font-size:17px;
    border-radius:12px;
    border: 2px solid rgba(255,255,255,0.3);
    transition: all 0.3s ease;
    padding:12px 18px;
    font-weight:400;
    color:#2c3e50;
}

.med-card textarea{
    height: auto;
    min-height: 110px;
    resize: vertical;
    padding-top:15px;
}

.med-card input::placeholder,
.med-card select::placeholder,
.med-card textarea::placeholder{
    color:#7f8c8d;
    font-weight:400;
}

.med-card input:focus,
.med-card select:focus,
.med-card textarea:focus{
    background:rgba(255,255,255,1);
    border-color: #198754;
    box-shadow: 0 0 0 0.3rem rgba(25,135,84,.25);
    transform:translateY(-2px);
}

/* Button */
.med-card button{
    height:58px;
    font-size:18px;
    border-radius:12px;
    font-weight: 600;
    transition: all 0.3s ease;
    letter-spacing:0.5px;
    box-shadow: 0 4px 15px rgba(25,135,84,.3);
}

.med-card button:hover{
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(25,135,84,.5);
}

/* Animation */
@keyframes slideFade{
    from{
        opacity:0;
        transform:translateY(50px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* Medication List */
.med-list{
    width:60%;
    max-width:700px;
    margin-top:25px;
    animation:slideFade 1.2s ease;
}

.med-item{
    background:rgba(255,255,255,0.95);
    border-radius:16px;
    padding:22px 26px;
    margin-bottom:16px;
    box-shadow:0 6px 20px rgba(0,0,0,.2);
    transition: all 0.3s ease;
    border: 1px solid rgba(255,255,255,0.5);
    position: relative;
}

.med-item:hover{
    transform: translateX(8px);
    box-shadow:0 8px 25px rgba(0,0,0,.25);
    background:rgba(255,255,255,1);
}

.med-item strong{
    color: #2c3e50;
    font-size: 19px;
    font-weight:600;
    display: block;
    margin-bottom: 10px;
}

.med-item small{
    color: #7f8c8d;
    font-size:14px;
    font-weight:500;
}

.med-item .dosage-info{
    color: #16a085;
    font-weight: 500;
    font-size: 16px;
    display: block;
    margin-top: 8px;
}

.med-item .timing-info{
    color: #e67e22;
    font-size: 15px;
    margin-top: 8px;
    display: block;
    line-height: 1.6;
}

.med-item .badge{
    position: absolute;
    top: 12px;
    right: 15px;
    font-size: 11px;
}

.alert{
    border-radius: 12px;
    margin-bottom: 25px;
    font-size:15px;
    font-weight:500;
    border:none;
}

.no-meds{
    text-align: center;
    padding: 40px;
    background: rgba(255,255,255,0.12);
    border-radius: 16px;
    color: #fff;
    font-size: 17px;
    font-weight:500;
}

.input-label{
    font-size: 15px;
    color: rgba(255,255,255,0.95);
    margin-bottom: 10px;
    display: block;
    font-weight:500;
    letter-spacing:0.3px;
}

.form-group{
    margin-bottom:20px;
}

@media (max-width: 1024px){
    .med-card,
    .med-list{
        width:80%;
    }
}

@media (max-width: 768px){
    .sidebar{
        width:200px;
    }
    .main-content{
        margin-left:200px;
    }
    .med-card,
    .med-list{
        width:90%;
        padding:35px 30px;
    }
    
    .med-card h3{
        font-size:28px;
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
    <a class="active" href="medications.php">Medications</a>
    <a href="diet_plan.php">Diet Plan</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div style="width:100%; display:flex; flex-direction:column; align-items:center;">
        <!-- Form Card -->
        <div class="med-card">
            <h3>💊 Medications</h3>

            <?php if($message): ?>
            <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <input class="form-control" name="name" placeholder="Medicine Name (e.g., Aspirin)" required maxlength="100">
                </div>
                
                <div class="form-group">
                    <input class="form-control" name="dosage" placeholder="Dosage (e.g., 500mg)" required maxlength="50">
                </div>
                
                <label class="input-label">⏰ Timing Instructions</label>
                <div class="form-group">
                    <textarea class="form-control" name="timing" placeholder="E.g., Twice daily - Morning 8 AM, Evening 8 PM" required maxlength="200"></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100" name="add">
                    ➕ Add Medication
                </button>
            </form>
        </div>

        <!-- Medication List -->
        <div class="med-list">
            <?php
            // Use prepared statement for SELECT query
            $stmt = mysqli_prepare($conn, "SELECT * FROM medications WHERE user_id = ? ORDER BY id DESC");
            mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($res) > 0){
                while($m = mysqli_fetch_assoc($res)){
                    // Escape output to prevent XSS
                    $name = htmlspecialchars($m['medicine_name']);
                    $dosage = htmlspecialchars($m['dosage']);
                    $timing = htmlspecialchars($m['timing']);
                    
                    echo "
                    <div class='med-item'>
                        <strong>💊 {$name}</strong>
                        <span class='dosage-info'>📊 {$dosage}</span>
                        <span class='timing-info'>🕐 {$timing}</span>
                    </div>";
                }
            } else {
                echo "<div class='no-meds'>💊 No medications found. Add your first medication above!</div>";
            }
            
            mysqli_stmt_close($stmt);
            ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>