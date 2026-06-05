<?php
// Handle rental booking submissions matching your exact database columns
header('Content-Type: application/json');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed. Please use POST request.']);
    exit;
}

// 1. Gather fields from your HTML form submission
// (Make sure the names in your HTML form match these POST keys: full_name, camera_model, pickup_date)
$customer_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$camera_model  = isset($_POST['camera_model']) ? trim($_POST['camera_model']) : '';
$booking_date  = isset($_POST['pickup_date']) ? trim($_POST['pickup_date']) : '';
$status        = 'Pending'; // Default status value as shown in your phpMyAdmin

// 2. Simple Validation
if (empty($customer_name) || empty($camera_model) || empty($booking_date)) {
    http_response_code(400);
    echo json_encode(['error' => 'All required fields must be filled out.']);
    exit;
}

// 3. Insert into database matching your exact column names from Screenshot 2026-06-06 012302.png
$stmt = $conn->prepare("INSERT INTO bookings (customer_name, camera_model, booking_date, status) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $customer_name, $camera_model, $booking_date, $status);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode([
        'success' => 'Thank you for your booking request! Your reservation is currently pending.',
        'message' => 'Please message us via DM to finalize your rental details.'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to process booking. Please try again later.']);
}

$stmt->close();
$conn->close();
?>