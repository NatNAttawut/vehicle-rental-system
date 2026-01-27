<?php
require_once __DIR__ . '/lib/supabase.php';

$email = trim($_POST['email'] ?? '');
if ($email === '') {
  header("Location: /forgot_password.php?err=กรุณากรอกอีเมล");
  exit;
}

/* 1) หา user จาก customer */
$url = SUPABASE_URL . "/rest/v1/customer?cust_email=eq." . urlencode($email) . "&select=cust_id,cust_email";
[$http, $resp] = sb_request('GET', $url, sb_service_headers());
$rows = json_decode($resp, true);

/* เพื่อความปลอดภัย ไม่บอกว่าอีเมลมีหรือไม่ */
if ($http < 200 || $http >= 300 || empty($rows)) {
  header("Location: /forgot_password.php?sent=1");
  exit;
}

$cust_id = $rows[0]['cust_id'];

/* 2) สร้าง token */
$selector  = bin2hex(random_bytes(8));
$validator = bin2hex(random_bytes(32));
$tokenHash = password_hash($validator, PASSWORD_DEFAULT);
$expiresAt = gmdate('c', time() + 60 * 30); // 30 นาที

/* 3) บันทึกลง password_resets */
$insertUrl = SUPABASE_URL . "/rest/v1/password_resets";
$payload = json_encode([
  "cust_id" => $cust_id,
  "email" => $email,
  "selector" => $selector,
  "token_hash" => $tokenHash,
  "expires_at" => $expiresAt,
  "used" => false
]);

sb_request(
  'POST',
  $insertUrl,
  array_merge(sb_service_headers(), ['Prefer: return=minimal']),
  $payload
);

/* 4) ส่งอีเมล (ตัวอย่าง mail ธรรมดา) */
$base = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
$link = $base . "/reset_password.php?selector=$selector&validator=$validator";

@mail(
  $email,
  "รีเซ็ตรหัสผ่าน",
  "คลิกลิงก์เพื่อรีเซ็ตรหัสผ่าน:\n$link\n\nลิงก์มีอายุ 30 นาที"
);

header("Location: /forgot_password.php?sent=1");
exit;
