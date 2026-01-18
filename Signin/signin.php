<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Sign In - มีรถหรือยัง?</title>
    <link rel="stylesheet" href="signin.css">
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
</head>
<body>
    <header class="top-header">
        <div class="logo">
            <a href="../index.php"><img src="/IMG/LogoCodecraft.png" alt="Logo"></a>
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
                ยังไม่มีบัญชี? <a href="/Signup/signup.php">สมัครสมาชิก</a>
            </p>

            <div class="buttons">
                <button type="button" onclick="handleLogin()">Login</button>
                <a href="../index.php"><button type="button">Cancel</button></a>
            </div>
            
            <p id="loginMessage" style="margin-top: 15px; color: white; font-weight: bold; text-align: center;"></p>
        </div>

        <div class="signin-poster">
             <img src="/IMG/poster.jpg" alt="Poster" style="width: 100%; height: 100%; object-fit: cover; display: block;"> 
        </div>
    </div>

    <script>
        const supabaseUrl = 'https://ucpfkzoswswaxsiovxon.supabase.co';
        const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InVjcGZrem9zd3N3YXhzaW92eG9uIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Njg3MDU1NjQsImV4cCI6MjA4NDI4MTU2NH0.z0C8t5V1CNfSQ1IaJwKRLFiAZR-K4m-uIFQQKA0P_Zg';
        const supabase = supabase.createClient(supabaseUrl, supabaseKey);

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
                // 1. ล็อกอินกับ Supabase Auth
                const { data: authData, error: authError } = await supabase.auth.signInWithPassword({
                    email: email,
                    password: password
                });

                if (authError) throw authError;

                // 2. ถ้าล็อกอินผ่าน -> ไปเช็ค Role ในตาราง Customer
                msg.innerText = '🔍 กำลังตรวจสอบสิทธิ์การใช้งาน...';
                
                const { data: userData, error: dbError } = await supabase
                    .from('Customer')
                    .select('Cust_Role')
                    .eq('auth_id', authData.user.id) // หาจาก auth_id
                    .single();

                if (dbError) throw dbError;

                // 3. แยกทางเดิน (Redirect) ตามบทบาท
                if (userData.Cust_Role === 'admin') {
                    // --- ทางเดินแอดมิน ---
                    alert("👑 ยินดีต้อนรับผู้ดูแลระบบ!");
                    msg.innerText = 'กำลังเข้าสู่ระบบ Admin...';
                    msg.style.color = '#00ff00';
                    // สร้างโฟลเดอร์ Admin รอไว้ หรือเปลี่ยน path ตามจริง
                    window.location.href = '../Admin/admin_dashboard.php'; 
                } else {
                    // --- ทางเดินคนทั่วไป ---
                    alert("✅ เข้าสู่ระบบสำเร็จ!");
                    msg.innerText = 'กำลังไปหน้าแรก...';
                    msg.style.color = '#00ff00';
                    window.location.href = '../index.php';
                }

            } catch (err) {
                console.error(err);
                alert("❌ เกิดข้อผิดพลาด: " + err.message);
                msg.innerText = '❌ Error: ' + err.message;
                msg.style.color = 'red';
            }
        }
    </script>
</body>
</html>