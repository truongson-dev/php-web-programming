<?php
$page_title = 'Trung Tâm Kết Nối - SuperSports';
$current_page = 'matching';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

require_once 'backend/db.php';

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$view_mode = isset($_GET['view']) ? $_GET['view'] : 'challenges';
$active_sport = isset($_GET['sport']) ? $_GET['sport'] : 'football';


// Xử lý tạo bài đăng mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_challenge') {
    if (!$user) {
        echo "<script>alert('Vui lòng đăng nhập để đăng bài!'); window.location.href='login.php';</script>";
        exit;
    }

    $team_name = isset($_POST['team_name']) ? $_POST['team_name'] : 'Đội của ' . $user['name'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $level = $_POST['level'];
    $location = $_POST['location'];
    $message = $_POST['message'];
    $my_team_id = 0;

    // Kiểm tra xem team đã tồn tại chưa hoặc tạo mới
    $stmt = $conn->prepare("SELECT id FROM teams WHERE name = ? AND leader_id = ?");
    $stmt->bind_param("si", $team_name, $user['id']);
    $stmt->execute();
    $team_res = $stmt->get_result();

    if ($team_res->num_rows > 0) {
        $my_team_id = $team_res->fetch_assoc()['id'];
    } else {
        $logo = 'https://ui-avatars.com/api/?name=' . urlencode($team_name) . '&background=random&size=100';
        $stmt = $conn->prepare("INSERT INTO teams (name, leader_id, level, sport, logo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $team_name, $user['id'], $level, $active_sport, $logo);
        $stmt->execute();
        $my_team_id = $conn->insert_id;
    }

    $stmt = $conn->prepare("INSERT INTO team_challenges (challenging_team_id, sport, match_date, match_time, level, location, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("issssss", $my_team_id, $active_sport, $date, $time, $level, $location, $message);

    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                const note = document.getElementById('success-notification');
                note.querySelector('h4').innerText = 'Đã Gửi Bài!';
                note.querySelector('p').innerText = 'Bài đăng tìm đối thủ của bạn đang được Admin duyệt.';
                note.classList.remove('hidden');
                setTimeout(() => { note.classList.add('hidden'); }, 4000);
            }
        </script>";
    } else {
        echo "<script>alert('Lỗi khi đăng bài: " . $conn->error . "');</script>";
    }
}

// Xử lý đăng tin tuyển đồng đội
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_recruitment') {
    if (!$user) {
        echo "<script>alert('Vui lòng đăng nhập!'); window.location.href='login.php';</script>";
        exit;
    }

    $team_name = $_POST['team_name'];
    $sport = $active_sport;
    $position = $_POST['position'];
    $quantity = (int) $_POST['quantity'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $message = $_POST['message'];

    // Get or create team
    $stmt = $conn->prepare("SELECT id FROM teams WHERE name = ? AND leader_id = ?");
    $stmt->bind_param("si", $team_name, $user['id']);
    $stmt->execute();
    $team_res = $stmt->get_result();

    if ($team_res->num_rows > 0) {
        $team_id = $team_res->fetch_assoc()['id'];
    } else {
        $logo = 'https://ui-avatars.com/api/?name=' . urlencode($team_name) . '&background=random&size=100';
        $stmt = $conn->prepare("INSERT INTO teams (name, leader_id, sport, logo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $team_name, $user['id'], $sport, $logo);
        $stmt->execute();
        $team_id = $conn->insert_id;
    }

    $stmt = $conn->prepare("INSERT INTO team_recruitment (team_id, user_id, sport, position, quantity, match_date, match_time, location, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iississss", $team_id, $user['id'], $sport, $position, $quantity, $date, $time, $location, $message);

    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                const note = document.getElementById('success-notification');
                note.querySelector('h4').innerText = 'Đã Đăng Tin!';
                note.querySelector('p').innerText = 'Tin tuyển đồng đội của bạn đã được hiển thị.';
                note.classList.remove('hidden');
                setTimeout(() => { note.classList.add('hidden'); }, 4000);
            }
        </script>";
    }
}

// Xử lý ứng tuyển
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply_recruitment') {
    if (!$user) {
        echo "<script>alert('Vui lòng đăng nhập!'); window.location.href='login.php';</script>";
        exit;
    }
    $rec_id = $_POST['recruitment_id'];
    $msg = $_POST['apply_message'];

    $stmt = $conn->prepare("INSERT INTO recruitment_applications (recruitment_id, user_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $rec_id, $user['id'], $msg);
    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                const note = document.getElementById('success-notification');
                note.querySelector('h4').innerText = 'Đã Gửi Yêu Cầu!';
                note.querySelector('p').innerText = 'Đội trưởng sẽ sớm phản hồi yêu cầu của bạn.';
                note.classList.remove('hidden');
                setTimeout(() => { note.classList.add('hidden'); }, 4000);
            }
        </script>";
    }
}

