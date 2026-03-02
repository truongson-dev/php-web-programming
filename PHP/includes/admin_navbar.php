<?php
// Admin Navbar
?>
<nav class="bg-gray-900 shadow-xl sticky top-0 z-50 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between h-20">
            <div class="flex items-center">
                <a href="admin.php" class="flex-shrink-0 flex items-center group">
                    <div
                        class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center mr-3 group-hover:rotate-12 transition-transform shadow-lg shadow-green-900/20">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-black text-white tracking-tighter italic">SUPERSPORTS<span
                            class="text-green-500 not-italic">ADMIN</span></span>
                </a>
            </div>

            <div class="flex items-center gap-6">
                <!-- Back to Site -->
                <a href="index.php"
                    class="hidden md:flex items-center gap-2 text-gray-400 hover:text-white transition-colors text-[10px] font-black uppercase tracking-widest border border-gray-700 px-4 py-2 rounded-xl">
                    <i class="fas fa-eye"></i> Website
                </a>

                <?php if (isset($_SESSION['user'])): ?>
                    <div class="flex items-center gap-4 pl-6 border-l border-gray-800">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-black text-white uppercase tracking-wider">
                                <?php echo htmlspecialchars($_SESSION['user']['name']); ?>
                            </p>
                            <p class="text-[10px] text-green-500 font-bold uppercase tracking-widest">
                                QUẢN TRỊ VIÊN
                            </p>
                        </div>
                        <div
                            class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 border border-gray-700">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <!-- Logout Button - Subtle & Clean -->
                        <a href="logout.php"
                            class="w-10 h-10 bg-white/5 hover:bg-red-600/20 text-gray-400 hover:text-red-500 rounded-xl flex items-center justify-center transition-all border border-white/5">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="login.php"
                        class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-green-600/20 active:scale-95">
                        Đăng Nhập
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>