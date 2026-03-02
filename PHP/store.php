<?php
$page_title = 'Cửa Hàng Thể Thao - SuperSports';
$current_page = 'store';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'backend/db.php';

// Fetch products from database
$query = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($query);
$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => (float) $row['price'],
            'category' => $row['category'],
            'image' => $row['image'],
            'description' => $row['description'],
            'isHot' => (bool) $row['is_hot'],
            'stock' => (int) $row['stock']
        ];
    }
}

$SHOE_SIZES = ['39', '40', '41', '42', '43'];
$CLOTHING_SIZES = ['S', 'M', 'L', 'XL', 'XXL'];

$active_category = isset($_GET['category']) ? $_GET['category'] : 'all';
?>

<!-- Hero Store Section -->
<div class="relative h-[450px] overflow-hidden">
    <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&q=80&w=2000"
        class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-r from-black via-black/50 to-transparent"></div>
    <div class="absolute inset-0 flex items-center">
        <div class="max-w-7xl mx-auto px-4 w-full">
            <div class="max-w-2xl animate-fadeIn">
                <h1 class="text-4xl font-black text-white uppercase italic tracking-tighter mb-6 leading-tight">
                    Trang bị <span class="text-green-500">Chuyên Nghiệp</span><br>Cho Nhà Vô Địch
                </h1>
                <p class="text-gray-300 text-lg font-medium italic mb-10 leading-relaxed">
                    Khám phá bộ sưu tập dụng cụ thể thao cao cấp. Từ những đôi giày tốc độ đến bộ áo đấu thoáng khí -
                    tất cả đều sẵn sàng giúp bạn bứt phá.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 -translate-y-20 relative z-10">
    <!-- Lọc sản phẩm -->
    <div
        class="bg-white/80 backdrop-blur-xl p-8 rounded-[40px] shadow-2xl border border-white/20 flex flex-col md:flex-row justify-between items-center gap-8 mb-16">
        <div class="flex bg-gray-100/50 p-1.5 rounded-[24px] overflow-x-auto max-w-full">
            <?php foreach (['all', 'GIÀY', 'ÁO ĐẤU', 'PHỤ KIỆN'] as $cat): ?>
                <button onclick="setCategory('<?php echo $cat; ?>')" id="cat-<?php echo $cat; ?>"
                    class="cat-btn px-10 py-3 rounded-2xl text-[11px] font-black transition-all flex items-center gap-3 whitespace-nowrap <?php echo $active_category === $cat ? 'bg-green-600 text-white shadow-lg' : 'text-gray-400 hover:text-green-600'; ?>">
                    <i class="fas <?php
                    echo $cat === 'all' ? 'fa-th-large' : ($cat === 'GIÀY' ? 'fa-shoe-prints' : ($cat === 'ÁO ĐẤU' ? 'fa-tshirt' : 'fa-basketball-ball'));
                    ?>"></i>
                    <?php echo $cat === 'all' ? 'TẤT CẢ' : $cat; ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="relative w-full md:w-80 group">
            <i
                class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-green-600 transition-colors"></i>
            <input type="text" id="product-search" placeholder="Tìm kiếm sản phẩm đỉnh cao..."
                class="w-full pl-14 pr-6 py-4 bg-gray-50 border border-gray-100 rounded-[24px] text-xs font-bold outline-none focus:ring-4 focus:ring-green-500/10 shadow-inner transition-all"
                oninput="filterProducts()">
        </div>
    </div>

    <!-- Product Grid -->
    <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <?php foreach ($products as $product): ?>
            <div class="product-card bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 group flex flex-col p-4"
                data-category="<?php echo $product['category']; ?>" data-name="<?php echo strtolower($product['name']); ?>">
                <div class="relative aspect-square w-full overflow-hidden rounded-xl bg-gray-50">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />

                    <div
                        class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                        <button onclick="openProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)"
                            class="bg-white text-gray-900 w-12 h-12 rounded-full flex items-center justify-center shadow-xl hover:bg-green-600 hover:text-white transition-all transform translate-y-4 group-hover:translate-y-0 duration-300">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>

                    <?php if (isset($product['isHot']) && $product['isHot']): ?>
                        <div
                            class="absolute top-6 left-6 bg-orange-500 text-white text-[8px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest shadow-lg">
                            BEST SELLER</div>
                    <?php endif; ?>
                </div>

                <div class="p-6 flex-grow flex flex-col mt-2">
                    <div class="flex justify-between items-center mb-4">
                        <span
                            class="text-[9px] font-black text-green-600 uppercase tracking-widest bg-green-50 px-3 py-1 rounded-lg">
                            <?php echo $product['category']; ?>
                        </span>
                        <div class="flex text-yellow-400 text-[8px] gap-0.5">
                            <?php for ($i = 0; $i < 5; $i++): ?><i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <h3
                        class="text-xl font-black text-gray-900 mb-2 uppercase italic group-hover:text-green-600 transition-colors line-clamp-1">
                        <?php echo $product['name']; ?>
                    </h3>
                    <p class="text-gray-400 text-[11px] mb-8 font-medium italic line-clamp-2">
                        "<?php echo $product['description']; ?>"
                    </p>

                    <div class="mt-auto pt-6 border-t border-gray-50 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex flex-col">
                            <p class="text-[9px] font-black text-gray-300 uppercase mb-1">Giá ưu đãi</p>
                            <span class="text-xl md:text-2xl font-black text-green-700 tracking-tighter">
                                <?php echo number_format($product['price'], 0, ',', '.'); ?>đ
                            </span>
                        </div>
                        <button onclick="openProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)"
                            class="bg-gray-900 text-white px-5 py-2.5 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-green-600 transition-all shadow-lg active:scale-95 flex-shrink-0 whitespace-nowrap">
                            CHI TIẾT
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden text-center py-40 animate-fadeIn">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
            <i class="fas fa-search-minus text-gray-300 text-4xl"></i>
        </div>
        <h3 class="text-3xl font-black text-gray-900 uppercase italic mb-3 tracking-tighter">Không tìm thấy sản phẩm
        </h3>
        <p class="text-gray-400 text-base font-medium italic">Chúng tôi không tìm thấy kết quả phù hợp với từ khóa của
            bạn.</p>
    </div>
