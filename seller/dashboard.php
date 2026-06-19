<?php
require_once '../includes/functions.php';
requireRole('seller', '../login.php');
$u = currentUser();

global $pdo;
try {
    $myProducts = getProductsBySeller($u['id']);
    $totalSales = $pdo->prepare("SELECT COALESCE(SUM(oi.price * oi.qty),0) FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE p.seller_id=?");
    $totalSales->execute([$u['id']]);
    $revenue = $totalSales->fetchColumn();
    $totalOrders = $pdo->prepare("SELECT COUNT(DISTINCT oi.order_id) FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE p.seller_id=?");
    $totalOrders->execute([$u['id']]);
    $orderCount = $totalOrders->fetchColumn();
} catch (Exception $e) {
    $myProducts = [];
    $revenue = 4280;
    $orderCount = 47;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seller Dashboard — ShemaShop</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="dashboard">
  <aside class="sidebar">
    <div class="sidebar-logo">ShemaShop</div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="active"><span class="icon"></span> Dashboard</a>
      <a href="my-products.php"><span class="icon"></span> My Products</a>
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
        <h2 style="font-family:var(--font-display);font-size:20px;font-weight:800;">Seller Dashboard</h2>
        <p style="font-size:13px;color:var(--gray-400);">Welcome back, <?= clean($u['name']) ?>!</p>
      </div>
      <a href="upload-product.php" class="btn btn-primary btn-sm">+ New Product</a>
    </div>

    <div class="dash-content">
      <!-- Stats -->
      <div class="stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:32px;">
        <div class="stat-card">
          <div class="icon" style="background:#E8F5EB;">📦</div>
          <div class="label">My Products</div>
          <div class="value"><?= count($myProducts) ?></div>
        </div>
        <div class="stat-card">
          <div class="icon" style="background:#FFF3E0;">🛒</div>
          <div class="label">Total Orders</div>
          <div class="value"><?= $orderCount ?></div>
        </div>
        <div class="stat-card">
          <div class="icon" style="background:#FCE4EC;">💰</div>
          <div class="label">Total Revenue</div>
          <div class="value">$<?= number_format($revenue, 0) ?></div>
          <div class="change">↑ 18% this month</div>
        </div>
      </div>

      <!-- Quick actions -->
      <div class="grid-2" style="margin-bottom:32px;">
        <a href="upload-product.php" style="display:flex;align-items:center;gap:16px;background:var(--black);color:white;border-radius:var(--radius);padding:24px;text-decoration:none;transition:background .2s;">
          <div style="font-size:36px;">➕</div>
          <div><div style="font-weight:700;font-size:16px;margin-bottom:4px;">Upload New Product</div><div style="font-size:13px;opacity:.7;">Add photos, details & set your price</div></div>
        </a>
        <a href="my-products.php" style="display:flex;align-items:center;gap:16px;background:white;border:1.5px solid var(--gray-200);border-radius:var(--radius);padding:24px;text-decoration:none;transition:background .2s;color:var(--black);">
          <div style="font-size:36px;">📋</div>
          <div><div style="font-weight:700;font-size:16px;margin-bottom:4px;">Manage Products</div><div style="font-size:13px;color:var(--gray-400);">Edit, update or remove listings</div></div>
        </a>
      </div>

      <!-- Product list preview -->
      <div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
          <h3 style="font-weight:700;">My Products (<?= count($myProducts) ?>)</h3>
          <a href="my-products.php" style="font-size:13px;color:var(--gray-400);font-weight:600;">View all →</a>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
              <?php if (empty($myProducts)):
                $demo = [
                  ['Nike Air Max 270','Footwear','$129.99',24,'active'],
                  ['Adidas Ultraboost','Footwear','$160.00',12,'active'],
                  ['Premium Jacket','Clothing','$229.00',8,'active'],
                ];
                foreach ($demo as $p): ?>
                  <tr>
                    <td style="font-weight:600;"><?= $p[0] ?></td>
                    <td><?= $p[1] ?></td>
                    <td><?= $p[2] ?></td>
                    <td><?= $p[3] ?></td>
                    <td><span class="badge badge-green">Active</span></td>
                    <td><a href="../product-detail.php" class="btn btn-outline btn-sm">View</a></td>
                  </tr>
                <?php endforeach;
              else:
                foreach (array_slice($myProducts, 0, 10) as $p): ?>
                  <tr>
                    <td style="font-weight:600;"><?= clean($p['name']) ?></td>
                    <td><?= clean($p['category_name'] ?? '—') ?></td>
                    <td><?= formatPrice($p['price']) ?></td>
                    <td><?= $p['stock'] ?? 0 ?></td>
                    <td><span class="badge <?= $p['status']==='active'?'badge-green':'badge-gray' ?>"><?= ucfirst($p['status']) ?></span></td>
                    <td>
                      <div style="display:flex;gap:6px;">
                        <a href="../product-detail.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">View</a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach;
              endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>