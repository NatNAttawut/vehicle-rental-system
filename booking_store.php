<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/supabase.php';

require_login(); // เรียกหลัง start session เท่านั้น


// รับค่าจากฟอร์ม
$car_id     = (int)($_POST['car_id'] ?? 0);
$book_start = $_POST['book_start'] ?? '';
$book_exp   = $_POST['book_exp'] ?? '';

if ($car_id <= 0 || $book_start === '' || $book_exp === '') {
  exit("กรอกข้อมูลไม่ครบ");
}

// ------------------------------
// STEP 1) ดึง uid จาก access_token (auth.uid())
// ------------------------------
if (empty($_SESSION['sb_token'])) {
  header("Location: /login.php?err=" . urlencode("กรุณาเข้าสู่ระบบใหม่"));
  exit;
}

$headersAuth = [
  'Authorization: Bearer ' . $_SESSION['sb_token'],
  'apikey: ' . SUPABASE_ANON_KEY,
  'Content-Type: application/json'
];


$meUrl = SUPABASE_URL . "/auth/v1/user";
[$hm, $rm, $em] = sb_request('GET', $meUrl, $headersAuth);

if ($em) exit("cURL error: " . htmlspecialchars($em));
if ($hm < 200 || $hm >= 300) exit("อ่านข้อมูลผู้ใช้ไม่สำเร็จ: " . htmlspecialchars($rm));

$me  = json_decode($rm, true);
$uid = $me['id'] ?? '';
if ($uid === '') exit("ไม่พบ uid ของผู้ใช้");

// ------------------------------
// STEP 2) map uid -> cust_id (ใช้ service role)
// ------------------------------
$cuUrl = SUPABASE_URL
  . "/rest/v1/customer?select=cust_id,cust_role"
  . "&auth_user_id=eq." . rawurlencode($uid)
  . "&limit=1";

[$hc, $rc, $ec] = sb_request('GET', $cuUrl, sb_service_headers());

if ($ec) exit("cURL error: " . htmlspecialchars($ec));
$rows = json_decode($rc, true);

if ($hc < 200 || $hc >= 300 || empty($rows)) {
  exit("ไม่พบ customer ที่ผูกกับบัญชีนี้ (auth_user_id ไม่ตรง)");
}

$cust_id = (int)($rows[0]['cust_id'] ?? 0);
if ($cust_id <= 0) exit("cust_id ไม่ถูกต้อง");

$_SESSION['cust_id'] = $cust_id;
$_SESSION['role']    = $rows[0]['cust_role'] ?? ($_SESSION['role'] ?? 'user');

// ------------------------------
// STEP 3) เช็ควันชน (service role)
// ------------------------------
$checkUrl = SUPABASE_URL . "/rest/v1/booking?select=book_id"
  . "&car_id=eq.$car_id"
  . "&book_status=in.(pending,approved,active)"
  . "&book_start=lte." . rawurlencode($book_exp)
  . "&book_exp=gte." . rawurlencode($book_start)
  . "&limit=1";

[$hck, $rck, $eck] = sb_request('GET', $checkUrl, sb_service_headers());
if ($eck) exit("cURL error: " . htmlspecialchars($eck));

if ($hck >= 200 && $hck < 300) {
  $conflict = json_decode($rck, true);
  if (!empty($conflict)) {
    header("Location: /booking_create.php?car_id=$car_id&err=conflict");
    exit;
  }
} else {
  exit("ตรวจสอบวันชนไม่สำเร็จ: " . htmlspecialchars($rck));
}

// ------------------------------
// STEP 3.5) ตรวจสถานะรถ (กันคนยิง API จองรถที่ไม่ available)
// ------------------------------
$carUrl = SUPABASE_URL . "/rest/v1/cars?select=car_status,car_price_per_day&car_id=eq.$car_id&limit=1";
[$hcar, $rcar, $ecar] = sb_request('GET', $carUrl, sb_service_headers());
if ($ecar) exit("cURL error: " . htmlspecialchars($ecar));

$carRows = json_decode($rcar, true) ?: [];
$car = $carRows[0] ?? null;

if (!$car) exit("ไม่พบข้อมูลรถ");
if (($car['car_status'] ?? '') !== 'available') {
  header("Location: /cars.php?err=car_unavailable");
  exit;
}

$pricePerDay = (float)($car['car_price_per_day'] ?? 0);

// คำนวณจำนวนวัน (ขั้นต่ำ 1 วัน)
$ds = strtotime($book_start);
$de = strtotime($book_exp);
if ($ds === false || $de === false) exit("รูปแบบวันที่ไม่ถูกต้อง");
if ($de < $ds) exit("วันสิ้นสุดต้องไม่ก่อนวันเริ่ม");

$total_days = (int)floor(($de - $ds) / 86400) + 1;
if ($total_days < 1) $total_days = 1;

$total_amount = $total_days * $pricePerDay;

// ------------------------------
// STEP 4) INSERT booking (JWT user -> ให้ตรง policy ทาง A)
// ------------------------------
$insertUrl = SUPABASE_URL . "/rest/v1/booking";

$payload = json_encode([
  "car_id"       => $car_id,
  "cust_id"      => $cust_id,
  "book_start"   => $book_start,
  "book_exp"     => $book_exp,
  "book_status"  => "pending",
  "price_per_day"=> $pricePerDay,
  "total_days"   => $total_days,
  "total_amount" => $total_amount
]);

[$http, $resp, $err] = sb_request(
  'POST',
  $insertUrl,
  array_merge($headersAuth, [
    'Prefer: return=representation'
  ]),
  $payload
);

if ($err) exit("cURL error: " . htmlspecialchars($err));

if ($http >= 200 && $http < 300) {
  $ins = json_decode($resp, true) ?: [];
  $book_id = (int)($ins[0]['book_id'] ?? 0);

  // ทำให้รถเป็น unavailable ทันที (service role กัน RLS cars)
  $patchCarUrl = SUPABASE_URL . "/rest/v1/cars?car_id=eq.$car_id";
  $patchPayload = json_encode(["car_status" => "unavailable"]);
  sb_request('PATCH', $patchCarUrl, array_merge(sb_service_headers(), ['Prefer: return=minimal']), $patchPayload);

  // ไปหน้าใบยืนยัน (ให้ปริ้นได้)
  if ($book_id > 0) {
    header("Location: /booking_receipt.php?book_id=$book_id");
    exit;
  }

  header("Location: /my_bookings.php?ok=1");
  exit;
}

http_response_code($http);
echo "จองไม่สำเร็จ: " . htmlspecialchars($resp);
