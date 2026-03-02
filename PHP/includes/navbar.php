<?php
// Determine current page if not set
if (!isset($current_page)) {
    $current_page = 'home';
}

function isActive($page_name, $current_page)
{
    return $page_name === $current_page
        ? 'bg-green-100 text-green-700'
        : 'text-gray-600 hover:text-green-600 hover:bg-green-50';
}
?>
<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="index.php" class="flex-shrink-0 flex items-center">
                    <i class="fas fa-bolt text-green-600 text-3xl mr-2"></i>
                    <span class="text-2xl font-black text-gray-900 tracking-tighter italic">SUPER<span
                            class="text-green-600">SPORTS</span></span>
                </a>
                <div class="hidden md:ml-8 md:flex md:space-x-4">
                    <a href="index.php"
                        class="px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2 <?php echo isActive('home', $current_page); ?>">
                        <i class="fas fa-home"></i> Trang Chủ
                    </a>
                    <a href="pitches.php"
                        class="px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2 <?php echo isActive('pitches', $current_page); ?>">
                        <i class="fas fa-futbol"></i> Sân Bóng
                    </a>
                    <a href="matching.php"
                        class="px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2 <?php echo isActive('matching', $current_page); ?>">
                        <i class="fas fa-swords"></i> Tìm Đối Thủ
                    </a>
                    <a href="store.php"
                        class="px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2 <?php echo isActive('store', $current_page); ?>">
                        <i class="fas fa-shopping-cart"></i> Cửa Hàng
                    </a>

                    <a href="news.php"
                        class="px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2 <?php echo isActive('news', $current_page); ?>">
                        <i class="fas fa-newspaper"></i> Tin Tức
                    </a>

                    <?php if (isset($_SESSION['user']) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
                        <a href="admin.php"
                            class="px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2 <?php echo isActive('admin', $current_page); ?> text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100">
                            <i class="fas fa-shield-alt"></i> Admin
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex items-center">
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="flex items-center space-x-4">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-gray-900">
                                <?php echo htmlspecialchars($_SESSION['user']['name']); ?>
                            </p>
                            <p class="text-xs text-gray-500 uppercase">
                                <?php echo htmlspecialchars($_SESSION['user']['role']); ?>
                            </p>
                        </div>
                        <a href="logout.php"
                            class="bg-gray-100 p-2 rounded-full text-gray-600 hover:text-red-600 transition-colors">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="login.php"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                        Đăng Nhập
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>