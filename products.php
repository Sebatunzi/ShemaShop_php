<?php
require_once 'includes/functions.php';
$categories = getCategories();
$cat    = isset($_GET['cat']) ? (int)$_GET['cat'] : null;
$q      = clean($_GET['q'] ?? '');
$sort   = clean($_GET['sort'] ?? 'new');
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 20;
$offset = ($page - 1) * $limit;

// Build dynamic query
global $pdo;
$sql    = "SELECT p.*, u.name AS seller_name, c.name AS category_name
           FROM products p
           JOIN users u ON p.seller_id = u.id
           JOIN categories c ON p.category_id = c.id
           WHERE p.status = 'active'";
$params = [];
if ($cat)  { $sql .= " AND p.category_id = ?"; $params[] = $cat; }
if ($q)    { $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; }
if ($sort === 'price_asc')  $sql .= " ORDER BY p.price ASC";
elseif ($sort === 'price_desc') $sql .= " ORDER BY p.price DESC";
else $sql .= " ORDER BY p.created_at DESC";
$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit; $params[] = $offset;

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch (Exception $e) {
    $products = [];
}
$activeCat = $cat ? array_filter($categories, fn($c) => $c['id'] == $cat) : null;
$activeCatName = $activeCat ? reset($activeCat)['name'] : 'All Products';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $activeCatName ?> — ShemaShop</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="page-header">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Home</a> / <span><?= $activeCatName ?></span>
    </div>
    <h1><?= $q ? 'Search: "' . $q . '"' : $activeCatName ?></h1>
  </div>
</div>

