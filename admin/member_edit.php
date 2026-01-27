<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/supabase.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

if (empty($_SESSION['cust_id']) || ($_SESSION['cust_role'] ?? '') !== 'admin') {
  header("Location: /login.php?err=" . urlencode("ต้องเป็นแอดมินเท่านั้น"));
  exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
  header("Location: members.php?err=" . urlencode("ไม่พบ id"));
  exit;
}

/* =========================
   ✅ Handle POST update FIRST (before any HTML output)
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $cust_name   = trim($_POST['cust_name'] ?? '');
  $cust_phone  = trim($_POST['cust_phone'] ?? '');
  $cust_role   = strtolower(trim($_POST['cust_role'] ?? 'user'));
  $cust_status = strtolower(trim($_POST['cust_status'] ?? 'active'));

  if (!in_array($cust_role, ['user','admin'], true)) $cust_role = 'user';
  if (!in_array($cust_status, ['active','suspended'], true)) $cust_status = 'active';

  $patchUrl = SUPABASE_URL . "/rest/v1/customer?cust_id=eq.$id";
  $payload = json_encode([
    "cust_name"   => $cust_name,
    "cust_phone"  => $cust_phone,
    "cust_role"   => $cust_role,
    "cust_status" => $cust_status,
  ], JSON_UNESCAPED_UNICODE);

  [$hp,$rp,$ep] = sb_request('PATCH', $patchUrl, array_merge(
    sb_service_headers(),
    ['Content-Type: application/json', 'Prefer: return=representation']
  ), $payload);

  if ($ep || $hp < 200 || $hp >= 300) {
    // ส่ง error กลับมาแสดงบนหน้าเดิมแบบไม่ใช้ header()
    $save_error = $ep ?: $rp;
  } else {
    header("Location: members.php?msg=" . urlencode("บันทึกข้อมูลแล้ว"));
    exit;
  }
}

/* =========================
   Fetch customer (GET display)
   ========================= */
$select = rawurlencode("cust_id,auth_user_id,cust_uname,cust_email,cust_name,cust_phone,cust_picture,cust_role,cust_status");
$url = SUPABASE_URL . "/rest/v1/customer?select={$select}&cust_id=eq.$id&limit=1";
[$h1,$r1,$e1] = sb_request('GET', $url, sb_service_headers());
$rows = json_decode($r1, true) ?: [];
$u = $rows[0] ?? null;

if (!$u) {
  header("Location: members.php?err=" . urlencode("ไม่พบสมาชิก"));
  exit;
}

$title = "แก้ไขสมาชิก #".$id;
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= h($title) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f5f6f8">
<div class="container py-4" style="max-width:720px">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">แก้ไขข้อมูลสมาชิก</h4>
    <a class="btn btn-outline-secondary" href="members.php">← กลับ</a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">

      <?php if (!empty($save_error)): ?>
        <div class="alert alert-danger">บันทึกไม่สำเร็จ: <?= h($save_error) ?></div>
      <?php endif; ?>

      <div class="d-flex gap-3 align-items-center mb-3">
        <?php $pic = $u['cust_picture'] ?: 'https://www.gravatar.com/avatar/?d=mp&s=80'; ?>
        <img src="<?= h($pic) ?>" style="width:54px;height:54px;border-radius:50%;object-fit:cover;border:2px solid #e9ecef">
        <div>
          <div class="fw-bold"><?= h($u['cust_uname']) ?></div>
          <div class="text-muted small"><?= h($u['cust_email']) ?></div>
        </div>
      </div>

      <form method="post" action="member_edit.php?id=<?= (int)$id ?>">
        <input type="hidden" name="cust_id" value="<?= (int)$id ?>">

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">ชื่อ-นามสกุล</label>
            <input class="form-control" name="cust_name" value="<?= h($u['cust_name'] ?? '') ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">เบอร์โทร</label>
            <input class="form-control" name="cust_phone" value="<?= h($u['cust_phone'] ?? '') ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Role</label>
            <select class="form-select" name="cust_role">
              <?php $role = strtolower($u['cust_role'] ?? 'user'); ?>
              <option value="user"  <?= $role==='user'?'selected':'' ?>>User</option>
              <option value="admin" <?= $role==='admin'?'selected':'' ?>>Admin</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">สถานะการใช้งาน</label>
            <?php $st = strtolower($u['cust_status'] ?? 'active'); ?>
            <select class="form-select" name="cust_status">
              <option value="active"    <?= $st==='active'?'selected':'' ?>>ปกติ (active)</option>
              <option value="suspended" <?= $st!=='active'?'selected':'' ?>>ถูกระงับ (suspended)</option>
            </select>
          </div>

          <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary">บันทึก</button>
            <a class="btn btn-outline-secondary" href="members.php">ยกเลิก</a>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
