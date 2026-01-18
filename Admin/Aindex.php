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

        <!-- Banner -->
        <section class="banner">
        </section>

        <!-- Car List -->
        <section class="car-grid">
            <div class="car-card">
                <img src="/IMG/Imgcar/toyotavios.png" alt="Toyota Vios">
            <div class="car-info">
                <h3>Toyota Vios</h3>
             </div>
            </div>

           <div class="car-card">
                <img src="/IMG/Imgcar/toyotavios.png" alt="Toyota Vios">
            <div class="car-info">
                <h3>Toyota Vios</h3>
             </div>
            </div>
            <div class="car-card">
                <img src="/IMG/Imgcar/toyotavios.png" alt="Toyota Vios">
            <div class="car-info">
                <h3>Toyota Vios</h3>
             </div>
            </div>
            <div class="car-card">
                <img src="/IMG/Imgcar/toyotavios.png" alt="Toyota Vios">
            <div class="car-info">
                <h3>Toyota Vios</h3>
             </div>
            </div>
            <div class="car-card">
                <img src="/IMG/Imgcar/toyotavios.png" alt="Toyota Vios">
            <div class="car-info">
                <h3>Toyota Vios</h3>
             </div>
            </div>
            <div class="car-card">
                <img src="/IMG/Imgcar/toyotavios.png" alt="Toyota Vios">
            <div class="car-info">
                <h3>Toyota Vios</h3>
             </div>
            </div>
    
        </section>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

<script>
    // --- ตั้งค่า Supabase (ใช้ Key เดิมของคุณ) ---
    const supabaseUrl = 'https://ucpfkzoswswaxsiovxon.supabase.co';
    const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InVjcGZrem9zd3N3YXhzaW92eG9uIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Njg3MDU1NjQsImV4cCI6MjA4NDI4MTU2NH0.z0C8t5V1CNfSQ1IaJwKRLFiAZR-K4m-uIFQQKA0P_Zg';
    const supabase = supabase.createClient(supabaseUrl, supabaseKey);

    // --- ฟังก์ชันทำงานทันทีที่โหลดหน้าเว็บ ---
    window.onload = async function() {
        checkLoginStatus();
    };

    async function checkLoginStatus() {
        // 1. เช็คว่ามี Session ค้างอยู่ไหม
        const { data: { session } } = await supabase.auth.getSession();

        if (session) {
            // ถ้ามีคนล็อกอินอยู่ -> ไปดึงชื่อมาจากฐานข้อมูล
            const userId = session.user.id;

            // ดึงชื่อจากตาราง Customer
            const { data: userProfile, error } = await supabase
                .from('Customer')
                .select('Cust_FirstName, Cust_LastName')
                .eq('auth_id', userId)  // ✅ แก้เป็นอันนี้ครับ
                .single();

            if (userProfile) {
                // เอาชื่อมาต่อกัน
                const fullName = `${userProfile.Cust_FirstName} ${userProfile.Cust_LastName}`;
                
                // เปลี่ยนหน้าจอ
                showLoggedInState(fullName);
            }
        }
    }

    // ฟังก์ชันเปลี่ยนปุ่ม Sign up/in เป็น ชื่อ + Logout
    function showLoggedInState(name) {
        const authDiv = document.getElementById('auth-container');
        
        // แก้ไข HTML ในกล่อง auth ให้เป็นชื่อและปุ่ม Logout
        authDiv.innerHTML = `
            <span style="font-weight: bold; margin-right: 15px; color: black;">${name}</span>
            <button onclick="handleLogout()" style="background: none; border: 1px solid black; padding: 5px 10px; cursor: pointer; border-radius: 4px;">Log out</button>
        `;
    }

    // ฟังก์ชันออกจากระบบ
    async function handleLogout() {
        await supabase.auth.signOut(); // สั่ง Logout
        window.location.reload();      // รีเฟรชหน้าจอให้กลับเป็นปกติ
    }
</script>

</body>
</html>
