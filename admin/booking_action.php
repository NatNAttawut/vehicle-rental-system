<?php
require_once __DIR__ . '/../lib/supabase.php';
require_admin();

$book_id = (int)($_POST['book_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($book_id <= 0) exit("book_id ไม่ถูกต้อง");

$map = [
  'approve' => 'approved',
  'reject' => 'rejected',
  'set_active' => 'active',
  'complete' => 'completed',
  'cancel' => 'cancelled',
];

if (!isset($map[$action])) exit("action ไม่ถูกต้อง");
$newStatus = $map[$action];

// โหลด booking เพื่อรู้ car_id และสถานะรถ
$bookingUrl = SUPABASE_URL . "/rest/v1/booking?select=book_id,car_id,book_status&book_id=eq.$book_id&limit=1";
[$hb, $rb, $eb] = sb_request('GET', $bookingUrl, sb_auth_headers_from_session());
$bk = (json_decode($rb, true) ?: [])[0] ?? null;
if (!$bk) exit("ไม่พบรายการจอง");

$car_id = (int)($bk['car_id'] ?? 0);

// ถ้าจะเปลี่ยนเป็น approved/active ให้เช็คว่ารถไม่ได้ maintenance
if (in_array($newStatus, ['approved','active'], true)) {
  $carUrl = SUPABASE_URL . "/rest/v1/cars?select=car_status&car_id=eq.$car_id&limit=1";
  [$hc, $rc, $ec] = sb_request('GET', $carUrl, sb_auth_headers_from_session());
  $car = (json_decode($rc, true) ?: [])[0] ?? null;
  $carStatus = $car['car_status'] ?? '';
  if ($carStatus === 'maintenance') {
    exit("ไม่สามารถตั้งสถานะเป็น $newStatus ได้ เพราะรถอยู่ในสถานะ maintenance");
  }
}

$url = SUPABASE_URL . "/rest/v1/booking?book_id=eq.$book_id";
$payload = json_encode(["book_status" => $newStatus]);

[$http, $resp, $err] = sb_request('PATCH', $url, array_merge(sb_auth_headers_from_session(), [
  'Prefer: return=minimal'
]), $payload);

if ($err) exit("Error: " . htmlspecialchars($err));
if ($http >= 200 && $http < 300) {
  header("Location: /admin/bookings.php");
  exit;
}

http_response_code($http);
echo "อัปเดตไม่สำเร็จ: " . htmlspecialchars($resp);
