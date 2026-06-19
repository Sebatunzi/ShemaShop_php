<?php
require_once 'includes/functions.php';
$featured  = getFeaturedProducts(8);
$trending  = getTrendingProducts(8);
$categories = getCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ShemaShop — Shop Smarter, Live Better</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- ── HERO ───────────────────────────────────────────── -->
<section class="hero section-sm">
  <div class="container">
    <div class="hero-grid">
      <!-- Main hero card -->
      <div class="hero-main" style="border-radius: 20px 0 0 20px; min-height: 480px;">
        <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=900&q=80"
             alt="Summer Collection" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
        <div class="hero-main-content" style="border-radius: 20px 0 0 20px;">
          <span class="hero-eyebrow">🔥 New Arrivals 2025</span>
          <h1 class="hero-title">Color of<br>Summer<br>Outfit</h1>
          <p class="hero-sub">100+ collections to inspire your style this season.</p>
          <a href="products.php" class="btn btn-primary" style="width:fit-content">View Collections →</a>
        </div>
      </div>

      <!-- Side cards -->
      <div class="hero-side" style="border-radius:0 20px 20px 0; overflow:hidden;">
        <div class="hero-side-card">
          <img src="https://images.unsplash.com/photo-1571731956672-f2b94d7dd0cb?w=600&q=80" alt="Outdoor Active">
          <div class="hero-side-card-content">
            <h3>Outdoor Active</h3>
            <p>Built for movement</p>
          </div>
        </div>
        <div class="hero-side-card">
          <img src="https://images.unsplash.com/photo-1491553895911-0055eca6402d?w=600&q=80" alt="Casual Comfort">
          <div class="hero-side-card-content">
            <h3>Casual Comfort</h3>
            <p>Everyday essentials</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── CATEGORY STRIP ─────────────────────────────────── -->
<section class="section-sm">
  <div class="container">
    <div class="cat-strip">
      <a href="products.php" class="cat-pill active">🏠 All</a>
      <?php foreach ($categories as $cat): ?>
        <a href="products.php?cat=<?= $cat['id'] ?>" class="cat-pill">
          <?= clean($cat['name']) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── FEATURED PRODUCTS ──────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="section-head">
      <h2>Featured Products</h2>
      <a href="products.php" class="see-all">See all products →</a>
    </div>

    <div class="grid-4">
      <?php if (empty($featured)): ?>
        <?php for ($i = 0; $i < 8; $i++): ?>
          <div class="product-card" onclick="window.location='product-detail.php?id=<?= $i+1 ?>'">
            <div class="product-card-img">
              <img src="https://images.unsplash.com/photo-<?= ['1542291026','1523275335','1542291026','1553062407','1523275335','1542291026','1553062407','1523275335'][$i] ?>?w=400&q=80" alt="Product">
              <button class="wishlist-btn">♡</button>
              <?php if ($i < 2): ?><span class="badge badge-green product-badge">NEW</span><?php endif; ?>
            </div>
            <div class="product-card-body">
              <div class="product-cat">Electronics</div>
              <div class="product-name">Premium Product <?= $i+1 ?></div>
              <div class="product-seller">by ShemaShop Seller</div>
              <div class="product-footer">
                <div>
                  <span class="product-price">$<?= number_format(29.99 + $i*15, 2) ?></span>
                  <div class="product-stars">★★★★☆ (<?= 12+$i*7 ?>)</div>
                </div>
                <button class="product-add-btn" onclick="event.stopPropagation(); addToCart(<?= $i+1 ?>)">+</button>
              </div>
            </div>
          </div>
        <?php endfor; ?>
      <?php else: ?>
        <?php foreach ($featured as $p): ?>
          <div class="product-card" onclick="window.location='product-detail.php?id=<?= $p['id'] ?>'">
            <div class="product-card-img">
              <img src="<?= productImage($p['image']) ?>" alt="<?= clean($p['name']) ?>">
              <button class="wishlist-btn" onclick="event.stopPropagation()">♡</button>
              <?php if ($p['is_new'] ?? false): ?><span class="badge badge-green product-badge">NEW</span><?php endif; ?>
            </div>
            <div class="product-card-body">
              <div class="product-cat"><?= clean($p['category_name'] ?? '') ?></div>
              <div class="product-name"><?= clean($p['name']) ?></div>
              <div class="product-seller">by <?= clean($p['seller_name'] ?? '') ?></div>
              <div class="product-footer">
                <div>
                  <span class="product-price"><?= formatPrice($p['price']) ?></span>
                  <div class="product-stars">★★★★☆</div>
                </div>
                <button class="product-add-btn" onclick="event.stopPropagation(); addToCart(<?= $p['id'] ?>)">+</button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ── CASUAL INSPIRATIONS ────────────────────────────── -->
