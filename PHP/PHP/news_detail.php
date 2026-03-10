<?php
$page_title = 'Chi Tiết Tin Tức - SuperSports';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

require_once 'backend/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$article = null;

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $article = [
            'id' => $row['id'],
            'title' => $row['title'],
            'category' => $row['category'],
            'date' => date('d/m/Y', strtotime($row['created_at'])),
            'image' => $row['image'],
            'desc' => $row['description'],
            'content' => $row['content'],
            'author' => $row['author'] ?? 'Admin'
        ];
    }
}

if (!$article) {
    header('Location: news.php');
    exit;
}
?>

<div class="max-w-4xl mx-auto px-4 py-16">
    <a href="news.php"
        class="inline-flex items-center gap-2 text-gray-500 font-bold mb-8 hover:text-green-600 transition-colors">
        <i class="fas fa-arrow-left"></i> QUAY LẠI DANH SÁCH
    </a>

    <header class="mb-12">
        <div class="flex items-center gap-3 mb-6">
            <span
                class="bg-green-100 text-green-700 text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest">
                <?php echo $article['category']; ?>
            </span>
            <span class="text-gray-400 text-xs font-medium">/
                <?php echo $article['date']; ?>
            </span>
        </div>
        <h1 class="text-4xl md:text-5xl font-black text-gray-900 leading-tight mb-8">
            <?php echo $article['title']; ?>
        </h1>
        <div class="flex items-center gap-4 p-6 bg-gray-50 rounded-3xl">
            <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center text-white font-bold">
                <?php echo substr($article['author'], 0, 1); ?>
            </div>
            <div>
                <p class="text-sm font-black text-gray-900">
                    <?php echo $article['author']; ?>
                </p>
                <p class="text-xs text-gray-500 uppercase font-bold tracking-tighter">Biên tập viên thể thao</p>
            </div>
        </div>
    </header>

    <div class="rounded-[40px] overflow-hidden mb-12 shadow-2xl">
        <img src="<?php echo $article['image']; ?>" alt="<?php echo $article['title']; ?>" class="w-full h-auto" />
    </div>

    <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed space-y-6">
        <p
            class="text-xl font-bold text-gray-900 leading-relaxed italic border-l-4 border-green-500 pl-6 mb-8 bg-green-50 py-6 pr-6 rounded-r-3xl">
            <?php echo $article['desc']; ?>
        </p>
        <div class="text-lg content-rich-text space-y-6">
            <?php
            // Split content by newlines and display with proper formatting
            $paragraphs = explode("\n\n", $article['content']);
            foreach ($paragraphs as $paragraph) {
                if (!empty(trim($paragraph))) {
                    echo '<p class="text-gray-800 leading-relaxed font-medium">' . nl2br(htmlspecialchars($paragraph)) . '</p>';
                }
            }
            ?>
        </div>
    </div>

    <footer class="mt-16 pt-10 border-t border-gray-100">
        <div class="flex flex-wrap gap-4">
            <span class="px-4 py-2 bg-gray-100 rounded-xl text-xs font-bold text-gray-500">#BongDaPhui</span>
            <span class="px-4 py-2 bg-gray-100 rounded-xl text-xs font-bold text-gray-500">#TheThaoVietNam</span>
            <span class="px-4 py-2 bg-gray-100 rounded-xl text-xs font-bold text-gray-500">#TinTuc</span>
        </div>

        <div class="mt-12 bg-green-50 rounded-3xl p-8 flex items-center justify-between gap-6">
            <div>
                <h4 class="font-black text-gray-900 mb-2">Bạn thấy bài viết hữu ích?</h4>
                <p class="text-gray-600 text-sm">Chia sẻ ngay với đồng đội của mình nhé!</p>
            </div>
            <div class="flex gap-3">
                <button
                    class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-blue-600 shadow-sm hover:scale-110 transition-transform"><i
                        class="fab fa-facebook-f"></i></button>
                <button
                    class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-green-600 shadow-sm hover:scale-110 transition-transform"><i
                        class="fas fa-link"></i></button>
            </div>
        </div>
    </footer>
</div>

<?php require_once 'includes/footer.php'; ?>