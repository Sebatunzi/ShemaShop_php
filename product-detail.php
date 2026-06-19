<?php
require_once 'includes/functions.php';
$id = (int)($_GET['id'] ?? 0);

// Demo product if DB empty
$demo = [
  'id'=>$id,'name'=>'Nike Air Max 270 React','price'=>129.99,'old_price'=>159.99,
  'image'=>'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80',
  'description'=>'The Nike Air Max 270 React combines two of Nike\'s most innovative cushioning technologies. With a massive Air unit in the heel and React foam throughout, it delivers unbelievable comfort from the first wear. The bold upper design is inspired by the sport that started it all — running.',
  'category_name'=>'Footwear','seller_name'=>'Nike Official Store','seller_location'=>'New York, USA',
  'stock'=>24,'rating'=>4.5,'reviews'=>189,'featured'=>1
];

try {
  $product = $id ? (getProductById($id) ?: $demo) : $demo;
} catch (Exception $e) {
  $product = $demo;
}

try {
  $comments = getComments($id);
} catch (Exception $e) {
  $comments = [];
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
  if (!isLoggedIn()) { header('Location: login.php'); exit; }
  $qty = max(1, (int)$_POST['qty']);
  $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
  header('Location: cart.php'); exit;
}

// Handle comment submission
$commentMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
  if (!isLoggedIn()) { header('Location: login.php'); exit; }
  $text   = clean($_POST['comment_text'] ?? '');
  $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
  if (strlen($text) >= 5) {
    try {
      global $pdo;
      $stmt = $pdo->prepare("INSERT INTO comments (product_id, user_id, text, rating, created_at) VALUES (?,?,?,?,NOW())");
      $stmt->execute([$id, $_SESSION['user_id'], $text, $rating]);
      $commentMsg = 'success';
    } catch (Exception $e) { $commentMsg = 'error'; }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= clean($product['name']) ?> — ShemaShop</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="product-detail">
  <div class="breadcrumb" style="margin-bottom:24px;">
    <a href="index.php">Home</a> / <a href="products.php">Products</a> / <span><?= clean($product['name']) ?></span>
  </div>

  <div class="product-detail-grid">
    <!-- Images -->
    <div class="product-imgs">
      <div class="product-main-img">
        <img src="<?= $product['image'] ?? 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80' ?>" alt="<?= clean($product['name']) ?>" id="mainImg">
      </div>
      <div class="product-thumbs">
        <?php
        $thumbs = [
          $product['image'] ?? 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80',
          'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=400&q=80',
          'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=400&q=80',
        ];
        foreach ($thumbs as $i => $thumb): ?>
          <div class="product-thumb <?= $i === 0 ? 'active' : '' ?>" onclick="switchImage('<?= $thumb ?>', this)">
            <img src="<?= $thumb ?>" alt="View <?= $i+1 ?>">
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Info -->
    <div class="product-info">
      <span class="badge badge-green" style="margin-bottom:12px;">✓ In Stock (<?= $product['stock'] ?? 24 ?> left)</span>
      <h1><?= clean($product['name']) ?></h1>

      <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
        <div class="product-stars" style="font-size:16px;">
          <?= str_repeat('★', (int)($product['rating'] ?? 4)) ?><?= str_repeat('☆', 5 - (int)($product['rating'] ?? 4)) ?>
        </div>
        <span style="font-size:14px;color:var(--gray-400);"><?= number_format($product['rating'] ?? 4.5, 1) ?> (<?= number_format($product['reviews'] ?? 189) ?> reviews)</span>
      </div>

      <div class="product-price-big">
        <?= formatPrice($product['price']) ?>
        <?php if (!empty($product['old_price'])): ?>
          <span class="old"><?= formatPrice($product['old_price']) ?></span>
          <span class="badge badge-red" style="font-size:13px;vertical-align:middle;margin-left:8px;">
            -<?= round((1 - $product['price']/$product['old_price'])*100) ?>%
          </span>
        <?php endif; ?>
      </div>

      <p class="product-desc"><?= nl2br(clean($product['description'])) ?></p>

      <form method="POST">
        <div class="product-qty-row">
          <label style="font-size:14px;font-weight:600;">Quantity:</label>
          <div class="qty-ctrl">
            <button type="button" onclick="changeQty(-1)">−</button>
            <input type="number" name="qty" id="qtyInput" value="1" min="1" max="<?= $product['stock'] ?? 99 ?>">
            <button type="button" onclick="changeQty(1)">+</button>
          </div>
        </div>
        <div class="product-actions">
          <button type="submit" name="add_to_cart" class="btn btn-primary" style="flex:1;">🛒 Add to Cart</button>
          <button type="button" class="btn btn-outline" onclick="showToast('Added to wishlist!')">♡ Wishlist</button>
        </div>
      </form>

      <div class="product-meta">
        <p><strong>Category:</strong> <?= clean($product['category_name'] ?? '') ?></p>
        <p><strong>Seller:</strong> <?= clean($product['seller_name'] ?? '') ?></p>
        <p><strong>Ships from:</strong> <?= clean($product['seller_location'] ?? 'Worldwide') ?></p>
        <p><strong>SKU:</strong> ACT-<?= str_pad($id, 6, '0', STR_PAD_LEFT) ?></p>
      </div>

      <!-- Download button (for digital products) -->
      <div style="margin-top:20px;padding:16px;background:var(--gray-50);border-radius:var(--radius);border:1.5px solid var(--gray-200);">
        <div style="display:flex;align-items:center;gap:12px;">
          <span style="font-size:24px;">📄</span>
          <div>
            <div style="font-weight:600;font-size:14px;">Product Manual & Documents</div>
            <div style="font-size:12px;color:var(--gray-400);">Download specifications and user guide</div>
          </div>
          <a href="#" style="margin-left:auto;" class="btn btn-outline btn-sm" onclick="checkDownload(event)">↓ Download</a>
        </div>
      </div>
    </div>
  </div>

  <!-- COMMENTS SECTION -->
  <div class="comments-section">
    <h2 style="font-family:var(--font-display);font-size:22px;font-weight:800;margin-bottom:24px;">
      Customer Reviews
      <span style="font-size:14px;font-weight:400;color:var(--gray-400);margin-left:8px;">(<?= count($comments) ?> reviews)</span>
    </h2>

    <!-- Leave a review -->
    <?php if (isLoggedIn() && isCustomer()): ?>
      <?php if ($commentMsg === 'success'): ?>
        <div class="form-success">Your review has been posted!</div>
      <?php elseif ($commentMsg === 'error'): ?>
        <div class="form-error">Failed to post review. Please try again.</div>
      <?php endif; ?>

      <div class="card" style="margin-bottom:32px;">
        <div class="card-header"><strong style="font-size:14px;">Write a Review</strong></div>
        <div class="card-body">
          <form method="POST">
            <div class="form-group">
              <div class="form-label">Rating</div>
              <div style="display:flex;gap:8px;" id="starPicker">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                  <label style="cursor:pointer;font-size:24px;color:var(--orange);" title="<?= $i ?> stars">
                    <input type="radio" name="rating" value="<?= $i ?>" style="display:none;" <?= $i===5?'checked':'' ?>>
                    ★
                  </label>
                <?php endfor; ?>
              </div>
            </div>
            <div class="form-group">
              <textarea class="form-control" name="comment_text" placeholder="Share your experience with this product…" required style="min-height:100px;"></textarea>
            </div>
            <button type="submit" name="submit_comment" class="btn btn-primary">Post Review</button>
          </form>
        </div>
      </div>
    <?php elseif (!isLoggedIn()): ?>
      <div style="text-align:center;padding:24px;background:var(--gray-50);border-radius:var(--radius);margin-bottom:32px;">
        <p style="color:var(--gray-600);margin-bottom:12px;">Login to leave a review</p>
        <a href="login.php" class="btn btn-primary btn-sm">Login Now</a>
      </div>
    <?php endif; ?>

    <!-- Comments list -->
    <?php if (empty($comments)): ?>
      <div class="empty-state" style="padding:40px;">
        <div class="icon">💬</div>
        <h3>No reviews yet</h3>
        <p>Be the first to review this product!</p>
      </div>
    <?php else: ?>
      <?php foreach ($comments as $c): ?>
        <div class="comment-item">
          <div class="comment-avatar">
            <?php if (!empty($c['avatar'])): ?>
              <img src="<?= $c['avatar'] ?>" alt="<?= clean($c['author']) ?>">
            <?php else: ?>
              <?= strtoupper(substr($c['author'], 0, 2)) ?>
            <?php endif; ?>
          </div>
          <div class="comment-body">
            <div class="comment-stars"><?= str_repeat('★', $c['rating'] ?? 5) ?><?= str_repeat('☆', 5 - ($c['rating'] ?? 5)) ?></div>
            <div class="comment-author"><?= clean($c['author']) ?></div>
            <div class="comment-time"><?= timeAgo($c['created_at']) ?></div>
            <p class="comment-text"><?= nl2br(clean($c['text'])) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<footer class="footer" style="margin-top:64px;">
  <div class="container">
    <div class="footer-bottom">
      <p>© 2025 ShemaShop. All rights reserved.</p>
    </div>
  </div>
</footer>

<script>
function switchImage(src, el) {
  document.getElementById('mainImg').src = src;
  document.querySelectorAll('.product-thumb').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
}
function changeQty(delta) {
  const inp = document.getElementById('qtyInput');
  inp.value = Math.max(1, parseInt(inp.value) + delta);
}
function showToast(msg) {
  const t = document.createElement('div');
  t.textContent = msg;
  t.style.cssText='position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#0D0D0D;color:#fff;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;z-index:9999;';
  document.body.appendChild(t); setTimeout(()=>t.remove(), 2500);
}
function checkDownload(e) {
  e.preventDefault();
  <?php if (isLoggedIn()): ?>
    showToast('Download started!');
  <?php else: ?>
    if (confirm('Please login to download product documents.')) window.location = 'login.php';
  <?php endif; ?>
}
</script>
</body>
</html>