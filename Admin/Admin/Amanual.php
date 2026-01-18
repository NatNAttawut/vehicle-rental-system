<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ฟอร์มข้อมูลรถ</title>
    <link rel="stylesheet" href="Astyle.css">
</head>
<body>

    <?php include 'Aheader.php'; ?>

    <div class="container">
        <div class="admin-form-box">
            <h3 style="margin-bottom: 20px; border-bottom: 1px solid #999; padding-bottom: 10px;">แก้ไขข้อมูลรถ</h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>ชื่อรถ</label>
                    <input type="text" id="carName" class="form-input">
                </div>
                <div class="form-group">
                    <label>หมายเลขรถ</label>
                    <input type="text" id="carIdDisplay" class="form-input">
                </div>
                <div class="form-group">
                    <label>เลขทะเบียนรถ</label>
                    <input type="text" id="plateNo" class="form-input">
                </div>
                <div class="form-group">
                    <label>ยี่ห้อรถ</label>
                    <input type="text" id="brand" class="form-input">
                </div>
                <div class="form-group">
                    <label>รุ่นรถ</label>
                    <input type="text" id="model" class="form-input">
                </div>
                <div class="form-group">
                    <label>สถานะ</label>
                    <input type="text" id="status" class="form-input" placeholder="ว่าง / ไม่ว่าง">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>รูปภาพรถ</label>
                    <input type="file" id="carImage" class="form-input" style="background:none;">
                    <small>อัพโหลดไฟล์</small>
                </div>
            </div>

            <div class="form-actions">
                <button onclick="saveCar()" class="btn-save">บันทึก</button>
                <a href="manage_cars.php" class="btn-cancel">ยกเลิก</a>
            </div>
        </div>
    </div>

    <script>
        // ตรวจสอบว่ามี ID ส่งมาไหม (ถ้ามี = แก้ไข, ถ้าไม่มี = เพิ่มใหม่)
        const urlParams = new URLSearchParams(window.location.search);
        const carId = urlParams.get('id');

        async function loadCarData() {
            if (!carId) return;

            const { data: car, error } = await supabase.from('Cars').select('*').eq('id', carId).single();
            if (car) {
                document.getElementById('carName').value = car.car_name || '';
                document.getElementById('carIdDisplay').value = car.car_id || '';
                document.getElementById('plateNo').value = car.plate_no || '';
                document.getElementById('brand').value = car.brand || '';
                document.getElementById('model').value = car.model || '';
                document.getElementById('status').value = car.status || '';
            }
        }

        async function saveCar() {
            const data = {
                car_name: document.getElementById('carName').value,
                car_id: document.getElementById('carIdDisplay').value,
                plate_no: document.getElementById('plateNo').value,
                brand: document.getElementById('brand').value,
                model: document.getElementById('model').value,
                status: document.getElementById('status').value
            };

            let error;
            if (carId) {
                // อัปเดตข้อมูลเก่า
                const res = await supabase.from('Cars').update(data).eq('id', carId);
                error = res.error;
            } else {
                // สร้างข้อมูลใหม่
                const res = await supabase.from('Cars').insert([data]);
                error = res.error;
            }

            if (!error) {
                alert('บันทึกข้อมูลเรียบร้อย');
                window.location.href = 'manage_cars.php';
            } else {
                alert('เกิดข้อผิดพลาด: ' + error.message);
            }
        }

        window.addEventListener('load', loadCarData);
    </script>
</body>
</html>