// Fetch Teams looking for members (General list)
$teams_query = "SELECT * FROM teams WHERE sport = '$active_sport' LIMIT 10";
$teams_result = $conn->query($teams_query);
$teams = [];
if ($teams_result && $teams_result->num_rows > 0) {
    while ($row = $teams_result->fetch_assoc()) {
        $teams[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'sport' => $row['sport'],
            'membersCount' => rand(5, 12),
            'maxMembers' => 15,
            'logo' => $row['logo'] && $row['logo'] !== 'https://via.placeholder.com/100' ? $row['logo'] : 'https://ui-avatars.com/api/?name=' . urlencode($row['name']) . '&background=random&size=100',
            'description' => 'Đội bóng hoạt động tích cực.',
            'isLookingForMembers' => true,
            'level' => $row['level']
        ];
    }
}

// Fetch Recruitment Posts
$recruitment_query = "SELECT tr.*, t.name as team_name, t.logo as team_logo, t.level as team_level
                      FROM team_recruitment tr
                      JOIN teams t ON tr.team_id = t.id
                      WHERE tr.status = 'open' AND tr.sport = '$active_sport'
                      ORDER BY tr.created_at DESC";
$recruitment_result = $conn->query($recruitment_query);
$recruitments = [];
if ($recruitment_result && $recruitment_result->num_rows > 0) {
    while ($row = $recruitment_result->fetch_assoc()) {
        $recruitments[] = $row;
    }
}

// Fetch Challenges
$challenges_query = "SELECT tc.*, t.name as team_name, t.logo as team_logo 
                     FROM team_challenges tc 
                     JOIN teams t ON tc.challenging_team_id = t.id 
                     WHERE tc.status = 'open' AND tc.sport = '$active_sport'";
$challenges_result = $conn->query($challenges_query);
$challenges = [];
if ($challenges_result && $challenges_result->num_rows > 0) {
    while ($row = $challenges_result->fetch_assoc()) {
        $challenges[] = [
            'id' => $row['id'],
            'challengingTeamId' => $row['challenging_team_id'],
            'challengingTeamName' => $row['team_name'],
            'challengingTeamLogo' => $row['team_logo'] && $row['team_logo'] !== 'https://via.placeholder.com/100' ? $row['team_logo'] : 'https://ui-avatars.com/api/?name=' . urlencode($row['team_name']) . '&background=random&size=100',
            'sport' => $row['sport'],
            'date' => $row['match_date'],
            'time' => $row['match_time'],
            'location' => $row['location'],
            'level' => $row['level'],
            'message' => $row['message']
        ];
    }
}
?>

