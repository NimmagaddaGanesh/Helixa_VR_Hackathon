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
    $meal = trim($_POST['meal']);
    $food = trim($_POST['food']);
    $cal = trim($_POST['cal']);
    $carbs = trim($_POST['carbs']);
    $protein = trim($_POST['protein']);
    $fat = trim($_POST['fat']);
    
    // Validate required inputs
    if(empty($meal) || empty($food) || empty($cal)){
        $message = "Please fill all required fields.";
        $messageType = "danger";
    } elseif(!is_numeric($cal) || $cal < 0){
        $message = "Please enter a valid calorie value.";
        $messageType = "danger";
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = mysqli_prepare($conn, "INSERT INTO diet_plan(user_id, meal_type, food_name, calories, carbs, protein, fat) VALUES(?, ?, ?, ?, ?, ?, ?)");
        
        mysqli_stmt_bind_param($stmt, "issssss", $uid, $meal, $food, $cal, $carbs, $protein, $fat);
        
        if(mysqli_stmt_execute($stmt)){
            $message = "Diet entry added successfully!";
            $messageType = "success";
        } else {
            $message = "Error adding diet entry. Please try again.";
            $messageType = "danger";
        }
        
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Diet Plan | HealthTrack+</title>
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
    url("https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=1600&q=80");
    background-size:cover;
    background-position:center;
    background-attachment:fixed;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:30px 20px;
}

