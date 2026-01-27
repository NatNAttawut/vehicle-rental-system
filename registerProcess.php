<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/supabase.php';


function back_err(string $msg) {
  header("Location: /register.php?err=" . urlencode($msg));
  exit;
}

$username   = trim($_POST['username'] ?? '');
$password   = (string)($_POST['password'] ?? '');
$email      = trim($_POST['email'] ?? '');
$fullname   = trim($_POST['fullname'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$house      = trim($_POST['house'] ?? '');
$alley      = trim($_POST['alley'] ?? ''); // ไม่บังคับ
$province   = trim($_POST['province'] ?? '');
$prefecture = trim($_POST['district'] ?? '');     // อำเภอ
$district   = trim($_POST['subdistrict'] ?? '');  // ตำบล
$postcode   = trim($_POST['zipcode'] ?? '');

if ($username === '' || $password === '' || $email === '' || $phone === '' || $house === '' || $province === '' || $prefecture === '' || $district === '' || $postcode === '') {
  back_err("กรอกข้อมูลไม่ครบ");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  back_err("รูปแบบอีเมลไม่ถูกต้อง");
}

if (strlen($password) < 6) {
  back_err("รหัสผ่านต้องยาวอย่างน้อย 6 ตัวอักษร");
}

if ($fullname === '') {
  back_err("กรุณากรอกชื่อ-นามสกุล");
}

// 1) สมัคร Supabase Auth
$signupUrl = SUPABASE_URL . "/auth/v1/signup";
$signupPayload = json_encode([
  "email"    => $email,
  "password" => $password,
  "data"     => [
    "username" => $username
  ]
]);

[$hs, $rs, $es] = sb_request('POST', $signupUrl, [
  'apikey: ' . SUPABASE_ANON_KEY,
  'Content-Type: application/json'
], $signupPayload);

if ($es) back_err("cURL error: $es");

if ($hs < 200 || $hs >= 300) {
  back_err("สมัครไม่สำเร็จ มี Email นี้แล้ว ");
}

$auth = json_decode($rs, true) ?: [];
$auth_user_id = $auth['user']['id'] ?? '';
if ($auth_user_id === '') {
  back_err("สมัครสำเร็จ แต่ไม่พบ auth user id");
}

// 2) อัปโหลดรูป (ถ้ามี)
$pictureUrl = null;
if (!empty($_FILES['picture']) && ($_FILES['picture']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {

  if (($_FILES['picture']['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
    back_err("อัปโหลดรูปไม่สำเร็จ");
  }

  $tmp = $_FILES['picture']['tmp_name'];
  $size = (int)($_FILES['picture']['size'] ?? 0);
  if ($size > 2 * 1024 * 1024) {
    back_err("ไฟล์รูปใหญ่เกิน 2MB");
  }

  $ext = strtolower(pathinfo($_FILES['picture']['name'] ?? '', PATHINFO_EXTENSION));
  if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) {
    back_err("ไฟล์รูปต้องเป็น jpg/png/webp เท่านั้น");
  }

  $bucket = "profiles";
  $path = "profile_" . $auth_user_id . "_" . time() . "." . $ext;

  $uploadUrl = SUPABASE_URL . "/storage/v1/object/" . rawurlencode($bucket) . "/" . rawurlencode($path);
  $bin = file_get_contents($tmp);

  $mime = "application/octet-stream";
  if ($ext === 'jpg' || $ext === 'jpeg') $mime = "image/jpeg";
  if ($ext === 'png') $mime = "image/png";
  if ($ext === 'webp') $mime = "image/webp";

  [$hu, $ru, $eu] = sb_request('POST', $uploadUrl, [
    'Authorization: Bearer ' . SUPABASE_SERVICE_ROLE_KEY,
    'apikey: ' . SUPABASE_SERVICE_ROLE_KEY,
    'Content-Type: ' . $mime,
    'x-upsert: true'
  ], $bin);

  if ($eu) back_err("อัปโหลดรูปไม่สำเร็จ: $eu");
  if ($hu < 200 || $hu >= 300) {
    back_err("อัปโหลดรูปไม่สำเร็จ: " . $ru . " (แนะนำ: เช็คว่ามี bucket ชื่อ profiles และตั้ง public แล้ว)");
  }

  $pictureUrl = SUPABASE_URL . "/storage/v1/object/public/{$bucket}/{$path}";
}

// 3) Insert ลง customer
$customerUrl = SUPABASE_URL . "/rest/v1/customer";
$payload = [
  "auth_user_id"   => $auth_user_id,
 'cust_uname'      => $_POST['username'],
  'cust_email'      => $_POST['email'],
  'cust_name'       => $_POST['fullname'],
  'cust_phone'      => $_POST['phone'],
  'cust_house'      => $_POST['house'],
  'cust_province'   => $_POST['province'],
  'cust_prefecture' => $_POST['district'],   
  'cust_district'   => $_POST['subdistrict'],
  'cust_postcode'   => $_POST['zipcode'],    
  'cust_role'       => 'user',
  'cust_status'     => 'active'
];

if ($pictureUrl) $payload["cust_picture"] = $pictureUrl;

[$hc, $rc, $ec] = sb_request('POST', $customerUrl, array_merge(
  sb_service_headers(),
  ['Prefer: return=representation']
), json_encode($payload));

if ($ec) back_err("บันทึก customer ไม่สำเร็จ: $ec");

if ($hc < 200 || $hc >= 300) {
  back_err("บันทึก customer ไม่สำเร็จ: " . $rc);
}

// สมัครสำเร็จ -> ไปหน้า login พร้อมแจ้ง
header("Location: /login.php?registered=1");
exit;
