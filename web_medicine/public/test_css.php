<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$css_path = __DIR__ . "/../assets/css/style.css";

echo "<h2>CSS File Check</h2>";

if (file_exists($css_path)) {
    echo "✅ style.css EXISTS at: $css_path<br>";
    echo "File size: " . filesize($css_path) . " bytes<br>";
} else {
    echo "❌ style.css NOT FOUND at: $css_path<br>";
}

echo "<hr>";
echo "<h3>Test with inline CSS:</h3>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>CSS Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .auth-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #5568d3;
        }
    </style>
</head>
<body class="auth-container">

<div class="auth-box">
    <h2>If you can see this styled, CSS works!</h2>
    <form>
        <input type="email" placeholder="Email" required>
        <input type="password" placeholder="Password" required>
        <button type="button">Test Button</button>
    </form>
</div>

</body>
</html>