<?php
require_once __DIR__ . '/../lib/supabase.php';
require_admin();

$car_id = (int)($_POST['car_id'] ?? 0);
$car_status = trim($_POST['car_status'] ?? '');

if ($car_id <= 0) exit("car_id ไม่ถูกต้อง");
if (!in_array($car_status, ['available','unavailable','maintenance'], true)) exit("สถานะไม่ถูกต้อง");

// ถ้าจะตั้ง available แต่มี booking approved/active อยู่ ให้กันไว้
if ($car_status === 'available') {
  $checkUrl = SUPABASE_URL . "/rest/v1/booking?select=book_id"
    . "&car_id=eq.$car_id"
    . "&book_status=in.(approved,active)"
    . "&limit=1";
  [$hc, $rc, $ec] = sb_request('GET', $checkUrl, sb_auth_headers_from_session());
  $rows = json_decode($rc, true) ?: [];
  if (!empty($rows)) {
    exit("ไม่สามารถตั้ง available ได้ เพราะมี booking ที่ approved/active อยู่");
  }
}

$url = SUPABASE_URL . "/rest/v1/cars?car_id=eq.$car_id";
$payload = json_encode(["car_status" => $car_status]);

[$http, $resp, $err] = sb_request('PATCH', $url, array_merge(sb_auth_headers_from_session(), [
  'Prefer: return=minimal'
]), $payload);

if ($err) exit("Error: " . htmlspecialchars($err));
if ($http >= 200 && $http < 300) {
  header("Location: /admin/cars.php");
  exit;
}

http_response_code($http);
echo "อัปเดตไม่สำเร็จ: " . htmlspecialchars($resp);
