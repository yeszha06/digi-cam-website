<?php
// db.php
header('Content-Type: application/json');

// MySQL Configuration details from your InfinityFree panel
$host = "sql303.byetcluster.com";  
$user = "if0_42071750";             
$pass = "ysaass5220"; 
$dbname = "if0_42071750_booking_db"; 

// Create database connection
$conn = new mysqli($host, $user, $pass, $dbname);

// If the website fails to connect to the database, return a structured error
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed. Please try again later.']);
    exit;
}
?>