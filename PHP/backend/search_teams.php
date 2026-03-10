<?php
require_once 'db.php';

header('Content-Type: application/json');

$location = isset($_GET['location']) ? $_GET['location'] : '';
$position = isset($_GET['position']) ? $_GET['position'] : '';
$time = isset($_GET['time']) ? $_GET['time'] : '';
$sport = isset($_GET['sport']) ? $_GET['sport'] : 'football';

// Kiểm tra dữ liệu hợp lệ
if (empty($location) && empty($time) && empty($position)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp khu vực, vị trí hoặc thời gian']);
    exit;
}

// Xây dựng truy vấn tìm kiếm
$sql = "SELECT tr.*, t.name as team_name, t.logo as team_logo 
        FROM team_recruitment tr 
        JOIN teams t ON tr.team_id = t.id 
        WHERE tr.status = 'open' AND tr.sport = ?";

$params = [$sport];
$types = "s";

if (!empty($location)) {
    $sql .= " AND tr.location LIKE ?";
    $params[] = "%" . $location . "%";
    $types .= "s";
}

if (!empty($position)) {
    $sql .= " AND tr.position LIKE ?";
    $params[] = "%" . $position . "%";
    $types .= "s";
}

if (!empty($time)) {
    // Xác định mức độ lọc chính xác. Hiện tại dùng so sánh LIKE đơn giản cho khung giờ hoặc thời gian cụ thể
    $sql .= " AND tr.match_time LIKE ?";
    $params[] = "%" . $time . "%";
    $types .= "s";
}

$sql .= " ORDER BY tr.created_at DESC LIMIT 20";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$matches = [];
while ($row = $result->fetch_assoc()) {
    $matches[] = [
        'id' => $row['id'],
        'team_name' => $row['team_name'],
        'team_logo' => $row['team_logo'] && $row['team_logo'] !== 'https://via.placeholder.com/100' ? $row['team_logo'] : 'https://ui-avatars.com/api/?name=' . urlencode($row['team_name']) . '&background=random&size=100',
        'location' => $row['location'],
        'match_time' => $row['match_time'],
        'match_date' => $row['match_date'],
        'position' => $row['position'],
        'message' => $row['message'],
        'created_at' => $row['created_at']
    ];
}

echo json_encode(['success' => true, 'matches' => $matches]);
