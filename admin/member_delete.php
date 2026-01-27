<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/supabase.php';

function go($k,$v){ header("Location: members.php?$k=" . urlencode($v)); exit; }

if (empty($_SESSION['cust_id']) || ($_SESSION['cust_role'] ?? '') !== 'admin') {
  header("Location: /login.php?err=" . urlencode("ต้องเป็นแอดมินเท่านั้น"));
  exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) go('err', 'ไม่พบ id');

if ((int)($_SESSION['cust_id']) === $id) {
  go('err', 'ห้ามลบบัญชีตัวเอง');
}

// 1) ดึง auth_user_id ก่อน
$select = rawurlencode("cust_id,auth_user_id");
$getUrl = SUPABASE_URL . "/rest/v1/customer?select={$select}&cust_id=eq.$id&limit=1";
[$h,$r,$e] = sb_request('GET', $getUrl, sb_service_headers());
$rows = json_decode($r, true) ?: [];
$auth_user_id = $rows[0]['auth_user_id'] ?? '';

if (empty($rows)) go('err', 'ไม่พบสมาชิก');

// 2) ลบในตาราง customer
$delUrl = SUPABASE_URL . "/rest/v1/customer?cust_id=eq.$id";
[$hd,$rd,$ed] = sb_request('DELETE', $delUrl, sb_service_headers());

if ($ed || $hd < 200 || $hd >= 300) {
  go('err', 'ลบ customer ไม่สำเร็จ: ' . ($ed ?: $rd));
}

// 3) (พยายาม) ลบ auth user ด้วย admin api (ต้องใช้ service role)
if ($auth_user_id) {
  $authDel = SUPABASE_URL . "/auth/v1/admin/users/" . rawurlencode($auth_user_id);
  sb_request('DELETE', $authDel, [
    'apikey: ' . SUPABASE_SERVICE_ROLE_KEY,
    'Authorization: Bearer ' . SUPABASE_SERVICE_ROLE_KEY
  ]);
  // ถ้าลบ auth ไม่ผ่านก็ไม่ทำให้ล้ม (กันระบบพัง)
}

go('msg', 'ลบสมาชิกเรียบร้อยแล้ว');
