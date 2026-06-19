<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Community — ShemaShop</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<section style="background:var(--black);color:white;padding:72px 0;text-align:center;">
  <div class="container">
    <h1 style="font-family:var(--font-display);font-size:48px;font-weight:800;margin-bottom:12px;">The ShemaShop Community</h1>
    <p style="color:rgba(255,255,255,.65);font-size:17px;max-width:480px;margin:0 auto 28px;">Share reviews, discover styles, and connect with fellow shoppers and sellers.</p>
    <div style="display:flex;gap:32px;justify-content:center;flex-wrap:wrap;">
      <div style="text-align:center;"><div style="font-size:28px;font-weight:800;font-family:var(--font-display);">50K+</div><div style="font-size:13px;opacity:.6;">Members</div></div>
      <div style="text-align:center;"><div style="font-size:28px;font-weight:800;font-family:var(--font-display);">200K+</div><div style="font-size:13px;opacity:.6;">Reviews</div></div>
      <div style="text-align:center;"><div style="font-size:28px;font-weight:800;font-family:var(--font-display);">5K+</div><div style="font-size:13px;opacity:.6;">Sellers</div></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 340px;gap:32px;align-items:start;">
      <div>
        <div class="section-head"><h2>Latest Reviews</h2></div>
        <?php
        $reviews = [
          ['Sarah K.','Nike Air Max 270','⭐⭐⭐⭐⭐','Absolutely love these shoes! Super comfortable from day one and the quality is outstanding. Fast shipping too!','2 hours ago','https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=60&q=80'],
          ['Marcus T.','Sony WH-1000XM5','⭐⭐⭐⭐⭐','Best headphones I\'ve ever owned. The noise cancellation is unbelievable. Worth every penny.','5 hours ago','https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=60&q=80'],
          ['Emma L.','Levi\'s 501 Jeans','⭐⭐⭐⭐☆','Great fit and true to size. The quality is exactly what you\'d expect from Levi\'s. Will definitely buy again.','1 day ago','https://images.unsplash.com/photo-1494790108755-2616b612b786?w=60&q=80'],
          ['David R.','Apple Watch S9','⭐⭐⭐⭐⭐','Game changer for my workouts. Sleep tracking, health monitoring — this watch does it all.','2 days ago','https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&q=80'],
          ['Priya M.','Patagonia Jacket','⭐⭐⭐⭐⭐','Perfect for Autumn weather! Lightweight but warm. Sustainable brand I trust. Fast delivery.','3 days ago','https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=60&q=80'],
        ];
        foreach ($reviews as $r): ?>
          <div class="comment-item card" style="margin-bottom:16px;padding:20px;">
            <div class="comment-avatar"><img src="<?= $r[5] ?>" alt="<?= $r[0] ?>"></div>
            <div class="comment-body">
              <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <div class="comment-author"><?= $r[0] ?></div>
                <span class="badge badge-gray" style="font-size:11px;"><?= $r[1] ?></span>
              </div>
              <div class="comment-stars" style="margin-top:4px;"><?= $r[2] ?></div>
              <p class="comment-text"><?= $r[3] ?></p>
              <div class="comment-time" style="margin-top:8px;"><?= $r[4] ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Sidebar -->
      <div>
        <!-- Top sellers -->
        <div class="card" style="margin-bottom:20px;">
          <div class="card-header"><strong style="font-size:14px;">🏆 Top Sellers This Month</strong></div>
          <div class="card-body" style="padding:12px 16px;">
            <?php
            $sellers = [
              ['Nike Official','234 sales','🥇'],
              ['Sony Store','198 sales','🥈'],
              ['Apple Partner','176 sales','🥉'],
              ['Adidas Store','154 sales','4️⃣'],
              ['Samsung Hub','132 sales','5️⃣'],
            ];
            foreach ($sellers as $i => $s): ?>
              <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:<?= $i < count($sellers)-1 ? '1px solid var(--gray-100)' : 'none' ?>;">
                <span style="font-size:20px;"><?= $s[2] ?></span>
                <div style="flex:1;">
                  <div style="font-size:14px;font-weight:600;"><?= $s[0] ?></div>
                  <div style="font-size:12px;color:var(--gray-400);"><?= $s[1] ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Trending tags -->
        <div class="card">
          <div class="card-header"><strong style="font-size:14px;">🔥 Trending</strong></div>
          <div class="card-body">
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
              <?php
              $tags = ['#Sneakers','#SummerStyle','#Tech2025','#Streetwear','#Sustainable','#NewArrivals','#BestSeller','#SalePicks','#EcoFriendly','#MustHave'];
              foreach ($tags as $tag): ?>
                <a href="products.php?q=<?= urlencode($tag) ?>" class="cat-pill" style="font-size:12px;padding:6px 12px;"><?= $tag ?></a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="container">
    <div class="footer-bottom"><p>© 2025 ShemaShop. All rights reserved.</p></div>
  </div>
</footer>
</body>
</html>