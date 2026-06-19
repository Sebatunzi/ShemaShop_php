<?php
require_once '../includes/functions.php';
requireLogin('../login.php');
if (!isCustomer()) { header('Location: ../index.php'); exit; }

$u = currentUser();
global $pdo;

try {
    $orders = getOrdersByUser($u['id']);
} catch (Exception $e) {
    $orders = [];
}

// Demo fallback
if (empty($orders)) {
    $orders = [
        ['id'=>1,'total'=>136.49,'status'=>'delivered','payment_method'=>'card','shipping_city'=>'Nairobi','created_at'=>date('Y-m-d H:i:s', strtotime('-3 days'))],
        ['id'=>2,'total'=>89.00, 'status'=>'pending',  'payment_method'=>'cod', 'shipping_city'=>'Nairobi','created_at'=>date('Y-m-d H:i:s', strtotime('-1 day'))],
    ];
}

// Fetch items for expanded order
$expandId = isset($_GET['order']) ? (int)$_GET['order'] : null;
$expandItems = [];
if ($expandId) {
    try {
        $expandItems = getOrderItems($expandId);
    } catch (Exception $e) {
        $expandItems = [
            ['name'=>'Nike Air Max 270','qty'=>1,'price'=>129.99,'image'=>null],
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders — ShemaShop</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="page-header">
  <div class="container">
    <div class="breadcrumb">
      <a href="../index.php">Home</a> / My Orders
    </div>
    <h1>My Orders</h1>
  </div>
</div>

<div class="container" style="padding-bottom:64px;max-width:860px;">

  <?php if (empty($orders)): ?>
    <div class="empty-state">
      <div class="icon">📦</div>
      <h3>No orders yet</h3>
      <p style="margin-bottom:20px;">You haven't placed any orders. Start shopping!</p>
      <a href="../products.php" class="btn btn-primary">Browse Products</a>
    </div>
  <?php else: ?>

    <p style="font-size:14px;color:var(--gray-400);margin-bottom:24px;">
      <?= count($orders) ?> order<?= count($orders)>1?'s':'' ?> found
    </p>

    <?php foreach ($orders as $order): ?>
      <?php
        $statusColors = [
          'pending'    => 'badge-orange',
          'processing' => 'badge-green',
          'shipped'    => 'badge-green',
          'delivered'  => 'badge-gray',
          'cancelled'  => 'badge-red',
        ];
        $statusColor = $statusColors[$order['status']] ?? 'badge-gray';
        $isExpanded  = $expandId === (int)$order['id'];
      ?>
      <div class="card" style="margin-bottom:16px;">
        <div class="card-body" style="padding:20px 24px;">
          <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">

            <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
              <div>
                <div style="font-size:11px;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">Order</div>
                <div style="font-weight:700;font-size:16px;">#<?= str_pad($order['id'],5,'0',STR_PAD_LEFT) ?></div>
              </div>
              <div>
                <div style="font-size:11px;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">Date</div>
                <div style="font-size:14px;"><?= date('M j, Y', strtotime($order['created_at'])) ?></div>
              </div>
              <div>
                <div style="font-size:11px;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">Total</div>
                <div style="font-weight:700;font-size:16px;"><?= formatPrice($order['total']) ?></div>
              </div>
              <div>
                <div style="font-size:11px;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">Payment</div>
                <div style="font-size:13px;"><?= strtoupper($order['payment_method'] ?? 'CARD') ?></div>
              </div>
            </div>

            <div style="display:flex;align-items:center;gap:10px;">
              <span class="badge <?= $statusColor ?>" style="font-size:12px;padding:5px 12px;">
                <?= ucfirst($order['status']) ?>
              </span>
              <a href="?order=<?= $isExpanded ? 0 : $order['id'] ?>" class="btn btn-outline btn-sm">
                <?= $isExpanded ? 'Hide Items ↑' : 'View Items ↓' ?>
              </a>
            </div>
          </div>

          <!-- Status timeline -->
          <?php
            $steps  = ['pending','processing','shipped','delivered'];
            $curIdx = array_search($order['status'], $steps);
          ?>
          <?php if ($order['status'] !== 'cancelled'): ?>
          <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--gray-100);">
            <div style="display:flex;align-items:center;gap:0;position:relative;">
              <?php foreach ($steps as $i => $step): ?>
                <?php $done = ($curIdx !== false && $i <= $curIdx); ?>
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;position:relative;">
                  <?php if ($i < count($steps)-1): ?>
                    <div style="position:absolute;top:12px;left:50%;width:100%;height:2px;background:<?= ($done && $i<$curIdx)?'var(--green)':'var(--gray-200)' ?>;z-index:0;"></div>
                  <?php endif; ?>
                  <div style="width:24px;height:24px;border-radius:99px;background:<?= $done?'var(--green)':'var(--gray-200)' ?>;display:flex;align-items:center;justify-content:center;z-index:1;font-size:12px;color:white;font-weight:700;">
                    <?= $done ? '✓' : ($i+1) ?>
                  </div>
                  <div style="font-size:11px;margin-top:6px;color:<?= $done?'var(--green)':'var(--gray-400)' ?>;font-weight:<?= $done?'600':'400' ?>;text-align:center;">
                    <?= ucfirst($step) ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php else: ?>
            <div style="margin-top:16px;padding:10px 14px;background:#FFEBEE;border-radius:6px;font-size:13px;color:var(--red);">
              ✕ This order was cancelled.
            </div>
          <?php endif; ?>

          <!-- Expanded items -->
          <?php if ($isExpanded && !empty($expandItems)): ?>
          <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--gray-100);">
            <h4 style="font-size:14px;font-weight:700;margin-bottom:14px;">Items in this order</h4>
            <?php foreach ($expandItems as $item): ?>
              <div style="display:flex;align-items:center;gap:14px;padding:10px 0;border-bottom:1px solid var(--gray-100);">
                <div style="width:52px;height:52px;border-radius:8px;overflow:hidden;background:var(--gray-100);flex-shrink:0;">
                  <?php if (!empty($item['image'])): ?>
                    <img src="../<?= $item['image'] ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                  <?php else: ?>
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:20px;">📦</div>
                  <?php endif; ?>
                </div>
                <div style="flex:1;">
                  <div style="font-weight:600;font-size:14px;"><?= htmlspecialchars($item['name']) ?></div>
                  <div style="font-size:12px;color:var(--gray-400);">Qty: <?= $item['qty'] ?></div>
                </div>
                <div style="font-weight:700;"><?= formatPrice($item['price'] * $item['qty']) ?></div>
              </div>
            <?php endforeach; ?>
            <div style="text-align:right;margin-top:12px;">
              <a href="../products.php" class="btn btn-outline btn-sm">Buy Again →</a>
            </div>
          </div>
          <?php elseif ($isExpanded): ?>
          <div style="margin-top:16px;padding:12px;background:var(--gray-50);border-radius:8px;font-size:13px;color:var(--gray-400);text-align:center;">
            No item details available for this order.
          </div>
          <?php endif; ?>

        </div>
      </div>
    <?php endforeach; ?>

  <?php endif; ?>
</div>

<footer class="footer">
  <div class="container">
    <div class="footer-bottom">
      <p>© 2025 ShemaShop. All rights reserved.</p>
      <a href="../index.php" style="color:rgba(255,255,255,.5);font-size:13px;">← Back to Home</a>
    </div>
  </div>
</footer>
</body>
</html>