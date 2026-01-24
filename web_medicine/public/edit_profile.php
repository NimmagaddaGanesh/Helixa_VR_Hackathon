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
$message = "";
$messageType = "";

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

if (!$profile) {
    $profile = [];
}

/* HANDLE FORM SUBMISSION */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone = trim($_POST['phone']);
    $gender = trim($_POST['gender']);
    $height = trim($_POST['height']);
    $weight = trim($_POST['weight']);
    $blood_group = trim($_POST['blood_group']);
    $dob = trim($_POST['dob']);
    
    $profile_photo = $profile['profile_photo'] ?? '';
    
    // Handle file upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'profile_' . $uid . '.' . $ext;
            $upload_path = '../uploads/' . $new_filename;
            
            // Create uploads directory if it doesn't exist
            if (!is_dir('../uploads')) {
                mkdir('../uploads', 0755, true);
            }
            
            // Delete old photo if exists
            if (!empty($profile['profile_photo']) && file_exists('../' . $profile['profile_photo'])) {
                unlink('../' . $profile['profile_photo']);
            }
            
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_path)) {
                $profile_photo = 'uploads/' . $new_filename;
            } else {
                $message = "Error uploading profile photo.";
                $messageType = "danger";
            }
        } else {
            $message = "Invalid file type. Only JPG, PNG, and GIF allowed.";
            $messageType = "danger";
        }
    }
    
    // Update user DOB
    if (!empty($dob)) {
        $stmt = $conn->prepare("UPDATE users SET dob=? WHERE id=?");
        $stmt->bind_param("si", $dob, $uid);
        $stmt->execute();
        $stmt->close();
    }
    
    // Check if profile exists
    $check_stmt = $conn->prepare("SELECT user_id FROM patient_profile WHERE user_id=?");
    $check_stmt->bind_param("i", $uid);
    $check_stmt->execute();
    $exists = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();
    
    if ($exists) {
        // Update existing profile
        $stmt = $conn->prepare("
            UPDATE patient_profile 
            SET phone=?, gender=?, height=?, weight=?, blood_group=?, profile_photo=?
            WHERE user_id=?
        ");
        $stmt->bind_param("ssssssi", $phone, $gender, $height, $weight, $blood_group, $profile_photo, $uid);
    } else {
        // Insert new profile
        $stmt = $conn->prepare("
            INSERT INTO patient_profile (user_id, phone, gender, height, weight, blood_group, profile_photo)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issssss", $uid, $phone, $gender, $height, $weight, $blood_group, $profile_photo);
    }
    
    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
        $messageType = "success";
        
        // Refresh profile data
        $stmt->close();
        $stmt = $conn->prepare("
            SELECT phone, gender, height, weight, blood_group, profile_photo
            FROM patient_profile
            WHERE user_id=?
        ");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        // Refresh user data
        $stmt = $conn->prepare("SELECT full_name, dob, email FROM users WHERE id=?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    } else {
        $message = "Error updating profile. Please try again.";
        $messageType = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile | HealthTrack+</title>
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

.edit-card {
    background:#fff;
    border-radius:20px;
    padding:40px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    max-width:800px;
    margin:0 auto;
}

.edit-card h3 {
    font-size:28px;
    font-weight:700;
    color:#2c3e50;
    margin-bottom:10px;
}

.form-label {
    font-weight:600;
    color:#495057;
    margin-bottom:8px;
    font-size:14px;
}

.form-control,
.form-select {
    height:50px;
    border-radius:10px;
    border:2px solid #e9ecef;
    padding:12px 18px;
    font-size:15px;
    transition:all 0.3s ease;
}

.form-control:focus,
.form-select:focus {
    border-color:#667eea;
    box-shadow:0 0 0 0.2rem rgba(102,126,234,.25);
}

/* CIRCULAR PROFILE PHOTO SECTION */
.photo-upload-section {
    text-align:center;
    margin-bottom:40px;
    padding:30px;
    background:#f8f9fa;
    border-radius:16px;
}

.circular-photo-wrapper {
    position:relative;
    width:150px;
    height:150px;
    margin:0 auto 20px;
}

.circular-photo {
    width:150px;
    height:150px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid #667eea;
    box-shadow:0 8px 20px rgba(0,0,0,0.15);
    background:#e9ecef;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:60px;
    color:#6c757d;
}

.circular-photo img {
    width:100%;
    height:100%;
    border-radius:50%;
    object-fit:cover;
}

.upload-photo-btn {
    display:inline-block;
    background:#28a745;
    color:#fff;
    padding:12px 30px;
    border-radius:10px;
    font-weight:600;
    font-size:15px;
    cursor:pointer;
    transition:all 0.3s ease;
    border:none;
    box-shadow:0 4px 12px rgba(40,167,69,0.3);
}

.upload-photo-btn:hover {
    background:#218838;
    transform:translateY(-2px);
    box-shadow:0 6px 16px rgba(40,167,69,0.4);
}

.upload-photo-btn::before {
    content:'📤';
    margin-right:8px;
}

#profile_photo {
    display:none;
}

.btn-save {
    background:linear-gradient(135deg,#667eea 0%, #764ba2 100%);
    color:#fff;
    border:none;
    padding:14px 40px;
    border-radius:10px;
    font-weight:600;
    font-size:16px;
    transition:all 0.3s ease;
}

.btn-save:hover {
    transform:translateY(-2px);
    box-shadow:0 6px 20px rgba(102,126,234,0.4);
    color:#fff;
}

.btn-cancel {
    background:#6c757d;
    color:#fff;
    border:none;
    padding:14px 40px;
    border-radius:10px;
    font-weight:600;
    font-size:16px;
    transition:all 0.3s ease;
}

.btn-cancel:hover {
    background:#5a6268;
    color:#fff;
    transform:translateY(-2px);
}

@media (max-width: 768px) {
    .sidebar {
        width:200px;
    }
    .main {
        margin-left:220px;
        padding:20px;
    }
    .edit-card {
        padding:25px;
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

<div class="edit-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>✏️ Edit Profile</h3>
            <p class="text-muted" style="margin:0;">Update your personal information</p>
        </div>
        <a href="profile.php" class="btn btn-cancel">← Back</a>
    </div>

    <?php if($message): ?>
    <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        
        <!-- CIRCULAR PROFILE PHOTO SECTION -->
        <div class="photo-upload-section">
            <h5 style="margin-bottom:25px;color:#2c3e50;font-weight:600;">Profile Photo</h5>
            
            <div class="circular-photo-wrapper">
                <div class="circular-photo" id="photoPreview">
                    <?php if (!empty($profile['profile_photo'])): ?>
                        <img src="../<?=htmlspecialchars($profile['profile_photo'])?>" alt="Profile Photo" id="previewImage">
                    <?php else: ?>
                        <span id="defaultIcon">👤</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <input type="file" name="profile_photo" id="profile_photo" accept="image/*">
            <label for="profile_photo" class="upload-photo-btn">Upload Photo</label>
            
            <p class="text-muted mt-3" style="font-size:13px;">JPG, PNG or GIF (Max 5MB)</p>
        </div>

        <!-- PERSONAL INFORMATION -->
        <h5 style="margin-bottom:20px;color:#2c3e50;border-bottom:2px solid #f0f0f0;padding-bottom:10px;">
            📋 Personal Information
        </h5>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" disabled>
                <small class="text-muted">Cannot be changed here</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
                <small class="text-muted">Cannot be changed here</small>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone" class="form-control" placeholder="Enter phone number" 
                       value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" maxlength="15">
            </div>
            <div class="col-md-6">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="dob" class="form-control" 
                       value="<?= htmlspecialchars($user['dob'] ?? '') ?>" max="<?= date('Y-m-d') ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
                <option value="">Select Gender</option>
                <option value="Male" <?= ($profile['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= ($profile['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                <option value="Other" <?= ($profile['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <!-- HEALTH INFORMATION -->
        <h5 style="margin:30px 0 20px;color:#2c3e50;border-bottom:2px solid #f0f0f0;padding-bottom:10px;">
            🏥 Health Information
        </h5>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Height (cm)</label>
                <input type="number" name="height" class="form-control" placeholder="e.g., 170" 
                       value="<?= htmlspecialchars($profile['height'] ?? '') ?>" min="50" max="300" step="0.1">
            </div>
            <div class="col-md-4">
                <label class="form-label">Weight (kg)</label>
                <input type="number" name="weight" class="form-control" placeholder="e.g., 70" 
                       value="<?= htmlspecialchars($profile['weight'] ?? '') ?>" min="20" max="500" step="0.1">
            </div>
            <div class="col-md-4">
                <label class="form-label">Blood Group</label>
                <select name="blood_group" class="form-select">
                    <option value="">Select Blood Group</option>
                    <option value="A+" <?= ($profile['blood_group'] ?? '') === 'A+' ? 'selected' : '' ?>>A+</option>
                    <option value="A-" <?= ($profile['blood_group'] ?? '') === 'A-' ? 'selected' : '' ?>>A-</option>
                    <option value="B+" <?= ($profile['blood_group'] ?? '') === 'B+' ? 'selected' : '' ?>>B+</option>
                    <option value="B-" <?= ($profile['blood_group'] ?? '') === 'B-' ? 'selected' : '' ?>>B-</option>
                    <option value="AB+" <?= ($profile['blood_group'] ?? '') === 'AB+' ? 'selected' : '' ?>>AB+</option>
                    <option value="AB-" <?= ($profile['blood_group'] ?? '') === 'AB-' ? 'selected' : '' ?>>AB-</option>
                    <option value="O+" <?= ($profile['blood_group'] ?? '') === 'O+' ? 'selected' : '' ?>>O+</option>
                    <option value="O-" <?= ($profile['blood_group'] ?? '') === 'O-' ? 'selected' : '' ?>>O-</option>
                </select>
            </div>
        </div>

        <!-- SUBMIT BUTTONS -->
        <div class="text-center mt-4">
            <button type="submit" name="update_profile" class="btn btn-save me-2">
                💾 Save Changes
            </button>
            <a href="profile.php" class="btn btn-cancel">
                ✖️ Cancel
            </a>
        </div>
    </form>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Image Preview with Circular Display
document.getElementById('profile_photo').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const photoPreview = document.getElementById('photoPreview');
            const defaultIcon = document.getElementById('defaultIcon');
            const previewImage = document.getElementById('previewImage');
            
            // Remove default icon if exists
            if (defaultIcon) {
                defaultIcon.remove();
            }
            
            // Update or create image element
            if (previewImage) {
                previewImage.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.id = 'previewImage';
                img.src = e.target.result;
                img.alt = 'Profile Photo';
                photoPreview.appendChild(img);
            }
        }
        
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>
</body>
</html>