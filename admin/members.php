<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/../partials/header_admin.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/supabase.php';

// กันสิทธิ์แอดมิน (ถ้าคุณมีฟังก์ชัน require_admin() ใช้อยู่แล้วให้เรียกแทนได้)
if (empty($_SESSION['cust_id']) || ($_SESSION['cust_role'] ?? '') !== 'admin') {
  header("Location: /login.php?err=" . urlencode("ต้องเป็นแอดมินเท่านั้น"));
  exit;
}

$title = "จัดการสมาชิก";

// ====== Pagination / Search ======
// ====== Pagination / Search ======
$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$q = trim($_GET['q'] ?? ''); // search by uname/email/name

// ====== Count total ======
$countUrl = SUPABASE_URL . "/rest/v1/customer?select=cust_id";
if ($q !== '') {
  $like = rawurlencode("%{$q}%");
  $countUrl .= "&or=(cust_uname.ilike.$like,cust_email.ilike.$like,cust_name.ilike.$like)";
}

// ขอให้ Supabase ส่ง total มาใน Content-Range header
[$hCount, $rCount, $eCount] = sb_request('GET', $countUrl, array_merge(
  sb_service_headers(),
  ['Prefer: count=exact']
));

$total = 0;

if (!$eCount) {
  // hCount อาจเป็น array หรือ string
  $headerLines = is_array($hCount) ? $hCount : preg_split("/\r\n|\n|\r/", (string)$hCount);

  foreach ($headerLines as $line) {
    if (preg_match('/content-range:\s*\d+-\d+\/(\d+)/i', $line, $m)) {
      $total = (int)$m[1];
      break;
    }
  }

  // fallback: ถ้าไม่เจอ content-range จริง ๆ ให้ใช้จำนวนแถวที่ได้กลับมา
  if ($total === 0) {
    $tmp = json_decode($rCount, true);
    if (is_array($tmp)) $total = count($tmp);
  }
}

// ✅ คำนวณ totalPages (กันหาร 0)
$totalPages = max(1, (int)ceil($total / $limit));


// ====== Fetch rows ======
$select = rawurlencode("cust_id,cust_uname,cust_email,cust_name,cust_phone,cust_picture,cust_role,cust_status,created_at");
$listUrl = SUPABASE_URL . "/rest/v1/customer?select={$select}&order=cust_id.desc&limit={$limit}&offset={$offset}";

if ($q !== '') {
  $like = rawurlencode("%{$q}%");
  $listUrl .= "&or=(cust_uname.ilike.$like,cust_email.ilike.$like,cust_name.ilike.$like)";
}

[$h, $r, $e] = sb_request('GET', $listUrl, sb_service_headers());
$rows = json_decode($r, true) ?: [];



$q = trim($_GET['q'] ?? ''); // search by uname/email/name

// ====== Count total (ชัวร์) ======
$countUrl = SUPABASE_URL . "/rest/v1/customer?select=cust_id&limit=1&offset=0";
if ($q !== '') {
  $like = rawurlencode("%{$q}%");
  $countUrl .= "&or=(cust_uname.ilike.$like,cust_email.ilike.$like,cust_name.ilike.$like)";
}

[$hCount, $rCount, $eCount] = sb_request('GET', $countUrl, array_merge(
  sb_service_headers(),
  [
    'Prefer: count=exact',
    'Range: 0-0'
  ]
));

$total = 0;
if (!$eCount) {
  $headerLines = is_array($hCount) ? $hCount : preg_split("/\r\n|\n|\r/", (string)$hCount);
  foreach ($headerLines as $line) {
    if (preg_match('/content-range:\s*\d+-\d+\/(\d+)/i', $line, $m)) {
      $total = (int)$m[1];
      break;
    }
  }
}


// ขอให้ Supabase ส่ง total มาใน Content-Range header
[$hCount, $rCount, $eCount] = sb_request('GET', $countUrl, array_merge(
  sb_service_headers(),
  ['Prefer: count=exact']
));

if ($eCount) {
  $total = 0;
} else {
  // sb_request ของคุณอาจคืน header เป็น "string" หรือ "array" หรือไม่คืนเลย
  // เลยอ่าน total แบบ fallback: ถ้าไม่เจอ ก็ใช้ count($rows) แทน
  $total = 0;

  // ถ้า sb_request คืน header เป็น array (บางเวอร์ชัน)
  if (is_array($hCount)) {
    foreach ($hCount as $line) {
      if (preg_match('/content-range:\s*\d+-\d+\/(\d+)/i', $line, $m)) {
        $total = (int)$m[1];
        break;
      }
    }
  }
}

// ====== Fetch rows ======
$select = rawurlencode("cust_id,cust_uname,cust_email,cust_name,cust_phone,cust_picture,cust_role,cust_status,created_at");
$listUrl = SUPABASE_URL . "/rest/v1/customer?select={$select}&order=cust_id.asc&limit={$limit}&offset={$offset}";
if ($q !== '') {
  $like = rawurlencode("%{$q}%");
  $listUrl .= "&or=(cust_uname.ilike.$like,cust_email.ilike.$like,cust_name.ilike.$like)";
}

