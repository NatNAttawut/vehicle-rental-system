<?php
$title = "เพิ่มรถ";
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../lib/supabase.php';
require_admin();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">เพิ่มรถ</h4>
  <a class="btn btn-outline-secondary btn-sm" href="/admin/cars.php">กลับ</a>
</div>

<?php if (isset($_GET['err'])): ?>
  <div class="alert alert-danger">
    เพิ่มรถไม่สำเร็จ: <?= htmlspecialchars($_GET['err']) ?>
  </div>
<?php endif; ?>

<form method="post" action="/admin/cars_create.php" enctype="multipart/form-data" class="card p-3 shadow-sm">
  <div class="row g-3">

    <div class="col-md-6">
      <label class="form-label">ชื่อรถ *</label>
      <input type="text" name="car_name" class="form-control" required>
    </div>

    <div class="col-md-3">
      <label class="form-label">หมายเลขรถ</label>
      <input type="text" name="car_num" class="form-control">
    </div>

    <div class="col-md-3">
      <label class="form-label">ทะเบียน</label>
      <input type="text" name="car_regis" class="form-control">
    </div>

    <div class="col-md-4">
      <label class="form-label">ยี่ห้อ</label>
      <input type="text" name="car_brand" class="form-control">
    </div>

    <div class="col-md-4">
      <label class="form-label">รุ่น</label>
      <input type="text" name="car_model" class="form-control">
    </div>

    <div class="col-md-4">
      <label class="form-label">ราคา/วัน (บาท) *</label>
      <input type="number" name="car_price_per_day" class="form-control" min="0" step="0.01" value="0" required>
    </div>

    <div class="col-md-4">
      <label class="form-label">ประเภทรถ *</label>
      <select name="car_type" class="form-select" required>
        <option value="car" selected>รถยนต์ (car)</option>
        <option value="motorcycle">มอเตอร์ไซ (motorcycle)</option>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">สถานะ *</label>
      <select name="car_status" class="form-select" required>
        <option value="available" selected>available</option>
        <option value="unavailable">unavailable</option>
        <option value="maintenance">maintenance</option>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">รูปรถ (ไม่บังคับ)</label>
      <input type="file" name="car_image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
      <div class="form-text">รองรับ jpg/jpeg/png/webp</div>
    </div>

  </div>

  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-warning">บันทึก</button>
    <a class="btn btn-outline-secondary" href="/admin/cars.php">ยกเลิก</a>
  </div>
</form>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
