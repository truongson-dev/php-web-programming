<?php
$page_title = 'Danh Sách Sân - SuperSports';
$current_page = 'pitches';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

require_once 'backend/db.php';

// Fetch Pitches from Database
$query = "SELECT * FROM pitches ORDER BY id DESC";
$res = $conn->query($query);
$pitches = [];
if ($res && $res->num_rows > 0) {
    while($row = $res->fetch_assoc()) {
        $pitches[] = $row;
    }
}

$TIMES = [
    "06:00 - 07:30", "07:30 - 09:00", "09:00 - 10:30", 
    "15:00 - 16:30", "16:30 - 18:00", "18:00 - 19:30", 
    "19:30 - 21:00", "21:00 - 22:30"
];

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>

<div class="max-w-7xl mx-auto px-4 py-12 relative" id="pitches-app">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-16 gap-8">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="bg-white p-1.5 rounded-[24px] shadow-sm border border-gray-100 flex items-center h-fit">
                <button onclick="filterBySport('all')" id="filter-all" class="filter-btn px-6 py-2.5 rounded-2xl text-[10px] font-black transition-all bg-[#1a1c1e] text-white shadow-lg">TẤT CẢ</button>
                <button onclick="filterBySport('football')" id="filter-football" class="filter-btn px-6 py-2.5 rounded-2xl text-[10px] font-black transition-all text-gray-400 hover:text-green-600">BÓNG ĐÁ</button>
                <button onclick="filterBySport('badminton')" id="filter-badminton" class="filter-btn px-6 py-2.5 rounded-2xl text-[10px] font-black transition-all text-gray-400 hover:text-blue-600">CẦU LÔNG</button>
            </div>
        </div>

        <div class="relative w-full lg:w-96 group">
            <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-green-600 transition-colors"></i>
            <input type="text" id="search-query" placeholder="Tìm kiếm tên sân..." class="w-full pl-14 pr-8 py-4 bg-white border border-gray-100 rounded-[28px] text-sm outline-none focus:ring-4 focus:ring-green-500/10 shadow-sm font-bold transition-all text-gray-900" oninput="updateFilters()">
        </div>
    </div>

    <!-- Pitches Grid -->
    <div id="pitches-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($pitches as $pitch): ?>
            <div class="pitch-card bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500 group flex flex-col p-3" 
                 data-sport="<?php echo $pitch['sport']; ?>" 
                 data-name="<?php echo strtolower($pitch['name']); ?>">
                <div class="h-[220px] relative overflow-hidden rounded-xl bg-gray-50">
                    <img src="<?php echo $pitch['image']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="<?php echo $pitch['name']; ?>">
                    <div class="absolute top-5 left-5">
                        <span class="px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-[0.2em] text-white shadow-lg <?php echo $pitch['sport'] === 'football' ? 'bg-green-600' : 'bg-blue-600'; ?>">
                            <?php echo $pitch['sport'] === 'football' ? 'Bóng đá' : 'Cầu lông'; ?>
                        </span>
                    </div>
                </div>
                
                <div class="p-5 flex-grow flex flex-col">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-black text-gray-900 leading-tight uppercase italic group-hover:text-green-600 transition-colors line-clamp-1"><?php echo $pitch['name']; ?></h3>
                    </div>
                    
                    <p class="text-gray-400 text-[11px] mb-6 font-bold flex items-center italic line-clamp-1">
                        <i class="fas fa-map-marker-alt mr-2 text-green-500"></i> <?php echo $pitch['location']; ?>
                    </p>
                    
                    <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between">
                        <div>
                            <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Giá thuê</p>
                            <p class="text-xl font-black text-green-700 tracking-tighter">
                                <?php echo number_format($pitch['price'], 0, ',', '.'); ?>đ<span class="text-[10px] font-bold text-gray-400 ml-1">/giờ</span>
                            </p>
                        </div>
                        <button 
                            onclick="openBooking(<?php echo htmlspecialchars(json_encode($pitch)); ?>)"
                            <?php echo $pitch['status'] === 'busy' ? 'disabled' : ''; ?>
                            class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-all hover:scale-[1.02] shadow-lg text-[9px] uppercase disabled:bg-gray-100 disabled:text-gray-300 disabled:cursor-not-allowed flex-shrink-0 whitespace-nowrap">
                            <?php echo $pitch['status'] === 'busy' ? 'HẾT' : 'ĐẶT SÂN'; ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modals Section -->
    <!-- Booking Modal -->
    <div id="booking-modal" class="fixed inset-0 z-[60] hidden flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/80 backdrop-blur-md" onclick="closeBooking()"></div>
        <div class="bg-white w-full max-w-5xl rounded-[56px] overflow-hidden shadow-2xl relative flex flex-col md:flex-row transform transition-all max-h-[90vh] overflow-y-auto italic">
            <div class="md:w-2/5 relative">
                <img id="modal-image" src="" class="w-full h-full object-cover min-h-[300px]" alt="">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent flex flex-col justify-end p-10 text-white">
                    <span id="modal-type" class="bg-green-600 text-white text-[10px] font-black px-5 py-2 rounded-full uppercase tracking-widest mb-4 w-fit shadow-xl"></span>
                    <h2 id="modal-name" class="text-2xl font-black mb-3 uppercase italic tracking-tighter leading-tight"></h2>
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
                    <p class="mt-3 text-[10px] text-gray-400 font-bold italic flex items-center gap-2">
                        <i class="fas fa-info-circle text-green-500"></i> 
                        Chọn ngày khác để xem các khung giờ trống
                    </p>
                </div>

                <div class="mb-12">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] mb-5 italic flex items-center gap-3">
                        <span class="w-6 h-6 bg-green-100 text-green-600 rounded-lg flex items-center justify-center text-[10px] not-italic">2</span> 
                        Chọn khung giờ (90 phút)
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <?php foreach ($TIMES as $i => $slot): ?>
                            <button
                                onclick="selectSlot('<?php echo $slot; ?>', this)"
                                data-slot="<?php echo $slot; ?>"
                                class="slot-btn p-6 rounded-[28px] text-sm font-black transition-all border-2 flex flex-col items-center justify-center gap-2 italic bg-white border-gray-100 text-gray-600 hover:border-green-500 hover:text-green-600 hover:shadow-lg hover:shadow-green-50">
                                <span class="text-lg"><?php echo $slot; ?></span>
                                <span class="slot-status text-[9px] font-black uppercase tracking-widest opacity-60 flex items-center gap-1">
                                    <i class="fas fa-circle text-[6px] text-green-500"></i> TRỐNG
                                </span>
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
                     <div class="flex justify-between items-end relative z-10">
                        <div>
                          <p class="text-[10px] text-gray-400 font-bold uppercase mb-2 tracking-widest opacity-60">Tổng thanh toán</p>
                          <h4 id="total-display" class="text-3xl font-black text-green-500 tracking-tighter drop-shadow-lg">0đ</h4>
                        </div>
                        <button onclick="goPayment()" 
                            class="bg-green-600 hover:bg-green-700 text-white px-10 py-4 rounded-2xl font-bold transition-all hover:scale-[1.02] shadow-lg text-[11px] uppercase border border-white/10 italic">
                            XÁC NHẬN ĐẶT LỊCH
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

            <h2 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic">QUÉT MÃ THANH TOÁN</h2>
            <p class="text-gray-500 mb-12 font-medium italic">Sử dụng ứng dụng Ngân hàng hoặc Ví điện tử để quét</p>

            <div class="bg-gray-50 rounded-[56px] p-12 mb-12 inline-block border-2 border-dashed border-gray-200 shadow-inner">
               <div class="bg-white p-6 rounded-[48px] shadow-2xl mb-10 inline-block border border-gray-100 ring-8 ring-white">
                  <img id="qr-code" src="" alt="Payment QR" class="w-64 h-64">
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
                    <p id="payment-note" class="font-black text-green-900 bg-green-200/50 px-6 py-3 rounded-2xl inline-block uppercase tracking-wider text-base shadow-sm border border-green-300/30 font-mono"></p>
                    <button class="w-12 h-12 bg-white text-green-600 rounded-xl flex items-center justify-center hover:bg-green-600 hover:text-white transition-all shadow-sm">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>

            <button onclick="confirmPayment()" 
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-5 rounded-2xl shadow-lg transition-all hover:scale-[1.02] flex items-center justify-center gap-5 text-xl uppercase italic">
                XÁC NHẬN ĐƠN HÀNG <i class="fas fa-check-circle text-2xl text-white"></i>
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
</div>