[$h, $r, $e] = sb_request('GET', $listUrl, sb_service_headers());
$rows = json_decode($r, true) ?: [];

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function role_badge($role){
  $role = strtolower((string)$role);
  if ($role === 'admin') return '<span class="badge rounded-pill text-bg-warning">👑 Admin</span>';
  return '<span class="badge rounded-pill text-bg-secondary">User</span>';
}

function status_badge($status){
  $status = strtolower((string)$status);
  if ($status === 'active') return '<span class="text-success fw-semibold">✅ ปกติ</span>';
  return '<span class="text-danger fw-semibold">❌ ถูกระงับ</span>';
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= h($title) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{ background:#f5f6f8; }
    .table thead th{ background:#fafafa; }
    .avatar{
      width:38px;height:38px;border-radius:50%;
      object-fit:cover;border:2px solid #e9ecef;
      background:#fff;
    }
    .uname{ font-weight:700; }
    .email{ color:#6c757d; font-size:13px; }
    .toolbar{
      display:flex; gap:10px; align-items:center; justify-content:space-between;
      flex-wrap:wrap;
    }
  </style>
</head>
<body>

<?php // ถ้าคุณมี header admin แยกก็ include ได้
// include __DIR__ . '/../partials/header.php';
?>

<div class="container py-4">
  <div class="toolbar mb-3">
    <div>
      <h4 class="mb-0">👥 จัดการสมาชิก </h4>
    </div>

    <form class="d-flex gap-2" method="get" action="">
      <input class="form-control" style="min-width:260px" name="q" value="<?= h($q) ?>" placeholder="ค้นหา username / email / ชื่อ">
      <button class="btn btn-dark">ค้นหา</button>
      <a class="btn btn-outline-secondary" href="members.php">ล้าง</a>
    </form>
  </div>

  <?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-success"><?= h($_GET['msg']) ?></div>
  <?php endif; ?>
  <?php if (!empty($_GET['err'])): ?>
    <div class="alert alert-danger"><?= h($_GET['err']) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th style="width:80px">ID</th>
            <th>ข้อมูลสมาชิก</th>
            <th style="width:160px">เบอร์โทร</th>
            <th style="width:140px">สถานะ (Role)</th>
            <th style="width:140px">การใช้งาน</th>
            <th style="width:170px" class="text-end">จัดการ</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $u): ?>
          <?php
            $pic = $u['cust_picture'] ?? '';
            if ($pic === '') $pic = 'https://www.gravatar.com/avatar/?d=mp&s=80';
          ?>
          <tr>
            <td><?= (int)$u['cust_id'] ?></td>
            <td>
              <div class="d-flex gap-3 align-items-center">
                <img class="avatar" src="<?= h($pic) ?>" alt="avatar">
                <div>
                  <div class="uname"><?= h(($u['cust_name'] ?? '') !== '' ? $u['cust_name'] : ($u['cust_uname'] ?? '-')) ?></div>
                  <div class="email"><?= h($u['cust_email'] ?: '-') ?></div>
                  <div class="text-muted small">username: <?= h($u['cust_uname'] ?: '-') ?></div>
                </div>
              </div>
            </td>
            <td><?= h($u['cust_phone'] ?: '-') ?></td>
            <td><?= role_badge($u['cust_role'] ?? 'user') ?></td>
            <td><?= status_badge($u['cust_status'] ?? 'active') ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary"
                 href="member_edit.php?id=<?= (int)$u['cust_id'] ?>">แก้ไข</a>

              <a class="btn btn-sm btn-outline-danger"
                 href="member_delete.php?id=<?= (int)$u['cust_id'] ?>"
                 onclick="return confirm('ยืนยันลบสมาชิก ID <?= (int)$u['cust_id'] ?> ? (ลบแล้วกู้คืนไม่ได้)');">ลบ</a>
            </td>
          </tr>
        <?php endforeach; ?>

        <?php if (empty($rows)): ?>
          <tr><td colspan="6" class="text-center py-4 text-muted">ไม่พบข้อมูล</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted small">
      หน้า <?= $page ?> / <?= $totalPages ?>
    </div>
    <nav>
      <ul class="pagination mb-0">
        <?php
          $base = "members.php?q=" . urlencode($q) . "&page=";
        ?>
        <li class="page-item <?= $page<=1?'disabled':'' ?>">
          <a class="page-link" href="<?= $base . max(1,$page-1) ?>">ก่อนหน้า</a>
        </li>
        <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
          <a class="page-link" href="<?= $base . min($totalPages,$page+1) ?>">ถัดไป</a>
        </li>
      </ul>
    </nav>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
