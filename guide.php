<?php
$title = "หน้าแรก";
require_once __DIR__ . '/partials/header.php';
?>
  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คู่มือการใช้งาน - มีรถหรือยัง?</title>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


</head>
<body class="bg-light">

<div class="guide-header text-center">
    <div class="container">
        <h1 class="fw-bold mb-3">คู่มือการใช้งาน & คำถามที่พบบ่อย</h1>
        <p class="lead opacity-75">ขั้นตอนการเช่ารถง่ายๆ กับ "มีรถหรือยัง?" ภายใน 5 นาที</p>
    </div>
</div>

<div class="container mb-5">
    
    <div class="row g-4 mb-5 text-center">
        <div class="col-12 text-center mb-2">
            <h3 class="fw-bold">4 ขั้นตอนง่ายๆ ในการเช่ารถ</h3>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card step-card p-4">
                <div class="step-icon"><i class="bi bi-person-plus-fill"></i></div>
                <h5>1. สมัครสมาชิก</h5>
                <p class="text-muted small">ลงทะเบียนและเข้าสู่ระบบเพื่อเริ่มต้นใช้งานระบบจองรถ</p>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card step-card p-4">
                <div class="step-icon"><i class="bi bi-car-front-fill"></i></div>
                <h5>2. เลือกรถที่ถูกใจ</h5>
                <p class="text-muted small">เลือกดูรถยนต์หรือมอเตอร์ไซค์ เช็คสถานะ "ว่าง" แล้วกดจอง</p>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card step-card p-4">
                <div class="step-icon"><i class="bi bi-calendar-check-fill"></i></div>
                <h5>3. ระบุวันเวลา</h5>
                <p class="text-muted small">เลือกวันรับรถและคืนรถ ระบบจะคำนวณราคาให้ทราบทันที</p>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card step-card p-4">
                <div class="step-icon"><i class="bi bi-check-circle-fill"></i></div>
                <h5>4. รออนุมัติ & รับรถ</h5>
                <p class="text-muted small">รอแอดมินอนุมัติ (เช็คสถานะได้ที่เมนูประวัติ) แล้วมารับรถได้เลย</p>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <h4 class="mb-4">📋 เอกสารที่ต้องเตรียม (ในวันรับรถ)</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-transparent px-0 py-3 d-flex align-items-center gap-3">
                            <i class="bi bi-card-heading fs-4 text-primary"></i>
                            <div>
                                <strong>บัตรประชาชนตัวจริง</strong>
                                <div class="small text-muted">ที่ยังไม่หมดอายุ (สำหรับคนไทย)</div>
                            </div>
                        </li>
                        <li class="list-group-item bg-transparent px-0 py-3 d-flex align-items-center gap-3">
                            <i class="bi bi-person-vcard fs-4 text-primary"></i>
                            <div>
                                <strong>ใบขับขี่ตัวจริง</strong>
                                <div class="small text-muted">ตรงตามประเภทรถที่เช่า (รถยนต์/มอเตอร์ไซค์)</div>
                            </div>
                        </li>
                        <li class="list-group-item bg-transparent px-0 py-3 d-flex align-items-center gap-3">
                            <i class="bi bi-cash-stack fs-4 text-primary"></i>
                            <div>
                                <strong>ค่าเช่ารถ</strong>
                                <div class="small text-muted">ชำระเต็มจำนวน ณ วันที่รับรถ</div>
                            </div>
                        </li>
                        <li class="list-group-item bg-transparent px-0 py-3 d-flex align-items-center gap-3">
                            <i class="bi bi-shield-lock fs-4 text-primary"></i>
                            <div>
                                <strong>เงินมัดจำ (ถ้ามี)</strong>
                                <div class="small text-muted">คืนให้ทันทีเมื่อนำรถมาคืนในสภาพปกติ</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <h4 class="mb-4">❓ คำถามที่พบบ่อย (FAQ)</h4>
            
            <div class="accordion" id="faqAccordion">
                
                <div class="accordion-item mb-3 border rounded overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            จองรถล่วงหน้าได้กี่วัน?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            คุณสามารถจองรถล่วงหน้าได้ไม่จำกัดจำนวนวัน ยิ่งจองเร็วยิ่งมีโอกาสได้รถรุ่นที่ต้องการสูงครับ
                        </div>
                    </div>
                </div>

                <div class="accordion-item mb-3 border rounded overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            ยกเลิกการจองได้หรือไม่?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            <strong>ได้ครับ</strong> หากสถานะการจองยังเป็น "รออนุมัติ (Pending)" คุณสามารถกดยกเลิกได้เองที่หน้า "ประวัติการจอง" <br>
                            แต่หากสถานะเป็น "อนุมัติแล้ว" กรุณาติดต่อเจ้าหน้าที่ผ่านทางไลน์หรือเบอร์โทรศัพท์
                        </div>
                    </div>
                </div>

                <div class="accordion-item mb-3 border rounded overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            คืนรถช้ามีค่าปรับไหม?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            หากคืนรถช้ากว่าเวลาที่กำหนด จะมีค่าปรับรายชั่วโมงตามที่บริษัทกำหนด หากคาดว่าจะคืนช้า กรุณาแจ้งเจ้าหน้าที่ล่วงหน้าเพื่อขยายเวลาเช่า
                        </div>
                    </div>
                </div>

                <div class="accordion-item mb-3 border rounded overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            รับรถได้ที่ไหน?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            รับรถได้ที่สำนักงานของเรา (ดูแผนที่ในหน้า <a href="contact.php">ติดต่อเรา</a>) เวลาทำการ 08:00 - 18:00 น. ทุกวัน
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="bg-dark text-white rounded-4 p-5 text-center mt-5">
        <h2 class="fw-bold">พร้อมออกเดินทางหรือยัง?</h2>
        <p class="lead mb-4">เลือกรถที่ใช่ แล้วไปสัมผัสประสบการณ์ใหม่ๆ กันเลย</p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="cars.php" class="btn btn-warning btn-lg px-4 fw-bold">จองรถยนต์</a>
            <a href="motorcycle.php" class="btn btn-outline-light btn-lg px-4">จองมอเตอร์ไซค์</a>
        </div>
    </div>

</div>

</body>
</html>