</div>

<!-- Product Modal -->
<div id="product-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/90 backdrop-blur-md" onclick="closeProduct()"></div>
    <div
        class="bg-white w-full max-w-4xl rounded-[56px] overflow-hidden shadow-2xl relative z-10 flex flex-col md:flex-row transition-all max-h-[90vh] overflow-y-auto italic">
        <button onclick="closeProduct()"
            class="absolute top-10 right-10 text-gray-400 hover:text-gray-900 transition-colors w-12 h-12 flex items-center justify-center rounded-full hover:bg-gray-100 z-50">
            <i class="fas fa-times text-2xl"></i>
        </button>

        <div class="md:w-1/2 relative bg-gray-50">
            <img id="modal-image" src="" class="w-full h-full object-cover min-h-[400px]" alt="" />
        </div>

        <div class="md:w-1/2 p-10 md:p-14 flex flex-col">
            <!-- Selection View -->
            <div id="selection-view" class="h-full flex flex-col justify-center animate-fadeIn">
                <span id="modal-category"
                    class="text-green-600 font-bold text-[10px] uppercase tracking-widest mb-4"></span>
                <h2 id="modal-name" class="text-2xl font-black text-gray-900 uppercase italic mb-4 leading-tight"></h2>
                <p id="modal-description" class="text-gray-500 font-medium italic mb-8 leading-relaxed"></p>

                <div class="flex items-end gap-3 mb-12">
                    <p id="modal-price" class="text-4xl font-black text-gray-900"></p>
                    <p class="text-gray-400 font-bold text-sm mb-1">/Sản phẩm</p>
                </div>

                <div id="size-selection-container" class="mb-10">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-4 italic">Kích
                        thước của bạn</label>
                    <div id="size-list" class="flex flex-wrap gap-3"></div>
                </div>

                <div class="mb-12">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-4 italic">Số
                        lượng cần mua</label>
                    <div class="flex items-center gap-8 bg-gray-100/50 w-fit p-3 rounded-3xl border border-gray-100">
                        <button onclick="updateQty(-1)"
                            class="w-12 h-12 rounded-2xl bg-white text-gray-900 flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all shadow-sm active:scale-90">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span id="quantity-display" class="text-2xl font-black w-8 text-center">1</span>
                        <button onclick="updateQty(1)"
                            class="w-12 h-12 rounded-2xl bg-white text-gray-900 flex items-center justify-center hover:bg-green-50 hover:text-green-600 transition-all shadow-sm active:scale-90">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="mt-auto">
                    <button onclick="goPayment()"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] flex items-center justify-center gap-4 text-lg uppercase italic">
                        TIẾP TỤC ĐẶT HÀNG <i class="fas fa-arrow-right"></i>
                    </button>
                    <p id="total-price-display"
                        class="text-center text-gray-400 font-black text-[10px] uppercase mt-6 tracking-widest"></p>
                </div>
            </div>

            <!-- Payment View -->
            <div id="payment-view" class="hidden text-center animate-fadeIn h-full flex flex-col justify-center">
                <h2 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic leading-tight">THANH
                    TOÁN<br>AN TOÀN</h2>
                <p class="text-gray-500 mb-10 font-medium italic">Vui lòng quét mã để xác nhận đơn hàng</p>

                <div class="bg-gray-50 rounded-[48px] p-10 mb-10 border-2 border-dashed border-gray-200 relative group">
                    <div
                        class="bg-white p-6 rounded-[32px] shadow-xl mb-8 inline-block transform group-hover:scale-105 transition-transform duration-500">
                        <img id="payment-qr" src="" alt="Payment QR" class="w-56 h-56">
                    </div>
                    <div class="space-y-2">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tổng tiền cần chuyển
                        </p>
                        <p id="final-total" class="text-4xl font-black text-green-700 tracking-tighter">0đ</p>
                    </div>
                </div>

                <div class="bg-green-50/50 rounded-[32px] p-8 text-left mb-10 border border-green-100 shadow-inner">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase mb-2">Đơn hàng</p>
                            <p id="order-summary" class="font-black text-gray-900 uppercase leading-tight"></p>
                            <p id="size-summary" class="text-[10px] text-green-600 font-black uppercase mt-1"></p>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase mb-2">Nội dung bắt buộc</p>
                        <p id="transfer-note"
                            class="font-black text-white bg-gray-900 px-4 py-2 rounded-xl inline-block uppercase tracking-wider text-xs">
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 mt-auto">
                    <button onclick="backToSelection()"
                        class="w-14 h-14 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center hover:bg-gray-200 transition-all active:scale-95">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <button onclick="confirmOrder()"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] flex items-center justify-center gap-3 uppercase italic text-sm">
                        XÁC NHẬN CHUYỂN KHOẢN
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Notification -->
<div id="success-notification"
    class="fixed top-12 left-1/2 -translate-x-1/2 z-[150] hidden bg-gray-900 text-white px-10 py-6 rounded-[32px] shadow-2xl flex items-center gap-5 animate-slideDown border border-green-500/30 ring-8 ring-white/10">
    <div
        class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white shadow-lg shadow-green-500/40">
        <i class="fas fa-check text-xl"></i>
    </div>
    <div>
        <h4 class="font-black text-base uppercase tracking-wider">Đã Ghi Nhận Đơn Hàng!</h4>
        <p class="text-xs text-gray-400 font-medium">Đội ngũ SuperSports sẽ soạn hàng ngay.</p>
    </div>
