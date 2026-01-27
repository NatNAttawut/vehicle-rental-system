<?php
require_once __DIR__ . '/../lib/supabase.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: /admin/cars.php");
  exit;
}

$car_id    = (int)($_POST['car_id'] ?? 0);
$car_name  = trim($_POST['car_name'] ?? '');
$car_brand = trim($_POST['car_brand'] ?? '');
$car_model = trim($_POST['car_model'] ?? '');
$car_status= trim($_POST['car_status'] ?? 'available');
$car_price_per_day = (float)($_POST['car_price_per_day'] ?? 0);

// รูปเดิม: (ถ้าฟอร์มไม่ได้ส่งมา เราจะดึงจาก DB อีกทีให้)
$car_img_old = trim($_POST['car_img_old'] ?? ''); // เผื่อคุณเพิ่มในฟอร์มภายหลัง

if ($car_id <= 0 || $car_name === '' || $car_brand === '' || $car_model === '') {
  header("Location: /admin/cars.php?updated=0&err=" . urlencode("ข้อมูลไม่ครบ"));
  exit;
}

$allowedStatus = ['available','unavailable','maintenance'];
if (!in_array($car_status, $allowedStatus, true)) $car_status = 'available';

/* =========================
   1) หา car_img เดิมจาก DB (กันกรณีฟอร์มไม่ได้ส่ง car_img_old มา)
   ========================= */
if ($car_img_old === '') {
  $getUrl = SUPABASE_URL . "/rest/v1/cars?select=car_img&car_id=eq.$car_id&limit=1";
  [$gh,$gr,$ge] = sb_request('GET', $getUrl, sb_service_headers());
  $grows = json_decode($gr, true) ?: [];
  if (!empty($grows[0]['car_img'])) $car_img_old = (string)$grows[0]['car_img'];
}

/* =========================
   2) ถ้ามีรูปใหม่ -> upload ไป Storage
   ========================= */
$car_img_final = $car_img_old;

// ฟอร์มคุณชื่อ input file = car_image
if (!empty($_FILES['car_image']) && $_FILES['car_image']['error'] === UPLOAD_ERR_OK) {

  $tmp  = $_FILES['car_image']['tmp_name'];
  $name = $_FILES['car_image']['name'];

  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) {
    header("Location: /admin/cars.php?updated=0&err=" . urlencode("ไฟล์รูปต้องเป็น jpg/png/webp"));
    exit;
  }

  $bucket = 'car-images'; // ✅ จากรูปของคุณ ชื่อนี้ถูกต้องแล้ว
  $path = "cars/{$car_id}-" . time() . "." . $ext; // ✅ กัน cache ด้วยชื่อใหม่ทุกครั้ง

  $uploadUrl = SUPABASE_URL . "/storage/v1/object/{$bucket}/{$path}";
  $fileData = file_get_contents($tmp);
  $mime = mime_content_type($tmp) ?: 'application/octet-stream';

  $ch = curl_init($uploadUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . SUPABASE_SERVICE_ROLE_KEY,
    "apikey: " . SUPABASE_SERVICE_ROLE_KEY,
    "Content-Type: " . $mime,
    "x-upsert: true",
  ]);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);

  $respUp = curl_exec($ch);
  $httpUp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $errUp  = curl_error($ch);
  curl_close($ch);

  if ($errUp || $httpUp < 200 || $httpUp >= 300) {
    app_log('error', 'upload_car_image_failed', [
      'car_id' => $car_id,
      'http' => $httpUp,
      'err' => $errUp ?: null,
      'resp' => is_string($respUp) ? substr($respUp, 0, 200) : null,
    ]);
    header("Location: /admin/cars.php?updated=0&err=" . urlencode("อัปโหลดรูปไม่สำเร็จ"));
    exit;
  }

  // ✅ public url
  $car_img_final = SUPABASE_URL . "/storage/v1/object/public/{$bucket}/{$path}";
}

/* =========================
   3) Patch cars table
   ========================= */
$patchUrl = SUPABASE_URL . "/rest/v1/cars?car_id=eq.$car_id";

$data = [
  "car_name" => $car_name,
  "car_brand" => $car_brand,
  "car_model" => $car_model,
  "car_status" => $car_status,
  "car_price_per_day" => $car_price_per_day,
];

// ✅ คงรูปเดิมไว้เสมอ (ถ้ามีค่า) / หรือถ้าอัปโหลดใหม่ก็จะเป็นค่าใหม่
if ($car_img_final !== '') {
  $data["car_img"] = $car_img_final;
}

$payload = json_encode($data, JSON_UNESCAPED_UNICODE);

[$http, $resp, $err] = sb_request(
  'PATCH',
  $patchUrl,
  array_merge(sb_service_headers(), ['Prefer: return=minimal']),
  $payload
);

if ($err || $http < 200 || $http >= 300) {
  app_log('error', 'patch_car_failed', [
    'car_id' => $car_id,
    'http' => $http,
    'err' => $err ?: null,
    'resp' => is_string($resp) ? substr($resp, 0, 200) : null,
  ]);
  header("Location: /admin/cars.php?updated=0&err=" . urlencode("บันทึกไม่สำเร็จ"));
  exit;
}

header("Location: /admin/cars.php?updated=1");
exit;
