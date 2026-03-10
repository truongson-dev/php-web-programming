<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức yêu cầu không hợp lệ']);
    exit;
}

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để đặt sân!']);
    exit;
}

$user_id = $_SESSION['user']['id'];
$pitch_id = isset($_POST['pitch_id']) ? intval($_POST['pitch_id']) : 0;
$booking_date = isset($_POST['booking_date']) ? $_POST['booking_date'] : '';
$time_slot = isset($_POST['time_slot']) ? $_POST['time_slot'] : '';
$total_price = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;

// Kiểm tra dữ liệu đầu vào
if (!$pitch_id || !$booking_date || !$time_slot || !$total_price) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đặt sân!']);
    exit;
}

// Kiểm tra xem khung giờ đã được đặt chưa
$check_query = "SELECT id FROM bookings WHERE pitch_id = ? AND booking_date = ? AND time_slot = ? AND status != 'cancelled'";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("iss", $pitch_id, $booking_date, $time_slot);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Khung giờ này đã được đặt. Vui lòng chọn khung giờ khác!']);
    exit;
}

// Thực hiện đặt sân (Lưu vào CSDL)
$subtotal = $total_price; // Subtotal bằng total_price (chưa áp dụng discount)
$discount = 0; // Mặc định không có discount

$insert_query = "INSERT INTO bookings (user_id, pitch_id, booking_date, time_slot, subtotal, discount, total_price, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
$stmt = $conn->prepare($insert_query);
$stmt->bind_param("iissddd", $user_id, $pitch_id, $booking_date, $time_slot, $subtotal, $discount, $total_price);

if ($stmt->execute()) {
    $booking_id = $conn->insert_id;
    echo json_encode([
        'success' => true,
        'message' => 'Đặt sân thành công! Đơn đặt sân đang chờ xác nhận.',
        'booking_id' => $booking_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi đặt sân: ' . $conn->error]);
}

$stmt->close();
$conn->close();
