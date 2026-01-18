<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ข้อมูลส่วนตัว - มีรถหรือยัง?</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="top-header">
        <div class="logo">
            <a href="index.html">
                <img src="/IMG/LogoCodecraft.png" alt="Logo">
            </a>
        </div>
        <h1>มีรถหรือยัง?</h1>
        <div class="auth-user">
            <span>นาย สมปอง ยาคู</span>
            <span class="divider">|</span>
            <a href="index.html" class="logout-link">Log out</a>
        </div>
    </header>

    <nav class="menu">
        <a href="Aindex.html">หน้าแรก</a>
        <a href="ACars.html">รถยนต์</a>
        <a href="Amotorcycle.html">มอเตอร์ไซค์</a>
        <a href="AReccar.html">รถแนะนำ</a>
        <a href="Acushistory.html">ประวัติ</a>
        <a href="">แก้ไขข้อมูลรถ</a>
        <a href="Acontact.html">ติดต่อ</a>

    <main class="container">
        
        <div class="profile-page-header">
            <h2>ข้อมูลส่วนตัว</h2>
        </div>

        <div class="profile-layout">
            
            <div class="profile-left">
                <div class="profile-img-container">
                    <img src="https://via.placeholder.com/200x200?text=Profile+Image" alt="รูปโปรไฟล์">
                </div>
                <button class="btn-upload-profile">อัพโหลดรูปภาพ</button>
            </div>

            <div class="profile-right">
                <form class="profile-form">
                    <div class="p-form-row">
                        <label>รหัสสมาชิก</label>
                        <input type="text" value="" readonly> </div>
                    <div class="p-form-row">
                        <label>ชื่อ-นามสกุล</label>
                        <input type="text" value="">
                    </div>
                    <div class="p-form-row">
                        <label>เบอร์โทรศัพท์</label>
                        <input type="text" value="">
                    </div>
                    <div class="p-form-row">
                        <label>บ้านเลขที่</label>
                        <input type="text" value="">
                    </div>
                    <div class="p-form-row">
                        <label>ตำบล</label>
                        <input type="text" value="">
                    </div>
                    <div class="p-form-row">
                        <label>อำเภอ</label>
                        <input type="text" value="">
                    </div>
                    <div class="p-form-row">
                        <label>จังหวัด</label>
                        <input type="text" value="">
                    </div>
                    <div class="p-form-row">
                        <label>รหัสไปรษณีย์</label>
                        <input type="text" value="">
                    </div>
                </form>

                <div class="profile-actions">
                    <button class="btn-save">บันทึก</button>
                    <button class="btn-edit">แก้ไขข้อมูล</button>
                </div>
            </div>

        </div>

</html>