<?php
// คัดลอกไฟล์นี้เป็น config.php แล้วใส่ค่าให้ครบ
define('SUPABASE_URL', 'https://miufcovolnbawvqxtwox.supabase.co');
define('SUPABASE_ANON_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im1pdWZjb3ZvbG5iYXd2cXh0d294Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjkwNjQ4OTUsImV4cCI6MjA4NDY0MDg5NX0._zoTeaoqH9_gIlFkTbdN81pxDMAoyP1Lv10Lz2EOkYw');
// ใช้สำหรับงานฝั่ง server เท่านั้น (ห้ามเปิดเผย)
define('SUPABASE_SERVICE_ROLE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im1pdWZjb3ZvbG5iYXd2cXh0d294Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc2OTA2NDg5NSwiZXhwIjoyMDg0NjQwODk1fQ.0z_ENpkSdo_XluE-USFlwxOjYZeGcUR7weY9dRHef6Q');

// Bucket ชื่อ
define('BUCKET_CAR_IMAGES', 'car-images');
define('BUCKET_CUST_PICTURE', 'profiles');
?>