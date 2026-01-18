<?php include "Aheader.php"; ?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <title>ยืนยันการจอง</title>
  <link rel="stylesheet" href="../style.css" />
</head>
<body>
  <div style="padding:20px;">
    <h2>ยืนยันการจองยานพาหนะ</h2>

    <table border="1" cellpadding="8" cellspacing="0" style="margin-top:10px; width:100%;">
      <thead>
        <tr>
          <th>Booking ID</th>
          <th>Car ID</th>
          <th>Cust ID</th>
          <th>เริ่ม</th>
          <th>สิ้นสุด</th>
          <th>สถานะ</th>
          <th>การยืนยัน</th>
        </tr>
      </thead>
      <tbody id="bookingRows"></tbody>
    </table>
  </div>

  <script src="../assets/js/admin-bookings.js"></script>
</body>
</html>
