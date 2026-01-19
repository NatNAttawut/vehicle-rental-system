<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Sign Up - มีรถหรือยัง?</title>
  <link rel="stylesheet" href="signup.css">
  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
  <script src="../assets/js/supabaseClient.js"></script>
</head>
<body>
  <header class="top-header">
    <div class="logo">
      <a href="../index.php"><img src="../IMG/LogoCodecraft.png" alt="Logo"></a>
    </div>
    <h1>มีรถหรือยัง?</h1>
  </header>

  <div class="signup-container">
    <h1>Sign Up</h1>

    <div id="signupForm">
      <label>Username</label>
      <input type="text" id="username">

      <label>Email</label>
      <input type="email" id="email">

      <label>Password</label>
      <input type="password" id="password">

      <div class="row">
        <div><label>First Name</label><input type="text" id="firstName"></div>
        <div><label>Last Name</label><input type="text" id="lastName"></div>
      </div>

      <div class="row">
        <div><label>Address Line</label><input type="text" id="address"></div>
        <div><label>Sub-district</label><input type="text" id="subDistrict"></div>
      </div>

      <div class="row">
        <div><label>District</label><input type="text" id="district"></div>
        <div><label>Province</label><input type="text" id="province"></div>
      </div>

      <div class="row">
        <div><label>Postal Code</label><input type="text" id="postalCode"></div>
        <div><label>Tel</label><input type="text" id="tel"></div>
      </div>

      <div class="buttons">
        <button type="button" onclick="registerUser()">Sign up</button>
        <a href="../index.php"><button type="button">Cancel</button></a>
      </div>
      <p id="statusMessage" style="color: white; text-align: center; margin-top: 20px; font-weight: bold;"></p>
    </div>
  </div>

  <script>
    const sb = window.sb;

    async function registerUser() {
      const statusMsg = document.getElementById('statusMessage');
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;
      const username = document.getElementById('username').value.trim();

      if (statusMsg) {
        statusMsg.innerText = 'กำลังบันทึกข้อมูล...';
        statusMsg.style.color = 'yellow';
      }

      if (!email || !password || !username) {
        alert("⚠️ กรุณากรอก Username, Email, Password ให้ครบ");
        return;
      }

      try {
        // 1) สมัคร Supabase Auth
        const { data: authData, error: authError } = await sb.auth.signUp({
          email,
          password
        });

        if (authError) {
          alert("❌ สมัครไม่ผ่าน (Auth): " + authError.message);
          return;
        }

        if (!authData.user) {
          alert("❌ สมัครไม่สำเร็จ: ไม่พบ user");
          return;
        }

        // 2) Upsert ลงตาราง customer (กันชน unique เพราะมี trigger)
        const payload = {
          auth_id: authData.user.id,
          cust_uname: username,
          cust_phone: document.getElementById('tel').value.trim(),
          cust_house: document.getElementById('address').value.trim(),
          cust_district: document.getElementById('subDistrict').value.trim(),
          cust_prefecture: document.getElementById('district').value.trim(),
          cust_province: document.getElementById('province').value.trim(),
          cust_postcode: parseInt(document.getElementById('postalCode').value || '0', 10),
          cust_role: 'user',
          cust_status: 'active'
        };

        const { error: upsertErr } = await sb
          .from('customer')
          .upsert([payload], { onConflict: 'auth_id' });

        if (upsertErr) {
          alert("❌ บันทึกตารางไม่ผ่าน (DB): " + upsertErr.message);
          return;
        }

        alert("🎉 สมัครสำเร็จ! ไปเช็คอีเมลยืนยัน (ถ้ามี) แล้วค่อย Sign in");
        window.location.href = '../Signin/signin.php';

      } catch (error) {
        console.error(error);
        alert("❌ Error ร้ายแรง: " + (error.message || error));
      }
    }
  </script>
</body>
</html>
