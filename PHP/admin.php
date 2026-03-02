<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'backend/db.php';

// Kiểm tra quyền Admin (Phải thực hiện trước khi có bất kỳ output nào)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$page_title = 'Điều Hành Hệ Thống - SuperSports Admin';
$current_page = 'admin';
require_once 'includes/header.php';
require_once 'includes/admin_navbar.php';

// Xử lý thêm sân mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_pitch') {
        $name = $_POST['name'] ?? '';
        $location = $_POST['location'] ?? '';
        $price = $_POST['price'] ?? 0;
        $type = $_POST['type'] ?? '';
        $sport = $_POST['sport'] ?? '';
        $image = $_POST['image'] ??
            'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&q=80&w=600';
        $status = 'available';

        $stmt = $conn->prepare("INSERT INTO pitches (name, location, price, type, sport, image, status) VALUES (?, ?, ?, ?, ?,
?, ?)");
        $stmt->bind_param("ssissss", $name, $location, $price, $type, $sport, $image, $status);

        if ($stmt->execute()) {
            $success_msg = "Thêm sân thành công!";
        } else {
            $error_msg = "Lỗi khi thêm sân: " . $conn->error;
        }
    } elseif ($_POST['action'] === 'update_pitch') {
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $location = $_POST['location'] ?? '';
        $price = $_POST['price'] ?? 0;
        $type = $_POST['type'] ?? '';
        $sport = $_POST['sport'] ?? '';
        $image = $_POST['image'] ?? '';

        $stmt = $conn->prepare("UPDATE pitches SET name=?, location=?, price=?, type=?, sport=?, image=? WHERE id=?");
        $stmt->bind_param("ssdsssi", $name, $location, $price, $type, $sport, $image, $id);

        if ($stmt->execute()) {
            $success_msg = "Cập nhật sân thành công!";
        } else {
            $error_msg = "Lỗi khi cập nhật sân: " . $conn->error;
        }
    } elseif ($_POST['action'] === 'add_product') {
        $name = $_POST['name'] ?? '';
        $category = $_POST['category'] ?? '';
        $price = $_POST['price'] ?? 0;
        $stock = $_POST['stock'] ?? 0;
        $description = $_POST['description'] ?? '';
        $image = $_POST['image'] ?? '';
        $is_hot = isset($_POST['is_hot']) ? 1 : 0;

        $stmt = $conn->prepare("INSERT INTO products (name, category, price, stock, description, image, is_hot) VALUES (?, ?, ?,
?, ?, ?, ?)");
        $stmt->bind_param("ssdissi", $name, $category, $price, $stock, $description, $image, $is_hot);

        if ($stmt->execute()) {
            $success_msg = "Thêm sản phẩm thành công!";
        } else {
            $error_msg = "Lỗi khi thêm sản phẩm: " . $conn->error;
        }
    } elseif ($_POST['action'] === 'update_product') {
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $category = $_POST['category'] ?? '';
        $price = $_POST['price'] ?? 0;
        $stock = $_POST['stock'] ?? 0;
        $description = $_POST['description'] ?? '';
        $image = $_POST['image'] ?? '';
        $is_hot = isset($_POST['is_hot']) ? 1 : 0;

        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, stock=?, description=?, image=?, is_hot=? WHERE
id=?");
        $stmt->bind_param("ssdissii", $name, $category, $price, $stock, $description, $image, $is_hot, $id);

        if ($stmt->execute()) {
            $success_msg = "Cập nhật sản phẩm thành công!";
        } else {
            $error_msg = "Lỗi khi cập nhật sản phẩm: " . $conn->error;
        }
    } elseif ($_POST['action'] === 'update_user') {
        $id = $_POST['id'] ?? 0;
        $role = $_POST['role'] ?? 'user';

        $stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
        $stmt->bind_param("si", $role, $id);

        if ($stmt->execute()) {
            $success_msg = "Cập nhật người dùng thành công!";
        } else {
            $error_msg = "Lỗi khi cập nhật người dùng: " . $conn->error;
        }
    } elseif ($_POST['action'] === 'add_maintenance') {
        $item_name = $_POST['item_name'] ?? '';
        $cost = $_POST['cost'] ?? 0;
        $description = $_POST['description'] ?? '';

        $stmt = $conn->prepare("INSERT INTO maintenance (item_name, cost, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $item_name, $cost, $description);

        if ($stmt->execute()) {
            $success_msg = "Thêm kế hoạch bảo trì thành công!";
        } else {
            $error_msg = "Lỗi khi thêm bảo trì: " . $conn->error;
        }
    } elseif ($_POST['action'] === 'add_news') {
        $title = $_POST['title'] ?? '';
        $category = $_POST['category'] ?? '';
        $image = $_POST['image'] ?? '';
        $description = $_POST['description'] ?? '';
        $content = $_POST['content'] ?? '';
        $author = $_POST['author'] ?? 'Admin';

        $stmt = $conn->prepare("INSERT INTO news (title, category, image, description, content, author) VALUES (?, ?, ?, ?, ?,
?)");
        $stmt->bind_param("ssssss", $title, $category, $image, $description, $content, $author);

        if ($stmt->execute()) {
            $success_msg = "Thêm tin tức thành công!";
        } else {
            $error_msg = "Lỗi khi thêm tin tức: " . $conn->error;
        }
    } elseif ($_POST['action'] === 'update_news') {
        $id = $_POST['id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $category = $_POST['category'] ?? '';
        $image = $_POST['image'] ?? '';
        $description = $_POST['description'] ?? '';
        $content = $_POST['content'] ?? '';
        $author = $_POST['author'] ?? 'Admin';

        $stmt = $conn->prepare("UPDATE news SET title=?, category=?, image=?, description=?, content=?, author=? WHERE id=?");
        $stmt->bind_param("ssssssi", $title, $category, $image, $description, $content, $author, $id);

        if ($stmt->execute()) {
            $success_msg = "Cập nhật tin tức thành công!";
        } else {
            $error_msg = "Lỗi khi cập nhật tin tức: " . $conn->error;
        }
    } elseif ($_POST['action'] === 'add_user') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';
        $points = $_POST['points'] ?? 0;

        $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
        if ($check->num_rows > 0) {
            $error_msg = "Email này đã được sử dụng!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $success_msg = "Thêm người dùng mới thành công!";
            } else {
                $error_msg = "Lỗi khi thêm người dùng: " . $conn->error;
            }
        }

    }
}

// Xử lý các yêu cầu GET
if (isset($_GET['delete_news'])) {
    $id = $_GET['delete_news'];
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success_msg = "Xóa tin tức thành công!";
    } else {
        $error_msg = "Lỗi khi xóa tin tức: " . $conn->error;
    }
}

// Xử lý xóa sân
if (isset($_GET['delete_pitch'])) {
    $id = $_GET['delete_pitch'];
    $stmt = $conn->prepare("DELETE FROM pitches WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success_msg = "Xóa sân thành công!";
    } else {
        $error_msg = "Lỗi khi xóa sân: " . $conn->error;
    }
}

// Xử lý xóa sản phẩm
if (isset($_GET['delete_product'])) {
    $id = $_GET['delete_product'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success_msg = "Xóa sản phẩm thành công!";
    } else {
        $error_msg = "Lỗi khi xóa sản phẩm: " . $conn->error;
    }
}



// Duyệt/Hủy đặt sân
if (isset($_GET['approve_booking'])) {
    $id = $_GET['approve_booking'];
    $stmt = $conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success_msg = "Đã duyệt đơn đặt sân thành công!";
    } else {
        $error_msg = "Lỗi khi duyệt: " . $conn->error;
    }
}

if (isset($_GET['cancel_booking'])) {
    $id = $_GET['cancel_booking'];
    $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success_msg = "Đã hủy đơn đặt sân!";
    } else {
        $error_msg = "Lỗi khi hủy: " . $conn->error;
    }
}

// Duyệt/Từ chối bài đăng tìm đối thủ
if (isset($_GET['approve_challenge'])) {
    $id = $_GET['approve_challenge'];
    $stmt = $conn->prepare("UPDATE team_challenges SET status = 'open' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success_msg = "Đã duyệt bài đăng!";
    } else {
        $error_msg = "Lỗi khi duyệt: " . $conn->error;
    }
}

if (isset($_GET['reject_challenge'])) {
    $id = $_GET['reject_challenge'];
    $stmt = $conn->prepare("UPDATE team_challenges SET status = 'cancelled' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success_msg = "Đã từ chối bài đăng!";
    } else {
        $error_msg = "Lỗi khi từ chối: " . $conn->error;
    }
}

// Xử lý đơn hàng
if (isset($_GET['approve_order'])) {
    $id = $_GET['approve_order'];
    $stmt = $conn->prepare("UPDATE orders SET status = 'shipped' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success_msg = "Đã xác nhận đơn hàng và chuyển sang trạng thái Giao hàng!";
    } else {
        $error_msg = "Lỗi khi cập nhật đơn hàng: " . $conn->error;
    }
}

if (isset($_GET['cancel_order'])) {
    $id = $_GET['cancel_order'];
    $stmt = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success_msg = "Đã hủy đơn hàng!";
    } else {
        $error_msg = "Lỗi khi hủy đơn hàng: " . $conn->error;
    }
}

// Xử lý xóa người dùng
if (isset($_GET['delete_user'])) {
    $id = $_GET['delete_user'];
    // Không cho phép tự xóa chính mình
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
        $error_msg = "Bạn không thể tự xóa tài khoản của chính mình!";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success_msg = "Xóa người dùng thành công!";
        } else {
            $error_msg = "Lỗi khi xóa người dùng: " . $conn->error;
        }
    }
}

