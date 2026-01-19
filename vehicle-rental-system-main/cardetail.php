<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายละเอียดรถ - มีรถหรือยัง?</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container">
        
        <div class="detail-layout">
            
            <div class="detail-left">
                <div class="main-car-img">
                    รูปภาพรถ
                </div>

                <div class="thumbnail-row">
                    <div class="thumb-box">รูปภาพรถ</div>
                    <div class="thumb-box">รูปภาพรถ</div>
                </div>

                <div class="desc-box">
                    คำอธิบายรถ
                </div>
            </div>

            <div class="detail-right">
                
                <div class="info-group">
                    <div class="info-row">
                        <div class="label-box">ชื่อรถ</div>
                        <div class="value-text">Toyota Yaris</div> 
                    </div>

                    <div class="info-row">
                        <div class="label-box">จำนวนดาว</div>
                        <div class="value-text star-rating">(สภาพรถ) <br> <span>★★★★★</span></div>
                    </div>

                    <div class="info-row">
                        <div class="label-box">จำนวนเช่า</div>
                        <div class="value-text">01</div>
                    </div>

                    <div class="info-row">
                        <div class="label-box">ราคา</div>
                        <div class="value-text price-text">590.00฿</div>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="Cars.php" class="btn-back">ย้อนกลับ</a>
                    <a href="/Signin/signin.php" class="btn-order" style="text-decoration:none;">สั่งซื้อ</a>
                </div>

            </div>
        </div>

</html>