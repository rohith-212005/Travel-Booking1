<?php
// Database configuration
$host = 'sql204.infinityfree.com'; // Replace with your database host
$dbname = 'if0_39639012_travelbooking'; // Replace with your database name
$username = 'if0_39639012'; // Replace with your database username
$password = 'AbIG3WEFXR'; // Replace with your database password

try {
    // Create a new PDO instance for database connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set PDO to throw exceptions on errors
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Optional: Set default fetch mode to associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Optional: Set character encoding to UTF-8
    $conn->exec("SET NAMES 'utf8'");
    
} catch (PDOException $e) {
    // Handle connection errors
    die("Database connection failed: " . $e->getMessage());
}
?>
