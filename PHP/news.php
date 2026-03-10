<?php
$page_title = 'Tin Tức Thể Thao - SuperSports';
$current_page = 'news';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

require_once 'backend/db.php';

// Fetch news from database
$news_query = "SELECT * FROM news ORDER BY created_at DESC";
$news_result = $conn->query($news_query);
$news_list = [];
if ($news_result && $news_result->num_rows > 0) {
    while ($row = $news_result->fetch_assoc()) {
        $news_list[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'category' => $row['category'],
            'date' => date('d/m/Y', strtotime($row['created_at'])),
            'image' => $row['image'],
            'desc' => $row['description'],
            'author' => $row['author'] ?? 'Admin'
        ];
    }
}
?>

<div class="max-w-7xl mx-auto px-4 py-12">
    <div class="text-center mb-16">
        <h1 class="text-4xl font-black text-gray-900 mb-4 uppercase tracking-tighter">Tin Tức Thể Thao</h1>
        <div class="h-1.5 w-24 bg-green-600 mx-auto rounded-full mb-6"></div>
        <p class="text-gray-500 max-w-2xl mx-auto">Cập nhật những tin tức mới nhất về bóng đá, cầu lông và các sự kiện
            thể thao phong trào nổi bật.</p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
        <?php foreach ($news_list as $item): ?>
            <div
                class="bg-white rounded-[32px] overflow-hidden border border-gray-100 group hover:shadow-2xl transition-all duration-500 flex flex-col">
                <div class="relative h-64 overflow-hidden">
                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                    <div class="absolute top-4 left-4">
                        <span
                            class="bg-green-600 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest shadow-lg">
                            <?php echo $item['category']; ?>
                        </span>
                    </div>
                </div>
                <div class="p-8 flex-grow">
                    <div class="flex items-center text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-4">
                        <i class="far fa-calendar-alt mr-2 text-green-500"></i>
                        <?php echo $item['date']; ?>
                        <span class="mx-2 text-gray-200">|</span>
                        <i class="far fa-user mr-2 text-green-500"></i>
                        <?php echo $item['author']; ?>
                    </div>
                    <h4
                        class="text-2xl font-black text-gray-900 mb-4 leading-tight group-hover:text-green-600 transition-colors">
                        <?php echo $item['title']; ?>
                    </h4>
                    <p class="text-gray-500 text-sm mb-6 line-clamp-3 leading-relaxed">
                        <?php echo $item['desc']; ?>
                    </p>
                    <a href="news_detail.php?id=<?php echo $item['id']; ?>"
                        class="inline-flex items-center gap-2 text-sm font-black text-gray-900 border-b-2 border-green-500 pb-1 hover:text-green-600 hover:border-green-600 transition-all">
                        ĐỌC CHI TIẾT <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>