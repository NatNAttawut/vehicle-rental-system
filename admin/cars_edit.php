<?php
$title = "แก้ไขรถ";
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../lib/supabase.php';
require_admin();

$car_id = (int)($_GET['car_id'] ?? 0);
if ($car_id <= 0) exit("car_id ไม่ถูกต้อง");

$url = SUPABASE_URL . "/rest/v1/cars?select=car_id,car_name,car_brand,car_model,car_status,car_img,car_price_per_day&car_id=eq.$car_id&limit=1";
[$http, $resp, $err] = sb_request('GET', $url, sb_service_headers());
if ($err) exit("cURL error: " . htmlspecialchars($err));
if ($http < 200 || $http >= 300) exit("อ่านข้อมูลรถไม่สำเร็จ: " . htmlspecialchars($resp));

$rows = json_decode($resp, true) ?: [];
if (empty($rows)) exit("ไม่พบรถที่ต้องการแก้ไข");
$car = $rows[0];
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">แก้ไขข้อมูลรถ #<?= (int)$car['car_id'] ?></h4>
  <a class="btn btn-outline-secondary btn-sm" href="/admin/cars.php">กลับ</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" action="/admin/cars_update.php" enctype="multipart/form-data">
      <input type="hidden" name="car_id" value="<?= (int)$car['car_id'] ?>">

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">ชื่อรถ</label>
          <input class="form-control" name="car_name" required value="<?= htmlspecialchars($car['car_name'] ?? '') ?>">
        </div>

        <div class="col-md-3">
          <label class="form-label">ยี่ห้อ (Brand)</label>
          <input class="form-control" name="car_brand" required value="<?= htmlspecialchars($car['car_brand'] ?? '') ?>">
        </div>

        <div class="col-md-3">
          <label class="form-label">รุ่น (Model)</label>
          <input class="form-control" name="car_model" required value="<?= htmlspecialchars($car['car_model'] ?? '') ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">สถานะ</label>
          <?php $st = $car['car_status'] ?? 'available'; ?>
          <select class="form-select" name="car_status" required>
            <?php foreach (['available','unavailable','maintenance'] as $s): ?>
              <option value="<?= $s ?>" <?= ($st === $s) ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
          <div class="text-muted small mt-1">maintenance จะกันการ approve/active อัตโนมัติ</div>
        </div>

        <div class="col-md-8">
          <label class="form-label">รูป (อัปโหลดใหม่ถ้าต้องการเปลี่ยน)</label>
          <input class="form-control" type="file" name="car_image" accept=".jpg,.jpeg,.png,.webp">
          <?php if (!empty($car['car_img'])): ?>
            <div class="mt-2">
              <div class="small text-muted mb-1">รูปปัจจุบัน:</div>
              <img src="<?= htmlspecialchars($car['car_img']) ?>" style="max-width:260px; height:160px; object-fit:cover;" class="rounded border">
            </div>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label class="form-label">ราคา/วัน (บาท)</label>
          <input type="number" class="form-control" name="car_price_per_day" step="1" min="0" value="<?= htmlspecialchars((string)($car['car_price_per_day'] ?? '0')) ?>" required>
        </div>

        <div class="col-12">
          <button class="btn btn-primary">บันทึกการแก้ไข</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
