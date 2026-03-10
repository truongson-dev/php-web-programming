<?php if (isset($current_page) && $current_page === 'admin'): ?>

<?php else: ?>
    <footer class="bg-[#1a1c1e] text-white pt-20 pb-10 mt-auto border-t border-white/5">
        <div class="w-full px-10 lg:px-20">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <!-- Brand Column -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/20">
                            <i class="fas fa-futbol text-white text-xl"></i>
                        </div>
                        <span class="text-2xl font-black tracking-tighter italic uppercase">SUPER<span
                                class="text-green-500">SPORTS</span></span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed font-medium italic">
                        Nền tảng kết nối đam mê thể thao hàng đầu Việt Nam. Đặt sân dễ dàng, ghép đội nhanh chóng và mua sắm
                        đồ dùng thể thao chất lượng.
                    </p>
                    <div class="flex gap-4">
                        <a href="#"
                            class="w-10 h-10 bg-white/5 hover:bg-green-600 rounded-xl flex items-center justify-center transition-all group">
                            <i class="fab fa-facebook-f text-gray-400 group-hover:text-white transition-colors"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-white/5 hover:bg-green-600 rounded-xl flex items-center justify-center transition-all group">
                            <i class="fab fa-tiktok text-gray-400 group-hover:text-white transition-colors"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-white/5 hover:bg-green-600 rounded-xl flex items-center justify-center transition-all group">
                            <i class="fab fa-youtube text-gray-400 group-hover:text-white transition-colors"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-sm font-black uppercase tracking-[0.2em] mb-8 text-green-500">Khám Phá</h4>
                    <ul class="space-y-4">
                        <li><a href="pitches.php"
                                class="text-gray-400 hover:text-white text-sm font-bold transition-colors flex items-center gap-2 group italic"><i
                                    class="fas fa-chevron-right text-[10px] text-green-600 group-hover:translate-x-1 transition-transform"></i>
                                Tìm Sân Ngay</a></li>
                        <li><a href="matching.php"
                                class="text-gray-400 hover:text-white text-sm font-bold transition-colors flex items-center gap-2 group italic"><i
                                    class="fas fa-chevron-right text-[10px] text-green-600 group-hover:translate-x-1 transition-transform"></i>
                                Ghép Đội Nhanh</a></li>
                        <li><a href="store.php"
                                class="text-gray-400 hover:text-white text-sm font-bold transition-colors flex items-center gap-2 group italic"><i
                                    class="fas fa-chevron-right text-[10px] text-green-600 group-hover:translate-x-1 transition-transform"></i>
                                Cửa Hàng Đồ Tập</a></li>
                        <li><a href="leaderboard.php"
                                class="text-gray-400 hover:text-white text-sm font-bold transition-colors flex items-center gap-2 group italic"><i
                                    class="fas fa-chevron-right text-[10px] text-green-600 group-hover:translate-x-1 transition-transform"></i>
                                Bảng Xếp Hạng</a></li>
                    </ul>
                </div>

                <!-- Contact Support -->
                <div>
                    <h4 class="text-sm font-black uppercase tracking-[0.2em] mb-8 text-green-500">Hỗ Trợ</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-green-600 mt-1"></i>
                            <span class="text-gray-400 text-sm font-medium">99 Tô Hiến Thành, phường An Hải, TP.Đà
                                Nẵng</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone-alt text-green-600"></i>
                            <span class="text-gray-400 text-sm font-medium">0900.888.999</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-green-600"></i>
                            <span class="text-gray-400 text-sm font-medium">hotro@SuperSports.vn</span>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h4 class="text-sm font-black uppercase tracking-[0.2em] mb-8 text-green-500">Cập Nhật Kèo Mới</h4>
                    <p class="text-gray-400 text-xs mb-6 font-medium italic">Để lại email để nhận thông báo về các trận đấu
                        và ưu đãi sân bãi mới nhất.</p>
                    <div class="relative group">
                        <input type="email" placeholder="Email của bạn..."
                            class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-6 pr-14 text-sm outline-none focus:border-green-500/50 transition-all font-bold placeholder:text-gray-600">
                        <button
                            class="absolute right-2 top-2 bottom-2 w-10 bg-green-600 hover:bg-green-500 rounded-xl flex items-center justify-center transition-all group-focus-within:scale-105 shadow-lg shadow-green-500/20">
                            <i class="fas fa-paper-plane text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex gap-8">
                    <a href="#"
                        class="text-gray-500 hover:text-white text-[10px] font-black uppercase tracking-widest transition-colors">Điều
                        khoản</a>
                    <a href="#"
                        class="text-gray-500 hover:text-white text-[10px] font-black uppercase tracking-widest transition-colors">Chính
                        sách bảo mật</a>
                </div>
            </div>
        </div>
    </footer>
<?php endif; ?>
</body>

</html>