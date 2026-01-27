<?php
require_once __DIR__ . '/lib/supabase.php';
require_login();

$title = "จองรถ";
require_once __DIR__ . '/partials/header.php';

$car_id = (int)($_GET['car_id'] ?? 0);
if ($car_id <= 0) {
  echo "<div class='alert alert-danger'>ไม่พบรหัสรถ</div>";
  require_once __DIR__ . '/partials/footer.php';
  exit;
}

// ดึงข้อมูลรถ (อ่านได้แบบ public)
$carUrl = SUPABASE_URL . "/rest/v1/cars?select=car_id,car_name,car_brand,car_model,car_status,car_img,car_price_per_day&car_id=eq.$car_id&limit=1";
[$hcar, $rcar, $ecar] = sb_request('GET', $carUrl, sb_anon_headers());
$carRows = json_decode($rcar, true) ?: [];
$car = $carRows[0] ?? null;

if (!$car) {
  echo "<div class='alert alert-danger'>ไม่พบข้อมูลรถ</div>";
  require_once __DIR__ . '/partials/footer.php';
  exit;
}

$pricePerDay = (float)($car['car_price_per_day'] ?? 0);
$carStatus   = $car['car_status'] ?? 'available';
?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h4 class="mb-3">จองรถ (Car ID: <?= (int)$car_id ?>)</h4>

          <div class="d-flex gap-3 mb-3">
            <?php if (!empty($car['car_img'])): ?>
              <img src="<?= htmlspecialchars($car['car_img']) ?>" style="width:180px;height:120px;object-fit:cover" class="rounded border">
            <?php endif; ?>
            <div>
              <div class="fw-bold"><?= htmlspecialchars($car['car_name'] ?? '-') ?></div>
              <div class="text-muted">
                <?= htmlspecialchars(($car['car_brand'] ?? '-') . ' / ' . ($car['car_model'] ?? '-')) ?>
              </div>
              <div class="mt-2">
                <span class="badge <?= ($carStatus==='available'?'text-bg-success':($carStatus==='maintenance'?'text-bg-warning':'text-bg-danger')) ?>">
                  <?= htmlspecialchars($carStatus) ?>
                </span>
              </div>
              <div class="mt-2">
                <div class="small text-muted">ราคา/วัน</div>
                <div class="fs-5 fw-bold"><?= number_format($pricePerDay, 2) ?> บาท</div>
              </div>
            </div>
          </div>

          <?php if (isset($_GET['err']) && $_GET['err']==='conflict'): ?>
            <div class="alert alert-warning">ช่วงวันดังกล่าวมีคนจองรถคันนี้แล้ว กรุณาเลือกวันใหม่</div>
          <?php endif; ?>

          <?php if ($carStatus !== 'available'): ?>
            <div class="alert alert-danger">รถคันนี้ไม่พร้อมให้จอง (<?= htmlspecialchars($carStatus) ?>)</div>
            <a class="btn btn-secondary" href="/cars.php">กลับหน้ารถทั้งหมด</a>
          <?php else: ?>

          <form method="post" action="/booking_store.php" id="bookingForm">
            <input type="hidden" name="car_id" value="<?= (int)$car_id ?>">

            <div class="mb-3">
              <label class="form-label">วันเริ่มจอง</label>
              <input type="date" class="form-control" name="book_start" id="book_start" required>
            </div>

            <div class="mb-3">
              <label class="form-label">วันสิ้นสุดจอง</label>
              <input type="date" class="form-control" name="book_exp" id="book_exp" required>
            </div>

            <div class="border rounded p-3 bg-light mb-3">
              <div class="d-flex justify-content-between">
                <div class="text-muted">จำนวนวัน</div>
                <div class="fw-bold"><span id="total_days">0</span> วัน</div>
              </div>
              <div class="d-flex justify-content-between">
                <div class="text-muted">ราคา/วัน</div>
                <div class="fw-bold"><?= number_format($pricePerDay, 2) ?> บาท</div>
              </div>
              <hr class="my-2">
              <div class="d-flex justify-content-between">
                <div class="text-muted">ยอดรวม</div>
                <div class="fs-5 fw-bold"><span id="total_amount">0.00</span> บาท</div>
              </div>
            </div>

            <button class="btn btn-primary w-100">บันทึกการจอง</button>
          </form>

          <script>
            (function(){
              const pricePerDay = <?= json_encode($pricePerDay) ?>;
              const startEl = document.getElementById('book_start');
              const endEl   = document.getElementById('book_exp');
              const daysEl  = document.getElementById('total_days');
              const amtEl   = document.getElementById('total_amount');

              function calc(){
                const s = startEl.value ? new Date(startEl.value) : null;
                const e = endEl.value ? new Date(endEl.value) : null;

                let days = 0;
                if (s && e && e >= s){
                  const diff = Math.round((e - s) / (1000*60*60*24));
                  days = diff + 1; // นับแบบรวมวันเริ่มด้วย (ขั้นต่ำ 1 วัน)
                }
                const amount = days * pricePerDay;

                daysEl.textContent = days;
                amtEl.textContent  = amount.toFixed(2);
              }

              startEl.addEventListener('change', calc);
              endEl.addEventListener('change', calc);
            })();
          </script>

          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
