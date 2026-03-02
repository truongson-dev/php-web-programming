<?php
// Cấu hình kết nối cơ sở dữ liệu (Database Connection)
$host = '127.0.0.1';
$dbname = 'du_an_php';
$username = 'root';
$password = '';

try {
    $conn = new mysqli($host, $username, $password, $dbname);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Thiết lập bảng mã UTF-8 để hiển thị tiếng Việt
    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    die("Lỗi: " . $e->getMessage());
}
?>