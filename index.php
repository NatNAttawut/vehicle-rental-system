<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$title = "หน้าแรก";
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/supabase.php';

// ดึงรถทั้งหมดจาก Supabase
// ใช้ select=* เพื่อไม่พังถ้าชื่อคอลัมน์บางตัวต่างกัน
$carsUrl = SUPABASE_URL . "/rest/v1/cars?select=*&order=car_id.asc";

[$hc, $rc, $ec] = sb_request('GET', $carsUrl, [
  'apikey: ' . SUPABASE_ANON_KEY,
  'Authorization: Bearer ' . SUPABASE_ANON_KEY
]);

$cars = [];
if (!$ec && $hc >= 200 && $hc < 300) {
  $cars = json_decode($rc, true) ?: [];
}

// helper escape
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// ทำป้ายสถานะให้เหมือนตัวอย่าง
function status_meta($status){
  $s = strtolower(trim((string)$status));
  if ($s === 'available')   return ['label'=>'ว่าง',     'badge'=>'success', 'overlay'=>false, 'btn'=>true];
  if ($s === 'unavailable') return ['label'=>'ไม่ว่าง',  'badge'=>'danger',  'overlay'=>true,  'btn'=>false];
  if ($s === 'repair')      return ['label'=>'ซ่อมบำรุง','badge'=>'warning', 'overlay'=>true,  'btn'=>false];
  return ['label'=>($status ?: 'ไม่ทราบสถานะ'), 'badge'=>'secondary', 'overlay'=>true, 'btn'=>false];
}
?>

<style>
  body{ background:#f2f2f2; }
  .hero-wrap{ padding: 18px 0 6px; }
  .hero-img{
    width:100%;
    max-height: 360px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid rgba(0,0,0,.08);
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
  }

  .car-card{
    border: 0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
  }
  .car-thumb{
    position: relative;
    background: #fff;
    height: 170px;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
  }
  .car-thumb img{
    width:100%;
    height:100%;
    object-fit: contain; /* ให้เหมือนรูปตัวอย่าง (รถอยู่กลาง) */
    background:#fff;
  }
  .overlay{
    position:absolute; inset:0;
    background: rgba(0,0,0,.55);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size: 24px;
    font-weight: 800;
    color:#fff;
    letter-spacing: .5px;
  }
  .price{
    font-size: 20px;
    font-weight: 800;
    color: #198754;
  }
  .btn-book{
    background:#198754;
    border: none;
    font-weight: 800;
    border-radius: 10px;
    padding: 10px 12px;
  }
  .btn-book:disabled{
    background:#cfd4da;
    color:#6c757d;
  }
  .small-muted{ color:#6c757d; font-size: 13px; }
</style>

<div class="container hero-wrap">
  <!-- เปลี่ยนรูป banner ให้เป็นรูปที่คุณใช้จริงได้ -->
  <img class="hero-img" src="/img/Banner1.png" alt="banner">
</div>

<div class="container pb-5">
  <div class="row g-4 mt-2">

    <?php if (empty($cars)): ?>
      <div class="col-12">
        <div class="alert alert-warning mb-0">
          ยังไม่มีข้อมูลรถในระบบ หรือดึงข้อมูลจาก Supabase ไม่สำเร็จ
        </div>
      </div>
    <?php endif; ?>

    <?php foreach ($cars as $c): ?>
      <?php
        $carId   = (int)($c['car_id'] ?? 0);

        // ชื่อรถ: เผื่อชื่อคอลัมน์ต่างกัน
        $carName = $c['car_name'] ?? $c['name'] ?? $c['car_title'] ?? 'รถ';

        // ป้ายทะเบียน: เผื่อชื่อคอลัมน์ต่างกัน
        $plate   = $c['car_plate'] ?? $c['plate'] ?? $c['car_number'] ?? '';

        $price   = (float)($c['car_price_per_day'] ?? $c['price_per_day'] ?? $c['price'] ?? 0);

        // รูป: หลายโปรเจกต์ใช้ car_img หรือ car_image
        $img     = $c['car_img'] ?? $c['car_image'] ?? $c['image_url'] ?? '';
        if ($img === '') $img = "https://via.placeholder.com/700x400?text=Car";

        $meta = status_meta($c['car_status'] ?? '');
      ?>

      <div class="col-12 col-md-6 col-lg-4 col-xl-3">
        <div class="card car-card h-100">
          <div class="car-thumb">
            <img src="<?= h($img) ?>" alt="car">
            <?php if ($meta['overlay']): ?>
              <div class="overlay"><?= h($meta['label']) ?></div>
            <?php endif; ?>
          </div>

          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start gap-2">
              <div class="fw-bold"><?= h($carName) ?></div>
              <span class="badge text-bg-<?= h($meta['badge']) ?>"><?= h($meta['label']) ?></span>
            </div>

            <?php if ($plate !== ''): ?>
              <div class="small-muted mt-1">ทะเบียน: <?= h($plate) ?></div>
            <?php endif; ?>

            <div class="mt-2">
              <span class="price"><?= number_format($price, 0) ?></span>
              <span class="small-muted"> บาท/วัน</span>
            </div>

            <div class="mt-3">
              <?php if ($meta['btn']): ?>
                <!-- ไปหน้า booking_create.php เพื่อเลือกวัน แล้วค่อย POST ไป booking_store.php -->
                <a class="btn btn-book w-100 text-white" href="/booking_create.php?car_id=<?= $carId ?>">
                  จองทันที
                </a>
              <?php else: ?>
                <button class="btn btn-book w-100" disabled>ไม่สามารถจองได้</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

    <?php endforeach; ?>

  </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
