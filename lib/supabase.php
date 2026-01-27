<?php
require_once __DIR__ . '/../config.php';

/**
 * ✅ Simple structured logger for Render
 * - Logs as JSON (easy to search)
 * - Masks sensitive values
 */
function app_log(string $level, string $msg, array $ctx = []): void {
  // Ensure session for correlation id (optional)
  if (session_status() !== PHP_SESSION_ACTIVE) @session_start();

  // Create a lightweight request id (per session/request)
  $req = $_SERVER['HTTP_X_REQUEST_ID'] ?? ($_SESSION['req_id'] ?? null);
  if (!$req) {
    try {
      $req = bin2hex(random_bytes(4));
    } catch (\Throwable $e) {
      $req = substr(md5(uniqid('', true)), 0, 8);
    }
    $_SESSION['req_id'] = $req;
  }

  // Mask sensitive context keys
  $sensitiveKeys = ['Authorization', 'apikey', 'token', 'access_token', 'sb_token', 'password', 'cust_pass', 'SUPABASE_SERVICE_ROLE_KEY'];
  foreach ($ctx as $k => $v) {
    if (in_array($k, $sensitiveKeys, true)) {
      $ctx[$k] = '[REDACTED]';
      continue;
    }
    // Mask obvious secrets if accidentally passed
    if (is_string($v) && (stripos($v, 'Bearer ') !== false)) {
      $ctx[$k] = 'Bearer [REDACTED]';
    }
  }

  $payload = [
    'ts'    => date('Y-m-d H:i:s'),
    'level' => strtoupper($level),
    'req'   => $req,
    'msg'   => $msg,
    'path'  => $_SERVER['REQUEST_URI'] ?? null,
    'ctx'   => $ctx
  ];

  error_log(json_encode($payload, JSON_UNESCAPED_UNICODE));
}

/**
 * ✅ Supabase REST request helper
 * - Adds timeouts for Render
 * - Logs method/url/http/ms (without secrets)
 */
function sb_request($method, $url, $headers = [], $body = null) {
  $t0 = microtime(true);

  $ch = curl_init($url);
  $defaultHeaders = ['Content-Type: application/json'];
  $all = array_merge($defaultHeaders, $headers);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $all);

  // ✅ Prevent hanging requests (important on Render)
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // seconds
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);        // seconds

  if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

  $resp = curl_exec($ch);
  $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err  = curl_error($ch);
  curl_close($ch);

  $ms = (int)round((microtime(true) - $t0) * 1000);

  // ✅ Log request summary (safe)
  $lvl = ($err || $http < 200 || $http >= 300) ? 'error' : 'info';
  app_log($lvl, 'sb_request', [
    'method' => $method,
    // remove query strings that could include sensitive info (rare but safe)
    'url'    => strtok($url, '?'),
    'http'   => $http,
    'ms'     => $ms,
    'curl_err' => $err ?: null
  ]);

  return [$http, $resp, $err];
}

function sb_service_headers() {
  return [
    'apikey: ' . SUPABASE_SERVICE_ROLE_KEY,
    'Authorization: Bearer ' . SUPABASE_SERVICE_ROLE_KEY
  ];
}

function sb_anon_headers() {
  return [
    'apikey: ' . SUPABASE_ANON_KEY,
    'Authorization: Bearer ' . SUPABASE_ANON_KEY
  ];
}

function sb_auth_headers_from_session(string $fallback = 'anon') {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();

  // ✅ รองรับหลายชื่อ token (กันพลาด)
  $token = $_SESSION['access_token'] ?? $_SESSION['sb_token'] ?? '';

  if (!empty($token)) {
    return [
      'apikey: ' . SUPABASE_ANON_KEY,
      'Authorization: Bearer ' . $token
    ];
  }

  if ($fallback === 'service') return sb_service_headers();
  return sb_anon_headers();
}

/* ✅ ใช้ cust_id เป็นตัวเช็ค login */
function require_login() {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();

  if (empty($_SESSION['cust_id'])) {
    $next = $_SERVER['REQUEST_URI'] ?? '/';
    header('Location: /login.php?next=' . urlencode($next));
    exit;
  }
}

/* ✅ role */
function is_admin() {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  return (($_SESSION['cust_role'] ?? '') === 'admin');
}

function require_admin() {
  require_login();
  if (!is_admin()) {
    http_response_code(403);
    echo "Forbidden (admin only)";
    exit;
  }
}
