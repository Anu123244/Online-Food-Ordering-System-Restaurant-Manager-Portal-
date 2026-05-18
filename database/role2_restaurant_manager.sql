-- FoodHub Online Food Ordering System
-- Role 2: Restaurant Manager independent database
-- Import this file in phpMyAdmin before running the project.
-- XAMPP compatible: MySQL/MariaDB + PHP mysqli.

CREATE DATABASE IF NOT EXISTS foodhub_roles CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE foodhub_roles;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS platform_settings;
DROP TABLE IF EXISTS complaints;
DROP TABLE IF EXISTS delivery_addresses;
DROP TABLE IF EXISTS saved_restaurants;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS delivery_assignments;
DROP TABLE IF EXISTS delivery_agents;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS discounts;
DROP TABLE IF EXISTS menu_items;
DROP TABLE IF EXISTS menu_categories;
DROP TABLE IF EXISTS restaurants;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(30),
    role ENUM('customer','manager','agent','admin') NOT NULL,
    profile_pic VARCHAR(255),
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE restaurants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    manager_id INT NOT NULL,
    name VARCHAR(160) NOT NULL,
    description TEXT,
    cuisine_type VARCHAR(80),
    address VARCHAR(255),
    city VARCHAR(80),
    logo_path VARCHAR(255),
    opening_hours VARCHAR(120),
    delivery_radius_km DECIMAL(6,2) DEFAULT 5.00,
    is_open TINYINT(1) DEFAULT 1,
    is_approved TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_restaurants_manager FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE menu_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    display_order INT DEFAULT 0,
    CONSTRAINT fk_categories_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    category_id INT,
    name VARCHAR(140) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255),
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_items_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    CONSTRAINT fk_items_category FOREIGN KEY (category_id) REFERENCES menu_categories(id) ON DELETE SET NULL
);

CREATE TABLE discounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    menu_item_id INT NOT NULL,
    restaurant_id INT NOT NULL,
    discount_pct DECIMAL(5,2) NOT NULL,
    valid_from DATETIME NOT NULL,
    valid_until DATETIME NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    CONSTRAINT fk_discounts_item FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    CONSTRAINT fk_discounts_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    restaurant_id INT NOT NULL,
    agent_id INT NULL,
    delivery_address VARCHAR(255) NOT NULL,
    payment_method ENUM('Cash','Card') NOT NULL DEFAULT 'Cash',
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('pending','accepted','preparing','ready','picked_up','on_the_way','delivered','cancelled') NOT NULL DEFAULT 'pending',
    estimated_delivery_minutes INT DEFAULT 40,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_orders_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    CONSTRAINT fk_orders_agent_user FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_item FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
);

CREATE TABLE delivery_agents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    vehicle_type VARCHAR(60) NOT NULL,
    is_online TINYINT(1) DEFAULT 0,
    current_location_text VARCHAR(255),
    total_earnings DECIMAL(10,2) DEFAULT 0,
    is_approved TINYINT(1) DEFAULT 1,
    CONSTRAINT fk_delivery_agents_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE delivery_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    agent_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    picked_up_at DATETIME NULL,
    delivered_at DATETIME NULL,
    status ENUM('assigned','declined','picked_up','on_the_way','delivered') DEFAULT 'assigned',
    CONSTRAINT fk_assignments_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_assignments_agent_user FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    customer_id INT NOT NULL,
    restaurant_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    manager_reply TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_customer FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

CREATE TABLE saved_restaurants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    restaurant_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_saved_customer FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_saved_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);

CREATE TABLE delivery_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    label VARCHAR(80) NOT NULL,
    address_line VARCHAR(255) NOT NULL,
    city VARCHAR(80) NOT NULL,
    is_default TINYINT(1) DEFAULT 0,
    CONSTRAINT fk_addresses_customer FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submitter_id INT NOT NULL,
    restaurant_id INT NULL,
    subject VARCHAR(160) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('open','resolved') DEFAULT 'open',
    admin_note TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_complaints_submitter FOREIGN KEY (submitter_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_complaints_restaurant FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE SET NULL
);

CREATE TABLE platform_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value VARCHAR(255) NOT NULL
);

SET FOREIGN_KEY_CHECKS = 1;

-- Demo data for Role 2: Restaurant Manager
-- Login: manager@foodhub.test
-- Password: password123

INSERT INTO users (id, name, email, password_hash, phone, role, is_active) VALUES
(1, 'Tithi Restaurant Manager', 'manager@foodhub.test', '$2y$10$H6/7zv2Tv7yK79bY3x1niu/M2p.1ZQQYRXCAjQTOuDtkccnE9vrNK', '01711111111', 'manager', 1),
(2, 'Demo Customer', 'customer@foodhub.test', '$2y$10$H6/7zv2Tv7yK79bY3x1niu/M2p.1ZQQYRXCAjQTOuDtkccnE9vrNK', '01722222222', 'customer', 1),
(3, 'Demo Agent', 'agent@foodhub.test', '$2y$10$H6/7zv2Tv7yK79bY3x1niu/M2p.1ZQQYRXCAjQTOuDtkccnE9vrNK', '01733333333', 'agent', 1),
(4, 'Second Customer', 'customer2@foodhub.test', '$2y$10$H6/7zv2Tv7yK79bY3x1niu/M2p.1ZQQYRXCAjQTOuDtkccnE9vrNK', '01744444444', 'customer', 1),
(5, 'Pending Restaurant Manager', 'pending@foodhub.test', '$2y$10$H6/7zv2Tv7yK79bY3x1niu/M2p.1ZQQYRXCAjQTOuDtkccnE9vrNK', '01755555555', 'manager', 1);