// Xử lý bảo trì
if (isset($_GET['delete_maintenance'])) {
    $id = $_GET['delete_maintenance'];
    $stmt = $conn->prepare("DELETE FROM maintenance WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute())
        $success_msg = "Xóa kế hoạch bảo trì thành công!";
}

if (isset($_GET['complete_maintenance'])) {
    $id = $_GET['complete_maintenance'];
    $stmt = $conn->prepare("UPDATE maintenance SET status = 'completed' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute())
        $success_msg = "Đã đánh dấu hoàn thành bảo trì!";
}

// Xử lý xóa tin tức
if (isset($_GET['delete_news'])) {
    $id = $_GET['delete_news'];
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success_msg = "Xóa tin tức thành công!";
    } else {
        $error_msg = "Lỗi khi xóa tin tức: " . $conn->error;
    }
}

// Cấu hình Tabs
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'bookings';

$tabs = [
    ['id' => 'bookings', 'label' => 'Lịch Đặt Sân', 'icon' => 'fa-calendar-check'],
    ['id' => 'matching', 'label' => 'Tìm Đối Thủ', 'icon' => 'fa-swords'],
    ['id' => 'pitches', 'label' => 'Quản Lý Sân', 'icon' => 'fa-futbol'],
    ['id' => 'store', 'label' => 'Cửa Hàng', 'icon' => 'fa-shopping-cart'],
    ['id' => 'news', 'label' => 'Tin Tức', 'icon' => 'fa-newspaper'],
    ['id' => 'users', 'label' => 'Người Dùng', 'icon' => 'fa-user-cog'],
    ['id' => 'stats', 'label' => 'Thống Kê', 'icon' => 'fa-chart-pie'],
];
?>

<div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <div class="w-80 bg-white border-r border-gray-100 h-screen fixed top-0 left-0 flex flex-col pt-8 z-[40]">
        <div class="px-8 mb-10">
            <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-2">QUẢN TRỊ VIÊN</h2>
            <p class="text-xl font-black text-gray-900 italic uppercase">Hệ Thống</p>
        </div>

        <nav class="flex-grow px-4 space-y-1.5">
            <?php foreach ($tabs as $tab): ?>
                <a href="?tab=<?php echo $tab['id']; ?>"
                    class="flex items-center gap-4 px-6 py-4 rounded-[20px] text-[13px] font-black transition-all group <?php echo $active_tab === $tab['id'] ? 'bg-green-600 text-white shadow-xl shadow-green-600/20' : 'text-gray-500 hover:bg-gray-50 hover:text-green-600'; ?>">
                    <i class="fas <?php echo $tab['icon']; ?> text-lg group-hover:scale-110 transition-transform"></i>
                    <span class="uppercase tracking-wider"><?php echo $tab['label']; ?></span>
                    <?php if ($active_tab === $tab['id']): ?>
                        <i class="fas fa-chevron-right ml-auto text-[10px] opacity-50"></i>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-grow ml-80 p-10 min-h-screen">
        <div class="max-w-6xl">
            <!-- Header Section -->
            <div class="flex justify-between items-end mb-12">
                <div>
                    <?php
                    $current_label = '';
                    foreach ($tabs as $t)
                        if ($t['id'] == $active_tab)
                            $current_label = $t['label'];
                    ?>
                    <h3 class="text-2xl font-black text-gray-900 uppercase italic tracking-tighter mb-2">
                        <?php echo $current_label; ?>
                    </h3>
                    <p class="text-gray-400 font-medium italic">Chào buổi sáng, bạn đang quản lý
                        <?php echo strtolower($current_label); ?>.
                    </p>
                </div>

                <?php if ($active_tab === 'pitches'): ?>
                    <button onclick="document.getElementById('add-pitch-modal').classList.remove('hidden')"
                        class="relative z-10 bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-green-600/20 transition-all active:scale-95 flex items-center gap-3">
                        <i class="fas fa-plus"></i> THÊM SÂN MỚI
                    </button>
                <?php elseif ($active_tab === 'store'): ?>
                    <button onclick="document.getElementById('add-product-modal').classList.remove('hidden')"
                        class="relative z-10 bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-600/20 transition-all active:scale-95 flex items-center gap-3">
                        <i class="fas fa-plus"></i> THÊM SẢN PHẨM
                    </button>
                <?php elseif ($active_tab === 'news'): ?>
                    <button onclick="document.getElementById('add-news-modal').classList.remove('hidden')"
                        class="relative z-10 bg-orange-600 hover:bg-orange-700 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-orange-600/20 transition-all active:scale-95 flex items-center gap-3">
                        <i class="fas fa-plus"></i> ĐĂNG TIN MỚI
                    </button>
                <?php endif; ?>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($success_msg)): ?>
                <div
                    class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-bold rounded-r-xl italic animate-slideDown">
                    <i class="fas fa-check-circle mr-2"></i> <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error_msg)): ?>
                <div
                    class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm font-bold rounded-r-xl italic animate-slideDown">
                    <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <!-- Content Area -->
            <div
                class="bg-white rounded-[40px] border border-gray-100 shadow-sm p-10 min-h-[600px] relative overflow-hidden">
                <?php if ($active_tab === 'bookings'): ?>
                    <div class="space-y-12">
                        <!-- Pending Bookings Section -->
                        <div>
                            <div class="flex items-center justify-between mb-8">
                                <h3 class="text-xl font-black text-gray-900 uppercase italic tracking-tighter">Đơn Đặt Sân
                                    Chờ Duyệt</h3>
                                <span
                                    class="px-4 py-1.5 bg-yellow-100 text-yellow-600 text-[10px] font-black rounded-full uppercase tracking-widest">Cần
                                    xử lý</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                                <?php
                                $pending_query = "SELECT b.*, u.name as user_name, p.name as pitch_name 
                                                 FROM bookings b 
                                                 JOIN users u ON b.user_id = u.id 
                                                 JOIN pitches p ON b.pitch_id = p.id 
                                                 WHERE b.status = 'pending'
                                                 ORDER BY b.created_at DESC";
                                $pending_result = $conn->query($pending_query);
                                if ($pending_result->num_rows === 0): ?>
                                    <div
                                        class="col-span-full py-12 text-center bg-gray-50 rounded-[32px] border border-dashed border-gray-200">
                                        <p class="text-gray-400 font-bold italic uppercase text-xs tracking-widest">Không có yêu
                                            cầu chờ duyệt</p>
                                    </div>
                                <?php else:
                                    while ($booking = $pending_result->fetch_assoc()): ?>
                                        <div
                                            class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm hover:shadow-xl transition-all group relative overflow-hidden">
                                            <div class="flex items-center gap-4 mb-6">
                                                <div
                                                    class="w-14 h-14 bg-yellow-50 rounded-2xl flex items-center justify-center text-xl text-yellow-600 shadow-sm border border-yellow-100 group-hover:bg-yellow-500 group-hover:text-white transition-all">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <div>
                                                    <span
                                                        class="text-[9px] text-gray-300 font-bold uppercase tracking-tighter">#BK-<?php echo $booking['id']; ?></span>
                                                    <p class="text-[11px] text-gray-500 font-bold mb-1 uppercase">
                                                        <?php echo $booking['pitch_name']; ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="space-y-3 mb-8">
                                                <div class="flex justify-between items-center text-[10px] font-bold">
                                                    <span class="text-gray-400">Khách hàng:</span>
                                                    <span class="text-gray-900"><?php echo $booking['user_name']; ?></span>
                                                </div>
                                                <div class="flex justify-between items-center text-[10px] font-bold">
                                                    <span class="text-gray-400">Thời gian:</span>
                                                    <span class="text-gray-900"><?php echo $booking['time_slot']; ?> |
                                                        <?php echo $booking['booking_date']; ?></span>
                                                </div>
                                            </div>
                                            <div class="flex gap-3">
                                                <a href="?tab=bookings&approve_booking=<?php echo $booking['id']; ?>"
                                                    class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-all hover:scale-[1.05] shadow-md text-[10px] uppercase text-center">DUYỆT</a>
                                                <a href="?tab=bookings&cancel_booking=<?php echo $booking['id']; ?>"
                                                    onclick="return confirm('Xác nhận hủy đơn này?')"
                                                    class="flex-1 py-3 bg-white text-red-500 border border-red-100 rounded-xl font-bold transition-all hover:bg-red-50 text-[10px] uppercase text-center">HỦY</a>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- All Bookings Table Section -->
                        <div>
                            <div class="flex items-center justify-between mb-8">
                                <h3 class="text-xl font-black text-gray-900 uppercase italic tracking-tighter">Danh Sách Tất
                                    Cả Đặt Sân</h3>
                                <div class="flex gap-4">
                                    <span
                                        class="px-4 py-1.5 bg-blue-50 text-blue-600 text-[10px] font-black rounded-full uppercase tracking-widest">Tổng
                                        quan</span>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-gray-100">
                                            <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                Mã/Sân</th>
                                            <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                Khách Hàng</th>
                                            <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                Thời Gian</th>
                                            <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                Tổng Tiền</th>
                                            <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                Trạng Thái</th>
                                            <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <?php
                                        $all_bookings_query = "SELECT b.*, u.name as user_name, p.name as pitch_name 
                                                             FROM bookings b 
                                                             JOIN users u ON b.user_id = u.id 
                                                             JOIN pitches p ON b.pitch_id = p.id 
                                                             ORDER BY b.created_at DESC";
                                        $all_bookings_result = $conn->query($all_bookings_query);
                                        while ($booking = $all_bookings_result->fetch_assoc()):
                                            $status_class = '';
                                            $status_text = '';
                                            switch ($booking['status']) {
                                                case 'pending':
                                                    $status_class = 'bg-yellow-100 text-yellow-600';
                                                    $status_text = 'Chờ duyệt';
                                                    break;
                                                case 'confirmed':
                                                    $status_class = 'bg-green-100 text-green-600';
                                                    $status_text = 'Đã duyệt';
                                                    break;
                                                case 'cancelled':
                                                    $status_class = 'bg-red-100 text-red-600';
                                                    $status_text = 'Đã hủy';
                                                    break;
                                                case 'completed':
                                                    $status_class = 'bg-blue-100 text-blue-600';
                                                    $status_text = 'Hoàn thành';
                                                    break;
                                            }
                                            ?>
                                            <tr class="group hover:bg-gray-50/50 transition-colors">
                                                <td class="py-6">
                                                    <p class="text-[10px] font-black text-gray-300 mb-1">
                                                        #BK-<?php echo $booking['id']; ?></p>
                                                    <p class="text-[11px] font-black text-gray-800 uppercase italic">
                                                        <?php echo $booking['pitch_name']; ?>
                                                    </p>
                                                </td>
                                                <td class="py-6">
                                                    <p class="text-[11px] font-bold text-gray-900">
                                                        <?php echo $booking['user_name']; ?>
                                                    </p>
                                                </td>
                                                <td class="py-6">
                                                    <p class="text-[11px] font-bold text-gray-900">
                                                        <?php echo $booking['booking_date']; ?>
                                                    </p>
                                                    <p class="text-[10px] font-medium text-gray-400 italic">
                                                        <?php echo $booking['time_slot']; ?>
                                                    </p>
                                                </td>
                                                <td class="py-6">
                                                    <p class="text-[11px] font-black text-green-700">
                                                        <?php echo number_format($booking['total_price'], 0, ',', '.'); ?>đ
                                                    </p>
                                                </td>
                                                <td class="py-6">
                                                    <span
                                                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest <?php echo $status_class; ?>">
                                                        <?php echo $status_text; ?>
                                                    </span>
                                                </td>
                                                <td class="py-6">
                                                    <?php if ($booking['status'] === 'pending'): ?>
                                                        <div class="flex gap-2">
                                                            <a href="?tab=bookings&approve_booking=<?php echo $booking['id']; ?>"
                                                                class="w-8 h-8 bg-green-50 text-green-600 rounded-lg flex items-center justify-center hover:bg-green-600 hover:text-white transition-all shadow-sm"><i
                                                                    class="fas fa-check"></i></a>
                                                            <a href="?tab=bookings&cancel_booking=<?php echo $booking['id']; ?>"
                                                                onclick="return confirm('Hủy đơn này?')"
                                                                class="w-8 h-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm"><i
                                                                    class="fas fa-times"></i></a>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-[10px] text-gray-300 italic font-medium">No actions</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php elseif ($active_tab === 'matching'): ?>
                    <div class="space-y-12">
                        <!-- Pending Challenges -->
                        <div>
                            <div class="flex items-center justify-between mb-8">
                                <h3 class="text-xl font-black text-gray-900 uppercase italic tracking-tighter">Bài Đăng Tìm
                                    Đối Chờ Duyệt</h3>
                                <span
                                    class="px-4 py-1.5 bg-yellow-100 text-yellow-600 text-[10px] font-black rounded-full uppercase tracking-widest">Cần
                                    duyệt</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <?php
                                $challenges_query = "SELECT tc.*, t.name as team_name, t.logo 
                                                 FROM team_challenges tc 
                                                 JOIN teams t ON tc.challenging_team_id = t.id 
                                                 WHERE tc.status = 'pending'
                                                 ORDER BY tc.created_at DESC";
                                $challenges_res = $conn->query($challenges_query);

                                if ($challenges_res->num_rows === 0): ?>
                                    <div
                                        class="col-span-full py-12 text-center bg-gray-50 rounded-[32px] border border-dashed border-gray-200">
                                        <p class="text-gray-400 font-bold italic uppercase text-xs tracking-widest">Không có bài
                                            đăng chờ duyệt</p>
                                    </div>
                                <?php else:
                                    while ($challenge = $challenges_res->fetch_assoc()): ?>
                                        <div
                                            class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm hover:shadow-xl transition-all group relative overflow-hidden">
                                            <div class="flex items-center gap-4 mb-6">
                                                <img src="<?php echo $challenge['logo']; ?>"
                                                    class="w-14 h-14 rounded-2xl object-cover shadow-sm border border-gray-100" />
                                                <div>
                                                    <span
                                                        class="text-[9px] text-gray-300 font-bold uppercase tracking-tighter">#POST-<?php echo $challenge['id']; ?></span>
                                                    <p class="text-[11px] text-gray-500 font-bold mb-1 uppercase">
                                                        <?php echo $challenge['team_name']; ?>
                                                    </p>
                                                    <span
                                                        class="bg-blue-50 text-blue-600 text-[9px] font-black px-2 py-0.5 rounded uppercase"><?php echo $challenge['sport']; ?></span>
                                                </div>
                                            </div>
                                            <div class="space-y-3 mb-6 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                                <p class="text-sm font-medium italic text-gray-600 line-clamp-2">
                                                    "<?php echo $challenge['message']; ?>"</p>
                                                <div
                                                    class="pt-4 border-t border-gray-200 mt-4 flex justify-between text-[11px] font-bold text-gray-500">
                                                    <span><i class="fas fa-clock mr-1"></i>
                                                        <?php echo $challenge['match_time']; ?></span>
                                                    <span><i class="fas fa-calendar mr-1"></i>
                                                        <?php echo $challenge['match_date']; ?></span>
                                                </div>
                                            </div>
                                            <div class="flex gap-3">
                                                <a href="?tab=matching&approve_challenge=<?php echo $challenge['id']; ?>"
                                                    class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-all hover:scale-[1.05] shadow-md text-[10px] uppercase text-center">DUYỆT
                                                    ĐĂNG</a>
                                                <a href="?tab=matching&reject_challenge=<?php echo $challenge['id']; ?>"
                                                    onclick="return confirm('Từ chối bài đăng này?')"
                                                    class="flex-1 py-3 bg-white text-red-500 border border-red-100 rounded-xl font-bold transition-all hover:bg-red-50 text-[10px] uppercase text-center">TỪ
                                                    CHỐI</a>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- All Challenges History -->
                        <div>
                            <div class="flex items-center justify-between mb-8">
                                <h3 class="text-xl font-black text-gray-900 uppercase italic tracking-tighter">Lịch Sử Tất
                                    Cả Bài Đăng</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-gray-100">
                                            <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                Mã/Đội</th>
                                            <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                Môn/Trình Độ</th>
                                            <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                Thời Gian</th>
                                            <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                Địa Điểm</th>
                                            <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                Trạng Thái</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <?php
                                        $all_challenges_query = "SELECT tc.*, t.name as team_name 
                                                               FROM team_challenges tc 
                                                               JOIN teams t ON tc.challenging_team_id = t.id 
                                                               ORDER BY tc.created_at DESC";
                                        $all_challenges_res = $conn->query($all_challenges_query);
                                        while ($challenge = $all_challenges_res->fetch_assoc()):
                                            $st_class = '';
                                            $st_text = '';
                                            switch ($challenge['status']) {
                                                case 'pending':
                                                    $st_class = 'bg-yellow-100 text-yellow-600';
                                                    $st_text = 'Chờ duyệt';
                                                    break;
                                                case 'open':
                                                    $st_class = 'bg-green-100 text-green-600';
                                                    $st_text = 'Đang mở';
                                                    break;
                                                case 'accepted':
                                                    $st_class = 'bg-blue-100 text-blue-600';
                                                    $st_text = 'Đã nhận kèo';
                                                    break;
                                                case 'cancelled':
                                                    $st_class = 'bg-red-100 text-red-600';
                                                    $st_text = 'Đã hủy';
                                                    break;
                                            }
                                            ?>
                                            <tr class="group hover:bg-gray-50/50 transition-colors">
                                                <td class="py-6">
                                                    <p class="text-[10px] font-black text-gray-300 mb-1">
                                                        #POST-<?php echo $challenge['id']; ?></p>
                                                    <p class="text-[11px] font-black text-gray-800 uppercase italic">
                                                        <?php echo $challenge['team_name']; ?>
                                                    </p>
                                                </td>
                                                <td class="py-6">
                                                    <span
                                                        class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[9px] font-black rounded uppercase"><?php echo $challenge['sport']; ?></span>
                                                    <p class="text-[10px] font-bold text-gray-400 mt-1">
                                                        <?php echo $challenge['level']; ?>
                                                    </p>
                                                </td>
                                                <td class="py-6">
                                                    <p class="text-[11px] font-bold text-gray-900">
                                                        <?php echo $challenge['match_date']; ?>
                                                    </p>
                                                    <p class="text-[10px] font-medium text-gray-400 italic">
                                                        <?php echo $challenge['match_time']; ?>
                                                    </p>
                                                </td>
                                                <td class="py-6">
                                                    <p class="text-[11px] font-bold text-gray-600 italic line-clamp-1">
                                                        <?php echo $challenge['location']; ?>
                                                    </p>
                                                </td>
                                                <td class="py-6">
                                                    <span
                                                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest <?php echo $st_class; ?>">
                                                        <?php echo $st_text; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php elseif ($active_tab === 'pitches'): ?>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <?php
                        $res = $conn->query("SELECT * FROM pitches ORDER BY id DESC");
                        while ($pitch = $res->fetch_assoc()):
                            ?>
                            <div
                                class="p-8 border border-gray-50 bg-gray-50/50 rounded-[32px] flex items-center gap-6 group hover:bg-white hover:shadow-xl hover:border-green-100 transition-all">
                                <div class="w-24 h-24 bg-white rounded-[24px] overflow-hidden border border-gray-100">
                                    <img src="<?php echo $pitch['image']; ?>"
                                        class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" />
                                </div>
                                <div class="flex-grow">
                                    <h4
                                        class="font-black text-lg uppercase italic text-gray-900 mb-1 group-hover:text-green-600 transition-colors">
                                        <?php echo $pitch['name']; ?>
                                    </h4>
                                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-4">Loại hình:
                                        <?php echo $pitch['type']; ?>
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <p class="text-xl font-black text-green-700 tracking-tighter">
                                            <?php echo number_format($pitch['price'], 0, ',', '.'); ?>đ<span
                                                class="text-[10px] text-gray-400 ml-1">/trận</span>
                                        </p>
                                        <div class="flex gap-2">
                                            <button
                                                onclick='openEditModal(<?php echo htmlspecialchars(json_encode($pitch), ENT_QUOTES, 'UTF-8'); ?>)'
                                                class="w-10 h-10 bg-white border border-gray-100 text-blue-500 rounded-xl hover:bg-blue-50 transition-all shadow-sm flex items-center justify-center"><i
                                                    class="fas fa-edit"></i></button>
                                            <a href="?tab=pitches&delete_pitch=<?php echo $pitch['id']; ?>"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa sân này?')"
                                                class="w-10 h-10 bg-white border border-gray-100 text-red-500 rounded-xl hover:bg-red-50 transition-all shadow-sm flex items-center justify-center"><i
                                                    class="fas fa-trash"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php elseif ($active_tab === 'store'): ?>
                    <div class="space-y-16">
                        <!-- Section 1: Đơn Hàng Mới -->
                        <div>
                            <div class="flex items-center justify-between mb-8">
                                <h3 class="text-xl font-black text-gray-900 uppercase italic tracking-tighter">Đơn Hàng Chờ
                                    Xử Lý</h3>
                                <span
                                    class="px-4 py-1.5 bg-blue-100 text-blue-600 text-[10px] font-black rounded-full uppercase tracking-widest">Cần
                                    xử lý</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php
                                $orders_query = "SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.status = 'pending' ORDER BY o.created_at DESC";
                                $orders_result = $conn->query($orders_query);
                                if ($orders_result->num_rows === 0): ?>
                                    <div
                                        class="col-span-full py-12 text-center bg-gray-50 rounded-[32px] border border-dashed border-gray-200">
                                        <p class="text-gray-400 font-bold italic uppercase text-xs tracking-widest">Không có đơn
                                            hàng mới</p>
                                    </div>
                                <?php endif;
                                while ($order = $orders_result->fetch_assoc()):
                                    ?>
                                    <div
                                        class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm hover:shadow-xl transition-all group relative overflow-hidden">
                                        <div class="flex items-center gap-4 mb-6">
                                            <div
                                                class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-xl text-blue-600 shadow-sm border border-blue-100 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                                <i class="fas fa-shopping-bag"></i>
                                            </div>
                                            <div>
                                                <span
                                                    class="text-[9px] text-gray-300 font-bold uppercase tracking-tighter">#ORD-<?php echo $order['id']; ?></span>
                                                <p class="text-[11px] text-gray-500 font-bold mb-2 uppercase">
                                                    <?php echo $order['user_name']; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="space-y-3 mb-8 text-[10px] font-bold">
                                            <div class="flex justify-between items-center"><span class="text-gray-400">Ngày
                                                    đặt:</span><span
                                                    class="text-gray-900"><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></span>
                                            </div>
                                            <div class="flex justify-between items-center"><span class="text-gray-400">Tổng
                                                    tiền:</span><span
                                                    class="text-blue-700 font-black text-sm"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</span>
                                            </div>
                                        </div>
                                        <div class="flex gap-3">
                                            <a href="?tab=store&approve_order=<?php echo $order['id']; ?>"
                                                class="flex-1 py-3 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:shadow-lg shadow-blue-600/20 transition-all active:scale-95 text-center lowercase first-letter:uppercase italic">GIAO
                                                HÀNG</a>
                                            <a href="?tab=store&cancel_order=<?php echo $order['id']; ?>"
                                                onclick="return confirm('Hủy đơn này?')"
                                                class="flex-1 py-3 bg-white text-red-400 border border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-50 hover:text-red-600 transition-all text-center italic">HỦY</a>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>

                        <!-- Section 2: Kho Hàng -->
                        <div class="pt-16 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-8">
                                <h3 class="text-xl font-black text-gray-900 uppercase italic tracking-tighter">Quản Lý Kho
                                    Hàng</h3>
                                <span
                                    class="px-4 py-1.5 bg-gray-100 text-gray-600 text-[10px] font-black rounded-full uppercase tracking-widest">Sản
                                    phẩm</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php
                                $res = $conn->query("SELECT * FROM products ORDER BY id DESC");
                                while ($product = $res->fetch_assoc()): ?>
                                    <div
                                        class="p-8 border border-gray-50 bg-gray-50/50 rounded-[40px] flex items-center gap-6 group hover:bg-white hover:shadow-xl hover:border-blue-100 transition-all">
                                        <div
                                            class="w-24 h-24 bg-white rounded-[24px] overflow-hidden border border-gray-100 shadow-sm flex-shrink-0">
                                            <img src="<?php echo $product['image']; ?>"
                                                class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" />
                                        </div>
                                        <div class="flex-grow">
                                            <div class="flex justify-between items-start mb-1">
                                                <h4
                                                    class="font-black text-sm uppercase italic text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-1">
                                                    <?php echo $product['name']; ?>
                                                </h4>
                                            </div>
                                            <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-4">Kho:
                                                <span
                                                    class="<?php echo $product['stock'] < 10 ? 'text-red-500' : 'text-green-600'; ?>"><?php echo $product['stock']; ?>
                                                    SP</span>
                                            </p>
                                            <div class="flex items-center justify-between">
                                                <p class="text-lg font-black text-blue-700 tracking-tighter">
                                                    <?php echo number_format($product['price'], 0, ',', '.'); ?>đ
                                                </p>
                                                <div class="flex gap-2">
                                                    <button
                                                        onclick='openEditProductModal(<?php echo htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8'); ?>)'
                                                        class="w-9 h-9 bg-white border border-gray-100 text-blue-500 rounded-xl hover:bg-blue-50 transition-all shadow-sm flex items-center justify-center"><i
                                                            class="fas fa-edit text-xs"></i></button>
                                                    <a href="?tab=store&delete_product=<?php echo $product['id']; ?>"
                                                        onclick="return confirm('Xóa sản phẩm này?')"
                                                        class="w-9 h-9 bg-white border border-gray-100 text-red-500 rounded-xl hover:bg-red-50 transition-all shadow-sm flex items-center justify-center"><i
                                                            class="fas fa-trash text-xs"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>

                <?php elseif ($active_tab === 'news'): ?>
                    <div class="space-y-8">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-xl font-black text-gray-900 uppercase italic tracking-tighter">Quản Lý Tin Tức
                            </h3>
                            <span
                                class="px-4 py-1.5 bg-orange-100 text-orange-600 text-[10px] font-black rounded-full uppercase tracking-widest">
                                <?php
                                $count_news = $conn->query("SELECT COUNT(*) as total FROM news")->fetch_assoc()['total'];
                                echo $count_news;
                                ?> Bài viết
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <?php
                            $res = $conn->query("SELECT * FROM news ORDER BY created_at DESC");
                            while ($item = $res->fetch_assoc()):
                                ?>
                                <div
                                    class="bg-white p-6 rounded-[32px] border border-gray-100 flex gap-6 hover:shadow-2xl hover:border-orange-100 transition-all duration-500 group relative">
                                    <div class="w-32 h-32 rounded-2xl overflow-hidden flex-shrink-0 shadow-md">
                                        <img src="<?php echo $item['image']; ?>"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                    </div>
                                    <div class="flex-grow flex flex-col justify-center">
                                        <div class="flex justify-between items-start mb-3">
                                            <span
                                                class="bg-orange-50 text-orange-600 text-[9px] font-black px-3 py-1.5 rounded-full uppercase tracking-widest italic border border-orange-100">
                                                <?php echo $item['category']; ?>
                                            </span>
                                            <div class="flex gap-2">
                                                <button
                                                    onclick='openEditNewsModal(<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>)'
                                                    class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm border border-blue-100">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </button>
                                                <a href="?tab=news&delete_news=<?php echo $item['id']; ?>"
                                                    onclick="return confirm('Xóa tin này?')"
                                                    class="w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm border border-red-100">
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <h4
                                            class="font-black text-gray-900 line-clamp-2 uppercase italic mb-3 text-sm leading-tight tracking-tighter">
                                            <?php echo $item['title']; ?>
                                        </h4>
                                        <div class="flex items-center gap-3 mt-auto">
                                            <div
                                                class="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center text-[10px] font-black text-gray-400 border border-gray-200">
                                                <?php echo strtoupper(substr($item['author'], 0, 1)); ?>
                                            </div>
                                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">
                                                <?php echo date('d/m/Y', strtotime($item['created_at'])); ?> <span
                                                    class="mx-1 opacity-30">|</span> BY <?php echo $item['author']; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                <?php elseif ($active_tab === 'users'): ?>
                    <div class="space-y-8">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-xl font-black text-gray-900 uppercase italic tracking-tighter">Quản Lý Tài Khoản
                            </h3>
                            <span
                                class="px-4 py-1.5 bg-purple-100 text-purple-600 text-[10px] font-black rounded-full uppercase tracking-widest">
                                <?php
                                $count_res = $conn->query("SELECT COUNT(*) as total FROM users");
                                echo $count_res->fetch_assoc()['total'];
                                ?> Thành viên
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                            Người dùng</th>
                                        <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Liên
                                            hệ</th>
                                        <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Vai
                                            trò</th>

                                        <th class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Ngày
                                            tham gia</th>
                                        <th
                                            class="py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                            Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <?php
                                    $users_res = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
                                    while ($u = $users_res->fetch_assoc()): ?>
                                        <tr class="group hover:bg-gray-50/50 transition-colors">
                                            <td class="py-6">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center text-gray-500 font-black text-sm uppercase">
                                                        <?php echo substr($u['name'], 0, 1); ?>
                                                    </div>
                                                    <div>
                                                        <p class="text-[11px] font-black text-gray-900 uppercase italic">
                                                            <?php echo $u['name']; ?>
                                                        </p>
                                                        <p class="text-[9px] text-gray-400 font-bold">ID:
                                                            #USR-<?php echo $u['id']; ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-6">
                                                <p class="text-[11px] font-bold text-gray-600"><?php echo $u['email']; ?></p>
                                            </td>
                                            <td class="py-6">
                                                <?php if ($u['role'] === 'admin'): ?>
                                                    <span
                                                        class="px-3 py-1 bg-red-50 text-red-600 text-[9px] font-black rounded-full uppercase tracking-widest border border-red-100 italic">ADMIN</span>
                                                <?php else: ?>
                                                    <span
                                                        class="px-3 py-1 bg-blue-50 text-blue-600 text-[9px] font-black rounded-full uppercase tracking-widest border border-blue-100 italic">USER</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="py-6">
                                                <p class="text-[11px] font-bold text-gray-500">
                                                    <?php echo date('d/m/Y', strtotime($u['created_at'])); ?>
                                                </p>
                                            </td>
                                            <td class="py-6 text-right">
                                                <div class="flex gap-2 justify-end">
                                                    <button
                                                        onclick='openEditUserModal(<?php echo htmlspecialchars(json_encode($u), ENT_QUOTES, 'UTF-8'); ?>)'
                                                        class="w-9 h-9 bg-white border border-gray-100 text-blue-500 rounded-xl hover:bg-blue-50 transition-all shadow-sm flex items-center justify-center">
                                                        <i class="fas fa-user-edit text-xs"></i>
                                                    </button>
                                                    <a href="?tab=users&delete_user=<?php echo $u['id']; ?>"
                                                        onclick="return confirm('Xóa người dùng này? Thao tác không thể hoàn tác!')"
                                                        class="w-9 h-9 bg-white border border-gray-100 text-red-500 rounded-xl hover:bg-red-50 transition-all shadow-sm flex items-center justify-center">
                                                        <i class="fas fa-trash-alt text-xs"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>



                <?php elseif ($active_tab === 'stats'): ?>
                    <?php
                    // Tạo bảng bảo trì nếu chưa có
                    $conn->query("CREATE TABLE IF NOT EXISTS maintenance (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        item_name VARCHAR(255) NOT NULL,
                        cost DECIMAL(10, 2) NOT NULL,
                        description TEXT,
                        status ENUM('pending', 'completed') DEFAULT 'pending',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )");

                    // Lấy dữ liệu thống kê tổng quát
                    $total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
                    $total_revenue_bookings = $conn->query("SELECT SUM(total_price) as total FROM bookings WHERE status != 'cancelled'")->fetch_assoc()['total'] ?? 0;
                    $total_revenue_orders = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'")->fetch_assoc()['total'] ?? 0;
                    $total_maintenance = $conn->query("SELECT SUM(cost) as total FROM maintenance WHERE status = 'completed'")->fetch_assoc()['total'] ?? 0;

                    // Lấy dữ liệu cho biểu đồ 7 ngày gần nhất
                    $days = [];
                    $booking_data = [];
                    $order_data = [];

                    for ($i = 6; $i >= 0; $i--) {
                        $date = date('Y-m-d', strtotime("-$i days"));
                        $days[] = date('d/m', strtotime($date));

                        $b_val = $conn->query("SELECT SUM(total_price) as total FROM bookings WHERE DATE(booking_date) = '$date' AND status != 'cancelled'")->fetch_assoc()['total'] ?? 0;
                        $booking_data[] = $b_val;

                        $o_val = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE DATE(created_at) = '$date' AND status != 'cancelled'")->fetch_assoc()['total'] ?? 0;
                        $order_data[] = $o_val;
                    }
                    ?>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                    <div class="space-y-12">
                        <!-- Overview Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div
                                class="bg-gradient-to-br from-green-500 to-green-600 p-8 rounded-[32px] text-white shadow-lg shadow-green-500/20 group hover:scale-[1.02] transition-all">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-xl">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-widest opacity-80">Tổng đơn đặt
                                        sân</span>
                                </div>
                                <h4 class="text-3xl font-black italic tracking-tighter mb-1">
                                    <?php echo number_format($total_bookings); ?>
                                </h4>
                                <p class="text-[10px] font-bold opacity-60 uppercase italic">Lượt đăng ký</p>
                            </div>

                            <div
                                class="bg-gradient-to-br from-blue-500 to-blue-600 p-8 rounded-[32px] text-white shadow-lg shadow-blue-500/20 group hover:scale-[1.02] transition-all">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-xl">
                                        <i class="fas fa-futbol"></i>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-widest opacity-80">Doanh thu đặt
                                        sân</span>
                                </div>
                                <h4 class="text-3xl font-black italic tracking-tighter mb-1">
                                    <?php echo number_format($total_revenue_bookings, 0, ',', '.'); ?>đ
                                </h4>
                                <p class="text-[10px] font-bold opacity-60 uppercase italic">Tổng tiền sân</p>
                            </div>

                            <div
                                class="bg-gradient-to-br from-purple-500 to-purple-600 p-8 rounded-[32px] text-white shadow-lg shadow-purple-500/20 group hover:scale-[1.02] transition-all">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-xl">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-widest opacity-80">Doanh thu bán
                                        đồ</span>
                                </div>
                                <h4 class="text-3xl font-black italic tracking-tighter mb-1">
                                    <?php echo number_format($total_revenue_orders, 0, ',', '.'); ?>đ
                                </h4>
                                <p class="text-[10px] font-bold opacity-60 uppercase italic">Tổng bán lẻ</p>
                            </div>

                            <div
                                class="bg-gradient-to-br from-red-500 to-red-600 p-8 rounded-[32px] text-white shadow-lg shadow-red-500/20 group hover:scale-[1.02] transition-all">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-xl">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-widest opacity-80">Chi phí bảo
                                        trì</span>
                                </div>
                                <h4 class="text-3xl font-black italic tracking-tighter mb-1">
                                    <?php echo number_format($total_maintenance, 0, ',', '.'); ?>đ
                                </h4>
                                <p class="text-[10px] font-bold opacity-60 uppercase italic">Số tiền đã sửa chữa</p>
                            </div>
                        </div>

                        <!-- Revenue Chart -->
                        <div class="bg-white p-10 rounded-[40px] border border-gray-100 shadow-sm relative overflow-hidden">
                            <div class="flex items-center justify-between mb-10">
                                <div>
                                    <h3 class="text-xl font-black text-gray-900 uppercase italic tracking-tighter">Biểu Đồ
                                        Doanh Thu</h3>
                                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider italic">Số liệu 7
                                        ngày gần nhất</p>
                                </div>
                                <div class="flex gap-4">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                                        <span class="text-[10px] font-black uppercase text-gray-500">Sân bóng</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 bg-purple-500 rounded-full"></span>
                                        <span class="text-[10px] font-black uppercase text-gray-500">Cửa hàng</span>
                                    </div>
                                </div>
                            </div>
                            <div class="h-[400px]">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>

                        <!-- Maintenance Section -->
                        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                            <!-- Maintenance History -->
                            <div class="xl:col-span-2 bg-white border border-gray-100 rounded-[40px] p-10 shadow-sm">
                                <div class="flex items-center justify-between mb-8">
                                    <h3 class="text-xl font-black text-gray-900 uppercase italic tracking-tighter">Danh Sách
                                        Bảo Trì & Sửa Chữa</h3>
                                    <span
                                        class="px-4 py-1.5 bg-red-50 text-red-600 text-[10px] font-black rounded-full uppercase tracking-widest">Cần
                                        lưu ý</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="border-b border-gray-100">
                                                <th
                                                    class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                    Hạng mục</th>
                                                <th
                                                    class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                    Chi phí</th>
                                                <th
                                                    class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                                    Trạng thái</th>
                                                <th
                                                    class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                                    Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            <?php
                                            $m_res = $conn->query("SELECT * FROM maintenance ORDER BY created_at DESC");
                                            if ($m_res->num_rows === 0): ?>
                                                <tr>
                                                    <td colspan="4"
                                                        class="py-10 text-center text-gray-400 font-bold italic text-xs uppercase tracking-widest">
                                                        Chưa có bản ghi bảo trì nào</td>
                                                </tr>
                                            <?php endif;
                                            while ($m = $m_res->fetch_assoc()):
                                                ?>
                                                <tr class="group hover:bg-gray-50 transition-colors">
                                                    <td class="py-6">
                                                        <p class="text-[11px] font-black text-gray-900 uppercase italic">
                                                            <?php echo $m['item_name']; ?>
                                                        </p>
                                                        <p class="text-[9px] text-gray-400 font-bold truncate max-w-[200px]">
                                                            <?php echo $m['description']; ?>
                                                        </p>
                                                    </td>
                                                    <td class="py-6">
                                                        <p class="text-[11px] font-black text-red-600 tracking-tighter">
                                                            <?php echo number_format($m['cost'], 0, ',', '.'); ?>đ
                                                        </p>
                                                    </td>
                                                    <td class="py-6 text-center">
                                                        <?php if ($m['status'] === 'pending'): ?>
                                                            <span
                                                                class="px-3 py-1 bg-yellow-50 text-yellow-600 text-[8px] font-black rounded-full uppercase tracking-widest italic border border-yellow-100">Chờ
                                                                xử lý</span>
                                                        <?php else: ?>
                                                            <span
                                                                class="px-3 py-1 bg-green-50 text-green-600 text-[8px] font-black rounded-full uppercase tracking-widest italic border border-green-100">Đã
                                                                hoàn thành</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="py-6 text-right">
                                                        <div class="flex gap-2 justify-end">
                                                            <?php if ($m['status'] === 'pending'): ?>
                                                                <a href="?tab=stats&complete_maintenance=<?php echo $m['id']; ?>"
                                                                    class="w-8 h-8 bg-green-50 text-green-600 rounded-lg flex items-center justify-center hover:bg-green-600 hover:text-white transition-all"><i
                                                                        class="fas fa-check text-[10px]"></i></a>
                                                            <?php endif; ?>
                                                            <a href="?tab=stats&delete_maintenance=<?php echo $m['id']; ?>"
                                                                onclick="return confirm('Xóa bảo trì này?')"
                                                                class="w-8 h-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all"><i
                                                                    class="fas fa-trash-alt text-[10px]"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Add Maintenance Form -->
                            <div class="bg-gray-900 rounded-[40px] p-10 text-white shadow-2xl relative overflow-hidden">
                                <div class="relative z-10">
                                    <h3 class="text-xl font-black uppercase italic tracking-tighter mb-2">Thêm Hạng Mục</h3>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase italic mb-8">Cập nhật chi phí
                                        sửa chữa</p>

                                    <form method="POST" class="space-y-6">
                                        <input type="hidden" name="action" value="add_maintenance">
                                        <div>
                                            <label
                                                class="block text-[8px] font-black text-gray-500 uppercase tracking-widest mb-2">Tên
                                                hạng mục sửa chữa</label>
                                            <input type="text" name="item_name" required placeholder="VD: Thay lưới sân 7"
                                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-xs font-bold outline-none focus:ring-4 focus:ring-red-500/20 transition-all font-italic">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[8px] font-black text-gray-500 uppercase tracking-widest mb-2">Chi
                                                phí dự kiến (VNĐ)</label>
                                            <input type="number" name="cost" required placeholder="500000"
                                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-xs font-bold outline-none focus:ring-4 focus:ring-red-500/20 transition-all font-italic">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[8px] font-black text-gray-500 uppercase tracking-widest mb-2">Mô
                                                tả chi tiết</label>
                                            <textarea name="description" rows="3"
                                                placeholder="Ghi chú về tình trạng hư hỏng..."
                                                class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-xs font-bold outline-none focus:ring-4 focus:ring-red-500/20 transition-all font-italic"></textarea>
                                        </div>
                                        <button type="submit"
                                            class="w-full bg-red-600 hover:bg-red-700 py-5 rounded-2xl font-black uppercase tracking-widest text-[11px] shadow-lg shadow-red-600/20 transition-all active:scale-95 italic">
                                            LƯU KẾ HOẠCH
                                        </button>
                                    </form>
                                </div>
                                <i
                                    class="fas fa-tools absolute -right-10 -bottom-10 text-[200px] text-white/5 pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    <script>
                        const ctx = document.getElementById('revenueChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: <?php echo json_encode($days); ?>,
                                datasets: [
                                    {
                                        label: 'Sân bóng',
                                        data: <?php echo json_encode($booking_data); ?>,
                                        borderColor: '#3b82f6',
                                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                        borderWidth: 4,
                                        tension: 0.4,
                                        fill: true,
                                        pointRadius: 6,
                                        pointBackgroundColor: '#fff',
                                        pointBorderWidth: 3
                                    },
                                    {
                                        label: 'Cửa hàng',
                                        data: <?php echo json_encode($order_data); ?>,
                                        borderColor: '#a855f7',
                                        backgroundColor: 'rgba(168, 85, 247, 0.1)',
                                        borderWidth: 4,
                                        tension: 0.4,
                                        fill: true,
                                        pointRadius: 6,
                                        pointBackgroundColor: '#fff',
                                        pointBorderWidth: 3
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: '#f3f4f6', borderDash: [5, 5] },
                                        ticks: {
                                            font: { family: 'Inter', weight: '700', size: 10 },
                                            color: '#9ca3af',
                                            callback: function (value) {
                                                return (value / 1000).toFixed(0) + 'k';
                                            }
                                        }
                                    },
                                    x: {
                                        grid: { display: false },
                                        ticks: {
                                            font: { family: 'Inter', weight: '700', size: 10 },
                                            color: '#9ca3af'
                                        }
                                    }
                                }
                            }
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<!-- Add Pitch Modal -->
<div id="add-pitch-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')"></div>
    <div
        class="bg-white w-full max-w-2xl rounded-[40px] overflow-hidden shadow-2xl relative z-10 p-10 md:p-14 animate-slideDown max-h-[90vh] overflow-y-auto italic">
        <div class="flex justify-between items-center mb-10">
            <h3 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter">Thêm Sân Mới</h3>
            <button onclick="document.getElementById('add-pitch-modal').classList.add('hidden')"
                class="w-12 h-12 bg-gray-50 text-gray-400 hover:text-red-500 rounded-2xl flex items-center justify-center transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="add_pitch">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tên
                        Sân</label>
                    <input type="text" name="name" required placeholder="VD: Sân vận động Mỹ Đình"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Vị
                        Trí</label>
                    <input type="text" name="location" required placeholder="VD: Nam Từ Liêm, Hà Nội"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Giá
                        Thuê (/Trận)</label>
                    <input type="number" name="price" required placeholder="300000"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Loại
                        Sân</label>
                    <select name="type"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
                        <option value="SÂN 5">SÂN 5</option>
                        <option value="SÂN 7">SÂN 7</option>
                        <option value="SÂN 11">SÂN 11</option>
                        <option value="SÂN ĐƠN (Cầu lông)">SÂN ĐƠN</option>
                        <option value="SÂN ĐÔI (Cầu lông)">SÂN ĐÔI</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Môn
                        Thể Thao</label>
                    <select name="sport"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
                        <option value="football">Bóng Đá</option>
                        <option value="badminton">Cầu Lông</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Link
                    Ảnh Sân</label>
                <input type="url" name="image" placeholder="https://unsplash.com/..."
                    class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
            </div>

            <div class="pt-6">
                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] uppercase tracking-widest text-sm">
                    THÊM SÂN MỚI
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Product Modal -->
<div id="add-product-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')"></div>
    <div
        class="bg-white w-full max-w-2xl rounded-[40px] overflow-hidden shadow-2xl relative z-10 p-10 md:p-14 animate-slideDown max-h-[90vh] overflow-y-auto italic">
        <div class="flex justify-between items-center mb-10">
            <h3 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter">Thêm Sản Phẩm Mới
            </h3>
            <button onclick="document.getElementById('add-product-modal').classList.add('hidden')"
                class="w-12 h-12 bg-gray-50 text-gray-400 hover:text-red-500 rounded-2xl flex items-center justify-center transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="add_product">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tên
                        Sản Phẩm</label>
                    <input type="text" name="name" required placeholder="VD: Nike Mercurial 2024"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition-all font-bold italic">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Danh
                        Mục</label>
                    <select name="category"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition-all font-bold italic">
                        <option value="GIÀY">GIÀY</option>
                        <option value="ÁO ĐẤU">ÁO ĐẤU</option>
                        <option value="PHỤ KIỆN">PHỤ KIỆN</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Giá
                        Bán (VNĐ)</label>
                    <input type="number" name="price" required placeholder="1250000"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition-all font-bold italic">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Số
                        Lượng Kho</label>
                    <input type="number" name="stock" required placeholder="50"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition-all font-bold italic">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Link
                    Ảnh</label>
                <input type="url" name="image" placeholder="https://images.unsplash.com/..."
                    class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition-all font-bold italic">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Mô Tả
                    Sản Phẩm</label>
                <textarea name="description" rows="3" placeholder="Mô tả ngắn về sản phẩm..."
                    class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition-all font-bold italic"></textarea>
            </div>

            <div class="flex items-center gap-3 bg-gray-50 p-4 rounded-2xl">
                <input type="checkbox" name="is_hot" id="add-prod-hot" class="w-5 h-5 rounded accent-blue-600">
                <label for="add-prod-hot" class="text-xs font-black uppercase text-gray-500 italic">Đặt làm sản
                    phẩm nổi bật (HOT)</label>
            </div>

            <div class="pt-6">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] uppercase tracking-widest text-sm">
                    THÊM SẢN PHẨM NGAY
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div id="edit-product-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')"></div>
    <div
        class="bg-white w-full max-w-2xl rounded-[40px] overflow-hidden shadow-2xl relative z-10 p-10 md:p-14 animate-slideDown max-h-[90vh] overflow-y-auto italic">
        <div class="flex justify-between items-center mb-10">
            <h3 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter">Sửa Sản Phẩm</h3>
            <button onclick="document.getElementById('edit-product-modal').classList.add('hidden')"
                class="w-12 h-12 bg-gray-50 text-gray-400 hover:text-red-500 rounded-2xl flex items-center justify-center transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="update_product">
            <input type="hidden" name="id" id="edit-prod-id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tên
                        Sản
                        Phẩm</label>
                    <input type="text" name="name" id="edit-prod-name" required
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition-all font-bold italic">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Danh
                        Mục</label>
                    <select name="category" id="edit-prod-category"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition-all font-bold italic">
                        <option value="GIÀY">GIÀY</option>
                        <option value="ÁO ĐẤU">ÁO ĐẤU</option>
                        <option value="PHỤ KIỆN">PHỤ KIỆN</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Giá
                        Bán
                        (VNĐ)</label>
                    <input type="number" name="price" id="edit-prod-price" required
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition-all font-bold italic">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Số
                        Lượng
                        Kho</label>
                    <input type="number" name="stock" id="edit-prod-stock" required
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition-all font-bold italic">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Link
                    Ảnh</label>
                <input type="url" name="image" id="edit-prod-image"
                    class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition-all font-bold italic">
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Mô
                    Tả</label>
                <textarea name="description" id="edit-prod-description" rows="3"
                    class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition-all font-bold italic"></textarea>
            </div>

            <div class="flex items-center gap-3 bg-gray-50 p-4 rounded-2xl">
                <input type="checkbox" name="is_hot" id="edit-prod-hot" class="w-5 h-5 rounded accent-blue-600">
                <label for="edit-prod-hot" class="text-xs font-black uppercase text-gray-500 italic">Sản phẩm
                    nổi bật
                    (HOT)</label>
            </div>

            <div class="pt-6">
                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] uppercase tracking-widest text-sm">
                    CẬP NHẬT SẢN PHẨM
                </button>
            </div>
        </form>
    </div>
