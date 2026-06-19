<?php
require_once '../includes/functions.php';
requireRole('admin', '../login.php');

global $pdo;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete' && isset($_POST['id'])) {
        try {
            $pdo->prepare("UPDATE users SET status='banned' WHERE id=?")->execute([(int)$_POST['id']]);
        } catch (Exception $e) {}
    }
    header('Location: manage-users.php'); exit;
}

try {
    $users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
} catch (Exception $e) { $users = []; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users — Admin</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="dashboard">
  <aside class="sidebar">
    <div class="sidebar-logo">ShemShop</div>
    <nav class="sidebar-nav">
      <a href="dashboard.php"><span class="icon"></span> Dashboard</a>
      <a href="manage-products.php"><span class="icon"></span> Products</a>
      <a href="manage-users.php" class="active"><span class="icon">👥</span> Users</a>
      <div style="margin-top:auto;border-top:1px solid rgba(255,255,255,.1);padding-top:16px;">
        <a href="../index.php"><span class="icon"></span> View Site</a>
        <a href="../logout.php"><span class="icon"></span> Sign Out</a>
      </div>
    </nav>
  </aside>
  <main class="dashboard-main">
    <div class="dash-topbar">
      <h2 style="font-family:var(--font-display);font-size:20px;font-weight:800;">Manage Users</h2>
    </div>
    <div class="dash-content">
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>User</th><th>Email</th><th>Role</th><th>Location</th><th>Joined</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php if (empty($users)):
              $demo = [
                ['Sarah K.','sarah@email.com','customer','New York','2 hrs ago'],
                ['Marcus T.','marcus@email.com','seller','London','5 hrs ago'],
                ['Admin User','admin@accesstech.com','admin','System','—'],
              ];
              foreach ($demo as $u): ?>
                <tr>
                  <td><div style="display:flex;align-items:center;gap:8px;"><div class="user-initials"><?= strtoupper(substr($u[0],0,2)) ?></div><strong style="font-size:14px;"><?= $u[0] ?></strong></div></td>
                  <td style="font-size:13px;color:var(--gray-600);"><?= $u[1] ?></td>
                  <td><span class="badge <?= ['admin'=>'badge-red','seller'=>'badge-green','customer'=>'badge-gray'][$u[2]] ?>"><?= ucfirst($u[2]) ?></span></td>
                  <td><?= $u[3] ?></td>
                  <td><?= $u[4] ?></td>
                  <td><button class="btn btn-outline btn-sm">View</button></td>
                </tr>
              <?php endforeach;
            else:
              foreach ($users as $u): ?>
                <tr>
                  <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                      <div class="user-initials" style="<?= $u['role']==='admin'?'background:var(--red)':($u['role']==='seller'?'background:var(--green)':'') ?>">
                        <?= strtoupper(substr($u['name'],0,2)) ?>
                      </div>
                      <strong style="font-size:14px;"><?= clean($u['name']) ?></strong>
                    </div>
                  </td>
                  <td style="font-size:13px;color:var(--gray-600);"><?= clean($u['email']) ?></td>
                  <td><span class="badge <?= ['admin'=>'badge-red','seller'=>'badge-green','customer'=>'badge-gray'][$u['role']] ?? 'badge-gray' ?>"><?= ucfirst($u['role']) ?></span></td>
                  <td><?= clean($u['location'] ?? '—') ?></td>
                  <td style="font-size:12px;color:var(--gray-400);"><?= timeAgo($u['created_at']) ?></td>
                  <td>
                    <?php if ($u['role'] !== 'admin'): ?>
                      <form method="POST" style="display:inline;" onsubmit="return confirm('Ban this user?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button type="submit" class="btn btn-sm" style="color:var(--red);border:1px solid var(--red);background:none;border-radius:6px;padding:6px 12px;cursor:pointer;font-size:12px;font-weight:600;">Ban</button>
                      </form>
                    <?php endif; ?>
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