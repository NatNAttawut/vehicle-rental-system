<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>มีรถหรือยัง?</title>
    <link rel="stylesheet" href="Astyle.css">
</head>
<body>

    <?php include 'Aheader.php'; ?>
    
    <main class="container">
        
        <div class="history-tab-area">
            <div class="tab-box">ยืนยันการจองยานพาหนะ</div>
            <div class="tab-label">ของผู้ใช้/ลูกค้า</div>
        </div>

        <div class="history-table-wrapper">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>รูปภาพ</th>
                        <th>เลขทะเบียน</th>
                        <th>ชื่อผู้จอง</th>
                        <th>โทรศัพท์</th>
                        <th>วันที่</th>
                        <th>เวลา</th>
                        <th>การยืม</th>
                        <th>การคืน</th>
                        <th>หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><div class="table-img-placeholder"></div></td>
                        <td>กน 1054</td>
                        <td>ทำไปเรื่อย</td>
                        <td>12345678</td>
                        <td>27 ม.ค 2560</td>
                        <td>08.00-10.00</td>
                        <td>อนุมัติแล้ว</td>
                        <td>เสร็จสิ้น</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

</html>