<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/supabase.php';

function back_err(string $msg) {
  header("Location: /login.php?err=" . urlencode($msg));
  exit;
}

$identifier = trim($_POST['identifier'] ?? '');
$password   = (string)($_POST['password'] ?? '');

if ($identifier === '' || $password === '') {
  back_err("กรุณากรอกอีเมลหรือ Username และรหัสผ่าน");
}

/* =========================
   0) แปลง identifier -> email
   ========================= */
$email = $identifier;

if (!filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
  // เป็น username → ไปหา email จาก customer
  $u = rawurlencode($identifier);
  $findUrl = SUPABASE_URL . "/rest/v1/customer"
    . "?select=cust_email"
    . "&cust_uname=eq.$u"
    . "&limit=1";

  [$h1, $r1, $e1] = sb_request('GET', $findUrl, sb_service_headers());
  if ($e1) back_err("ดึงข้อมูลผู้ใช้ไม่สำเร็จ");

  $rows = json_decode($r1, true) ?: [];
  if (empty($rows[0]['cust_email'])) {
    back_err("ไม่พบบัญชีผู้ใช้นี้");
  }
  $email = trim($rows[0]['cust_email']);
}

/* =========================
   1) Login Supabase Auth
   ========================= */
$loginUrl = SUPABASE_URL . "/auth/v1/token?grant_type=password";
$payload = json_encode([
  "email"    => $email,
  "password" => $password
]);

[$http, $resp, $err] = sb_request('POST', $loginUrl, [
  'apikey: ' . SUPABASE_ANON_KEY,
  'Content-Type: application/json'
], $payload);

if ($err) back_err("cURL error: $err");
if ($http < 200 || $http >= 300) {
  back_err("อีเมล/Username หรือรหัสผ่านไม่ถูกต้อง");
}

$data = json_decode($resp, true) ?: [];

$auth_user_id = $data['user']['id'] ?? '';
$access_token = $data['access_token'] ?? '';

if ($auth_user_id === '' || $access_token === '') {
  back_err("ไม่สามารถเข้าสู่ระบบได้ (token ไม่สมบูรณ์)");
}

if ($auth_user_id === '') back_err("ไม่พบข้อมูลผู้ใช้ใน Auth");

/* =========================
   2) ดึงข้อมูล customer (ให้ครบ)
   ========================= */
$customerUrl = SUPABASE_URL . "/rest/v1/customer"
  . "?select=cust_id,cust_name,cust_uname,cust_picture,cust_role,cust_status"
  . "&auth_user_id=eq.$auth_user_id"
  . "&limit=1";

[$hc, $rc, $ec] = sb_request('GET', $customerUrl, sb_service_headers());
if ($ec) back_err("ดึงข้อมูลลูกค้าไม่สำเร็จ");

$rows = json_decode($rc, true) ?: [];
$customer = $rows[0] ?? null;

if (!$customer) back_err("ไม่พบข้อมูลลูกค้าในระบบ");

if (($customer['cust_status'] ?? '') !== 'active') {
  back_err("บัญชีถูกระงับการใช้งาน");
}

/* =========================
   3) เก็บ session (สำคัญมาก)
   ========================= */
$_SESSION['cust_id']      = $customer['cust_id'];
$_SESSION['cust_name']    = $customer['cust_name'];
$_SESSION['cust_uname']   = $customer['cust_uname'];
$_SESSION['cust_picture'] = $customer['cust_picture'] ?? '';
$_SESSION['cust_role']    = $customer['cust_role'];

$_SESSION['access_token'] = $access_token; // ✅ เพิ่มบรรทัดนี้
$_SESSION['sb_token']     = $access_token; // จะเก็บซ้ำก็ได้




/* =========================
   4) ไปหน้าแรก
   ========================= */
header("Location: /index.php");
exit;