INSERT INTO restaurants (id, manager_id, name, description, cuisine_type, address, city, logo_path, opening_hours, delivery_radius_km, is_open, is_approved) VALUES
(1, 1, 'Tithi Spice Kitchen', 'Fresh burgers, rice bowls, and snacks for online delivery.', 'Fast Food', 'Mirpur 10, Dhaka', 'Dhaka', 'assets/images/logo-mark.svg', '10:00 AM - 11:00 PM', 8.00, 1, 1),
(2, 5, 'Pending Food Corner', 'This restaurant is waiting for admin approval.', 'Bengali', 'Uttara, Dhaka', 'Dhaka', 'assets/images/logo-mark.svg', '11:00 AM - 10:00 PM', 5.00, 0, 0);

INSERT INTO menu_categories (id, restaurant_id, name, display_order) VALUES
(1, 1, 'panta vat', 1),
(2, 1, 'Rice Bowls', 2),
(3, 1, 'Drinks', 3),
(4, 1, 'Snacks', 4);

INSERT INTO menu_items (id, restaurant_id, category_id, name, description, price, image_path, is_available) VALUES
(1, 1, 1, 'Chicken Burger', 'Crispy chicken burger with house sauce.', 220.00, 'assets/images/food-burger.svg', 1),
(2, 1, 2, 'Beef Rice Bowl', 'Beef, rice, salad, and spicy sauce.', 320.00, 'assets/images/food-rice.svg', 1),
(3, 1, 3, 'Lemonade', 'Fresh lemonade.', 90.00, 'assets/images/food-lemonade.svg', 1),
(4, 1, 4, 'French Fries', 'Crispy salted fries.', 140.00, 'assets/images/food-fries.svg', 1);

INSERT INTO discounts (id, menu_item_id, restaurant_id, discount_pct, valid_from, valid_until, is_active) VALUES
(1, 1, 1, 10.00, DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_ADD(NOW(), INTERVAL 30 DAY), 1),
(2, 3, 1, 15.00, DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_ADD(NOW(), INTERVAL 15 DAY), 0);

INSERT INTO orders (id, customer_id, restaurant_id, agent_id, delivery_address, payment_method, subtotal, delivery_fee, total_amount, status, estimated_delivery_minutes, created_at) VALUES
(1, 2, 1, NULL, 'Dhanmondi 27, Dhaka', 'Cash', 530.00, 60.00, 590.00, 'pending', 45, NOW()),
(2, 2, 1, NULL, 'Banani 11, Dhaka', 'Card', 320.00, 60.00, 380.00, 'accepted', 35, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 2, 1, 3, 'Uttara Sector 7, Dhaka', 'Cash', 220.00, 60.00, 280.00, 'delivered', 40, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(4, 4, 1, NULL, 'Mirpur DOHS, Dhaka', 'Cash', 460.00, 60.00, 520.00, 'preparing', 30, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(5, 4, 1, 3, 'Gulshan 2, Dhaka', 'Card', 410.00, 60.00, 470.00, 'delivered', 40, DATE_SUB(NOW(), INTERVAL 10 DAY)),
(6, 2, 1, 3, 'Mohammadpur, Dhaka', 'Cash', 640.00, 60.00, 700.00, 'delivered', 50, DATE_SUB(NOW(), INTERVAL 35 DAY));

INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price) VALUES
(1, 1, 2, 220.00),
(1, 3, 1, 90.00),
(2, 2, 1, 320.00),
(3, 1, 1, 220.00),
(4, 1, 1, 220.00),
(4, 4, 1, 140.00),
(4, 3, 1, 90.00),
(5, 2, 1, 320.00),
(5, 3, 1, 90.00),
(6, 2, 2, 320.00);

INSERT INTO delivery_agents (user_id, vehicle_type, is_online, current_location_text, total_earnings, is_approved) VALUES
(3, 'Bike', 1, 'Mirpur', 120.00, 1);

INSERT INTO delivery_assignments (order_id, agent_id, status, assigned_at, picked_up_at, delivered_at) VALUES
(3, 3, 'delivered', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY)),
(5, 3, 'delivered', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY)),
(6, 3, 'delivered', DATE_SUB(NOW(), INTERVAL 35 DAY), DATE_SUB(NOW(), INTERVAL 35 DAY), DATE_SUB(NOW(), INTERVAL 35 DAY));

INSERT INTO reviews (order_id, customer_id, restaurant_id, rating, comment, manager_reply) VALUES
(3, 2, 1, 5, 'Food was hot and delivery was smooth.', NULL),
(5, 4, 1, 4, 'Good food, but the lemonade was a little too sweet.', 'Thank you for the feedback. We will adjust the sweetness level.');

INSERT INTO complaints (submitter_id, restaurant_id, subject, description, status, admin_note) VALUES
(2, 1, 'Late delivery question', 'Customer asked admin about an old late delivery issue.', 'open', NULL),
(4, 1, 'Missing sauce packet', 'Customer reported that sauce was missing from one order.', 'resolved', 'Restaurant manager was notified.');

INSERT INTO platform_settings (setting_key, setting_value) VALUES
('delivery_fee', '60'),
('commission_rate', '10');
