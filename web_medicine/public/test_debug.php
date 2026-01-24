<?php
// Force display ALL errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>PHP Diagnostics</h2>";

// Test 1: PHP Version
echo "<h3>1. PHP Version</h3>";
echo "PHP Version: " . phpversion() . "<br>";

// Test 2: File paths
echo "<h3>2. File Path Check</h3>";
echo "Current file: " . __FILE__ . "<br>";
echo "Current directory: " . __DIR__ . "<br>";

$db_path = __DIR__ . "/../config/db.php";
echo "DB path should be: " . $db_path . "<br>";

if (file_exists($db_path)) {
    echo "✅ db.php EXISTS<br>";
} else {
    echo "❌ db.php NOT FOUND<br>";
}

// Test 3: Try to include db.php
echo "<h3>3. Database Connection Test</h3>";
try {
    require_once("../config/db.php");
    echo "✅ db.php included successfully<br>";
    
    if (isset($conn)) {
        echo "✅ Connection object exists<br>";
        echo "Database host: " . $conn->host_info . "<br>";
        
        // Test query
        $result = $conn->query("SELECT DATABASE()");
        if ($result) {
            $row = $result->fetch_row();
            echo "✅ Connected to database: " . $row[0] . "<br>";
        }
    } else {
        echo "❌ Connection object NOT created<br>";
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "<br>";
}

// Test 4: Check if users table exists
echo "<h3>4. Users Table Check</h3>";
if (isset($conn)) {
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result && $result->num_rows > 0) {
        echo "✅ Users table EXISTS<br>";
        
        // Check table structure
        $result = $conn->query("DESCRIBE users");
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Users table NOT FOUND<br>";
        echo "<p>Run this SQL in phpMyAdmin:</p>";
        echo "<pre>";
        echo "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    dob DATE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";
        echo "</pre>";
    }
}

// Test 5: Session test
echo "<h3>5. Session Test</h3>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "✅ Session started successfully<br>";
} else {
    echo "✅ Session already active<br>";
}
echo "Session ID: " . session_id() . "<br>";

// Test 6: MySQLi extension
echo "<h3>6. PHP Extensions</h3>";
echo "MySQLi Extension: " . (extension_loaded('mysqli') ? '✅ Loaded' : '❌ Not Loaded') . "<br>";
echo "Session Extension: " . (extension_loaded('session') ? '✅ Loaded' : '❌ Not Loaded') . "<br>";

echo "<br><hr><br>";
echo "<h3>If all tests pass, try accessing:</h3>";
echo "<a href='login.php'>Login Page</a> | ";
echo "<a href='register.php'>Register Page</a> | ";
echo "<a href='index.php'>Home Page</a>";
?>