</div>
<!-- Edit User Modal -->
<div id="edit-user-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')"></div>
    <div
        class="bg-white w-full max-w-md rounded-[40px] overflow-hidden shadow-2xl relative z-10 p-10 md:p-14 animate-slideDown italic">
        <div class="flex justify-between items-center mb-10">
            <h3 class="text-2xl font-black text-gray-900 uppercase italic tracking-tighter">Sửa Người Dùng</h3>
            <button onclick="document.getElementById('edit-user-modal').classList.add('hidden')"
                class="w-10 h-10 bg-gray-50 text-gray-400 hover:text-red-500 rounded-xl flex items-center justify-center transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="update_user">
            <input type="hidden" name="id" id="edit-user-id">

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tên &
                    Email</label>
                <p id="edit-user-info"
                    class="text-sm font-bold text-gray-900 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                </p>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Vai
                    Trò</label>
                <select name="role" id="edit-user-role"
                    class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-purple-500/10 outline-none transition-all font-bold italic">
                    <option value="user">USER</option>
                    <option value="admin">ADMIN</option>
                </select>
            </div>



            <div class="pt-6">
                <button type="submit"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] uppercase tracking-widest text-sm">
                    CẬP NHẬT THAY ĐỔI
                </button>
            </div>
        </form>
    </div>
