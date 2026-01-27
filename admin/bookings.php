<?php
$title = "รายการจองทั้งหมด";
require_once __DIR__ . '/../partials/header_admin.php';
require_once __DIR__ . '/../lib/supabase.php';
require_admin();

$status = trim($_GET['status'] ?? ''); // ว่าง = ทั้งหมด

$base = SUPABASE_URL . "/rest/v1/booking?select=book_id,book_start,book_exp,book_status,car_id,cust_id,"
  . "cars(car_name,car_status),customer(cust_uname,cust_phone)"
  . "&order=book_id.desc";

$url = $base;
if ($status !== '') {
  $url .= "&book_status=eq." . rawurlencode($status);
}

[$http, $resp, $err] = sb_request('GET', $url, sb_service_headers());
$rows = json_decode($resp, true) ?: [];

function badge($s) {
  $c = 'text-bg-secondary';
  if ($s === 'pending') $c = 'text-bg-warning';
  elseif ($s === 'approved') $c = 'text-bg-success';
  elseif ($s === 'active') $c = 'text-bg-primary';
  elseif ($s === 'completed') $c = 'text-bg-dark';
  elseif ($s === 'rejected') $c = 'text-bg-danger';
  elseif ($s === 'cancelled') $c = 'text-bg-secondary';
  return "<span class=\"badge $c\">" . htmlspecialchars($s) . "</span>";
}
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
  <h4 class="mb-0">รายการจองทั้งหมด</h4>

  <form class="d-flex gap-2" method="get">
    <select class="form-select form-select-sm" name="status">
      <option value="" <?= $status===''?'selected':'' ?>>ทั้งหมด</option>
      <?php foreach (['pending','approved','active','completed','rejected','cancelled'] as $s): ?>
        <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= $s ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-dark btn-sm">Filter</button>
  </form>
</div>

<table class="table table-bordered bg-white align-middle">
  <thead>
    <tr>
      <th>#</th>
      <th>ผู้จอง</th>
      <th>โทร</th>
      <th>รถ</th>
      <th>สถานะรถ</th>
      <th>เริ่ม</th>
      <th>สิ้นสุด</th>
      <th>สถานะการจอง</th>
      <th style="width:280px;">จัดการ</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r):
      $car = $r['cars'] ?? [];
      $cus = $r['customer'] ?? [];
      $bs = $r['book_status'] ?? '';
      $carStatus = $car['car_status'] ?? '';
    ?>
      <tr>
        <td><?= (int)$r['book_id'] ?></td>
        <td><?= htmlspecialchars($cus['cust_uname'] ?? '-') ?></td>
        <td><?= htmlspecialchars($cus['cust_phone'] ?? '-') ?></td>
        <td><?= htmlspecialchars($car['car_name'] ?? '-') ?></td>
        <td><?= htmlspecialchars($carStatus ?: '-') ?></td>
        <td><?= htmlspecialchars($r['book_start']) ?></td>
        <td><?= htmlspecialchars($r['book_exp']) ?></td>
        <td><?= badge($bs) ?></td>
        <td>
          <div class="d-flex flex-wrap gap-2">
            <?php if ($bs === 'pending'): ?>
              <form method="post" action="/admin/booking_action.php">
                <input type="hidden" name="book_id" value="<?= (int)$r['book_id'] ?>">
                <input type="hidden" name="action" value="approve">
                <button class="btn btn-success btn-sm">Approve</button>
              </form>
              <form method="post" action="/admin/booking_action.php">
                <input type="hidden" name="book_id" value="<?= (int)$r['book_id'] ?>">
                <input type="hidden" name="action" value="reject">
                <button class="btn btn-danger btn-sm">Reject</button>
              </form>
            <?php elseif ($bs === 'approved'): ?>
              <form method="post" action="/admin/booking_action.php">
                <input type="hidden" name="book_id" value="<?= (int)$r['book_id'] ?>">
                <input type="hidden" name="action" value="set_active">
                <button class="btn btn-primary btn-sm">Set Active</button>
              </form>
              <form method="post" action="/admin/booking_action.php" onsubmit="return confirm('ยกเลิกการจองนี้?');">
                <input type="hidden" name="book_id" value="<?= (int)$r['book_id'] ?>">
                <input type="hidden" name="action" value="cancel">
                <button class="btn btn-outline-danger btn-sm">Cancel</button>
              </form>
            <?php elseif ($bs === 'active'): ?>
              <form method="post" action="/admin/booking_action.php" onsubmit="return confirm('ปิดงานเป็น completed?');">
                <input type="hidden" name="book_id" value="<?= (int)$r['book_id'] ?>">
                <input type="hidden" name="action" value="complete">
                <button class="btn btn-warning btn-sm">Complete</button>
              </form>
            <?php else: ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
          </div>
          <?php if ($carStatus === 'maintenance'): ?>
            <div class="text-danger small mt-1">รถอยู่ระหว่างซ่อม: ระบบจะไม่อนุมัติเป็น approved/active</div>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
