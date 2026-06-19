<?php
require_once '../includes/functions.php';
requireRole('seller', '../login.php');
$u = currentUser();
global $pdo;

$success = $error = '';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $pdo->prepare("UPDATE products SET status='deleted' WHERE id=? AND seller_id=?")
            ->execute([(int)$_POST['delete_id'], $u['id']]);
        $success = 'Product removed successfully.';
    } catch (Exception $e) {
        $error = 'Could not delete product.';
    }
}

// Handle toggle active/inactive
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_id'])) {
    try {
        $pdo->prepare("UPDATE products SET status = IF(status='active','inactive','active') WHERE id=? AND seller_id=?")
            ->execute([(int)$_POST['toggle_id'], $u['id']]);
    } catch (Exception $e) {}
    header('Location: my-products.php'); exit;
}

try {
    $products = getProductsBySeller($u['id']);
} catch (Exception $e) {
    $products = [];
}

// Demo fallback
if (empty($products)) {
    $products = [
        ['id'=>1,'name'=>'Nike Air Max 270','category_name'=>'Footwear','price'=>129.99,'stock'=>24,'status'=>'active','featured'=>1,'created_at'=>date('Y-m-d'),'image'=>null],
        ['id'=>2,'name'=>'Adidas Ultraboost','category_name'=>'Footwear','price'=>160.00,'stock'=>12,'status'=>'active','featured'=>0,'created_at'=>date('Y-m-d'),'image'=>null],
        ['id'=>3,'name'=>'Patagonia Jacket','category_name'=>'Clothing','price'=>229.00,'stock'=>8, 'status'=>'inactive','featured'=>0,'created_at'=>date('Y-m-d'),'image'=>null],
    ];
}

