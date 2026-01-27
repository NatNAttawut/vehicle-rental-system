<?php
$title = "การจองของฉัน";
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/lib/supabase.php';
require_login();

$cust_id = (int)($_SESSION['cust_id'] ?? 0);
$url = SUPABASE_URL . "/rest/v1/booking?select=book_id,book_start,book_exp,book_status,cars(car_name,car_brand,car_model)&cust_id=eq.$cust_id&order=book_id.desc";
[$http, $resp, $err] = sb_request('GET', $url, sb_auth_headers_from_session());
$rows = json_decode($resp, true) ?: [];
?>
<h4 class="mb-3">การจองของฉัน</h4>
<?php if (isset($_GET['cancelled'])): ?>
<div class="alert alert-success">ยกเลิกการจองเรียบร้อย</div>
<?php endif; ?>

<table class="table table-bordered bg-white">
  <thead>
    <tr>
      <th>#</th>
      <th>รถ</th>
      <th>เริ่ม</th>
      <th>สิ้นสุด</th>
      <th>สถานะ</th>
      <th>จัดการ</th>
    </tr>
  </thead>
  <tbody>
    
    <?php foreach ($rows as $r): $car = $r['cars'] ?? []; ?>
      <tr>
        <td><?= (int)$r['book_id'] ?></td>
        <td><?= htmlspecialchars(($car['car_name'] ?? '-') . ' / ' . ($car['car_brand'] ?? '-') . ' ' . ($car['car_model'] ?? '')) ?></td>
        <td><?= htmlspecialchars($r['book_start']) ?></td>
        <td><?= htmlspecialchars($r['book_exp']) ?></td>
        <td><?= htmlspecialchars($r['book_status']) ?></td>
        <td>
  <div class="d-flex gap-2 flex-wrap">
    <!-- ปุ่มพิมพ์ใบยืนยัน -->
    <?php if (in_array($r['book_status'], ['pending','approved','active'], true)): ?>
      <a
        href="/booking_receipt.php?book_id=<?= (int)$r['book_id'] ?>"
        target="_blank"
        class="btn btn-outline-dark btn-sm"
      >
        🖨 พิมพ์
      </a>
    <?php endif; ?>

    <!-- ปุ่มยกเลิก -->
    <?php if (in_array($r['book_status'], ['pending','approved'], true)): ?>
      <form
        method="post"
        action="/booking_cancel.php"
        onsubmit="return confirm('ยืนยันยกเลิกการจอง?');"
      >
        <input type="hidden" name="book_id" value="<?= (int)$r['book_id'] ?>">
        <button class="btn btn-outline-danger btn-sm">ยกเลิก</button>
      </form>
    <?php endif; ?>

    <?php if (!in_array($r['book_status'], ['pending','approved','active'], true)): ?>
      <span class="text-muted">-</span>
    <?php endif; ?>
  </div>
</td>

      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
