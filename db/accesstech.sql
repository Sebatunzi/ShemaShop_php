-- =========================================================
-- accesstech DATABASE SCHEMA
-- CAT 2 Project — e-shopping website
-- Roles: Admin, Seller, Customer (all stored in `users`)
-- =========================================================

CREATE DATABASE IF NOT EXISTS accesstech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE accesstech;

-- ---------------------------------------------------------
-- 1. USERS  (Admin / Seller / Customer all live here)
-- ---------------------------------------------------------
CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(100)        NOT NULL,
    email         VARCHAR(150)        NOT NULL UNIQUE,
    password      VARCHAR(255)        NOT NULL,           -- hashed with password_hash()
    role          ENUM('admin','seller','customer') NOT NULL DEFAULT 'customer',
    phone         VARCHAR(30)         NULL,
    status        ENUM('active','suspended') NOT NULL DEFAULT 'active',
    created_at    TIMESTAMP           DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 2. SELLER PROFILE  (extra info only a Seller needs — location etc.)
-- ---------------------------------------------------------
CREATE TABLE seller_profiles (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT NOT NULL,
    shop_name     VARCHAR(150) NULL,
    location_text VARCHAR(255) NULL,        -- e.g. "Kimihurura, Gasabo, Kigali"
    latitude      DECIMAL(10,7) NULL,
    longitude     DECIMAL(10,7) NULL,
    UNIQUE KEY uniq_user (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 3. PRODUCTS  (entered by Seller, approved by Admin)
-- ---------------------------------------------------------
CREATE TABLE products (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    seller_id     INT NOT NULL,
    name          VARCHAR(150) NOT NULL,
    description   TEXT NULL,
    price         DECIMAL(12,2) NOT NULL,
    image_path    VARCHAR(255) NULL,
    status        ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 4. PRODUCT DOCUMENTS  (manuals/spec sheets a Seller uploads, Customer downloads)
-- ---------------------------------------------------------
CREATE TABLE product_documents (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    product_id    INT NOT NULL,
    file_name     VARCHAR(255) NOT NULL,
    file_path     VARCHAR(255) NOT NULL,
    uploaded_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 5. ORDERS  (entered by Customer)
-- ---------------------------------------------------------
CREATE TABLE orders (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    customer_id   INT NOT NULL,
    total_amount  DECIMAL(12,2) NOT NULL,
    status        ENUM('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    order_id      INT NOT NULL,
    product_id    INT NOT NULL,
    quantity      INT NOT NULL DEFAULT 1,
    unit_price    DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 6. COMMENTS  (Customer commenting on product quality)
-- ---------------------------------------------------------
CREATE TABLE comments (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    product_id    INT NOT NULL,
    customer_id   INT NOT NULL,
    rating        TINYINT NOT NULL DEFAULT 5,   -- 1 to 5
    comment_text  TEXT NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 7. NEWSLETTER SUBSCRIBERS  (Join Our Community page)
-- ---------------------------------------------------------
CREATE TABLE newsletter_subscribers (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(100) NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- 8. CONTACT MESSAGES  (Support / Contact form)
-- ---------------------------------------------------------
CREATE TABLE contact_messages (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL,
    message       TEXT NOT NULL,
    submitted_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read       TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- SEED DATA — one Admin account so you can log in immediately
-- email:    admin@accesstech.com
-- password: Admin@123   (already hashed below with password_hash/BCRYPT)
-- ---------------------------------------------------------
INSERT INTO users (full_name, email, password, role)
VALUES (
  'Site Administrator',
  'admin@accesstech.com',
  '$2y$10$tvMm6/3gwT1TzpLTJMRJtOxdr5aG1zsFWUDPxagQfHKpEIcLtXwhC', -- Admin@123
  'admin'
);