$active   = array_filter($products, fn($p) => $p['status'] === 'active');
$inactive = array_filter($products, fn($p) => $p['status'] === 'inactive');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Products — Seller</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="dashboard">
  <aside class="sidebar">
    <div class="sidebar-logo">ShemaShop</div>
    <nav class="sidebar-nav">
      <a href="dashboard.php"><span class="icon"></span> Dashboard</a>
      <a href="my-products.php" class="active"><span class="icon"></span> My Products</a>
      <a href="upload-product.php"><span class="icon">➕</span> Add Product</a>
      <div class="section-label">Account</div>
      <a href="#"><span class="icon"></span> Profile</a>
      <a href="#"><span class="icon"></span> Earnings</a>
      <div style="margin-top:auto;border-top:1px solid rgba(255,255,255,.1);padding-top:16px;">
        <a href="../index.php"><span class="icon"></span> View Store</a>
        <a href="../logout.php"><span class="icon"></span> Sign Out</a>
      </div>
    </nav>
  </aside>

  <main class="dashboard-main">
    <div class="dash-topbar">
      <div>
        <h2 style="font-family:var(--font-display);font-size:20px;font-weight:800;">My Products</h2>
        <p style="font-size:13px;color:var(--gray-400);"><?= count($products) ?> total · <?= count($active) ?> active · <?= count($inactive) ?> inactive</p>
      </div>
      <a href="upload-product.php" class="btn btn-primary btn-sm">+ Add New Product</a>
    </div>

    <div class="dash-content">

      <?php if ($success): ?><div class="form-success"><?= $success ?></div><?php endif; ?>
      <?php if ($error):   ?><div class="form-error"><?= $error ?></div><?php endif; ?>

      <!-- Search bar -->
      <div style="margin-bottom:20px;">
        <div class="search-wrap" style="max-width:360px;">
          <input type="text" id="searchInput" placeholder="Filter products…" oninput="filterProducts(this.value)">
          <button type="button">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          </button>
        </div>
      </div>

      <?php if (empty($products)): ?>
        <div class="empty-state" style="padding:60px;">
          <div class="icon">📦</div>
          <h3>No products yet</h3>
          <p style="margin-bottom:20px;">Upload your first product and start selling!</p>
          <a href="upload-product.php" class="btn btn-primary">Upload Product</a>
        </div>

      <?php else: ?>
        <div class="table-wrap" id="productTable">
          <table>
            <thead>
              <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Added</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $p): ?>
                <tr class="product-row">
                  <td>
                    <div style="display:flex;align-items:center;gap:12px;">
                      <div style="width:48px;height:48px;border-radius:8px;overflow:hidden;background:var(--gray-100);flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                        <?php if (!empty($p['image'])): ?>
                          <img src="../<?= $p['image'] ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                        <?php else: ?>
                          <span style="font-size:20px;">📦</span>
                        <?php endif; ?>
                      </div>
                      <div>
                        <div style="font-weight:600;font-size:14px;" class="product-name-cell"><?= clean($p['name']) ?></div>
                        <?php if ($p['featured']): ?>
                          <span class="badge badge-orange" style="font-size:10px;margin-top:3px;">★ Featured</span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </td>
                  <td style="font-size:13px;color:var(--gray-600);"><?= clean($p['category_name'] ?? '—') ?></td>
                  <td style="font-weight:700;"><?= formatPrice($p['price']) ?></td>
                  <td>
                    <span style="font-weight:600;color:<?= ($p['stock']??0) < 5 ? 'var(--red)' : 'var(--black)' ?>">
                      <?= $p['stock'] ?? 0 ?>
                    </span>
                    <?php if (($p['stock']??0) < 5): ?>
                      <span style="font-size:11px;color:var(--red);display:block;">Low stock!</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge <?= $p['status']==='active' ? 'badge-green' : 'badge-gray' ?>">
                      <?= ucfirst($p['status']) ?>
                    </span>
                  </td>
                  <td style="font-size:12px;color:var(--gray-400);"><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
                  <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                      <a href="../product-detail.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm" target="_blank">View</a>

                      <form method="POST" style="display:inline;">
                        <input type="hidden" name="toggle_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-sm" style="background:<?= $p['status']==='active'?'var(--orange)':'var(--green)' ?>;color:white;border:none;border-radius:6px;padding:6px 12px;cursor:pointer;font-size:12px;font-weight:600;">
                          <?= $p['status']==='active' ? 'Deactivate' : 'Activate' ?>
                        </button>
                      </form>

                      <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this product? This cannot be undone.')">
                        <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-sm" style="color:var(--red);border:1.5px solid var(--red);background:none;border-radius:6px;padding:6px 12px;cursor:pointer;font-size:12px;font-weight:600;">Delete</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Summary cards -->
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:24px;">
          <div class="card" style="padding:20px;text-align:center;">
            <div style="font-size:24px;font-weight:800;font-family:var(--font-display);"><?= count($active) ?></div>
            <div style="font-size:13px;color:var(--gray-400);">Active Listings</div>
          </div>
          <div class="card" style="padding:20px;text-align:center;">
            <div style="font-size:24px;font-weight:800;font-family:var(--font-display);"><?= array_sum(array_column($products,'stock')) ?></div>
            <div style="font-size:13px;color:var(--gray-400);">Total Stock Units</div>
          </div>
          <div class="card" style="padding:20px;text-align:center;">
            <div style="font-size:24px;font-weight:800;font-family:var(--font-display);"><?= formatPrice(array_sum(array_map(fn($p)=>$p['price']*($p['stock']??0),$products))) ?></div>
            <div style="font-size:13px;color:var(--gray-400);">Inventory Value</div>
          </div>
        </div>

      <?php endif; ?>
    </div>
  </main>
</div>

<script>
function filterProducts(q) {
  const rows = document.querySelectorAll('.product-row');
  q = q.toLowerCase();
  rows.forEach(row => {
    const name = row.querySelector('.product-name-cell')?.textContent.toLowerCase() || '';
    row.style.display = name.includes(q) ? '' : 'none';
  });
}
</script>
</body>
</html>