</div>
<div id="edit-pitch-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')"></div>
    <div
        class="bg-white w-full max-w-2xl rounded-[40px] overflow-hidden shadow-2xl relative z-10 p-10 md:p-14 animate-slideDown max-h-[90vh] overflow-y-auto italic">
        <div class="flex justify-between items-center mb-10">
            <h3 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter">Sửa Sân Bóng</h3>
            <button onclick="document.getElementById('edit-pitch-modal').classList.add('hidden')"
                class="w-12 h-12 bg-gray-50 text-gray-400 hover:text-red-500 rounded-2xl flex items-center justify-center transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="update_pitch">
            <input type="hidden" name="id" id="edit-id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tên
                        Sân</label>
                    <input type="text" name="name" id="edit-name" required
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Vị
                        Trí</label>
                    <input type="text" name="location" id="edit-location" required
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Giá
                        Thuê</label>
                    <input type="number" name="price" id="edit-price" required
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Loại
                        Sân</label>
                    <select name="type" id="edit-type"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
                        <option value="SÂN 5">SÂN 5</option>
                        <option value="SÂN 7">SÂN 7</option>
                        <option value="SÂN 11">SÂN 11</option>
                        <option value="SÂN ĐƠN (Cầu lông)">SÂN ĐƠN</option>
                        <option value="SÂN ĐÔI (Cầu lông)">SÂN ĐÔI</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Môn
                        Thể Thao</label>
                    <select name="sport" id="edit-sport"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
                        <option value="football">Bóng Đá</option>
                        <option value="badminton">Cầu Lông</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Link
                    Ảnh Sân</label>
                <input type="url" name="image" id="edit-image"
                    class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
            </div>

            <div class="pt-6">
                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] uppercase tracking-widest text-sm">
                    CẬP NHẬT THÔNG TIN
                </button>
            </div>
        </form>
    </div>

    <!-- Add News Modal -->
    <div id="add-news-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/80 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')">
        </div>
        <div
            class="bg-white w-full max-w-2xl rounded-[40px] overflow-hidden shadow-2xl relative z-10 p-10 md:p-14 animate-slideDown max-h-[90vh] overflow-y-auto italic text-left">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter">Đăng Tin Tức Mới
                </h3>
                <button onclick="document.getElementById('add-news-modal').classList.add('hidden')"
                    class="w-12 h-12 bg-gray-50 text-gray-400 hover:text-red-500 rounded-2xl flex items-center justify-center transition-all">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="add_news">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tiêu
                            Đề</label>
                        <input type="text" name="title" required placeholder="Nhập tiêu đề tin tức..."
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-orange-500/10 outline-none transition-all font-bold italic">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Danh
                            Mục</label>
                        <input type="text" name="category" required placeholder="VD: Tin Nóng, Giải Đấu..."
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-orange-500/10 outline-none transition-all font-bold italic">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tác
                            Giả</label>
                        <input type="text" name="author" placeholder="Admin"
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-orange-500/10 outline-none transition-all font-bold italic">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Link
                            Ảnh</label>
                        <input type="url" name="image" placeholder="https://images.unsplash.com/..."
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-orange-500/10 outline-none transition-all font-bold italic">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Mô
                        Tả Ngắn</label>
                    <textarea name="description" rows="2" placeholder="Tóm tắt nội dung tin tức..."
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-orange-500/10 outline-none transition-all font-bold italic"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Nội
                        Dung Chi Tiết</label>
                    <textarea name="content" id="news-content-editor" rows="6"
                        placeholder="Viết nội dung bài viết ở đây..."
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-orange-500/10 outline-none transition-all font-bold italic"></textarea>
                </div>

                <div class="pt-6">
                    <button type="submit"
                        class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] uppercase tracking-widest text-sm">
                        XUẤT BẢN TIN TỨC
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit News Modal -->
    <div id="edit-news-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/80 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')">
        </div>
        <div
            class="bg-white w-full max-w-2xl rounded-[40px] overflow-hidden shadow-2xl relative z-10 p-10 md:p-14 animate-slideDown max-h-[90vh] overflow-y-auto italic text-left">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter">Sửa Tin Tức</h3>
                <button onclick="document.getElementById('edit-news-modal').classList.add('hidden')"
                    class="w-12 h-12 bg-gray-50 text-gray-400 hover:text-red-500 rounded-2xl flex items-center justify-center transition-all">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="update_news">
                <input type="hidden" name="id" id="edit-news-id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tiêu
                            Đề</label>
                        <input type="text" name="title" id="edit-news-title" required
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-orange-500/10 outline-none transition-all font-bold italic">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Danh
                            Mục</label>
                        <input type="text" name="category" id="edit-news-category" required
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-orange-500/10 outline-none transition-all font-bold italic">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tác
                            Giả</label>
                        <input type="text" name="author" id="edit-news-author"
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-orange-500/10 outline-none transition-all font-bold italic">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Link
                            Ảnh</label>
                        <input type="url" name="image" id="edit-news-image"
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-orange-500/10 outline-none transition-all font-bold italic">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Mô
                        Tả Ngắn</label>
                    <textarea name="description" id="edit-news-description" rows="2"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-orange-500/10 outline-none transition-all font-bold italic"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Nội
                        Dung Chi Tiết</label>
                    <textarea name="content" id="edit-news-content-editor" rows="6"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-orange-500/10 outline-none transition-all font-bold italic"></textarea>
                </div>

                <div class="pt-6">
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] uppercase tracking-widest text-sm">
                        CẬP NHẬT TIN TỨC
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Add Team Modal -->
    <div id="add-team-modal" class="fixed inset-0 z-[999] hidden flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/80 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')">
        </div>
        <div
            class="bg-white w-full max-w-md rounded-[40px] overflow-hidden shadow-2xl relative z-10 p-10 animate-slideDown italic">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-2xl font-black text-gray-900 uppercase italic tracking-tighter">Thêm Đội Mới</h3>
                <button onclick="document.getElementById('add-team-modal').classList.add('hidden')"
                    class="w-10 h-10 bg-gray-50 text-gray-400 hover:text-red-500 rounded-xl flex items-center justify-center transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="add_team">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tên
                        Đội</label>
                    <input type="text" name="name" required
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-red-500/10 outline-none transition-all font-bold italic">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Điểm Khởi
                        Đầu</label>
                    <input type="number" name="points" value="0"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-red-500/10 outline-none transition-all font-bold italic">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Link
                        Logo</label>
                    <input type="url" name="logo" placeholder="https://..."
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-red-500/10 outline-none transition-all font-bold italic">
                </div>
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[8px] font-black text-gray-400 uppercase mb-2">Số Trận</label>
                        <input type="number" name="matches_played" value="0"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="block text-[8px] font-black text-green-500 uppercase mb-2">Thắng</label>
                        <input type="number" name="wins" value="0"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="block text-[8px] font-black text-yellow-500 uppercase mb-2">Hòa</label>
                        <input type="number" name="draws" value="0"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="block text-[8px] font-black text-red-500 uppercase mb-2">Bại</label>
                        <input type="number" name="losses" value="0"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl font-bold">
                    </div>
                </div>
                <div class="pt-6">
                    <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] uppercase tracking-widest text-sm">XÁC
                        NHẬN THÊM</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Team Modal -->
    <div id="edit-team-modal" class="fixed inset-0 z-[999] hidden flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/80 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')">
        </div>
        <div
            class="bg-white w-full max-w-2xl rounded-[40px] overflow-hidden shadow-2xl relative z-10 p-10 animate-slideDown italic">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-2xl font-black text-gray-900 uppercase italic tracking-tighter">Sửa Đội Bóng</h3>
                <button onclick="document.getElementById('edit-team-modal').classList.add('hidden')"
                    class="w-10 h-10 bg-gray-50 text-gray-400 hover:text-red-500 rounded-xl flex items-center justify-center transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="update_team">
                <input type="hidden" name="id" id="edit-team-id">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tên
                            Đội</label>
                        <input type="text" name="name" id="edit-team-name" required
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-red-500/10 outline-none transition-all font-bold italic">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Điểm
                            Số</label>
                        <input type="number" name="points" id="edit-team-points" required
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-red-500/10 outline-none transition-all font-bold italic">
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[8px] font-black text-gray-400 uppercase mb-2">Số Trận</label>
                        <input type="number" name="matches_played" id="edit-team-matches"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="block text-[8px] font-black text-green-500 uppercase mb-2">Thắng</label>
                        <input type="number" name="wins" id="edit-team-wins"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="block text-[8px] font-black text-yellow-500 uppercase mb-2">Hòa</label>
                        <input type="number" name="draws" id="edit-team-draws"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="block text-[8px] font-black text-red-500 uppercase mb-2">Bại</label>
                        <input type="number" name="losses" id="edit-team-losses"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl font-bold">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Link
                        Logo</label>
                    <input type="url" name="logo" id="edit-team-logo"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-red-500/10 outline-none transition-all font-bold italic">
                </div>
                <div class="pt-6">
                    <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] uppercase tracking-widest text-sm">LƯU
                        THAY ĐỔI</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Player Stats Modal -->
    <div id="edit-player-stats-modal" class="fixed inset-0 z-[999] hidden flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/80 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')">
        </div>
        <div
            class="bg-white w-full max-w-md rounded-[40px] overflow-hidden shadow-2xl relative z-10 p-10 animate-slideDown italic">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-2xl font-black text-gray-900 uppercase italic tracking-tighter">Chỉ Số Cá Nhân</h3>
                <button onclick="document.getElementById('edit-player-stats-modal').classList.add('hidden')"
                    class="w-10 h-10 bg-gray-50 text-gray-400 hover:text-red-500 rounded-xl flex items-center justify-center transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="update_player_stats">
                <input type="hidden" name="id" id="edit-player-id">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Cầu
                        Thủ</label>
                    <p id="edit-player-name"
                        class="text-sm font-black text-gray-900 bg-gray-50 p-4 rounded-2xl border border-gray-100 italic uppercase">
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Bàn
                            Thắng</label>
                        <input type="number" name="goals" id="edit-player-goals" required
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 outline-none transition-all font-bold italic">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Danh
                            Hiệu MVP</label>
                        <input type="number" name="mvp" id="edit-player-mvp" required
                            class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-yellow-500/10 outline-none transition-all font-bold italic">
                    </div>
                </div>
                <div class="pt-6">
                    <button type="submit"
                        class="w-full bg-gray-900 hover:bg-black text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] uppercase tracking-widest text-sm">CẬP
                        NHẬT CHỈ SỐ</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Các hàm mở Modal - Đưa lên đầu để đảm bảo luôn khả dụng
        function openEditModal(pitch) {
            document.getElementById('edit-id').value = pitch.id;
            document.getElementById('edit-name').value = pitch.name;
            document.getElementById('edit-location').value = pitch.location;
            document.getElementById('edit-price').value = pitch.price;
            document.getElementById('edit-type').value = pitch.type;
            document.getElementById('edit-sport').value = pitch.sport;
            document.getElementById('edit-image').value = pitch.image;
            document.getElementById('edit-pitch-modal').classList.remove('hidden');
        }

        function openEditProductModal(product) {
            document.getElementById('edit-prod-id').value = product.id;
            document.getElementById('edit-prod-name').value = product.name;
            document.getElementById('edit-prod-category').value = product.category;
            document.getElementById('edit-prod-price').value = product.price;
            document.getElementById('edit-prod-stock').value = product.stock;
            document.getElementById('edit-prod-image').value = product.image;
            document.getElementById('edit-prod-description').value = product.description;
            document.getElementById('edit-prod-hot').checked = product.is_hot == 1;
            document.getElementById('edit-product-modal').classList.remove('hidden');
        }

        function openEditUserModal(user) {
            document.getElementById('edit-user-id').value = user.id;
            document.getElementById('edit-user-info').innerText = user.name + " (" + user.email + ")";
            document.getElementById('edit-user-role').value = user.role;
            document.getElementById('edit-user-points').value = user.points;
            document.getElementById('edit-user-modal').classList.remove('hidden');
        }

        function openEditNewsModal(news) {
            document.getElementById('edit-news-id').value = news.id;
            document.getElementById('edit-news-title').value = news.title;
            document.getElementById('edit-news-category').value = news.category;
            document.getElementById('edit-news-image').value = news.image;
            document.getElementById('edit-news-author').value = news.author;
            document.getElementById('edit-news-description').value = news.description;
            if (typeof editEditor !== 'undefined' && editEditor) {
                editEditor.setData(news.content);
            } else {
                const contentEl = document.getElementById('edit-news-content-editor');
                if (contentEl) contentEl.value = news.content;
            }
            document.getElementById('edit-news-modal').classList.remove('hidden');
        }

        function openEditTeamModal(team) {
            document.getElementById('edit-team-id').value = team.id;
            document.getElementById('edit-team-name').value = team.name;
            document.getElementById('edit-team-points').value = team.points;
            document.getElementById('edit-team-matches').value = team.matches;
            document.getElementById('edit-team-wins').value = team.wins;
            document.getElementById('edit-team-draws').value = team.draws;
            document.getElementById('edit-team-losses').value = team.losses;
            document.getElementById('edit-team-logo').value = team.logo;
            document.getElementById('edit-team-modal').classList.remove('hidden');
        }

        function openEditPlayerStatsModal(player) {
            document.getElementById('edit-player-id').value = player.id;
            document.getElementById('edit-player-name').innerText = player.name;
            document.getElementById('edit-player-goals').value = player.goals;
            document.getElementById('edit-player-mvp').value = player.mvp;
            document.getElementById('edit-player-stats-modal').classList.remove('hidden');
        }

        // Khởi tạo CKEditor với kiểm tra tồn tại
        let addEditor, editEditor;

        document.addEventListener('DOMContentLoaded', function () {
            const addNewsEl = document.querySelector('#news-content-editor');
            if (addNewsEl && typeof ClassicEditor !== 'undefined') {
                ClassicEditor
                    .create(addNewsEl)
                    .then(editor => { addEditor = editor; })
                    .catch(error => { console.error('CKEditor Add Error:', error); });
            }

            const editNewsEl = document.querySelector('#edit-news-content-editor');
            if (editNewsEl && typeof ClassicEditor !== 'undefined') {
                ClassicEditor
                    .create(editNewsEl)
                    .then(editor => { editEditor = editor; })
                    .catch(error => { console.error('CKEditor Edit Error:', error); });
            }

            // Sync editors before form submit
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function () {
                    if (addEditor) addEditor.updateSourceElement();
                    if (editEditor) editEditor.updateSourceElement();
                });
            });
        });
    </script>

    <style>
        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-slideDown {
            animation: slideDown 0.5s ease-out forwards;
        }
    </style>

    <!-- Add User Modal (For Rankings) -->
    <div id="add-user-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/80 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')">
        </div>
        <div
            class="bg-white w-full max-w-md rounded-[40px] overflow-hidden shadow-2xl relative z-10 p-10 md:p-14 animate-slideDown italic">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-2xl font-black text-gray-900 uppercase italic tracking-tighter">Thêm Cầu Thủ Mới</h3>
                <button onclick="document.getElementById('add-user-modal').classList.add('hidden')"
                    class="w-10 h-10 bg-gray-50 text-gray-400 hover:text-red-500 rounded-xl flex items-center justify-center transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="add_user">

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tên Cầu
                        Thủ</label>
                    <input type="text" name="name" required placeholder="Nguyễn Văn A"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
                </div>

                <div>
                    <label
                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Email</label>
                    <input type="email" name="email" required placeholder="email@example.com"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Mật
                        Khẩu</label>
                    <input type="password" name="password" required placeholder="******"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Vai
                        Trò</label>
                    <select name="role"
                        class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-bold italic">
                        <option value="user">Cầu Thủ (User)</option>
                        <option value="admin">Quản Trị Viên</option>
                    </select>
                </div>



                <div class="pt-6">
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] uppercase tracking-widest text-sm">
                        THÊM CẦU THỦ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>