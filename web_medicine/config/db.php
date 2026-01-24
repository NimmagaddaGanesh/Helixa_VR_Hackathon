<?php
// Database configuration
$conn = new mysqli("localhost", "root", "", "healthtrack");

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>