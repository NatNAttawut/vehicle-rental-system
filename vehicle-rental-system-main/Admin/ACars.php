<?php include "Aheader.php"; ?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <title>จัดการรถ</title>
  <link rel="stylesheet" href="../style.css" />
</head>
<body>
  <div style="padding:20px;">
    <h2>แก้ไขข้อมูลรถ</h2>

    <button onclick="openAdd()">+ เพิ่มรถ</button>

    <table border="1" cellpadding="8" cellspacing="0" style="margin-top:10px; width:100%;">
      <thead>
        <tr>
          <th>ชื่อรถ</th>
          <th>หมายเลขรถ</th>
          <th>เลขทะเบียนรถ</th>
          <th>ยี่ห้อ</th>
          <th>รุ่น</th>
          <th>สถานะ</th>
          <th>รูป</th>
          <th>ดำเนินการ</th>
        </tr>
      </thead>
      <tbody id="carRows"></tbody>
    </table>
  </div>

  <!-- Modal แก้/เพิ่ม -->
  <div id="carModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5);">
    <div style="background:#fff; max-width:700px; margin:60px auto; padding:20px;">
      <h3 id="modalTitle">เพิ่ม/แก้ไขข้อมูลรถ</h3>

      <input type="hidden" id="car_id">

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        <div>
          <label>ชื่อรถ</label>
          <input id="car_name" style="width:100%;">
        </div>
        <div>
          <label>หมายเลขรถ</label>
          <input id="car_num" style="width:100%;">
        </div>
        <div>
          <label>เลขทะเบียนรถ</label>
          <input id="car_regis" style="width:100%;">
        </div>
        <div>
          <label>ยี่ห้อ</label>
          <input id="car_brand" style="width:100%;">
        </div>
        <div>
          <label>รุ่น</label>
          <input id="car_model" style="width:100%;">
        </div>
        <div>
          <label>สถานะ</label>
          <select id="car_status" style="width:100%;">
            <option value="available">ว่าง</option>
            <option value="unavailable">ไม่ว่าง</option>
            <option value="maintenance">ไม่พร้อมใช้งาน</option>
          </select>
        </div>

        <div style="grid-column:1 / -1;">
          <label>รูป (URL หรือ path)</label>
          <input id="car_img" style="width:100%;">
        </div>
      </div>

      <div style="margin-top:15px; display:flex; gap:10px; justify-content:flex-end;">
        <button onclick="saveCar()">บันทึก</button>
        <button onclick="closeModal()">ยกเลิก</button>
      </div>
    </div>
  </div>

  <script src="../assets/js/admin-cars.js"></script>
</body>
</html>