<!-- Full-width Sticky Sub-navbar -->
<div class="sticky top-16 z-40 bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-4">
        <div class="flex flex-col xl:flex-row justify-between items-center gap-8">
            <!-- Left: Title -->
            <div class="flex-shrink-0">
                <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tighter italic">Trung Tâm <span
                        class="text-green-600">Tìm Người Chơi</span></h1>
            </div>

            <!-- Center: View Switcher Tabs -->
            <div class="flex flex-wrap justify-center gap-6">
                <?php
                $modes = [
                    ['id' => 'challenges', 'label' => 'Tìm Đối Thủ', 'icon' => 'fa-swords'],
                    ['id' => 'teams', 'label' => 'Tìm Đồng Đội', 'icon' => 'fa-user-plus'],
                ];
                foreach ($modes as $mode):
                    ?>
                    <a href="?sport=<?php echo $active_sport; ?>&view=<?php echo $mode['id']; ?>"
                        class="px-4 py-2 font-black text-[10px] tracking-widest uppercase transition-all rounded-xl flex items-center gap-2 <?php echo $view_mode === $mode['id'] ? 'bg-green-50 text-green-700 shadow-sm border border-green-100' : 'text-gray-400 hover:text-gray-600'; ?>">
                        <i class="fas <?php echo $mode['icon']; ?> text-xs"></i>
                        <?php echo $mode['label']; ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Right: Sport Selector -->
            <div class="flex bg-gray-50 p-1 rounded-[18px] border border-gray-100 h-fit flex-shrink-0">
                <a href="?sport=football&view=<?php echo $view_mode; ?>"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-2xl font-black text-[10px] transition-all <?php echo $active_sport === 'football' ? 'bg-green-600 text-white shadow-md' : 'text-gray-400 hover:text-green-600'; ?>">
                    <i class="fas fa-futbol"></i> BÓNG ĐÁ
                </a>
                <a href="?sport=badminton&view=<?php echo $view_mode; ?>"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-2xl font-black text-[10px] transition-all <?php echo $active_sport === 'badminton' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-400 hover:text-blue-600'; ?>">
                    <i class="fas fa-shuttlecock"></i> CẦU LÔNG
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-12">
    <div class="min-h-[500px]">
        <!-- View: Tìm Đối Thủ (Challenges) -->
        <?php if ($view_mode === 'challenges'): ?>
            <div class="space-y-12">
                <div class="grid lg:grid-cols-2 gap-8 animate-fadeIn">
                    <?php
                    foreach ($challenges as $challenge):
                        if ($challenge['sport'] !== $active_sport)
                            continue;
                        ?>
                        <div
                            class="bg-white rounded-2xl p-8 md:p-10 border border-gray-100 shadow-sm hover:shadow-2xl transition-all relative overflow-hidden group">
                            <div class="flex items-center gap-6 mb-8">
                                <img src="<?php echo $challenge['challengingTeamLogo']; ?>"
                                    class="w-24 h-24 rounded-2xl shadow-xl object-cover ring-4 ring-gray-50"
                                    alt="<?php echo $challenge['challengingTeamName']; ?>" />
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span
                                            class="bg-green-100 text-green-700 text-[10px] font-black px-3 py-1 rounded-lg uppercase tracking-wider">
                                            <?php echo $challenge['level']; ?>
                                        </span>
                                        <span
                                            class="text-gray-300 text-[10px] font-black uppercase tracking-widest italic opacity-60">#KÈO_GIAO_HỮU</span>
                                    </div>
                                    <h3 class="text-xl font-black text-gray-900 tracking-tighter uppercase italic">
                                        <?php echo $challenge['challengingTeamName']; ?>
                                    </h3>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-8">
                                <div class="bg-gray-50 p-5 rounded-xl border border-gray-100">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Thời gian</p>
                                    <p class="text-sm font-bold text-gray-900">
                                        <?php echo $challenge['time']; ?>
                                    </p>
                                    <p class="text-[11px] text-gray-500 font-medium">
                                        <?php echo $challenge['date']; ?>
                                    </p>
                                </div>
                                <div class="bg-gray-50 p-5 rounded-xl border border-gray-100">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Sân bãi</p>
                                    <p class="text-sm font-bold text-gray-900 line-clamp-1">
                                        <?php echo $challenge['location']; ?>
                                    </p>
                                    <p class="text-[11px] text-green-600 font-black uppercase">Đã đặt sân</p>
                                </div>
                            </div>

                            <button onclick="openAcceptChallenge(<?php echo htmlspecialchars(json_encode($challenge)); ?>)"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] flex items-center justify-center gap-4 uppercase italic">
                                NHẬN KÈO NGAY <i class="fas fa-bolt text-yellow-400"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div onclick="openCreateChallenge()"
                    class="border-4 border-dashed border-gray-200 rounded-[48px] p-20 flex flex-col items-center justify-center text-center group hover:border-green-400 hover:bg-green-50/30 transition-all cursor-pointer bg-white/50">
                    <div
                        class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mb-8 group-hover:bg-green-600 group-hover:text-white transition-all duration-500 shadow-inner">
                        <i class="fas fa-plus text-4xl group-hover:rotate-90 transition-transform"></i>
                    </div>
                    <h4 class="text-3xl font-black text-gray-900 mb-4 uppercase tracking-tighter italic">ĐĂNG BÀI TÌM ĐỐI
                        THỦ</h4>
                    <p class="text-gray-500 text-base max-w-sm font-medium leading-relaxed">Thiếu đối thủ? Đăng tin ngay để
                        hàng ngàn đội bóng khác thách đấu.</p>
                </div>
            </div>
        <?php elseif ($view_mode === 'teams'): ?>
            <div class="space-y-12 animate-fadeIn">
                <!-- Quick Team Finder Section -->
                <div class="bg-white rounded-[48px] p-8 md:p-12 border border-gray-100 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-12 opacity-5 pointer-events-none">
                        <i class="fas fa-search-location text-[200px] text-green-600"></i>
                    </div>
                    <div class="relative z-10">
                        <h2 class="text-3xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic">Tìm Đội Nhanh
                        </h2>
                        <p class="text-gray-500 mb-10 font-medium italic">Nhập vị trí và thời gian bạn muốn đá, chúng tôi sẽ
                            tìm đội phù hợp.</p>

                        <form id="auto-match-form" class="grid md:grid-cols-3 gap-6 items-end"
                            onsubmit="handleAutoMatch(event)">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Vị
                                    trí ứng tuyển</label>
                                <select id="quick-position" required
                                    class="w-full px-6 py-4 bg-gray-50 rounded-xl border-none outline-none focus:ring-2 focus:ring-green-500 font-bold appearance-none shadow-sm">
                                    <option value="">Chọn vị trí...</option>
                                    <option value="Thủ môn">THỦ MÔN</option>
                                    <option value="Hậu vệ">HẬU VỆ</option>
                                    <option value="Tiền vệ">TIỀN VỆ</option>
                                    <option value="Tiền đạo">TIỀN ĐẠO</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Khung
                                    giờ</label>
                                <input type="text" id="auto-time" required placeholder="VD: 19:30"
                                    class="w-full px-6 py-4 bg-gray-50 rounded-xl border-none outline-none focus:ring-2 focus:ring-green-500 font-bold shadow-sm">
                            </div>
                            <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] flex items-center justify-center gap-3 uppercase italic h-[60px]">
                                <i class="fas fa-search"></i> Tìm Kiếm
                            </button>
                        </form>
                    </div>
                </div>

                <div id="match-results" class="hidden space-y-6 animate-slideUp">
                    <!-- Results populated by JS -->
                </div>

                <!-- Post Recruitment Button/Card -->
                <div onclick="openCreateRecruitment()"
                    class="bg-gradient-to-br from-green-600 to-green-700 rounded-2xl p-12 text-white shadow-xl hover:shadow-2xl transition-all cursor-pointer group relative overflow-hidden flex flex-col items-center text-center">
                    <div class="absolute right-0 top-0 p-12 opacity-10 pointer-events-none">
                        <i class="fas fa-users-plus text-[150px]"></i>
                    </div>
                    <div
                        class="w-20 h-20 bg-white/20 rounded-3xl flex items-center justify-center mb-6 backdrop-blur-md border border-white/20 group-hover:scale-110 transition-transform">
                        <i class="fas fa-plus text-3xl"></i>
                    </div>
                    <h3 class="text-3xl font-black uppercase italic tracking-tighter mb-4">Đội Bạn Đang Thiếu Người?</h3>
                    <p class="text-green-50/80 font-medium max-w-md italic mb-8">Đăng tin tuyển thành viên ngay để hoàn
                        thiện đội hình cho trận đấu sắp tới.</p>
                    <span
                        class="px-10 py-4 bg-white text-green-700 rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg">ĐĂNG
                        TIN TUYỂN NGƯỜI</span>
                </div>

                <!-- Recruitment Posts Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php if (empty($recruitments)): ?>
                        <div
                            class="col-span-full py-20 text-center bg-white rounded-[40px] border border-dashed border-gray-200">
                            <div
                                class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-200 text-3xl">
                                <i class="fas fa-users-slash"></i>
                            </div>
                            <p class="text-gray-400 font-bold italic uppercase text-xs tracking-widest">Hiện chưa có tin tuyển
                                thành viên nào</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recruitments as $rec): ?>
                            <div
                                class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm hover:shadow-xl transition-all group flex flex-col relative overflow-hidden">
                                <div class="flex items-center gap-4 mb-6">
                                    <img src="<?php echo $rec['team_logo']; ?>"
                                        class="w-16 h-16 rounded-2xl object-cover shadow-lg ring-4 ring-gray-50" />
                                    <div>
                                        <h4 class="font-black text-gray-900 text-lg uppercase italic">
                                            <?php echo $rec['team_name']; ?>
                                        </h4>
                                        <div class="flex gap-2">
                                            <span
                                                class="text-[9px] font-black text-green-600 uppercase tracking-widest bg-green-50 px-2 py-0.5 rounded"><?php echo $rec['team_level']; ?></span>
                                            <span
                                                class="text-[9px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 px-2 py-0.5 rounded">Cần
                                                <?php echo $rec['quantity']; ?> ng</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-2xl mb-6 space-y-2">
                                    <div class="flex justify-between items-center text-[10px] font-bold">
                                        <span class="text-gray-400 uppercase">Vị trí cần:</span>
                                        <span
                                            class="text-green-600 font-black uppercase text-xs italic"><?php echo $rec['position']; ?></span>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] font-bold">
                                        <span class="text-gray-400 uppercase">Thời gian:</span>
                                        <span class="text-gray-900"><?php echo $rec['match_time']; ?> |
                                            <?php echo date('d/m', strtotime($rec['match_date'])); ?></span>
                                    </div>
                                </div>

                                <p class="text-gray-500 text-xs mb-8 italic line-clamp-2">"<?php echo $rec['message']; ?>"</p>

                                <button onclick="openApplyRecruitment(<?php echo htmlspecialchars(json_encode($rec)); ?>)"
                                    class="mt-auto w-full py-4 bg-gray-900 hover:bg-green-600 text-white rounded-2xl font-black transition-all hover:scale-[1.02] shadow-lg text-[10px] uppercase italic">
                                    ỨNG TUYỂN NGAY <i class="fas fa-paper-plane ml-2"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div
                class="py-20 text-center bg-white rounded-[40px] border border-dashed border-gray-100 italic text-gray-400 font-medium">
                Đang cập nhật danh sách...
            </div>
        <?php endif; ?>
    </div>

    <!-- Modals -->
    <div id="modal-container" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/80 backdrop-blur-md" onclick="closeModal()"></div>

        <div
            class="bg-white w-full max-w-xl rounded-[48px] overflow-hidden shadow-2xl relative p-8 md:p-14 animate-slideUp max-h-[90vh] overflow-y-auto">
            <button onclick="closeModal()"
                class="absolute top-10 right-10 text-gray-300 hover:text-gray-900 transition-colors w-12 h-12 flex items-center justify-center rounded-full hover:bg-gray-50 z-20">
                <i class="fas fa-times text-2xl"></i>
            </button>

            <!-- Create Challenge Modal -->
            <div id="create-challenge-modal" class="hidden">
                <div class="flex items-center gap-4 mb-8">
                    <div
                        class="w-16 h-16 bg-green-600 text-white rounded-[24px] flex items-center justify-center text-3xl shadow-lg shadow-green-100">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tighter italic">ĐĂNG BÀI TÌM ĐỐI
                            THỦ</h2>
                        <p class="text-gray-500 font-medium text-sm">Điền thông tin chi tiết để tìm đối thủ xứng
                            tầm.
                        </p>
                    </div>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="create_challenge">
                    <div class="space-y-6 mt-8">
                        <div>
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Tên
                                Đội Của Bạn</label>
                            <input type="text" name="team_name" placeholder="VD: FC Warriors" required
                                class="w-full px-6 py-4 bg-gray-50 rounded-[20px] border-none outline-none focus:ring-2 focus:ring-green-500 font-bold shadow-inner" />
                        </div>
                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label
                                    class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Ngày
                                    thi đấu</label>
                                <input type="date" name="date" required
                                    class="w-full px-6 py-4 bg-gray-50 rounded-[20px] border-none outline-none focus:ring-2 focus:ring-green-500 font-bold shadow-inner" />
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Khung
                                    giờ</label>
                                <select name="time" required
                                    class="w-full px-6 py-4 bg-gray-50 rounded-[20px] border-none outline-none focus:ring-2 focus:ring-green-500 font-bold appearance-none shadow-inner">
                                    <option value="17:00 - 18:30">17:00 - 18:30</option>
                                    <option value="18:30 - 20:00">18:30 - 20:00</option>
                                    <option value="20:00 - 21:30">20:00 - 21:30</option>
                                    <option value="21:30 - 23:00">21:30 - 23:00</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Trình
                                độ yêu cầu</label>
                            <div class="flex gap-2">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="level" value="Nghiệp dư" class="peer hidden" checked>
                                    <div
                                        class="py-3 px-2 rounded-xl border-2 border-gray-100 font-bold text-xs text-center peer-checked:border-green-500 peer-checked:text-green-600 transition-all hover:bg-gray-50">
                                        Nghiệp dư
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="level" value="Bán chuyên" class="peer hidden">
                                    <div
                                        class="py-3 px-2 rounded-xl border-2 border-gray-100 font-bold text-xs text-center peer-checked:border-green-500 peer-checked:text-green-600 transition-all hover:bg-gray-50">
                                        Bán chuyên
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="level" value="Chuyên nghiệp" class="peer hidden">
                                    <div
                                        class="py-3 px-2 rounded-xl border-2 border-gray-100 font-bold text-xs text-center peer-checked:border-green-500 peer-checked:text-green-600 transition-all hover:bg-gray-50">
                                        Chuyên nghiệp
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Địa
                                điểm / Tên sân (Nếu đã đặt)</label>
                            <div class="relative">
                                <i
                                    class="fas fa-map-marker-alt absolute left-6 top-1/2 -translate-y-1/2 text-green-600"></i>
                                <input type="text" name="location" placeholder="Nhập tên sân hoặc khu vực..." required
                                    class="w-full pl-14 pr-6 py-4 bg-gray-50 rounded-[20px] border-none outline-none focus:ring-2 focus:ring-green-500 font-bold shadow-inner" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Lời
                                nhắn thách đấu</label>
                            <textarea name="message" required
                                placeholder="Ví dụ: Kèo trà đá vui vẻ, chia sân 5/5, đá đẹp không cay cú..."
                                class="w-full px-6 py-4 bg-gray-50 rounded-[24px] border-none outline-none focus:ring-2 focus:ring-green-500 font-bold h-36 resize-none shadow-inner"></textarea>
                        </div>
                        <div class="flex gap-4 pt-4">
                            <button type="button" onclick="closeModal()"
                                class="flex-1 py-5 rounded-[24px] bg-gray-100 text-gray-500 font-black hover:bg-gray-200 transition-all">HỦY
                                BỎ</button>
                            <button type="submit"
                                class="flex-[2] bg-green-600 hover:bg-green-700 text-white font-bold py-5 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] flex items-center justify-center gap-3 uppercase italic">
                                <i class="fas fa-paper-plane"></i> ĐĂNG TIN NGAY
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Accept Challenge Modal -->
            <div id="accept-challenge-modal" class="hidden">
                <div id="accept-view-1" class="text-center">
                    <div
                        class="w-28 h-28 bg-green-50 rounded-[40px] flex items-center justify-center text-green-600 text-4xl mx-auto mb-8 shadow-inner">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 mb-3 uppercase tracking-tighter italic">Xác Nhận Nhận
                        Kèo</h2>
                    <p class="text-gray-500 mb-10 font-medium italic">Thử thách bản thân cùng <span
                            id="challenge-team-name" class="text-green-600 font-black uppercase"></span></p>
                    <div
                        class="bg-gray-50 rounded-[36px] p-8 text-left mb-10 border border-gray-100 space-y-5 shadow-inner">
                        <div class="flex justify-between items-center pb-5 border-b border-gray-200/50">
                            <span class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Thời
                                gian</span>
                            <span id="challenge-time" class="font-bold text-gray-900 text-lg"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Sân thi
                                đấu</span>
                            <span id="challenge-location" class="font-bold text-gray-900"></span>
                        </div>
                    </div>
                    <button onclick="goChallengePayment()"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-6 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] flex items-center justify-center gap-3 text-lg uppercase italic">
                        CHẤP NHẬN & THANH TOÁN
                    </button>
                </div>
                <div id="accept-view-2" class="hidden text-center">
                    <h2 class="text-2xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic">THANH TOÁN PHÍ
                        SÂN</h2>
                    <p class="text-gray-500 mb-8 font-medium italic">Vui lòng quét mã QR để hoàn tất nhận kèo.</p>
                    <div class="bg-gray-50 rounded-[40px] p-8 mb-8 inline-block border-2 border-dashed border-gray-200">
                        <div class="bg-white p-4 rounded-3xl shadow-sm mb-6 inline-block">
                            <img id="challenge-qr" src="" alt="Payment QR" class="w-48 h-48">
                        </div>
                        <div class="space-y-2">
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Số tiền cọc chia
                                sẻ
                                sân</p>
                            <p class="text-4xl font-black text-green-700">150.000đ</p>
                        </div>
                    </div>
                    <div class="bg-green-50 rounded-3xl p-6 text-left mb-8 border border-green-100 shadow-inner">
                        <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Chủ tài khoản thụ hưởng</p>
                        <p class="font-bold text-gray-900 mb-3 uppercase">SuperSports - TRUNG TÂM KẾT NỐI</p>
                        <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Nội dung chuyển khoản</p>
                        <p id="challenge-note"
                            class="font-bold text-green-800 bg-green-100 px-3 py-1 rounded-lg inline-block uppercase tracking-wider text-xs">
                        </p>
                    </div>
                    <button onclick="confirmAction()"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-5 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] flex items-center justify-center gap-3 uppercase italic">
                        XÁC NHẬN ĐÃ CHUYỂN KHOẢN
                    </button>
                </div>
            </div>

            <!-- Create Recruitment Modal -->
            <div id="create-recruitment-modal" class="hidden">
                <div class="flex items-center gap-4 mb-8">
                    <div
                        class="w-16 h-16 bg-green-600 text-white rounded-[24px] flex items-center justify-center text-3xl shadow-lg shadow-green-100">
                        <i class="fas fa-users-plus"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter italic">TUYỂN THÀNH VIÊN
                        </h2>
                        <p class="text-gray-500 font-medium text-sm">Đội của bạn đang cần thêm những mảnh ghép mới?</p>
                    </div>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="create_recruitment">
                    <div class="space-y-6 mt-8">
                        <div>
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Tên
                                Đội</label>
                            <input type="text" name="team_name" placeholder="VD: FC Ha Noi" required
                                class="w-full px-6 py-4 bg-gray-50 rounded-[20px] border-none outline-none focus:ring-2 focus:ring-green-500 font-bold shadow-inner" />
                        </div>
                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label
                                    class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Vị
                                    trí cần (Có thể nhập nhiều)</label>
                                <input type="text" name="position" placeholder="VD: Thủ môn, Hậu vệ, Tiền đạo..."
                                    required
                                    class="w-full px-6 py-4 bg-gray-50 rounded-[20px] border-none outline-none focus:ring-2 focus:ring-green-500 font-bold shadow-inner" />
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Số
                                    lượng cần tuyển</label>
                                <input type="number" name="quantity" value="1" min="1" max="30" required
                                    class="w-full px-6 py-4 bg-gray-50 rounded-[20px] border-none outline-none focus:ring-2 focus:ring-green-500 font-bold shadow-inner" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label
                                    class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Ngày
                                    thi đấu</label>
                                <input type="date" name="date" required
                                    class="w-full px-6 py-4 bg-gray-50 rounded-[20px] border-none outline-none focus:ring-2 focus:ring-green-500 font-bold shadow-inner" />
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Giờ
                                    thi đấu</label>
                                <input type="text" name="time" placeholder="VD: 18:00 - 19:30" required
                                    class="w-full px-6 py-4 bg-gray-50 rounded-[20px] border-none outline-none focus:ring-2 focus:ring-green-500 font-bold shadow-inner" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Ghi
                                chú</label>
                            <div class="relative">
                                <i
                                    class="fas fa-map-marker-alt absolute left-6 top-1/2 -translate-y-1/2 text-green-600"></i>
                                <input type="text" name="location" placeholder="Nhập địa điểm thi đấu hoặc ghi chú..."
                                    required
                                    class="w-full pl-14 pr-6 py-4 bg-gray-50 rounded-[20px] border-none outline-none focus:ring-2 focus:ring-green-500 font-bold shadow-inner" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Lời
                                nhắn</label>
                            <textarea name="message" required placeholder="Mô tả về đội và yêu cầu..."
                                class="w-full px-6 py-4 bg-gray-50 rounded-[24px] border-none outline-none focus:ring-2 focus:ring-green-500 font-bold h-32 resize-none shadow-inner"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-5 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] flex items-center justify-center gap-3 uppercase italic">
                            <i class="fas fa-paper-plane"></i> ĐĂNG TIN NGAY
                        </button>
                    </div>
                </form>
            </div>

            <!-- Apply Recruitment Modal -->
            <div id="apply-recruitment-modal" class="hidden">
                <div class="flex items-center gap-4 mb-8">
                    <img id="apply-team-logo" src="" class="w-16 h-16 rounded-2xl shadow-lg object-cover" />
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter italic">ỨNG TUYỂN</h2>
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">Đội: <span
                                id="apply-team-name" class="text-green-600 font-black"></span></p>
                    </div>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="apply_recruitment">
                    <input type="hidden" name="recruitment_id" id="apply-recruitment-id">
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100 italic text-sm text-gray-600">
                            <p class="mb-2 uppercase font-black text-[10px] text-gray-400 not-italic">Thông tin trận:
                            </p>
                            <p><i class="far fa-clock mr-2"></i> <span id="apply-time"></span></p>
                            <p><i class="fas fa-map-marker-alt mr-2"></i> <span id="apply-location"></span></p>
                            <p><i class="fas fa-user-tag mr-2"></i> Tuyển vị trí: <span id="apply-position"
                                    class="font-black text-green-600"></span></p>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest mb-3">Lời
                                giới thiệu</label>
                            <textarea name="apply_message" required
                                placeholder="Giới thiệu ngắn gọn về bản thân và kinh nghiệm..."
                                class="w-full px-6 py-4 bg-gray-50 rounded-[24px] border-none outline-none focus:ring-2 focus:ring-green-500 font-bold h-32 resize-none shadow-inner"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full bg-gray-900 hover:bg-green-600 text-white font-bold py-5 rounded-2xl shadow-lg transition-transform hover:scale-[1.02] flex items-center justify-center gap-3 uppercase italic">
                            GỬI YÊU CẦU THAM GIA <i class="fas fa-heart text-red-500"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Join Team Modal -->
            <div id="join-team-modal" class="hidden text-center">
                <img id="join-team-logo" src=""
                    class="w-24 h-24 rounded-3xl mx-auto mb-6 shadow-xl object-cover ring-4 ring-green-50" alt="" />
                <h2 id="join-team-name" class="text-3xl font-black text-gray-900 mb-6 uppercase italic"></h2>
                <div class="text-left space-y-4">
                    <input type="text" placeholder="Vị trí sở trường của bạn..."
                        class="w-full px-6 py-4 bg-gray-50 rounded-2xl border-none outline-none focus:ring-2 focus:ring-green-500 font-bold shadow-inner" />
                    <textarea placeholder="Gửi lời nhắn đến đội trưởng..."
                        class="w-full px-6 py-4 bg-gray-50 rounded-2xl border-none outline-none focus:ring-2 focus:ring-green-500 font-bold h-32 resize-none shadow-inner"></textarea>
                </div>
                <button onclick="confirmAction()"
                    class="w-full bg-gray-900 hover:bg-green-600 text-white font-black py-5 rounded-3xl mt-8 transition-all shadow-lg active:scale-95">GỬI
                    YÊU CẦU GIA NHẬP</button>
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
            <h4 class="font-black text-base uppercase tracking-wider">Thành Công!</h4>
            <p class="text-xs text-gray-400 font-medium">Hệ hệ thống đã ghi nhận yêu cầu của bạn.</p>
        </div>
    </div>
