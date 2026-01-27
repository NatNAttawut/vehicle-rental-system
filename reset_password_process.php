<?php
require_once __DIR__ . '/lib/supabase.php';

$selector  = $_POST['selector'] ?? '';
$validator = $_POST['validator'] ?? '';
$pass1 = $_POST['password'] ?? '';
$pass2 = $_POST['password2'] ?? '';

if ($pass1 === '' || $pass1 !== $pass2) {
  die("รหัสผ่านไม่ตรงกัน");
}

/* 1) ดึง token */
$url = SUPABASE_URL . "/rest/v1/password_resets?selector=eq." . urlencode($selector) . "&used=eq.false&select=*";
[$http, $resp] = sb_request('GET', $url, sb_service_headers());
$rows = json_decode($resp, true);

if ($http < 200 || $http >= 300 || empty($rows)) {
  die("ลิงก์ไม่ถูกต้อง");
}

$r = $rows[0];
if (strtotime($r['expires_at']) < time()) {
  die("ลิงก์หมดอายุ");
}

/* 2) ตรวจ validator */
if (!password_verify($validator, $r['token_hash'])) {
  die("ลิงก์ไม่ถูกต้อง");
}

/* 3) อัปเดตรหัสผ่าน */
$newHash = password_hash($pass1, PASSWORD_DEFAULT);

$updUserUrl = SUPABASE_URL . "/rest/v1/customer?cust_id=eq." . $r['cust_id'];
sb_request(
  'PATCH',
  $updUserUrl,
  array_merge(sb_service_headers(), ['Prefer: return=minimal']),
  json_encode(["cust_pass" => $newHash])
);

/* 4) mark token ว่าใช้แล้ว */
$updResetUrl = SUPABASE_URL . "/rest/v1/password_resets?id=eq." . $r['id'];
sb_request(
  'PATCH',
  $updResetUrl,
  array_merge(sb_service_headers(), ['Prefer: return=minimal']),
  json_encode(["used" => true])
);

header("Location: /login.php?reset=1");
exit;
