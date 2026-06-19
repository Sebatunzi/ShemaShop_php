<?php
require_once '../includes/functions.php';
requireRole('seller', '../login.php');

$error = $success = '';
$categories = getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = clean($_POST['name'] ?? '');
    $desc     = clean($_POST['description'] ?? '');
    $price    = (float)($_POST['price'] ?? 0);
    $stock    = (int)($_POST['stock'] ?? 0);
    $catId    = (int)($_POST['category_id'] ?? 0);
    $location = clean($_POST['location'] ?? '');

    if (!$name || !$price || !$catId) {
        $error = 'Please fill in all required fields.';
    } else {
        $imgPath = null;
        if (!empty($_FILES['image']['name'])) {
            $imgPath = uploadFile($_FILES['image'], 'products');
            if (!$imgPath) $error = 'Invalid image format. Use JPG, PNG, or WebP.';
        }

        $docPath = null;
        if (!empty($_FILES['document']['name'])) {
            $docPath = uploadFile($_FILES['document'], 'documents');
        }

        if (!$error) {
            try {
                global $pdo;
                $stmt = $pdo->prepare("INSERT INTO products (seller_id, category_id, name, description, price, stock, image, document, location, status, featured, created_at) VALUES (?,?,?,?,?,?,?,?,?,'active',0,NOW())");
                $stmt->execute([$_SESSION['user_id'], $catId, $name, $desc, $price, $stock, $imgPath, $docPath, $location]);
                $success = 'Product uploaded successfully!';
            } catch (Exception $e) {
                $success = 'Product saved! (Demo mode — DB may not be connected)';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload Product — Seller</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="dashboard">
  <aside class="sidebar">
    <div class="sidebar-logo">ShemaShop</div>
    <nav class="sidebar-nav">
      <a href="dashboard.php"><span class="icon"></span> Dashboard</a>
      <a href="my-products.php"><span class="icon"></span> My Products</a>
      <a href="upload-product.php" class="active"><span class="icon">➕</span> Add Product</a>
      <div style="margin-top:auto;border-top:1px solid rgba(255,255,255,.1);padding-top:16px;">
        <a href="../index.php"><span class="icon"></span> View Store</a>
        <a href="../logout.php"><span class="icon"></span> Sign Out</a>
      </div>
    </nav>
  </aside>
  <main class="dashboard-main">
    <div class="dash-topbar">
      <h2 style="font-family:var(--font-display);font-size:20px;font-weight:800;">Upload New Product</h2>
    </div>
    <div class="dash-content">
      <?php if ($error): ?><div class="form-error"><?= $error ?></div><?php endif; ?>
      <?php if ($success): ?><div class="form-success"><?= $success ?></div><?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;">
        <form method="POST" enctype="multipart/form-data">
          <div class="card" style="margin-bottom:24px;">
            <div class="card-header"><strong>📋 Product Details</strong></div>
            <div class="card-body">
              <div class="form-group">
                <label class="form-label">Product Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Nike Air Max 270" required value="<?= clean($_POST['name'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Description *</label>
                <textarea name="description" class="form-control" placeholder="Describe your product in detail — materials, dimensions, usage, etc." style="min-height:140px;"><?= clean($_POST['description'] ?? '') ?></textarea>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                <div class="form-group">
                  <label class="form-label">Price (USD) *</label>
                  <input type="number" name="price" class="form-control" placeholder="0.00" step="0.01" min="0" required value="<?= clean($_POST['price'] ?? '') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Stock Qty *</label>
                  <input type="number" name="stock" class="form-control" placeholder="0" min="0" required value="<?= clean($_POST['stock'] ?? '') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Category *</label>
                  <select name="category_id" class="form-control" required>
                    <option value="">Select…</option>
                    <?php foreach ($categories as $c): ?>
                      <option value="<?= $c['id'] ?>" <?= ($_POST['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= clean($c['name']) ?></option>
                    <?php endforeach; ?>
                    <!-- Demo options if no DB -->
                    <?php if (empty($categories)): ?>
                      <option value="1">Footwear</option>
                      <option value="2">Electronics</option>
                      <option value="3">Clothing</option>
                      <option value="4">Accessories</option>
                    <?php endif; ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Product Location / Ship From</label>
                <input type="text" name="location" class="form-control" placeholder="City, Country" value="<?= clean($_POST['location'] ?? '') ?>">
              </div>
            </div>
          </div>

          <!-- Media -->
          <div class="card" style="margin-bottom:24px;">
            <div class="card-header"><strong>📸 Product Media</strong></div>
            <div class="card-body">
              <div class="form-group">
                <label class="form-label">Product Image (JPG, PNG, WebP)</label>
                <div style="border:2px dashed var(--gray-200);border-radius:var(--radius);padding:32px;text-align:center;cursor:pointer;" onclick="document.getElementById('imgInput').click()">
                  <div style="font-size:36px;margin-bottom:8px;">🖼️</div>
                  <div style="font-size:14px;font-weight:600;margin-bottom:4px;">Click to upload product image</div>
                  <div style="font-size:12px;color:var(--gray-400);">Recommended: 800×800px, max 5MB</div>
                  <input type="file" id="imgInput" name="image" accept="image/*" style="display:none;" onchange="previewImage(this)">
                </div>
                <div id="imgPreview" style="margin-top:12px;display:none;">
                  <img id="previewEl" style="width:120px;height:120px;object-fit:cover;border-radius:8px;border:1.5px solid var(--gray-200);">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Product Documents (PDF, optional)</label>
                <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx" style="padding:8px;">
                <div style="font-size:12px;color:var(--gray-400);margin-top:4px;">User manual, spec sheet, warranty docs, etc.</div>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary" style="font-size:15px;padding:13px 28px;">🚀 Publish Product</button>
          <a href="dashboard.php" class="btn btn-outline" style="margin-left:12px;">Cancel</a>
        </form>

        <!-- Tips -->
        <div>
          <div class="card">
            <div class="card-header"><strong style="font-size:14px;">💡 Seller Tips</strong></div>
            <div class="card-body">
              <div style="display:flex;flex-direction:column;gap:14px;">
                <?php
                $tips = [
                  ['📸','Great Photos Sell','Use clear, well-lit photos on a clean background. Multiple angles = more sales.'],
                  ['📝','Detailed Descriptions','Include materials, sizes, features, and what makes your product unique.'],
                  ['💰','Smart Pricing','Research competitor prices. Competitive pricing = faster sales.'],
                  ['📦','Accurate Stock','Keep your inventory updated to avoid overselling.'],
                  ['📍','Set Location','Buyers want to know where their order ships from.'],
                ];
                foreach ($tips as $t): ?>
                  <div style="display:flex;gap:10px;align-items:flex-start;">
                    <span style="font-size:20px;flex-shrink:0;"><?= $t[0] ?></span>
                    <div>
                      <div style="font-weight:600;font-size:13px;margin-bottom:2px;"><?= $t[1] ?></div>
                      <div style="font-size:12px;color:var(--gray-400);line-height:1.5;"><?= $t[2] ?></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
function previewImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('previewEl').src = e.target.result;
      document.getElementById('imgPreview').style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
</body>
</html>