-- Database: pitch_booking_db
CREATE DATABASE IF NOT EXISTS pitch_booking_db;
USE pitch_booking_db;

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
    type VARCHAR(50) NOT NULL, -- Ví dụ: SÂN 5, SÂN 7, SÂN ĐƠN, SÂN ĐÔI
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
    category VARCHAR(50) NOT NULL, -- GIÀY, ÁO ĐẤU, PHỤ KIỆN
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

-- 9. Bảng Match Requests (Vãng lai ghép đội - cho filter auto-matching)
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

-- Thêm dữ liệu mẫu (Dùng để test)
INSERT INTO users (name, email, password, role) VALUES 
('Admin Master', 'admin@pitch.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Nguyễn Văn Cường', 'user@pitch.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

INSERT INTO pitches (name, location, price, type, sport, image, status) VALUES 
('Sân Đại Học Y', 'Quận Đống Đa, Hà Nội', 300000, 'SÂN 7', 'football', 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?auto=format&fit=crop&q=80&w=800', 'available'),
('Sân Cầu Lông Victor', 'Quận Cầu Giấy, Hà Nội', 80000, 'SÂN ĐÔI', 'badminton', 'https://images.unsplash.com/photo-1626224484214-4059d4c0ee4a?auto=format&fit=crop&q=80&w=800', 'available');

INSERT INTO products (name, category, price, stock, image, description, is_hot) VALUES 
('Nike Mercurial Vapor 15', 'GIÀY', 2450000, 50, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&q=80&w=600', 'Giày bóng đá chuyên nghiệp.', TRUE),
('Áo Đấu Việt Nam 2024', 'ÁO ĐẤU', 450000, 100, 'https://images.unsplash.com/photo-1516444463934-17f1df942de4?auto=format&fit=crop&q=80&w=600', 'Chất liệu thoáng khí.', TRUE);

-- Thêm index
CREATE INDEX idx_challenge_sport_date ON team_challenges(sport, match_date);
CREATE INDEX idx_booking_date ON bookings(booking_date);
