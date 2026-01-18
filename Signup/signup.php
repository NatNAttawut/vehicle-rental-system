<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - มีรถหรือยัง?</title>
    <link rel="stylesheet" href="signup.css">
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
</head>
<body>
    <header class="top-header">
        <div class="logo">
            <a href="../index.php"><img src="/IMG/LogoCodecraft.png" alt="Logo"></a>
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
        const supabaseUrl = 'https://ucpfkzoswswaxsiovxon.supabase.co';
        const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InVjcGZrem9zd3N3YXhzaW92eG9uIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Njg3MDU1NjQsImV4cCI6MjA4NDI4MTU2NH0.z0C8t5V1CNfSQ1IaJwKRLFiAZR-K4m-uIFQQKA0P_Zg';
        var supabase = supabase.createClient(supabaseUrl, supabaseKey);

        async function registerUser() {
            const statusMsg = document.getElementById('statusMessage');
            if(statusMsg) {
                statusMsg.innerText = 'กำลังบันทึกข้อมูล...';
                statusMsg.style.color = 'yellow';
            }

            try {
                var email = document.getElementById('email').value;
                var password = document.getElementById('password').value;
                var firstName = document.getElementById('firstName').value;
                
                if (!email || !password || !firstName) {
                    alert("⚠️ กรุณากรอก Email, Password และชื่อให้ครบ");
                    return;
                }
            } catch (err) {
                alert("❌ หาช่องกรอกข้อมูลไม่เจอ: " + err.message);
                return;
            }

           try {
                const { data: authData, error: authError } = await supabase.auth.signUp({
                    email: email,
                    password: password
                });
                
                if (authError) {
                    alert("❌ สมัครไม่ผ่าน (Auth): " + authError.message);
                    return;
                }

                if (authData.user) {
                    const { error: dbError } = await supabase.from('Customer').insert([
                        {
                            "auth_id": authData.user.id,
                            "Cust_Uname": document.getElementById('username').value,
                            "Cust_FirstName": firstName,
                            "Cust_LastName": document.getElementById('lastName').value.trim(),
                            "Cust_Phone": document.getElementById('tel').value,
                            "Cust_House": document.getElementById('address').value,
                            "Cust_District": document.getElementById('subDistrict').value,
                            "Cust_Prefecture": document.getElementById('district').value,
                            "Cust_Province": document.getElementById('province').value,
                            "Cust_Postcode": document.getElementById('postalCode').value,
                            "Cust_Role": 'user',
                            "Cust_Status": 'active'
                        }
                    ]);

                    if (dbError) {
                        alert("❌ บันทึกตารางไม่ผ่าน (DB): " + dbError.message);
                    } else {
                        alert("🎉 สำเร็จ! ไปเช็คอีเมลได้เลย");
                        window.location.href = '../Signin/signin.php';
                    }
                }
            } catch (error) {
                alert("❌ Error ร้ายแรง: " + error.message);
                console.error(error);
            }
        }
    </script>
</body>
</html>