<section class="section" style="background:var(--gray-50);">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>Casual Inspirations</h2>
        <p class="text-gray mt-8">Our favourite combinations you can apply to your daily life.</p>
      </div>
      <a href="products.php" class="btn btn-outline btn-sm">Browse Inspirations</a>
    </div>
    <div class="inspiration-grid">
      <div class="inspiration-card inspiration-main">
        <img src="https://images.unsplash.com/photo-1529139574466-a303027614b7?w=700&q=80" alt="Say it with Style" style="height:380px;">
        <div class="inner">
          <h3>Say it with Style</h3>
          <p>Bold graphics, effortless comfort.</p>
          <a href="products.php?cat=1" class="browse-btn">Shop Now ↗</a>
        </div>
      </div>
      <div class="inspiration-card inspiration-sm">
        <img src="https://images.unsplash.com/photo-1552902865-b72c031ac5ea?w=500&q=80" alt="Funky Never Gets Old" style="height:380px;">
        <div class="inner">
          <h3>Funky Never Gets Old</h3>
          <p>Retro vibes, modern twist.</p>
          <a href="products.php?cat=2" class="browse-btn">Shop Now ↗</a>
        </div>
      </div>
      <div class="inspiration-card inspiration-sm">
        <img src="https://images.unsplash.com/photo-1485230895905-ec40ba36b9bc?w=500&q=80" alt="Minimal is More" style="height:380px;">
        <div class="inner">
          <h3>Minimal is More</h3>
          <p>Clean lines, timeless appeal.</p>
          <a href="products.php?cat=3" class="browse-btn">Shop Now ↗</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── TRENDING ───────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="section-head">
      <h2>Trending Now</h2>
      <a href="products.php?sort=trending" class="see-all">View all →</a>
    </div>
    <!-- Category tabs -->
    <div class="cat-strip mb-24" style="margin-bottom:24px">
      <button class="cat-pill active" onclick="filterTrend(this,'all')">All</button>
      <button class="cat-pill" onclick="filterTrend(this,'shorts')">Shorts</button>
      <button class="cat-pill" onclick="filterTrend(this,'shoes')">Shoes</button>
      <button class="cat-pill" onclick="filterTrend(this,'jackets')">Jackets</button>
      <button class="cat-pill" onclick="filterTrend(this,'hats')">Hats</button>
    </div>

    <div class="grid-4" id="trendingGrid">
      <?php
      $trendImgs = [
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80',
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80',
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80',
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80',
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80',
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80',
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80',
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80',
      ];
      $trendImgs = [
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80',
        'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=400&q=80',
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80',
        'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=400&q=80',
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80',
        'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=400&q=80',
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80',
        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80',
      ];
      $trendNames = ['Nike Air Max 270','Jordan Retro 1','Adidas Ultra Boost','New Balance 990','Puma RS-X','Reebok Classic','Converse Chuck 70','Vans Old Skool'];
      $prices = [129,180,160,175,95,85,70,65];
      if (empty($trending)):
        for ($i = 0; $i < 8; $i++): ?>
          <div class="product-card" onclick="window.location='product-detail.php?id=<?= $i+1 ?>'">
            <div class="product-card-img">
              <img src="<?= $trendImgs[$i] ?>" alt="<?= $trendNames[$i] ?>">
              <button class="wishlist-btn" onclick="event.stopPropagation()">♡</button>
            </div>
            <div class="product-card-body">
              <div class="product-cat">Footwear</div>
              <div class="product-name"><?= $trendNames[$i] ?></div>
              <div class="product-seller">by Verified Seller</div>
              <div class="product-footer">
                <div>
                  <span class="product-price">$<?= $prices[$i] ?></span>
                  <div class="product-stars">★★★★★ (<?= 45+$i*12 ?>)</div>
                </div>
                <button class="product-add-btn" onclick="event.stopPropagation(); addToCart(<?= $i+1 ?>)">+</button>
              </div>
            </div>
          </div>
        <?php endfor;
      else:
        foreach ($trending as $p): ?>
          <div class="product-card" onclick="window.location='product-detail.php?id=<?= $p['id'] ?>'">
            <div class="product-card-img">
              <img src="<?= productImage($p['image']) ?>" alt="<?= clean($p['name']) ?>">
              <button class="wishlist-btn" onclick="event.stopPropagation()">♡</button>
            </div>
            <div class="product-card-body">
              <div class="product-cat"><?= clean($p['category_name'] ?? '') ?></div>
              <div class="product-name"><?= clean($p['name']) ?></div>
              <div class="product-seller">by <?= clean($p['seller_name'] ?? '') ?></div>
              <div class="product-footer">
                <div>
                  <span class="product-price"><?= formatPrice($p['price']) ?></span>
                  <div class="product-stars">★★★★☆</div>
                </div>
                <button class="product-add-btn" onclick="event.stopPropagation(); addToCart(<?= $p['id'] ?>)">+</button>
              </div>
            </div>
          </div>
        <?php endforeach;
      endif; ?>
    </div>
  </div>
