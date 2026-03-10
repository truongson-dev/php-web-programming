<?php
$page_title = 'Đăng Nhập - SuperSports';
$current_page = 'login';
require_once 'includes/header.php';
require_once 'backend/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Vui lòng nhập email và mật khẩu.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            $_SESSION['role'] = $user['role']; // Thêm dòng này để khớp với kiểm tra ở admin.php

            if ($user['role'] === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Email hoặc mật khẩu không chính xác.';
        }
    }
}
?>

<div class="min-h-[80vh] flex items-center justify-center bg-gray-50 px-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-10 border border-gray-100">
        <div class="text-center mb-8">
            <i class="fas fa-futbol text-green-600 text-4xl mb-4"></i>
            <h2 class="text-3xl font-black text-gray-900">Chào Mừng Trở Lại</h2>
            <p class="text-gray-500 mt-2">Đăng nhập để đặt sân và tìm đội ngay</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm font-bold rounded-r-xl italic">
                <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                <input type="email" name="email" placeholder="ten@vidu.com"
                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-green-500 outline-none transition-all"
                    required />
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Mật Khẩu</label>
                <input type="password" name="password" placeholder="••••••••"
                    class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-green-500 outline-none transition-all"
                    required />
            </div>
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center text-gray-600 cursor-pointer">
                    <input type="checkbox" class="mr-2 accent-green-600" />
                    Ghi nhớ tôi
                </label>
                <a href="#" class="text-green-600 font-bold hover:underline">Quên mật khẩu?</a>
            </div>
            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02]">
                Đăng Nhập
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-gray-500 text-sm">
                Chưa có tài khoản? <a href="register.php" class="text-green-600 font-black hover:underline">Đăng ký
                    ngay</a>
            </p>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-400 font-medium">DEMO ACCOUNTS:</p>
            <p class="text-xs text-gray-500 mt-1 italic">Admin: son@gmail.com / 123456</p>
            <p class="text-xs text-gray-500 italic">User: son1@gmail.com / 123456</p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>