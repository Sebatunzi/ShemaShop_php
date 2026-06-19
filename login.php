<?php
require_once 'includes/functions.php';
if (isLoggedIn()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if (!$email || !$pass) {
        $error = 'Please fill in all fields.';
    } else {
        try {
            $user = getUserByEmail($email);
            if ($user && verifyPassword($pass, $user['password'])) {
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_name']  = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role']       = $user['role'];
                $_SESSION['avatar']     = $user['avatar'] ?? '';
                $redir = match($user['role']) {
                    'admin'  => 'admin/dashboard.php',
                    'seller' => 'seller/dashboard.php',
                    default  => 'index.php',
                };
                header("Location: $redir"); exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (Exception $e) {
            $error = 'Login failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — ShemaShop</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="auth-page">
  <!-- Visual side -->
  <div class="auth-visual">
    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&q=80" alt="Shop">
    <div class="auth-visual-content">
      <h1>Shop Smarter,<br>Live Better.</h1>
      <p>Access thousands of products from trusted sellers worldwide.</p>
    </div>
  </div>

  <!-- Form side -->
  <div class="auth-form-wrap">
    <div class="auth-logo">🛍 Shema<strong>Shop</strong></div>
    <h2>Welcome back</h2>
    <p class="sub">Sign in to your account to continue shopping.</p>

    <?php if ($error): ?>
      <div class="form-error"><?= $error ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['registered'])): ?>
      <div class="form-success">Account created! Please sign in.</div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label class="form-label">Email address</label>
        <input type="email" name="email" class="form-control" placeholder="you@example.com"
               value="<?= clean($_POST['email'] ?? '') ?>" required autofocus>
      </div>
      <div class="form-group">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <label class="form-label">Password</label>
          <a href="#" style="font-size:12px;color:var(--gray-400);">Forgot password?</a>
        </div>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;">
        <input type="checkbox" id="rememberMe" name="remember" style="width:16px;height:16px;">
        <label for="rememberMe" style="font-size:13px;color:var(--gray-600);">Remember me for 30 days</label>
      </div>
      <button type="submit" class="btn btn-primary btn-full">Sign In →</button>
    </form>

    <!-- Quick demo logins -->
    <div style="margin-top:24px;padding:16px;background:var(--gray-50);border-radius:var(--radius);border:1.5px dashed var(--gray-200);">
      <p style="font-size:12px;font-weight:700;color:var(--gray-400);margin-bottom:10px;text-transform:uppercase;letter-spacing:.06em;">Demo Accounts</p>
      <div style="display:flex;flex-direction:column;gap:6px;">
        <button onclick="fillDemo('admin@accesstech.com','admin123')" class="btn btn-outline btn-sm">Login as Admin</button>
        <button onclick="fillDemo('seller@accesstech.com','seller123')" class="btn btn-outline btn-sm">Login as Seller</button>
        <button onclick="fillDemo('customer@accesstech.com','customer123')" class="btn btn-outline btn-sm">Login as Customer</button>
      </div>
    </div>

    <p class="auth-switch">Don't have an account? <a href="register.php">Create one →</a></p>
  </div>
</div>
<script>
function fillDemo(email, pass) {
  document.querySelector('input[name="email"]').value = email;
  document.querySelector('input[name="password"]').value = pass;
}
</script>
</body>
</html>