</div>

<script>
    let currentChallenge = null;

    function openCreateChallenge() {
        closeModals();
        document.getElementById('create-challenge-modal').classList.remove('hidden');
        document.getElementById('modal-container').classList.remove('hidden');
        document.getElementById('modal-container').style.display = 'flex';
        document.getElementById('modal-container').scrollTo(0, 0);
        document.body.style.overflow = 'hidden';
    }

    function openCreateRecruitment() {
        closeModals();
        document.getElementById('create-recruitment-modal').classList.remove('hidden');
        document.getElementById('modal-container').classList.remove('hidden');
        document.getElementById('modal-container').style.display = 'flex';
        document.getElementById('modal-container').scrollTo(0, 0);
        document.body.style.overflow = 'hidden';
    }

    function openApplyRecruitment(rec) {
        closeModals();
        document.getElementById('apply-team-logo').src = rec.team_logo;
        document.getElementById('apply-team-name').innerText = rec.team_name;
        document.getElementById('apply-recruitment-id').value = rec.id;
        document.getElementById('apply-time').innerText = rec.match_time + ' | ' + new Date(rec.match_date).toLocaleDateString('vi-VN');
        document.getElementById('apply-location').innerText = rec.location;
        document.getElementById('apply-position').innerText = rec.position;

        document.getElementById('apply-recruitment-modal').classList.remove('hidden');
        document.getElementById('modal-container').classList.remove('hidden');
        document.getElementById('modal-container').style.display = 'flex';
        document.getElementById('modal-container').scrollTo(0, 0);
        document.body.style.overflow = 'hidden';
    }

    function openAcceptChallenge(challenge) {
        currentChallenge = challenge;
        closeModals();
        document.getElementById('challenge-team-name').innerText = challenge.challengingTeamName;
        document.getElementById('challenge-time').innerText = challenge.time + ' | ' + challenge.date;
        document.getElementById('challenge-location').innerText = challenge.location;

        document.getElementById('accept-view-1').classList.remove('hidden');
        document.getElementById('accept-view-2').classList.add('hidden');
        document.getElementById('accept-challenge-modal').classList.remove('hidden');
        document.getElementById('modal-container').classList.remove('hidden');
        document.getElementById('modal-container').style.display = 'flex';
        document.getElementById('modal-container').scrollTo(0, 0);
        document.body.style.overflow = 'hidden';
    }

    function goChallengePayment() {
        const userName = "<?php echo $user ? $user['name'] : 'PLAYER'; ?>".toUpperCase().split(' ').pop();
        document.getElementById('challenge-qr').src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=STB:SuperSports-MATCH-${currentChallenge.id}-150000`;
        document.getElementById('challenge-note').innerText = `KEO ${currentChallenge.id} ${userName}`;

        document.getElementById('accept-view-1').classList.add('hidden');
        document.getElementById('accept-view-2').classList.remove('hidden');
    }

    function openJoinTeam(name, logo) {
        closeModals();
        document.getElementById('join-team-name').innerText = "Xin Gia Nhập " + name;
        document.getElementById('join-team-logo').src = logo;
        document.getElementById('join-team-modal').classList.remove('hidden');
        document.getElementById('modal-container').classList.remove('hidden');
        document.getElementById('modal-container').style.display = 'flex';
        document.getElementById('modal-container').scrollTo(0, 0);
        document.body.style.overflow = 'hidden';
    }

    function closeModals() {
        document.getElementById('create-challenge-modal').classList.add('hidden');
        document.getElementById('accept-challenge-modal').classList.add('hidden');
        document.getElementById('join-team-modal').classList.add('hidden');
    }

    function closeModal() {
        document.getElementById('modal-container').classList.add('hidden');
        document.getElementById('modal-container').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function confirmAction() {
        closeModal();
        const note = document.getElementById('success-notification');
        note.classList.remove('hidden');
        setTimeout(() => {
            note.classList.add('hidden');
        }, 3000);
    }

    async function handleAutoMatch(e) {
        e.preventDefault();
        const time = document.getElementById('auto-time').value;
        const position = document.getElementById('quick-position').value;
        const results = document.getElementById('match-results');
        const activeSport = "<?php echo $active_sport; ?>";

        results.innerHTML = '<div class="text-center py-10"><i class="fas fa-spinner fa-spin text-4xl text-green-600"></i></div>';
        results.classList.remove('hidden');

        try {
            const response = await fetch(`backend/search_teams.php?position=${encodeURIComponent(position)}&time=${encodeURIComponent(time)}&sport=${activeSport}`);
            const data = await response.json();

            if (data.success && data.matches.length > 0) {
                let html = `<h3 class="text-xl font-black text-gray-900 px-4 uppercase tracking-tighter italic">Kết quả tìm được (${data.matches.length})</h3>
                            <div class="grid md:grid-cols-2 gap-6">`;

                data.matches.forEach(match => {
                    html += `
                    <div class="bg-white p-8 rounded-[36px] border border-green-100 shadow-md hover:shadow-xl transition-all group">
                      <div class="flex items-center gap-4 mb-6">
                        <img src="${match.team_logo}" class="w-14 h-14 rounded-2xl object-cover shadow-lg border border-gray-100">
                        <div>
                          <h4 class="font-black text-gray-900 text-lg uppercase italic">${match.team_name}</h4>
                          <p class="text-xs text-green-600 font-bold uppercase tracking-widest"><i class="fas fa-map-marker-alt mr-1"></i> ${match.location}</p>
                          <p class="text-xs text-gray-400 font-bold uppercase tracking-widest"><i class="far fa-clock mr-1"></i> ${match.match_time}</p>
                        </div>
                      </div>
                      <div class="bg-green-50 p-4 rounded-2xl mb-4">
                        <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Cần vị trí</p>
                        <p class="text-green-700 font-black text-sm uppercase italic">${match.position}</p>
                      </div>
                      <p class="text-gray-500 text-sm mb-6 italic line-clamp-2">"${match.message}"</p>
                      <button onclick='openApplyRecruitment(${JSON.stringify(match)})' class="w-full py-4 bg-gray-900 hover:bg-green-600 text-white rounded-[20px] font-black text-sm transition-all shadow-md uppercase italic">Gửi Yêu Cầu Tham Gia</button>
                    </div>`;
                });

                html += `</div>`;
                results.innerHTML = html;
            } else {
                results.innerHTML = `
                <div class="text-center py-10">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search-minus text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-bold italic">Không tìm thấy đội nào phù hợp ở ${location} khung giờ ${time}.</p>
                    <p class="text-gray-400 text-xs mt-2">Hãy thử mở rộng khu vực hoặc đổi giờ khác.</p>
                </div>`;
            }

        } catch (error) {
            console.error('Error fetching matches:', error);
            results.innerHTML = `<p class="text-center text-red-500 font-bold">Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại.</p>`;
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

    @keyframes slideUp {
        from {
            transform: translateY(50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
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
        animation: fadeIn 0.3s ease-out;
    }

    .animate-slideUp {
        animation: slideUp 0.4s ease-out;
    }

    .animate-slideDown {
        animation: slideDown 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
</style>

</div>

<?php require_once 'includes/footer.php'; ?>