</section>

<!-- ── NEWSLETTER BANNER ──────────────────────────────── -->
<section class="section" style="background:var(--black);color:white;">
  <div class="container text-center">
    <h2 style="font-family:var(--font-display);font-size:36px;font-weight:800;margin-bottom:12px;">Get 15% Off Your First Order</h2>
    <p style="color:rgba(255,255,255,.65);margin-bottom:28px;font-size:16px;">Join 50,000+ smart shoppers — deals, drops, and style updates delivered weekly.</p>
    <form style="display:flex;gap:0;max-width:440px;margin:0 auto;" onsubmit="return subscribeNewsletter(event)">
      <input type="email" placeholder="your@email.com" style="flex:1;padding:14px 18px;border:none;border-radius:var(--radius-sm) 0 0 var(--radius-sm);font-size:14px;outline:none;">
      <button type="submit" class="btn btn-green" style="border-radius:0 var(--radius-sm) var(--radius-sm) 0;padding:14px 22px;">Subscribe</button>
    </form>
  </div>
</section>

<!-- ── FOOTER ─────────────────────────────────────────── -->
<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div style="font-family:var(--font-display);font-size:22px;font-weight:800;">🛍 Shema<strong>Shop</strong></div>
        <p>Your one-stop destination for quality products from trusted sellers worldwide. Discover, shop, and enjoy a smarter lifestyle.</p>
      </div>
      <div class="footer-col">
        <h4>Shop</h4>
        <a href="products.php">All Products</a>
        <a href="products.php?sort=new">New Arrivals</a>
        <a href="products.php?sort=trending">Trending</a>
        <a href="products.php?sale=1">Sale</a>
      </div>
      <div class="footer-col">
        <h4>Account</h4>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
        <a href="customer/my-orders.php">My Orders</a>
        <a href="customer/profile.php">Profile</a>
      </div>
      <div class="footer-col">
        <h4>Company</h4>
        <a href="about.php">About Us</a>
        <a href="support.php">Support</a>
        <a href="community.php">Community</a>
        <a href="#">Privacy Policy</a>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2025 ShemaShop. All rights reserved.</p>
      <div class="footer-socials">
        <a href="#">𝕏</a>
        <a href="#">📘</a>
        <a href="#">📸</a>
        <a href="#">▶</a>
      </div>
    </div>
  </div>
</footer>

<script>
function addToCart(productId) {
  fetch('cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `action=add&product_id=${productId}&qty=1`
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      const badge = document.querySelector('.cart-badge');
      if (badge) {
        badge.textContent = d.count;
      } else {
        const btn = document.querySelector('.cart-btn');
        const b = document.createElement('span');
        b.className = 'cart-badge';
        b.textContent = d.count;
        btn.appendChild(b);
      }
      showToast('Added to cart!');
    } else if (d.redirect) {
      window.location = d.redirect;
    }
  });
}

function showToast(msg) {
  const t = document.createElement('div');
  t.textContent = msg;
  t.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#0D0D0D;color:#fff;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;z-index:9999;animation:fadeInUp .3s ease';
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 2500);
}

function subscribeNewsletter(e) {
  e.preventDefault();
  showToast('🎉 You\'re subscribed! Check your inbox.');
  e.target.reset();
  return false;
}

function filterTrend(btn, type) {
  document.querySelectorAll('.cat-strip .cat-pill').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}
</script>

<style>
@keyframes fadeInUp { from { opacity:0; transform: translateX(-50%) translateY(10px); } to { opacity:1; transform: translateX(-50%) translateY(0); } }
</style>
</body>
</html>