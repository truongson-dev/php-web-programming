<?php
$page_title = 'SuperSports - Hệ Thống Đặt Sân & Tìm Đối Thủ';
$current_page = 'home';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Data Arrays
require_once 'backend/db.php';

// Fetch Promotions
$promotions_result = $conn->query("SELECT * FROM promotions ORDER BY created_at DESC LIMIT 3");
$PROMOTIONS = [];
if ($promotions_result && $promotions_result->num_rows > 0) {
    while ($row = $promotions_result->fetch_assoc()) {
        $PROMOTIONS[] = $row;
    }
}

// Fetch News
$news_result = $conn->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 3");
$NEWS_PREVIEW = [];
if ($news_result && $news_result->num_rows > 0) {
    while ($row = $news_result->fetch_assoc()) {
        $NEWS_PREVIEW[] = $row;
    }
}

// Fetch Testimonials
$testimonials_result = $conn->query("SELECT * FROM testimonials ORDER BY created_at DESC LIMIT 3");
$TESTIMONIALS = [];
if ($testimonials_result && $testimonials_result->num_rows > 0) {
    while ($row = $testimonials_result->fetch_assoc()) {
        $TESTIMONIALS[] = $row;
    }
}

// Fetch Popular Pitches
$pitches_result = $conn->query("SELECT * FROM pitches LIMIT 3");
$POPULAR_PITCHES = [];
if ($pitches_result && $pitches_result->num_rows > 0) {
    while ($row = $pitches_result->fetch_assoc()) {
        $POPULAR_PITCHES[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'location' => $row['location'],
            'price' => (float)$row['price'],
            'type' => $row['type'],
            'sport' => $row['sport'],
            'image' => $row['image']
        ];
    }
}

$BENEFITS = [
    [
        "icon" => "fa-heartbeat",
        "title" => "Cải Thiện Sức Khỏe",
        "desc" => "Tăng cường sức mạnh hệ tim mạch, đốt cháy calo hiệu quả và giúp cơ thể dẻo dai hơn qua từng trận đấu.",
        "color" => "text-red-500",
        "bg" => "bg-red-50"
    ],
    [
        "icon" => "fa-brain",
        "title" => "Giải Tỏa Căng Thẳng",
        "desc" => "Vận động giúp giải phóng endorphin, giảm stress sau những giờ làm việc căng thẳng và mệt mỏi.",
        "color" => "text-blue-500",
        "bg" => "bg-blue-50"
    ],
    [
        "icon" => "fa-users",
        "title" => "Mở Rộng Kết Nối",
        "desc" => "Bóng đá là cầu nối tuyệt vời để gặp gỡ những người bạn mới, xây dựng cộng đồng và tinh thần đồng đội.",
        "color" => "text-green-600",
        "bg" => "bg-green-50"
    ],
    [
        "icon" => "fa-shield-alt",
        "title" => "Rèn Luyện Kỷ Luật",
        "desc" => "Thúc đẩy tính tự giác, tinh thần trách nhiệm và khả năng phối hợp trong một tập thể gắn kết.",
        "color" => "text-orange-500",
        "bg" => "bg-orange-50"
    ]
];
?>