<script>
let currentPitchObj = null;
let currentSlotStr = null;
let bookedSlots = [];
const sessionUser = <?php echo isset($_SESSION['user']) ? json_encode($_SESSION['user']) : 'null'; ?>;

function filterBySport(sport) {
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('bg-[#1a1c1e]', 'text-white', 'shadow-lg'));
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.add('text-gray-400'));
    document.getElementById('filter-' + sport).classList.remove('text-gray-400');
    document.getElementById('filter-' + sport).classList.add('bg-[#1a1c1e]', 'text-white', 'shadow-lg');
    
    document.querySelectorAll('.pitch-card').forEach(card => {
        if (sport === 'all' || card.dataset.sport === sport) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

function updateFilters() {
    const query = document.getElementById('search-query').value.toLowerCase();
    document.querySelectorAll('.pitch-card').forEach(card => {
        const name = card.dataset.name;
        if (name.includes(query)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

async function loadBookedSlots() {
    if (!currentPitchObj) return;
    
    const date = document.getElementById('booking-date').value;
    if (!date) return;
    
    try {
        const response = await fetch(`backend/get_booked_slots.php?pitch_id=${currentPitchObj.id}&booking_date=${date}`);
        const data = await response.json();
        
        if (data.success) {
            bookedSlots = data.booked_slots || [];
            updateSlotButtons();
        }
    } catch (error) {
        console.error('Error loading booked slots:', error);
    }
}

function updateSlotButtons() {
    document.querySelectorAll('.slot-btn').forEach(btn => {
        const slot = btn.getAttribute('data-slot');
        
        // Remove all styling first
        btn.classList.remove('bg-green-600', 'text-white', 'shadow-xl', 'scale-[1.05]', 'border-green-600', 'ring-8', 'ring-green-50');
        btn.classList.remove('bg-red-50', 'text-red-400', 'border-red-200', 'cursor-not-allowed', 'opacity-60');
        btn.classList.add('bg-white', 'border-gray-100', 'text-gray-600', 'hover:border-green-500', 'hover:text-green-600');
        btn.disabled = false;
        
        const statusSpan = btn.querySelector('.slot-status');
        
        if (bookedSlots.includes(slot)) {
            // Slot is booked - disable it
            btn.classList.remove('bg-white', 'border-gray-100', 'text-gray-600', 'hover:border-green-500', 'hover:text-green-600');
            btn.classList.add('bg-red-50', 'text-red-400', 'border-red-200', 'cursor-not-allowed', 'opacity-60');
            btn.disabled = true;
            statusSpan.innerHTML = '<i class="fas fa-lock text-[8px] mr-1"></i> ĐÃ ĐẶT';
            statusSpan.classList.remove('opacity-60');
            statusSpan.classList.add('opacity-100', 'text-red-500');
        } else if (currentSlotStr === slot) {
            // Currently selected slot
            btn.classList.remove('bg-white', 'border-gray-100', 'text-gray-600', 'hover:border-green-500', 'hover:text-green-600');
            btn.classList.add('bg-green-600', 'text-white', 'shadow-xl', 'scale-[1.05]', 'border-green-600', 'ring-8', 'ring-green-50');
            statusSpan.innerHTML = '<i class="fas fa-check-circle text-[8px] mr-1"></i> ĐÃ CHỌN';
            statusSpan.classList.remove('opacity-60');
            statusSpan.classList.add('opacity-100');
        } else {
            // Available slot
            statusSpan.innerHTML = '<i class="fas fa-circle text-[6px] text-green-500 mr-1"></i> TRỐNG';
            statusSpan.classList.remove('opacity-100', 'text-red-500');
            statusSpan.classList.add('opacity-60');
        }
    });
}

function openBooking(pitch) {
    if (!sessionUser) {
        alert("Vui lòng đăng nhập để đặt sân!");
        window.location.href = "login.php";
        return;
    }

    currentPitchObj = pitch;
    currentSlotStr = null;
    bookedSlots = [];
    
    document.getElementById('modal-image').src = pitch.image;
    document.getElementById('modal-type').innerText = pitch.type;
    document.getElementById('modal-name').innerText = pitch.name;
    document.getElementById('modal-location').innerHTML = `<i class="fas fa-map-marker-alt mr-3 text-green-500"></i> ${pitch.location}`;
    document.getElementById('modal-price').innerText = pitch.price.toLocaleString('vi-VN');
    document.getElementById('booking-date').value = new Date().toISOString().split('T')[0];
    
    // Reset slot buttons
    document.querySelectorAll('.slot-btn').forEach(btn => {
        btn.classList.remove('bg-green-600', 'text-white', 'shadow-xl', 'scale-[1.05]', 'border-green-600', 'ring-8', 'ring-green-50');
        btn.classList.remove('bg-red-50', 'text-red-400', 'border-red-200', 'cursor-not-allowed', 'opacity-60');
        btn.classList.add('bg-white', 'border-gray-100', 'text-gray-600');
        btn.disabled = false;
        
        const statusSpan = btn.querySelector('.slot-status');
        if (statusSpan) {
            statusSpan.innerHTML = '<i class="fas fa-circle text-[6px] text-green-500 mr-1"></i> TRỐNG';
            statusSpan.classList.remove('opacity-100', 'text-red-500');
            statusSpan.classList.add('opacity-60');
        }
    });
    
    updateSummary();
    loadBookedSlots();
    
    document.getElementById('booking-modal').classList.remove('hidden');
    document.getElementById('booking-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeBooking() {
    document.getElementById('booking-modal').classList.add('hidden');
    document.getElementById('booking-modal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function selectSlot(slot, btn) {
    if (btn.disabled) {
        alert('Khung giờ này đã được đặt. Vui lòng chọn khung giờ khác!');
        return;
    }
    
    currentSlotStr = slot;
    updateSlotButtons();
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
    const userNameNote = sessionUser.name.split(' ').pop().toUpperCase();
    const dateNote = document.getElementById('booking-date').value.replace(/-/g, '');
    const note = `DATSAN ${currentPitchObj.id} ${userNameNote} ${dateNote}`;
    
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

async function confirmPayment() {
    const bookingData = {
        pitch_id: currentPitchObj.id,
        booking_date: document.getElementById('booking-date').value,
        time_slot: currentSlotStr,
        total_price: currentPitchObj.price * 1.5
    };
    
    try {
        const response = await fetch('backend/book_pitch.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(bookingData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('payment-modal').classList.add('hidden');
            document.getElementById('payment-modal').style.display = 'none';
            
            const notification = document.getElementById('success-notification');
            notification.querySelector('h4').innerText = 'Đặt Lịch Thành Công!';
            notification.querySelector('p').innerText = 'Đơn đặt sân đang chờ Admin xác nhận. Hẹn gặp bạn tại sân!';
            notification.classList.remove('hidden');
            
            setTimeout(() => {
                notification.classList.add('hidden');
                document.body.style.overflow = 'auto';
                location.reload(); // Reload to update booked slots
            }, 4000);
        } else {
            alert(data.message || 'Có lỗi xảy ra khi đặt sân!');
            closePayment();
        }
    } catch (error) {
        console.error('Error confirming payment:', error);
        alert('Có lỗi xảy ra. Vui lòng thử lại!');
        closePayment();
    }
}

// Add event listener for date change
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('booking-date');
    if (dateInput) {
        dateInput.addEventListener('change', loadBookedSlots);
    }
});
</script>

<style>
@keyframes slideUp {
    from { transform: translateY(50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
@keyframes slideDown {
    from { transform: translate(-50%, -100%); opacity: 0; }
    to { transform: translate(-50%, 0); opacity: 1; }
}
.animate-slideUp { animation: slideUp 0.4s ease-out forwards; }
.animate-slideDown { animation: slideDown 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
</style>

<?php require_once 'includes/footer.php'; ?>
