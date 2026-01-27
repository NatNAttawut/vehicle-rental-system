<?php
require_once __DIR__ . '/lib/supabase.php';
require_login();

$book_id = (int)($_POST['book_id'] ?? 0);
$cust_id = (int)($_SESSION['cust_id'] ?? 0);

if ($book_id <= 0 || $cust_id <= 0) exit("ข้อมูลไม่ถูกต้อง");

// ตรวจสอบว่าเป็นของตัวเอง + สถานะที่ยกเลิกได้
$checkUrl = SUPABASE_URL . "/rest/v1/booking?select=book_id,book_status"
  . "&book_id=eq.$book_id&cust_id=eq.$cust_id&limit=1";

[$hc, $rc, $ec] = sb_request('GET', $checkUrl, sb_auth_headers_from_session());
$row = (json_decode($rc, true) ?: [])[0] ?? null;

if (!$row) exit("ไม่พบรายการจอง");
$status = $row['book_status'] ?? '';

if (!in_array($status, ['pending','approved'], true)) {
  exit("รายการนี้ยกเลิกไม่ได้ (สถานะปัจจุบัน: $status)");
}

$patchUrl = SUPABASE_URL . "/rest/v1/booking?book_id=eq.$book_id";
$payload = json_encode(["book_status" => "cancelled"]);

[$hp, $rp, $ep] = sb_request('PATCH', $patchUrl, array_merge(sb_auth_headers_from_session(), [
  "Prefer: return=minimal"
]), $payload);

if ($ep) exit("Error: " . htmlspecialchars($ep));

if ($hp >= 200 && $hp < 300) {
  header("Location: /my_bookings.php?cancelled=1");
  exit;
}

http_response_code($hp);
echo "ยกเลิกไม่สำเร็จ: " . htmlspecialchars($rp);
