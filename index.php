<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>มีรถหรือยัง?</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container">

        <section class="banner">
        </section>

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
        const supabaseUrl = 'https://ucpfkzoswswaxsiovxon.supabase.co';
        const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InVjcGZrem9zd3N3YXhzaW92eG9uIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Njg3MDU1NjQsImV4cCI6MjA4NDI4MTU2NH0.z0C8t5V1CNfSQ1IaJwKRLFiAZR-K4m-uIFQQKA0P_Zg';
        const supabase = supabase.createClient(supabaseUrl, supabaseKey);

        window.onload = async function() {
            checkLoginStatus();
        };

        async function checkLoginStatus() {
            const { data: { session } } = await supabase.auth.getSession();

            if (session) {
                const userId = session.user.id;

                const { data: userProfile, error } = await supabase
                    .from('Customer')
                    .select('Cust_FirstName, Cust_LastName')
                    .eq('auth_id', userId) // ✅ ตรงนี้ถูกต้องแล้วครับ
                    .single();

                if (userProfile) {
                    const fullName = `${userProfile.Cust_FirstName} ${userProfile.Cust_LastName}`;
                    showLoggedInState(fullName);
                }
            }
        }

        function showLoggedInState(name) {
            // Script นี้จะวิ่งไปแก้ HTML ที่อยู่ใน header.php ได้ปกติครับ
            const authDiv = document.getElementById('auth-container');
            
            authDiv.innerHTML = `
                <span style="font-weight: bold; margin-right: 15px; color: black;">${name}</span>
                <button onclick="handleLogout()" style="background: none; border: 1px solid black; padding: 5px 10px; cursor: pointer; border-radius: 4px;">Log out</button>
            `;
        }

        async function handleLogout() {
            await supabase.auth.signOut();
            window.location.reload();
        }
    </script>

</body>
</html>