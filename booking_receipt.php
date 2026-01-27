<?php
$title = "ใบยืนยันการจอง";
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/lib/supabase.php';
?>

<style>
/* ===== Print layout ===== */
@media print {

  /* ซ่อนสิ่งที่ไม่ต้องพิมพ์ */
  header, nav, .top-bar, .navbar, .no-print {
    display: none !important;
  }

  body {
    margin: 0;
    padding: 0;
    background: #fff !important;
  }

  /* ครอบกลางหน้ากระดาษ */
  .print-wrapper {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    width: 100%;
  }

  /* กระดาษ */
  .print-paper {
    width: 210mm;              /* A4 */
    max-width: 210mm;
    padding: 20mm;
    box-sizing: border-box;
    margin: 0 auto;
  }

  /* กันตัดหน้าแปลก ๆ */
  .print-paper * {
    page-break-inside: avoid;
  }
}
</style>



<?php
require_login();

$book_id = (int)($_GET['book_id'] ?? 0);
if ($book_id <= 0) {
  echo "<div class='alert alert-danger'>ไม่พบเลขที่การจอง</div>";
  require_once __DIR__ . '/partials/footer.php';
  exit;
}

// ดึง booking (ใช้ service เพื่อ join car ได้ง่าย และไม่ติด RLS ตอนแสดง)
$url = SUPABASE_URL . "/rest/v1/booking?select=book_id,car_id,cust_id,book_start,book_exp,book_status,price_per_day,total_days,total_amount,created_at&book_id=eq.$book_id&limit=1";
[$hb, $rb, $eb] = sb_request('GET', $url, sb_service_headers());
$rows = json_decode($rb, true) ?: [];
$b = $rows[0] ?? null;

if (!$b) {
  echo "<div class='alert alert-danger'>ไม่พบข้อมูลการจอง</div>";
  require_once __DIR__ . '/partials/footer.php';
  exit;
}

// ดึงข้อมูลรถ
$car_id = (int)($b['car_id'] ?? 0);
$carUrl = SUPABASE_URL . "/rest/v1/cars?select=car_name,car_brand,car_model,car_regis,car_num,car_img,car_price_per_day&car_id=eq.$car_id&limit=1";
[$hc, $rc, $ec] = sb_request('GET', $carUrl, sb_service_headers());
$crows = json_decode($rc, true) ?: [];
$c = $crows[0] ?? [];

// ดึงข้อมูลลูกค้า
$cust_id = (int)($b['cust_id'] ?? 0);
$cusUrl = SUPABASE_URL . "/rest/v1/customer?select=cust_name,cust_phone,cust_email&cust_id=eq.$cust_id&limit=1";
[$hcu, $rcu, $ecu] = sb_request('GET', $cusUrl, sb_service_headers());
$curows = json_decode($rcu, true) ?: [];
$u = $curows[0] ?? [];
?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">ใบยืนยันการจอง</h4>
    <div class="d-print-none d-flex gap-2">
      <button class="btn btn-dark" onclick="window.print()">พิมพ์เอกสาร</button>
      <a class="btn btn-outline-secondary" href="/my_bookings.php">กลับรายการจอง</a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-4">
      <div class="row">
        <div class="col-md-8">
          <div class="mb-2"><span class="text-muted">เลขที่การจอง:</span> <span class="fw-bold">#<?= (int)$b['book_id'] ?></span></div>
          <div class="mb-2"><span class="text-muted">วันที่จอง:</span> <?= htmlspecialchars($b['created_at'] ?? '-') ?></div>
          <hr>

          <h5 class="mb-2">ข้อมูลลูกค้า</h5>
          <div>ชื่อ: <span class="fw-bold"><?= htmlspecialchars($u['cust_name'] ?? '-') ?></span></div>
          <div>โทร: <?= htmlspecialchars($u['cust_phone'] ?? '-') ?></div>
          <div>อีเมล: <?= htmlspecialchars($u['cust_email'] ?? '-') ?></div>

          <hr>

          <h5 class="mb-2">ข้อมูลรถ</h5>
          <div>ชื่อรถ: <span class="fw-bold"><?= htmlspecialchars($c['car_name'] ?? '-') ?></span></div>
          <div>ยี่ห้อ/รุ่น: <?= htmlspecialchars(($c['car_brand'] ?? '-') . ' / ' . ($c['car_model'] ?? '-')) ?></div>
          <div>ทะเบียน: <?= htmlspecialchars($c['car_regis'] ?? '-') ?></div>
          <div>หมายเลขรถ: <?= htmlspecialchars($c['car_num'] ?? '-') ?></div>

          <hr>

          <h5 class="mb-2">รายละเอียดค่าใช้จ่าย</h5>
          <div>วันเริ่ม: <span class="fw-bold"><?= htmlspecialchars($b['book_start'] ?? '-') ?></span></div>
          <div>วันสิ้นสุด: <span class="fw-bold"><?= htmlspecialchars($b['book_exp'] ?? '-') ?></span></div>
          <div>จำนวนวัน: <span class="fw-bold"><?= (int)($b['total_days'] ?? 0) ?></span> วัน</div>
          <div>ราคา/วัน: <span class="fw-bold"><?= number_format((float)($b['price_per_day'] ?? 0), 2) ?></span> บาท</div>
          <div class="fs-4 fw-bold mt-2">ยอดรวม: <?= number_format((float)($b['total_amount'] ?? 0), 2) ?> บาท</div>

          <div class="alert alert-info mt-3 mb-0">
            ให้นำเอกสารนี้ไปชำระเงินที่ร้าน/จุดบริการ พร้อมแจ้งเลขที่การจอง (#<?= (int)$b['book_id'] ?>)
          </div>
        </div>

        <div class="col-md-4">
          <?php if (!empty($c['car_img'])): ?>
            <img src="<?= htmlspecialchars($c['car_img']) ?>" class="img-fluid rounded border">
          <?php endif; ?>
          <div class="text-muted small mt-2 d-none d-print-block">
            (เอกสารนี้สร้างจากระบบ Car Rental)
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
