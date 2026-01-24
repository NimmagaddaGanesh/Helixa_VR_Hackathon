<!DOCTYPE html>
<html>
<head>
    <title>HealthTrack+</title>
 
</head>
<style> * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        /* Navigation */
        nav {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-size: 1.8rem;
            font-weight: bold;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .nav-btn {
            background: white;
            color: #667eea;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Hero Section */
        .hero {
    position: relative;
    background: 
        linear-gradient(
            rgba(102, 126, 234, 0.85),
            rgba(248, 246, 250, 0.85)
        ),
         url("../assets/css/image.png") center 90% / cover no-repeat;
    color: white;
    padding: 100px 5%;
    text-align: center;
    min-height: 600px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

         
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            animation: fadeInDown 1s;
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            max-width: 600px;
            animation: fadeInUp 1s;
        }

        .hero-icon {
            font-size: 5rem;
            margin-bottom: 2rem;
            animation: bounce 2s infinite;
        }

        /* About Section */
        .about {
            padding: 80px 5%;
            background: #f8f9fa;
        }

        .about h2 {
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 3rem;
            color: #667eea;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            color: #667eea;
            margin-bottom: 1rem;
        }

        /* Auth Pages */
        .auth-container {
            display: none;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 50px 20px;
            align-items: center;
            justify-content: center;
        }

        .auth-box {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
            animation: fadeIn 0.5s;
        }

        .auth-box h2 {
            color: #667eea;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.9rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-primary {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
        }

        .auth-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }

        .auth-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-link a:hover {
            text-decoration: underline;
        }

        /* Dashboard */
        .dashboard {
            display: none;
            min-height: 100vh;
        }

        .dashboard-container {
            display: flex;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            min-height: 100vh;
            position: fixed;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 3rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .sidebar-logo {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .sidebar h2 {
            font-size: 1.5rem;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 1rem;
        }

        .sidebar-menu a {
            color: white;
            text-decoration: none;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 15px;
            border-radius: 10px;
            transition: background 0.3s;
            font-size: 1.1rem;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.2);
        }

        .main-content {
            margin-left: 280px;
            padding: 2rem;
            flex: 1;
            background: #f5f5f5;
        }

        .dashboard-header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .dashboard-header h1 {
            color: #333;
            margin-bottom: 0.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .stat-card h3 {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
        }

        .welcome-section {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .welcome-section h2 {
            color: #667eea;
            margin-bottom: 1rem;
            font-size: 2rem;
        }

        .btn-journey {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 3rem;
            border: none;
            border-radius: 25px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: transform 0.3s;
        }
        .btn{
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            border: none;
            text-decoration:none;
            border-radius: 25px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: transform 0.3s;
        }
        
        .btn-journey:hover {
            transform: translateY(-3px);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    /* CLEAN FEATURE CARD UI — NO HTML CHANGES */

.feature-card {
    background: #ffffff;
    padding: 2.8rem 2rem;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    text-align: center;
    transition: all 0.35s ease;
    font-size: 1rem;
    line-height: 1.6;
}

/* Hover effect */
.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 18px 40px rgba(0,0,0,0.15);
}

/* Emoji (icon) */
.feature-card::first-letter {
    font-size: 2.6rem;
    display: block;
    margin-bottom: 0.6rem;
}

/* Title line (emoji + title line) */
.feature-card::first-line {
    font-size: 1.3rem;
    font-weight: 700;
    color: #667eea;
}

/* Description text */
.feature-card br + * {
    display: block;
    margin-top: 0.8rem;
    font-size: 0.95rem;
    color: #555;
}

        .logout-btn {
            position: absolute;
            bottom: 2rem;
            left: 2rem;
            right: 2rem;
            background: rgba(255,255,255,0.2);
            border: 1px solid white;
            color: white;
            padding: 1rem;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }</style>
<body>

<nav>
    <h2>❤️ Helixa</h2>
   <a href="/web_medicine/public/login.php" class="btn">Login / Register</a>

</nav>

<section class="hero">
    <div class="hero-icon"></div>
    <h1>Helixa+❤️</h1>
    <p>Your comprehensive health companion for tracking medications, managing diet plans, and maintaining health records.</p>
</section>

<section class="about">
    <h2>About HealthTrack+</h2>
    <div class="features">
        <div class="feature-card">📋 Health Records <br> Store and manage all your medical records, test results, and health history in one secure place.</div>
        <div class="feature-card">💊 Medication Tracking <br>Never miss a dose with smart medication reminders and easy tracking of your prescriptions.</div>
        <div class="feature-card">🥗 Diet Plans<br>Get personalized nutrition plans based on your health conditions and dietary preferences.</div>
        <div class="feature-card">📊 Health Insights<br>Track your health metrics over time and gain valuable insights into your wellbeing.</div>
    </div>
</section>

</body>
</html>
