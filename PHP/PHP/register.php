<?php
$page_title = 'Đăng Ký - SuperSports';
$current_page = 'register';
require_once 'includes/header.php';
require_once 'backend/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ các thông tin.';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } else {
        // Kiểm tra email đã tồn tại chưa
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = 'Email này đã được đăng ký sử dụng.';
        } else {
            // Mã hóa mật khẩu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';

            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $success = 'Đăng ký tài khoản thành công! Bạn có thể đăng nhập ngay bây giờ.';
            } else {
                $error = 'Có lỗi xảy ra trong quá trình đăng ký: ' . $conn->error;
            }
        }
    }
}
?>

<div class="min-h-[90vh] flex items-center justify-center bg-gray-50 px-4 py-12">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-10 border border-gray-100">
        <div class="text-center mb-8">
            <i class="fas fa-user-plus text-green-600 text-4xl mb-4"></i>
            <h2 class="text-3xl font-black text-gray-900">Tạo Tài Khoản</h2>
            <p class="text-gray-500 mt-2">Tham gia cộng đồng thể thao lớn nhất</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm font-bold rounded-r-xl italic">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div
                class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-bold rounded-r-xl italic">
                <i class="fas fa-check-circle mr-2"></i>
                <?php echo $success; ?>
                <div class="mt-2">
                    <a href="login.php" class="underline hover:text-green-800">Đến trang đăng nhập ngay &rarr;</a>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php" class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 italic">Họ và Tên</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="name" placeholder="Ví dụ: Nguyễn Văn A"
                        class="w-full pl-12 pr-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-green-500 outline-none transition-all font-medium"
                        value="<?php echo htmlspecialchars($name ?? ''); ?>" required />
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 italic">Email của bạn</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="email" name="email" placeholder="ten@vidu.com"
                        class="w-full pl-12 pr-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-green-500 outline-none transition-all font-medium"
                        value="<?php echo htmlspecialchars($email ?? ''); ?>" required />
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 italic">Mật Khẩu</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="password" name="password" placeholder="••••••••"
                        class="w-full pl-12 pr-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-green-500 outline-none transition-all font-medium"
                        required />
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 italic">Xác Nhận Mật Khẩu</label>
                <div class="relative">
                    <i class="fas fa-shield-alt absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="password" name="confirm_password" placeholder="••••••••"
                        class="w-full pl-12 pr-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-green-500 outline-none transition-all font-medium"
                        required />
                </div>
            </div>

            <div class="flex items-center text-sm text-gray-600">
                <input type="checkbox" class="mr-2 accent-green-600 w-4 h-4" required />
                <span>Tôi đồng ý với <a href="#" class="text-green-600 font-bold hover:underline">Điều khoản & Chính
                        sách</a></span>
            </div>

            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] uppercase tracking-widest">
                Tạo Tài Khoản
            </button>
        </form>

        <div class="mt-8 text-center text-gray-500 text-sm">
            Đã có tài khoản? <a href="login.php"
                class="text-green-600 font-black hover:underline uppercase tracking-wider">Đăng nhập ngay</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>