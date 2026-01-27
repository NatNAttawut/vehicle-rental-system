<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$cust_id      = $_SESSION['cust_id'] ?? null;
$cust_name    = $_SESSION['cust_name'] ?? ($_SESSION['cust_uname'] ?? 'User');
$cust_picture = $_SESSION['cust_picture'] ?? '';
$cust_role    = $_SESSION['cust_role'] ?? 'user';

// fallback รูปถ้าไม่มี
$avatar = $cust_picture !== '' ? $cust_picture : '/img/default-avatar.png';
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title ?? 'Car Rental') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .top-bar { background:#ff9800; color:#000; }
    .top-bar .btn { background:#ffc107; border:none; font-weight:500; }
    .navbar-dark { background:#212529; }
    .nav-link.active { color:#ffc107 !important; font-weight:600; }

    /* ให้เหมือนรูป 2 */
    .user-pill {
      background: rgba(0,0,0,.08);
      border: 1px solid rgba(0,0,0,.15);
      border-radius: 999px;
      padding: 6px 10px;
    }
    .user-avatar {
      width: 34px; height: 34px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid rgba(0,0,0,.25);
      background: #fff;
    }
    .dropdown-menu {
      border-radius: 12px;
      overflow: hidden;
    }
  </style>
</head>
<body>

<div class="top-bar py-2">
  <div class="container d-flex justify-content-between align-items-center">

    <!-- Logo (กดกลับหน้าแรก) -->
    <a href="/index.php" class="d-flex align-items-center gap-2 text-decoration-none">
      <img src="/img/LogoCodecraft.png" alt="Logo" height="40">
    </a>

    <div class="fw-bold fs-5">มีรถหรือยัง?</div>

    <!-- Right actions -->
    <div class="d-flex align-items-center gap-2">

      <?php if (empty($cust_id)): ?>
        <a href="/register.php" class="btn btn-sm">สมัครสมาชิก</a>
        <a href="/login.php" class="btn btn-sm">เข้าสู่ระบบ</a>

      <?php else: ?>


        <!-- Dropdown โปรไฟล์ -->
        <div class="dropdown">
          <button class="btn btn-sm user-pill dropdown-toggle d-flex align-items-center gap-2"
                  type="button"
                  data-bs-toggle="dropdown"
                  aria-expanded="false">
            <img src="<?= htmlspecialchars($avatar) ?>" class="user-avatar" alt="avatar">
            <span class="fw-semibold"><?= htmlspecialchars($cust_name) ?></span>
          </button>

          <ul class="dropdown-menu dropdown-menu-end shadow">
            <li>
              <a class="dropdown-item" href="/profile_edit.php">แก้ไขข้อมูลส่วนตัว</a>
            </li>
            <li><hr class="dropdown-divider"></li>

            <li>
              <a class="dropdown-item" href="/my_bookings.php">รายการจองของฉัน</a>
            </li>

            <?php if ($cust_role === 'admin'): ?>
              <li>
                <a class="dropdown-item" href="/admin/cars.php">ไปหน้า Admin</a>
              </li>
            <?php endif; ?>

            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item text-danger fw-semibold" href="/logout.php">ออกจากระบบ</a>
            </li>
          </ul>
        </div>

      <?php endif; ?>

    </div>
  </div>
</div>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto gap-lg-3">
        <li class="nav-item">
          <a class="nav-link <?= ($_SERVER['REQUEST_URI']=='/' || str_contains($_SERVER['REQUEST_URI'],'index.php'))?'active':'' ?>" href="/index.php">กลับไปหน้าแรก</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'cars.php')?'active':'' ?>" href="/admin/cars.php">จัดการรถ</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'bookings.php')?'active':'' ?>" href="/admin/bookings.php">จัดการการจอง</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'members.php')?'active':'' ?>" href="/admin/members.php">จัดการสมาชิก</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
