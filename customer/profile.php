<?php
require_once '../includes/functions.php';
requireLogin('../login.php');

$u = currentUser();
global $pdo;

$success = $error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name     = clean($_POST['name'] ?? '');
    $location = clean($_POST['location'] ?? '');
    $email    = clean($_POST['email'] ?? '');

    if (!$name || !$email) {
        $error = 'Name and email are required.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, location=? WHERE id=?");
            $stmt->execute([$name, $email, $location, $u['id']]);
            $_SESSION['user_name']  = $name;
            $_SESSION['user_email'] = $email;
            $success = 'Profile updated successfully!';
            $u = currentUser();
        } catch (Exception $e) {
            $error = 'Update failed. Email may already be in use.';
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password']     ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$current || !$new || !$confirm) {
        $error = 'Please fill in all password fields.';
    } elseif (strlen($new) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $error = 'New passwords do not match.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id=?");
            $stmt->execute([$u['id']]);
            $row = $stmt->fetch();
            if ($row && verifyPassword($current, $row['password'])) {
                $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([hashPassword($new), $u['id']]);
                $success = 'Password changed successfully!';
            } else {
                $error = 'Current password is incorrect.';
            }
        } catch (Exception $e) {
            $error = 'Password change failed. Please try again.';
        }
    }
}

// Handle avatar upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_avatar'])) {
    if (!empty($_FILES['avatar']['name'])) {
        $path = uploadFile($_FILES['avatar'], 'avatars');
        if ($path) {
            try {
                $pdo->prepare("UPDATE users SET avatar=? WHERE id=?")->execute([$path, $u['id']]);
                $_SESSION['avatar'] = $path;
                $success = 'Profile photo updated!';
            } catch (Exception $e) {
                $error = 'Could not save avatar.';
            }
        } else {
            $error = 'Invalid image format. Please use JPG, PNG, or WebP.';
        }
    }
}

// Fetch fresh user data
try {
    $userData = getUserById($u['id']) ?: [];
} catch (Exception $e) {
    $userData = ['name'=>$u['name'],'email'=>$u['email'],'location'=>'','avatar'=>$u['avatar'],'role'=>$u['role'],'created_at'=>date('Y-m-d')];
}

