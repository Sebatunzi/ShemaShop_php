<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us — ShemaShop</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<!-- Hero -->
<section style="background:var(--black);color:white;padding:80px 0;text-align:center;">
  <div class="container">
    <span class="badge badge-green" style="margin-bottom:16px;font-size:12px;">EST. 2020</span>
    <h1 style="font-family:var(--font-display);font-size:clamp(36px,5vw,60px);font-weight:800;margin-bottom:16px;">Redefining How<br>People Shop Online</h1>
    <p style="font-size:18px;color:rgba(255,255,255,.65);max-width:560px;margin:0 auto;">We connect buyers with trusted sellers worldwide — making quality products accessible to everyone, everywhere.</p>
  </div>
</section>

<!-- Mission -->
<section class="section">
  <div class="container">
    <div class="grid-2" style="gap:64px;align-items:center;">
      <div>
        <span style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--gray-400);">Our Mission</span>
        <h2 style="font-family:var(--font-display);font-size:36px;font-weight:800;margin:12px 0 20px;">Commerce Built on<br>Trust & Transparency</h2>
        <p style="color:var(--gray-600);line-height:1.8;margin-bottom:16px;">ShemaShop was founded with a simple belief: shopping online should feel as safe and personal as shopping in your favorite local store. We vet every seller, protect every transaction, and champion every buyer.</p>
        <p style="color:var(--gray-600);line-height:1.8;">From global brands to independent artisans — if it's on ShemaShop, it meets our quality and authenticity standards.</p>
        <div class="grid-2" style="margin-top:32px;gap:20px;">
          <?php
          $stats = [['50K+','Active Customers'],['5K+','Verified Sellers'],['200K+','Products Listed'],['99%','Satisfaction Rate']];
          foreach ($stats as $s): ?>
            <div>
              <div style="font-family:var(--font-display);font-size:32px;font-weight:800;"><?= $s[0] ?></div>
              <div style="font-size:13px;color:var(--gray-400);"><?= $s[1] ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div style="border-radius:var(--radius-lg);overflow:hidden;aspect-ratio:4/3;">
        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=700&q=80" alt="Team" style="width:100%;height:100%;object-fit:cover;">
      </div>
    </div>
  </div>
</section>

<!-- Values -->
<section class="section" style="background:var(--gray-50);">
  <div class="container">
    <div class="section-head"><h2>Our Values</h2></div>
    <div class="grid-3">
      <?php
      $vals = [
        ['🛡️','Trust First','Every seller is verified. Every product is authenticated. Every transaction is protected.'],
        ['🌍','Global Access','We believe great products should be available to everyone, no matter where you are.'],
        ['⚡','Speed & Simplicity','From discovery to delivery, we make every step of shopping effortless.'],
        ['💚','Sustainability','We prioritize eco-friendly sellers and carbon-neutral shipping wherever possible.'],
        ['🤝','Community','Our buyers and sellers aren\'t just users — they\'re the heart of ShemaShop.'],
        ['🔒','Privacy','Your data belongs to you. We never sell it, period.'],
      ];
      foreach ($vals as $v): ?>
        <div style="background:white;border-radius:var(--radius);padding:32px;border:1.5px solid var(--gray-100);">
          <div style="font-size:36px;margin-bottom:16px;"><?= $v[0] ?></div>
          <h3 style="font-size:18px;font-weight:700;margin-bottom:8px;"><?= $v[1] ?></h3>
          <p style="font-size:14px;color:var(--gray-600);line-height:1.7;"><?= $v[2] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="section" style="text-align:center;">
  <div class="container">
    <h2 style="font-family:var(--font-display);font-size:32px;font-weight:800;margin-bottom:12px;">Ready to Join ShemaShop?</h2>
    <p style="color:var(--gray-600);margin-bottom:28px;">Whether you're here to shop or sell — we'd love to have you.</p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
      <a href="register.php" class="btn btn-primary">Create Account →</a>
      <a href="products.php" class="btn btn-outline">Browse Products</a>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div style="font-family:var(--font-display);font-size:22px;font-weight:800;">🛍 Shema<strong>Shop</strong></div>
        <p>Your one-stop destination for quality products from trusted sellers worldwide.</p>
      </div>
      <div class="footer-col">
        <h4>Shop</h4>
        <a href="products.php">All Products</a>
        <a href="products.php?sort=new">New Arrivals</a>
      </div>
      <div class="footer-col">
        <h4>Company</h4>
        <a href="about.php">About Us</a>
        <a href="support.php">Support</a>
      </div>
      <div class="footer-col">
        <h4>Account</h4>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      </div>
    </div>
    <div class="footer-bottom"><p>© 2025 ShemaShop. All rights reserved.</p></div>
  </div>
</footer>
</body>
</html>