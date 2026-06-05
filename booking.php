<?php
// Handle rental booking submissions
header('Content-Type: application/json');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed. Please use POST request.']);
    exit;
}

$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$gov_id = isset($_POST['gov_id']) ? trim($_POST['gov_id']) : '';
$camera_model = isset($_POST['camera_model']) ? trim($_POST['camera_model']) : '';
$pickup_date = isset($_POST['pickup_date']) ? trim($_POST['pickup_date']) : '';
$return_date = isset($_POST['return_date']) ? trim($_POST['return_date']) : '';
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

// Validation
if (empty($full_name) || empty($phone) || empty($gov_id) || empty($camera_model) || empty($pickup_date) || empty($return_date)) {
    http_response_code(400);
    echo json_encode(['error' => 'All required fields must be filled out.']);
    exit;
}

$pickup_timestamp = strtotime($pickup_date);
$return_timestamp = strtotime($return_date);
$today = strtotime(date('Y-m-d'));

if ($pickup_timestamp === false || $return_timestamp === false) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date format.']);
    exit;
}

if ($pickup_timestamp < $today) {
    http_response_code(400);
    echo json_encode(['error' => 'Pickup date cannot be in the past.']);
    exit;
}

if ($return_timestamp <= $pickup_timestamp) {
    http_response_code(400);
    echo json_encode(['error' => 'Return date must be after pickup date.']);
    exit;
}

// Generate Booking ID
$booking_id = 'BK' . date('YmdHis') . rand(1000, 9999);

// Insert into Database using Secure Prepared Statements
$stmt = $conn->prepare("INSERT INTO rentals (booking_id, full_name, phone, gov_id, camera_model, pickup_date, return_date, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $booking_id, $full_name, $phone, $gov_id, $camera_model, $pickup_date, $return_date, $notes);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode([
        'success' => 'Thank you for your booking request! Check your email for updates.',
        'booking_id' => $booking_id,
        'message' => 'Please message us via DM to confirm availability and finalize your reservation.'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to process booking. Please try again later.']);
}

$stmt->close();
$conn->close();
?>