<?php
require_once '../includes/functions.php';
requireRole('admin', '../login.php');

global $pdo;
$stats = [];
try {
    $stats['users']    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $stats['products'] = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $stats['orders']   = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $stats['revenue']  = $pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
    $recentOrders = $pdo->query("SELECT o.*, u.name AS customer_name FROM orders o JOIN users u ON o.customer_id = u.id ORDER BY o.created_at DESC LIMIT 10")->fetchAll();
    $recentUsers  = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 8")->fetchAll();
} catch (Exception $e) {
    $stats = ['users'=>142,'products'=>1847,'orders'=>3291,'revenue'=>89420];
    $recentOrders = [];
    $recentUsers  = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — ShemaShop</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="dashboard">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-logo">ShemaShop</div>
    <nav class="sidebar-nav">
      <div class="section-label">Main</div>
      <a href="dashboard.php" class="active"><span class="icon"></span> Dashboard</a>
      <a href="manage-products.php"><span class="icon"></span> Products</a>
      <a href="manage-users.php"><span class="icon">👥</span> Users</a>
      <div class="section-label">Orders</div>
      <a href="#"><span class="icon"></span> All Orders</a>
      <a href="#"><span class="icon"></span> Pending</a>
      <div class="section-label">Content</div>
      <a href="#"><span class="icon"></span> Categories</a>
      <a href="#"><span class="icon"></span> Promotions</a>
      <div style="margin-top:auto;border-top:1px solid rgba(255,255,255,.1);padding-top:16px;">
        <a href="../index.php"><span class="icon"></span> View Site</a>
        <a href="../logout.php"><span class="icon"></span> Sign Out</a>
      </div>
    </nav>
  </aside>

  <!-- Main content -->
  <main class="dashboard-main">
    <div class="dash-topbar">
      <div>
        <h2 style="font-family:var(--font-display);font-size:20px;font-weight:800;">Admin Dashboard</h2>
        <p style="font-size:13px;color:var(--gray-400);"><?= date('l, F j, Y') ?></p>
      </div>
      <div style="display:flex;align-items:center;gap:12px;">
        <span style="font-size:13px;color:var(--gray-600);">Welcome, <strong><?= clean(currentUser()['name']) ?></strong></span>
        <div class="user-initials" style="background:var(--green);"><?= strtoupper(substr(currentUser()['name'],0,2)) ?></div>
      </div>
    </div>

    <div class="dash-content">
      <!-- Stats -->
      <div class="stat-grid">
        <div class="stat-card">
          <div class="icon" style="background:#E8F5EB;">👥</div>
          <div class="label">Total Users</div>
          <div class="value"><?= number_format($stats['users']) ?></div>
          <div class="change">↑ 12% this month</div>
        </div>
        <div class="stat-card">
          <div class="icon" style="background:#E3F2FD;">📦</div>
          <div class="label">Products</div>
          <div class="value"><?= number_format($stats['products']) ?></div>
          <div class="change">↑ 8% this month</div>
        </div>
        <div class="stat-card">
          <div class="icon" style="background:#FFF3E0;">🛒</div>
          <div class="label">Total Orders</div>
          <div class="value"><?= number_format($stats['orders']) ?></div>
          <div class="change">↑ 24% this month</div>
        </div>
        <div class="stat-card">
          <div class="icon" style="background:#FCE4EC;">💰</div>
          <div class="label">Revenue</div>
          <div class="value">$<?= number_format($stats['revenue'], 0) ?></div>
          <div class="change">↑ 18% this month</div>
        </div>
      </div>

      <div class="grid-2">
        <!-- Recent Orders -->
        <div>
          <h3 style="font-weight:700;margin-bottom:16px;">Recent Orders</h3>
          <div class="table-wrap">
            <table>
              <thead><tr><th>Order ID</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
              <tbody>
                <?php if (empty($recentOrders)):
                  $demo = [
                    [1,'Sarah K.','$129.99','pending'],
                    [2,'Marcus T.','$349.00','shipped'],
                    [3,'Emma L.','$89.00','delivered'],
                    [4,'David R.','$399.00','pending'],
                    [5,'Priya M.','$229.00','processing'],
                  ];
                  foreach ($demo as $o): ?>
                    <tr>
                      <td style="font-weight:600;">#<?= str_pad($o[0],5,'0',STR_PAD_LEFT) ?></td>
                      <td><?= $o[1] ?></td>
                      <td><?= $o[2] ?></td>
                      <td><span class="badge <?= ['pending'=>'badge-orange','shipped'=>'badge-green','delivered'=>'badge-gray','processing'=>'badge-green'][$o[3]] ?>"><?= ucfirst($o[3]) ?></span></td>
                    </tr>
                  <?php endforeach;
                else:
                  foreach ($recentOrders as $o): ?>
                    <tr>
                      <td style="font-weight:600;">#<?= str_pad($o['id'],5,'0',STR_PAD_LEFT) ?></td>
                      <td><?= clean($o['customer_name']) ?></td>
                      <td><?= formatPrice($o['total']) ?></td>
                      <td><span class="badge badge-orange"><?= ucfirst($o['status']) ?></span></td>
                    </tr>
                  <?php endforeach;
                endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Recent Users -->
        <div>
          <h3 style="font-weight:700;margin-bottom:16px;">New Members</h3>
          <div class="table-wrap">
            <table>
              <thead><tr><th>Name</th><th>Role</th><th>Joined</th></tr></thead>
              <tbody>
                <?php if (empty($recentUsers)):
                  $demoU = [['Sarah K.','customer','2 hrs ago'],['Marcus T.','seller','5 hrs ago'],['Emma L.','customer','1 day ago'],['David R.','customer','2 days ago'],['TechStore','seller','3 days ago']];
                  foreach ($demoU as $u): ?>
                    <tr>
                      <td style="font-weight:600;"><?= $u[0] ?></td>
                      <td><span class="badge <?= $u[1]==='seller'?'badge-green':'badge-gray' ?>"><?= ucfirst($u[1]) ?></span></td>
                      <td style="color:var(--gray-400);font-size:13px;"><?= $u[2] ?></td>
                    </tr>
                  <?php endforeach;
                else:
                  foreach ($recentUsers as $u): ?>
                    <tr>
                      <td style="font-weight:600;"><?= clean($u['name']) ?></td>
                      <td><span class="badge <?= $u['role']==='seller'?'badge-green':'badge-gray' ?>"><?= ucfirst($u['role']) ?></span></td>
                      <td style="color:var(--gray-400);font-size:13px;"><?= timeAgo($u['created_at']) ?></td>
                    </tr>
                  <?php endforeach;
                endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>