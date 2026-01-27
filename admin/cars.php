<?php
$title = "จัดการรถ";
require_once __DIR__ . '/../partials/header_admin.php';
require_once __DIR__ . '/../lib/supabase.php';
require_admin();

// ดึงรายการรถ (ใช้ service role เพราะเป็นหน้า admin)
$url = SUPABASE_URL . "/rest/v1/cars?select=car_id,car_name,car_brand,car_model,car_status,car_img&order=car_id.desc";
[$http, $resp, $err] = sb_request('GET', $url, sb_service_headers());
$rows = json_decode($resp, true) ?: [];

function carBadgeClass($status) {
  if ($status === 'available') return 'text-bg-success';
  if ($status === 'unavailable') return 'text-bg-danger';
  if ($status === 'maintenance') return 'text-bg-warning';
  return 'text-bg-secondary';
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">จัดการรถ</h4>
  <a class="btn btn-warning btn-sm" href="/admin/cars_add.php">+ เพิ่มรถ</a>
</div>

<?php if (isset($_GET['updated']) && $_GET['updated'] === '1'): ?>
  <div class="alert alert-success">บันทึกการแก้ไขสำเร็จ</div>
<?php endif; ?>

<?php if (isset($_GET['deleted']) && $_GET['deleted'] === '1'): ?>
  <div class="alert alert-success">ลบรถสำเร็จ</div>
<?php elseif (isset($_GET['deleted']) && $_GET['deleted'] === '0' && ($_GET['reason'] ?? '') === 'has_booking'): ?>
  <div class="alert alert-warning">ลบไม่ได้: รถคันนี้มีรายการจองที่ยังไม่สิ้นสุด (pending/approved/active)</div>
<?php elseif (isset($_GET['deleted']) && $_GET['deleted'] === '0' && ($_GET['reason'] ?? '') === 'notfound'): ?>
  <div class="alert alert-danger">ไม่พบข้อมูลรถ</div>
<?php endif; ?>

<table class="table table-bordered bg-white align-middle">
  <thead>
    <tr>
      <th>#</th>
      <th>รูป</th>
      <th>ชื่อรถ</th>
      <th>ยี่ห้อ/รุ่น</th>
      <th>สถานะ</th>
      <th style="width:160px;">จัดการ</th>
      <th style="width:120px;">ลบ</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($rows as $c): ?>
      <tr>
        <td><?= (int)$c['car_id'] ?></td>

        <td style="width:120px;">
          <?php if (!empty($c['car_img'])): ?>
            <img src="<?= htmlspecialchars($c['car_img']) ?>"
                 style="width:110px;height:70px;object-fit:cover;"
                 class="rounded border">
          <?php else: ?>
            <span class="text-muted">-</span>
          <?php endif; ?>
        </td>

        <td><?= htmlspecialchars($c['car_name'] ?? '-') ?></td>

        <td><?= htmlspecialchars(($c['car_brand'] ?? '-') . ' ' . ($c['car_model'] ?? '')) ?></td>

        <td>
          <span class="badge <?= carBadgeClass($c['car_status'] ?? '') ?>">
            <?= htmlspecialchars($c['car_status'] ?? '-') ?>
          </span>
        </td>

        <!-- ✅ แก้ไขข้อมูลทั้งหมด -->
        <td>
          <a class="btn btn-dark btn-sm w-100"
             href="/admin/cars_edit.php?car_id=<?= (int)$c['car_id'] ?>">
             แก้ไข
          </a>

          <?php if (($c['car_status'] ?? '') === 'maintenance'): ?>
            <div class="text-muted small mt-1">สถานะนี้จะกันการ approve/active อัตโนมัติ</div>
          <?php endif; ?>
        </td>

        <!-- ✅ ลบ (กันลบรถที่มีการจองอยู่ ทำใน cars_delete.php) -->
        <td>
          <form method="post" action="/admin/cars_delete.php"
                onsubmit="return confirm('ยืนยันลบรถคันนี้? หากมีการจองอยู่จะไม่สามารถลบได้');">
            <input type="hidden" name="car_id" value="<?= (int)$c['car_id'] ?>">
            <button class="btn btn-danger btn-sm w-100">ลบ</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
