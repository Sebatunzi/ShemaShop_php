<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/session.php';

// ── Sanitize ─────────────────────────────────────────────
function clean($val) {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

// ── Password ─────────────────────────────────────────────
function hashPassword($plain) { return password_hash($plain, PASSWORD_BCRYPT); }
function verifyPassword($plain, $hash) { return password_verify($plain, $hash); }

// ── Users ─────────────────────────────────────────────────
function getUserByEmail($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function getUserById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// ── Products ──────────────────────────────────────────────
function getProducts($limit = 20, $offset = 0, $category = null) {
    global $pdo;
    $sql = "SELECT p.*, u.name AS seller_name, c.name AS category_name
            FROM products p
            JOIN users u ON p.seller_id = u.id
            JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'active'";
    $params = [];
    if ($category) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category;
    }
    $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getProductById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, u.name AS seller_name, u.location AS seller_location,
                           c.name AS category_name
                           FROM products p
                           JOIN users u ON p.seller_id = u.id
                           JOIN categories c ON p.category_id = c.id
                           WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getProductsBySeller($seller_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name
                           FROM products p JOIN categories c ON p.category_id = c.id
                           WHERE p.seller_id = ? ORDER BY p.created_at DESC");
    $stmt->execute([$seller_id]);
    return $stmt->fetchAll();
}

function getFeaturedProducts($limit = 8) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, u.name AS seller_name, c.name AS category_name
                           FROM products p
                           JOIN users u ON p.seller_id = u.id
                           JOIN categories c ON p.category_id = c.id
                           WHERE p.status = 'active' AND p.featured = 1
                           ORDER BY p.created_at DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getTrendingProducts($limit = 8) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, u.name AS seller_name, c.name AS category_name,
                           COUNT(oi.id) AS order_count
                           FROM products p
                           JOIN users u ON p.seller_id = u.id
                           JOIN categories c ON p.category_id = c.id
                           LEFT JOIN order_items oi ON p.id = oi.product_id
                           WHERE p.status = 'active'
                           GROUP BY p.id ORDER BY order_count DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

// ── Categories ────────────────────────────────────────────
function getCategories() {
    global $pdo;
    return $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
}

// ── Cart ──────────────────────────────────────────────────
function getCartItems() {
    if (!isset($_SESSION['cart'])) return [];
    global $pdo;
    $ids = array_keys($_SESSION['cart']);
    if (!$ids) return [];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();
    foreach ($products as &$p) {
        $p['qty'] = $_SESSION['cart'][$p['id']];
        $p['subtotal'] = $p['price'] * $p['qty'];
    }
    return $products;
}

function cartTotal() {
    $items = getCartItems();
    return array_sum(array_column($items, 'subtotal'));
}

function cartCount() {
    if (!isset($_SESSION['cart'])) return 0;
    return array_sum($_SESSION['cart']);
}

// ── Comments ──────────────────────────────────────────────
function getComments($product_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT c.*, u.name AS author, u.avatar
                           FROM comments c JOIN users u ON c.user_id = u.id
                           WHERE c.product_id = ? ORDER BY c.created_at DESC");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll();
}

// ── Orders ────────────────────────────────────────────────
function getOrdersByUser($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function getOrderItems($order_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT oi.*, p.name, p.image FROM order_items oi
                           JOIN products p ON oi.product_id = p.id
                           WHERE oi.order_id = ?");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll();
}

// ── Format ────────────────────────────────────────────────
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60)      return 'just now';
    if ($diff < 3600)    return floor($diff/60) . 'm ago';
    if ($diff < 86400)   return floor($diff/3600) . 'h ago';
    return floor($diff/86400) . 'd ago';
}

function uploadFile($file, $folder = 'products') {
    $allowed = ['jpg','jpeg','png','webp','gif','pdf'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return false;
    $name = uniqid() . '_' . time() . '.' . $ext;
    $dest = __DIR__ . "/../uploads/$folder/$name";
    if (move_uploaded_file($file['tmp_name'], $dest)) return "uploads/$folder/$name";
    return false;
}

function productImage($img) {
    if (!$img) return 'https://placehold.co/400x400/f0f0f0/999?text=No+Image';
    if (str_starts_with($img, 'http')) return $img;

    $img = ltrim($img, '/');

    $subFolder = "Shema'sWeb";
    $diskPath  = $_SERVER['DOCUMENT_ROOT'] . '/' . $subFolder . '/' . $img;

    if (file_exists($diskPath)) {
        // URL-encode the folder name so apostrophe doesn't break HTML
        return '/' . rawurlencode($subFolder) . '/' . $img;
    }

    return 'https://placehold.co/400x400/f0f0f0/999?text=No+Image';
}