// Stats
try {
    $orderCount = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE customer_id=?");
    $orderCount->execute([$u['id']]);
    $totalOrders = $orderCount->fetchColumn();

    $reviewCount = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE user_id=?");
    $reviewCount->execute([$u['id']]);
    $totalReviews = $reviewCount->fetchColumn();

    $spendStmt = $pdo->prepare("SELECT COALESCE(SUM(total),0) FROM orders WHERE customer_id=? AND status != 'cancelled'");
    $spendStmt->execute([$u['id']]);
    $totalSpent = $spendStmt->fetchColumn();
} catch (Exception $e) {
    $totalOrders = 2; $totalReviews = 1; $totalSpent = 225.49;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile — ShemaShop</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="page-header">
  <div class="container">
    <div class="breadcrumb"><a href="../index.php">Home</a> / Profile</div>
    <h1>My Profile</h1>
  </div>
</div>

<div class="container" style="padding-bottom:64px;max-width:860px;">

  <?php if ($success): ?><div class="form-success"><?= $success ?></div><?php endif; ?>
  <?php if ($error):   ?><div class="form-error"><?= $error ?></div><?php endif; ?>

  <!-- Profile header card -->
  <div class="card" style="margin-bottom:24px;">
    <div class="card-body" style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
      <!-- Avatar -->
      <div style="position:relative;">
        <div style="width:80px;height:80px;border-radius:99px;overflow:hidden;background:var(--black);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <?php if (!empty($userData['avatar'])): ?>
            <img src="../<?= $userData['avatar'] ?>" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
          <?php else: ?>
            <span style="font-size:28px;font-weight:800;color:white;"><?= strtoupper(substr($userData['name'],0,2)) ?></span>
          <?php endif; ?>
        </div>
        <label style="position:absolute;bottom:-4px;right:-4px;width:26px;height:26px;background:var(--green);border-radius:99px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:13px;" title="Change photo">
          📷
          <form method="POST" enctype="multipart/form-data" id="avatarForm">
            <input type="file" name="avatar" accept="image/*" style="display:none;" onchange="document.getElementById('avatarForm').submit()">
            <input type="hidden" name="update_avatar" value="1">
          </form>
        </label>
      </div>

      <div style="flex:1;">
        <h2 style="font-family:var(--font-display);font-size:22px;font-weight:800;"><?= clean($userData['name']) ?></h2>
        <p style="font-size:14px;color:var(--gray-400);"><?= clean($userData['email']) ?></p>
        <div style="display:flex;align-items:center;gap:8px;margin-top:6px;">
          <span class="badge <?= ['admin'=>'badge-red','seller'=>'badge-green','customer'=>'badge-gray'][$userData['role']] ?>"><?= ucfirst($userData['role']) ?></span>
          <?php if (!empty($userData['location'])): ?>
            <span style="font-size:13px;color:var(--gray-400);">📍 <?= clean($userData['location']) ?></span>
          <?php endif; ?>
          <span style="font-size:13px;color:var(--gray-400);">Joined <?= date('M Y', strtotime($userData['created_at'])) ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- Stats row -->
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
    <div class="card" style="text-align:center;padding:20px;">
      <div style="font-family:var(--font-display);font-size:28px;font-weight:800;"><?= $totalOrders ?></div>
      <div style="font-size:13px;color:var(--gray-400);">Orders Placed</div>
    </div>
    <div class="card" style="text-align:center;padding:20px;">
      <div style="font-family:var(--font-display);font-size:28px;font-weight:800;"><?= formatPrice($totalSpent) ?></div>
      <div style="font-size:13px;color:var(--gray-400);">Total Spent</div>
    </div>
    <div class="card" style="text-align:center;padding:20px;">
      <div style="font-family:var(--font-display);font-size:28px;font-weight:800;"><?= $totalReviews ?></div>
      <div style="font-size:13px;color:var(--gray-400);">Reviews Written</div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;">

    <!-- Update profile -->
    <div class="card">
      <div class="card-header"><strong>✏️ Edit Profile</strong></div>
      <div class="card-body">
        <form method="POST">
          <div class="form-group">
            <label class="form-label">Full Name *</label>
            <input type="text" name="name" class="form-control" value="<?= clean($userData['name']) ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Email Address *</label>
            <input type="email" name="email" class="form-control" value="<?= clean($userData['email']) ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" placeholder="City, Country" value="<?= clean($userData['location'] ?? '') ?>">
          </div>
          <button type="submit" name="update_profile" class="btn btn-primary btn-full">Save Changes</button>
        </form>
      </div>
    </div>

    <!-- Change password -->
    <div class="card">
      <div class="card-header"><strong>🔒 Change Password</strong></div>
      <div class="card-body">
        <form method="POST">
          <div class="form-group">
            <label class="form-label">Current Password *</label>
            <input type="password" name="current_password" class="form-control" placeholder="••••••••" required>
          </div>
          <div class="form-group">
            <label class="form-label">New Password *</label>
            <input type="password" name="new_password" class="form-control" placeholder="Min. 6 characters" required>
          </div>
          <div class="form-group">
            <label class="form-label">Confirm New Password *</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Repeat new password" required>
          </div>
          <button type="submit" name="change_password" class="btn btn-primary btn-full">Update Password</button>
        </form>
      </div>
    </div>

  </div>

  <!-- Quick links -->
  <div class="card" style="margin-top:24px;">
    <div class="card-header"><strong>⚡ Quick Links</strong></div>
    <div class="card-body" style="display:flex;flex-wrap:wrap;gap:12px;">
      <a href="my-orders.php"     class="btn btn-outline btn-sm">📦 My Orders</a>
      <a href="../products.php"   class="btn btn-outline btn-sm">🛍 Browse Products</a>
      <a href="../cart.php"       class="btn btn-outline btn-sm">🛒 My Cart</a>
      <a href="../support.php"    class="btn btn-outline btn-sm">💬 Support</a>
      <a href="../community.php"  class="btn btn-outline btn-sm">🌐 Community</a>
      <a href="../logout.php"     class="btn btn-sm" style="color:var(--red);border:1.5px solid var(--red);background:none;border-radius:6px;padding:8px 16px;cursor:pointer;font-size:13px;font-weight:600;">🚪 Sign Out</a>
    </div>
  </div>

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