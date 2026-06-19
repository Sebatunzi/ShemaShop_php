<?php
require_once '../includes/functions.php';
requireRole('admin', '../login.php');

global $pdo;

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete' && isset($_POST['id'])) {
        try {
            $pdo->prepare("UPDATE products SET status='deleted' WHERE id=?")->execute([(int)$_POST['id']]);
        } catch (Exception $e) {}
    }
    if ($action === 'toggle_featured' && isset($_POST['id'])) {
        try {
            $pdo->prepare("UPDATE products SET featured = NOT featured WHERE id=?")->execute([(int)$_POST['id']]);
        } catch (Exception $e) {}
    }
    header('Location: manage-products.php'); exit;
}

try {
    $products = $pdo->query("SELECT p.*, u.name AS seller_name, c.name AS cat
                             FROM products p
                             LEFT JOIN users u ON p.seller_id = u.id
                             LEFT JOIN categories c ON p.category_id = c.id
                             WHERE p.status != 'deleted'
                             ORDER BY p.created_at DESC LIMIT 100")->fetchAll();
} catch (Exception $e) { $products = []; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Products — Admin</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="dashboard">
  <aside class="sidebar">
    <div class="sidebar-logo">ShemaShop</div>
    <nav class="sidebar-nav">
      <a href="dashboard.php"><span class="icon"></span> Dashboard</a>
      <a href="manage-products.php" class="active"><span class="icon"></span> Products</a>
      <a href="manage-users.php"><span class="icon"></span> Users</a>
      <div style="margin-top:auto;border-top:1px solid rgba(255,255,255,.1);padding-top:16px;">
        <a href="../index.php"><span class="icon"></span> View Site</a>
        <a href="../logout.php"><span class="icon"></span> Sign Out</a>
      </div>
    </nav>
  </aside>
  <main class="dashboard-main">
    <div class="dash-topbar">
      <h2 style="font-family:var(--font-display);font-size:20px;font-weight:800;">Manage Products</h2>
      <span class="badge badge-gray"><?= count($products) ?> total</span>
    </div>
    <div class="dash-content">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Product</th>
              <th>Category</th>
              <th>Seller</th>
              <th>Price</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($products)):
              $demo = [
                ['Nike Air Max 270','Footwear','Nike Official','$129.99','active',1],
                ['Sony WH-1000XM5','Electronics','Sony Store','$349.00','active',1],
                ['Levi\'s 501 Jeans','Clothing','Levi\'s Shop','$89.00','active',0],
                ['Apple Watch S9','Electronics','Apple Partner','$399.00','active',1],
              ];
              foreach ($demo as $p): ?>
                <tr>
                  <td style="font-weight:600;"><?= $p[0] ?></td>
                  <td><?= $p[1] ?></td>
                  <td><?= $p[2] ?></td>
                  <td><?= $p[3] ?></td>
                  <td><span class="badge badge-green">Active</span></td>
                  <td>
                    <div style="display:flex;gap:8px;">
                      <button class="btn btn-outline btn-sm">Edit</button>
                      <button class="btn btn-sm" style="color:var(--red);border:1px solid var(--red);background:none;">Delete</button>
                    </div>
                  </td>
                </tr>
              <?php endforeach;
            else:
              foreach ($products as $p): ?>
                <tr>
                  <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                      <div style="width:40px;height:40px;border-radius:6px;overflow:hidden;background:var(--gray-100);flex-shrink:0;">
                        <?php if ($p['image']): ?><img src="../<?= $p['image'] ?>" alt="" style="width:100%;height:100%;object-fit:cover;"><?php endif; ?>
                      </div>
                      <div style="font-weight:600;font-size:14px;"><?= clean($p['name']) ?></div>
                    </div>
                  </td>
                  <td><?= clean($p['cat'] ?? '—') ?></td>
                  <td><?= clean($p['seller_name'] ?? '—') ?></td>
                  <td style="font-weight:600;"><?= formatPrice($p['price']) ?></td>
                  <td>
                    <span class="badge <?= $p['status']==='active'?'badge-green':'badge-gray' ?>"><?= ucfirst($p['status']) ?></span>
                    <?php if ($p['featured']): ?><span class="badge badge-orange" style="margin-left:4px;">★ Featured</span><?php endif; ?>
                  </td>
                  <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                      <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="toggle_featured">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-outline btn-sm"><?= $p['featured'] ? 'Unfeature' : 'Feature' ?></button>
                      </form>
                      <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this product?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-sm" style="color:var(--red);border:1px solid var(--red);background:none;border-radius:6px;padding:6px 12px;cursor:pointer;font-size:12px;font-weight:600;">Delete</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach;
            endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
</body>
</html>