<div class="flex flex-col">
    <!-- Hero Section -->
    <section class="bg-soccer h-[85vh] flex items-center justify-center text-center px-4 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/40 z-0"></div>
        <div class="max-w-4xl animate-fadeIn relative z-10">
            <h1 class="text-4xl md:text-6xl font-black text-white mb-6 drop-shadow-2xl leading-[0.9] uppercase italic">
                KẾT NỐI <span class="text-green-400">TẬN TÂM</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-100 mb-10 font-medium max-w-2xl mx-auto leading-relaxed">
                Hệ thống đặt sân bóng và tìm đối thủ thông minh số 1 Việt Nam. Nơi mọi cầu thủ tìm thấy đồng đội và sân
                chơi ưng ý.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-6">
                <a href="pitches.php"
                    class="px-16 py-6 bg-green-600 hover:bg-green-700 text-white rounded-2xl font-bold transition-all hover:scale-[1.02] shadow-lg flex items-center justify-center gap-4 text-xl uppercase italic">
                    <i class="fas fa-calendar-check mr-2"></i> Đặt sân ngay
                </a>
                <a href="matching.php"
                    class="px-12 py-5 bg-white hover:bg-gray-50 text-green-700 rounded-2xl font-bold transition-all hover:scale-[1.02] shadow-lg border border-gray-100 flex items-center justify-center text-lg uppercase italic">
                    Tìm Đối Thủ
                </a>
            </div>
        </div>
    </section>

    <!-- Popular Pitches Preview -->
    <section class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6 text-left">
                <div>
                    <h2 class="text-sm font-black text-green-600 uppercase tracking-widest mb-4">Sân bóng nổi bật</h2>
                    <h3 class="text-3xl md:text-4xl font-black text-gray-900 italic">Dành Cho Những <span
                            class="text-green-600 uppercase">Kèo Đỉnh Nhất</span></h3>
                </div>
                <a href="pitches.php"
                    class="text-sm font-black text-gray-900 border-b-2 border-green-500 pb-1 hover:text-green-600 transition-all flex items-center gap-2">
                    XEM TẤT CẢ SÂN <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($POPULAR_PITCHES as $pitch): ?>
                <div
                    class="group bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 text-left">
                        <div class="relative h-64 overflow-hidden rounded-t-2xl">
                            <img src="<?php echo $pitch['image']; ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                alt="">
                            <div class="absolute top-6 left-6">
                                <span
                                    class="bg-green-600 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest shadow-xl">
                                    <?php echo $pitch['type']; ?>
                                </span>
                            </div>
                        </div>
                        <div class="p-8">
                            <h4
                                class="text-xl font-black text-gray-900 mb-2 uppercase italic group-hover:text-green-600 transition-colors">
                                <?php echo $pitch['name']; ?>
                            </h4>
                            <p class="text-gray-400 text-sm font-bold mb-8 italic flex items-center">
                                <i class="fas fa-map-marker-alt mr-2 text-green-500"></i>
                                <?php echo $pitch['location']; ?>
                            </p>
                            <div class="flex items-center justify-between pt-6 border-t border-gray-200/50">
                                <div>
                                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Giá từ</p>
                                    <p class="text-xl font-black text-green-700 tracking-tighter">
                                        <?php echo number_format($pitch['price'], 0, ',', '.'); ?>đ
                                    </p>
                                </div>
                                <button onclick="openBooking(<?php echo htmlspecialchars(json_encode($pitch)); ?>)"
                                    class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-2xl font-bold transition-all hover:scale-[1.02] shadow-lg text-[10px] uppercase">
                                    ĐẶT SÂN
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-24 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row items-end justify-between mb-16 gap-6">
                <div class="max-w-2xl">
                    <h2 class="text-sm font-black text-green-600 uppercase tracking-widest mb-4">Giá trị cốt lõi</h2>
                    <h3 class="text-4xl md:text-5xl font-black text-gray-900 leading-tight">
                        Tại Sao Bạn Nên <br /><span
                            class="text-green-600 underline decoration-green-200 underline-offset-8">Chơi Thể
                            Thao?</span>
                    </h3>
                </div>
                <p class="text-gray-500 font-medium max-w-sm italic">
                    Không chỉ là một trò chơi, bóng đá là lối sống giúp bạn hoàn thiện bản thân mỗi ngày.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($BENEFITS as $benefit): ?>
                    <div
                        class="group p-8 bg-white border border-gray-100 rounded-[32px] hover:shadow-2xl hover:shadow-green-100 transition-all duration-500">
                        <div
                            class="w-16 h-16 <?php echo $benefit['bg'] . ' ' . $benefit['color']; ?> flex items-center justify-center rounded-2xl text-2xl mb-8 group-hover:scale-110 transition-transform">
                            <i class="fas <?php echo $benefit['icon']; ?>"></i>
                        </div>
                        <h4 class="text-xl font-black text-gray-900 mb-4"><?php echo $benefit['title']; ?></h4>
                        <p class="text-gray-500 text-sm leading-relaxed">
                            <?php echo $benefit['desc']; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Promotions Section -->
    <section class="py-24 bg-gray-50 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-sm font-black text-green-600 uppercase tracking-[0.3em] mb-4">Ưu đãi độc quyền</h2>
                <h3 class="text-4xl md:text-5xl font-black text-gray-900 italic">Khuyến Mãi <span
                        class="text-green-600">Hấp Dẫn</span></h3>
                <p class="text-gray-500 mt-4 font-medium italic">Tiết kiệm chi phí, bùng nổ đam mê cùng SuperSports</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($PROMOTIONS as $promo): ?>
                    <div class="relative group perspective">
                        <div
                            class="bg-gradient-to-br <?php echo $promo['color']; ?> p-10 rounded-[40px] text-white shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 relative overflow-hidden">
                            <div class="absolute top-[-20px] right-[-20px] opacity-10 text-[120px] rotate-12">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div class="relative z-10">
                                <h4 class="text-sm font-black uppercase tracking-widest opacity-80 mb-2">
                                    <?php echo $promo['title']; ?>
                                </h4>
                                <div class="flex items-baseline gap-2 mb-6">
                                    <span
                                        class="text-6xl font-black tracking-tighter"><?php echo $promo['discount']; ?></span>
                                    <span class="text-xl font-bold">OFF</span>
                                </div>
                                <p class="text-white/80 text-sm mb-10 font-medium leading-relaxed italic h-12">
                                    <?php echo $promo['description']; ?>
                                </p>
                                <div
                                    class="flex items-center justify-between bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20">
                                    <div>
                                        <p class="text-[10px] font-black uppercase opacity-60">Mã ưu đãi</p>
                                        <p class="font-black text-lg tracking-wider"><?php echo $promo['code']; ?></p>
                                    </div>
                                    <button
                                        class="bg-white hover:bg-green-50 text-gray-900 px-6 py-2 rounded-xl font-bold transition-all hover:scale-[1.02] shadow-md text-[10px] uppercase">
                                        SAO CHÉP
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Stats Counter -->
    <section class="bg-green-600 py-20 text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 translate-x-1/2 -translate-y-1/2">
            <i class="fas fa-futbol text-[400px]"></i>
        </div>
        <div class="max-w-7xl mx-auto px-4 relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-12 text-center">
                <?php
                $stats = [
                    ["val" => "500+", "label" => "Sân Bóng Đối Tác"],
                    ["val" => "15K+", "label" => "Cầu Thủ Đăng Ký"],
                    ["val" => "1.2M", "label" => "Lượt Đặt Sân"],
                    ["val" => "24/7", "label" => "Hỗ Trợ Người Chơi"]
                ];
                foreach ($stats as $stat):
                    ?>
                    <div class="animate-pulse">
                        <p class="text-5xl font-black mb-2"><?php echo $stat['val']; ?></p>
                        <div class="h-1 w-12 bg-white/30 mx-auto rounded-full mb-3"></div>
                        <p class="text-green-100 font-bold uppercase text-[10px] tracking-[0.2em]">
                            <?php echo $stat['label']; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Sports News Section -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 italic">Tin Tức Thể Thao</h2>
                    <div class="h-1.5 w-16 bg-green-600 mt-3 rounded-full"></div>
                </div>
                <a href="news.php" class="text-green-600 font-black text-sm hover:underline flex items-center gap-2">
                    XEM TẤT CẢ <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($NEWS_PREVIEW as $item): ?>
                    <div
                        class="bg-white rounded-[32px] overflow-hidden border border-gray-100 group hover:shadow-xl transition-all">
                        <div class="relative h-56 overflow-hidden">
                            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                            <div class="absolute top-4 left-4">
                                <span
                                    class="bg-green-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider">
                                    <?php echo $item['category']; ?>
                                </span>
                            </div>
                        </div>
                        <div class="p-8">
                            <div
                                class="flex items-center text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-3">
                                <i class="far fa-calendar-alt mr-2 text-green-500"></i> <?php echo date('d/m/Y', strtotime($item['created_at'])); ?>
                            </div>
                            <h4
                                class="text-xl font-black text-gray-900 mb-4 leading-snug group-hover:text-green-600 transition-colors uppercase italic">
                                <?php echo $item['title']; ?>
                            </h4>
                            <p class="text-gray-500 text-sm mb-6 line-clamp-2 leading-relaxed italic">
                                <?php echo $item['description']; ?>
                            </p>
                            <a href="news_detail.php?id=<?php echo $item['id']; ?>"
                                class="text-xs font-black text-gray-900 border-b-2 border-green-500 pb-1 hover:text-green-600 transition-colors">
                                ĐỌC TIẾP
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="mb-16">
                <h2 class="text-sm font-black text-green-600 uppercase tracking-[0.3em] mb-4">Cộng đồng nói gì</h2>
                <h3 class="text-4xl md:text-5xl font-black text-gray-900 italic">Nhận Xét Từ <span
                        class="text-green-600">Khách Hàng</span></h3>
                <div class="h-1.5 w-24 bg-green-200 mx-auto mt-6 rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($TESTIMONIALS as $review): ?>
                    <div
                        class="bg-gray-50 p-10 rounded-[40px] border border-gray-100 relative group hover:bg-green-600 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-green-900/20">
                        <div
                            class="absolute top-8 right-10 text-6xl text-gray-200 group-hover:text-green-500 transition-colors opacity-30">
                            <i class="fas fa-quote-right"></i>
                        </div>

                        <div class="flex text-yellow-400 mb-6 gap-1">
                            <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                <i class="fas fa-star text-sm"></i>
                            <?php endfor; ?>
                        </div>

                        <p
                            class="text-gray-600 group-hover:text-white transition-colors text-lg italic leading-relaxed mb-10 relative z-10">
                            "<?php echo $review['content']; ?>"
                        </p>

                        <div class="flex items-center gap-4 border-t border-gray-200 group-hover:border-white/20 pt-8">
                            <img src="<?php echo $review['avatar']; ?>" alt="<?php echo $review['name']; ?>"
                                class="w-14 h-14 rounded-2xl object-cover shadow-lg border-2 border-white group-hover:border-green-400" />
                            <div class="text-left">
                                <h4 class="font-black text-gray-900 group-hover:text-white transition-colors">
                                    <?php echo $review['name']; ?>
                                </h4>
                                <p
                                    class="text-[10px] font-bold text-green-600 group-hover:text-green-200 uppercase tracking-widest">
                                    <?php echo $review['role']; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <div
                class="bg-gradient-to-br from-green-700 to-green-900 rounded-[48px] p-12 md:p-20 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute left-0 top-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2">
                </div>
                <h2
                    class="text-4xl md:text-5xl font-black mb-8 leading-tight relative z-10 italic uppercase tracking-tighter">
                    Sẵn Sàng Để Ra Sân <br />Và Tỏa Sáng?</h2>
                <p class="text-green-100 mb-10 text-lg max-w-xl mx-auto relative z-10 opacity-90 italic">
                    Gia nhập cộng đồng hơn 10.000 cầu thủ ngay hôm nay để nhận được ưu đãi đặt sân đầu tiên.
                </p>
                <div class="flex flex-wrap justify-center gap-6 relative z-10">
                    <a href="login.php"
                        class="px-12 py-5 bg-white text-green-700 rounded-2xl font-black text-lg shadow-xl hover:scale-105 transition-all min-w-[200px]">
                        ĐĂNG KÝ NGAY
                    </a>
                    <a href="pitches.php"
                        class="px-12 py-5 bg-green-500 text-white rounded-2xl font-black text-lg shadow-xl hover:scale-105 transition-all border border-green-400 min-w-[200px]">
                        XEM DANH SÁCH SÂN
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Modals & Scripts -->
    <!-- Booking Modal -->
    <div id="booking-modal" class="fixed inset-0 z-[60] hidden flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/80 backdrop-blur-md" onclick="closeBooking()"></div>
        <div class="bg-white w-full max-w-5xl rounded-[56px] overflow-hidden shadow-2xl relative flex flex-col md:flex-row transform transition-all max-h-[90vh] overflow-y-auto italic border border-white/20">
            <div class="md:w-2/5 relative">
                <img id="modal-image" src="" class="w-full h-full object-cover min-h-[300px]" alt="">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent flex flex-col justify-end p-10 text-white">
                    <span id="modal-type" class="bg-green-600 text-white text-[10px] font-black px-5 py-2 rounded-full uppercase tracking-widest mb-4 w-fit shadow-xl"></span>
                    <h2 id="modal-name" class="text-4xl font-black mb-3 uppercase italic tracking-tighter leading-tight"></h2>
                    <p id="modal-location" class="text-base font-bold opacity-80 mb-8 flex items-center"></p>
                    <div class="bg-white/10 backdrop-blur-xl rounded-[32px] p-8 border border-white/20 shadow-inner">
                        <p class="text-[10px] uppercase font-black opacity-60 mb-2 tracking-widest">Giá cơ bản</p>
                        <p class="text-4xl font-black text-green-400 tracking-tighter"><span id="modal-price"></span>đ<span class="text-sm font-normal text-white ml-2 opacity-60">/giờ</span></p>
                    </div>
                </div>
                <button onclick="closeBooking()" class="absolute top-10 left-10 w-14 h-14 bg-black/40 hover:bg-black/60 text-white rounded-2xl flex items-center justify-center transition-all backdrop-blur-md border border-white/20">
                    <i class="fas fa-arrow-left text-xl"></i>
                </button>
            </div>

            <div class="md:w-3/5 p-10 md:p-16 text-left bg-white">
                <div class="mb-12">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] mb-5 italic flex items-center gap-3">
                        <span class="w-6 h-6 bg-green-100 text-green-600 rounded-lg flex items-center justify-center text-[10px] not-italic">1</span> 
                        Chọn ngày thi đấu
                    </label>
                    <input type="date" id="booking-date" class="w-full px-10 py-6 bg-gray-50 border border-gray-100 rounded-[32px] text-xl font-black outline-none focus:ring-4 focus:ring-green-500/20 transition-all shadow-inner italic">
                </div>

                <div class="mb-12">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] mb-5 italic flex items-center gap-3">
                        <span class="w-6 h-6 bg-green-100 text-green-600 rounded-lg flex items-center justify-center text-[10px] not-italic">2</span> 
                        Chọn khung giờ (90 phút)
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <?php 
                        $TIMES = ["06:00 - 07:30", "07:30 - 09:00", "09:00 - 10:30", "15:00 - 16:30", "16:30 - 18:00", "18:00 - 19:30", "19:30 - 21:00", "21:00 - 22:30"];
                        foreach ($TIMES as $i => $slot): 
                            $isBusy = ($i === 1 || $i === 5); ?>
                            <button
                                onclick="selectSlot('<?php echo $slot; ?>', this)"
                                <?php echo $isBusy ? 'disabled' : ''; ?>
                                class="slot-btn p-6 rounded-[28px] text-sm font-black transition-all border-2 flex flex-col items-center justify-center gap-2 italic <?php echo $isBusy ? 'bg-gray-50 border-gray-100 text-gray-300 cursor-not-allowed' : 'bg-white border-gray-100 text-gray-600 hover:border-green-500 hover:text-green-600 hover:shadow-lg hover:shadow-green-50'; ?>">
                                <span class="text-lg"><?php echo $slot; ?></span>
                                <span class="text-[9px] font-black uppercase tracking-widest opacity-40"><?php echo $isBusy ? 'Hết sân' : 'Trống'; ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bg-gray-900 rounded-[48px] p-10 text-white shadow-2xl relative overflow-hidden italic">
                     <div class="absolute right-[-30px] top-[-30px] opacity-10 rotate-12">
                       <i class="fas fa-receipt text-[180px]"></i>
                     </div>
                     <div class="flex justify-between items-center mb-8 opacity-60 text-[10px] font-black uppercase tracking-[0.3em] relative z-10">
                        <span>Chi tiết thanh toán</span>
                        <span class="bg-white/10 px-3 py-1 rounded-full border border-white/10">1 trận (90 PHÚT)</span>
                     </div>
                     <div class="space-y-4 mb-10 relative z-10 border-b border-white/10 pb-10">
                        <div class="flex justify-between font-black text-base">
                          <span class="opacity-60 uppercase tracking-widest">Giá sân</span>
                          <span id="subtotal-display" class="text-xl">0đ</span>
                        </div>
                     </div>
                     <div class="flex flex-col md:flex-row justify-between md:items-end gap-6 relative z-10">
                        <div>
                          <p class="text-[10px] text-gray-400 font-bold uppercase mb-2 tracking-widest opacity-60">Tổng thanh toán</p>
                          <h4 id="total-display" class="text-4xl md:text-5xl font-black text-green-500 tracking-tighter drop-shadow-lg">0đ</h4>
                        </div>
                        <button onclick="goPayment()" class="w-full md:w-auto bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white px-8 py-4 rounded-2xl font-black text-[11px] uppercase tracking-wider transition-all shadow-xl shadow-green-500/20 active:scale-95 hover:scale-105 border border-white/10 whitespace-nowrap">
                          XÁC NHẬN ĐẶT SÂN
                        </button>
                     </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="payment-modal" class="fixed inset-0 z-[70] hidden flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/90 backdrop-blur-xl" onclick="closePayment()"></div>
        <div class="bg-white w-full max-w-xl rounded-[64px] p-12 md:p-16 shadow-2xl relative text-center animate-slideUp max-h-[90vh] overflow-y-auto italic">
            <button onclick="closePayment()" class="absolute top-12 right-12 text-gray-300 hover:text-gray-900 transition-colors w-14 h-14 flex items-center justify-center rounded-2xl hover:bg-gray-50 border border-gray-100 shadow-sm">
              <i class="fas fa-times text-2xl"></i>
            </button>

            <h2 class="text-4xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic">QUÉT MÃ THANH TOÁN</h2>
            <p class="text-gray-500 mb-12 font-medium italic">Sử dụng ứng dụng Ngân hàng hoặc Ví điện tử để quét</p>

            <div class="bg-gray-50 rounded-[56px] p-12 mb-12 inline-block border-2 border-dashed border-gray-200 shadow-inner">
               <div class="bg-white p-6 rounded-[48px] shadow-2xl mb-10 inline-block border border-gray-100 ring-8 ring-white">
                  <img id="qr-code" src="" alt="Payment QR" class="w-64 h-64 grayscale group-hover:grayscale-0 transition-all duration-700">
               </div>
               <div class="space-y-3">
                 <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Số tiền cần chuyển</p>
                 <p id="payment-total" class="text-6xl font-black text-green-700 tracking-tighter drop-shadow-md">0đ</p>
               </div>
            </div>

            <div class="bg-green-50 rounded-[40px] p-10 text-left mb-12 border border-green-100 shadow-inner relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-10 text-8xl text-green-600 rotate-12 transition-transform group-hover:scale-110">
                    <i class="fas fa-university"></i>
                </div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-green-500"></i> Nội dung chuyển khoản
                </p>
                <div class="flex items-center justify-between">
                    <p id="payment-note" class="font-black text-green-900 bg-green-200/50 px-6 py-3 rounded-2xl inline-block uppercase tracking-wider text-base shadow-sm border border-green-300/30"></p>
                    <button class="w-12 h-12 bg-white text-green-600 rounded-xl flex items-center justify-center hover:bg-green-600 hover:text-white transition-all shadow-sm">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>

            <button onclick="confirmPayment()" class="w-full bg-gray-900 hover:bg-green-600 text-white font-black py-5 rounded-[24px] transition-all shadow-2xl flex items-center justify-center gap-4 text-base uppercase tracking-widest active:scale-95 group">
              XÁC NHẬN ĐÃ THANH TOÁN <i class="fas fa-check-circle text-xl text-green-400 group-hover:text-white transition-colors"></i>
            </button>
        </div>
    </div>

    <!-- Success Notification -->
    <div id="success-notification" class="fixed top-12 left-1/2 -translate-x-1/2 z-[100] hidden bg-gray-900 text-white px-12 py-8 rounded-[40px] shadow-[0_30px_100px_-15px_rgba(0,0,0,0.5)] flex items-center gap-8 border border-green-500/30 ring-8 ring-white/5 animate-slideDown overflow-hidden">
        <div class="absolute left-0 top-0 w-2 h-full bg-green-500"></div>
        <div class="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-green-500/40 rotate-12 group-hover:rotate-0 transition-transform">
            <i class="fas fa-check text-3xl"></i>
        </div>
        <div class="text-left">
            <h4 class="font-black text-2xl uppercase tracking-tighter mb-1">Đặt Lịch Thành Công!</h4>
            <p class="text-sm text-gray-400 font-medium italic">Hệ thống đã ghi nhận lịch của bạn. Hẹn gặp bạn tại sân!</p>
        </div>
    </div>

    <script>
    let currentPitchObj = null;
    let currentSlotStr = null;
    let bookedSlots = []; // Danh sách khung giờ đã được đặt
    const sessionUser = <?php echo isset($_SESSION['user']) ? json_encode($_SESSION['user']) : 'null'; ?>;

    // Hàm lấy danh sách khung giờ đã đặt từ database
    async function fetchBookedSlots(pitchId, bookingDate) {
        try {
            const response = await fetch(`backend/get_booked_slots.php?pitch_id=${pitchId}&booking_date=${bookingDate}`);
            const data = await response.json();
            
            if (data.success) {
                bookedSlots = data.booked_slots || [];
                updateSlotButtons();
            }
        } catch (error) {
            console.error('Lỗi khi lấy danh sách khung giờ đã đặt:', error);
        }
    }

    // Hàm cập nhật trạng thái các nút khung giờ
    function updateSlotButtons() {
        document.querySelectorAll('.slot-btn').forEach(btn => {
            const slotText = btn.querySelector('span:first-child').innerText;
            const isBooked = bookedSlots.includes(slotText);
            
            if (isBooked) {
                // Disable nút nếu đã được đặt
                btn.disabled = true;
                btn.classList.remove('bg-white', 'border-gray-100', 'text-gray-600', 'hover:border-green-500', 'hover:text-green-600', 'hover:shadow-lg', 'hover:shadow-green-50');
                btn.classList.add('bg-gray-50', 'border-gray-100', 'text-gray-300', 'cursor-not-allowed');
                btn.querySelector('span:last-child').innerText = 'Đã đặt';
            } else {
                // Enable nút nếu còn trống
                btn.disabled = false;
                btn.classList.remove('bg-gray-50', 'text-gray-300', 'cursor-not-allowed');
                btn.classList.add('bg-white', 'border-gray-100', 'text-gray-600', 'hover:border-green-500', 'hover:text-green-600', 'hover:shadow-lg', 'hover:shadow-green-50');
                btn.querySelector('span:last-child').innerText = 'Trống';
            }
        });
    }

    // Hàm mở modal đặt sân
    async function openBooking(pitch) {
        if (!sessionUser) {
            alert("Vui lòng đăng nhập để đặt sân!");
            window.location.href = "login.php";
            return;
        }

        currentPitchObj = pitch;
        currentSlotStr = null;
        
        document.getElementById('modal-image').src = pitch.image;
        document.getElementById('modal-type').innerText = pitch.type;
        document.getElementById('modal-name').innerText = pitch.name;
        document.getElementById('modal-location').innerHTML = `<i class="fas fa-map-marker-alt mr-3 text-green-500"></i> ${pitch.location}`;
        document.getElementById('modal-price').innerText = pitch.price.toLocaleString('vi-VN');
        
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('booking-date').value = today;
        
        // Reset trạng thái các nút
        document.querySelectorAll('.slot-btn').forEach(btn => {
            btn.classList.remove('bg-green-600', 'text-white', 'shadow-xl', 'scale-[1.02]', 'border-green-600', 'ring-8', 'ring-green-50');
        });
        
        // Lấy danh sách khung giờ đã đặt
        await fetchBookedSlots(pitch.id, today);
        
        updateSummary();
        document.getElementById('booking-modal').classList.remove('hidden');
        document.getElementById('booking-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    // Lắng nghe sự kiện thay đổi ngày để cập nhật khung giờ
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('booking-date');
        if (dateInput) {
            dateInput.addEventListener('change', async function() {
                if (currentPitchObj) {
                    currentSlotStr = null;
                    document.querySelectorAll('.slot-btn').forEach(btn => {
                        btn.classList.remove('bg-green-600', 'text-white', 'shadow-xl', 'scale-[1.05]', 'border-green-600', 'ring-8', 'ring-green-50');
                    });
                    await fetchBookedSlots(currentPitchObj.id, this.value);
                }
            });
        }
    });

    function closeBooking() {
        document.getElementById('booking-modal').classList.add('hidden');
        document.getElementById('booking-modal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function selectSlot(slot, btn) {
        if (btn.disabled) return;
        
        currentSlotStr = slot;
        document.querySelectorAll('.slot-btn').forEach(b => {
            b.classList.remove('bg-green-600', 'text-white', 'shadow-xl', 'scale-[1.05]', 'border-green-600', 'ring-8', 'ring-green-50');
        });
        btn.classList.add('bg-green-600', 'text-white', 'shadow-xl', 'scale-[1.05]', 'border-green-600', 'ring-8', 'ring-green-50');
        updateSummary();
    }

    function updateSummary() {
        if (!currentPitchObj) return;
        const total = currentPitchObj.price * 1.5;
        document.getElementById('subtotal-display').innerText = total.toLocaleString('vi-VN') + 'đ';
        document.getElementById('total-display').innerText = total.toLocaleString('vi-VN') + 'đ';
    }

    function goPayment() {
        if (!currentSlotStr) {
            alert("Vui lòng chọn khung giờ thi đấu!");
            return;
        }
        
        const total = currentPitchObj.price * 1.5;
        const note = `DATSAN ${currentPitchObj.id} ${sessionUser.name.split(' ').pop().toUpperCase()} ${new Date().getTime().toString().slice(-6)}`;
        
        document.getElementById('payment-total').innerText = total.toLocaleString('vi-VN') + 'đ';
        document.getElementById('qr-code').src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=STB:SuperSports-${total}-${note}`;
        document.getElementById('payment-note').innerText = note;
        
        document.getElementById('booking-modal').classList.add('hidden');
        document.getElementById('booking-modal').style.display = 'none';
        document.getElementById('payment-modal').classList.remove('hidden');
        document.getElementById('payment-modal').style.display = 'flex';
    }

    function closePayment() {
        document.getElementById('payment-modal').classList.add('hidden');
        document.getElementById('payment-modal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Hàm xác nhận thanh toán và lưu vào database
    async function confirmPayment() {
        const bookingDate = document.getElementById('booking-date').value;
        const totalPrice = currentPitchObj.price * 1.5;

        // Hiển thị loading
        const paymentModal = document.getElementById('payment-modal');
        const originalContent = paymentModal.innerHTML;
        
        try {
            // Gửi request đặt sân lên server
            const formData = new FormData();
            formData.append('pitch_id', currentPitchObj.id);
            formData.append('booking_date', bookingDate);
            formData.append('time_slot', currentSlotStr);
            formData.append('total_price', totalPrice);

            const response = await fetch('backend/book_pitch.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Đóng modal thanh toán
                document.getElementById('payment-modal').classList.add('hidden');
                document.getElementById('payment-modal').style.display = 'none';
                
                // Hiển thị thông báo thành công
                const notification = document.getElementById('success-notification');
                notification.classList.remove('hidden');
                
                // Cập nhật danh sách khung giờ đã đặt
                bookedSlots.push(currentSlotStr);
                updateSlotButtons();
                
                setTimeout(() => {
                    notification.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                    
                    // Đóng modal đặt sân
                    closeBooking();
                }, 4000);
            } else {
                // Hiển thị lỗi
                alert(data.message || 'Có lỗi xảy ra khi đặt sân. Vui lòng thử lại!');
                closePayment();
            }
        } catch (error) {
            console.error('Lỗi khi đặt sân:', error);
            alert('Có lỗi xảy ra khi đặt sân. Vui lòng thử lại!');
            closePayment();
        }
    }
    </script>
</div>

<?php require_once 'includes/footer.php'; ?>
