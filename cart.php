<?php
require_once 'includes/functions.php';

// Handle cart actions via AJAX/POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        if (!isLoggedIn()) { echo json_encode(['success'=>false,'redirect'=>'login.php']); exit; }
        $pid = (int)$_POST['product_id'];
        $qty = max(1,(int)($_POST['qty'] ?? 1));
        $_SESSION['cart'][$pid] = ($_SESSION['cart'][$pid] ?? 0) + $qty;
        echo json_encode(['success'=>true,'count'=>cartCount()]);
        exit;
    }
    if ($action === 'update') {
        $pid = (int)$_POST['product_id'];
        $qty = (int)$_POST['qty'];
        if ($qty <= 0) unset($_SESSION['cart'][$pid]);
        else $_SESSION['cart'][$pid] = $qty;
        header('Location: cart.php'); exit;
    }
    if ($action === 'remove') {
        $pid = (int)$_POST['product_id'];
        unset($_SESSION['cart'][$pid]);
        header('Location: cart.php'); exit;
    }
    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        header('Location: cart.php'); exit;
    }
}

$items = getCartItems();
$total = cartTotal();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cart — ShemaShop</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="page-header">
  <div class="container">
    <div class="breadcrumb"><a href="index.php">Home</a> / Cart</div>
    <h1>Shopping Cart <?php if (!empty($items)): ?><span style="font-size:18px;font-weight:400;color:var(--gray-400);">(<?= count($items) ?> items)</span><?php endif; ?></h1>
  </div>
</div>

<div class="cart-page">
  <!-- Items -->
  <div>
    <?php if (empty($items)): ?>
      <div class="empty-state">
        <div class="icon">🛒</div>
        <h3>Your cart is empty</h3>
        <p style="margin-bottom:20px;">Browse our products and add something you'll love.</p>
        <a href="products.php" class="btn btn-primary">Start Shopping</a>
      </div>
    <?php else: ?>
      <div class="cart-items">
        <?php foreach ($items as $item): ?>
          <div class="cart-item">
            <div class="cart-item-img">
              <img src="<?= productImage($item['image']) ?>" alt="<?= clean($item['name']) ?>">
            </div>
            <div class="cart-item-info">
              <div class="cart-item-name"><?= clean($item['name']) ?></div>
              <div style="font-size:13px;color:var(--gray-400);margin-top:2px;"><?= clean($item['category_name'] ?? 'Product') ?></div>
              <div class="cart-qty-ctrl">
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                  <button type="submit" name="qty" value="<?= $item['qty']-1 ?>">−</button>
                </form>
                <span><?= $item['qty'] ?></span>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                  <button type="submit" name="qty" value="<?= $item['qty']+1 ?>">+</button>
                </form>
              </div>
            </div>
            <div style="text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
              <div style="font-weight:700;font-size:16px;"><?= formatPrice($item['subtotal']) ?></div>
              <div style="font-size:12px;color:var(--gray-400);"><?= formatPrice($item['price']) ?> each</div>
              <form method="POST">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                <button type="submit" class="cart-remove">✕ Remove</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;">
        <a href="products.php" class="btn btn-outline btn-sm">← Continue Shopping</a>
        <form method="POST">
          <button type="submit" name="action" value="clear" class="btn btn-sm" style="color:var(--red);border:1.5px solid var(--red);background:none;border-radius:6px;padding:8px 16px;cursor:pointer;font-size:13px;font-weight:600;">Clear Cart</button>
        </form>
      </div>
    <?php endif; ?>
  </div>

  <!-- Summary -->
  <?php if (!empty($items)): ?>
  <div class="cart-summary">
    <h3>Order Summary</h3>
    <div class="summary-row">
      <span>Subtotal (<?= count($items) ?> items)</span>
      <span><?= formatPrice($total) ?></span>
    </div>
    <div class="summary-row">
      <span>Shipping</span>
      <span style="color:var(--green);font-weight:600;">FREE</span>
    </div>
    <div class="summary-row">
      <span>Tax (5%)</span>
      <span><?= formatPrice($total * 0.05) ?></span>
    </div>
    <div class="summary-row total">
      <span>Total</span>
      <span><?= formatPrice($total * 1.05) ?></span>
    </div>

    <!-- Coupon -->
    <div style="margin-top:20px;margin-bottom:20px;">
      <div class="search-wrap" style="border-color:var(--gray-200);">
        <input type="text" placeholder="Coupon code…" style="font-size:13px;">
        <button style="font-size:13px;padding:0 14px;">Apply</button>
      </div>
    </div>

    <a href="checkout.php" class="btn btn-primary btn-full" style="font-size:15px;padding:14px;">
      Proceed to Checkout →
    </a>

    <div style="display:flex;align-items:center;justify-content:center;gap:12px;margin-top:16px;">
      <span style="font-size:22px;">🔒</span>
      <span style="font-size:12px;color:var(--gray-400);">Secure checkout powered by SSL encryption</span>
    </div>

    <div style="display:flex;justify-content:center;gap:8px;margin-top:16px;">
      <span style="font-size:24px;">💳</span><span style="font-size:24px;">🏦</span><span style="font-size:24px;">📱</span>
    </div>
  </div>
  <?php endif; ?>
</div>

<footer class="footer" style="margin-top:64px;">
  <div class="container">
    <div class="footer-bottom">
      <p>© 2025 ShemaShop. All rights reserved.</p>
    </div>
  </div>
</footer>
</body>
</html>