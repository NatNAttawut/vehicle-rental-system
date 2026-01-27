<?php
require_once __DIR__ . '/../lib/supabase.php';
require_admin();

$car_name  = trim($_POST['car_name'] ?? '');
$car_num   = trim($_POST['car_num'] ?? '');
$car_regis = trim($_POST['car_regis'] ?? '');
$car_brand = trim($_POST['car_brand'] ?? '');
$car_model = trim($_POST['car_model'] ?? '');
$car_status = trim($_POST['car_status'] ?? 'available');
$car_type   = trim($_POST['car_type'] ?? 'car');

$car_price_per_day = (float)($_POST['car_price_per_day'] ?? 0);

if ($car_name === '') {
  header("Location: /admin/cars_add.php?err=" . urlencode("ชื่อรถห้ามว่าง"));
  exit;
}

if (!in_array($car_type, ['car','motorcycle'], true)) {
  header("Location: /admin/cars_add.php?err=" . urlencode("ประเภทรถไม่ถูกต้อง"));
  exit;
}

if (!in_array($car_status, ['available','unavailable','maintenance'], true)) {
  $car_status = 'available';
}

$publicImgUrl = null;

/**
 * อัปโหลดรูป (ถ้ามี)
 */
if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] !== UPLOAD_ERR_NO_FILE) {
  if ($_FILES['car_image']['error'] !== UPLOAD_ERR_OK) {
    header("Location: /admin/cars_add.php?err=" . urlencode("อัปโหลดรูปไม่สำเร็จ"));
    exit;
  }

  $tmp  = $_FILES['car_image']['tmp_name'];
  $orig = $_FILES['car_image']['name'];
  $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));

  if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) {
    header("Location: /admin/cars_add.php?err=" . urlencode("ไฟล์รูปไม่รองรับ (jpg/jpeg/png/webp)"));
    exit;
  }

  $bin = file_get_contents($tmp);
  if ($bin === false) {
    header("Location: /admin/cars_add.php?err=" . urlencode("อ่านไฟล์รูปไม่สำเร็จ"));
    exit;
  }

  $bucket   = BUCKET_CAR_IMAGES;
  $filename = "cars/" . date("Ymd_His") . "_" . bin2hex(random_bytes(4)) . "." . $ext;
  $uploadUrl = SUPABASE_URL . "/storage/v1/object/$bucket/$filename";

  [$httpU, $respU, $errU] = sb_request(
    'POST',
    $uploadUrl,
    array_merge(sb_service_headers(), [
      "Content-Type: " . ($_FILES['car_image']['type'] ?: "application/octet-stream"),
      "x-upsert: true"
    ]),
    $bin
  );

  if ($errU) {
    header("Location: /admin/cars_add.php?err=" . urlencode("Upload error: $errU"));
    exit;
  }
  if ($httpU < 200 || $httpU >= 300) {
    header("Location: /admin/cars_add.php?err=" . urlencode("อัปโหลด Storage ไม่ผ่าน: $respU"));
    exit;
  }

  $publicImgUrl = SUPABASE_URL . "/storage/v1/object/public/$bucket/$filename";
}

/**
 * INSERT cars (สำคัญ: ใช้ service role เพื่อไม่ติด RLS)
 */
$payload = [
  "car_name" => $car_name,
  "car_num" => $car_num,
  "car_regis" => $car_regis,
  "car_brand" => $car_brand,
  "car_model" => $car_model,
  "car_status" => $car_status,
  "car_type" => $car_type,
  "car_price_per_day" => $car_price_per_day,
];

// ใส่รูปเฉพาะตอนมีรูป
if ($publicImgUrl !== null) {
  $payload["car_img"] = $publicImgUrl;
}

$url = SUPABASE_URL . "/rest/v1/cars";

[$http, $resp, $err] = sb_request(
  'POST',
  $url,
  array_merge(sb_service_headers(), ['Prefer: return=minimal']),
  json_encode($payload)
);

if ($err) {
  header("Location: /admin/cars_add.php?err=" . urlencode("DB error: $err"));
  exit;
}

if ($http >= 200 && $http < 300) {
  header("Location: /admin/cars.php?added=1");
  exit;
}

header("Location: /admin/cars_add.php?err=" . urlencode("เพิ่มรถไม่สำเร็จ: $resp"));
exit;
