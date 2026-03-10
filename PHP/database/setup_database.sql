-- Tạo database nếu chưa có
CREATE DATABASE IF NOT EXISTS du_an_php;
USE du_an_php;

-- 1. Bảng Users (Người dùng)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Bảng Pitches (Sân bóng/Sân cầu lông)
CREATE TABLE IF NOT EXISTS pitches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    type VARCHAR(50) NOT NULL,
    sport ENUM('football', 'badminton') NOT NULL,
    image VARCHAR(255),
    status ENUM('available', 'busy') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Bảng Bookings (Đặt sân)
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    pitch_id INT NOT NULL,
    booking_date DATE NOT NULL,
    time_slot VARCHAR(50) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    discount DECIMAL(10, 2) DEFAULT 0,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pitch_id) REFERENCES pitches(id) ON DELETE CASCADE
);

-- 4. Bảng Teams (Đội bóng)
CREATE TABLE IF NOT EXISTS teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    leader_id INT NOT NULL,
    level ENUM('Nghiệp dư', 'Bán chuyên', 'Chuyên nghiệp') DEFAULT 'Nghiệp dư',
    sport ENUM('football', 'badminton') DEFAULT 'football',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (leader_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Bảng Team Challenges (Kèo tìm đối)
CREATE TABLE IF NOT EXISTS team_challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    challenging_team_id INT NOT NULL,
    sport ENUM('football', 'badminton') DEFAULT 'football',
    match_date DATE,
    match_time VARCHAR(50),
    location VARCHAR(255),
    level ENUM('Nghiệp dư', 'Bán chuyên', 'Chuyên nghiệp'),
    message TEXT,
    status ENUM('open', 'accepted', 'completed', 'cancelled') DEFAULT 'open',
    accepted_by_team_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (challenging_team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (accepted_by_team_id) REFERENCES teams(id) ON DELETE SET NULL
);

-- 6. Bảng Products (Sản phẩm cửa hàng)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    description TEXT,
    is_hot BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 7. Bảng Orders (Đơn hàng sản phẩm)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'paid', 'shipped', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 8. Bảng Order Items (Chi tiết đơn hàng)
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    size VARCHAR(10),
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 9. Bảng Match Requests (Vãng lai ghép đội)
CREATE TABLE IF NOT EXISTS match_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    preferred_position VARCHAR(50),
    match_date DATE,
    match_time VARCHAR(50),
    status ENUM('open', 'matched', 'cancelled') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 10. Bảng Promotions (Khuyến mãi)
CREATE TABLE IF NOT EXISTS promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    discount VARCHAR(50) NOT NULL,
    `desc` TEXT,
    code VARCHAR(50) NOT NULL,
    color VARCHAR(100) DEFAULT 'from-green-500 to-green-700',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 11. Bảng News (Tin tức)
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    content TEXT,
    image VARCHAR(255),
    author VARCHAR(100) DEFAULT 'Admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 12. Bảng Testimonials (Đánh giá)
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(100),
    content TEXT,
    rating INT DEFAULT 5,
    avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Thêm dữ liệu mẫu
INSERT INTO users (name, email, password, role) VALUES 
('Admin Master', 'admin@pitch.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Nguyễn Văn Cường', 'user@pitch.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user')
ON DUPLICATE KEY UPDATE name=name;

INSERT INTO pitches (name, location, price, type, sport, image, status) VALUES 
('Sân Đại Học Y', 'Quận Đống Đa, Hà Nội', 300000, 'SÂN 7', 'football', 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?auto=format&fit=crop&q=80&w=800', 'available'),
('Sân Cầu Lông Victor', 'Quận Cầu Giấy, Hà Nội', 80000, 'SÂN ĐÔI', 'badminton', 'https://images.unsplash.com/photo-1626224484214-4059d4c0ee4a?auto=format&fit=crop&q=80&w=800', 'available'),
('Sân Mỹ Đình Mini', 'Quận Nam Từ Liêm, Hà Nội', 400000, 'SÂN 11', 'football', 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&q=80&w=800', 'available')
ON DUPLICATE KEY UPDATE name=name;

INSERT INTO promotions (title, discount, `desc`, code, color) VALUES 
('Khuyến mãi mùa hè', '30%', 'Giảm giá cho tất cả sân bóng', 'SUMMER30', 'from-orange-500 to-red-600'),
('Ưu đãi cuối tuần', '20%', 'Đặt sân thứ 7, chủ nhật', 'WEEKEND20', 'from-blue-500 to-purple-600'),
('Thành viên mới', '15%', 'Cho lần đặt sân đầu tiên', 'NEWMEMBER15', 'from-green-500 to-teal-600')
ON DUPLICATE KEY UPDATE title=title;

INSERT INTO news (title, category, `desc`, content, image, date) VALUES 
('Giải bóng đá phong trào 2024', 'Sự kiện', 'Giải đấu lớn nhất năm dành cho các đội nghiệp dư', 'Nội dung chi tiết...', 'https://images.unsplash.com/photo-1579952363873-27f3bade9f55?auto=format&fit=crop&q=80&w=800', '15/01/2024'),
('Khai trương sân mới tại Hà Đông', 'Tin tức', 'Hệ thống sân cỏ nhân tạo cao cấp', 'Nội dung chi tiết...', 'https://images.unsplash.com/photo-1556056504-5c7696c4c28d?auto=format&fit=crop&q=80&w=800', '20/01/2024'),
('Tips chọn giày bóng đá phù hợp', 'Hướng dẫn', 'Bí quyết chọn giày cho từng vị trí', 'Nội dung chi tiết...', 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&q=80&w=800', '25/01/2024')
ON DUPLICATE KEY UPDATE title=title;

INSERT INTO testimonials (name, role, content, rating, avatar) VALUES 
('Trần Minh Tuấn', 'Cầu thủ nghiệp dư', 'Hệ thống đặt sân rất tiện lợi, giao diện đẹp và dễ sử dụng!', 5, 'https://i.pravatar.cc/150?img=12'),
('Lê Hoàng Nam', 'Đội trưởng FC Warriors', 'Tìm được nhiều đội bóng cùng trình độ để giao lưu. Rất hài lòng!', 5, 'https://i.pravatar.cc/150?img=33'),
('Phạm Thị Lan', 'Quản lý sân Mỹ Đình', 'Đối tác tuyệt vời, giúp sân của chúng tôi luôn kín lịch!', 5, 'https://i.pravatar.cc/150?img=45')
ON DUPLICATE KEY UPDATE name=name;

INSERT INTO products (name, category, price, stock, image, description, is_hot) VALUES 
('Nike Mercurial Vapor 15', 'GIÀY', 2450000, 50, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&q=80&w=600', 'Giày bóng đá chuyên nghiệp.', TRUE),
('Áo Đấu Việt Nam 2024', 'ÁO ĐẤU', 450000, 100, 'https://images.unsplash.com/photo-1516444463934-17f1df942de4?auto=format&fit=crop&q=80&w=600', 'Chất liệu thoáng khí.', TRUE)
ON DUPLICATE KEY UPDATE name=name;

-- Thêm index để tăng hiệu suất
CREATE INDEX IF NOT EXISTS idx_challenge_sport_date ON team_challenges(sport, match_date);
CREATE INDEX IF NOT EXISTS idx_booking_date ON bookings(booking_date);
CREATE INDEX IF NOT EXISTS idx_booking_pitch_date ON bookings(pitch_id, booking_date);