</div>

<script>
    let currentProduct = null;
    let qty = 1;
    let selectedSize = '';
    let activeCategory = 'all';
    const SHOE_SIZES = <?php echo json_encode($SHOE_SIZES); ?>;
    const CLOTHING_SIZES = <?php echo json_encode($CLOTHING_SIZES); ?>;

    function setCategory(cat) {
        activeCategory = cat;
        document.querySelectorAll('.cat-btn').forEach(btn => {
            btn.classList.remove('bg-green-600', 'text-white', 'shadow-lg');
            btn.classList.add('text-gray-400', 'hover:text-green-600');
        });
        const activeBtn = document.getElementById('cat-' + cat);
        activeBtn.classList.remove('text-gray-400', 'hover:text-green-600');
        activeBtn.classList.add('bg-green-600', 'text-white', 'shadow-lg');
        filterProducts();
    }

    function filterProducts() {
        const query = document.getElementById('product-search').value.toLowerCase();
        const cards = document.querySelectorAll('.product-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const name = card.dataset.name;
            const category = card.dataset.category;
            const matchesCat = activeCategory === 'all' || category === activeCategory;
            const matchesSearch = name.includes(query);

            if (matchesCat && matchesSearch) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        const emptyState = document.getElementById('empty-state');
        visibleCount === 0 ? emptyState.classList.remove('hidden') : emptyState.classList.add('hidden');
    }

    function openProduct(product) {
        currentProduct = product;
        qty = 1;

        document.getElementById('modal-image').src = product.image;
        document.getElementById('modal-name').innerText = product.name;
        document.getElementById('modal-category').innerText = product.category;
        document.getElementById('modal-description').innerText = product.description;
        document.getElementById('modal-price').innerText = product.price.toLocaleString('vi-VN') + 'đ';

        const sizeCont = document.getElementById('size-selection-container');
        const sizeList = document.getElementById('size-list');
        sizeList.innerHTML = '';

        if (product.category === 'PHỤ KIỆN') {
            sizeCont.classList.add('hidden');
            selectedSize = 'N/A';
        } else {
            sizeCont.classList.remove('hidden');
            const sizes = product.category === 'GIÀY' ? SHOE_SIZES : CLOTHING_SIZES;
            selectedSize = sizes[1];

            sizes.forEach(size => {
                const btn = document.createElement('button');
                btn.className = `w-14 h-14 rounded-2xl font-black text-sm transition-all flex items-center justify-center border-2 ${selectedSize === size ? 'bg-green-600 border-green-600 text-white shadow-lg' : 'bg-gray-50 border-gray-100 text-gray-400'}`;
                btn.innerText = size;
                btn.onclick = () => {
                    selectedSize = size;
                    document.querySelectorAll('#size-list button').forEach(b => {
                        b.classList.remove('bg-green-600', 'border-green-600', 'text-white', 'shadow-lg');
                        b.classList.add('bg-gray-50', 'border-gray-100', 'text-gray-400');
                    });
                    btn.classList.add('bg-green-600', 'border-green-600', 'text-white', 'shadow-lg');
                    btn.classList.remove('bg-gray-50', 'border-gray-100', 'text-gray-400');
                };
                sizeList.appendChild(btn);
            });
        }

        updateDisplay();
        document.getElementById('selection-view').classList.remove('hidden');
        document.getElementById('payment-view').classList.add('hidden');
        document.getElementById('product-modal').classList.remove('hidden');
        document.getElementById('product-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function updateQty(val) {
        qty = Math.max(1, qty + val);
        updateDisplay();
    }

    function updateDisplay() {
        document.getElementById('quantity-display').innerText = qty;
        const total = currentProduct.price * qty;
        document.getElementById('total-price-display').innerText = 'Dự kiến: ' + total.toLocaleString('vi-VN') + 'đ';
    }

    function goPayment() {
        const total = currentProduct.price * qty;
        document.getElementById('final-total').innerText = total.toLocaleString('vi-VN') + 'đ';
        document.getElementById('order-summary').innerText = `${currentProduct.name} x${qty}`;
        document.getElementById('size-summary').innerText = selectedSize === 'N/A' ? 'FULL SIZE' : `KÍCH THƯỚC: ${selectedSize}`;
        document.getElementById('transfer-note').innerText = `BUY ${currentProduct.id} QTY${qty}`;
        document.getElementById('payment-qr').src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=STB:SuperSports-ORDER-${currentProduct.id}-QTY${qty}-SIZE${selectedSize}-${total}`;

        document.getElementById('selection-view').classList.add('hidden');
        document.getElementById('payment-view').classList.remove('hidden');
    }

    function backToSelection() {
        document.getElementById('selection-view').classList.remove('hidden');
        document.getElementById('payment-view').classList.add('hidden');
    }

    function closeProduct() {
        document.getElementById('product-modal').classList.add('hidden');
        document.getElementById('product-modal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    async function confirmOrder() {
        const total = currentProduct.price * qty;
        const formData = new FormData();
        formData.append('product_id', currentProduct.id);
        formData.append('quantity', qty);
        formData.append('size', selectedSize);
        formData.append('total_amount', total);

        try {
            const response = await fetch('backend/place_order.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.success) {
                closeProduct();
                const note = document.getElementById('success-notification');
                note.classList.remove('hidden');
                setTimeout(() => {
                    note.classList.add('hidden');
                    window.location.reload(); // Reload to update stock
                }, 3000);
            } else {
                alert(data.message || 'Có lỗi xảy ra khi đặt hàng.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Không thể kết nối đến máy chủ.');
        }
    }
</script>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideDown {
        from {
            transform: translate(-50%, -100%);
            opacity: 0;
        }

        to {
            transform: translate(-50%, 0);
            opacity: 1;
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.4s ease-out forwards;
    }

    .animate-slideDown {
        animation: slideDown 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }

    #product-modal::-webkit-scrollbar {
        width: 0;
    }
</style>

<?php require_once 'includes/footer.php'; ?>