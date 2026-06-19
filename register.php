<?php
require_once 'includes/functions.php';
if (isLoggedIn()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = clean($_POST['name'] ?? '');
    $email    = clean($_POST['email'] ?? '');
    $pass     = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $role     = in_array($_POST['role'] ?? '', ['customer','seller']) ? $_POST['role'] : 'customer';
    $location = clean($_POST['location'] ?? '');

    if (!$name || !$email || !$pass) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($pass) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($pass !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            if (getUserByEmail($email)) {
                $error = 'An account with this email already exists.';
            } else {
                global $pdo;
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, location, created_at) VALUES (?,?,?,?,?,NOW())");
                $stmt->execute([$name, $email, hashPassword($pass), $role, $location]);
                header('Location: login.php?registered=1'); exit;
            }
        } catch (Exception $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account — ShemaShop</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-visual">
    <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=800&q=80" alt="Register">
    <div class="auth-visual-content">
      <h1>Join the<br>ShemaShop<br>Community.</h1>
      <p>Buy, sell, and connect with thousands of shoppers and sellers.</p>
    </div>
  </div>

  <div class="auth-form-wrap" style="padding:40px 56px;overflow-y:auto;">
    <div class="auth-logo">🛍 Shema<strong>Shop</strong></div>
    <h2>Create your account</h2>
    <p class="sub">Start shopping or selling in minutes.</p>

    <?php if ($error): ?>
      <div class="form-error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <!-- Account type selector -->
      <div class="form-group">
        <label class="form-label">I want to…</label>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <label id="roleCustomer" style="border:1.5px solid var(--black);border-radius:var(--radius-sm);padding:14px;cursor:pointer;text-align:center;background:var(--black);color:white;transition:all .2s;">
            <input type="radio" name="role" value="customer" style="display:none;" checked onchange="updateRole()">
            <div style="font-size:22px;margin-bottom:4px;">🛒</div>
            <div style="font-size:13px;font-weight:600;">Shop / Buy</div>
            <div style="font-size:11px;opacity:.7;">Customer account</div>
          </label>
          <label id="roleSeller" style="border:1.5px solid var(--gray-200);border-radius:var(--radius-sm);padding:14px;cursor:pointer;text-align:center;transition:all .2s;">
            <input type="radio" name="role" value="seller" style="display:none;" onchange="updateRole()">
            <div style="font-size:22px;margin-bottom:4px;">🏪</div>
            <div style="font-size:13px;font-weight:600;">Sell Products</div>
            <div style="font-size:11px;color:var(--gray-400);">Seller account</div>
          </label>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Full Name *</label>
        <input type="text" name="name" class="form-control" placeholder="John Doe"
               value="<?= clean($_POST['name'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">Email Address *</label>
        <input type="email" name="email" class="form-control" placeholder="you@example.com"
               value="<?= clean($_POST['email'] ?? '') ?>" required>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="form-group">
          <label class="form-label">Password *</label>
          <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
        </div>
        <div class="form-group">
          <label class="form-label">Confirm Password *</label>
          <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
        </div>
      </div>
      <div class="form-group" id="locationGroup">
        <label class="form-label">Location (for sellers)</label>
        <input type="text" name="location" class="form-control" placeholder="City, Country"
               value="<?= clean($_POST['location'] ?? '') ?>">
      </div>
      <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:20px;">
        <input type="checkbox" id="terms" required style="margin-top:3px;width:16px;height:16px;">
        <label for="terms" style="font-size:13px;color:var(--gray-600);">I agree to the <a href="#" style="font-weight:600;color:var(--black);">Terms of Service</a> and <a href="#" style="font-weight:600;color:var(--black);">Privacy Policy</a></label>
      </div>
      <button type="submit" class="btn btn-primary btn-full">Create Account →</button>
    </form>

    <p class="auth-switch">Already have an account? <a href="login.php">Sign in →</a></p>
  </div>
</div>

<script>
function updateRole() {
  const isSeller = document.querySelector('input[name="role"][value="seller"]').checked;
  const cLabel   = document.getElementById('roleCustomer');
  const sLabel   = document.getElementById('roleSeller');
  if (isSeller) {
    sLabel.style.cssText = 'border:1.5px solid var(--black);border-radius:8px;padding:14px;cursor:pointer;text-align:center;background:var(--black);color:white;transition:all .2s;';
    cLabel.style.cssText = 'border:1.5px solid var(--gray-200);border-radius:8px;padding:14px;cursor:pointer;text-align:center;transition:all .2s;';
  } else {
    cLabel.style.cssText = 'border:1.5px solid var(--black);border-radius:8px;padding:14px;cursor:pointer;text-align:center;background:var(--black);color:white;transition:all .2s;';
    sLabel.style.cssText = 'border:1.5px solid var(--gray-200);border-radius:8px;padding:14px;cursor:pointer;text-align:center;transition:all .2s;';
  }
}
document.querySelectorAll('input[name="role"]').forEach(r => r.addEventListener('change', updateRole));
</script>
</body>
</html>