<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

$pitch_id = isset($_GET['pitch_id']) ? intval($_GET['pitch_id']) : 0;
$booking_date = isset($_GET['booking_date']) ? $_GET['booking_date'] : '';

if (!$pitch_id || !$booking_date) {
    echo json_encode(['success' => false, 'message' => 'Tham số không hợp lệ']);
    exit;
}

// Lấy tất cả khung giờ đã được đặt cho sân này vào ngày này
$query = "SELECT time_slot FROM bookings WHERE pitch_id = ? AND booking_date = ? AND status != 'cancelled'";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $pitch_id, $booking_date);
$stmt->execute();
$result = $stmt->get_result();

$booked_slots = [];
while ($row = $result->fetch_assoc()) {
    $booked_slots[] = $row['time_slot'];
}

echo json_encode(['success' => true, 'booked_slots' => $booked_slots]);

$stmt->close();
$conn->close();
