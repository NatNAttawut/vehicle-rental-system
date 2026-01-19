<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Sign In - มีรถหรือยัง?</title>
    <link rel="stylesheet" href="signin.css">
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

    <div class="signin-container">
        <div class="signin-form">
            <h1>Sign in</h1>

            <label>Email</label>
            <input type="email" id="loginEmail" placeholder="กรอกอีเมลของคุณ">

            <label>Password</label>
            <input type="password" id="loginPassword" placeholder="กรอกรหัสผ่าน">

            <p class="register-link">
                ยังไม่มีบัญชี? <a href="../Signup/signup.php">สมัครสมาชิก</a>
            </p>

            <div class="buttons">
                <button type="button" onclick="handleLogin()">Login</button>
                <a href="../index.php"><button type="button">Cancel</button></a>
            </div>
            
            <p id="loginMessage" style="margin-top: 15px; color: white; font-weight: bold; text-align: center;"></p>
        </div>

        <div class="signin-poster">
             <!-- ถ้าไม่มีไฟล์ poster.jpg ให้เปลี่ยนรูปหรือเอาออกได้ -->
             <img src="../IMG/Banner1.png" alt="Poster" style="width: 100%; height: 100%; object-fit: cover; display: block;"> 
        </div>
    </div>

   <script>
        const sb = window.sb;

        async function handleLogin() {
            const email = document.getElementById('loginEmail').value.trim();
            const password = document.getElementById('loginPassword').value;
            const msg = document.getElementById('loginMessage');

            if (!email || !password) {
                alert("⚠️ กรุณากรอก Email และ Password ให้ครบ");
                return;
            }

            msg.innerText = '⏳ กำลังตรวจสอบข้อมูล...';
            msg.style.color = 'yellow';

            try {
                // 1. ล็อกอินเข้าสู่ระบบ (Auth)
                const { data: authData, error: authError } = await sb.auth.signInWithPassword({
                    email: email,
                    password: password
                });

                if (authError) throw authError;

                // 2. ถ้าล็อกอินผ่าน -> เช็ค Role ในตาราง Customer
                msg.innerText = '🔍 กำลังตรวจสอบสิทธิ์...';
                
                const { data: userData, error: dbError } = await sb
                    .from('customer')
                    .select('cust_role')
                    .eq('auth_id', authData.user.id)
                    .single();

                if (dbError) throw dbError;

                // 3. แยกย้ายตามบทบาท (Redirect)
                if (userData && userData.cust_role === 'admin') {
                    // --- กรณีเป็น ADMIN ---
                    alert("👑 ยินดีต้อนรับผู้ดูแลระบบ!");
                    msg.innerText = 'กำลังเข้าสู่หน้า Admin...';
                    msg.style.color = '#00ff00';
                    
                    // 👉 ส่งไปหน้า Aindex.php (ตรวจสอบ Path ให้ถูกต้องนะครับ)
                    // สมมติว่าไฟล์อยู่ในโฟลเดอร์ Admin ให้ใช้: '../Admin/Aindex.php'
                    // หรือถ้าอยู่โฟลเดอร์เดียวกันให้ใช้: 'Aindex.php'
                    window.location.href = '../Admin/Aindex.php'; 

                } else {
                    // --- กรณีเป็น USER ทั่วไป ---
                    alert("✅ เข้าสู่ระบบสำเร็จ!");
                    msg.innerText = 'กำลังไปหน้าแรก...';
                    msg.style.color = '#00ff00';

                    // 👉 ส่งไปหน้า Uindex.php
                    // สมมติว่าไฟล์อยู่ในโฟลเดอร์ User ให้ใช้: '../User/Uindex.php'
                    window.location.href = '../User/Uindex.php';
                }

            } catch (err) {
                console.error("Login Error:", err);
                alert("❌ เกิดข้อผิดพลาด: " + (err.message || "ไม่สามารถดึงข้อมูลได้"));
                msg.innerText = '❌ Error: ' + err.message;
                msg.style.color = 'red';
            }
        }
    </script>
</body>
</html>