/* Glass Card */
.diet-card{
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

.diet-card h3{
    text-align:center;
    margin-bottom:35px;
    font-size:34px;
    font-weight:700;
    letter-spacing:0.5px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

/* Inputs */
.diet-card input,
.diet-card select{
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

.diet-card input::placeholder,
.diet-card select::placeholder{
    color:#7f8c8d;
    font-weight:400;
}

.diet-card input:focus,
.diet-card select:focus{
    background:rgba(255,255,255,1);
    border-color: #ffc107;
    box-shadow: 0 0 0 0.3rem rgba(255,193,7,.25);
    transform:translateY(-2px);
}

/* Button */
.diet-card button{
    height:58px;
    font-size:18px;
    border-radius:12px;
    font-weight: 600;
    transition: all 0.3s ease;
    letter-spacing:0.5px;
    box-shadow: 0 4px 15px rgba(255,193,7,.3);
}

.diet-card button:hover{
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(255,193,7,.5);
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

/* Diet List */
.diet-list{
    width:60%;
    max-width:700px;
    margin-top:25px;
    animation:slideFade 1.2s ease;
}

.diet-item{
    background:rgba(255,255,255,0.95);
    border-radius:16px;
    padding:22px 26px;
    margin-bottom:16px;
    box-shadow:0 6px 20px rgba(0,0,0,.2);
    transition: all 0.3s ease;
    border: 1px solid rgba(255,255,255,0.5);
    position: relative;
}

.diet-item:hover{
    transform: translateX(8px);
    box-shadow:0 8px 25px rgba(0,0,0,.25);
    background:rgba(255,255,255,1);
}

.diet-item strong{
    color: #2c3e50;
    font-size: 19px;
    font-weight:600;
    display: block;
    margin-bottom: 12px;
}

.diet-item .meal-badge{
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 10px;
}

.meal-breakfast{
    background-color: #fff3cd;
    color: #856404;
}

.meal-lunch{
    background-color: #d1ecf1;
    color: #0c5460;
}

.meal-dinner{
    background-color: #f8d7da;
    color: #721c24;
}

.meal-snack{
    background-color: #d4edda;
    color: #155724;
}

.diet-item .nutrients{
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-top: 14px;
}

.nutrient-box{
    text-align: center;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 10px;
    font-size: 13px;
}

.nutrient-box .value{
    display: block;
    font-weight: 600;
    color: #2c3e50;
    font-size: 16px;
}

.nutrient-box .label{
    display: block;
    color: #7f8c8d;
    font-size: 12px;
    margin-top: 3px;
}

.alert{
    border-radius: 12px;
    margin-bottom: 25px;
    font-size:15px;
    font-weight:500;
    border:none;
}

.no-diet{
    text-align: center;
    padding: 40px;
    background: rgba(255,255,255,0.12);
    border-radius: 16px;
    color: #fff;
    font-size: 17px;
    font-weight:500;
}

.nutrition-grid{
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.input-label{
    font-size: 15px;
    color: rgba(255,255,255,0.95);
    margin-bottom: 10px;
    display: block;
    font-weight:500;
    letter-spacing:0.3px;
}

.total-calories{
    background: rgba(255,255,255,0.12);
    backdrop-filter:blur(10px);
    padding: 25px;
    border-radius: 16px;
    text-align: center;
    margin-bottom: 20px;
    border: 1px solid rgba(255,255,255,0.2);
}

.total-calories .big-number{
    font-size: 42px;
    font-weight: 700;
    color: #ffc107;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.total-calories .label{
    font-size: 16px;
    color: rgba(255,255,255,0.9);
    font-weight:500;
    letter-spacing:0.5px;
}

.form-group{
    margin-bottom:20px;
}

@media (max-width: 1024px){
    .diet-card,
    .diet-list{
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
    .diet-card,
    .diet-list{
        width:90%;
        padding:35px 30px;
    }
    
    .diet-card h3{
        font-size:28px;
    }
    
    .nutrition-grid{
        grid-template-columns: 1fr;
        gap:10px;
    }
    
    .diet-item .nutrients{
        grid-template-columns: repeat(2, 1fr);
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
    <a class="active" href="diet_plan.php">Diet Plan</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div style="width:100%; display:flex; flex-direction:column; align-items:center;">
        <!-- Diet Form -->
        <div class="diet-card">
            <h3>🥗 Diet Plan</h3>

            <?php if($message): ?>
            <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <select class="form-control" name="meal" required>
                        <option value="">Select Meal Type</option>
                        <option value="Breakfast">🌅 Breakfast</option>
                        <option value="Lunch">☀️ Lunch</option>
                        <option value="Dinner">🌙 Dinner</option>
                        <option value="Snack">🍎 Snack</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <input class="form-control" name="food" placeholder="Food Name (e.g., Grilled Chicken Salad)" required maxlength="100">
                </div>
                
                <div class="form-group">
                    <input class="form-control" type="number" name="cal" placeholder="Calories (e.g., 250)" required min="0" max="5000" step="1">
                </div>
                
                <label class="input-label">📊 Macronutrients (Optional)</label>
                <div class="nutrition-grid mb-4">
                    <input class="form-control" type="number" name="carbs" placeholder="Carbs (g)" min="0" max="1000" step="0.1">
                    <input class="form-control" type="number" name="protein" placeholder="Protein (g)" min="0" max="1000" step="0.1">
                    <input class="form-control" type="number" name="fat" placeholder="Fat (g)" min="0" max="1000" step="0.1">
                </div>

                <button type="submit" class="btn btn-warning w-100" name="add">
                    ➕ Add to Diet Plan
                </button>
            </form>
        </div>

        <!-- Diet List -->
        <div class="diet-list">
            <?php
            // Calculate total calories
            $totalStmt = mysqli_prepare($conn, "SELECT SUM(calories) as total FROM diet_plan WHERE user_id = ?");
            mysqli_stmt_bind_param($totalStmt, "i", $_SESSION['user_id']);
            mysqli_stmt_execute($totalStmt);
            $totalResult = mysqli_stmt_get_result($totalStmt);
            $totalRow = mysqli_fetch_assoc($totalResult);
            $totalCalories = $totalRow['total'] ?? 0;
            mysqli_stmt_close($totalStmt);
            
            if($totalCalories > 0){
                echo "
                <div class='total-calories'>
                    <div class='big-number'>" . number_format($totalCalories) . "</div>
                    <div class='label'>Total Daily Calories</div>
                </div>";
            }
            
            // Use prepared statement for SELECT query
            $stmt = mysqli_prepare($conn, "SELECT * FROM diet_plan WHERE user_id = ? ORDER BY 
                CASE meal_type 
                    WHEN 'Breakfast' THEN 1 
                    WHEN 'Lunch' THEN 2 
                    WHEN 'Snack' THEN 3 
                    WHEN 'Dinner' THEN 4 
                    ELSE 5 
                END, id DESC");
            mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($res) > 0){
                while($r = mysqli_fetch_assoc($res)){
                    // Escape output to prevent XSS
                    $mealType = htmlspecialchars($r['meal_type']);
                    $foodName = htmlspecialchars($r['food_name']);
                    $calories = htmlspecialchars($r['calories']);
                    $carbs = !empty($r['carbs']) ? htmlspecialchars($r['carbs']) : '0';
                    $protein = !empty($r['protein']) ? htmlspecialchars($r['protein']) : '0';
                    $fat = !empty($r['fat']) ? htmlspecialchars($r['fat']) : '0';
                    
                    // Determine badge class
                    $badgeClass = 'meal-' . strtolower($mealType);
                    
                    echo "
                    <div class='diet-item'>
                        <span class='meal-badge {$badgeClass}'>{$mealType}</span>
                        <strong>{$foodName}</strong>
                        
                        <div class='nutrients'>
                            <div class='nutrient-box' style='background: #fff3cd;'>
                                <span class='value'>🔥 {$calories}</span>
                                <span class='label'>Calories</span>
                            </div>
                            <div class='nutrient-box' style='background: #e7f3ff;'>
                                <span class='value'>🍞 {$carbs}g</span>
                                <span class='label'>Carbs</span>
                            </div>
                            <div class='nutrient-box' style='background: #ffe7e7;'>
                                <span class='value'>🥩 {$protein}g</span>
                                <span class='label'>Protein</span>
                            </div>
                            <div class='nutrient-box' style='background: #fff8dc;'>
                                <span class='value'>🧈 {$fat}g</span>
                                <span class='label'>Fat</span>
                            </div>
                        </div>
                    </div>";
                }
            } else {
                echo "<div class='no-diet'>🥗 No diet entries found. Start planning your meals above!</div>";
            }
            
            mysqli_stmt_close($stmt);
            ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>