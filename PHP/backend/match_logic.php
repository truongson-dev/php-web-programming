<?php
/**
 * Logic Ghép Đội Tự Động (Auto-Matching Algorithm)
 */

function findMatches($conn, $userId, $position, $date, $time)
{
    // 1. Lưu yêu cầu tìm đội hiện tại vào Database
    $stmt = $conn->prepare("INSERT INTO match_requests (user_id, preferred_position, match_date, match_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userId, $position, $date, $time);
    $stmt->execute();

    // 2. Tìm kiếm các người chơi khác có cùng ngày và khung giờ
    // Thuật toán ưu tiên: Cùng thời gian -> Khác vị trí (để tạo đội cân bằng)
    $query = "SELECT r.*, u.name as user_name 
              FROM match_requests r
              JOIN users u ON r.user_id = u.id
              WHERE r.match_date = ? 
              AND r.match_time = ? 
              AND r.user_id != ? 
              AND r.status = 'open'
              ORDER BY r.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $date, $time, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $candidates = [];
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }

    // Logic gợi ý: 
    // Nếu tìm đủ 6 người khác (cho sân 7), hệ thống sẽ gửi thông báo tạo group chat tự động.
    return $candidates;
}

// Xử lý Request từ Frontend
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $pos = $_POST['position'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Giả sử đã có kết nối $conn (MySQLi)
    $results = findMatches($conn, $userId, $pos, $date, $time);

    echo json_encode([
        'status' => 'success',
        'matches_found' => count($results),
        'data' => $results
    ]);
}
?>