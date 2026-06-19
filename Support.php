<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Support — ShemaShop</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<section style="background:var(--gray-50);padding:64px 0;text-align:center;">
  <div class="container">
    <h1 style="font-family:var(--font-display);font-size:42px;font-weight:800;margin-bottom:12px;">How can we help?</h1>
    <p style="color:var(--gray-600);margin-bottom:28px;font-size:16px;">Search our help center or contact our team directly.</p>
    <div class="search-wrap" style="max-width:500px;margin:0 auto;">
      <input type="text" placeholder="Search for answers…" style="font-size:15px;">
      <button><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></button>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid-3" style="margin-bottom:48px;">
      <?php
      $channels = [
        ['💬','Live Chat','Available 9am–6pm (Mon–Fri)','Start Chat'],
        ['📧','Email Us','We reply within 24 hours','Send Email'],
        ['📞','Call Us','+1 (800) 123-4567','Call Now'],
      ];
      foreach ($channels as $c): ?>
        <div class="card" style="text-align:center;padding:32px;">
          <div style="font-size:40px;margin-bottom:16px;"><?= $c[0] ?></div>
          <h3 style="font-size:18px;font-weight:700;margin-bottom:6px;"><?= $c[1] ?></h3>
          <p style="font-size:14px;color:var(--gray-400);margin-bottom:20px;"><?= $c[2] ?></p>
          <button class="btn btn-outline btn-sm"><?= $c[3] ?></button>
        </div>
      <?php endforeach; ?>
    </div>

    <h2 style="font-family:var(--font-display);font-size:26px;font-weight:800;margin-bottom:24px;">Frequently Asked Questions</h2>
    <div style="display:flex;flex-direction:column;gap:12px;max-width:800px;">
      <?php
      $faqs = [
        ['How do I track my order?','Log into your account, go to "My Orders" and click on any order to see real-time tracking information.'],
        ['How can I return a product?','You can initiate a return within 30 days of delivery from your orders page. Most returns are free.'],
        ['How do I become a seller?','Create an account, select "Seller" as your role, complete your profile, and start uploading products.'],
        ['Is my payment information secure?','Yes. We use SSL encryption and never store raw card data. Payments are processed by our certified payment partners.'],
        ['How long does shipping take?','Standard shipping takes 3-7 business days. Express options (1-2 days) are available at checkout.'],
        ['Can I cancel my order?','Orders can be cancelled within 2 hours of placement. After that, please wait for delivery and initiate a return.'],
      ];
      foreach ($faqs as $i => $faq): ?>
        <div style="border:1.5px solid var(--gray-200);border-radius:var(--radius);overflow:hidden;">
          <button onclick="toggleFaq(<?= $i ?>)" style="width:100%;padding:18px 20px;background:none;border:none;cursor:pointer;display:flex;justify-content:space-between;align-items:center;font-size:15px;font-weight:600;text-align:left;">
            <?= $faq[0] ?>
            <span id="faqIcon<?= $i ?>">+</span>
          </button>
          <div id="faqBody<?= $i ?>" style="display:none;padding:0 20px 18px;font-size:14px;color:var(--gray-600);line-height:1.7;"><?= $faq[1] ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Contact form -->
<section class="section" style="background:var(--gray-50);">
  <div class="container">
    <div style="max-width:600px;margin:0 auto;">
      <h2 style="font-family:var(--font-display);font-size:26px;font-weight:800;margin-bottom:24px;text-align:center;">Still need help?</h2>
      <div class="card">
        <div class="card-body">
          <div class="form-group">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" placeholder="Your full name">
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" placeholder="your@email.com">
          </div>
          <div class="form-group">
            <label class="form-label">Subject</label>
            <select class="form-control">
              <option>Order issue</option><option>Returns & refunds</option>
              <option>Account problems</option><option>Seller support</option><option>Other</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Message</label>
            <textarea class="form-control" placeholder="Describe your issue in detail…"></textarea>
          </div>
          <button class="btn btn-primary btn-full" onclick="alert('Message sent! We\'ll get back to you within 24 hours.')">Send Message</button>
        </div>
      </div>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="container">
    <div class="footer-bottom"><p>© 2025 ShemaShop. All rights reserved.</p></div>
  </div>
</footer>

<script>
function toggleFaq(i) {
  const body = document.getElementById('faqBody'+i);
  const icon = document.getElementById('faqIcon'+i);
  const open = body.style.display !== 'none';
  body.style.display = open ? 'none' : 'block';
  icon.textContent = open ? '+' : '−';
}
</script>
</body>
</html>