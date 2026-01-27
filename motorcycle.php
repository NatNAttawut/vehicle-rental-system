<?php
$title = "รถมอเตอร์ไซค์";
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/lib/supabase.php';

// เฉพาะมอเตอร์ไซค์เท่านั้น
$listUrl = SUPABASE_URL . "/rest/v1/cars?select=*&car_type=eq.motorcycle&order=car_id.desc";
[$http, $resp, $err] = sb_request('GET', $listUrl, sb_anon_headers());
$cars = json_decode($resp, true) ?: [];
?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h4 class="mb-0">รถมอเตอร์ไซค์</h4>
</div>

<div class="row g-3">
<?php foreach ($cars as $car):
  $img = $car['car_img'] ?? '';
  $price = (float)($car['car_price_per_day'] ?? 0);
?>
  <div class="col-md-4">
    <div class="card shadow-sm h-100">
      <?php if ($img): ?>
        <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" style="height:200px; object-fit:cover;">
      <?php endif; ?>
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <h5 class="card-title mb-1"><?= htmlspecialchars($car['car_name'] ?? '-') ?></h5>
          <span class="badge text-bg-secondary"><?= htmlspecialchars($car['car_status'] ?? '-') ?></span>
        </div>

        <div class="text-muted small">
          Brand: <?= htmlspecialchars($car['car_brand'] ?? '-') ?> <br/>
          Model: <?= htmlspecialchars($car['car_model'] ?? '-') ?> <br/>
          Price per day: <?= number_format($price, 2) ?> บาท
        </div>

        <div class="mt-3">
          <?php if (($car['car_status'] ?? 'available') === 'available'): ?>
            <a class="btn btn-outline-primary btn-sm" href="/booking_create.php?car_id=<?= (int)$car['car_id'] ?>">จองรถคันนี้</a>
          <?php else: ?>
            <button class="btn btn-secondary btn-sm" disabled>
              ไม่พร้อมให้จอง (<?= htmlspecialchars($car['car_status'] ?? '-') ?>)
            </button>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>

<!-- Success Modal -->
<div class="modal fade" id="okModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header border-0">
        <h5 class="modal-title w-100">สำเร็จ</h5>
      </div>
      <div class="modal-body">
        <div class="mb-3 fs-1 text-success">✔</div>
        <p class="fs-5 mb-0">จองรถสำเร็จแล้ว</p>
      </div>
      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">ตกลง</button>
      </div>
    </div>
  </div>
</div>

<!-- Conflict Modal -->
<div class="modal fade" id="conflictModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header border-0">
        <h5 class="modal-title w-100">แจ้งเตือน</h5>
      </div>
      <div class="modal-body">
        <div class="mb-3 fs-1 text-danger">✖</div>
        <p class="fs-5 mb-0">ช่วงวันที่เลือกมีคนจองแล้ว</p>
        <small class="text-muted">กรุณาเลือกวันใหม่</small>
      </div>
      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">ตกลง</button>
      </div>
    </div>
  </div>
</div>

<?php if (isset($_GET['ok']) || (isset($_GET['err']) && $_GET['err'] === 'conflict')): ?>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);

    if (params.get('ok') === '1') {
      new bootstrap.Modal(document.getElementById('okModal')).show();
      params.delete('ok');
    }

    if (params.get('err') === 'conflict') {
      new bootstrap.Modal(document.getElementById('conflictModal')).show();
      params.delete('err');
    }

    // ล้าง query string กันเด้งซ้ำตอน refresh
    const url = new URL(window.location.href);
    url.search = params.toString();
    history.replaceState({}, '', url.toString());
  });
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
