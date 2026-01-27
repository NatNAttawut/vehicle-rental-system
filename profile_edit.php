<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/supabase.php';

if (empty($_SESSION['cust_id'])) {
  header("Location: /login.php");
  exit;
}

$cust_id = $_SESSION['cust_id'];
$msg = $_GET['msg'] ?? '';
$err = $_GET['err'] ?? '';

/* ดึงข้อมูลลูกค้าปัจจุบัน */
$url = SUPABASE_URL . "/rest/v1/customer"
  . "?select=*"
  . "&cust_id=eq.$cust_id"
  . "&limit=1";

[$h, $r, $e] = sb_request('GET', $url, sb_service_headers());
$data = json_decode($r, true) ?: [];
$me = $data[0] ?? null;

if (!$me) {
  die("ไม่พบข้อมูลผู้ใช้");
}

$title = "แก้ไขข้อมูลส่วนตัว";
require __DIR__ . '/partials/header.php';
?>

<div class="container" style="max-width:720px">
  <h3 class="mb-4">แก้ไขข้อมูลส่วนตัว</h3>

  <?php if ($msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>
  <?php if ($err): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>

  <form method="post" action="/profile_update.php" enctype="multipart/form-data" class="card p-4 shadow-sm">

    <div class="mb-3 text-center">
      <img src="<?= $me['cust_picture'] ?: '/img/default-avatar.png' ?>"
           class="rounded-circle mb-2"
           style="width:120px;height:120px;object-fit:cover">
      <input type="file" name="picture" class="form-control mt-2">
    </div>

    <div class="mb-3">
      <label class="form-label">Username</label>
      <input class="form-control" value="<?= htmlspecialchars($me['cust_uname']) ?>" disabled>
    </div>

    <div class="mb-3">
      <label class="form-label">ชื่อ - นามสกุล</label>
      <input class="form-control" name="cust_name" required value="<?= htmlspecialchars($me['cust_name']) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">เบอร์โทรศัพท์</label>
      <input class="form-control" name="cust_phone" required value="<?= htmlspecialchars($me['cust_phone']) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">บ้านเลขที่ / ถนน</label>
      <input class="form-control" name="cust_house" required value="<?= htmlspecialchars($me['cust_house']) ?>">
    </div>

    <div class="row g-2">
      <div class="col-md-4">
        <label class="form-label">จังหวัด</label>
        <input class="form-control" name="cust_province" required value="<?= htmlspecialchars($me['cust_province']) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">อำเภอ</label>
        <input class="form-control" name="cust_prefecture" required value="<?= htmlspecialchars($me['cust_prefecture']) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">ตำบล</label>
        <input class="form-control" name="cust_district" required value="<?= htmlspecialchars($me['cust_district']) ?>">
      </div>
    </div>

    <div class="mt-3">
      <label class="form-label">รหัสไปรษณีย์</label>
      <input class="form-control" name="cust_postcode" required value="<?= htmlspecialchars($me['cust_postcode']) ?>">
    </div>

    <div class="mt-4 d-flex gap-2">
      <button class="btn btn-warning fw-bold">บันทึกข้อมูล</button>
      <a href="/index.php" class="btn btn-secondary">ยกเลิก</a>
    </div>

  </form>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
