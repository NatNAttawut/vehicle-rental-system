<?php
$title = "หน้าแรก";
require_once __DIR__ . '/partials/header.php';
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ติดต่อเรา - มีรถหรือยัง?</title>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
  .map-container{
    width: 100%;
    min-height: 520px;      /* ปรับได้: 500-700 */
    height: 100%;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,.12);
  }
  .map-container iframe{
    width: 100%;
    height: 100%;
    display: block;         /* กันช่องว่างใต้ iframe */
  }

  /* ถ้าจอเล็ก ให้สูงพอดี */
  @media (max-width: 991.98px){
    .map-container{ min-height: 420px; }
  }
</style>


</head>
<body class="bg-light"> 
<div class="contact-header text-center">
    <div class="container">
        <h1 class="fw-bold">ติดต่อเรา</h1>
        <p class="text-muted">สอบถามข้อมูลเพิ่มเติม เช่ารถ หรือแจ้งปัญหาการใช้งาน</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        
        <div class="col-lg-5">
            <div class="info-card">
                <h4 class="mb-4">ข้อมูลการติดต่อ</h4>
                
                <div class="info-item">
                    <div class="icon-box">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold">ที่อยู่สำนักงาน</h5>
                        <p class="text-muted mb-0">
                            119 หมู่ 9 ถนนลำปาง-แม่ทะ  <br> 
                            ตำบลชมพู อำเภอเมือง <br> จังหวัดลำปาง 52100
                        </p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="icon-box">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold">เบอร์โทรศัพท์</h5>
                        <p class="text-muted mb-0">081-111-111 (คุณแอดมิน)</p>
                        <p class="text-muted mb-0">000-000-000 (สำนักงาน)</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="icon-box">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold">อีเมล</h5>
                        <p class="text-muted mb-0">contact@codecraft.com</p>
                        <p class="text-muted mb-0">support@codecraft.com</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="icon-box">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold">เวลาทำการ</h5>
                        <p class="text-muted mb-0">จันทร์ - อาทิตย์: 08:00 - 18:00 น.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="map-container">
                <iframe 
src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3774.246737526978!2d99.48486167608246!3d18.23401668278912!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30d96a4d55354b3d%3A0x34d4741c70264733!2z4Lih4Lir4Liy4Lin4Li04LiX4Lii4Liy4Lil4Lix4Lii4Lij4Liy4LiK4Lig4Lix4LiP4Lil4Liz4Lib4Liy4LiH!5e0!3m2!1sth!2sth!4v1705755555555!5m2!1sth!2sth"
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>

    </div>

    <div class="text-center mt-5 pt-4">
        <h4 class="fw-bold mb-4">ติดตามข่าวสาร & โปรโมชั่น</h4>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="https://facebook.com"  title="Facebook">
                <i class="bi bi-facebook"></i>
            </a>
            <a href="https://instagram.com"  title="Instagram">
                <i class="bi bi-instagram"></i>
            </a>
            <a href="https://tiktok.com"  title="TikTok">
                <i class="bi bi-tiktok"></i>
            </a>
            <a href="https://twitter.com" title="X (Twitter)">
                <i class="bi bi-twitter-x"></i>
            </a>
            <a href="https://line.me"  title="Line">
                <i class="bi bi-line"></i>
            </a>
        </div>
    </div>

</div>

</body>
</html>