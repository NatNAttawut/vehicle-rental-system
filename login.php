<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$title = "เข้าสู่ระบบ";
$registered = isset($_GET['registered']);
$err = $_GET['err'] ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body{
      min-height:100vh;
      background:
        radial-gradient(circle at 20% 10%, rgba(255,152,0,.25), transparent 35%),
        radial-gradient(circle at 80% 60%, rgba(0,200,255,.15), transparent 40%),
        linear-gradient(180deg, #0b1220, #05070c);
      color:#e9eef5;
    }
    .glass-card{
      background: rgba(16, 24, 36, 0.82);
      border: 1px solid rgba(255,255,255,0.10);
      border-radius: 18px;
      box-shadow: 0 20px 60px rgba(0,0,0,.55);
      overflow:hidden;
    }
    .card-head{
      padding: 16px 18px;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      background: linear-gradient(180deg, rgba(255,255,255,0.05), rgba(255,255,255,0));
    }
    .soft-input{
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(255,255,255,0.10);
      color: #e9eef5;
    }
    .soft-input:focus{
      border-color: rgba(255,193,7,.55);
      box-shadow: 0 0 0 .25rem rgba(255,193,7,.15);
      background: rgba(255,255,255,0.08);
      color:#fff;
    }
    .btn-orange{
      background: #ff9800;
      border: none;
      color:#111;
      font-weight:700;
      border-radius: 12px;
      padding: 12px 14px;
    }
    .btn-orange:hover{ background:#ffb300; }
    .btn-ghost{
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(255,255,255,0.10);
      color:#e9eef5;
      border-radius: 12px;
    }
    .muted{ color: rgba(233,238,245,.75); }
    .tiny{ font-size: 12px; }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-6 col-xl-5">

      <?php if ($registered): ?>
        <div class="alert alert-success">สมัครสำเร็จ! กรุณาเข้าสู่ระบบ</div>
      <?php endif; ?>

      <?php if ($err !== ''): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>

      <div class="glass-card">
        <div class="card-head d-flex align-items-center justify-content-between gap-3">
          <div class="d-flex align-items-center gap-3">
            <a href="/index.php" class="text-decoration-none d-flex align-items-center gap-2">
              <img src="/img/LogoCodecraft.png" alt="Logo" style="height:38px;width:auto;border-radius:8px;">
            </a>
            <div>
              <div class="fw-bold">เข้าสู่ระบบ</div>
              <div class="muted tiny">ล็อกอินเพื่อดูรายการจอง/จองรถ</div>
            </div>
          </div>
          <a class="btn btn-warning btn-sm fw-bold rounded-pill px-3" href="/index.php">← กลับหน้าแรก</a>
        </div>

        <div class="p-4">
          <form method="post" action="/loginProcess.php">

            <div class="col-12">
              <label class="form-label fw-semibold">Username หรือ Email</label>
              <input class="form-control soft-input" name="identifier" required autocomplete="username" placeholder="Username or Email">
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">รหัสผ่าน</label>
              <input class="form-control soft-input" name="password" type="password" required autocomplete="current-password" placeholder="Password">
            </div>
            <br>
            <div class="col-12">
              <button class="btn btn-orange w-100">เข้าสู่ระบบ</button>
            </div>

            <div class="col-12">
              <a class="btn btn-ghost w-100" href="/register.php">ยังไม่มีบัญชี? สมัครสมาชิก</a>
            </div>
            <div class="col-12">
              <a class="btn btn-ghost w-100" href="/forgot_password.php">ลืมรหัสผ่านหรอ? คลิกตรงนี่สิ</a>
            </div>
          </form>
        </div>

        <div class="text-center tiny muted py-3" style="border-top:1px solid rgba(255,255,255,.08)">
          © 2026 รถเช่ายืมหรอ? : ระบบยืม/เช่ารถ
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
