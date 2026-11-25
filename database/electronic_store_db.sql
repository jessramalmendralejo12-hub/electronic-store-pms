-- Electronic Store Product Management System
-- Database: electronic_store_db (for XAMPP / local use)

CREATE DATABASE IF NOT EXISTS electronic_store_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE electronic_store_db;

-- ------------------------------------------------------------
-- Tables
-- ------------------------------------------------------------
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name  VARCHAR(100) NOT NULL,
    username   VARCHAR(50)  NOT NULL,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100) NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
    id             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    name           VARCHAR(150)  NOT NULL,
    category_id    INT UNSIGNED  NOT NULL,
    brand          VARCHAR(100)  NOT NULL,
    model_number   VARCHAR(100)  NOT NULL,
    description    TEXT          DEFAULT NULL,
    purchase_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    selling_price  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock_quantity INT UNSIGNED  NOT NULL DEFAULT 0,
    image          VARCHAR(255)  DEFAULT NULL,
    date_added     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_products_category (category_id),
    CONSTRAINT fk_products_category FOREIGN KEY (category_id)
        REFERENCES categories (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Sample data
-- Default admin login -> username: admin | password: admin123
-- ------------------------------------------------------------
INSERT INTO users (full_name, username, password) VALUES
('Store Administrator', 'admin', '$2y$10$/SMY5fZmCGLRyndILcqoN.Yv/5F8q2czTpDXWC.LToVen12M5SZvu');

INSERT INTO categories (name, description) VALUES
('Smartphones',   'Mobile phones and accessories'),
('Laptops',       'Notebooks and ultrabooks'),
('Audio',         'Headphones, earbuds and speakers'),
('Gaming',        'Consoles, controllers and gaming gear'),
('Smart Home',    'Smart plugs, cameras and home devices'),
('Accessories',   'Chargers, cables and peripherals');

INSERT INTO products (name, category_id, brand, model_number, description, purchase_price, selling_price, stock_quantity, date_added) VALUES
('Galaxy A55 5G 8/256GB',      1, 'Samsung', 'SM-A556E',    '6.6-inch Super AMOLED, 50MP camera, 5000mAh battery.',        19500.00, 22990.00, 25, '2026-10-01 09:00:00'),
('iPhone 15 128GB',            1, 'Apple',   'A3090',       '6.1-inch Super Retina XDR, A16 Bionic chip.',                 42000.00, 46990.00,  8, '2026-10-03 10:30:00'),
('Redmi Note 13 Pro',          1, 'Xiaomi',  '2312DRA50G',  '200MP camera, 120Hz AMOLED display.',                         12500.00, 14990.00, 30, '2026-10-05 14:15:00'),
('VivoBook 15',                2, 'ASUS',    'X1504ZA',     '15.6-inch FHD, Intel Core i5, 8GB RAM, 512GB SSD.',           27000.00, 31995.00, 12, '2026-10-08 11:00:00'),
('IdeaPad Slim 3',             2, 'Lenovo',  '82XB00ABPH',  '14-inch, Ryzen 5, 16GB RAM, 512GB SSD.',                      29500.00, 34990.00,  5, '2026-10-10 13:45:00'),
('MacBook Air M2 13"',         2, 'Apple',   'MLY33',       '13.6-inch Liquid Retina, M2 chip, 8GB RAM, 256GB SSD.',       52000.00, 58990.00,  0, '2026-10-12 15:20:00'),
('WH-CH520 Wireless Headphones', 3, 'Sony',  'WH-CH520',    'Bluetooth headphones with up to 50 hours battery life.',       2200.00,  2990.00, 40, '2026-10-14 09:40:00'),
('AirPods Pro (2nd Gen)',      3, 'Apple',   'MTJV3',       'Active noise cancellation, USB-C charging case.',             13500.00, 15990.00,  9, '2026-10-16 16:00:00'),
('PlayStation 5 Slim',         4, 'Sony',    'CFI-2000',    'Disc edition, 1TB SSD, includes one DualSense controller.',   26500.00, 29990.00,  3, '2026-10-18 10:10:00'),
('DualSense Wireless Controller', 4, 'Sony', 'CFI-ZCT1W',   'White wireless controller with haptic feedback.',              3300.00,  3990.00, 18, '2026-10-20 12:30:00'),
('Tapo C200 Wi-Fi Camera',     5, 'TP-Link', 'Tapo C200',   '1080p pan/tilt home security camera with night vision.',        900.00,  1290.00, 22, '2026-10-22 08:50:00'),
('Anker 20000mAh Power Bank',  6, 'Anker',   'A1288',       '22.5W fast charging, dual USB output.',                        1900.00,  2490.00,  0, '2026-10-25 17:05:00'),
('Logitech MX Master 3S Mouse', 6, 'Logitech', '910-006559', 'Wireless ergonomic mouse, 8K DPI, quiet clicks.',             4200.00,  5290.00, 14, '2026-10-27 14:00:00');