<div class="container" style="padding-bottom:64px;">
  <div style="display:grid;grid-template-columns:240px 1fr;gap:32px;align-items:start;">

    <!-- Sidebar Filters -->
    <aside>
      <div class="card" style="position:sticky;top:80px;">
        <div class="card-header">
          <strong style="font-size:14px;font-weight:700;">Filters</strong>
        </div>
        <div class="card-body" style="padding:16px;">
          <form method="GET">
            <?php if ($q): ?><input type="hidden" name="q" value="<?= $q ?>"><br><?php endif; ?>

            <div style="margin-bottom:20px;">
              <div class="form-label">Category</div>
              <div style="display:flex;flex-direction:column;gap:6px;">
                <a href="products.php<?= $q ? '?q='.$q : '' ?>" class="cat-pill <?= !$cat ? 'active' : '' ?>" style="border-radius:6px;justify-content:flex-start;">All</a>
                <?php foreach ($categories as $c): ?>
                  <a href="products.php?cat=<?= $c['id'] ?><?= $q ? '&q='.$q : '' ?>"
                     class="cat-pill <?= $cat == $c['id'] ? 'active' : '' ?>"
                     style="border-radius:6px;justify-content:flex-start;">
                    <?= clean($c['name']) ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>

            <div style="margin-bottom:20px;">
              <div class="form-label">Sort By</div>
              <select name="sort" class="form-control" onchange="this.form.submit()">
                <option value="new"        <?= $sort==='new'        ?'selected':'' ?>>Newest First</option>
                <option value="price_asc"  <?= $sort==='price_asc'  ?'selected':'' ?>>Price: Low → High</option>
                <option value="price_desc" <?= $sort==='price_desc' ?'selected':'' ?>>Price: High → Low</option>
                <option value="trending"   <?= $sort==='trending'   ?'selected':'' ?>>Trending</option>
              </select>
              <?php if ($cat): ?><input type="hidden" name="cat" value="<?= $cat ?>"> <?php endif; ?>
            </div>

            <div>
              <div class="form-label">Price Range</div>
              <div style="display:flex;gap:8px;align-items:center;">
                <input type="number" name="min" placeholder="Min" value="<?= clean($_GET['min'] ?? '') ?>" class="form-control" style="padding:8px 10px;font-size:13px;">
                <span style="color:var(--gray-400);">–</span>
                <input type="number" name="max" placeholder="Max" value="<?= clean($_GET['max'] ?? '') ?>" class="form-control" style="padding:8px 10px;font-size:13px;">
              </div>
              <button type="submit" class="btn btn-primary btn-sm w-full mt-8" style="margin-top:10px;">Apply</button>
            </div>
          </form>
        </div>
      </div>
    </aside>

    <!-- Product Grid -->
    <main>
      <!-- Search bar -->
      <form method="GET" style="margin-bottom:24px;">
        <?php if ($cat): ?><input type="hidden" name="cat" value="<?= $cat ?>"><?php endif; ?>
        <div class="search-wrap">
          <input type="text" name="q" placeholder="Search products…" value="<?= $q ?>">
          <button type="submit">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          </button>
        </div>
      </form>

      <?php if (empty($products)): ?>
        <!-- Demo products when DB is empty -->
        <?php
        $demoProducts = [
          ['id'=>1,'name'=>'Nike Air Max 270','cat'=>'Footwear','price'=>129,'img'=>'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80','seller'=>'Nike Official'],
          ['id'=>2,'name'=>'Sony WH-1000XM5','cat'=>'Electronics','price'=>349,'img'=>'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80','seller'=>'Sony Store'],
          ['id'=>3,'name'=>'Levi\'s 501 Jeans','cat'=>'Clothing','price'=>89,'img'=>'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80','seller'=>'Levi\'s Shop'],
          ['id'=>4,'name'=>'Apple Watch S9','cat'=>'Electronics','price'=>399,'img'=>'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80','seller'=>'Apple Partner'],
          ['id'=>5,'name'=>'Adidas Ultraboost','cat'=>'Footwear','price'=>160,'img'=>'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=400&q=80','seller'=>'Adidas Store'],
          ['id'=>6,'name'=>'Samsung Galaxy Buds','cat'=>'Electronics','price'=>149,'img'=>'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=400&q=80','seller'=>'Samsung Hub'],
          ['id'=>7,'name'=>'Patagonia Jacket','cat'=>'Clothing','price'=>229,'img'=>'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=400&q=80','seller'=>'Patagonia'],
          ['id'=>8,'name'=>'New Balance 990v6','cat'=>'Footwear','price'=>175,'img'=>'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80','seller'=>'NB Store'],
        ];
        ?>
        <p style="font-size:13px;color:var(--gray-400);margin-bottom:20px;">Showing <?= count($demoProducts) ?> demo products</p>
        <div class="grid-4">
          <?php foreach ($demoProducts as $p): ?>
            <div class="product-card" onclick="window.location='product-detail.php?id=<?= $p['id'] ?>'">
              <div class="product-card-img">
                <img src="<?= $p['img'] ?>" alt="<?= $p['name'] ?>">
                <button class="wishlist-btn" onclick="event.stopPropagation()">♡</button>
              </div>
              <div class="product-card-body">
                <div class="product-cat"><?= $p['cat'] ?></div>
                <div class="product-name"><?= $p['name'] ?></div>
                <div class="product-seller">by <?= $p['seller'] ?></div>
                <div class="product-footer">
                  <div>
                    <span class="product-price">$<?= $p['price'] ?></span>
                    <div class="product-stars">★★★★☆</div>
                  </div>
                  <button class="product-add-btn" onclick="event.stopPropagation(); addToCart(<?= $p['id'] ?>)">+</button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p style="font-size:13px;color:var(--gray-400);margin-bottom:20px;"><?= count($products) ?> products found</p>
        <div class="grid-4">
          <?php foreach ($products as $p): ?>
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
          <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (count($products) === $limit): ?>
        <div style="display:flex;justify-content:center;gap:8px;margin-top:40px;">
          <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page'=>$page-1])) ?>" class="btn btn-outline btn-sm">← Prev</a>
          <?php endif; ?>
          <span style="padding:8px 16px;font-size:14px;color:var(--gray-600);">Page <?= $page ?></span>
          <a href="?<?= http_build_query(array_merge($_GET, ['page'=>$page+1])) ?>" class="btn btn-outline btn-sm">Next →</a>
        </div>
        <?php endif; ?>
      <?php endif; ?>
    </main>
  </div>
</div>

<footer class="footer">
  <div class="container">
    <div class="footer-bottom">
      <p>© 2025 ShemaShop. All rights reserved.</p>
      <a href="index.php" style="color:rgba(255,255,255,.5);font-size:13px;">← Back to Home</a>
    </div>
  </div>
</footer>

<script>
function addToCart(id) {
  fetch('cart.php', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`action=add&product_id=${id}&qty=1` })
  .then(r=>r.json()).then(d=>{
    if (d.success) {
      const b = document.querySelector('.cart-badge');
      if (b) b.textContent = d.count;
      showToast('Added to cart!');
    } else if (d.redirect) window.location = d.redirect;
  });
}
function showToast(msg) {
  const t = document.createElement('div');
  t.textContent = msg;
  t.style.cssText='position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#0D0D0D;color:#fff;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;z-index:9999;';
  document.body.appendChild(t); setTimeout(()=>t.remove(),2500);
}
</script>
</body>
</html>