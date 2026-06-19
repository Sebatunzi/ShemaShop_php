<?php
require_once __DIR__ . '/session.php';
$u = currentUser();
$cartCount = function_exists('cartCount') ? cartCount() : (array_sum($_SESSION['cart'] ?? []));
$root = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false
      || strpos($_SERVER['PHP_SELF'], '/seller/') !== false
      || strpos($_SERVER['PHP_SELF'], '/customer/') !== false) ? '../' : '';
?>
<nav class="navbar">
  <div class="nav-inner">
    <!-- Logo -->
    <a href="<?= $root ?>index.php" class="nav-logo">
      <span class="logo-icon">🛍</span>
      <span class="logo-text">Shema<strong>Shop</strong></span>
    </a>

    <!-- Search -->
    <form class="nav-search" action="<?= $root ?>products.php" method="GET">
      <div class="search-wrap">
        <input type="text" name="q" placeholder="Search products, brands…"
               value="<?= clean($_GET['q'] ?? '') ?>">
        <select name="cat">
          <option value="">All Categories</option>
          <?php foreach (getCategories() as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= clean($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></button>
      </div>
    </form>

    <!-- Actions -->
    <div class="nav-actions">
      <a href="<?= $root ?>cart.php" class="nav-icon-btn cart-btn">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        <?php if ($cartCount > 0): ?><span class="cart-badge"><?= $cartCount ?></span><?php endif; ?>
      </a>

      <?php if ($u['id']): ?>
        <div class="nav-user-menu">
          <button class="nav-user-btn" onclick="toggleUserMenu()">
            <?php if ($u['avatar']): ?>
              <img src="<?= $root . $u['avatar'] ?>" alt="<?= $u['name'] ?>" class="user-avatar-sm">
            <?php else: ?>
              <div class="user-initials"><?= strtoupper(substr($u['name'],0,2)) ?></div>
            <?php endif; ?>
            <span><?= clean($u['name']) ?></span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="user-dropdown" id="userDropdown">
            <?php if ($u['role'] === 'admin'): ?>
              <a href="<?= $root ?>admin/dashboard.php">Admin Panel</a>
            <?php elseif ($u['role'] === 'seller'): ?>
              <a href="<?= $root ?>seller/dashboard.php">Seller Dashboard</a>
              <a href="<?= $root ?>seller/my-products.php">My Products</a>
              <a href="<?= $root ?>seller/upload-product.php">Upload Product</a>
            <?php else: ?>
              <a href="<?= $root ?>customer/my-orders.php">My Orders</a>
              <a href="<?= $root ?>customer/profile.php">Profile</a>
            <?php endif; ?>
            <hr>
            <a href="<?= $root ?>logout.php" class="logout-link">Sign Out</a>
          </div>
        </div>
      <?php else: ?>
        <a href="<?= $root ?>login.php" class="btn-nav-login">Login</a>
        <a href="<?= $root ?>register.php" class="btn-nav-register">Register</a>
      <?php endif; ?>

      <button class="nav-hamburger" onclick="toggleMobileMenu()" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>

  <!-- Mobile menu -->
  <div class="mobile-menu" id="mobileMenu">
    <form action="<?= $root ?>products.php" method="GET">
      <input type="text" name="q" placeholder="Search…">
      <button type="submit">Search</button>
    </form>
    <a href="<?= $root ?>index.php">Home</a>
    <a href="<?= $root ?>products.php">Products</a>
    <a href="<?= $root ?>community.php">Community</a>
    <a href="<?= $root ?>about.php">About</a>
    <a href="<?= $root ?>support.php">Support</a>
    <?php if ($u['id']): ?>
      <a href="<?= $root ?>logout.php">Sign Out</a>
    <?php else: ?>
      <a href="<?= $root ?>login.php">Login</a>
      <a href="<?= $root ?>register.php">Register</a>
    <?php endif; ?>
  </div>
</nav>

<script>
function toggleUserMenu() {
  document.getElementById('userDropdown').classList.toggle('show');
}
function toggleMobileMenu() {
  document.getElementById('mobileMenu').classList.toggle('show');
}
document.addEventListener('click', e => {
  if (!e.target.closest('.nav-user-menu')) {
    const d = document.getElementById('userDropdown');
    if (d) d.classList.remove('show');
  }
});
</script>