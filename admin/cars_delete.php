<?php
require_once __DIR__ . '/../lib/supabase.php';
require_admin();

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$car_id = (int)($_POST['car_id'] ?? 0);
if ($car_id <= 0) {
  header("Location: /admin/cars.php?deleted=0&reason=bad_id");
  exit;
}

$deleteUrl = SUPABASE_URL . "/rest/v1/cars?car_id=eq.$car_id";

[$http, $resp, $err] = sb_request(
  'DELETE',
  $deleteUrl,
  array_merge(sb_service_headers(), ['Prefer: return=minimal']),
  null
);

if ($err) {
  header("Location: /admin/cars.php?deleted=0&reason=err");
  exit;
}

if ($http >= 200 && $http < 300) {
  header("Location: /admin/cars.php?deleted=1");
  exit;
}

header("Location: /admin/cars.php?deleted=0&reason=db");
exit;
