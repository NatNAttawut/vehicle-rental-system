<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$title = "สมัครสมาชิก";
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

  <style>
    body{
      min-height:100vh;
      background:
        radial-gradient(circle at 20% 10%, rgba(255,152,0,.25), transparent 35%),
        radial-gradient(circle at 80% 60%, rgba(0,200,255,.15), transparent 40%),
        linear-gradient(180deg, #0b1220, #05070c);
      color:#e9eef5;
    }
    .glass-card{
      background: rgba(16, 24, 36, 0.82);
      border: 1px solid rgba(255,255,255,0.10);
      border-radius: 18px;
      box-shadow: 0 20px 60px rgba(0,0,0,.55);
      overflow:hidden;
    }
    .card-head{
      padding: 16px 18px;
      border-bottom: 1px solid rgb(255, 255, 255);
      background: linear-gradient(180deg, rgba(255,255,255,0.05), rgba(255,255,255,0));
    }
    .soft-input{
      background: rgba(255, 255, 255, 0.85);
      border: 1px solid rgba(255,255,255,0.10);
      color: #32353a;
    }
    .soft-input:focus{
      border-color: rgba(255,193,7,.55);
      box-shadow: 0 0 0 .25rem rgba(255,193,7,.15);
      background: rgba(255,255,255,0.08);
      color:#fff;
    }
    .btn-orange{
      background: #ff9800;
      border: none;
      color:#111;
      font-weight:700;
      border-radius: 12px;
      padding: 12px 14px;
    }
    .soft-input.form-select option {
      color: #000;          
    }
    .btn-orange:hover{ background:#ffb300; }
    .btn-ghost{
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(255,255,255,0.10);
      color:#e9eef5;
      border-radius: 12px;
    }
    
    .muted{ color: rgba(233,238,245,.75); }
    .tiny{ font-size: 12px; }
    .req{ color:#ff5a5a; }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-lg-7 col-xl-6">

      <?php if (isset($_GET['err'])): ?>
        <div class="alert alert-danger">
          <?= htmlspecialchars($_GET['err']) ?>
        </div>
      <?php endif; ?>

      <div class="glass-card">
        <div class="card-head d-flex align-items-center justify-content-between gap-3">
          <div class="d-flex align-items-center gap-3">
            <a href="/index.php" class="text-decoration-none d-flex align-items-center gap-2">
              <img src="/img/LogoCodecraft.png" alt="Logo" style="height:38px;width:auto;border-radius:8px;">
            </a>
            <div>
              <div class="fw-bold">มีรถหรือยัง?</div>
              <div class="muted tiny">สมัครสมาชิกเพื่อจองรถได้ทันที</div>
            </div>
          </div>
          <a class="btn btn-warning btn-sm fw-bold rounded-pill px-3" href="/index.php">← กลับหน้าแรก</a>
        </div>

        <div class="p-4">
          <form method="post" action="/registerProcess.php" enctype="multipart/form-data">
            
            <div class="col-md-6">
              <label class="form-label fw-semibold">Username <span class="req">*</span></label>
              <input class="form-control soft-input" name="username" required minlength="3" maxlength="50" autocomplete="username" placeholder="Username">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">รหัสผ่าน <span class="req">*</span></label>
              <input class="form-control soft-input" name="password" type="password" required minlength="6" autocomplete="new-password" placeholder="Password">
              <div class="tiny muted mt-1">แนะนำอย่างน้อย 6 ตัวอักษร</div>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">อีเมล <span class="req">*</span></label>
              <input class="form-control soft-input" name="email" type="email" required autocomplete="email" placeholder="Email">
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">ชื่อ-นามสกุล <span class="req">*</span></label>
              <input class="form-control soft-input" name="fullname" type="text" required
              autocomplete="name" placeholder="ชื่อ นามสกุล">
            </div>


            <div class="col-12">
              <label class="form-label fw-semibold">เบอร์โทรศัพท์ <span class="req">*</span></label>
              <input class="form-control soft-input" name="phone" required maxlength="20" placeholder="Phone Number">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">บ้านเลขที่ <span class="req">*</span></label>
              <input class="form-control soft-input" name="house" required maxlength="100" placeholder="House number">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">ซอย/ถนน</label>
              <input class="form-control soft-input" name="alley" maxlength="100" placeholder="Alley / Road">
            </div>
  
            <div class="col-md-6">
              <label class="form-label fw-semibold">จังหวัด <span class="req">*</span></label>
              <select id="province" name="province" class="form-select soft-input" required>
              <option value="">เลือกจังหวัด</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">อำเภอ <span class="req">*</span></label>
              <select id="district" name="district" class="form-select soft-input" required>
              <option value="">เลือกอำเภอ</option>
              </select>
              </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">ตำบล <span class="req">*</span></label>
              <select id="subdistrict" name="subdistrict" class="form-select soft-input" required>
              <option value="">เลือกตำบล</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">รหัสไปรษณีย์ <span class="req">*</span></label>
              <input id="zipcode" name="zipcode" class="form-control soft-input" required readonly placeholder="รหัสไปรษณีย์">
              </div>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">แนบรูปโปรไฟล์ (ไม่บังคับ)</label>
              <input class="form-control soft-input" type="file" name="picture" accept=".jpg,.jpeg,.png,.webp">
              <div class="tiny muted mt-1">รองรับ .jpg .png .webp</div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="agree" id="agree" required>
                <label class="form-check-label tiny muted" for="agree">
                  ยินยอมให้ดำเนินการเก็บรวบรวมข้อมูลดังกล่าว <span class="req">*</span>
                </label>
              </div>
            </div>

            <div class="col-12">
              <button class="btn btn-orange w-100">
                สมัครสมาชิก
              </button>
            </div>

            <div class="col-12">
              <a class="btn btn-ghost w-100" href="/login.php">มีบัญชีอยู่แล้ว? เข้าสู่ระบบ</a>
            </div>

          </form>
        </div>

        <div class="text-center tiny muted py-3" style="border-top:1px solid rgba(255,255,255,.08)">
          © 2026 รถเช่ายืมหรอ? : ระบบยืม/เช่ารถ
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let addressData = [];
let tsProvince, tsDistrict, tsSubdistrict;

function createTomSelect(selector, placeholderText) {
  return new TomSelect(selector, {
    create: false,
    sortField: { field: "text", direction: "asc" },
    placeholder: placeholderText,
    allowEmptyOption: true
  });
}

function setOptions(ts, items, placeholder) {
  ts.clear(true);
  ts.clearOptions();
  ts.addOption({ value: "", text: placeholder });
  items.forEach(t => ts.addOption({ value: t, text: t }));
  ts.refreshOptions(false);
  ts.setValue("", true);
}

async function initThaiAddress() {
  const res = await fetch("../assets/thai_address.json");
  addressData = await res.json();

  tsProvince = createTomSelect("#province", "เลือกจังหวัด");
  tsDistrict = createTomSelect("#district", "เลือกอำเภอ");
  tsSubdistrict = createTomSelect("#subdistrict", "เลือกตำบล");

  tsDistrict.disable();
  tsSubdistrict.disable();

  const provinces = [...new Set(addressData.map(x => x.province))];
  setOptions(tsProvince, provinces, "เลือกจังหวัด");

  tsProvince.on("change", () => {
    const province = tsProvince.getValue();
    document.querySelector("#zipcode").value = "";

    setOptions(tsDistrict, [], "เลือกอำเภอ");
    setOptions(tsSubdistrict, [], "เลือกตำบล");

    if (!province) {
      tsDistrict.disable();
      tsSubdistrict.disable();
      return;
    }

    tsDistrict.enable();
    tsSubdistrict.disable();

    const districts = [...new Set(
      addressData.filter(x => x.province === province).map(x => x.district)
    )];

    setOptions(tsDistrict, districts, "เลือกอำเภอ");
  });

  tsDistrict.on("change", () => {
    const province = tsProvince.getValue();
    const district = tsDistrict.getValue();
    document.querySelector("#zipcode").value = "";

    setOptions(tsSubdistrict, [], "เลือกตำบล");

    if (!(province && district)) {
      tsSubdistrict.disable();
      return;
    }

    tsSubdistrict.enable();

    const subs = [...new Set(
      addressData
        .filter(x => x.province === province && x.district === district)
        .map(x => x.subdistrict)
    )];

    setOptions(tsSubdistrict, subs, "เลือกตำบล");
  });

  tsSubdistrict.on("change", () => {
    const province = tsProvince.getValue();
    const district = tsDistrict.getValue();
    const subdistrict = tsSubdistrict.getValue();

    const found = addressData.find(x =>
      x.province === province &&
      x.district === district &&
      x.subdistrict === subdistrict
    );

    document.querySelector("#zipcode").value = found ? found.zipcode : "";
  });
}

document.addEventListener("DOMContentLoaded", initThaiAddress);
</script>

</body>
</html>
