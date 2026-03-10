<?php
header('Content-Type: application/json');
require_once 'db.php';
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để đặt hàng!']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user']['id'];
    $product_id = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    $size = $_POST['size'] ?? 'N/A';
    $total_amount = $_POST['total_amount'] ?? 0;

    // Bắt đầu transaction
    $conn->begin_transaction();

    try {
        // 1. Tạo đơn hàng mới
        $order_query = "INSERT INTO orders (user_id, total_amount, status, created_at) VALUES (?, ?, 'pending', NOW())";
        $stmt = $conn->prepare($order_query);
        $stmt->bind_param("id", $user_id, $total_amount);
        $stmt->execute();
        $order_id = $conn->insert_id;

        // 2. Thêm chi tiết đơn hàng
        // Lấy giá sản phẩm từ database để đảm bảo chính xác
        $prod_query = "SELECT price FROM products WHERE id = ?";
        $stmt_prod = $conn->prepare($prod_query);
        $stmt_prod->bind_param("i", $product_id);
        $stmt_prod->execute();
        $price = $stmt_prod->get_result()->fetch_assoc()['price'];

        $item_query = "INSERT INTO order_items (order_id, product_id, quantity, size, price) VALUES (?, ?, ?, ?, ?)";
        $stmt_item = $conn->prepare($item_query);
        $stmt_item->bind_param("iiisd", $order_id, $product_id, $quantity, $size, $price);
        $stmt_item->execute();

        // 3. Cập nhật số lượng kho (giảm bớt)
        $update_stock = "UPDATE products SET stock = stock - ? WHERE id = ?";
        $stmt_stock = $conn->prepare($update_stock);
        $stmt_stock->bind_param("ii", $quantity, $product_id);
        $stmt_stock->execute();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Đặt hàng thành công!', 'order_id' => $order_id]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức yêu cầu không hợp lệ']);
}
