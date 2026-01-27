<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/supabase.php';

if (empty($_SESSION['cust_id'])) {
  header("Location: /login.php");
  exit;
}

function back_err($m){
  header("Location: /profile_edit.php?err=" . urlencode($m));
  exit;
}

$cust_id = $_SESSION['cust_id'];

$name  = trim($_POST['cust_name'] ?? '');
$phone = trim($_POST['cust_phone'] ?? '');
$house = trim($_POST['cust_house'] ?? '');
$prov  = trim($_POST['cust_province'] ?? '');
$pref  = trim($_POST['cust_prefecture'] ?? '');
$dist  = trim($_POST['cust_district'] ?? '');
$post  = trim($_POST['cust_postcode'] ?? '');

if ($name==''||$phone==''||$house==''||$prov==''||$pref==''||$dist==''||$post=='') {
  back_err("กรอกข้อมูลไม่ครบ");
}

$payload = [
  'cust_name'       => $name,
  'cust_phone'      => $phone,
  'cust_house'      => $house,
  'cust_province'   => $prov,
  'cust_prefecture' => $pref,
  'cust_district'   => $dist,
  'cust_postcode'   => $post,
];

/* อัปโหลดรูป (ถ้ามี) */
if (!empty($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
  $ext = strtolower(pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION));
  if (!in_array($ext,['jpg','jpeg','png','webp'])) back_err("ไฟล์รูปไม่ถูกต้อง");

  $path = "profile_$cust_id.".$ext;
  $bin = file_get_contents($_FILES['picture']['tmp_name']);

  $uploadUrl = SUPABASE_URL . "/storage/v1/object/profiles/$path";

  [$h,$r,$e] = sb_request('POST',$uploadUrl,[
    'Authorization: Bearer '.SUPABASE_SERVICE_ROLE_KEY,
    'apikey: '.SUPABASE_SERVICE_ROLE_KEY,
    'Content-Type: application/octet-stream',
    'x-upsert: true'
  ],$bin);

  if ($h<200||$h>=300) back_err("อัปโหลดรูปไม่สำเร็จ");

  $payload['cust_picture'] =
    SUPABASE_URL."/storage/v1/object/public/profiles/$path";

  $_SESSION['cust_picture'] = $payload['cust_picture'];
}

/* update */
$url = SUPABASE_URL . "/rest/v1/customer?cust_id=eq.$cust_id";

[$h,$r,$e] = sb_request('PATCH',$url,
  array_merge(sb_service_headers(),['Prefer: return=minimal']),
  json_encode($payload)
);

if ($h<200||$h>=300) back_err("บันทึกข้อมูลไม่สำเร็จ");

$_SESSION['cust_name'] = $name;

header("Location: /profile_edit.php?msg=บันทึกข้อมูลเรียบร้อยแล้ว");
exit;
